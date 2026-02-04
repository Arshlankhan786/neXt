<?php
include 'includes/header.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $category_id = (int)$_POST['category_id'];
            $name = sanitize($_POST['name']);
            $description = sanitize($_POST['description']);
            $status = sanitize($_POST['status']);
            
            $stmt = $conn->prepare("INSERT INTO courses (category_id, name, description, status) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $category_id, $name, $description, $status);
            
            if ($stmt->execute()) {
                $course_id = $stmt->insert_id;
                
                // Insert course fees for different durations
                $durations = [3, 6, 9, 12, 18, 24];
                foreach ($durations as $duration) {
                    if (isset($_POST["fee_$duration"]) && !empty($_POST["fee_$duration"])) {
                        $fee = (float)$_POST["fee_$duration"];
                        $feeStmt = $conn->prepare("INSERT INTO course_fees (course_id, duration_months, fee_amount) VALUES (?, ?, ?)");
                        $feeStmt->bind_param("iid", $course_id, $duration, $fee);
                        $feeStmt->execute();
                        $feeStmt->close();
                    }
                }
                
                $_SESSION['success'] = "Course added successfully!";
            } else {
                $_SESSION['error'] = "Failed to add course.";
            }
            $stmt->close();
            
        } elseif ($_POST['action'] === 'edit') {
            $id = (int)$_POST['id'];
            $category_id = (int)$_POST['category_id'];
            $name = sanitize($_POST['name']);
            $description = sanitize($_POST['description']);
            $status = sanitize($_POST['status']);
            
            $stmt = $conn->prepare("UPDATE courses SET category_id = ?, name = ?, description = ?, status = ? WHERE id = ?");
            $stmt->bind_param("isssi", $category_id, $name, $description, $status, $id);
            
            if ($stmt->execute()) {
                // Update fees
                $durations = [3, 6, 9, 12, 18, 24];
                foreach ($durations as $duration) {
                    if (isset($_POST["fee_$duration"]) && !empty($_POST["fee_$duration"])) {
                        $fee = (float)$_POST["fee_$duration"];
                        $conn->query("INSERT INTO course_fees (course_id, duration_months, fee_amount) 
                                     VALUES ($id, $duration, $fee) 
                                     ON DUPLICATE KEY UPDATE fee_amount = $fee");
                    }
                }
                
                $_SESSION['success'] = "Course updated successfully!";
            } else {
                $_SESSION['error'] = "Failed to update course.";
            }
            $stmt->close();
        }
        
        header('Location: courses.php');
        exit();
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    $check = $conn->query("SELECT COUNT(*) as count FROM students WHERE course_id = $id");
    $result = $check->fetch_assoc();
    
    if ($result['count'] > 0) {
        $_SESSION['error'] = "Cannot delete course. It has enrolled students.";
    } else {
        $conn->query("DELETE FROM courses WHERE id = $id");
        $_SESSION['success'] = "Course deleted successfully!";
    }
    
    header('Location: courses.php');
    exit();
}

