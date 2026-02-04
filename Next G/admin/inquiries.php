<?php
session_start();
require_once './config/database.php';
require_once './config/auth.php';
requireLogin();

// Administrator can only access inquiries
if ($_SESSION['admin_role'] === 'Administrator') {
    // Allowed
} elseif (!in_array($_SESSION['admin_role'], ['Super Admin', 'Admin'])) {
    $_SESSION['error'] = "Access denied.";
    header('Location: index.php');
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $name = sanitize($_POST['name']);
        $mobile = sanitize($_POST['mobile']);
        $email = sanitize($_POST['email']);
        $course_interested = sanitize($_POST['course_interested']);
        $message = sanitize($_POST['message']);
        $source = sanitize($_POST['source']);
        $created_by = $_SESSION['admin_id'];

        $stmt = $conn->prepare("INSERT INTO inquiries (name, mobile, email, course_interested, message, source, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $name, $mobile, $email, $course_interested, $message, $source, $created_by);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Inquiry added successfully!";
        } else {
            $_SESSION['error'] = "Failed to add inquiry.";
        }
        $stmt->close();
        header('Location: inquiries.php');
        exit();
    }

    if ($_POST['action'] === 'update_status') {
        $id = (int)$_POST['id'];
        $status = sanitize($_POST['status']);

        $stmt = $conn->prepare("UPDATE inquiries SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Status updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update status.";
        }
        $stmt->close();
        header('Location: inquiry_details.php?id=' . $id);
        exit();
    }
}

// Handle delete - Delete inquiry only if NOT converted
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Check if converted to student
    $check = $conn->query("SELECT id FROM students WHERE inquiry_id = $id");
    if ($check->num_rows > 0) {
        $_SESSION['error'] = "Cannot delete. This inquiry has been converted to a student.";
    } else {
        $conn->query("DELETE FROM inquiries WHERE id = $id");
        $_SESSION['success'] = "Inquiry deleted successfully!";
    }
    
    header('Location: inquiries.php');
    exit();
}

// Filters
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$source_filter = isset($_GET['source']) ? $_GET['source'] : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

$where = "1=1";
if ($status_filter) $where .= " AND i.status = '$status_filter'";
if ($source_filter) $where .= " AND i.source = '$source_filter'";
if ($start_date) $where .= " AND DATE(i.created_at) >= '$start_date'";
if ($end_date) $where .= " AND DATE(i.created_at) <= '$end_date'";

