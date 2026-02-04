<?php
// ============================================
// HANDLE FORM SUBMISSION (NO OUTPUT BEFORE THIS POINT)
// ============================================

session_start();
require_once './config/database.php'; // Only include ONCE

// Do NOT reconnect in loops or functions  // include required db first, not header!

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

   
    if ($_POST['action'] === 'add') {
        $student_code = 'STU' . date('Ymd') . rand(1000, 9999);
        $full_name = sanitize($_POST['full_name']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone']);
        $address = sanitize($_POST['address']);
        $category_id = (int)$_POST['category_id'];
        $course_id = (int)$_POST['course_id'];
        $duration_months = (int)$_POST['duration_months'];
        $batch = sanitize($_POST['batch']); // NEW: Batch field
        $total_fees = (float)$_POST['total_fees'];
        $enrollment_date = sanitize($_POST['enrollment_date']);

        // UPDATED SQL: Include batch column
        $stmt = $conn->prepare("INSERT INTO students (student_code, full_name, email, phone, address, category_id, course_id, duration_months, batch, total_fees, enrollment_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Active')");
        $stmt->bind_param("sssssiissds", $student_code, $full_name, $email, $phone, $address, $category_id, $course_id, $duration_months, $batch, $total_fees, $enrollment_date);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Student enrolled successfully! Student Code: $student_code";
        } else {
            $_SESSION['error'] = "Failed to enroll student.";
        }
        $stmt->close();
        header('Location: students.php');
        exit();
    }

    if ($_POST['action'] === 'mark_completed') {
        $id = (int)$_POST['id'];
        $completion_date = date('Y-m-d');
        $stmt = $conn->prepare("UPDATE students SET status = 'Completed', completion_date = ? WHERE id = ?");
        $stmt->bind_param("si", $completion_date, $id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Student marked as completed!";
        } else {
            $_SESSION['error'] = "Failed to update status.";
        }
        $stmt->close();
        header('Location: students.php');
        exit();
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("UPDATE students SET status = 'Deleted' WHERE id = $id");
    $_SESSION['success'] = "Student deleted successfully!";
    header('Location: students.php');
    exit();
}

