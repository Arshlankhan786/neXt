<?php
// Start output buffering to prevent header issues
ob_start();

require_once 'config/database.php';
require_once 'config/auth.php';
requireLogin();

$current_page = basename($_SERVER['PHP_SELF'], '.php');
$admin = getCurrentAdmin();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucfirst(str_replace('_', ' ', $current_page)); ?> - Academy Fees Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Additional responsive fixes */
        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1rem;
            }

            .navbar-brand i {
                display: none;
            }

            #userDropdown .ms-2 {
                display: none;
            }

            #userDropdown .badge {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.1rem;
            }
        }
    </style>
</head>

<body>
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-purple fixed-top">
        <div class="container-fluid">
            <button class="btn btn-link text-white me-2 me-md-3 p-1" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <a class="navbar-brand flex-grow-1" href="index.php">
                <i class="fas fa-graduation-cap d-none d-sm-inline"></i>
                <span class="d-none d-sm-inline">Next Academy</span>
                <span class="d-inline d-sm-none">Academy</span>
            </a>

            <div class="ms-auto">
                <div class="dropdown">
                    <button class="btn btn-link text-white dropdown-toggle text-decoration-none p-1" type="button"
                        id="userDropdown" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle fa-lg"></i>
                        <span class="ms-2 d-none d-md-inline"><?php echo htmlspecialchars($admin['name']); ?></span>
                        <span
                            class="badge bg-light text-purple ms-2 d-none d-lg-inline"><?php echo htmlspecialchars($admin['role']); ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="px-3 py-2 d-md-none">
                            <div class="text-muted small">Logged in as</div>
                            <div class="fw-bold"><?php echo htmlspecialchars($admin['name']); ?></div>
                            <div><span
                                    class="badge bg-purple mt-1"><?php echo htmlspecialchars($admin['role']); ?></span>
                            </div>
                        </li>
                        <li class="d-md-none">
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i>
                                Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h4><i class="fas fa-user-circle fa-lg "></i>
                <div class="fw-bold"><?php echo htmlspecialchars($admin['name']); ?></div>
            </h4>
        </div>
        <ul class="sidebar-menu">
           
          
           


<!-- REPLACE EXISTING MENU VISIBILITY CHECKS -->
<!-- Change lines for students, payments, courses, etc. to exclude HR -->

<?php if (in_array($_SESSION['admin_role'], ['Super Admin', 'Admin'])): ?>
     <li class="<?php echo $current_page === 'index' ? 'active' : ''; ?>">
                <a href="index.php"><i class="fas fa-tachometer-alt"></i> Current Report</a>
            </li>
<li class="<?php echo $current_page === 'students' ? 'active' : ''; ?>">
    <a href="students.php"><i class="fas fa-user-graduate"></i> Students</a>
</li>
<li class="<?php echo $current_page === 'past_students' || $current_page === 'past_student_details' ? 'active' : ''; ?>">
    <a href="past_students.php"><i class="fas fa-history"></i> Past Students</a>
</li>
<li class="<?php echo $current_page === 'payments' ? 'active' : ''; ?>">
    <a href="payments.php"><i class="fa-solid fa-indian-rupee-sign"></i> Payments</a>
</li>
<li class="<?php echo $current_page === 'courses' ? 'active' : ''; ?>">
    <a href="courses.php"><i class="fas fa-book"></i> Courses</a>
</li>
<li class="<?php echo $current_page === 'students_course_expiry' ? 'active' : ''; ?>">
    <a href="students_course_expiry.php"><i class="fas fa-calendar-check"></i> Students Timeline</a>
</li>
<li class="<?php echo $current_page === 'send_notification' ? 'active' : ''; ?>">
    <a href="send_notification.php"><i class="fas fa-paper-plane"></i> Send Notification</a>
</li>
<li class="<?php echo $current_page === 'attendance_report' ? 'active' : ''; ?>">
    <a href="attendance_report.php"><i class="fas fa-clipboard-check"></i> Attendance Report</a>
</li>

<?php endif; ?>
   
<!-- REPLACE THE INQUIRIES MENU ITEM (around line 90) -->

<?php if (in_array($_SESSION['admin_role'], ['Super Admin', 'Admin', 'Administrator'])): ?>
<li class="<?php echo $current_page === 'inquiries' || $current_page === 'inquiry_details' ? 'active' : ''; ?>">
    <a href="inquiries.php"><i class="fas fa-clipboard-list"></i> Leads Management</a>
</li>
<li class="<?php echo $current_page === 'task_manager' ? 'active' : ''; ?>">
    <a href="task_manager.php"><i class="fas fa-tasks"></i> My Tasks</a>
</li>
            <!--<li class="<?php // echo $current_page === 'completed_tasks' ? 'active' : ''; ?>">-->
            <!--    <a href="completed_tasks.php"><i class="fas fa-check-circle"></i> Completed Tasks</a>-->
            <!--</li>-->
<?php endif; ?>
<li class="<?php echo $current_page === 'categories' ? 'active' : ''; ?>">
                <a href="categories.php"><i class="fas fa-folder"></i> Categories</a>
            </li>

            <?php if (isSuperAdmin()): ?>
                <li class="<?php echo $current_page === 'reports' ? 'active' : ''; ?>">
                    <a href="reports.php"><i class="fas fa-chart-bar"></i> Overall Report</a>
                </li>
                <li class="<?php echo $current_page === 'admins' ? 'active' : ''; ?>">
                    <a href="admins.php"><i class="fas fa-user-shield"></i> Admins</a>
                </li>
            <?php endif; ?>


           
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">