<?php
include 'includes/header.php';

// Get course topics
$topics = $conn->query("SELECT * FROM course_topics WHERE student_id = {$student['id']} ORDER BY created_at DESC");
$topics_completed = $conn->query("SELECT COUNT(*) as count FROM course_topics WHERE student_id = {$student['id']} AND status = 'Completed'")->fetch_assoc()['count'];
$topics_total = $topics->num_rows;
$progress_percentage = $topics_total > 0 ? ($topics_completed / $topics_total) * 100 : 0;
?>

<div class="page-header">
    <h2><i class="fas fa-chart-line text-purple"></i> Course Progress</h2>
    <p class="text-muted mb-0">Track your learning journey</p>
</div>

<!-- Progress Summary -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <h3 class="text-purple mb-0"><?php echo $topics_total; ?></h3>
                <small class="text-muted">Total Topics</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <h3 class="text-success mb-0"><?php echo $topics_completed; ?></h3>
                <small class="text-muted">Completed</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <h3 class="text-info mb-0"><?php echo number_format($progress_percentage, 1); ?>%</h3>
                <small class="text-muted">Progress</small>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar bg-info" style="width: <?php echo $progress_percentage; ?>%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Topics List -->
<div class="table-card">
    <h5 class="text-purple mb-3"><i class="fas fa-list-check"></i> Course Topics</h5>
    
    <?php if ($topics->num_rows > 0): ?>
    <div class="list-group">
        <?php while ($topic = $topics->fetch_assoc()): ?>
        <div class="list-group-item">
            <div class="d-flex align-items-start">
                <div class="me-3">
                    <?php if ($topic['status'] === 'Completed'): ?>
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    <?php else: ?>
                        <i class="far fa-circle fa-2x text-secondary"></i>
                    <?php endif; ?>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-1"><?php echo htmlspecialchars($topic['topic_name']); ?></h6>
                    <?php if ($topic['description']): ?>
                    <p class="mb-1 text-muted small"><?php echo htmlspecialchars($topic['description']); ?></p>
                    <?php endif; ?>
                    <?php if ($topic['completed_date']): ?>
                    <small class="text-success">
                        <i class="fas fa-calendar-check"></i> Completed on <?php echo date('d M Y', strtotime($topic['completed_date'])); ?>
                    </small>
                    <?php else: ?>
                    <small class="text-muted">
                        <i class="fas fa-clock"></i> Pending
                    </small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> No topics have been added to your course yet.
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>