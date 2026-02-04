<?php
require_once 'config/database.php';
require_once 'config/auth.php';
requireLogin();

$current_page = 'task_manager';
$admin = getCurrentAdmin();

include 'includes/header.php';
?>

<style>
/* Pill/Capsule Button Style for Tasks */
.task-pill {
    display: inline-flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 20px;
    margin: 8px;
    border-radius: 50px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 500;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    transition: all 0.3s ease;
    cursor: pointer;
    border: none;
    min-width: 200px;
}

.task-pill:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.task-pill.completed {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    opacity: 0.8;
}

.task-pill.incomplete {
    background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%);
}

.task-pill.repeating {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.task-pill-title {
    flex-grow: 1;
    margin-right: 10px;
    cursor: pointer;
}

.task-pill-actions {
    display: flex;
    gap: 8px;
}

.task-pill-btn {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
}

.task-pill-btn:hover {
    background: rgba(255, 255, 255, 0.4);
    transform: scale(1.1);
}

.task-section {
    margin-bottom: 30px;
}

.task-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 15px 20px;
    background: white;
    border-radius: 12px;
    margin-bottom: 15px;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s;
}

.task-section-header:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.task-section-header h5 {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.task-badge {
    background: var(--purple-primary);
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
}

.task-container {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.collapse-icon {
    transition: transform 0.3s;
}

.collapsed .collapse-icon {
    transform: rotate(-90deg);
}

.task-details {
    font-size: 0.85rem;
    margin-top: 5px;
    opacity: 0.9;
}

/* Add Task Form Styles */
.add-task-form {
    background: white;
    padding: 25px;
    border-radius: 15px;
    margin-bottom: 30px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.form-check-input:checked {
    background-color: var(--purple-primary);
    border-color: var(--purple-primary);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 40px;
    color: #999;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 15px;
    opacity: 0.5;
}
</style>

<div class="page-header">
    <h2><i class="fas fa-tasks text-purple"></i> My Task Manager</h2>
    <p class="text-muted mb-0">Manage your personal tasks with auto-repeat functionality</p>
</div>

<!-- Add Task Form -->
<div class="add-task-form">
    <h5 class="text-purple mb-3"><i class="fas fa-plus-circle"></i> Add New Task</h5>
    <form id="addTaskForm">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Task Title *</label>
                <input type="text" class="form-control" name="title" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Description (Optional)</label>
                <input type="text" class="form-control" name="description">
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="is_repeating" name="is_repeating">
                    <label class="form-check-label" for="is_repeating">
                        <strong>Repeating Task</strong>
                    </label>
                </div>
            </div>
            <div class="col-md-4 mb-3" id="repeat_interval_section" style="display: none;">
                <label class="form-label">Repeat Every (Hours)</label>
                <input type="number" class="form-control" name="repeat_interval_hours" min="1" value="24">
            </div>
            <div class="col-md-4 mb-3" id="max_repeats_section" style="display: none;">
                <label class="form-label">Max Repeats (Optional)</label>
                <input type="number" class="form-control" name="max_repeats" min="1" placeholder="Unlimited">
            </div>
        </div>
        
        <button type="submit" class="btn btn-purple">
            <i class="fas fa-plus"></i> Add Task
        </button>
    </form>
</div>

<!-- Active Tasks Section -->
<div class="task-section">
    <div class="task-section-header" onclick="toggleSection('active')">
        <h5>
            <i class="fas fa-play-circle text-primary"></i>
            Active Tasks
            <span class="task-badge" id="active-count">0</span>
        </h5>
        <i class="fas fa-chevron-down collapse-icon"></i>
    </div>
    <div id="active-tasks" class="task-container"></div>
</div>

<!-- Completed Tasks Section -->
<div class="task-section">
    <div class="task-section-header collapsed" onclick="toggleSection('completed')">
        <h5>
            <i class="fas fa-check-circle text-success"></i>
            Completed Tasks
            <span class="task-badge" id="completed-count">0</span>
        </h5>
        <i class="fas fa-chevron-down collapse-icon"></i>
    </div>
    <div id="completed-tasks" class="task-container" style="display: none;"></div>
</div>

<!-- Incomplete (Missed) Tasks Section -->
<div class="task-section">
    <div class="task-section-header collapsed" onclick="toggleSection('incomplete')">
        <h5>
            <i class="fas fa-exclamation-triangle text-danger"></i>
            Missed Tasks
            <span class="task-badge" id="incomplete-count">0</span>
        </h5>
        <i class="fas fa-chevron-down collapse-icon"></i>
    </div>
    <div id="incomplete-tasks" class="task-container" style="display: none;"></div>
</div>

<script>
// Toggle repeating task fields
document.getElementById('is_repeating').addEventListener('change', function() {
    const display = this.checked ? 'block' : 'none';
    document.getElementById('repeat_interval_section').style.display = display;
    document.getElementById('max_repeats_section').style.display = display;
});

// Toggle section collapse
function toggleSection(section) {
    const container = document.getElementById(section + '-tasks');
    const header = container.previousElementSibling;
    
    if (container.style.display === 'none') {
        container.style.display = 'flex';
        header.classList.remove('collapsed');
    } else {
        container.style.display = 'none';
        header.classList.add('collapsed');
    }
}

// Load all tasks
function loadTasks() {
    fetch('task_manager_actions.php?action=get_all')
        .then(r => {
            if (!r.ok) throw new Error('Network error');
            return r.json();
        })
        .then(data => {
            if (data.success) {
                renderTasks('active', data.active);
                renderTasks('completed', data.completed);
                renderTasks('incomplete', data.incomplete);
                
                document.getElementById('active-count').textContent = data.active.length;
                document.getElementById('completed-count').textContent = data.completed.length;
                document.getElementById('incomplete-count').textContent = data.incomplete.length;
            } else {
                console.error('Error loading tasks:', data.message);
            }
        })
        .catch(err => {
            console.error('Fetch error:', err);
            showNotification('error', 'Failed to load tasks. Please refresh the page.');
        });
}

// Render tasks
function renderTasks(section, tasks) {
    const container = document.getElementById(section + '-tasks');
    
    if (tasks.length === 0) {
        container.innerHTML = '<div class="empty-state"><i class="fas fa-inbox"></i><p>No tasks here</p></div>';
        return;
    }
    
    container.innerHTML = tasks.map(task => {
        let pillClass = '';
        if (task.is_repeating == 1) {
            pillClass = 'repeating';
        } else if (task.status === 'Completed') {
            pillClass = 'completed';
        } else if (task.status === 'Incomplete') {
            pillClass = 'incomplete';
        }
        
        const dueDate = task.next_due_date ? `Due: ${task.next_due_date}` : '';
        const repeatInfo = task.is_repeating == 1 ? `Repeat: ${task.repeat_interval_hours}h (${task.repeat_count}/${task.max_repeats || '∞'})` : '';
        
        return `
            <div class="task-pill ${pillClass}">
                <div class="task-pill-title" onclick="${task.status === 'Pending' ? 'completeTask(' + task.id + ')' : 'void(0)'}">
                    <strong>${task.title}</strong>
                    ${task.description ? `<div class="task-details">${task.description}</div>` : ''}
                    ${dueDate ? `<div class="task-details"><i class="fas fa-clock"></i> ${dueDate}</div>` : ''}
                    ${repeatInfo ? `<div class="task-details"><i class="fas fa-sync"></i> ${repeatInfo}</div>` : ''}
                </div>
                <div class="task-pill-actions">
                    ${task.status === 'Pending' ? `<button style="opacity: 0;" onclick="event.stopPropagation(); completeTask(${task.id})" title="Mark Done"></button>` : ''}
                    <button class="task-pill-btn" onclick="event.stopPropagation(); deleteTask(${task.id})" title="Delete"><i class="fas fa-trash"></i></button>
                </div>
            </div>
        `;
    }).join('');
}

// Add task
document.getElementById('addTaskForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('action', 'add');
    
    fetch('task_manager_actions.php', {
        method: 'POST',
        body: formData
    })
    .then(r => {
        if (!r.ok) throw new Error('Network error');
        return r.json();
    })
    .then(data => {
        if (data.success) {
            this.reset();
            document.getElementById('repeat_interval_section').style.display = 'none';
            document.getElementById('max_repeats_section').style.display = 'none';
            loadTasks();
            showNotification('success', data.message);
        } else {
            showNotification('error', data.message);
        }
    })
    .catch(err => {
        console.error('Add task error:', err);
        showNotification('error', 'Failed to add task. Please try again.');
    });
});

// Complete task (clicking title or Done button)
function completeTask(taskId) {
    if (!confirm('Mark this task as completed?')) return;
    
    const formData = new FormData();
    formData.append('action', 'complete');
    formData.append('task_id', taskId);
    
    fetch('task_manager_actions.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            loadTasks();
            showNotification('success', data.message);
        } else {
            showNotification('error', data.message);
        }
    });
}

// Delete task
function deleteTask(taskId) {
    if (!confirm('Delete this task permanently?')) return;
    
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('task_id', taskId);
    
    fetch('task_manager_actions.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            loadTasks();
            showNotification('success', data.message);
        } else {
            showNotification('error', data.message);
        }
    });
}

// Show notification
function showNotification(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'check-circle' : 'exclamation-circle';
    
    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show`;
    alert.innerHTML = `
        <i class="fas fa-${icon}"></i> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.querySelector('.page-header').after(alert);
    
    setTimeout(() => {
        alert.remove();
    }, 3000);
}

// Auto-refresh every 30 seconds
setInterval(loadTasks, 30000);

// Initial load
loadTasks();
</script>

<?php include 'includes/footer.php'; ?>