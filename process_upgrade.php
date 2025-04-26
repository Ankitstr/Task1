<?php
require_once 'includes/init.php';
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    try {
        // Start transaction
        $db->beginTransaction();
        
        // Update user's membership status
        $query = "UPDATE users SET is_member = 1 WHERE id = :user_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->execute();
        
        // Update session
        $_SESSION['is_member'] = true;
        
        // Commit transaction
        $db->commit();
        
        // Redirect to profile with success message
        $_SESSION['success_message'] = "Congratulations! You are now a Premium Member.";
        header("Location: profile.php");
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $db->rollBack();
        $_SESSION['error_message'] = "An error occurred while processing your upgrade. Please try again.";
        header("Location: profile.php");
        exit();
    }
} else {
    // If not POST request, redirect to profile
    header("Location: profile.php");
    exit();
}
?> 