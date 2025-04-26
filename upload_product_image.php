<?php
require_once 'config/database.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug information
error_log("Session data: " . print_r($_SESSION, true));

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Please login to continue";
    error_log("User not logged in. Redirecting to login.");
    header("Location: login.php");
    exit();
}

// Check if user is admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    $_SESSION['error_message'] = "You do not have permission to access this page. Please contact an administrator.";
    error_log("User is not admin. User ID: " . $_SESSION['user_id']);
    
    // Verify admin status in database
    $database = new Database();
    $db = $database->getConnection();
    $query = "SELECT is_admin FROM users WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && $user['is_admin']) {
        error_log("User is admin in database but not in session. Updating session.");
        $_SESSION['is_admin'] = true;
    } else {
        error_log("User is not admin in database. Redirecting to index.");
        header("Location: index.php");
        exit();
    }
}

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get product details
$database = new Database();
$db = $database->getConnection();

$query = "SELECT id, name, image FROM products WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $product_id);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    $_SESSION['error_message'] = "Product not found";
    header("Location: products.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Product Image - E-Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Upload Product Image</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['error_message'])): ?>
                            <div class="alert alert-danger">
                                <?php 
                                echo $_SESSION['error_message'];
                                unset($_SESSION['error_message']);
                                ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['success_message'])): ?>
                            <div class="alert alert-success">
                                <?php 
                                echo $_SESSION['success_message'];
                                unset($_SESSION['success_message']);
                                ?>
                            </div>
                        <?php endif; ?>

                        <form action="process_product_image.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                            
                            <div class="mb-3">
                                <label for="product_name" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="product_name" value="<?php echo htmlspecialchars($product['name']); ?>" readonly>
                            </div>

                            <?php if ($product['image']): ?>
                            <div class="mb-3">
                                <label class="form-label">Current Image</label>
                                <div>
                                    <img src="uploads/products/<?php echo htmlspecialchars($product['image']); ?>" 
                                         alt="Current Product Image" 
                                         class="img-thumbnail" 
                                         style="max-width: 200px;">
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="image" class="form-label">New Product Image</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/jpeg,image/png,image/gif" required>
                                <div class="form-text">Allowed formats: JPG, PNG, GIF. Maximum size: 5MB</div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload"></i> Upload Image
                                </button>
                                <a href="edit_product.php?id=<?php echo $product_id; ?>" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Product
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html> 