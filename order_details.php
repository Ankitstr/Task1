<?php
session_start();
require_once 'config/database.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: orders.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Get order details
$query = "SELECT o.*, 
          a.street, a.city, a.state, a.zip_code, a.country
          FROM orders o
          LEFT JOIN addresses a ON o.address_id = a.id
          WHERE o.id = :order_id AND o.user_id = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':order_id', $_GET['id']);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header("Location: orders.php");
    exit();
}

// Get order items
$query = "SELECT oi.*, p.name, p.image
          FROM order_items oi
          JOIN products p ON oi.product_id = p.id
          WHERE oi.order_id = :order_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':order_id', $_GET['id']);
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - E-Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <a href="profile.php" class="text-decoration-none text-dark">
                                    <i class="fas fa-user"></i> Profile
                                </a>
                            </li>
                            <li class="list-group-item active">
                                <a href="orders.php" class="text-decoration-none text-dark">
                                    <i class="fas fa-shopping-bag"></i> Orders
                                </a>
                            </li>
                            <li class="list-group-item">
                                <a href="addresses.php" class="text-decoration-none text-dark">
                                    <i class="fas fa-map-marker-alt"></i> Addresses
                                </a>
                            </li>
                            <li class="list-group-item">
                                <a href="logout.php" class="text-decoration-none text-dark">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Order #<?php echo $order['id']; ?></h4>
                        <span class="badge bg-<?php 
                            echo $order['status'] == 'completed' ? 'success' : 
                                ($order['status'] == 'processing' ? 'warning' : 'info'); 
                        ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <!-- Order Summary -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Order Information</h5>
                                <p><strong>Order Date:</strong> <?php echo date('M d, Y', strtotime($order['created_at'])); ?></p>
                                <p><strong>Payment Method:</strong> <?php echo ucfirst($order['payment_method']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <h5>Shipping Address</h5>
                                <p>
                                    <?php echo htmlspecialchars($order['street']); ?><br>
                                    <?php echo htmlspecialchars($order['city']); ?>, 
                                    <?php echo htmlspecialchars($order['state']); ?> 
                                    <?php echo htmlspecialchars($order['zip_code']); ?><br>
                                    <?php echo htmlspecialchars($order['country']); ?>
                                </p>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <h5>Order Items</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo htmlspecialchars($item['image'] ?? 'assets/images/product-placeholder.jpg'); ?>" 
                                                     class="me-3" width="50" height="50" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                                <?php echo htmlspecialchars($item['name']); ?>
                                            </div>
                                        </td>
                                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                        <td>$<?php echo number_format($order['total'], 2); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Shipping:</strong></td>
                                        <td>$<?php echo number_format($order['shipping'], 2); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                        <td>$<?php echo number_format($order['total'] + $order['shipping'], 2); ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
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