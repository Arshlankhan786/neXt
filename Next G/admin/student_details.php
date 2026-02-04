<?php
session_start();
require_once 'config/database.php';
require_once 'config/auth.php';
requireLogin();

$student_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($student_id === 0) {
    header('Location: students.php');
    exit();
}

// ---- handle update ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_student') {
        $full_name = sanitize($_POST['full_name']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone']);
        $address = sanitize($_POST['address']);
        $birthdate = sanitize($_POST['birthdate']);
        $category_id = (int)$_POST['category_id'];
        $course_id = (int)$_POST['course_id'];
        $duration_months = (int)$_POST['duration_months'];
        $total_fees = (float)$_POST['total_fees'];

        $stmt = $conn->prepare("UPDATE students SET full_name=?, email=?, phone=?, address=?, birthdate=?, category_id=?, course_id=?, duration_months=?, total_fees=? WHERE id=?");
        $stmt->bind_param("sssssiidii", $full_name, $email, $phone, $address, $birthdate, $category_id, $course_id, $duration_months, $total_fees, $student_id);

        $stmt->execute();
        $stmt->close();

        $_SESSION['success'] = "Student details updated successfully!";
        header("Location: student_details.php?id=$student_id");
        exit();
    }
}

// ---- handle photo upload ----
// ---- handle photo upload ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['student_photo'])) {
    $upload_dir = __DIR__ . '/uploads/students/';
    
    // Create directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $file = $_FILES['student_photo'];
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    // Validation
    if ($file['error'] === UPLOAD_ERR_OK) {
        if (!in_array($file['type'], $allowed_types)) {
            $_SESSION['error'] = "Invalid file type. Only JPG, PNG, GIF allowed.";
        } elseif ($file['size'] > $max_size) {
            $_SESSION['error'] = "File too large. Maximum 5MB allowed.";
        } else {
            // Delete old photo
            $old_photo = $conn->query("SELECT photo FROM students WHERE id=$student_id")->fetch_assoc()['photo'];
            if ($old_photo && file_exists($old_photo)) {
                unlink($old_photo);
            }
            
            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'student_' . $student_id . '_' . time() . '.' . $extension;
            $target_path = $upload_dir . $filename;
            $db_path = 'uploads/students/' . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                $stmt = $conn->prepare("UPDATE students SET photo = ? WHERE id = ?");
                $stmt->bind_param("si", $db_path, $student_id);
                
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Photo uploaded successfully!";
                } else {
                    $_SESSION['error'] = "Failed to save photo to database.";
                    unlink($target_path);
                }
                $stmt->close();
            } else {
                $_SESSION['error'] = "Failed to upload photo. Check folder permissions.";
            }
        }
    } else {
        $_SESSION['error'] = "Upload error: " . $file['error'];
    }
    
    header("Location: student_details.php?id=$student_id");
    exit();
}

// ---- handle delete photo ----
if (isset($_GET['delete_photo'])) {
    $photo = $conn->query("SELECT photo FROM students WHERE id=$student_id")->fetch_assoc()['photo'];
    if ($photo && file_exists($photo)) unlink($photo);
    $conn->query("UPDATE students SET photo=NULL WHERE id=$student_id");
    $_SESSION['success']="Photo deleted!";
    header("Location: student_details.php?id=$student_id");
    exit();
}

