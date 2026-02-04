<?php
/**
 * Database Configuration - OPTIMIZED FOR HOSTING
 */

define('DB_HOST', 'srv842.hstgr.io');
define('DB_USER', 'u946810828_Next_academy');
define('DB_PASS', 'NextAcademy2806');
define('DB_NAME', 'u946810828_Next');

// Create SINGLE persistent connection
$conn = null;


// Create database connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to UTF-8
    $conn->set_charset("utf8mb4");
    
    // Set timezone to Indian Standard Time (IST)
    $conn->query("SET time_zone = '+05:30'");
    
} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage());
}

// Set PHP timezone to IST
date_default_timezone_set('Asia/Kolkata');

/**
 * Sanitize input data
 */
function sanitize($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data);
}

/**
 * Execute prepared statement
 */
function executeQuery($query, $types = "", $params = []) {
    global $conn;
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return false;
    }
    
    if (!empty($types) && !empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    return $stmt;
}

/**
 * Get single result
 */
function getSingleResult($query, $types = "", $params = []) {
    $stmt = executeQuery($query, $types, $params);
    if (!$stmt) return null;
    
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
    
    return $data;
}

/**
 * Get multiple results
 */
function getResults($query, $types = "", $params = []) {
    $stmt = executeQuery($query, $types, $params);
    if (!$stmt) return [];
    
    $result = $stmt->get_result();
    $data = [];
    
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    $stmt->close();
    return $data;
}

/**
 * Get current IST date
 */
function getCurrentISTDate() {
    return date('Y-m-d');
}

/**
 * Get current IST time
 */
function getCurrentISTTime() {
    return date('H:i:s');
}

/**
 * Get current IST datetime
 */
function getCurrentISTDateTime() {
    return date('Y-m-d H:i:s');
}

/**
 * Format date in Indian format
 */
function formatIndianDate($date) {
    return date('d-m-Y', strtotime($date));
}

/**
 * Format time in Indian format (12-hour with AM/PM)
 */
function formatIndianTime($time) {
    return date('h:i A', strtotime($time));
}

/**
 * Format datetime in Indian format
 */
function formatIndianDateTime($datetime) {
    return date('d-m-Y h:i A', strtotime($datetime));
}
?>