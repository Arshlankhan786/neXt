<?php
/**
 * Student Authentication & Session Management
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if student is logged in
 */
function isStudentLoggedIn() {
    return isset($_SESSION['student_id']) && isset($_SESSION['student_code']);
}

/**
 * Require student login - redirect to login if not logged in
 */
function requireStudentLogin() {
    if (!isStudentLoggedIn()) {
        header('Location: ../auth/login.php');
        exit();
    }
}

/**
 * Logout student
 */
function logoutStudent() {
    session_unset();
    session_destroy();
    header('Location: .');
    exit();
}

/**
 * Get current student info
 */
function getCurrentStudent() {
    if (!isStudentLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['student_id'],
        'code' => $_SESSION['student_code'],
        'name' => $_SESSION['student_name'],
        'email' => $_SESSION['student_email'] ?? ''
    ];
}
?>