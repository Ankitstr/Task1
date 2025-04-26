<?php
require_once 'includes/init.php';
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Get order ID from URL
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($order_id <= 0) {
    header("Location: index.php");
    exit();
}

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Get order details
$query = "SELECT o.*, u.name as user_name, u.email as user_email 
          FROM orders o 
          JOIN users u ON o.user_id = u.id 
          WHERE o.id = :order_id AND o.user_id = :user_id";

$stmt = $db->prepare($query);
$stmt->bindParam(':order_id', $order_id);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();

$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header("Location: index.php");
    exit();
}

// Get order items
$query = "SELECT oi.*, p.name as product_name, p.image 
          FROM order_items oi 
          JOIN products p ON oi.product_id = p.id 
          WHERE oi.order_id = :order_id";

$stmt = $db->prepare($query);
$stmt->bindParam(':order_id', $order_id);
$stmt->execute();

$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare product images
foreach ($order_items as &$item) {
    if (empty($item['image'])) {
        $item['image'] = 'assets/images/placeholder.png';
    } else {
        $image_path = 'uploads/products/' . $item['image'];
        $item['image'] = file_exists($image_path) ? $image_path : 'assets/images/placeholder.png';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - E-Commerce Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">Order Confirmation</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success">
                            <h5 class="alert-heading">Thank you for your order!</h5>
                            <p>Your order has been placed successfully. Order ID: #<?php echo $order_id; ?></p>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Order Details</h5>
                                <p><strong>Order ID:</strong> #<?php echo $order_id; ?></p>
                                <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                                <p><strong>Status:</strong> <?php echo ucfirst($order['status']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <h5>Shipping Information</h5>
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($order['shipping_name']); ?></p>
                                <p><strong>Address:</strong> <?php echo htmlspecialchars($order['shipping_address']); ?></p>
                                <p><strong>City:</strong> <?php echo htmlspecialchars($order['shipping_city']); ?></p>
                                <p><strong>State:</strong> <?php echo htmlspecialchars($order['shipping_state']); ?></p>
                                <p><strong>ZIP:</strong> <?php echo htmlspecialchars($order['shipping_zip']); ?></p>
                                <p><strong>Country:</strong> <?php echo htmlspecialchars($order['shipping_country']); ?></p>
                            </div>
                        </div>

                        <h5>Order Items</h5>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order_items as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                                 class="me-2" style="width: 50px; height: 50px; object-fit: contain;">
                                            <?php echo htmlspecialchars($item['product_name']); ?>
                                        </div>
                                    </td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                                    <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3">Total</th>
                                    <th>$<?php echo number_format($order['total_amount'], 2); ?></th>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="text-center mt-4">
                            <a href="index.php" class="btn btn-primary">Continue Shopping</a>
                            <a href="orders.php" class="btn btn-outline-primary ms-2">View All Orders</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html> 