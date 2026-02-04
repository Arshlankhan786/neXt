<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';
$student_id = (int)($_POST['student_id'] ?? 0);

if ($action === 'add_project') {
    $project_name = sanitize($_POST['project_name']);
    $description = sanitize($_POST['description'] ?? '');
    $start_date = $_POST['start_date'] ?? null;
    $end_date = $_POST['end_date'] ?? null;
    $status = $_POST['status'] ?? 'Not Started';
    
    $stmt = $conn->prepare("INSERT INTO student_projects (student_id, project_name, description, start_date, end_date, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $student_id, $project_name, $description, $start_date, $end_date, $status);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Project added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add project']);
    }
    $stmt->close();
}

if ($action === 'update_project') {
    $project_id = (int)$_POST['project_id'];
    $project_name = sanitize($_POST['project_name']);
    $description = sanitize($_POST['description'] ?? '');
    $start_date = $_POST['start_date'] ?? null;
    $end_date = $_POST['end_date'] ?? null;
    $status = $_POST['status'];
    $remarks = sanitize($_POST['remarks'] ?? '');
    
    $stmt = $conn->prepare("UPDATE student_projects SET project_name = ?, description = ?, start_date = ?, end_date = ?, status = ?, remarks = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $project_name, $description, $start_date, $end_date, $status, $remarks, $project_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    $stmt->close();
}

if ($action === 'delete_project') {
    $project_id = (int)$_POST['project_id'];
    
    if ($conn->query("DELETE FROM student_projects WHERE id = $project_id")) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}

if ($action === 'get_projects') {
    $projects = $conn->query("SELECT * FROM student_projects WHERE student_id = $student_id ORDER BY created_at DESC");
    $data = [];
    while ($row = $projects->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode(['success' => true, 'projects' => $data]);
}
?>