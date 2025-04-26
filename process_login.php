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
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Validate input
    if (empty($email)) {
        sendJsonResponse(false, "Email is required");
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendJsonResponse(false, "Invalid email format");
    }
    if (empty($password)) {
        sendJsonResponse(false, "Password is required");
    }
    
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        // Get user by email including admin status
        $stmt = $db->prepare("SELECT id, name, password, is_admin FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() === 0) {
            sendJsonResponse(false, "Invalid email or password");
        }
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verify password
        if (!password_verify($password, $user['password'])) {
            sendJsonResponse(false, "Invalid email or password");
        }
        
        // Set session variables
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['is_admin'] = (bool)$user['is_admin']; // Set admin status
        
        error_log("User logged in. ID: " . $user['id'] . ", Admin: " . ($user['is_admin'] ? 'Yes' : 'No'));
        
        // Set remember me cookie if requested
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $expires = time() + (30 * 24 * 60 * 60); // 30 days
            
            // Store token in database
            $stmt = $db->prepare("UPDATE users SET remember_token = ?, token_expires = ? WHERE id = ?");
            $stmt->execute([$token, date('Y-m-d H:i:s', $expires), $user['id']]);
            
            // Set cookie
            setcookie('remember_token', $token, $expires, '/', '', true, true);
        }
        
        sendJsonResponse(true, "Login successful");
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        sendJsonResponse(false, "Database error: " . $e->getMessage());
    }
} else {
    sendJsonResponse(false, "Invalid request method");
}
?> 