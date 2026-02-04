
<?php
include 'includes/header.php';

// ==================================
// CURRENT MONTH CALCULATION
// ==================================
$current_month_start = date('Y-m-01');
$current_month_end = date('Y-m-t');

// ==================================
// 1. MONTHLY PAYMENT STATS
// ==================================
// Fees paid this month
$monthly_payment = $conn->query("
    SELECT COALESCE(SUM(amount_paid), 0) as monthly_paid
    FROM payments 
    WHERE student_id = {$student['id']}
    AND payment_date BETWEEN '$current_month_start' AND '$current_month_end'
")->fetch_assoc()['monthly_paid'];

// Total payment summary
$payment_summary = $conn->query("
    SELECT 
        s.total_fees,
        COALESCE(SUM(p.amount_paid), 0) as total_paid,
        (s.total_fees - COALESCE(SUM(p.amount_paid), 0)) as pending_fees
    FROM students s
    LEFT JOIN payments p ON s.id = p.student_id
    WHERE s.id = {$student['id']}
    GROUP BY s.id
")->fetch_assoc();

$total_fees = $payment_summary['total_fees'];
$total_paid = $payment_summary['total_paid'];
$pending_fees = $payment_summary['pending_fees'];
$payment_percentage = ($total_fees > 0) ? ($total_paid / $total_fees) * 100 : 0;

// ==================================
// 2. PROJECT STATS
// ==================================
$project_stats = $conn->query("
    SELECT 
        COUNT(*) as total_projects,
        SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed_projects
    FROM student_projects
    WHERE student_id = {$student['id']}
")->fetch_assoc();

$total_projects = $project_stats['total_projects'] ?? 0;
$completed_projects = $project_stats['completed_projects'] ?? 0;

// ==================================
// 3. ATTENDANCE COUNT (THIS MONTH)
// ==================================
$attendance_count = $conn->query("
    SELECT COUNT(*) as present_days
    FROM student_attendance
    WHERE student_id = {$student['id']}
    AND status = 'Present'
    AND attendance_date BETWEEN '$current_month_start' AND '$current_month_end'
")->fetch_assoc()['present_days'];

// ==================================
// 4. POINTS CALCULATION
// ==================================
$total_points = 0;

// Payment Points (10 if paid this month)
$payment_points = ($monthly_payment > 0) ? 10 : 0;
$total_points += $payment_points;

// Project Points (based on category)
$course_details = $conn->query("
    SELECT s.*, c.name as course_name, cat.id as category_id, cat.name as category_name
    FROM students s
    JOIN courses c ON s.course_id = c.id
    JOIN categories cat ON s.category_id = cat.id
    WHERE s.id = {$student['id']}
")->fetch_assoc();

$category_name = $course_details['category_name'];
$points_per_project = ($category_name === 'Web Development') ? 15 : 6;
$project_points = $completed_projects * $points_per_project;
$total_points += $project_points;

// Attendance Points (1 point per day)
$attendance_points = $attendance_count;
$total_points += $attendance_points;

// ==================================
// 5. RANKING (among all active students)
// ==================================
// Calculate points for all students and rank
$all_students_points = $conn->query("
    SELECT 
        s.id,
        s.full_name,
        -- Payment points
        (CASE 
            WHEN EXISTS(
                SELECT 1 FROM payments p 
                WHERE p.student_id = s.id 
                AND p.payment_date BETWEEN '$current_month_start' AND '$current_month_end'
            ) THEN 10 ELSE 0 
        END) +
        -- Project points
        (SELECT COUNT(*) 
         FROM student_projects sp 
         WHERE sp.student_id = s.id 
         AND sp.status = 'Completed'
        ) * (CASE WHEN cat.name = 'Web Development' THEN 15 ELSE 6 END) +
        -- Attendance points
        (SELECT COUNT(*) 
         FROM student_attendance sa 
         WHERE sa.student_id = s.id 
         AND sa.status = 'Present'
         AND sa.attendance_date BETWEEN '$current_month_start' AND '$current_month_end'
        ) as total_points
    FROM students s
    JOIN courses c ON s.course_id = c.id
    JOIN categories cat ON s.category_id = cat.id
    WHERE s.status = 'Active' AND s.login_enabled = 1
    ORDER BY total_points DESC
");

$rank = 0;
$student_rank = 0;
while ($row = $all_students_points->fetch_assoc()) {
    $rank++;
    if ($row['id'] == $student['id']) {
        $student_rank = $rank;
        break;
    }
}

// Get total active students for rank display
$total_students = $conn->query("SELECT COUNT(*) as count FROM students WHERE status = 'Active' AND login_enabled = 1")->fetch_assoc()['count'];

// ==================================
// 6. RECENT PROJECTS
// ==================================
$recent_projects = $conn->query("
    SELECT * FROM student_projects 
    WHERE student_id = {$student['id']} 
    ORDER BY created_at DESC 
    LIMIT 5
");

// ==================================
// 7. RECENT NOTIFICATIONS
// ==================================
$recent_notifications = $conn->query("
    SELECT * FROM student_notifications 
    WHERE student_id = {$student['id']} 
    ORDER BY created_at DESC 
    LIMIT 3
");

// ==================================
// 8. COURSE PROGRESS
// ==================================
$enrollment_date = new DateTime($course_details['enrollment_date']);
$current_date = new DateTime();
$expected_end = clone $enrollment_date;
$expected_end->modify('+' . $course_details['duration_months'] . ' months');

$total_days = $enrollment_date->diff($expected_end)->days;
$days_elapsed = $enrollment_date->diff($current_date)->days;
$course_progress = min(100, ($days_elapsed / $total_days) * 100);

// ==================================
// 9. CHECK TODAY'S ATTENDANCE
// ==================================
$today = date('Y-m-d');
$attendance_check = $conn->query("
    SELECT * FROM student_attendance 
    WHERE student_id = {$student['id']} 
    AND attendance_date = '$today'
");
$attendance_marked = $attendance_check->num_rows > 0;
?>

<?php if (!$attendance_marked): ?>
<div class="alert alert-warning alert-dismissible fade show">
    <h5><i class="fas fa-exclamation-triangle"></i> Attendance Not Marked!</h5>
    <p class="mb-2">You haven't marked your attendance for today yet.</p>
    <a href="attendance.php" class="btn btn-warning btn-sm">
        <i class="fas fa-calendar-check"></i> Mark Attendance Now
    </a>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php else: ?>
<div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle"></i> <strong>Attendance Marked!</strong> You marked your attendance today at 
    <?php 
    $att = $attendance_check->fetch_assoc();
    echo date('h:i A', strtotime($att['check_in_time'])); 
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="page-header">
  <div class="row g-3">
    <div class="col-md-3 d-flex align-items-start justify-content-center flex-column">
          <h2><?php echo htmlspecialchars($student['name']); ?></h2>
    <p class="text-muted mb-0">Welcome back!</p>
    </div> 
      <!-- Monthly Fees Paid -->
    <div class="col-lg-2 col-md-6">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small"><?php echo date('F Y'); ?></p>
                        <h4 class="mb-0 text-success">₹<?php echo number_format($monthly_payment, 2); ?></h4>
                        
                    </div>
                    <!--<div class="card-icon icon-success">-->
                    <!--    <i class="fa-solid fa-indian-rupee-sign"></i>-->
                    <!--</div>-->
                </div>
            </div>
        </div>
  </div>
  <!-- Projects Count -->
    <div class="col-lg-2 col-md-6">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">This Month Projects</p>
                        <h4 class="mb-0 text-purple"><?php echo $completed_projects; ?> <?php // echo $total_projects; ?></h4>
                        <!--<small class="text-muted">Completed</small>-->
                    </div>
                    <!--<div class="card-icon icon-purple">-->
                    <!--    <i class="fas fa-project-diagram"></i>-->
                    <!--</div>-->
                </div>
            </div>
        </div>
    </div>
    
      <!-- Attendance Count -->
    <div class="col-lg-2 col-md-4">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Attendance</p>
                        <h4 class="mb-0 text-info"><?php echo $attendance_count; ?> Days</h4>
                        <!--<small class="text-muted">This Month</small>-->
                    </div>
                    <!--<div  class="card-icon icon-warning">-->
                    <!--    <i class="fas fa-calendar-check"></i>-->
                    <!--</div>-->
                </div>
            </div>
        </div>
    </div>
      <!-- Rank Count -->
    <div class="col-lg-2 col-md-4 ms-auto">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Std Rank</p>
                        <!--<h4 class="mb-0 text-info"><?php echo $attendance_count; ?> Days</h4>-->
                        <h2 class="mb-0">#<?php echo $student_rank; ?></h2>
                        <!--<small class="text-muted">This Month</small>-->
                    </div>
                    <!--<div  class="card-icon icon-warning">-->
                    <!--    <i class="fas fa-calendar-check"></i>-->
                    <!--</div>-->
                </div>
            </div>
        </div>
    </div>
    
</div>
</div>
<!-- Summary Cards -->
<div class="row g-3 mb-3">
    <div class="col-lg-3 col-md-6">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Total Fees</p>
                        <h4 class="mb-0">₹<?php echo number_format($total_fees, 2); ?></h4>
                    </div>
                    <div class="card-icon icon-purple">
                    <i class="fa-solid fa-indian-rupee-sign"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Total Amount Paid</p>
                        <h4 class="mb-0 text-success">₹<?php echo number_format($total_paid, 2); ?></h4>
                    </div>
                    <div class="card-icon icon-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Pending Fees</p>
                        <h4 class="mb-0 text-danger">₹<?php echo number_format($pending_fees, 2); ?></h4>
                    </div>
                    <div class="card-icon icon-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Duration</p>
                        <h4 class="mb-0"><?php echo $course_details['duration_months']; ?>M</h4>
                    </div>
                    <div class="card-icon icon-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Points & Ranking Card -->
<!--<div class="row g-3 mb-3">-->
<!--    <div class="col-12">-->
<!--        <div class="card dashboard-card" style="background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);">-->
<!--            <div class="card-body text-white">-->
<!--                <div class="row align-items-center">-->
<!--                    <div class="col-md-4 text-center border-end border-light">-->
<!--                        <h1 class="display-4 mb-0"><i class="fas fa-trophy"></i></h1>-->
<!--                        <h3 class="mb-0">Rank #<?php // echo $student_rank; ?></h3>-->
<!--                        <small class="opacity-75">Out of <?php // echo $total_students; ?> students</small>-->
<!--                    </div>-->
<!--                    <div class="col-md-8">-->
<!--                        <h4 class="mb-3"><i class="fas fa-star"></i> Your Points Breakdown</h4>-->
<!--                        <div class="row">-->
<!--                            <div class="col-4">-->
<!--                                <div class="text-center p-2 rounded" style="background: rgba(255,255,255,0.1);">-->
<!--                                    <h5 class="mb-0"><?php // echo $payment_points; ?> pts</h5>-->
<!--                                    <small>Payment</small>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="col-4">-->
<!--                                <div class="text-center p-2 rounded" style="background: rgba(255,255,255,0.1);">-->
<!--                                    <h5 class="mb-0"><?php // echo $project_points; ?> pts</h5>-->
<!--                                    <small>Projects (<?php // echo $completed_projects; ?> × <?php // echo $points_per_project; ?>)</small>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="col-4">-->
<!--                                <div class="text-center p-2 rounded" style="background: rgba(255,255,255,0.1);">-->
<!--                                    <h5 class="mb-0"><?php // echo $attendance_points; ?> pts</h5>-->
<!--                                    <small>Attendance</small>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="text-center mt-3">-->
<!--                            <h2 class="mb-0"><i class="fas fa-award"></i> Total: <?php // echo $total_points; ?> Points</h2>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

<!-- Payment & Course Progress -->
<div class="row g-3 mb-3">
    <div class="col-lg-6">
        <div class="table-card">
            <h6 class="text-purple mb-2"><i class="fas fa-chart-line"></i> Payment Progress</h6>
            <div class="progress" style="height: 25px;">
                <div class="progress-bar bg-success" 
                     style="width: <?php echo $payment_percentage; ?>%">
                    <?php echo number_format($payment_percentage, 1); ?>%
                </div>
            </div>
            <div class="mt-2">
                <div class="d-flex justify-content-between mb-1 small">
                    <span>Paid:</span>
                    <strong class="text-success">₹<?php echo number_format($total_paid, 2); ?></strong>
                </div>
                <div class="d-flex justify-content-between small">
                    <span>Remaining:</span>
                    <strong class="text-danger">₹<?php echo number_format($pending_fees, 2); ?></strong>
                </div>
            </div>
        </div>
    </div>
    
   
        <div class="col-lg-6">
            <a style="text-decoration: none;color: auto !important;" href="/student/course_progress.php">
        <div class="table-card">
            <h6 class="text-purple mb-2"><i class="fas fa-graduation-cap"></i> Course Progress</h6>
            <div class="progress" style="height: 25px;">
                <div class="progress-bar <?php echo $course_progress >= 90 ? 'bg-danger' : ($course_progress >= 75 ? 'bg-warning' : 'bg-primary'); ?>" 
                     style="width: <?php echo $course_progress; ?>%">
                    <?php echo number_format($course_progress, 1); ?>%
                </div>
            </div>
            <div class="mt-2">
                <div class="d-flex justify-content-between mb-1 small">
                    <span>Enrolled:</span>
                    <strong><?php echo date('d M Y', strtotime($course_details['enrollment_date'])); ?></strong>
                </div>
                <div class="d-flex justify-content-between small">
                    <span>Expected End:</span>
                    <strong><?php echo $expected_end->format('d M Y'); ?></strong>
                </div>
            </div>
        </div>
        </a>
    </div>
   
</div>

<!-- Course Details & Recent Projects -->
<div class="row g-3">
    <div class="col-lg-6">
        <div class="table-card">
            <h6 class="text-purple mb-2"><i class="fas fa-book"></i> My Course</h6>
            <table class="table table-sm mb-0">
                <tr>
                    <td class="small"><strong>Category:</strong></td>
                    <td class="small"><?php echo htmlspecialchars($course_details['category_name']); ?></td>
                </tr>
                <tr>
                    <td class="small"><strong>Course:</strong></td>
                    <td class="small"><?php echo htmlspecialchars($course_details['course_name']); ?></td>
                </tr>
                <tr>
                    <td class="small"><strong>Duration:</strong></td>
                    <td class="small"><?php echo $course_details['duration_months']; ?> Months</td>
                </tr>
                <tr>
                    <td class="small"><strong>Status:</strong></td>
                    <td><span class="badge bg-success small"><?php echo $course_details['status']; ?></span></td>
                </tr>
            </table>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="table-card">
            <h6 class="text-purple mb-2"><i class="fas fa-project-diagram"></i> Recent Projects</h6>
            <?php if ($recent_projects->num_rows > 0): ?>
            <div class="list-group list-group-flush">
                <?php while ($project = $recent_projects->fetch_assoc()): 
                    $status_class = 'secondary';
                    if ($project['status'] == 'Completed') $status_class = 'success';
                    if ($project['status'] == 'In Progress') $status_class = 'primary';
                    if ($project['status'] == 'On Hold') $status_class = 'warning';
                ?>
                <div class="list-group-item px-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <small><strong><?php echo htmlspecialchars($project['project_name']); ?></strong></small>
                        <span class="badge bg-<?php echo $status_class; ?> small"><?php echo $project['status']; ?></span>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <p class="text-muted small mb-0">No projects yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Notifications -->
<?php if ($recent_notifications->num_rows > 0): ?>
<div class="row g-3 mt-2">
    <div class="col-12">
        <div class="table-card">
            <h6 class="text-purple mb-2"><i class="fas fa-bell"></i> Recent Notifications</h6>
            <?php while ($notif = $recent_notifications->fetch_assoc()): ?>
            <div class="alert alert-<?php echo $notif['type'] === 'warning' ? 'warning' : 'info'; ?> py-2 px-3 small mb-2">
                <strong><?php echo htmlspecialchars($notif['title']); ?></strong>
                <p class="mb-1"><?php echo htmlspecialchars($notif['message']); ?></p>
                <small class="text-muted"><i class="fas fa-clock"></i> <?php echo date('d M Y, h:i A', strtotime($notif['created_at'])); ?></small>
            </div>
            <?php endwhile; ?>
            <a href="notifications.php" class="btn btn-sm btn-outline-primary">
                View All <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>

