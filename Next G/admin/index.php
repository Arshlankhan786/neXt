<?php
include 'includes/header.php';

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Overdue Students
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

$total_collection = $conn->query("SELECT COALESCE(SUM(amount_paid), 0) as total 
                                 FROM payments 
                                 WHERE payment_date BETWEEN '$start_date' AND '$end_date'")->fetch_assoc()['total'];

$total_students = $conn->query("SELECT COUNT(DISTINCT student_id) as count 
                               FROM payments 
                               WHERE payment_date BETWEEN '$start_date' AND '$end_date'")->fetch_assoc()['count'];

// ============================================
// TODAY'S ATTENDANCE - MORNING BATCH
// ============================================
$today = getCurrentISTDate();
$current_month_start = date('Y-m-01');
$current_month_end = date('Y-m-t');

// Get all students ranked by points
$ranked_students = [];
$rank_query = $conn->query("
    SELECT 
        s.id,
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

$rank = 1;
while ($row = $rank_query->fetch_assoc()) {
    $ranked_students[$row['id']] = $rank;
    $rank++;
}

$morningPresent = $conn->query("
    SELECT 
        s.id,
        s.student_code,
        s.full_name,
        s.photo,
        a.check_in_time,
        a.created_at
    FROM students s
    JOIN student_attendance a ON s.id = a.student_id
    WHERE s.status = 'Active'
    AND s.batch = 'Morning'
    AND a.attendance_date = '$today'
    AND a.status = 'Present'
    ORDER BY a.check_in_time DESC
");

$morningAbsent = $conn->query("
    SELECT 
        s.id,
        s.student_code,
        s.full_name,
        s.photo,
        s.phone
    FROM students s
    WHERE s.status = 'Active'
    AND s.login_enabled = 1
    AND s.batch = 'Morning'
    AND NOT EXISTS (
        SELECT 1 FROM student_attendance a
        WHERE a.student_id = s.id
        AND a.attendance_date = '$today'
    )
    ORDER BY s.full_name ASC
");

// ============================================
// TODAY'S ATTENDANCE - EVENING BATCH
// ============================================
$eveningPresent = $conn->query("
    SELECT 
        s.id,
        s.student_code,
        s.full_name,
        s.photo,
        a.check_in_time,
        a.created_at
    FROM students s
    JOIN student_attendance a ON s.id = a.student_id
    WHERE s.status = 'Active'
    AND s.batch = 'Evening'
    AND a.attendance_date = '$today'
    AND a.status = 'Present'
    ORDER BY a.check_in_time DESC
");

$eveningAbsent = $conn->query("
    SELECT 
        s.id,
        s.student_code,
        s.full_name,
        s.photo,
        s.phone
    FROM students s
    WHERE s.status = 'Active'
    AND s.login_enabled = 1
    AND s.batch = 'Evening'
    AND NOT EXISTS (
        SELECT 1 FROM student_attendance a
        WHERE a.student_id = s.id
        AND a.attendance_date = '$today'
    )
    ORDER BY s.full_name ASC
");

$morning_present_count = $morningPresent->num_rows;
$morning_absent_count = $morningAbsent->num_rows;
$evening_present_count = $eveningPresent->num_rows;
$evening_absent_count = $eveningAbsent->num_rows;

$recentAdmissions = $conn->query("
    SELECT id, student_code, full_name, photo, enrollment_date
    FROM students
    WHERE status = 'Active'
    ORDER BY enrollment_date DESC
    LIMIT 8
");
?>

<style>
.story-container {
    display: flex;
    gap: 12px;
    overflow-x: auto;
    padding: 15px 0;
    scrollbar-width: thin;
}

.story-card {
    flex: 0 0 auto;
    width: 85px;
    text-align: center;
    cursor: pointer;
    transition: transform 0.2s;
}

.story-card:hover {
    transform: translateY(-3px);
}

.story-avatar {
    width: 75px;
    height: 75px;
    border-radius: 50%;
    padding: 3px;
    margin: 0 auto 6px;
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
    position: relative;
}

.story-rank-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: linear-gradient(135deg, #7c3aed, #a78bfa);
    color: white;
    font-size: 10px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid white;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    z-index: 10;
}

.story-rank-badge.top-3 {
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
}

.story-rank-badge.top-10 {
    background: linear-gradient(135deg, #10b981, #34d399);
}

.story-avatar.present {
    background: linear-gradient(45deg, #10b981, #34d399);
}

.story-avatar.absent {
    background: linear-gradient(45deg, #ef4444, #f87171);
}

.story-avatar-inner {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    border: 2px solid white;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f3f4f6;
}

.story-avatar-inner img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.story-avatar-inner i {
    font-size: 28px;
    color: #9ca3af;
}

.story-name {
    font-size: 10px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 85px;
}

.story-time {
    font-size: 9px;
    color: #6b7280;
}

.batch-section {
    background: white;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 15px;
    box-shadow: 0 1px 5px rgba(0,0,0,0.05);
}

.batch-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f3f4f6;
}

.batch-icon {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.batch-icon.morning {
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    color: white;
}

.batch-icon.evening {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
}

.attendance-divider {
    margin: 20px 0;
    border-top: 1px dashed #e5e7eb;
    position: relative;
}

.attendance-divider span {
    position: absolute;
    top: -10px;
    left: 50%;
    transform: translateX(-50%);
    background: white;
    padding: 0 12px;
    color: #9ca3af;
    font-size: 11px;
    font-weight: 600;
}
</style>

<div class="row">
    <h2 class="col-md-6"><i class="fas fa-tachometer-alt text-purple pb-4"></i> Current Month Fees Report</h2>

    <div class="mb-4 col-md-6">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
                <input type="date" class="form-control" name="start_date" value="<?php echo $start_date; ?>">
            </div>
            <div class="col-md-5">
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
                        </div>
                        <span class="badge bg-danger">₹<?php echo number_format($student['pending_fees'], 2); ?></span>
                    </div>
                </a>
                <?php endwhile; ?>
            </div>
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

    <!-- ============================================ -->
    <!-- TODAY'S ATTENDANCE - STORY STYLE -->
    <!-- ============================================ -->
    <div class="col-12 mt-4">
        <h2><i class="fas fa-calendar-check text-success"></i> Today's Attendance</h2>
        <p class="text-muted mb-3">
            <i class="fas fa-calendar"></i> <?php echo date('l, d F Y'); ?> | 
            <i class="fas fa-clock"></i> <?php echo date('h:i A'); ?>
        </p>
    </div>

    <!-- Morning Batch -->
    <div class="col-lg-6">
        <div class="batch-section">
            <div class="batch-header">
                <div class="batch-icon morning">
                    <i class="fas fa-sun"></i>
                </div>
                <div>
                    <h5 class="mb-0">Morning Batch</h5>
                    <small class="text-muted">
                        Present: <strong class="text-success"><?php echo $morning_present_count; ?></strong> | 
                        Absent: <strong class="text-danger"><?php echo $morning_absent_count; ?></strong>
                    </small>
                </div>
            </div>
            
            <?php if ($morning_present_count > 0 || $morning_absent_count > 0): ?>
<div class="mb-3">
    <div class="story-container flex-wrap">

        <!-- Present Students -->
        <?php while ($student = $morningPresent->fetch_assoc()): 
            $has_photo = !empty($student['photo']) && file_exists($student['photo']);
            $student_rank = isset($ranked_students[$student['id']]) ? $ranked_students[$student['id']] : 0;
            $rank_class = $student_rank <= 3 ? 'top-3' : ($student_rank <= 10 ? 'top-10' : '');
        ?>
        <div class="story-card" onclick="window.location.href='student_details.php?id=<?php echo $student['id']; ?>'">
            <div class="story-avatar present">
                <?php if ($student_rank > 0): ?>
                <div class="story-rank-badge <?php echo $rank_class; ?>">
                    <?php echo $student_rank; ?>
                </div>
                <?php endif; ?>
                <div class="story-avatar-inner">
                    <?php if ($has_photo): ?>
                        <img src="<?php echo htmlspecialchars($student['photo']); ?>" alt="">
                    <?php else: ?>
                        <i class="fas fa-user-graduate"></i>
                    <?php endif; ?>
                </div>
            </div>
            <div class="story-name"><?php echo htmlspecialchars($student['full_name']); ?></div>
            <div class="story-time">
                <?php echo formatIndianTime($student['check_in_time']); ?>
            </div>
        </div>
        <?php endwhile; ?>

        <!-- Absent Students -->
        <?php while ($student = $morningAbsent->fetch_assoc()): 
            $has_photo = !empty($student['photo']) && file_exists($student['photo']);
            $student_rank = isset($ranked_students[$student['id']]) ? $ranked_students[$student['id']] : 0;
            $rank_class = $student_rank <= 3 ? 'top-3' : ($student_rank <= 10 ? 'top-10' : '');
        ?>
        <div class="story-card" onclick="window.location.href='student_details.php?id=<?php echo $student['id']; ?>'">
            <div class="story-avatar absent">
                <?php if ($student_rank > 0): ?>
                <div class="story-rank-badge <?php echo $rank_class; ?>">
                    <?php echo $student_rank; ?>
                </div>
                <?php endif; ?>
                <div class="story-avatar-inner">
                    <?php if ($has_photo): ?>
                        <img src="<?php echo htmlspecialchars($student['photo']); ?>" alt="">
                    <?php else: ?>
                        <i class="fas fa-user-graduate"></i>
                    <?php endif; ?>
                </div>
            </div>
            <div class="story-name"><?php echo htmlspecialchars($student['full_name']); ?></div>
            <div class="story-time text-danger">
                <i class="fas fa-phone"></i>
            </div>
        </div>
        <?php endwhile; ?>

    </div>
</div>
<?php endif; ?>
        </div>
    </div>

    <!-- Evening Batch -->
    <div class="col-lg-6">
        <div class="batch-section">
            <div class="batch-header">
                <div class="batch-icon evening">
                    <i class="fas fa-moon"></i>
                </div>
                <div>
                    <h5 class="mb-0">Evening Batch</h5>
                    <small class="text-muted">
                        Present: <strong class="text-success"><?php echo $evening_present_count; ?></strong> | 
                        Absent: <strong class="text-danger"><?php echo $evening_absent_count; ?></strong>
                    </small>
                </div>
            </div>
            
            <?php if ($evening_present_count > 0 || $evening_absent_count > 0): ?>
<div class="mb-3">
    <div class="story-container flex-wrap">

        <!-- Present Students -->
        <?php while ($student = $eveningPresent->fetch_assoc()): 
            $has_photo = !empty($student['photo']) && file_exists($student['photo']);
            $student_rank = isset($ranked_students[$student['id']]) ? $ranked_students[$student['id']] : 0;
            $rank_class = $student_rank <= 3 ? 'top-3' : ($student_rank <= 10 ? 'top-10' : '');
        ?>
        <div class="story-card" onclick="window.location.href='student_details.php?id=<?php echo $student['id']; ?>'">
            <div class="story-avatar present">
                <?php if ($student_rank > 0): ?>
                <div class="story-rank-badge <?php echo $rank_class; ?>">
                    <?php echo $student_rank; ?>
                </div>
                <?php endif; ?>
                <div class="story-avatar-inner">
                    <?php if ($has_photo): ?>
                        <img src="<?php echo htmlspecialchars($student['photo']); ?>" alt="">
                    <?php else: ?>
                        <i class="fas fa-user-graduate"></i>
                    <?php endif; ?>
                </div>
            </div>
            <div class="story-name"><?php echo htmlspecialchars($student['full_name']); ?></div>
            <div class="story-time">
                <?php echo formatIndianTime($student['check_in_time']); ?>
            </div>
        </div>
        <?php endwhile; ?>

        <!-- Absent Students -->
        <?php while ($student = $eveningAbsent->fetch_assoc()): 
            $has_photo = !empty($student['photo']) && file_exists($student['photo']);
            $student_rank = isset($ranked_students[$student['id']]) ? $ranked_students[$student['id']] : 0;
            $rank_class = $student_rank <= 3 ? 'top-3' : ($student_rank <= 10 ? 'top-10' : '');
        ?>
        <div class="story-card" onclick="window.location.href='student_details.php?id=<?php echo $student['id']; ?>'">
            <div class="story-avatar absent">
                <?php if ($student_rank > 0): ?>
                <div class="story-rank-badge <?php echo $rank_class; ?>">
                    <?php echo $student_rank; ?>
                </div>
                <?php endif; ?>
                <div class="story-avatar-inner">
                    <?php if ($has_photo): ?>
                        <img src="<?php echo htmlspecialchars($student['photo']); ?>" alt="">
                    <?php else: ?>
                        <i class="fas fa-user-graduate"></i>
                    <?php endif; ?>
                </div>
            </div>
            <div class="story-name"><?php echo htmlspecialchars($student['full_name']); ?></div>
            <div class="story-time text-danger">
                <i class="fas fa-phone"></i>
            </div>
        </div>
        <?php endwhile; ?>

    </div>
</div>
<?php endif; ?>
        </div>
    </div>

    <!-- Recent Admissions -->
    <div class="col-12 mt-4">
        <h2><i class="fas fa-user-plus text-success"></i> Recent Top Admissions</h2>
        <p class="text-muted mb-0">Latest students enrolled</p>
    </div>

    <div class="col-12">
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
    </div>

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

<?php include 'includes/footer.php'; ?>
