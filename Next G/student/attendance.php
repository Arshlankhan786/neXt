<?php
include 'includes/header.php';

$success = '';
$error = '';

// Get current date in IST
$current_date = date('Y-m-d');
$current_time = date('H:i:s');

// Check if already marked today
$today_attendance = $conn->query("
    SELECT * FROM student_attendance 
    WHERE student_id = {$student['id']} 
    AND attendance_date = '$current_date'
")->fetch_assoc();

// Handle check-in
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'check_in') {
        if ($today_attendance) {
            $error = "You have already checked in today!";
        } else {
            $stmt = $conn->prepare("
                INSERT INTO student_attendance (student_id, attendance_date, status, check_in_time) 
                VALUES (?, ?, 'Present', ?)
            ");
            $stmt->bind_param("iss", $student['id'], $current_date, $current_time);
            
            if ($stmt->execute()) {
                $success = "Check-in successful at " . date('h:i A', strtotime($current_time));
                // Refresh today's attendance
                $today_attendance = $conn->query("
                    SELECT * FROM student_attendance 
                    WHERE student_id = {$student['id']} 
                    AND attendance_date = '$current_date'
                ")->fetch_assoc();
            } else {
                $error = "Failed to check in. Please try again.";
            }
            $stmt->close();
        }
    }
    
    if ($_POST['action'] === 'check_out') {
        if (!$today_attendance) {
            $error = "You need to check in first!";
        } elseif ($today_attendance['check_out_time']) {
            $error = "You have already checked out today!";
        } else {
            $check_in = new DateTime($today_attendance['check_in_time']);
            $check_out = new DateTime($current_time);
            $interval = $check_in->diff($check_out);
            $total_hours = $interval->h + ($interval->i / 60);
            
            $stmt = $conn->prepare("
                UPDATE student_attendance 
                SET check_out_time = ?, total_hours = ? 
                WHERE id = ?
            ");
            $stmt->bind_param("sdi", $current_time, $total_hours, $today_attendance['id']);
            
            if ($stmt->execute()) {
                $success = "Check-out successful at " . date('h:i A', strtotime($current_time)) . 
                           ". Total hours: " . number_format($total_hours, 2) . "h";
                // Refresh today's attendance
                $today_attendance = $conn->query("
                    SELECT * FROM student_attendance 
                    WHERE student_id = {$student['id']} 
                    AND attendance_date = '$current_date'
                ")->fetch_assoc();
            } else {
                $error = "Failed to check out. Please try again.";
            }
            $stmt->close();
        }
    }
}

// Get last 30 days attendance for chart
$last_30_days = [];
$attendance_data = [];

for ($i = 29; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $last_30_days[] = date('M d', strtotime($date));
    
    // Check if present on this date
    $check = $conn->query("
        SELECT status FROM student_attendance 
        WHERE student_id = {$student['id']} 
        AND attendance_date = '$date'
    ");
    
    if ($check->num_rows > 0) {
        $attendance_data[] = 1; // Present
    } else {
        $attendance_data[] = 0; // Absent
    }
}

// Get attendance history (current month)
$current_month = date('Y-m');
$filter_month = isset($_GET['month']) ? $_GET['month'] : $current_month;

$attendance_history = $conn->query("
    SELECT * FROM student_attendance 
    WHERE student_id = {$student['id']} 
    AND DATE_FORMAT(attendance_date, '%Y-%m') = '$filter_month'
    ORDER BY attendance_date DESC
");

// Get stats for current month
$stats = [];
$result = $conn->query("
    SELECT COUNT(*) as count FROM student_attendance 
    WHERE student_id = {$student['id']} 
    AND DATE_FORMAT(attendance_date, '%Y-%m') = '$current_month'
    AND status = 'Present'
");
$stats['present'] = $result->fetch_assoc()['count'];

$total_days_in_month = date('t');
$current_day = date('j');
$stats['absent'] = $current_day - $stats['present'];
$stats['rate'] = ($current_day > 0) ? ($stats['present'] / $current_day) * 100 : 0;
?>

<div class="page-header">
    <h2><i class="fas fa-calendar-check text-purple"></i> My Attendance</h2>
    <p class="text-muted mb-0">Track your daily attendance</p>
</div>

<?php if ($success): ?>
<div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Current Date & Time -->
<div class="row g-3 mb-3">
    <div class="col-12">
        <div class="card dashboard-card text-center">
            <div class="card-body">
                <h4 class="text-purple mb-2">
                    <i class="fas fa-calendar"></i> <?php echo date('l, F d, Y'); ?>
                </h4>
                <h2 class="text-success mb-0" id="currentTime">
                    <i class="fas fa-clock"></i> <?php echo date('h:i:s A'); ?>
                </h2>
            </div>
        </div>
    </div>
</div>

<!-- Check-in/Check-out Buttons -->
<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="table-card">
            <h6 class="text-success mb-3"><i class="fas fa-sign-in-alt"></i> Check In</h6>
            <?php if (!$today_attendance): ?>
                <form method="POST">
                    <input type="hidden" name="action" value="check_in">
                    <button type="submit" class="btn btn-success btn-lg w-100">
                        <i class="fas fa-sign-in-alt"></i> Mark Check In
                    </button>
                </form>
            <?php else: ?>
                <div class="alert alert-success mb-0">
                    <i class="fas fa-check-circle"></i> <strong>Checked In</strong>
                    <br>Time: <?php echo date('h:i A', strtotime($today_attendance['check_in_time'])); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="table-card">
            <h6 class="text-danger mb-3"><i class="fas fa-sign-out-alt"></i> Check Out</h6>
            <?php if ($today_attendance && !$today_attendance['check_out_time']): ?>
                <form method="POST">
                    <input type="hidden" name="action" value="check_out">
                    <button type="submit" class="btn btn-danger btn-lg w-100">
                        <i class="fas fa-sign-out-alt"></i> Mark Check Out
                    </button>
                </form>
            <?php elseif ($today_attendance && $today_attendance['check_out_time']): ?>
                <div class="alert alert-info mb-0">
                    <i class="fas fa-check-circle"></i> <strong>Checked Out</strong>
                    <br>Time: <?php echo date('h:i A', strtotime($today_attendance['check_out_time'])); ?>
                    <br>Total Hours: <strong><?php echo number_format($today_attendance['total_hours'], 2); ?>h</strong>
                </div>
            <?php else: ?>
                <div class="alert alert-secondary mb-0">
                    <i class="fas fa-info-circle"></i> Please check in first
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Monthly Stats -->
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <h3 class="text-success mb-0"><?php echo $stats['present']; ?></h3>
                <small class="text-muted">Days Present</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <h3 class="text-danger mb-0"><?php echo $stats['absent']; ?></h3>
                <small class="text-muted">Days Absent</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <h3 class="text-purple mb-0"><?php echo number_format($stats['rate'], 1); ?>%</h3>
                <small class="text-muted">Attendance Rate</small>
            </div>
        </div>
    </div>
</div>

<!-- Attendance Chart -->
<div class="table-card mb-3">
    <h6 class="text-purple mb-3"><i class="fas fa-chart-bar"></i> Last 30 Days Attendance</h6>
    <canvas id="attendanceChart" height="80"></canvas>
</div>

<!-- Attendance History -->
<div class="table-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="text-purple mb-0"><i class="fas fa-history"></i> Attendance History</h6>
        <form method="GET" class="d-flex gap-2">
            <input type="month" class="form-control form-control-sm" name="month" 
                   value="<?php echo $filter_month; ?>" max="<?php echo date('Y-m'); ?>">
            <button type="submit" class="btn btn-sm btn-purple">Filter</button>
        </form>
    </div>
    
    <?php if ($attendance_history->num_rows > 0): ?>
    <div class="table-responsive">
        <table class="table table-sm table-hover">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Total Hours</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($att = $attendance_history->fetch_assoc()): ?>
                <tr>
                    <td><?php echo date('d M Y (l)', strtotime($att['attendance_date'])); ?></td>
                    <td>
                        <span class="badge bg-success">
                            <?php echo date('h:i A', strtotime($att['check_in_time'])); ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($att['check_out_time']): ?>
                            <span class="badge bg-danger">
                                <?php echo date('h:i A', strtotime($att['check_out_time'])); ?>
                            </span>
                        <?php else: ?>
                            <span class="badge bg-warning">Not marked</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($att['total_hours']): ?>
                            <strong><?php echo number_format($att['total_hours'], 2); ?>h</strong>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge bg-success"><?php echo $att['status']; ?></span>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> No attendance records for <?php echo date('F Y', strtotime($filter_month)); ?>
    </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Real-time clock
function updateClock() {
    const now = new Date();
    const hours = now.getHours();
    const minutes = now.getMinutes();
    const seconds = now.getSeconds();
    const ampm = hours >= 12 ? 'PM' : 'AM';
    const displayHours = hours % 12 || 12;
    
    const timeString = `${displayHours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')} ${ampm}`;
    document.getElementById('currentTime').innerHTML = '<i class="fas fa-clock"></i> ' + timeString;
}

setInterval(updateClock, 1000);
updateClock();

// Attendance Chart with Present (Green) and Absent (Red) bars
const ctx = document.getElementById('attendanceChart');
const attendanceData = <?php echo json_encode($attendance_data); ?>;

// Separate data for present and absent
const presentData = attendanceData.map(val => val === 1 ? 1 : null);
const absentData = attendanceData.map(val => val === 0 ? 1 : null);

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($last_30_days); ?>,
        datasets: [
            {
                label: 'Present',
                data: presentData,
                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                borderColor: 'rgba(16, 185, 129, 1)',
                borderWidth: 1
            },
            {
                label: 'Absent',
                data: absentData,
                backgroundColor: 'rgba(239, 68, 68, 0.8)',
                borderColor: 'rgba(239, 68, 68, 1)',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            y: {
                beginAtZero: true,
                max: 1,
                ticks: {
                    stepSize: 1,
                    callback: function(value) {
                        return value === 1 ? 'Yes' : 'No';
                    }
                }
            },
            x: {
                stacked: false
            }
        },
        plugins: {
            legend: {
                display: true,
                position: 'top'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label;
                    }
                }
            }
        }
    }
});
</script>

<?php include 'includes/footer.php'; ?>