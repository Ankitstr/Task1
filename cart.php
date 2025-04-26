<?php
require_once 'includes/init.php';
require_once 'config/database.php';

// Check if user is logged in


$database = new Database();
$db = $database->getConnection();

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update':
                $product_id = $_POST['product_id'];
                $quantity = (int)$_POST['quantity'];
                
                if (isset($_SESSION['cart'][$product_id])) {
                    if ($quantity <= 0) {
                        unset($_SESSION['cart'][$product_id]);
                    } else {
                        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
                    }
                }
                break;
                
            case 'remove':
                $product_id = $_POST['product_id'];
                if (isset($_SESSION['cart'][$product_id])) {
                    unset($_SESSION['cart'][$product_id]);
                }
                break;
        }
        
        // Update cart count
        $total_items = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total_items += $item['quantity'];
        }
        $_SESSION['cart_count'] = $total_items;
    }
}

// Get cart items with product details
$cart_items = array();
$total = 0;

if (!empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($product_ids) - 1) . '?';
    
    $query = "SELECT id, name, price, image 
              FROM products 
              WHERE id IN ($placeholders)";
    $stmt = $db->prepare($query);
    $stmt->execute($product_ids);
    
    while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Prepare image path
        if (empty($product['image'])) {
            $image_path = 'assets/images/placeholder.png';
        } else {
            $image_path = 'uploads/products/' . $product['image'];
            if (!file_exists($image_path)) {
                $image_path = 'assets/images/placeholder.png';
            }
        }
        
        $cart_items[] = array(
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $image_path,
            'quantity' => $_SESSION['cart'][$product['id']]['quantity']
        );
        $total += $product['price'] * $_SESSION['cart'][$product['id']]['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - E-Commerce Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-4">
        <h2 class="mb-4">Shopping Cart</h2>
        
        <?php if (empty($cart_items)): ?>
            <div class="alert alert-info">
                Your cart is empty. <a href="products.php">Continue shopping</a>
            </div>
        <?php else: ?>
            <div class="row">
                <!-- Cart Items -->
                <div class="col-md-8">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="card mb-3">
                            <div class="row g-0">
                                <div class="col-md-2">
                                    <div class="product-image-container" style="width: 100px; height: 100px; overflow: hidden; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa;">
                                        <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                             class="img-fluid" 
                                             alt="<?php echo htmlspecialchars($item['name']); ?>"
                                             style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                                        <p class="card-text">
                                            <strong>$<?php echo number_format($item['price'], 2); ?></strong>
                                        </p>
                                        <form method="POST" class="d-flex align-items-center">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                            <input type="number" name="quantity" class="form-control me-2" 
                                                   value="<?php echo $item['quantity']; ?>" min="1" style="width: 80px;">
                                            <button type="submit" class="btn btn-outline-primary">Update</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="card-body">
                                        <form method="POST">
                                            <input type="hidden" name="action" value="remove">
                                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Order Summary -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span>$<?php echo number_format($total, 2); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping:</span>
                                <span>Free</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total:</strong>
                                <strong>$<?php echo number_format($total, 2); ?></strong>
                            </div>
                            <a href="checkout.php" class="btn btn-primary w-100">Proceed to Checkout</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html> 