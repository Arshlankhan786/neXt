<?php
require_once '../admin/config/database.php';
require_once './student_auth.php';
requireStudentLogin();

$current_page = basename($_SERVER['PHP_SELF'], '.php');
$student = getCurrentStudent();

// Get student full details
$student_data = $conn->query("SELECT * FROM students WHERE id = {$student['id']}")->fetch_assoc();

// Get unread notifications count
$unread_count = $conn->query("SELECT COUNT(*) as count FROM student_notifications WHERE student_id = {$student['id']} AND is_read = 0")->fetch_assoc()['count'];

// Check if attendance marked today
$today = date('Y-m-d');
$already_marked_today = $conn->query("
    SELECT COUNT(*) as count 
    FROM student_attendance 
    WHERE student_id = {$student['id']} 
    AND attendance_date = '$today'
")->fetch_assoc()['count'] > 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucfirst(str_replace('_', ' ', $current_page)); ?> - Student Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/student-style.css">
     <link rel="icon" type="icon" href="../skill-development.png">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar student-navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <button class="btn btn-link text-white me-3" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-graduation-cap"></i> Student Portal
            </a>
            
            <div class="ms-auto d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn btn-link text-white dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle fa-lg"></i>
                        <span class="ms-2 d-none d-md-inline"><?php echo htmlspecialchars($student['name']); ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="px-3 py-2">
                            <div class="text-muted small">Student Code</div>
                            <div class="fw-bold"><?php echo htmlspecialchars($student['code']); ?></div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user"></i> My Profile</a></li>
                        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="student-sidebar" id="sidebar">
        <div class="text-white text-center py-3 border-bottom border-light">
            <div  class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 35px;color: #7c3aed;">
                <i class="fas fa-user-graduate"></i>
            </div>
            <h5 class="mt-2 mb-0"><?php echo htmlspecialchars($student['name']); ?></h5>
            <small><?php echo htmlspecialchars($student['code']); ?></small>
        </div>
        
        <ul class="sidebar-menu">
            <li class="<?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            </li>
     <li class="<?php echo $current_page === 'attendance' ? 'active' : ''; ?>">
    <a href="attendance.php">
        <i class="fas fa-calendar-check"></i> Attendance
        <?php if (!$already_marked_today): ?>
        <span class="badge bg-danger ms-2">!</span>
        <?php endif; ?>
    </a>
</li>
            <li class="<?php echo $current_page === 'profile' ? 'active' : ''; ?>">
                <a href="profile.php"><i class="fas fa-user"></i> My Profile</a>
            </li>
            
            <!-- ADD AFTER attendance menu item -->
<li class="<?php echo $current_page === 'course_progress' ? 'active' : ''; ?>">
    <a href="course_progress.php"><i class="fas fa-chart-line"></i> Course Progress</a>
</li>
<li class="<?php echo $current_page === 'projects' ? 'active' : ''; ?>">
    <a href="projects.php"><i class="fas fa-project-diagram"></i> Projects</a>
</li>
            <li class="<?php echo $current_page === 'payments' ? 'active' : ''; ?>">
                <a href="payments.php"><i class="fas fa-indian-rupee-sign"></i> Payment History</a>
            </li>
            <li class="<?php echo $current_page === 'receipts' ? 'active' : ''; ?>">
                <a href="receipts.php"><i class="fas fa-receipt"></i> Receipts</a>
            </li>
            <li class="<?php echo $current_page === 'notifications' ? 'active' : ''; ?>">
                <a href="notifications.php">
                    <i class="fas fa-bell"></i> Notifications
                    <?php if ($unread_count > 0): ?>
                    <span class="badge bg-danger ms-2"><?php echo $unread_count; ?></span>
                    <?php endif; ?>
                </a>
            </li>
        </ul>
    </div>

    <!--  Main Content -->
    <div class="student-content" id="mainContent">