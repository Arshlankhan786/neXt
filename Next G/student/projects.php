<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'includes/header.php';

// Handle project submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $link = trim($_POST['link']);
        
        if (!empty($title) && !empty($link)) {
       $today = date('Y-m-d');

$stmt = $conn->prepare("
    INSERT INTO student_projects 
    (
        student_id,
        project_name,
        description,
        project_link,
        start_date,
        end_date,
        status
    ) 
    VALUES (?, ?, ?, ?, ?, ?, 'Completed')
");

$stmt->bind_param(
    "isssss",
    $student['id'],
    $title,
    $description,
    $link,
    $today,
    $today
);

            
            if ($stmt->execute()) {
                $stmt->close();
                header('Location: projects.php?success=1');
                exit();
            }
            $stmt->close();
        }
    } elseif ($_POST['action'] === 'edit') {
        $project_id = (int)$_POST['project_id'];
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $link = trim($_POST['link']);
        
        if (!empty($title) && !empty($link)) {
            $stmt = $conn->prepare("UPDATE student_projects SET project_name = ?, description = ?, project_link = ? WHERE id = ? AND student_id = ?");
            $stmt->bind_param("sssii", $title, $description, $link, $project_id, $student['id']);
            
            if ($stmt->execute()) {
                $stmt->close();
                header('Location: projects.php?updated=1');
                exit();
            }
            $stmt->close();
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $project_id = (int)$_GET['delete'];
    $conn->query("DELETE FROM student_projects WHERE id = $project_id AND student_id = {$student['id']}");
    header('Location: projects.php?deleted=1');
    exit();
}

// Get student projects
$projects = $conn->query("SELECT * FROM student_projects WHERE student_id = {$student['id']} ORDER BY created_at DESC");
$projects_total = $projects ? $projects->num_rows : 0;
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2><i class="fas fa-project-diagram text-purple"></i> My Projects</h2>
        <p class="text-muted mb-0">Showcase your work and achievements</p>
    </div>
    <button class="btn btn-purple" data-bs-toggle="modal" data-bs-target="#addProjectModal">
        <i class="fas fa-plus"></i> Add Project
    </button>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> Project added successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> Project updated successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> Project deleted successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Project Statistics -->
<div class="row g-3 mb-4">
    <div class="col-md-12">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <h3 class="text-purple mb-0"><?php echo $projects_total; ?></h3>
                <small class="text-muted">Total Projects</small>
            </div>
        </div>
    </div>
</div>

<!-- Projects List -->
<div class="row g-4">
    <?php if ($projects && $projects->num_rows > 0): ?>
        <?php while ($project = $projects->fetch_assoc()): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 project-card">
                <div class="project-header">
                    <div class="project-icon">
                        <i class="fas fa-folder"></i>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="mb-2">
                        <h5 class="card-title mb-1">
                            <a href="<?php echo htmlspecialchars($project['project_link']); ?>" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               class="text-decoration-none text-purple">
                                <?php echo htmlspecialchars($project['project_name']); ?>
                                <i class="fas fa-external-link-alt fa-xs"></i>
                            </a>
                        </h5>
                    </div>
                    
                    <?php if (!empty($project['description'])): ?>
                    <p class="card-text text-muted small mt-2"><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
                    <?php endif; ?>
                    
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-calendar"></i> 
                            <?php echo date('d M Y', strtotime($project['created_at'])); ?>
                        </small>
                    </div>
                </div>
                
                <div class="card-footer bg-white border-top">
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-primary flex-fill" onclick='editProject(<?php echo json_encode($project); ?>)'>
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <a href="?delete=<?php echo $project['id']; ?>" 
                           class="btn btn-sm btn-danger flex-fill" 
                           onclick="return confirm('Delete this project?')">
                            <i class="fas fa-trash"></i> Delete
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-3x mb-3"></i>
                <h5>No Projects Yet</h5>
                <p class="mb-3">Start showcasing your work by adding your first project!</p>
                <button class="btn btn-purple" data-bs-toggle="modal" data-bs-target="#addProjectModal">
                    <i class="fas fa-plus"></i> Add Your First Project
                </button>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Add Project Modal -->
<div class="modal fade" id="addProjectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus"></i> Add New Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="mb-3">
                        <label class="form-label">Project Title *</label>
                        <input type="text" class="form-control" name="title" required placeholder="E.g., E-commerce Website">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Project Link *</label>
                        <input type="url" class="form-control" name="link" required placeholder="https://example.com">
                        <small class="text-muted">Clicking the title will open this link in a new tab</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="4" placeholder="Describe your project..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-purple">
                        <i class="fas fa-save"></i> Add Project
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Project Modal -->
<div class="modal fade" id="editProjectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="project_id" id="edit_project_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Project Title *</label>
                        <input type="text" class="form-control" name="title" id="edit_title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Project Link *</label>
                        <input type="url" class="form-control" name="link" id="edit_link" required>
                        <small class="text-muted">Clicking the title will open this link in a new tab</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-purple">
                        <i class="fas fa-save"></i> Update Project
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.project-card {
    transition: transform 0.3s, box-shadow 0.3s;
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
}

.project-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(124, 58, 237, 0.2);
}

.project-header {
    background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
    padding: 30px;
    text-align: center;
}

.project-icon {
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
}

.card-title a {
    transition: color 0.3s;
}

.card-title a:hover {
    color: #5b21b6 !important;
}
</style>

<script>
function editProject(project) {
    document.getElementById('edit_project_id').value = project.id;
    document.getElementById('edit_title').value = project.project_name;
    document.getElementById('edit_description').value = project.description || '';
    document.getElementById('edit_link').value = project.project_link || '';
    
    const modal = new bootstrap.Modal(document.getElementById('editProjectModal'));
    modal.show();
}
</script>

<?php include 'includes/footer.php'; ?>