$inquiries = $conn->query("
    SELECT i.*, a.full_name as created_by_name 
    FROM inquiries i
    LEFT JOIN admins a ON i.created_by = a.id
    WHERE $where
    ORDER BY i.created_at DESC
");

// Stats
$stats = [];
$stats['total'] = $conn->query("SELECT COUNT(*) as count FROM inquiries")->fetch_assoc()['count'];
$stats['new'] = $conn->query("SELECT COUNT(*) as count FROM inquiries WHERE status = 'new'")->fetch_assoc()['count'];
$stats['contacted'] = $conn->query("SELECT COUNT(*) as count FROM inquiries WHERE status = 'contacted'")->fetch_assoc()['count'];
$stats['converted'] = $conn->query("SELECT COUNT(*) as count FROM inquiries WHERE status = 'converted'")->fetch_assoc()['count'];

// Get active courses for dropdown
$courses = $conn->query("SELECT id, name FROM courses WHERE status = 'Active' ORDER BY name");

include 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2><i class="fas fa-clipboard-list text-purple"></i> Leads Management</h2>
        <p class="text-muted mb-0">Manage course inquiries and leads</p>
    </div>
    <button class="btn btn-purple" data-bs-toggle="modal" data-bs-target="#addInquiryModal">
        <i class="fas fa-plus"></i> Add Lead
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

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Leads</p>
                        <h3 class="mb-0"><?php echo $stats['total']; ?></h3>
                    </div>
                    <div class="card-icon icon-purple">
                        <i class="fas fa-clipboard-list"></i>
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
                        <p class="text-muted mb-1">New Leads</p>
                        <h3 class="mb-0 text-warning"><?php echo $stats['new']; ?></h3>
                    </div>
                    <div class="card-icon icon-warning">
                        <i class="fas fa-exclamation-circle"></i>
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
                        <p class="text-muted mb-1">Contacted</p>
                        <h3 class="mb-0"><?php echo $stats['contacted']; ?></h3>
                    </div>
                    <div class="card-icon icon-purple">
                        <i class="fas fa-phone"></i>
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
                        <p class="text-muted mb-1">Converted</p>
                        <h3 class="mb-0 text-success"><?php echo $stats['converted']; ?></h3>
                    </div>
                    <div class="card-icon icon-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="table-card mb-4">
    <form method="GET" class="row g-3">
        <div class="col-md-3">
            <label class="form-label">Status</label>
            <select class="form-select" name="status">
                <option value="">All Status</option>
                <option value="new" <?php echo $status_filter === 'new' ? 'selected' : ''; ?>>New</option>
                <option value="contacted" <?php echo $status_filter === 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                <option value="followup" <?php echo $status_filter === 'followup' ? 'selected' : ''; ?>>Follow-up</option>
                <option value="converted" <?php echo $status_filter === 'converted' ? 'selected' : ''; ?>>Converted</option>
                <option value="closed" <?php echo $status_filter === 'closed' ? 'selected' : ''; ?>>Closed</option>
            </select>
        </div>
        
        <div class="col-md-3">
            <label class="form-label">Source</label>
            <select class="form-select" name="source">
                <option value="">All Sources</option>
                <option value="website" <?php echo $source_filter === 'website' ? 'selected' : ''; ?>>Website</option>
                <option value="manual" <?php echo $source_filter === 'manual' ? 'selected' : ''; ?>>Manual</option>
                <option value="phone" <?php echo $source_filter === 'phone' ? 'selected' : ''; ?>>Phone</option>
                <option value="walkin" <?php echo $source_filter === 'walkin' ? 'selected' : ''; ?>>Walk-in</option>
                <option value="referral" <?php echo $source_filter === 'referral' ? 'selected' : ''; ?>>Referral</option>
            </select>
        </div>
        
        <div class="col-md-2">
            <label class="form-label">Start Date</label>
            <input type="date" class="form-control" name="start_date" value="<?php echo $start_date; ?>">
        </div>
        
        <div class="col-md-2">
            <label class="form-label">End Date</label>
            <input type="date" class="form-control" name="end_date" value="<?php echo $end_date; ?>">
        </div>
        
        <div class="col-md-2">
            <label class="form-label">&nbsp;</label>
            <button type="submit" class="btn btn-purple w-100"><i class="fas fa-filter"></i> Filter</button>
        </div>
    </form>
</div>

<!-- Inquiries Table -->
<div class="table-card">
    <div class="mb-3">
        <input type="text" class="form-control" id="searchInquiry" placeholder="Search by name, mobile, email...">
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover" id="inquiriesTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>Course</th>
                    <th>Source</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $inquiries->data_seek(0);
                while ($inquiry = $inquiries->fetch_assoc()): 
                    $status_class = 'secondary';
                    if ($inquiry['status'] === 'new') $status_class = 'warning';
                    if ($inquiry['status'] === 'contacted') $status_class = 'info';
                    if ($inquiry['status'] === 'converted') $status_class = 'success';
                    if ($inquiry['status'] === 'closed') $status_class = 'danger';
                    
                    // Check if converted
                    $is_converted = $conn->query("SELECT id FROM students WHERE inquiry_id = {$inquiry['id']}")->num_rows > 0;
                ?>
                <tr onclick="window.location.href='inquiry_details.php?id=<?php echo $inquiry['id']; ?>'" style="cursor: pointer;">
                    <td><?php echo $inquiry['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($inquiry['name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($inquiry['mobile']); ?></td>
                    <td><small><?php echo htmlspecialchars($inquiry['course_interested']); ?></small></td>
                    <td><span class="badge bg-secondary"><?php echo ucfirst($inquiry['source']); ?></span></td>
                    <td><span class="badge bg-<?php echo $status_class; ?>"><?php echo ucfirst($inquiry['status']); ?></span></td>
                    <td><?php echo date('d M Y', strtotime($inquiry['created_at'])); ?></td>
                    <td>
                        <a onclick="event.stopPropagation();" href="inquiry_details.php?id=<?php echo $inquiry['id']; ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i>
                        </a>
                        <?php if (!$is_converted): ?>
                        <a onclick="event.stopPropagation();" href="?delete=<?php echo $inquiry['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this inquiry?')">
                            <i class="fas fa-trash"></i>
                        </a>
                        <?php else: ?>
                        <button onclick="event.stopPropagation();" class="btn btn-sm btn-secondary" disabled title="Cannot delete converted inquiry">
                            <i class="fas fa-lock"></i>
                        </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Inquiry Modal -->
<div class="modal fade" id="addInquiryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus"></i> Add New Lead</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mobile *</label>
                        <input type="tel" class="form-control" name="mobile" required pattern="[0-9]{10}" title="10-digit mobile number">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Course Interested *</label>
                        <select class="form-select" name="course_interested" required>
                            <option value="">Select Course</option>
                            <?php 
                            $courses->data_seek(0);
                            while ($course = $courses->fetch_assoc()): 
                            ?>
                            <option value="<?php echo htmlspecialchars($course['name']); ?>">
                                <?php echo htmlspecialchars($course['name']); ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Source *</label>
                        <select class="form-select" name="source" required>
                            <option value="manual">Manual</option>
                            <option value="phone">Phone</option>
                            <option value="walkin">Walk-in</option>
                            <option value="referral">Referral</option>
                            <option value="website">Website</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea class="form-control" name="message" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-purple">Add Lead</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('searchInquiry').addEventListener('keyup', function() {
    const filter = this.value.toUpperCase();
    const rows = document.querySelectorAll('#inquiriesTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent || row.innerText;
        row.style.display = text.toUpperCase().indexOf(filter) > -1 ? '' : 'none';
    });
});
</script>

<?php include 'includes/footer.php'; ?>