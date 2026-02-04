<?php
/**
 * TASK MANAGER - Production Ready
 * Handles normal and scheduled (repeating) tasks with proper separation of concerns
 */

// Prevent any output before JSON
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 0); // Log errors, don't display
ini_set('log_errors', 1);

session_start();
require_once 'config/database.php';
require_once 'config/auth.php';

// Clear buffer and set JSON header
ob_end_clean();
header('Content-Type: application/json');

// Authentication check
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$admin_id = $_SESSION['admin_id'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// ===============================
// HELPER FUNCTIONS
// ===============================

/**
 * Get current IST DateTime
 */
function getCurrentDateTime() {
    return date('Y-m-d H:i:s');
}

/**
 * Get current IST Date
 */
function getCurrentDate() {
    return date('Y-m-d');
}

/**
 * Log task history
 */
function logTaskHistory($conn, $task_id, $admin_id, $action, $notes = null) {
    $notes_escaped = $notes ? "'" . $conn->real_escape_string($notes) . "'" : "NULL";
    $query = "INSERT INTO admin_task_history (task_id, admin_id, action, notes, created_at) 
              VALUES ($task_id, $admin_id, '$action', $notes_escaped, NOW())";
    return $conn->query($query);
}

/**
 * Create a new scheduled task instance
 * This generates a fresh task for the next occurrence
 */
function createNextScheduledTask($conn, $original_task, $admin_id) {
    // Validate repeat interval
    if (!$original_task['is_repeating'] || !$original_task['repeat_interval_hours'] || $original_task['repeat_interval_hours'] < 1) {
        return false;
    }
    
    // Check if max repeats reached
    $current_repeat = (int)$original_task['repeat_count'];
    $max_repeats = $original_task['max_repeats'] ? (int)$original_task['max_repeats'] : null;
    
    if ($max_repeats !== null && $current_repeat >= $max_repeats) {
        // Max repeats reached - do NOT create new task
        return false;
    }
    
    // Calculate next due date
    $interval_hours = (int)$original_task['repeat_interval_hours'];
    $next_due_date = date('Y-m-d', strtotime("+{$interval_hours} hours"));
    
    // Prepare data
    $title = $conn->real_escape_string($original_task['title']);
    $description = $conn->real_escape_string($original_task['description'] ?? '');
    $new_repeat_count = $current_repeat + 1;
    
    // Create NEW task (not update existing)
    $query = "INSERT INTO admin_tasks 
              (admin_id, title, description, is_repeating, repeat_interval_hours, max_repeats, 
               repeat_count, next_due_date, status, created_at) 
              VALUES 
              ($admin_id, '$title', '$description', 1, $interval_hours, ";
    
    $query .= ($max_repeats !== null) ? "$max_repeats, " : "NULL, ";
    $query .= "$new_repeat_count, '$next_due_date', 'Pending', NOW())";
    
    if ($conn->query($query)) {
        $new_task_id = $conn->insert_id;
        
        // Log creation of new scheduled instance
        $notes = "Auto-created scheduled task (occurrence #{$new_repeat_count}) from task #{$original_task['id']}";
        logTaskHistory($conn, $new_task_id, $admin_id, 'created', $notes);
        
        return $new_task_id;
    }
    
    return false;
}

// ===============================
// AUTO-PROCESS OVERDUE TASKS
// Runs on every request to mark overdue tasks
// ===============================
function processOverdueTasks($conn, $admin_id) {
    $today = getCurrentDate();
    
    // Find all overdue pending tasks
    $query = "SELECT * FROM admin_tasks 
              WHERE admin_id = $admin_id 
              AND status = 'Pending' 
              AND next_due_date IS NOT NULL 
              AND next_due_date < '$today'";
    
    $result = $conn->query($query);
    
    if (!$result) return;
    
    while ($task = $result->fetch_assoc()) {
        $task_id = $task['id'];
        
        // Mark current task as incomplete (NEVER modify past completed/incomplete tasks)
        $update_query = "UPDATE admin_tasks 
                        SET status = 'Incomplete' 
                        WHERE id = $task_id AND status = 'Pending'"; // Double-check still pending
        
        if ($conn->query($update_query)) {
            // Log incomplete status
            $notes = "Automatically marked incomplete - missed due date: {$task['next_due_date']}";
            logTaskHistory($conn, $task_id, $admin_id, 'updated', $notes);
            
            // If this is a scheduled task, create next occurrence
            if ($task['is_repeating']) {
                $new_task_id = createNextScheduledTask($conn, $task, $admin_id);
                
                if ($new_task_id) {
                    // Log connection between old and new task
                    $notes = "New scheduled task #{$new_task_id} created after marking #{$task_id} incomplete";
                    logTaskHistory($conn, $task_id, $admin_id, 'updated', $notes);
                }
            }
        }
    }
}

// Auto-process on every action
try {
    processOverdueTasks($conn, $admin_id);
} catch (Exception $e) {
    error_log("Error processing overdue tasks: " . $e->getMessage());
}

// ===============================
// ACTION: ADD TASK
// ===============================
if ($action === 'add') {
    try {
        $title = $conn->real_escape_string(trim($_POST['title']));
        $description = $conn->real_escape_string(trim($_POST['description'] ?? ''));
        $is_repeating = isset($_POST['is_repeating']) ? 1 : 0;
        $repeat_interval_hours = $is_repeating ? (int)$_POST['repeat_interval_hours'] : NULL;
        $max_repeats = ($is_repeating && !empty($_POST['max_repeats'])) ? (int)$_POST['max_repeats'] : NULL;
        
        // Calculate next due date
        if ($is_repeating && $repeat_interval_hours > 0) {
            $next_due_date = date('Y-m-d', strtotime("+{$repeat_interval_hours} hours"));
        } else {
            $next_due_date = getCurrentDate(); // Due today
        }
        
        // Build INSERT query
        $query = "INSERT INTO admin_tasks 
                  (admin_id, title, description, is_repeating, repeat_interval_hours, max_repeats, 
                   repeat_count, next_due_date, status, created_at) 
                  VALUES 
                  ($admin_id, '$title', '$description', $is_repeating, ";
        
        $query .= ($repeat_interval_hours !== NULL) ? "$repeat_interval_hours, " : "NULL, ";
        $query .= ($max_repeats !== NULL) ? "$max_repeats, " : "NULL, ";
        $query .= "0, '$next_due_date', 'Pending', NOW())";
        
        if ($conn->query($query)) {
            $task_id = $conn->insert_id;
            
            // Log creation
            $type = $is_repeating ? "scheduled (repeating)" : "normal";
            $notes = "Created $type task - Due: $next_due_date";
            logTaskHistory($conn, $task_id, $admin_id, 'created', $notes);
            
            echo json_encode(['success' => true, 'message' => 'Task created successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit();
}

// ===============================
// ACTION: MARK TASK AS COMPLETED
// ===============================
if ($action === 'complete') {
    try {
        $task_id = (int)$_POST['task_id'];
        
        // Get task details (only if still pending)
        $result = $conn->query("SELECT * FROM admin_tasks 
                               WHERE id = $task_id 
                               AND admin_id = $admin_id 
                               AND status = 'Pending'");
        
        if ($result->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Task not found or already processed']);
            exit();
        }
        
        $task = $result->fetch_assoc();
        $now = getCurrentDateTime();
        
        // Mark CURRENT task as completed (NEVER modify - this is now historical)
        $update_query = "UPDATE admin_tasks 
                        SET status = 'Completed', 
                            completed_at = '$now' 
                        WHERE id = $task_id AND status = 'Pending'"; // Safety check
        
        if ($conn->query($update_query)) {
            // Log completion
            $notes = "Task completed manually";
            logTaskHistory($conn, $task_id, $admin_id, 'completed', $notes);
            
            $message = 'Task completed!';
            
            // If this is a scheduled task, create next occurrence
            if ($task['is_repeating']) {
                $new_task_id = createNextScheduledTask($conn, $task, $admin_id);
                
                if ($new_task_id) {
                    $message = 'Task completed! Next occurrence scheduled.';
                    
                    // Log connection
                    $notes = "Completed - spawned new scheduled task #{$new_task_id}";
                    logTaskHistory($conn, $task_id, $admin_id, 'completed', $notes);
                } else {
                    // Max repeats reached or error
                    $current = $task['repeat_count'] + 1;
                    $max = $task['max_repeats'] ?? '∞';
                    $message = "Task completed! Series finished ($current/$max repeats).";
                }
            }
            
            echo json_encode(['success' => true, 'message' => $message]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to mark task as completed']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit();
}

// ===============================
// ACTION: DELETE TASK
// ===============================
if ($action === 'delete') {
    try {
        $task_id = (int)$_POST['task_id'];
        
        // Log deletion before deleting
        logTaskHistory($conn, $task_id, $admin_id, 'deleted', 'Task deleted by user');
        
        // Delete task
        $query = "DELETE FROM admin_tasks WHERE id = $task_id AND admin_id = $admin_id";
        
        if ($conn->query($query)) {
            echo json_encode(['success' => true, 'message' => 'Task deleted']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete task']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit();
}

// ===============================
// ACTION: GET ALL TASKS
// ===============================
if ($action === 'get_all') {
    try {
        // Active/Pending tasks
        $active_result = $conn->query("
            SELECT * FROM admin_tasks 
            WHERE admin_id = $admin_id AND status = 'Pending' 
            ORDER BY next_due_date ASC, created_at DESC
        ");
        $active = [];
        if ($active_result) {
            while ($row = $active_result->fetch_assoc()) {
                $active[] = $row;
            }
        }
        
        // Completed tasks
        $completed_result = $conn->query("
            SELECT * FROM admin_tasks 
            WHERE admin_id = $admin_id AND status = 'Completed' 
            ORDER BY completed_at DESC LIMIT 50
        ");
        $completed = [];
        if ($completed_result) {
            while ($row = $completed_result->fetch_assoc()) {
                $completed[] = $row;
            }
        }
        
        // Incomplete (missed) tasks
        $incomplete_result = $conn->query("
            SELECT * FROM admin_tasks 
            WHERE admin_id = $admin_id AND status = 'Incomplete' 
            ORDER BY next_due_date DESC LIMIT 50
        ");
        $incomplete = [];
        if ($incomplete_result) {
            while ($row = $incomplete_result->fetch_assoc()) {
                $incomplete[] = $row;
            }
        }
        
        echo json_encode([
            'success' => true,
            'active' => $active,
            'completed' => $completed,
            'incomplete' => $incomplete
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
?>