// ============================================
// DATA FETCH after headers clear
// ============================================
$students = $conn->query("
    SELECT 
        s.id,
        s.student_code,
        s.full_name,
        s.phone,
        s.email,
        s.enrollment_date,
        s.duration_months,
        s.total_fees,
        s.status,
        c.name as course_name,
        COALESCE(SUM(p.amount_paid), 0) as total_paid,
        (s.total_fees - COALESCE(SUM(p.amount_paid), 0)) as pending_fees,
        EXISTS(
            SELECT 1 FROM payments p2 
            WHERE p2.student_id = s.id 
            AND YEAR(p2.payment_date) = YEAR(CURDATE())
            AND MONTH(p2.payment_date) = MONTH(CURDATE())
        ) as paid_this_month
    FROM students s 
    JOIN courses c ON s.course_id = c.id 
    LEFT JOIN payments p ON s.id = p.student_id
    WHERE s.status = 'Active'
    GROUP BY s.id
    ORDER BY s.created_at DESC
");

$categories = $conn->query("SELECT id, name FROM categories ORDER BY name");
$courses = $conn->query("SELECT * FROM courses WHERE status = 'Active' ORDER BY name");

// ============================================
// NOW include header (safe)
// ============================================
include 'includes/header.php';

?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2><i class="fas fa-user-graduate text-purple"></i> Active Students</h2>
        <p class="text-muted mb-0">Manage currently enrolled students</p>
    </div>
    <button class="btn btn-purple" data-bs-toggle="modal" data-bs-target="#addStudentModal">
        <i class="fas fa-plus"></i> Enroll Student
    </button>
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

<div class="table-card">
    <div class="mb-3">
        <input type="text" class="form-control" id="searchStudent" placeholder="Search students by name, code, phone...">
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover" id="studentsTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Course</th>
                    <th>Duration</th>
                    <th>Total Fees</th>
                    <th>Paid</th>
                    <th>Pending</th>
                    <!-- <th>Status</th> -->
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($student = $students->fetch_assoc()): 
                    // Payment status
                    $payment_status = 'pending';
                    if ($student['pending_fees'] <= 0) {
                        $payment_status = 'paid';
                    } elseif ($student['total_paid'] > 0) {
                        $payment_status = 'partial';
                    }
                    
                    // Overdue check: no payment this month AND has pending
                    $is_overdue = (!$student['paid_this_month'] && $student['pending_fees'] > 0);
                ?>
                <tr onclick="window.location.href='student_details.php?id=<?php echo $student['id']; ?>'" class="<?php echo $is_overdue ? 'table-danger' : ''; ?>" data-student-code="<?php echo htmlspecialchars($student['student_code']); ?>">
                    <td>
                        <?php echo htmlspecialchars($student['full_name']); ?>
                        <?php if ($is_overdue): ?>
                            <br><span class="badge bg-danger"><i class="fas fa-exclamation-triangle"></i> OVERDUE</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($student['phone']); ?></td>
                    <td><small><?php echo htmlspecialchars($student['course_name']); ?></small></td>
                    <td><?php echo $student['duration_months']; ?> M</td>
                    <td>₹<?php echo number_format($student['total_fees'], 2); ?></td>
                    <td><span class="badge bg-success">₹<?php echo number_format($student['total_paid'], 2); ?></span></td>
                    <td><span class="badge bg-warning">₹<?php echo number_format($student['pending_fees'], 2); ?></span></td>
                    <!-- <td><span class="badge status-<?php // echo $payment_status; ?>"><?php // echo ucfirst($payment_status); ?></span></td> -->
                    <td>
                     
                      <div class="d-flex gap-2">
                          <?php if ($student['pending_fees'] <= 0): ?>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Mark this student as completed?');">
                            <input type="hidden" name="action" value="mark_completed">
                            <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
                            <button onclick="event.stopPropagation();" type="submit" class="btn btn-sm btn-primary" title="Mark Completed">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                      
                        <?php endif; ?>
                        <a onclick="event.stopPropagation();" href="?delete=<?php echo $student['id']; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Delete this student? This will move them to Past Students.')">
                            <i class="fas fa-trash"></i>
                        </a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Student Modal (NO EMI LOGIC) -->
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus"></i> Enroll New Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="studentForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    
                    <h6 class="text-purple mb-3">Personal Information</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" class="form-control" name="full_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone *</label>
                            <input type="tel" class="form-control" name="phone" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Enrollment Date *</label>
                            <input type="date" class="form-control" name="enrollment_date" value="<?php echo date('Y-m-d'); ?>" required>
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
                        <select class="form-select" name="category_id" required>
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
                        <select class="form-select" name="course_id" id="course_select" required>
                            <option value="">Select Course</option>
                            <?php 
                            $courses->data_seek(0);
                            while ($course = $courses->fetch_assoc()): 
                            ?>
                            <option value="<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                        <small class="text-muted" id="course_help">Choose a course to see available durations</small>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Duration *</label>
                            <select class="form-select" name="duration_months" id="duration_select" required>
                                <option value="">Select Duration</option>
                            </select>
                        </div>
                        
                            <!-- NEW: Batch Selection -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Batch *</label>
                            <select class="form-select" name="batch" required>
                                <option value="Morning">Morning</option>
                                <option value="Evening">Evening</option>
                            </select>
                            <small class="text-muted">Student's class timing</small>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Total Fees *</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" class="form-control" name="total_fees" id="total_fees" step="0.01" required>
                            </div>
                            <small class="text-muted">You can adjust the amount if needed</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-purple">Enroll Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// ============================
// SEARCH FUNCTIONALITY - FIXED
// Searches by Student Code (hidden) + visible fields
// ============================
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchStudent');
    const table = document.getElementById('studentsTable');
    
    if (searchInput && table) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toUpperCase();
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            
            for (let i = 0; i < rows.length; i++) {
                let found = false;
                
                // Check student code (hidden in data attribute)
                const studentCode = rows[i].getAttribute('data-student-code');
                if (studentCode && studentCode.toUpperCase().indexOf(filter) > -1) {
                    found = true;
                }
                
                // If not found in code, search in visible cells
                if (!found) {
                    const cells = rows[i].getElementsByTagName('td');
                    for (let j = 0; j < cells.length; j++) {
                        const cellText = cells[j].textContent || cells[j].innerText;
                        if (cellText.toUpperCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }
                
                rows[i].style.display = found ? '' : 'none';
            }
        });
    }
});

// ============================
// MODAL EVENT - COURSE SELECTION
// ============================
document.getElementById('addStudentModal').addEventListener('shown.bs.modal', function () {
    const courseSelect = document.getElementById('course_select');
    const durationSelect = document.getElementById('duration_select');
    const feesInput = document.getElementById('total_fees');
    const courseHelp = document.getElementById('course_help');

    // Reset on modal open
    durationSelect.innerHTML = '<option value="">Select Duration</option>';
    feesInput.value = '';

    // ============================
    // COURSE CHANGE
    // ============================
    courseSelect.onchange = function () {
        const courseId = this.value;

        // Reset fields
        durationSelect.innerHTML = '<option value="">Select Duration</option>';
        feesInput.value = '';

        if (!courseId) return;

        fetch(`ajax/get_course_fees.php?course_id=${courseId}`)
            .then(res => res.json())
            .then(data => {
                // If preset fees exist
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
                    // No preset fees → default durations
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

                // Fallback to default durations
                [3, 6, 9, 12, 18, 24].forEach(m => {
                    const opt = document.createElement('option');
                    opt.value = m;
                    opt.textContent = `${m} Months`;
                    durationSelect.appendChild(opt);
                });

                courseHelp.textContent = 'Error loading durations';
                courseHelp.className = 'text-danger';
            });
    };

    // ============================
    // DURATION CHANGE
    // ============================
    durationSelect.onchange = function () {
        const selected = this.options[this.selectedIndex];
        if (!selected) return;

        const fee = selected.dataset.fee;

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
    };
});
</script>

<?php include 'includes/footer.php'; ?>