$courses = $conn->query("SELECT c.*, cat.name as category_name,
                        (SELECT COUNT(*) FROM students WHERE course_id = c.id) as student_count 
                        FROM courses c 
                        JOIN categories cat ON c.category_id = cat.id 
                        ORDER BY c.created_at DESC");

$categories = $conn->query("SELECT * FROM categories WHERE status = 'Active' ORDER BY name");
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2><i class="fas fa-book text-purple"></i> Courses</h2>
        <p class="text-muted mb-0">Manage courses and fees</p>
    </div>
    <button class="btn btn-purple" data-bs-toggle="modal" data-bs-target="#addCourseModal">
        <i class="fas fa-plus"></i> Add Course
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
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Course Name</th>
                    <th>Category</th>
                    <th>Students</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($course = $courses->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $course['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($course['name']); ?></strong></td>
                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($course['category_name']); ?></span></td>
                    <td><span class="badge bg-info"><?php echo $course['student_count']; ?> students</span></td>
                    <td><span class="badge status-<?php echo strtolower($course['status']); ?>"><?php echo $course['status']; ?></span></td>
                    <td>
                        <!-- <button class="btn btn-sm btn-info" onclick="viewFees(<?php // echo $course['id']; ?>)">
                            <i class="fas fa-dollar-sign"></i> Fees
                        </button> -->
                        <button class="btn btn-sm btn-primary" onclick="editCourse(<?php echo $course['id']; ?>)">
                            <i class="fas fa-edit"></i>
                        </button>
                        <a href="?delete=<?php echo $course['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this course?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Course Modal -->
<div class="modal fade" id="addCourseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus"></i> Add New Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category *</label>
                            <select class="form-select" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php $categories->data_seek(0); while ($cat = $categories->fetch_assoc()): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status *</label>
                            <select class="form-select" name="status" required>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Course Name *</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2"></textarea>
                    </div>
                    
                    <hr>
                    <h6 class="text-purple mb-3"><i class="fa-solid fa-indian-rupee-sign"></i> Fee Structure (Optional - can be set later)</h6>
                    
                    <div class="row g-3">
                        <?php foreach ([3, 6, 9, 12, 18, 24] as $duration): ?>
                        <div class="col-md-4">
                            <label class="form-label"><?php echo $duration; ?> Months Fee</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" class="form-control" name="fee_<?php echo $duration; ?>" step="0.01" min="0">
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-purple">Add Course</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Course Modal -->
<div class="modal fade" id="editCourseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body" id="editCourseForm">
                    <div class="text-center"><div class="spinner-border text-purple"></div></div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Fees Modal -->
<div class="modal fade" id="viewFeesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-dollar-sign"></i> Course Fees</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="feesContent">
                <div class="text-center"><div class="spinner-border text-purple"></div></div>
            </div>
        </div>
    </div>
</div>

<script>
function viewFees(courseId) {
    fetch(`ajax/get_course_fees.php?course_id=${courseId}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                let html = '<div class="table-responsive"><table class="table"><thead><tr><th>Duration</th><th>Fee Amount</th></tr></thead><tbody>';
                data.fees.forEach(f => {
                    html += `<tr><td>${f.duration_months} Months</td><td>₹${parseFloat(f.fee_amount).toLocaleString('en-IN')}</td></tr>`;
                });
                html += '</tbody></table></div>';
                document.getElementById('feesContent').innerHTML = html;
            }
        });
    new bootstrap.Modal(document.getElementById('viewFeesModal')).show();
}

function editCourse(id) {
    fetch(`ajax/get_course.php?id=${id}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                let html = `<input type="hidden" name="action" value="edit"><input type="hidden" name="id" value="${id}">
                    <div class="row"><div class="col-md-6 mb-3"><label>Category *</label><select class="form-select" name="category_id" required>
                    <?php $categories->data_seek(0); while ($cat = $categories->fetch_assoc()): ?>
                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endwhile; ?></select></div>
                    <div class="col-md-6 mb-3"><label>Status *</label><select class="form-select" name="status" required>
                    <option value="Active">Active</option><option value="Inactive">Inactive</option></select></div></div>
                    <div class="mb-3"><label>Course Name *</label><input type="text" class="form-control" name="name" value="${data.course.name}" required></div>
                    <div class="mb-3"><label>Description</label><textarea class="form-control" name="description" rows="2">${data.course.description || ''}</textarea></div>
                    <hr><h6 class="text-purple mb-3">Fee Structure</h6><div class="row g-3">`;
                [3,6,9,12,18,24].forEach(d => {
                    let fee = data.fees.find(f => f.duration_months == d);
                    html += `<div class="col-md-4"><label>${d} Months Fee</label><div class="input-group"><span class="input-group-text">₹</span>
                        <input type="number" class="form-control" name="fee_${d}" value="${fee ? fee.fee_amount : ''}" step="0.01" min="0"></div></div>`;
                });
                html += `</div><div class="modal-footer mt-3"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-purple">Update Course</button></div>`;
                document.getElementById('editCourseForm').innerHTML = html;
                document.querySelector('[name="category_id"]').value = data.course.category_id;
                document.querySelector('[name="status"]').value = data.course.status;
            }
        });
    new bootstrap.Modal(document.getElementById('editCourseModal')).show();
}
</script>

<?php include 'includes/footer.php'; ?>