<?php
session_start();
require_once './config/database.php';
require_once './config/auth.php';
requireLogin();

if ($_SESSION['admin_role'] === 'Administrator') {
    // Allowed
} elseif (!in_array($_SESSION['admin_role'], ['Super Admin', 'Admin'])) {
    $_SESSION['error'] = "Access denied.";
    header('Location: index.php');
    exit;
}

$inquiry_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($inquiry_id === 0) {
    header('Location: inquiries.php');
    exit();
}

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'add_note') {
        $note = sanitize($_POST['note']);
        $created_by = $_SESSION['admin_id'];

        $stmt = $conn->prepare("INSERT INTO inquiry_notes (inquiry_id, note, created_by) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $inquiry_id, $note, $created_by);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Note added successfully!";
        } else {
            $_SESSION['error'] = "Failed to add note.";
        }
        $stmt->close();
        header('Location: inquiry_details.php?id=' . $inquiry_id);
        exit();
    }

    if ($_POST['action'] === 'update_status') {
        $status = sanitize($_POST['status']);

        $stmt = $conn->prepare("UPDATE inquiries SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $inquiry_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Status updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update status.";
        }
        $stmt->close();
        header('Location: inquiry_details.php?id=' . $inquiry_id);
        exit();
    }

    // DIRECT ENROLLMENT - NO REDIRECT
    if ($_POST['action'] === 'enroll_student') {
        // Check if already converted
        $check = $conn->query("SELECT id FROM students WHERE inquiry_id = $inquiry_id");
        if ($check->num_rows > 0) {
            $_SESSION['error'] = "This inquiry has already been converted to a student.";
            header('Location: inquiry_details.php?id=' . $inquiry_id);
            exit();
        }

        $student_code = 'STU' . date('Ymd') . rand(1000, 9999);
        $full_name = sanitize($_POST['full_name']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone']);
        $address = sanitize($_POST['address']);
        $birthdate = sanitize($_POST['birthdate']);
        $category_id = (int)$_POST['category_id'];
        $course_id = (int)$_POST['course_id'];
        $duration_months = (int)$_POST['duration_months'];
        $total_fees = (float)$_POST['total_fees'];
        $enrollment_date = sanitize($_POST['enrollment_date']);

        $stmt = $conn->prepare("INSERT INTO students (student_code, full_name, email, phone, address, birthdate, category_id, course_id, duration_months, total_fees, enrollment_date, status, inquiry_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Active', ?)");
        $stmt->bind_param("ssssssiiidsi", $student_code, $full_name, $email, $phone, $address, $birthdate, $category_id, $course_id, $duration_months, $total_fees, $enrollment_date, $inquiry_id);

        if ($stmt->execute()) {
            $student_id = $stmt->insert_id;
            
            // Update inquiry status to converted
            $conn->query("UPDATE inquiries SET status = 'converted' WHERE id = $inquiry_id");
            
            // Add enrollment note
            $note = "Lead converted to student. Student Code: $student_code";
            $created_by = $_SESSION['admin_id'];
            $note_stmt = $conn->prepare("INSERT INTO inquiry_notes (inquiry_id, note, created_by) VALUES (?, ?, ?)");
            $note_stmt->bind_param("isi", $inquiry_id, $note, $created_by);
            $note_stmt->execute();
            $note_stmt->close();
            
            $_SESSION['success'] = "Student enrolled successfully! Student Code: $student_code";
        } else {
            $_SESSION['error'] = "Failed to enroll student.";
        }
        $stmt->close();
        header('Location: inquiry_details.php?id=' . $inquiry_id);
        exit();
    }
}