// Get student details with category
$student = $conn->query("SELECT s.*, c.name as course_name, cat.id as category_id, cat.name as category_name
                        FROM students s
                        JOIN courses c ON s.course_id = c.id
                        JOIN categories cat ON s.category_id = cat.id
                        WHERE s.id = $student_id AND s.status = 'Active'")->fetch_assoc();

if (!$student) {
    $_SESSION['error'] = "Student not found!";
    header('Location: students.php');
    exit();
}

// Get all categories and courses for edit
$categories = $conn->query("SELECT id, name FROM categories WHERE status = 'Active' ORDER BY name");
$all_courses = $conn->query("SELECT id, category_id, name FROM courses WHERE status = 'Active' ORDER BY name");

$has_photo = !empty($student['photo']) && file_exists($student['photo']);

// Get payment summary
$payment_summary = $conn->query("SELECT 
                                COALESCE(SUM(amount_paid), 0) as total_paid,
                                COUNT(*) as payment_count
                                FROM payments 
                                WHERE student_id = $student_id")->fetch_assoc();

$total_paid = $payment_summary['total_paid'] ?? 0;
$pending = $student['total_fees'] - $total_paid;

$paid_this_month = $conn->query("SELECT COUNT(*) as count FROM payments 
                                 WHERE student_id = $student_id 
                                 AND YEAR(payment_date) = YEAR(CURDATE())
                                 AND MONTH(payment_date) = MONTH(CURDATE())")->fetch_assoc()['count'] > 0;

$is_overdue = (!$paid_this_month && $pending > 0);

// Get payment history
$payments = $conn->query("SELECT p.*, a.full_name as admin_name 
                         FROM payments p 
                         JOIN admins a ON p.created_by = a.id 
                         WHERE p.student_id = $student_id 
                         ORDER BY p.payment_date DESC, p.created_at DESC");

$payment_status = 'Pending';
if ($pending <= 0) $payment_status = 'Paid';
elseif ($total_paid > 0) $payment_status = 'Partial';

// Calculate age if birthdate exists
$age = '';
if ($student['birthdate']) {
    $birthdate = new DateTime($student['birthdate']);
    $today = new DateTime();
    $age = $today->diff($birthdate)->y;
}
?>
<?php include 'includes/header.php'; ?>

<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2">
            <li class="breadcrumb-item"><a href="students.php">Students</a></li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($student['full_name']); ?></li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <h2><i class="fas fa-user-graduate text-purple"></i> Student Details</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editStudentModal">
            <i class="fas fa-edit"></i> Edit Details
        </button>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php
// After existing queries, add these:

// Get course progress
$course_end_date = date('Y-m-d', strtotime($student['enrollment_date'] . ' + ' . $student['duration_months'] . ' months'));
$total_days = (strtotime($course_end_date) - strtotime($student['enrollment_date'])) / 86400;
$days_elapsed = (strtotime('now') - strtotime($student['enrollment_date'])) / 86400;
$course_progress = min(100, ($days_elapsed / $total_days) * 100);

// Get topics
$topics = $conn->query("SELECT * FROM course_topics WHERE student_id = $student_id ORDER BY created_at DESC");
$topics_completed = $conn->query("SELECT COUNT(*) as count FROM course_topics WHERE student_id = $student_id AND status = 'Completed'")->fetch_assoc()['count'];
$topics_total = $topics->num_rows;

// Get projects
$projects = $conn->query("SELECT * FROM student_projects WHERE student_id = $student_id ORDER BY created_at DESC");
?>

<!-- INSERT BEFORE Student Information Card (around line 200) -->
<!-- Payment Summary Cards - Move to TOP -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card dashboard-card">
            <div class="card-body">
                <p class="text-muted mb-1">Total Fees</p>
                <h4 class="mb-0 text-purple">₹<?php echo number_format($student['total_fees'], 2); ?></h4>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card dashboard-card">
            <div class="card-body">
                <p class="text-muted mb-1">Amount Paid</p>
                <h4 class="mb-0 text-success">₹<?php echo number_format($total_paid, 2); ?></h4>
                <small class="text-muted"><?php echo $payment_summary['payment_count']; ?> payments</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card dashboard-card">
            <div class="card-body">
                <p class="text-muted mb-1">Pending Amount</p>
                <h4 class="mb-0 text-danger">₹<?php echo number_format($pending, 2); ?></h4>
                <span class="badge status-<?php echo strtolower($payment_status); ?>"><?php echo $payment_status; ?></span>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card dashboard-card">
            <div class="card-body">
                <p class="text-muted mb-1">Course Progress</p>
                <h4 class="mb-0 text-info"><?php echo number_format($course_progress, 1); ?>%</h4>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar bg-info" style="width: <?php echo $course_progress; ?>%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Course Timeline Card -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="table-card">
            <!--<h5 class="text-purple mb-3"><i class="fas fa-calendar-alt"></i> Course Timeline</h5>-->
            <div class="row">
                <div class="col-md-3">
                    <label class="text-muted mb-1">Enrollment Date</label>
                    <p class="mb-0"><strong><?php echo date('d M Y', strtotime($student['enrollment_date'])); ?></strong></p>
                </div>
                <div class="col-md-3">
                    <label class="text-muted mb-1">Course Duration</label>
                    <p class="mb-0"><strong><?php echo $student['duration_months']; ?> Months</strong></p>
                </div>
                <div class="col-md-3">
                    <label class="text-muted mb-1">Expected End Date</label>
                    <p class="mb-0"><strong><?php echo date('d M Y', strtotime($course_end_date)); ?></strong></p>
                </div>
                <div class="col-md-3">
                    <label class="text-muted mb-1">Days Remaining</label>
                    <?php 
                    $days_remaining = (strtotime($course_end_date) - strtotime('now')) / 86400;
                    $badge_class = $days_remaining < 0 ? 'danger' : ($days_remaining < 30 ? 'warning' : 'success');
                    ?>
                    <p class="mb-0">
                        <span class="badge bg-<?php echo $badge_class; ?>">
                            <?php echo $days_remaining < 0 ? 'Expired ' . abs(round($days_remaining)) . ' days ago' : round($days_remaining) . ' days left'; ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- THEN continue with existing Student Information Card -->

<div class="row g-4">
    <!-- Student Information Card -->
    <div class="col-lg-4">
        <div class="table-card">
            <div class="text-center mb-3">
                <?php if ($has_photo): ?>
                    <img src="<?php echo htmlspecialchars($student['photo']); ?>" 
                         alt="Student Photo" 
                         class="rounded-circle mb-3" 
                         style="width: 150px; height: 150px; object-fit: cover; border: 5px solid <?php echo $is_overdue ? '#ef4444' : '#7c3aed'; ?>;">
                <?php else: ?>
                    <div class="<?php echo $is_overdue ? 'bg-danger' : 'bg-purple'; ?> text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px; font-size: 40px;">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                <?php endif; ?>
                
                <h4 class="mt-3 mb-1"><?php echo htmlspecialchars($student['full_name']); ?></h4>
                <p class="text-muted mb-2"><?php echo $student['student_code']; ?></p>
                <?php if ($age): ?>
                    <p class="text-muted mb-2"><i class="fas fa-birthday-cake"></i> <?php echo $age; ?> years old</p>
                <?php endif; ?>
                <span class="badge status-<?php echo strtolower($student['status']); ?>"><?php echo $student['status']; ?></span>
                <?php if ($is_overdue): ?>
                    <br><span class="badge bg-danger mt-2"><i class="fas fa-exclamation-triangle"></i> OVERDUE - No payment this month</span>
                <?php endif; ?>
                
                <div class="mt-3">
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadPhotoModal">
                        <i class="fas fa-camera"></i> <?php echo $has_photo ? 'Change' : 'Upload'; ?> Photo
                    </button>
                    <?php if ($has_photo): ?>
                    <a href="?id=<?php echo $student_id; ?>&delete_photo=1" 
                       class="btn btn-sm btn-danger" 
                       onclick="return confirm('Delete this photo?')">
                        <i class="fas fa-trash"></i> Delete
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <hr>
            
            <div class="mb-3">
                <label class="text-muted mb-1"><i class="fas fa-phone"></i> Phone</label>
                <p class="mb-0"><strong><?php echo htmlspecialchars($student['phone']); ?></strong></p>
            </div>
            
            <?php if ($student['email']): ?>
            <div class="mb-3">
                <label class="text-muted mb-1"><i class="fas fa-envelope"></i> Email</label>
                <p class="mb-0"><?php echo htmlspecialchars($student['email']); ?></p>
            </div>
            <?php endif; ?>
            
            <?php if ($student['birthdate']): ?>
            <div class="mb-3">
                <label class="text-muted mb-1"><i class="fas fa-birthday-cake"></i> Date of Birth</label>
                <p class="mb-0"><?php echo date('d M Y', strtotime($student['birthdate'])); ?></p>
            </div>
            <?php endif; ?>
            
            <?php if ($student['address']): ?>
            <div class="mb-3">
                <label class="text-muted mb-1"><i class="fas fa-map-marker-alt"></i> Address</label>
                <p class="mb-0"><?php echo htmlspecialchars($student['address']); ?></p>
            </div>
            <?php endif; ?>
            
            <hr>
            
            <div class="mb-3">
                <label class="text-muted mb-1"><i class="fas fa-folder"></i> Category</label>
                <p class="mb-0"><span class="badge bg-secondary"><?php echo htmlspecialchars($student['category_name']); ?></span></p>
            </div>
            
            <div class="mb-3">
                <label class="text-muted mb-1"><i class="fas fa-book"></i> Course</label>
                <p class="mb-0"><strong><?php echo htmlspecialchars($student['course_name']); ?></strong></p>
            </div>
            
            <div class="mb-3">
                <label class="text-muted mb-1"><i class="fas fa-clock"></i> Duration</label>
                <p class="mb-0"><?php echo $student['duration_months']; ?> months</p>
            </div>
            
            <div class="mb-3">
                <label class="text-muted mb-1"><i class="fa-solid fa-indian-rupee-sign"></i> Total Fees</label>
                <p class="mb-0"><strong>₹<?php echo number_format($student['total_fees'], 2); ?></strong></p>
            </div>
            
            <div class="mb-3">
                <label class="text-muted mb-1"><i class="fas fa-calendar"></i> Enrollment Date</label>
                <p class="mb-0"><?php echo date('d M Y', strtotime($student['enrollment_date'])); ?></p>
            </div>
            
            <hr>
            
            <!-- Student Portal Access Section (keep existing code) -->
            <h5 class="text-purple mb-3"><i class="fas fa-key"></i> Student Portal Access</h5>
            
            <?php if ($student['login_enabled']): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <strong>Portal Access Enabled</strong>
                <div class="mt-2">
                    <strong>Username:</strong> <?php echo htmlspecialchars($student['username']); ?>
                </div>
            </div>
            
            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                <i class="fas fa-sync"></i> Reset Password
            </button>
            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#disableLoginModal">
                <i class="fas fa-lock"></i> Disable Access
            </button>
            
            <?php else: ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> <strong>Portal Access Disabled</strong>
            </div>
            
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#enableLoginModal">
                <i class="fas fa-unlock"></i> Enable Access
            </button>
            <?php endif; ?>
            
            <hr class="mt-4">
            
            <div class="d-grid gap-2">
                <a href="add_payment.php?student_id=<?php echo $student_id; ?>" class="btn btn-purple">
                   <i class="fa-solid fa-indian-rupee-sign"></i> Add Payment
                </a>
                <a href="send_notification.php?student_id=<?php echo $student_id; ?>" class="btn btn-info">
                    <i class="fas fa-bell"></i> Send Notification
                </a>
                <button class="btn btn-outline-primary" onclick="window.print()">
                    <i class="fas fa-print"></i> Print Details
                </button>
            </div>
        </div>
    </div>
    
    <!-- Fee Summary and Payments (keep existing code) -->
    <div class="col-lg-8">
        <!-- Fee Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <p class="text-muted mb-1">Total Fees</p>
                        <h4 class="mb-0 text-purple">₹<?php echo number_format($student['total_fees'], 2); ?></h4>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <p class="text-muted mb-1">Amount Paid</p>
                        <h4 class="mb-0 text-success">₹<?php echo number_format($total_paid, 2); ?></h4>
                        <small class="text-muted"><?php echo $payment_summary['payment_count']; ?> payments</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <p class="text-muted mb-1">Pending Amount</p>
                        <h4 class="mb-0 text-danger">₹<?php echo number_format($pending, 2); ?></h4>
                        <span class="badge status-<?php echo strtolower($payment_status); ?>"><?php echo $payment_status; ?></span>
                    </div>
                </div>
            </div>
            
            <!-- ADD BEFORE </div> closing row tag (around line 380) -->

    <!-- Course Topics Management -->
    <div class="col-lg-6 mt-4">
        <div class="table-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="text-purple mb-0"><i class="fas fa-list-check"></i> Course Topics (<?php echo $topics_completed; ?>/<?php echo $topics_total; ?>)</h5>
                <button class="btn btn-sm btn-purple" onclick="showAddTopicModal()">
                    <i class="fas fa-plus"></i> Add Topic
                </button>
            </div>
            
            <?php if ($topics->num_rows > 0): ?>
            <div class="list-group">
                <?php while ($topic = $topics->fetch_assoc()): ?>
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       id="topic_<?php echo $topic['id']; ?>"
                                       <?php echo $topic['status'] === 'Completed' ? 'checked' : ''; ?>
                                       onchange="toggleTopic(<?php echo $topic['id']; ?>, this.checked)">
                                <label class="form-check-label" for="topic_<?php echo $topic['id']; ?>">
                                    <strong><?php echo htmlspecialchars($topic['topic_name']); ?></strong>
                                    <?php if ($topic['description']): ?>
                                    <br><small class="text-muted"><?php echo htmlspecialchars($topic['description']); ?></small>
                                    <?php endif; ?>
                                    <?php if ($topic['completed_date']): ?>
                                    <br><small class="text-success"><i class="fas fa-check"></i> Completed on <?php echo date('d M Y', strtotime($topic['completed_date'])); ?></small>
                                    <?php endif; ?>
                                </label>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-danger" onclick="deleteTopic(<?php echo $topic['id']; ?>)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No topics added yet.
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Projects Management -->
    <div class="col-lg-6 mt-4">
        <div class="table-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="text-purple mb-0"><i class="fas fa-project-diagram"></i> Projects (<?php echo $projects->num_rows; ?>)</h5>
                <button class="btn btn-sm btn-purple" onclick="showAddProjectModal()">
                    <i class="fas fa-plus"></i> Add Project
                </button>
            </div>
            
            <?php if ($projects->num_rows > 0): ?>
            <div class="list-group">
                <?php while ($project = $projects->fetch_assoc()): 
                    $status_colors = [
                        'Not Started' => 'secondary',
                        'In Progress' => 'primary',
                        'Completed' => 'success',
                        'On Hold' => 'warning'
                    ];
                ?>
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <strong><?php echo htmlspecialchars($project['project_name']); ?></strong>
                            <span class="badge bg-<?php echo $status_colors[$project['status']]; ?> ms-2">
                                <?php echo $project['status']; ?>
                            </span>
                            <?php if ($project['description']): ?>
                            <p class="mb-1 mt-2"><small><?php echo htmlspecialchars($project['description']); ?></small></p>
                            <?php endif; ?>
                            <?php if ($project['start_date'] || $project['end_date']): ?>
                            <small class="text-muted">
                                <i class="fas fa-calendar"></i>
                                <?php if ($project['start_date']): echo date('d M Y', strtotime($project['start_date'])); endif; ?>
                                <?php if ($project['end_date']): echo ' - ' . date('d M Y', strtotime($project['end_date'])); endif; ?>
                            </small>
                            <?php endif; ?>
                            <?php if ($project['remarks']): ?>
                            <p class="mb-0 mt-1"><small class="text-info"><i class="fas fa-comment"></i> <?php echo htmlspecialchars($project['remarks']); ?></small></p>
                            <?php endif; ?>
                        </div>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" onclick='editProject(<?php echo json_encode($project); ?>)'>
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-outline-danger" onclick="deleteProject(<?php echo $project['id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No projects assigned yet.
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Topic Modal -->
<div class="modal fade" id="addTopicModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus"></i> Add Course Topic</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Topic Name *</label>
                    <input type="text" class="form-control" id="topic_name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" id="topic_description" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-purple" onclick="addTopic()">Add Topic</button>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Project Modal -->
<div class="modal fade" id="projectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="projectModalTitle"><i class="fas fa-plus"></i> Add Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="project_id">
                <div class="mb-3">
                    <label class="form-label">Project Name *</label>
                    <input type="text" class="form-control" id="project_name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" id="project_description" rows="2"></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="project_start_date">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control" id="project_end_date">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status *</label>
                    <select class="form-select" id="project_status">
                        <option value="Not Started">Not Started</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                        <option value="On Hold">On Hold</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Remarks</label>
                    <textarea class="form-control" id="project_remarks" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-purple" onclick="saveProject()">Save Project</button>
            </div>
        </div>
    </div>
</div>

<script>
const studentId = <?php echo $student_id; ?>;

// Topics Management
function showAddTopicModal() {
    document.getElementById('topic_name').value = '';
    document.getElementById('topic_description').value = '';
    new bootstrap.Modal(document.getElementById('addTopicModal')).show();
}

function addTopic() {
    const topicName = document.getElementById('topic_name').value;
    const description = document.getElementById('topic_description').value;
    
    if (!topicName) {
        alert('Topic name is required');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'add_topic');
    formData.append('student_id', studentId);
    formData.append('topic_name', topicName);
    formData.append('description', description);
    
    fetch('ajax/manage_topics.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to add topic');
        }
    });
}

function toggleTopic(topicId, checked) {
    const formData = new FormData();
    formData.append('action', 'toggle_topic');
    formData.append('topic_id', topicId);
    formData.append('status', checked ? 'Completed' : 'Pending');
    
    fetch('ajax/manage_topics.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function deleteTopic(topicId) {
    if (!confirm('Delete this topic?')) return;
    
    const formData = new FormData();
    formData.append('action', 'delete_topic');
    formData.append('topic_id', topicId);
    
    fetch('ajax/manage_topics.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

// Projects Management
function showAddProjectModal() {
    document.getElementById('projectModalTitle').innerHTML = '<i class="fas fa-plus"></i> Add Project';
    document.getElementById('project_id').value = '';
    document.getElementById('project_name').value = '';
    document.getElementById('project_description').value = '';
    document.getElementById('project_start_date').value = '';
    document.getElementById('project_end_date').value = '';
    document.getElementById('project_status').value = 'Not Started';
    document.getElementById('project_remarks').value = '';
    new bootstrap.Modal(document.getElementById('projectModal')).show();
}

function editProject(project) {
    document.getElementById('projectModalTitle').innerHTML = '<i class="fas fa-edit"></i> Edit Project';
    document.getElementById('project_id').value = project.id;
    document.getElementById('project_name').value = project.project_name;
    document.getElementById('project_description').value = project.description || '';
    document.getElementById('project_start_date').value = project.start_date || '';
    document.getElementById('project_end_date').value = project.end_date || '';
    document.getElementById('project_status').value = project.status;
    document.getElementById('project_remarks').value = project.remarks || '';
    new bootstrap.Modal(document.getElementById('projectModal')).show();
}

function saveProject() {
    const projectId = document.getElementById('project_id').value;
    const projectName = document.getElementById('project_name').value;
    
    if (!projectName) {
        alert('Project name is required');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', projectId ? 'update_project' : 'add_project');
    if (projectId) formData.append('project_id', projectId);
    formData.append('student_id', studentId);
    formData.append('project_name', projectName);
    formData.append('description', document.getElementById('project_description').value);
    formData.append('start_date', document.getElementById('project_start_date').value);
    formData.append('end_date', document.getElementById('project_end_date').value);
    formData.append('status', document.getElementById('project_status').value);
    formData.append('remarks', document.getElementById('project_remarks').value);
    
    fetch('ajax/manage_projects.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to save project');
        }
    });
}

function deleteProject(projectId) {
    if (!confirm('Delete this project?')) return;
    
    const formData = new FormData();
    formData.append('action', 'delete_project');
    formData.append('project_id', projectId);
    
    fetch('ajax/manage_projects.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>
        </div>
        
        <!-- Payment History -->
        <div class="table-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="text-purple mb-0"><i class="fas fa-history"></i> Payment History</h5>
                <?php if ($payments->num_rows > 0): ?>
                <button class="btn btn-sm btn-secondary" onclick="exportTableToCSV('paymentHistoryTable', 'student_payments.csv')">
                    <i class="fas fa-download"></i> Export
                </button>
                <?php endif; ?>
            </div>
            
            <?php if ($payments->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover" id="paymentHistoryTable">
                    <thead>
                        <tr>
                            <th>Receipt #</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Received By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($payment = $payments->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($payment['receipt_number'] ?? 'N/A'); ?></strong></td>
                            <td><?php echo date('d M Y', strtotime($payment['payment_date'])); ?></td>
                            <td><span class="badge bg-success fs-6">₹<?php echo number_format($payment['amount_paid'], 2); ?></span></td>
                            <td><span class="badge bg-info"><?php echo $payment['payment_method']; ?></span></td>
                            <td><small><?php echo htmlspecialchars($payment['admin_name']); ?></small></td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="window.open('receipt.php?id=<?php echo $payment['id']; ?>', '_blank')">
                                    <i class="fas fa-receipt"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> No payments recorded yet.
                <a href="add_payment.php?student_id=<?php echo $student_id; ?>" class="alert-link">Add first payment</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Edit Student Modal -->
<div class="modal fade" id="editStudentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Student Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editStudentForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_student">
                    
                    <h6 class="text-purple mb-3">Personal Information</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" class="form-control" name="full_name" value="<?php echo htmlspecialchars($student['full_name']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone *</label>
                            <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($student['phone']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($student['email']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" name="birthdate" value="<?php echo $student['birthdate']; ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" rows="2"><?php echo htmlspecialchars($student['address']); ?></textarea>
                    </div>
                    
                    <hr>
                    <h6 class="text-purple mb-3">Course & Fees Information</h6>
                    
                    <div class="mb-3">
                        <label class="form-label">Category *</label>
                        <select class="form-select" name="category_id" id="edit_category" required>
                            <option value="">Select Category</option>
                            <?php 
                            $categories->data_seek(0);
                            while ($cat = $categories->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($cat['id'] == $student['category_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Course *</label>
                        <select class="form-select" name="course_id" id="edit_course" required>
                            <option value="">Select Course</option>
                            <?php 
                            $all_courses->data_seek(0);
                            while ($course = $all_courses->fetch_assoc()): 
                            ?>
                            <option value="<?php echo $course['id']; ?>" 
                                    data-category="<?php echo $course['category_id']; ?>"
                                    <?php echo ($course['id'] == $student['course_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($course['name']); ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Duration (Months) *</label>
                            <select class="form-select" name="duration_months" id="edit_duration" required>
                                <option value="">Select Duration</option>
                                <?php foreach ([3, 6, 9, 12, 18, 24] as $d): ?>
                                <option value="<?php echo $d; ?>" <?php echo ($d == $student['duration_months']) ? 'selected' : ''; ?>>
                                    <?php echo $d; ?> Months
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Total Fees (₹) *</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" class="form-control" name="total_fees" id="edit_total_fees" 
                                       value="<?php echo $student['total_fees']; ?>" step="0.01" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> <strong>Warning:</strong> Changing course fees will not affect existing payment records. Only the pending amount calculation will be updated.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-purple">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Upload Photo Modal (keep existing) -->
<div class="modal fade" id="uploadPhotoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-camera"></i> <?php echo $has_photo ? 'Change' : 'Upload'; ?> Student Photo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="student_photo" class="form-label">Select Photo</label>
                        <input type="file" 
                               class="form-control" 
                               id="student_photo" 
                               name="student_photo" 
                               accept="image/jpeg,image/jpg,image/png,image/gif"
                               required>
                        <div class="form-text">
                            <i class="fas fa-info-circle"></i> Accepted formats: JPG, JPEG, PNG, GIF (Max 5MB)
                        </div>
                    </div>
                    
                    <div id="imagePreview" class="text-center" style="display: none;">
                        <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 100%; max-height: 300px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload Photo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Portal Access Modals (keep existing Enable/Reset/Disable modals) -->
<!-- Enable Login Modal -->
<div class="modal fade" id="enableLoginModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-unlock"></i> Enable Student Portal Access</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="student_portal_actions.php">
                <div class="modal-body">
                    <input type="hidden" name="action" value="enable_login">
                    <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> This will create login credentials for the student.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Username *</label>
                        <input type="text" class="form-control" name="username" 
                               value="<?php echo strtolower(str_replace(' ', '', $student['student_code'])); ?>" required>
                        <small class="text-muted">Student will use this to login</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Password *</label>
                        <input type="text" class="form-control" name="password" 
                               value="<?php echo substr($student['phone'], -6); ?>" required>
                        <small class="text-muted">Default: Last 6 digits of phone number</small>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> <strong>Important:</strong> Share these credentials with the student securely.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Enable Portal Access</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-sync"></i> Reset Student Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="student_portal_actions.php">
                <div class="modal-body">
                    <input type="hidden" name="action" value="reset_password">
                    <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
                    
                    <div class="alert alert-warning">
                        Resetting password for: <strong><?php echo htmlspecialchars($student['full_name']); ?></strong>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">New Password *</label>
                        <input type="text" class="form-control" name="new_password" 
                               value="<?php echo substr($student['phone'], -6); ?>" required>
                        <small class="text-muted">Recommended: Last 6 digits of phone</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Disable Login Modal -->
<div class="modal fade" id="disableLoginModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-lock"></i> Disable Portal Access</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="student_portal_actions.php">
                <div class="modal-body">
                    <input type="hidden" name="action" value="disable_login">
                    <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
                    
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> <strong>Warning!</strong><br>
                        This will disable portal access for <strong><?php echo htmlspecialchars($student['full_name']); ?></strong>.
                        The student will not be able to login.
                    </div>
                    
                    <p>Are you sure you want to continue?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Yes, Disable Access</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Image preview functionality
document.getElementById('student_photo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Validate file size
        if (file.size > 5000000) {
            alert('File size must be less than 5MB');
            this.value = '';
            document.getElementById('imagePreview').style.display = 'none';
            return;
        }
        
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            alert('Only JPG, JPEG, PNG, and GIF files are allowed');
            this.value = '';
            document.getElementById('imagePreview').style.display = 'none';
            return;
        }
        
        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
});
</script>

<?php include 'includes/footer.php'; ?>