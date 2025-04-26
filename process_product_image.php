<?php
require_once 'config/database.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Please login to continue";
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    $_SESSION['error_message'] = "You do not have permission to access this page";
    header("Location: index.php");
    exit();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    try {
        // Validate product ID
        if (!isset($_POST['product_id']) || !is_numeric($_POST['product_id'])) {
            throw new Exception("Invalid product ID");
        }
        
        $product_id = $_POST['product_id'];
        
        // Check if product exists
        $query = "SELECT id, image FROM products WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $product_id);
        $stmt->execute();
        
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$product) {
            throw new Exception("Product not found");
        }
        
        // Check if file was uploaded
        if (!isset($_FILES['image'])) {
            throw new Exception("No file was uploaded");
        }
        
        $file = $_FILES['image'];
        error_log("File upload error code: " . $file['error']);
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $upload_errors = [
                UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
                UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form',
                UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
            ];
            throw new Exception($upload_errors[$file['error']] ?? 'Unknown upload error');
        }
        
        // Validate file type
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowed_types)) {
            throw new Exception("Invalid file type. Only JPG, PNG, and GIF are allowed. Uploaded type: " . $file['type']);
        }
        
        // Validate file size (max 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            throw new Exception("File size too large. Maximum size is 5MB. Uploaded size: " . $file['size']);
        }
        
        // Create uploads directory if it doesn't exist
        $upload_dir = 'uploads/products/';
        if (!file_exists($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true)) {
                throw new Exception("Failed to create upload directory: " . $upload_dir);
            }
        }
        
        // Check directory permissions
        if (!is_writable($upload_dir)) {
            throw new Exception("Upload directory is not writable: " . $upload_dir);
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $target_path = $upload_dir . $filename;
        
        error_log("Attempting to move file to: " . $target_path);
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $target_path)) {
            $error = error_get_last();
            throw new Exception("Failed to move uploaded file: " . ($error['message'] ?? 'Unknown error'));
        }
        
        // Delete old image if exists
        if ($product['image'] && file_exists($upload_dir . $product['image'])) {
            unlink($upload_dir . $product['image']);
        }
        
        // Update product image in database
        $query = "UPDATE products SET image = :image WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':image', $filename);
        $stmt->bindParam(':id', $product_id);
        
        if (!$stmt->execute()) {
            $error = $stmt->errorInfo();
            throw new Exception("Database update failed: " . implode(", ", $error));
        }
        
        // Redirect with success message
        $_SESSION['success_message'] = "Product image uploaded successfully";
        header("Location: upload_product_image.php?id=" . $product_id);
        exit();
        
    } catch (Exception $e) {
        error_log("Upload Error: " . $e->getMessage());
        error_log("Stack Trace: " . $e->getTraceAsString());
        
        // Redirect with error message
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: upload_product_image.php?id=" . $product_id);
        exit();
    }
} else {
    // If not POST request, redirect to products page
    header("Location: products.php");
    exit();
}
?> 