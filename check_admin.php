<?php
require_once 'config/database.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You are not logged in. Please login first.<br>";
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Get user's admin status from database
$query = "SELECT id, name, email, is_admin FROM users WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

echo "User Information:<br>";
echo "ID: " . $user['id'] . "<br>";
echo "Name: " . $user['name'] . "<br>";
echo "Email: " . $user['email'] . "<br>";
echo "Admin Status (Database): " . ($user['is_admin'] ? 'Yes' : 'No') . "<br>";
echo "Admin Status (Session): " . (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] ? 'Yes' : 'No') . "<br>";

// If not admin, show how to become admin
if (!$user['is_admin']) {
    echo "<br>To become an admin, you need to:<br>";
    echo "1. Make sure the is_admin column exists in the users table<br>";
    echo "2. Set your is_admin value to 1 in the database<br>";
    echo "3. Log out and log back in<br>";
}
?> 