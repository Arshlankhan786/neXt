<?php
include 'includes/header.php';

// Date filter
$filter_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Get attendance for selected date with check-out times
$presentStudents = $conn->query("
    SELECT 
        s.id,
        s.student_code,
        s.full_name,
        s.phone,
        c.name as course_name,
        a.check_in_time,
        a.check_out_time,
        a.total_hours,
        a.created_at
    FROM students s
    JOIN student_attendance a ON s.id = a.student_id
    JOIN courses c ON s.course_id = c.id
    WHERE s.status = 'Active'
    AND a.attendance_date = '$filter_date'
    AND a.status = 'Present'
    ORDER BY a.check_in_time ASC
");

$absentStudents = $conn->query("
    SELECT 
        s.id,
        s.student_code,
        s.full_name,
        s.phone,
        c.name as course_name
    FROM students s
    JOIN courses c ON s.course_id = c.id
    WHERE s.status = 'Active'
    AND s.login_enabled = 1
    AND NOT EXISTS (
        SELECT 1 FROM student_attendance a
        WHERE a.student_id = s.id
        AND a.attendance_date = '$filter_date'
    )
    ORDER BY s.full_name ASC
");

// Statistics
$total_active = $conn->query("SELECT COUNT(*) as count FROM students WHERE status = 'Active' AND login_enabled = 1")->fetch_assoc()['count'];
$present_count = $presentStudents->num_rows;
$absent_count = $absentStudents->num_rows;
$attendance_rate = $total_active > 0 ? ($present_count / $total_active) * 100 : 0;

$filter_date = isset($_GET['date']) ? $_GET['date'] : getCurrentISTDate();

// Display current IST time
$current_ist_time = date('h:i:s A');
?>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2><i class="fas fa-calendar-check text-purple"></i> Attendance Report</h2>
            <p class="text-muted mb-0">Daily attendance tracking - IST Time: <strong><?php echo $current_ist_time; ?></strong></p>
        </div>
    </div>
</div>

<!-- Date Filter -->
<div class="row mb-4">
    <div class="col-md-6">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-8">
                <label class="form-label">Select Date</label>
                <input type="date" class="form-control" name="date" value="<?php echo $filter_date; ?>" max="<?php echo getCurrentISTDate(); ?>">

            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-purple w-100">
                    <i class="fas fa-search"></i> View Report
                </button>
            </div>
        </form>
    </div>
    <div class="col-md-6 text-end">
        <button class="btn btn-success" onclick="exportTableToCSV('attendanceReport', 'attendance_<?php echo $filter_date; ?>.csv')">
            <i class="fas fa-download"></i> Export CSV
        </button>
        <button class="btn btn-secondary" onclick="window.print()">
            <i class="fas fa-print"></i> Print
        </button>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <h3 class="text-purple mb-0"><?php echo $total_active; ?></h3>
                <small class="text-muted">Total Students</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <h3 class="text-success mb-0"><?php echo $present_count; ?></h3>
                <small class="text-muted">Present</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <h3 class="text-danger mb-0"><?php echo $absent_count; ?></h3>
                <small class="text-muted">Absent</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <h3 class="text-purple mb-0"><?php echo number_format($attendance_rate, 1); ?>%</h3>
                <small class="text-muted">Attendance Rate</small>
            </div>
        </div>
    </div>
</div>

<!-- Date Display -->
<div class="alert alert-info mb-4">
    <i class="fas fa-calendar"></i> <strong>Showing attendance for:</strong> <?php echo date('l, d F Y', strtotime($filter_date)); ?>
    <?php if ($filter_date === date('Y-m-d')): ?>
        <span class="badge bg-success ms-2">TODAY</span>
    <?php endif; ?>
</div>

<!-- Attendance Tables -->
<div class="row g-4">
    <!-- Present Students -->
    <div class="col-lg-6">
      <table class="table table-hover table-sm" id="presentTable">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>In</th>
            <th>Out</th>
            <th>Hours</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $sno = 1;
        while ($student = $presentStudents->fetch_assoc()): 
        ?>
        <tr>
            <td><?php echo $sno++; ?></td>
            <td>
                <a href="student_details.php?id=<?php echo $student['id']; ?>">
                    <?php echo htmlspecialchars($student['full_name']); ?>
                </a>
            </td>
            <td>
                <span class="badge bg-success">
                    <?php echo formatIndianTime($student['check_in_time']); ?>
                </span>
            </td>
            <td>
                <?php if ($student['check_out_time']): ?>
                    <span class="badge bg-danger">
                        <?php echo formatIndianTime($student['check_out_time']); ?>
                    </span>
                <?php else: ?>
                    <span class="badge bg-warning">Not marked</span>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($student['total_hours']): ?>
                    <span class="badge bg-info">
                        <?php echo number_format($student['total_hours'], 2); ?> hrs
                    </span>
                <?php else: ?>
                    <span class="text-muted">-</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
    </div>
    
    <!-- Absent Students -->
    <div class="col-lg-6">
        <div class="table-card">
            <h5 class="text-danger mb-3">
                <i class="fas fa-user-times"></i> Absent Students (<?php echo $absent_count; ?>)
            </h5>
            
            <?php if ($absent_count > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover table-sm" id="absentTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Phone</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sno = 1;
                        while ($student = $absentStudents->fetch_assoc()): 
                        ?>
                        <tr>
                            <td><?php echo $sno++; ?></td>
                            <td>
                                <a href="student_details.php?id=<?php echo $student['id']; ?>">
                                    <?php echo htmlspecialchars($student['full_name']); ?>
                                </a>
                            </td>
                            <td>
                                <a href="tel:<?php echo $student['phone']; ?>" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-phone"></i> <?php echo htmlspecialchars($student['phone']); ?>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> All students marked present for this date!
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Combined Report for Export -->
<div class="table-card mt-4 d-none" id="combinedReport">
    <h5>Attendance Report - <?php echo date('d-M-Y', strtotime($filter_date)); ?></h5>
    <table class="table" id="attendanceReport">
        <thead>
            <tr>
                <th>Student Code</th>
                <th>Name</th>
                <th>Course</th>
                <th>Status</th>
                <th>Check-in Time</th>
                <th>Phone</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            // Reset result pointers
            $presentStudents->data_seek(0);
            while ($student = $presentStudents->fetch_assoc()): 
            ?>
            <tr>
                <td><?php echo $student['student_code']; ?></td>
                <td><?php echo $student['full_name']; ?></td>
                <td><?php echo $student['course_name']; ?></td>
                <td>Present</td>
                <td><?php echo date('h:i A', strtotime($student['check_in_time'])); ?></td>
                <td><?php echo $student['phone']; ?></td>
            </tr>
            <?php endwhile; ?>
            
            <?php 
            $absentStudents->data_seek(0);
            while ($student = $absentStudents->fetch_assoc()): 
            ?>
            <tr>
                <td><?php echo $student['student_code']; ?></td>
                <td><?php echo $student['full_name']; ?></td>
                <td><?php echo $student['course_name']; ?></td>
                <td>Absent</td>
                <td>-</td>
                <td><?php echo $student['phone']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>