// Get inquiry details
$inquiry = $conn->query("
    SELECT i.*, a.full_name as created_by_name 
    FROM inquiries i
    LEFT JOIN admins a ON i.created_by = a.id
    WHERE i.id = $inquiry_id
")->fetch_assoc();

if (!$inquiry) {
    $_SESSION['error'] = "Inquiry not found!";
    header('Location: inquiries.php');
    exit();
}

// Get notes
$notes = $conn->query("
    SELECT n.*, a.full_name as created_by_name 
    FROM inquiry_notes n
    JOIN admins a ON n.created_by = a.id
    WHERE n.inquiry_id = $inquiry_id
    ORDER BY n.created_at DESC
");

// Check if converted to student
$converted_student = null;
if ($inquiry['status'] === 'converted') {
    $result = $conn->query("SELECT id, student_code, full_name FROM students WHERE inquiry_id = $inquiry_id");
    if ($result->num_rows > 0) {
        $converted_student = $result->fetch_assoc();
    }
}

// Get categories and courses for enrollment form
$categories = $conn->query("SELECT id, name FROM categories WHERE status = 'Active' ORDER BY name");
$all_courses = $conn->query("SELECT id, category_id, name FROM courses WHERE status = 'Active' ORDER BY name");

include 'includes/header.php';
?>

<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2">
            <li class="breadcrumb-item"><a href="inquiries.php">Leads</a></li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($inquiry['name']); ?></li>
        </ol>
    </nav>
    <h2><i class="fas fa-clipboard-list text-purple"></i> Lead Details</h2>
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

<div class="row g-4">
    <!-- Left Column -->
    <div class="col-lg-4">
        <div class="table-card">
            <h5 class="text-purple mb-3"><i class="fas fa-user"></i> Lead Information</h5>
            
            <div class="mb-3">
                <label class="text-muted mb-1">Name</label>
                <p class="mb-0"><strong><?php echo htmlspecialchars($inquiry['name']); ?></strong></p>
            </div>
            
            <div class="mb-3">
                <label class="text-muted mb-1">Mobile</label>
                <p class="mb-0">
                    <a href="tel:<?php echo $inquiry['mobile']; ?>">
                        <?php echo htmlspecialchars($inquiry['mobile']); ?>
                    </a>
                </p>
            </div>
            
            <?php if ($inquiry['email']): ?>
            <div class="mb-3">
                <label class="text-muted mb-1">Email</label>
                <p class="mb-0"><?php echo htmlspecialchars($inquiry['email']); ?></p>
            </div>
            <?php endif; ?>
            
            <div class="mb-3">
                <label class="text-muted mb-1">Course Interested</label>
                <p class="mb-0"><strong><?php echo htmlspecialchars($inquiry['course_interested']); ?></strong></p>
            </div>
            
            <div class="mb-3">
                <label class="text-muted mb-1">Source</label>
                <p class="mb-0"><span class="badge bg-secondary"><?php echo ucfirst($inquiry['source']); ?></span></p>
            </div>
            
            <div class="mb-3">
                <label class="text-muted mb-1">Status</label>
                <p class="mb-0">
                    <?php
                    $status_class = 'secondary';
                    if ($inquiry['status'] === 'new') $status_class = 'warning';
                    if ($inquiry['status'] === 'contacted') $status_class = 'info';
                    if ($inquiry['status'] === 'converted') $status_class = 'success';
                    if ($inquiry['status'] === 'closed') $status_class = 'danger';
                    ?>
                    <span class="badge bg-<?php echo $status_class; ?>"><?php echo ucfirst($inquiry['status']); ?></span>
                </p>
            </div>
            
            <div class="mb-3">
                <label class="text-muted mb-1">Created Date</label>
                <p class="mb-0"><?php echo date('d M Y, h:i A', strtotime($inquiry['created_at'])); ?></p>
            </div>
            
            <?php if ($inquiry['created_by_name']): ?>
            <div class="mb-3">
                <label class="text-muted mb-1">Created By</label>
                <p class="mb-0"><?php echo htmlspecialchars($inquiry['created_by_name']); ?></p>
            </div>
            <?php endif; ?>
            
            <?php if ($inquiry['message']): ?>
            <div class="mb-3">
                <label class="text-muted mb-1">Message</label>
                <p class="mb-0"><?php echo nl2br(htmlspecialchars($inquiry['message'])); ?></p>
            </div>
            <?php endif; ?>
            
            <hr>
            
            <!-- Update Status -->
            <form method="POST">
                <input type="hidden" name="action" value="update_status">
                <div class="mb-3">
                    <label class="form-label">Update Status</label>
                    <select class="form-select" name="status" required>
                        <option value="new" <?php echo $inquiry['status'] === 'new' ? 'selected' : ''; ?>>New</option>
                        <option value="contacted" <?php echo $inquiry['status'] === 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                        <option value="followup" <?php echo $inquiry['status'] === 'followup' ? 'selected' : ''; ?>>Follow-up</option>
                        <option value="converted" <?php echo $inquiry['status'] === 'converted' ? 'selected' : ''; ?>>Converted</option>
                        <option value="closed" <?php echo $inquiry['status'] === 'closed' ? 'selected' : ''; ?>>Closed</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">Update Status</button>
            </form>
            
            <?php if ($inquiry['status'] !== 'converted'): ?>
            <hr>
            <button class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#enrollStudentModal">
                <i class="fas fa-user-plus"></i> Enroll as Student
            </button>
            <?php endif; ?>
            
            <?php if ($converted_student): ?>
            <hr>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <strong>Enrolled as Student</strong><br>
                <?php if (in_array($_SESSION['admin_role'], ['Super Admin', 'Admin'])): ?>
                <a href="student_details.php?id=<?php echo $converted_student['id']; ?>" class="alert-link">
                    <?php echo $converted_student['full_name']; ?> (<?php echo $converted_student['student_code']; ?>)
                </a>
                <?php else: ?>
                <span class="text-success">
                    <?php echo $converted_student['full_name']; ?> (<?php echo $converted_student['student_code']; ?>)
                </span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Right Column - Notes -->
    <div class="col-lg-8">
        <div class="table-card">
            <h5 class="text-purple mb-3"><i class="fas fa-sticky-note"></i> Follow-up Notes</h5>
            
            <!-- Add Note Form -->
            <form method="POST" class="mb-4">
                <input type="hidden" name="action" value="add_note">
                <div class="mb-3">
                    <label class="form-label">Add Note</label>
                    <textarea class="form-control" name="note" rows="3" required placeholder="Enter follow-up note..."></textarea>
                </div>
                <button type="submit" class="btn btn-purple">
                    <i class="fas fa-plus"></i> Add Note
                </button>
            </form>
            
            <hr>
            
            <!-- Notes List -->
            <?php if ($notes->num_rows > 0): ?>
            <div class="notes-list">
                <?php while ($note = $notes->fetch_assoc()): ?>
                <div class="note-item mb-3 p-3 border rounded">
                    <div class="d-flex justify-content-between mb-2">
                        <strong><?php echo htmlspecialchars($note['created_by_name']); ?></strong>
                        <small class="text-muted">
                            <i class="fas fa-clock"></i> 
                            <?php echo date('d M Y, h:i A', strtotime($note['created_at'])); ?>
                        </small>
                    </div>
                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($note['note'])); ?></p>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No notes added yet.
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Enroll Student Modal -->
<div class="modal fade" id="enrollStudentModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus"></i> Enroll Student from Lead</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="enrollmentForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="enroll_student">
                    
                    <h6 class="text-purple mb-3">Personal Information</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" class="form-control" name="full_name" value="<?php echo htmlspecialchars($inquiry['name']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone *</label>
                            <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($inquiry['mobile']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($inquiry['email']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" name="birthdate">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" rows="2"></textarea>
                    </div>
                    
                    <hr>
                    <h6 class="text-purple mb-3">Course & Fees</h6>
                    
                    <div class="mb-3">
                        <label class="form-label">Category *</label>
                        <select class="form-select" name="category_id" id="enroll_category" required>
                            <option value="">Select Category</option>
                            <?php 
                            $categories->data_seek(0);
                            while ($cat = $categories->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $cat['id']; ?>">
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Course *</label>
                        <select class="form-select" name="course_id" id="enroll_course" required>
                            <option value="">Select Course</option>
                            <?php 
                            $all_courses->data_seek(0);
                            while ($course = $all_courses->fetch_assoc()): 
                            ?>
                            <option value="<?php echo $course['id']; ?>" data-category="<?php echo $course['category_id']; ?>">
                                <?php echo htmlspecialchars($course['name']); ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                        <small class="text-muted" id="enroll_course_help">Choose a course to see available durations</small>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Duration *</label>
                            <select class="form-select" name="duration_months" id="enroll_duration" required>
                                <option value="">Select Duration</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Total Fees *</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" class="form-control" name="total_fees" id="enroll_total_fees" step="0.01" required>
                            </div>
                            <small class="text-muted">You can adjust the amount if needed</small>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Enrollment Date *</label>
                            <input type="date" class="form-control" name="enrollment_date" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Enroll Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Category change - filter courses
document.getElementById('enroll_category')?.addEventListener('change', function() {
    const categoryId = this.value;
    const courseSelect = document.getElementById('enroll_course');
    const allOptions = courseSelect.querySelectorAll('option');
    
    allOptions.forEach(option => {
        if (option.value === '') {
            option.style.display = 'block';
        } else if (categoryId === '' || option.dataset.category === categoryId) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });
    
    courseSelect.value = '';
    document.getElementById('enroll_duration').innerHTML = '<option value="">Select Duration</option>';
    document.getElementById('enroll_total_fees').value = '';
});

// Course change - load fees
document.getElementById('enroll_course')?.addEventListener('change', function() {
    const courseId = this.value;
    const durationSelect = document.getElementById('enroll_duration');
    const feesInput = document.getElementById('enroll_total_fees');
    const courseHelp = document.getElementById('enroll_course_help');

    durationSelect.innerHTML = '<option value="">Select Duration</option>';
    feesInput.value = '';

    if (!courseId) return;

    fetch(`ajax/get_course_fees.php?course_id=${courseId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success && Array.isArray(data.fees) && data.fees.length > 0) {
                data.fees.forEach(fee => {
                    const opt = document.createElement('option');
                    opt.value = fee.duration_months;
                    opt.textContent = `${fee.duration_months} Months`;
                    opt.dataset.fee = fee.fee_amount;
                    durationSelect.appendChild(opt);
                });

                courseHelp.textContent = 'Durations loaded successfully';
                courseHelp.className = 'text-success';
            } else {
                [3, 6, 9, 12, 18, 24].forEach(m => {
                    const opt = document.createElement('option');
                    opt.value = m;
                    opt.textContent = `${m} Months`;
                    durationSelect.appendChild(opt);
                });

                courseHelp.textContent = 'No preset fees. Please enter custom amount.';
                courseHelp.className = 'text-warning';
            }
        })
        .catch(err => {
            console.error('AJAX error:', err);

            [3, 6, 9, 12, 18, 24].forEach(m => {
                const opt = document.createElement('option');
                opt.value = m;
                opt.textContent = `${m} Months`;
                durationSelect.appendChild(opt);
            });

            courseHelp.textContent = 'Error loading durations';
            courseHelp.className = 'text-danger';
        });
});

// Duration change - auto-fill fees
document.getElementById('enroll_duration')?.addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    if (!selected) return;

    const fee = selected.dataset.fee;
    const feesInput = document.getElementById('enroll_total_fees');
    const courseHelp = document.getElementById('enroll_course_help');

    if (fee) {
        feesInput.value = fee;
        courseHelp.textContent = 'Fee auto-filled. You can adjust it.';
        courseHelp.className = 'text-success';
    } else {
        feesInput.value = '';
        feesInput.focus();
        courseHelp.textContent = 'Please enter total fees manually.';
        courseHelp.className = 'text-info';
    }
});
</script>

<?php include 'includes/footer.php'; ?>