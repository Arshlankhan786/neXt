<?php
include 'includes/header.php';

$success = '';
$error = '';

// Get full student details
$student_details = $conn->query("
    SELECT s.*, c.name as course_name, cat.name as category_name
    FROM students s
    JOIN courses c ON s.course_id = c.id
    JOIN categories cat ON s.category_id = cat.id
    WHERE s.id = {$student['id']}
")->fetch_assoc();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_profile') {
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);
        
        if (empty($phone)) {
            $error = "Phone number is required.";
        } else {
            $stmt = $conn->prepare("UPDATE students SET email = ?, phone = ?, address = ? WHERE id = ?");
            $stmt->bind_param("sssi", $email, $phone, $address, $student['id']);
            
            if ($stmt->execute()) {
                $success = "Profile updated successfully!";
                // Refresh student details
                $student_details = $conn->query("
                    SELECT s.*, c.name as course_name, cat.name as category_name
                    FROM students s
                    JOIN courses c ON s.course_id = c.id
                    JOIN categories cat ON s.category_id = cat.id
                    WHERE s.id = {$student['id']}
                ")->fetch_assoc();
            } else {
                $error = "Failed to update profile.";
            }
            $stmt->close();
        }
    }
    
    if ($_POST['action'] === 'change_password') {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Get current password
        $result = $conn->query("SELECT password FROM students WHERE id = {$student['id']}");
        $user = $result->fetch_assoc();
        
        if (password_verify($current_password, $user['password'])) {
            if ($new_password === $confirm_password) {
                if (strlen($new_password) >= 6) {
                    $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE students SET password = ? WHERE id = ?");
                    $stmt->bind_param("si", $hashed, $student['id']);
                    
                    if ($stmt->execute()) {
                        $success = "Password changed successfully!";
                    } else {
                        $error = "Failed to change password.";
                    }
                    $stmt->close();
                } else {
                    $error = "Password must be at least 6 characters.";
                }
            } else {
                $error = "New passwords do not match.";
            }
        } else {
            $error = "Current password is incorrect.";
        }
    }
}

// Check photo - FIXED PATH
$has_photo = !empty($student_details['photo']) && file_exists(__DIR__ . '/../admin/' . $student_details['photo']);
?>

<div class="page-header">
    <h2><i class="fas fa-user text-purple"></i> My Profile</h2>
    <p class="text-muted mb-0">View and manage your personal information</p>
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

<div class="row g-4">
    <!-- Profile Info Card -->
    <div class="col-lg-4">
        <div class="table-card text-center">
            
            <?php if ($has_photo): ?>
                <img src="../admin/<?php echo htmlspecialchars($student_details['photo']); ?>" 
                     alt="Profile Photo"
                     class="rounded-circle mb-3"
                     style="width:150px;height:150px;object-fit:cover;border:5px solid var(--primary-color);">
            <?php else: ?>
                <div class="bg-purple text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 120px; height: 120px; font-size: 50px;">
                    <i class="fas fa-user-graduate"></i>
                </div>
            <?php endif; ?>
            
            <h4 class="mb-1"><?php echo htmlspecialchars($student_details['full_name']); ?></h4>
            <p class="text-muted mb-2"><?php echo htmlspecialchars($student_details['student_code']); ?></p>
            <span class="badge bg-success mb-3"><?php echo $student_details['status']; ?></span>
            
            <hr>
            
            <div class="text-start">
                <div class="mb-3">
                    <label class="text-muted mb-1"><i class="fas fa-folder"></i> Category</label>
                    <p class="mb-0"><strong><?php echo htmlspecialchars($student_details['category_name']); ?></strong></p>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted mb-1"><i class="fas fa-book"></i> Course</label>
                    <p class="mb-0"><strong><?php echo htmlspecialchars($student_details['course_name']); ?></strong></p>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted mb-1"><i class="fas fa-clock"></i> Duration</label>
                    <p class="mb-0"><?php echo $student_details['duration_months']; ?> months</p>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted mb-1"><i class="fas fa-calendar"></i> Enrolled</label>
                    <p class="mb-0"><?php echo date('d M Y', strtotime($student_details['enrollment_date'])); ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Profile Forms -->
    <div class="col-lg-8">
        <!-- Update Contact Info -->
        <div class="table-card mb-4">
            <h5 class="text-purple mb-3"><i class="fas fa-edit"></i> Update Contact Information</h5>
            <form method="POST">
                <input type="hidden" name="action" value="update_profile">
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($student_details['full_name']); ?>" disabled>
                        <small class="text-muted">Contact admin to change name</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Student Code</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($student_details['student_code']); ?>" disabled>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($student_details['email']); ?>" >
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone Number *</label>
                        <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($student_details['phone']); ?>" >
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea class="form-control" name="address" rows="3"><?php echo htmlspecialchars($student_details['address']); ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-purple">
                    <i class="fas fa-save"></i> Update Profile
                </button>
            </form>
        </div>
        
     
    </div>
</div>

<script>
// Password match validation
document.getElementById('passwordForm')?.addEventListener('submit', function(e) {
    const newPass = document.getElementById('new_password').value;
    const confirmPass = document.getElementById('confirm_password').value;
    
    if (newPass !== confirmPass) {
        e.preventDefault();
        alert('New passwords do not match!');
        return false;
    }
});
</script>

<?php include 'includes/footer.php'; ?>