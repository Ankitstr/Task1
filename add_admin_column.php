<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Add is_admin column if it doesn't exist
    $query = "ALTER TABLE users ADD COLUMN IF NOT EXISTS is_admin TINYINT(1) DEFAULT 0";
    $db->exec($query);
    
    echo "Column 'is_admin' added successfully to users table!<br>";
    
    // Verify the column exists
    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'is_admin'");
    if ($stmt->rowCount() > 0) {
        echo "Column 'is_admin' verified successfully!<br>";
    } else {
        echo "Error: Column 'is_admin' not found!<br>";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}
?> 