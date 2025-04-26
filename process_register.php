<?php
require_once 'includes/init.php';
require_once 'config/database.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to send JSON response
function sendJsonResponse($success, $message) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate input
    if (empty($name)) {
        sendJsonResponse(false, "Name is required");
    }
    if (empty($email)) {
        sendJsonResponse(false, "Email is required");
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendJsonResponse(false, "Invalid email format");
    }
    if (empty($password)) {
        sendJsonResponse(false, "Password is required");
    } elseif (strlen($password) < 6) {
        sendJsonResponse(false, "Password must be at least 6 characters long");
    }
    if ($password !== $confirm_password) {
        sendJsonResponse(false, "Passwords do not match");
    }
    
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        // Check if email already exists
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            sendJsonResponse(false, "Email already exists");
        }
        
        // Create new user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $db->prepare("INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())");
        if ($stmt->execute([$name, $email, $hashed_password])) {
            // Set session variables
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $db->lastInsertId();
            $_SESSION['user_name'] = $name;
            
            sendJsonResponse(true, "Registration successful");
        } else {
            sendJsonResponse(false, "Registration failed. Please try again.");
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        sendJsonResponse(false, "Database error: " . $e->getMessage());
    }
} else {
    sendJsonResponse(false, "Invalid request method");
}
?> 