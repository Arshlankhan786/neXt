<?php
include 'includes/header.php';

// ============================================
// REPORTS & ANALYTICS - NO CATEGORY DEPENDENCY
// ============================================

// Total Courses
$result = $conn->query("SELECT COUNT(*) as total FROM courses WHERE status = 'Active'");
$stats['total_courses'] = $result->fetch_assoc()['total'];

// Date filters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Overdue Students (no payment this month AND has pending)
$result = $conn->query("
    SELECT COUNT(DISTINCT s.id) as count
    FROM students s
    LEFT JOIN (
        SELECT student_id, SUM(amount_paid) as paid 
        FROM payments 
        GROUP BY student_id
    ) p ON s.id = p.student_id
    WHERE s.status = 'Active'
    AND (s.total_fees - COALESCE(p.paid, 0)) > 0
    AND NOT EXISTS (
        SELECT 1 FROM payments p2
        WHERE p2.student_id = s.id
        AND YEAR(p2.payment_date) = YEAR(CURDATE())
        AND MONTH(p2.payment_date) = MONTH(CURDATE())
    )
");
$stats['overdue_students'] = $result->fetch_assoc()['count'];

// ============================================
// OVERDUE STUDENTS (Top 5)
// ============================================
$overdueStudents = $conn->query("
    SELECT 
        s.id,
        s.student_code,
        s.full_name,
        s.phone,
        s.total_fees,
        COALESCE(SUM(p.amount_paid), 0) as total_paid,
        (s.total_fees - COALESCE(SUM(p.amount_paid), 0)) as pending_fees,
        MAX(p.payment_date) as last_payment_date,
        DATEDIFF(CURDATE(), MAX(p.payment_date)) as days_since_last_payment
    FROM students s
    LEFT JOIN payments p ON s.id = p.student_id
    WHERE s.status = 'Active'
    GROUP BY s.id
    HAVING pending_fees > 0
    AND (
        last_payment_date IS NULL 
        OR days_since_last_payment >= 30
    )
    ORDER BY days_since_last_payment DESC
    LIMIT 10
");


// ============================================
// ACTIVE PAYING STUDENTS (for selected date range) - WITH DATES
// ============================================
$activePayingStudents = $conn->query("
    SELECT 
        s.id,
        s.student_code,
        s.full_name,
        COALESCE(SUM(p.amount_paid), 0) as month_paid,
        MIN(p.payment_date) as first_payment_date,
        MAX(p.payment_date) as last_payment_date,
        COUNT(p.id) as payment_count
    FROM students s
    JOIN payments p 
        ON s.id = p.student_id 
        AND p.payment_date BETWEEN '$start_date' AND '$end_date'
    WHERE s.status = 'Active'
    GROUP BY s.id
    ORDER BY last_payment_date DESC
    LIMIT 10
");


// Revenue by course
$course_revenue = $conn->query("SELECT c.name, 
                                SUM(p.amount_paid) as total_revenue,
                                COUNT(DISTINCT s.id) as student_count
                                FROM payments p
                                JOIN students s ON p.student_id = s.id
                                JOIN courses c ON s.course_id = c.id
                                WHERE p.payment_date BETWEEN '$start_date' AND '$end_date'
                                GROUP BY c.id
                                ORDER BY total_revenue DESC
                                LIMIT 10");

// Monthly collection trend (last 12 months)
$monthly_collection = [];
for ($i = 11; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $result = $conn->query("SELECT COALESCE(SUM(amount_paid), 0) as total 
                           FROM payments 
                           WHERE DATE_FORMAT(payment_date, '%Y-%m') = '$month'");
    $monthly_collection[] = [
        'month' => date('M Y', strtotime($month . '-01')),
        'amount' => $result->fetch_assoc()['total']
    ];
}

// Payment method distribution
$payment_methods = $conn->query("SELECT payment_method, 
                                COUNT(*) as count,
                                SUM(amount_paid) as total
                                FROM payments
                                WHERE payment_date BETWEEN '$start_date' AND '$end_date'
                                GROUP BY payment_method");

// Top paying students
$top_students = $conn->query("SELECT s.student_code, s.full_name, 
                             SUM(p.amount_paid) as total_paid
                             FROM payments p
                             JOIN students s ON p.student_id = s.id
                             WHERE p.payment_date BETWEEN '$start_date' AND '$end_date'
                             GROUP BY s.id
                             ORDER BY total_paid DESC
                             LIMIT 10");

// Summary statistics
$total_collection = $conn->query("SELECT COALESCE(SUM(amount_paid), 0) as total 
                                 FROM payments 
                                 WHERE payment_date BETWEEN '$start_date' AND '$end_date'")->fetch_assoc()['total'];

$total_students = $conn->query("SELECT COUNT(DISTINCT student_id) as count 
                               FROM payments 
                               WHERE payment_date BETWEEN '$start_date' AND '$end_date'")->fetch_assoc()['count'];

// Duration-based revenue
$duration_revenue = $conn->query("SELECT 
                                  s.duration_months,
                                  SUM(p.amount_paid) as total_revenue,
                                  COUNT(DISTINCT s.id) as student_count
                                  FROM payments p
                                  JOIN students s ON p.student_id = s.id
                                  WHERE p.payment_date BETWEEN '$start_date' AND '$end_date'
                                  GROUP BY s.duration_months
                                  ORDER BY s.duration_months ASC");
                                  
                                  $todayPresentStudents = $conn->query("
    SELECT 
        s.id,
        s.student_code,
        s.full_name,
        a.check_in_time,
        a.created_at
    FROM students s
    JOIN student_attendance a ON s.id = a.student_id
    WHERE s.status = 'Active'
    AND a.attendance_date = CURDATE()
    AND a.status = 'Present'
    ORDER BY a.check_in_time ASC
");

// Today's Present Students with Check-Out Status
$todayPresentStudents = $conn->query("
    SELECT 
        s.id,
        s.student_code,
        s.full_name,
        a.check_in_time,
        a.check_out_time,
        a.total_hours,
        a.created_at
    FROM students s
    JOIN student_attendance a ON s.id = a.student_id
    WHERE s.status = 'Active'
    AND a.attendance_date = '" . getCurrentISTDate() . "'
    AND a.status = 'Present'
    ORDER BY a.check_in_time ASC
");

// Today's Absent Students - Use IST
$todayAbsentStudents = $conn->query("
    SELECT 
        s.id,
        s.student_code,
        s.full_name,
        s.phone
    FROM students s
    WHERE s.status = 'Active'
    AND s.login_enabled = 1
    AND NOT EXISTS (
        SELECT 1 FROM student_attendance a
        WHERE a.student_id = s.id
        AND a.attendance_date = '" . getCurrentISTDate() . "'
    )
    ORDER BY s.full_name ASC
");

// Attendance stats
$total_students_enabled = $conn->query("SELECT COUNT(*) as count FROM students WHERE status = 'Active' AND login_enabled = 1")->fetch_assoc()['count'];
$present_count = $todayPresentStudents->num_rows;
$absent_count = $todayAbsentStudents->num_rows;
?>

<div class="row">
    <h2 class="col-md-6"><i class="fas fa-tachometer-alt text-purple pb-4"></i> Current Month Fees Report</h2>

    <div class="mb-4 col-md-6">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
                 <!-- <small><label class="form-label">Start Date</label></small>  -->
                <input type="date" class="form-control" name="start_date" value="<?php echo $start_date; ?>">
            </div>
            <div class="col-md-5">
                 <!-- <small><label class="form-label">End Date</label></small>  -->
                <input type="date" class="form-control" name="end_date" value="<?php echo $end_date; ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-purple w-100">
                    <i class="fas fa-filter"></i> 
                </button>
            </div>
        </form>
    </div>
</div>
<div class="row g-4">
    <!-- Active Paying Students (WITH DATES) -->
    <div class="col-lg-6 pt-2">
        <div class="table-card position-relative">
            <div style="top: -12px;right: 10px" class="card-icon icon-success position-absolute">
                <i class="fas fa-check-circle"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    <p style="font-size: 12px;" class="p-0 m-0"><?php echo $total_students; ?></p>
                </span>
            </div>
            <h5 class="text-success mb-3"><i class="fas fa-check-circle"></i> Fees Paid</h5>
            <small class="text-muted d-block mb-2">Students who paid during this period</small>
            <?php if ($activePayingStudents->num_rows > 0): ?>  
            <div class="list-group list-group-flush">
                <?php while ($student = $activePayingStudents->fetch_assoc()): ?>
                <a href="student_details.php?id=<?php echo $student['id']; ?>" class="list-group-item list-group-item-action list-group-item-success d-flex justify-content-between">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong><?php echo htmlspecialchars($student['full_name']); ?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="mt-1">
                        <small class="text-muted">
                            <i class="fas fa-calendar-alt"></i>
                            <?php if ($student['first_payment_date'] == $student['last_payment_date']): ?>
                                <?php echo date('d M Y', strtotime($student['first_payment_date'])); ?>
                            <?php else: ?>
                                <?php echo date('d M', strtotime($student['first_payment_date'])); ?> - <?php echo date('d M Y', strtotime($student['last_payment_date'])); ?>
                            <?php endif; ?>
                            <span class="badge bg-secondary ms-1"><?php echo $student['payment_count']; ?>x</span>
                        </small>
                    </div>
                    <span class="badge bg-success m-0">₹<?php echo number_format($student['month_paid'], 2); ?></span>
                </a>
                <?php endwhile; ?>
            </div>
            <!-- View Full List Button -->
            <a href="paid_students_full_list.php?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" class="btn btn-sm btn-outline-success w-100 mt-3">
                <i class="fas fa-list"></i> View Full List (<?php echo $total_students; ?> Students)
            </a>
            <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No payments received during this period.
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Overdue Students -->
    <div class="col-lg-6 pt-2">
        <div class="table-card position-relative">
            <div style="top: -12px;right: 10px" class="card-icon icon-danger position-absolute">
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    <p style="font-size: 12px;" class="p-0 m-0"><?php echo number_format($stats['overdue_students']); ?></p>
                </span>
                <i class="fas fa-clock"></i>
            </div>
            <h5 class="text-danger mb-3"><i class="fas fa-exclamation-triangle"></i> Overdue Students</h5>
            <small class="text-muted d-block mb-2">Students with no payment this month</small>
            <?php if ($overdueStudents->num_rows > 0): ?>
            <div class="list-group list-group-flush">
                <?php while ($student = $overdueStudents->fetch_assoc()): ?>
                <a href="student_details.php?id=<?php echo $student['id']; ?>" class="list-group-item list-group-item-action list-group-item-danger">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?php echo htmlspecialchars($student['full_name']); ?></strong>
                            <br>
                            <!--<small class="text-muted"><?php // echo $student['student_code']; ?></small>-->
                        </div>
                        <span class="badge bg-danger">₹<?php echo number_format($student['pending_fees'], 2); ?></span>
                    </div>
                </a>
                <?php endwhile; ?>
            </div>
            <!-- View Full List Button -->
            <a href="overdue_students_full_list.php" class="btn btn-sm btn-outline-danger w-100 mt-3">
                <i class="fas fa-exclamation-triangle"></i> View Full List (<?php echo $stats['overdue_students']; ?> Students)
            </a>
            <?php else: ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> All students are up to date!
            </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="row mt-4 mb-3">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-calendar-check text-success"></i> Today's Attendance Report</h2>
                <p class="text-muted mb-0">
                    <i class="fas fa-calendar"></i> <?php echo date('l, d F Y'); ?> | 
                    <i class="fas fa-clock"></i> Last updated: <?php echo date('h:i A'); ?>
                </p>
            </div>
            <a href="attendance_report.php" class="btn btn-outline-purple">
                <i class="fas fa-chart-line"></i> View Full Report
            </a>
        </div>
    </div>
</div>

<!-- Attendance Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card attendance-stat-card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Total Students</p>
                        <h3 class="mb-0 fw-bold"><?php echo $total_students_enabled; ?></h3>
                    </div>
                    <div class="stat-icon bg-purple-light">
                        <i class="fas fa-users text-purple"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card attendance-stat-card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Present Today</p>
                        <h3 class="mb-0 fw-bold text-success"><?php echo $present_count; ?></h3>
                    </div>
                    <div class="stat-icon bg-success-light">
                        <i class="fas fa-user-check text-success"></i>
                    </div>
                </div>
                <div class="progress mt-2" style="height: 4px;">
                    <div class="progress-bar bg-success" style="width: <?php echo ($present_count / max($total_students_enabled, 1)) * 100; ?>%"></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card attendance-stat-card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Absent Today</p>
                        <h3 class="mb-0 fw-bold text-danger"><?php echo $absent_count; ?></h3>
                    </div>
                    <div class="stat-icon bg-danger-light">
                        <i class="fas fa-user-times text-danger"></i>
                    </div>
                </div>
                <div class="progress mt-2" style="height: 4px;">
                    <div class="progress-bar bg-danger" style="width: <?php echo ($absent_count / max($total_students_enabled, 1)) * 100; ?>%"></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card attendance-stat-card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Attendance Rate</p>
                        <h3 class="mb-0 fw-bold text-info"><?php echo number_format(($present_count / max($total_students_enabled, 1)) * 100, 1); ?>%</h3>
                    </div>
                    <div class="stat-icon bg-info-light">
                        <i class="fas fa-chart-pie text-info"></i>
                    </div>
                </div>
                <div class="progress mt-2" style="height: 4px;">
                    <div class="progress-bar bg-info" style="width: <?php echo ($present_count / max($total_students_enabled, 1)) * 100; ?>%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Attendance Lists -->
<div class="row g-4">
    <!-- Present Students -->
    <div class="col-lg-6">
        <div class="card modern-attendance-card border-0 shadow-sm">
            <div class="card-header bg-success-gradient text-white border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">
                            <i class="fas fa-user-check"></i> Present Today
                        </h5>
                        <small class="opacity-75">Students who checked in</small>
                    </div>
                    <span class="badge bg-white text-success fs-6 px-3 py-2">
                        <?php echo $present_count; ?>
                    </span>
                </div>
            </div>
            
            <div class="card-body p-0">
                <?php if ($todayPresentStudents->num_rows > 0): ?>
                <div class="attendance-list" style="max-height: 500px; overflow-y: auto;">
                    <?php while ($student = $todayPresentStudents->fetch_assoc()): ?>
                    <div class="attendance-item present-item" onclick="window.location.href='student_details.php?id=<?php echo $student['id']; ?>'">
                        <div class="d-flex align-items-center">
                            <div class="student-avatar bg-success-light">
                                <i class="fas fa-user-graduate text-success"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($student['full_name']); ?></h6>
                                <p class="mb-0 text-muted small">
                                    <i class="fas fa-id-card"></i> <?php echo $student['student_code']; ?>
                                </p>
                            </div>
                            <div class="text-end">
                                <div class="time-badges">
                                    <span class="badge bg-success-light text-success mb-1">
                                        <i class="fas fa-sign-in-alt"></i> <?php echo formatIndianTime($student['check_in_time']); ?>
                                    </span>
                                    <?php if ($student['check_out_time']): ?>
                                    <br>
                                    <span class="badge bg-danger-light text-danger">
                                        <i class="fas fa-sign-out-alt"></i> <?php echo formatIndianTime($student['check_out_time']); ?>
                                    </span>
                                    <?php else: ?>
                                    <br>
                                    <span class="badge bg-warning-light text-warning">
                                        <i class="fas fa-clock"></i> Still In
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <?php if ($student['total_hours']): ?>
                                <span class="badge bg-info-light text-info mt-1">
                                    <i class="fas fa-hourglass-half"></i> <?php echo number_format($student['total_hours'], 1); ?>h
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php else: ?>
                <div class="p-5 text-center">
                    <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No students have marked attendance yet today</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Absent Students -->
    <div class="col-lg-6">
        <div class="card modern-attendance-card border-0 shadow-sm">
            <div class="card-header bg-danger-gradient text-white border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">
                            <i class="fas fa-user-times"></i> Absent Today
                        </h5>
                        <small class="opacity-75">Students who haven't checked in</small>
                    </div>
                    <span class="badge bg-white text-danger fs-6 px-3 py-2">
                        <?php echo $absent_count; ?>
                    </span>
                </div>
            </div>
            
            <div class="card-body p-0">
                <?php if ($todayAbsentStudents->num_rows > 0): ?>
                <div class="attendance-list" style="max-height: 500px; overflow-y: auto;">
                    <?php while ($student = $todayAbsentStudents->fetch_assoc()): ?>
                    <div class="attendance-item absent-item">
                        <div class="d-flex align-items-center">
                            <div class="student-avatar bg-danger-light">
                                <i class="fas fa-user-graduate text-danger"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($student['full_name']); ?></h6>
                                <p class="mb-0 text-muted small">
                                    <i class="fas fa-id-card"></i> <?php echo $student['student_code']; ?>
                                </p>
                            </div>
                            <div class="action-buttons">
                                <a href="student_details.php?id=<?php echo $student['id']; ?>" 
                                   class="btn btn-sm btn-outline-primary me-2" 
                                   title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="tel:<?php echo $student['phone']; ?>" 
                                   class="btn btn-sm btn-danger" 
                                   title="Call Student"
                                   onclick="event.stopPropagation();">
                                    <i class="fas fa-phone"></i> Call
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php else: ?>
                <div class="p-5 text-center">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5 class="text-success">Perfect Attendance!</h5>
                    <p class="text-muted">All students have marked their attendance</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-3 mt-3">
    <div class="col-md-3">
        <a href="attendance_report.php" class="quick-action-card">
            <div class="icon-wrapper bg-purple-light">
                <i class="fas fa-clipboard-list text-purple"></i>
            </div>
            <div class="content">
                <h6 class="mb-0">Full Report</h6>
                <small class="text-muted">View detailed report</small>
            </div>
            <i class="fas fa-arrow-right arrow-icon"></i>
        </a>
    </div>
    
    <div class="col-md-3">
        <a href="send_notification.php" class="quick-action-card">
            <div class="icon-wrapper bg-warning-light">
                <i class="fas fa-bell text-warning"></i>
            </div>
            <div class="content">
                <h6 class="mb-0">Notify Absent</h6>
                <small class="text-muted">Send notifications</small>
            </div>
            <i class="fas fa-arrow-right arrow-icon"></i>
        </a>
    </div>
    
    <div class="col-md-3">
        <a href="attendance_report.php?date=<?php echo date('Y-m-d', strtotime('-1 day')); ?>" class="quick-action-card">
            <div class="icon-wrapper bg-info-light">
                <i class="fas fa-history text-info"></i>
            </div>
            <div class="content">
                <h6 class="mb-0">Yesterday</h6>
                <small class="text-muted">View previous day</small>
            </div>
            <i class="fas fa-arrow-right arrow-icon"></i>
        </a>
    </div>
    
    <div class="col-md-3">
        <button class="quick-action-card" onclick="exportAttendance()">
            <div class="icon-wrapper bg-success-light">
                <i class="fas fa-download text-success"></i>
            </div>
            <div class="content">
                <h6 class="mb-0">Export Data</h6>
                <small class="text-muted">Download CSV</small>
            </div>
            <i class="fas fa-arrow-right arrow-icon"></i>
        </button>
    </div>
</div>
     
    
    <!-- Recent Admissions Section -->
<div class="row mt-4 mb-3">
    <div class="col-12">
        <h2><i class="fas fa-user-plus text-success"></i> Recent Top Admissions </h2>
        <p class="text-muted mb-0">Latest students enrolled</p>
    </div>
</div>

<?php
// Get recent 6 admissions
$recentAdmissions = $conn->query("
    SELECT id, student_code, full_name, photo, enrollment_date
    FROM students
    WHERE status = 'Active'
    ORDER BY enrollment_date DESC
    LIMIT 8
");
?>

<div class="table-card mb-4">
    <?php if ($recentAdmissions->num_rows > 0): ?>
    <div class="recent-admissions-scroll">
        <div class="d-flex gap-3" style="overflow-x: auto; padding: 10px 0;">
            <?php while ($admission = $recentAdmissions->fetch_assoc()): 
                $has_photo = !empty($admission['photo']) && file_exists($admission['photo']);
            ?>
            <a href="student_details.php?id=<?php echo $admission['id']; ?>" class="admission-story-card text-decoration-none">
                <div class="story-photo-wrapper">
                    <?php if ($has_photo): ?>
                        <img src="<?php echo htmlspecialchars($admission['photo']); ?>" alt="<?php echo htmlspecialchars($admission['full_name']); ?>">
                    <?php else: ?>
                        <div class="story-placeholder">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <p class="story-name"><?php echo htmlspecialchars($admission['full_name']); ?></p>
                <small class="story-date"><?php echo date('d M', strtotime($admission['enrollment_date'])); ?></small>
            </a>
            <?php endwhile; ?>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-info mb-0">
        <i class="fas fa-info-circle"></i> No recent admissions found.
    </div>
    <?php endif; ?>
</div>
    <!-- Summary Cards  -->
    <div class="col-md-6">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1"><?php echo date('M Y', strtotime($start_date)); ?> Total Collection</p>
                        <h3 class="mb-0 text-purple">₹<?php echo number_format($total_collection, 2); ?></h3>
                        <small class="text-muted"><?php echo date('d M Y', strtotime($start_date)); ?> - <?php echo date('d M Y', strtotime($end_date)); ?></small>
                    </div>
                    <div class="card-icon icon-purple">
                        <i class="fa-solid fa-indian-rupee-sign"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
     <!-- Top Courses Table  -->
    <div class="col-lg-6">
        <div class="table-card">
            <h5 class="text-purple mb-3"><i class="fas fa-trophy"></i> Top Revenue Courses</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Students</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $course_revenue->data_seek(0);
                        while ($course = $course_revenue->fetch_assoc()): 
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($course['name']); ?></td>
                            <td><span class="badge bg-info"><?php echo $course['student_count']; ?></span></td>
                            <td><strong>₹<?php echo number_format($course['total_revenue'], 2); ?></strong></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
</div>

<script>
// ============================================
// CHART.JS v4 - ALL CHARTS
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    
    // Course Revenue Chart (Doughnut)
    const courseCtx = document.getElementById('courseChart');
    if (courseCtx) {
        <?php 
        $course_revenue->data_seek(0);
        $course_names = [];
        $course_revenues = [];
        while ($c = $course_revenue->fetch_assoc()) {
            $course_names[] = $c['name'];
            $course_revenues[] = $c['total_revenue'];
        }
        ?>
        
        new Chart(courseCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($course_names); ?>,
                datasets: [{
                    data: <?php echo json_encode($course_revenues); ?>,
                    backgroundColor: [
                        '#7c3aed',
                        '#a78bfa',
                        '#c4b5fd',
                        '#ddd6fe',
                        '#ede9fe',
                        '#f5f3ff',
                        '#8b5cf6',
                        '#9333ea',
                        '#a855f7',
                        '#b794f4'
                    ]
                }]
            },
            options: { 
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ₹' + context.parsed.toLocaleString('en-IN');
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>