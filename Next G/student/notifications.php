<?php
include 'includes/header.php';

// Mark notification as read if requested
if (isset($_GET['read']) && isset($_GET['id'])) {
    $notif_id = (int)$_GET['id'];
    $conn->query("UPDATE student_notifications SET is_read = 1 WHERE id = $notif_id AND student_id = {$student['id']}");
    header('Location: notifications.php');
    exit;
}

// Mark all as read
if (isset($_GET['mark_all_read'])) {
    $conn->query("UPDATE student_notifications SET is_read = 1 WHERE student_id = {$student['id']}");
    header('Location: notifications.php');
    exit;
}

// Delete notification
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $notif_id = (int)$_GET['id'];
    $conn->query("DELETE FROM student_notifications WHERE id = $notif_id AND student_id = {$student['id']}");
    header('Location: notifications.php');
    exit;
}

// Get filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Build query
$query = "SELECT * FROM student_notifications WHERE student_id = {$student['id']}";

if ($filter === 'unread') {
    $query .= " AND is_read = 0";
} elseif ($filter === 'read') {
    $query .= " AND is_read = 1";
}

$query .= " ORDER BY created_at DESC";

$notifications = $conn->query($query);

// Get counts
$total_count = $conn->query("SELECT COUNT(*) as count FROM student_notifications WHERE student_id = {$student['id']}")->fetch_assoc()['count'];
$unread_count = $conn->query("SELECT COUNT(*) as count FROM student_notifications WHERE student_id = {$student['id']} AND is_read = 0")->fetch_assoc()['count'];
?>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2><i class="fas fa-bell text-purple"></i> Notifications</h2>
            <p class="text-muted mb-0">Stay updated with important messages</p>
        </div>
        <?php if ($unread_count > 0): ?>
        <a href="?mark_all_read=1" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-check-double"></i> Mark All as Read
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total</p>
                        <h4 class="mb-0"><?php echo $total_count; ?></h4>
                    </div>
                    <div class="card-icon icon-purple">
                        <i class="fas fa-bell"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Unread</p>
                        <h4 class="mb-0 text-danger"><?php echo $unread_count; ?></h4>
                    </div>
                    <div class="card-icon icon-danger">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Read</p>
                        <h4 class="mb-0 text-success"><?php echo $total_count - $unread_count; ?></h4>
                    </div>
                    <div class="card-icon icon-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Tabs -->
<div class="table-card">
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link <?php echo $filter === 'all' ? 'active' : ''; ?>" href="?filter=all">
                All Notifications (<?php echo $total_count; ?>)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $filter === 'unread' ? 'active' : ''; ?>" href="?filter=unread">
                Unread (<?php echo $unread_count; ?>)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $filter === 'read' ? 'active' : ''; ?>" href="?filter=read">
                Read (<?php echo $total_count - $unread_count; ?>)
            </a>
        </li>
    </ul>
    
    <!-- Notifications List -->
    <?php if ($notifications->num_rows > 0): ?>
        <?php while ($notif = $notifications->fetch_assoc()): 
            $type_icon = [
                'info' => 'info-circle',
                'warning' => 'exclamation-triangle',
                'success' => 'check-circle',
                'payment' => 'indian-rupee-sign'
            ];
            
            $type_color = [
                'info' => 'info',
                'warning' => 'warning',
                'success' => 'success',
                'payment' => 'primary'
            ];
        ?>
        <div class="alert alert-<?php echo $type_color[$notif['type']]; ?> <?php echo $notif['is_read'] ? 'opacity-75' : ''; ?> d-flex align-items-start">
            <i class="fas fa-<?php echo $type_icon[$notif['type']]; ?> me-3 fs-4"></i>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-1">
                            <?php echo htmlspecialchars($notif['title']); ?>
                            <?php if (!$notif['is_read']): ?>
                            <span class="badge bg-danger ms-2">New</span>
                            <?php endif; ?>
                        </h6>
                        <p class="mb-2"><?php echo nl2br(htmlspecialchars($notif['message'])); ?></p>
                        <small class="text-muted">
                            <i class="fas fa-clock"></i> <?php echo date('d M Y, h:i A', strtotime($notif['created_at'])); ?>
                        </small>
                    </div>
                    <div class="ms-3">
                        <div class="btn-group btn-group-sm">
                            <?php if (!$notif['is_read']): ?>
                            <a href="?read=1&id=<?php echo $notif['id']; ?>" class="btn btn-outline-success" title="Mark as Read">
                                <i class="fas fa-check"></i>
                            </a>
                            <?php endif; ?>
                            <a href="?delete=1&id=<?php echo $notif['id']; ?>" class="btn btn-outline-danger" title="Delete" onclick="return confirm('Delete this notification?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
    <div class="text-center py-5">
        <i class="fas fa-bell-slash fa-5x text-muted mb-3"></i>
        <h5>No Notifications</h5>
        <p class="text-muted">
            <?php 
            if ($filter === 'unread') {
                echo "You don't have any unread notifications.";
            } elseif ($filter === 'read') {
                echo "You don't have any read notifications.";
            } else {
                echo "You don't have any notifications yet.";
            }
            ?>
        </p>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>