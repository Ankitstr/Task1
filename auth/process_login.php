<?php
require_once '../includes/init.php';
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$redirect_url = $_POST['redirect_url'] ?? 'index.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM users WHERE email = ?";
$stmt = $db->prepare($query);
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    // Set session variables
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['is_admin'] = $user['is_admin'] ?? false;
    
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'redirect_url' => $redirect_url
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid email or password'
    ]);
} 