<?php
session_start();
require_once 'config/database.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Get user information
$query = "SELECT * FROM users WHERE id = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get user's orders with product images
$query = "SELECT o.*, oi.product_id, p.image, p.name as product_name 
          FROM orders o 
          LEFT JOIN order_items oi ON o.id = oi.order_id 
          LEFT JOIN products p ON oi.product_id = p.id 
          WHERE o.user_id = :user_id 
          ORDER BY o.created_at DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - E-Shop</title>
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
                        <div class="text-center mb-3">
                            <i class="fas fa-user-circle fa-5x text-primary"></i>
                            <h5 class="mt-2"><?php echo htmlspecialchars($user['name']); ?></h5>
                            <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item active">
                                <i class="fas fa-user"></i> Profile
                            </li>
                            <li class="list-group-item">
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
                <div class="container py-5">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5>Account Information</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                                    <p><strong>Member Status:</strong> 
                                        <?php if ($user['is_member']): ?>
                                            <span class="badge bg-success">Premium Member</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Regular Member</span>
                                        <?php endif; ?>
                                    </p>
                                    <?php if (!$user['is_member']): ?>
                                        <div class="mt-3">
                                            <h6>Become a Premium Member</h6>
                                            <p>Enjoy exclusive benefits:</p>
                                            <ul>
                                                <li>10% discount on all purchases</li>
                                                <li>Free shipping on all orders</li>
                                                <li>Early access to new products</li>
                                                <li>Priority customer support</li>
                                            </ul>
                                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#upgradeModal">
                                                Upgrade to Premium
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Account Information</h4>
                                </div>
                                <div class="card-body">
                                    <form action="update_profile.php" method="POST">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Name</label>
                                                <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Email</label>
                                                <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Phone</label>
                                                <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Update Profile</button>
                                    </form>
                                </div>
                            </div>

                            <!-- Recent Orders -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h4>Recent Orders</h4>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($orders)): ?>
                                        <p class="text-muted">No orders found.</p>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Order ID</th>
                                                        <th>Product</th>
                                                        <th>Date</th>
                                                        <th>Total</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($orders as $order): ?>
                                                    <tr>
                                                        <td>#<?php echo $order['id']; ?></td>
                                                        <td>
                                                            <?php if (!empty($order['image'])): ?>
                                                                <img src="uploads/products/<?php echo htmlspecialchars($order['image']); ?>" 
                                                                     alt="<?php echo htmlspecialchars($order['product_name']); ?>"
                                                                     class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                                            <?php endif; ?>
                                                            <?php echo htmlspecialchars($order['product_name']); ?>
                                                        </td>
                                                        <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                                        <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                                        <td>
                                                            <span class="badge bg-<?php 
                                                                echo $order['status'] == 'completed' ? 'success' : 
                                                                    ($order['status'] == 'processing' ? 'warning' : 'info'); 
                                                            ?>">
                                                                <?php echo ucfirst($order['status']); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">
                                                                View Details
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upgrade Modal -->
                <div class="modal fade" id="upgradeModal" tabindex="-1" aria-labelledby="upgradeModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="upgradeModalLabel">Upgrade to Premium Membership</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-info">
                                    <h6>Premium Membership Benefits:</h6>
                                    <ul>
                                        <li>10% discount on all purchases</li>
                                        <li>Free shipping on all orders</li>
                                        <li>Early access to new products</li>
                                        <li>Priority customer support</li>
                                    </ul>
                                </div>
                                <form id="upgradeForm" action="process_upgrade.php" method="POST">
                                    <div class="mb-3">
                                        <label class="form-label">Select Payment Method</label>
                                        <select class="form-select" name="payment_method" required>
                                            <option value="">Choose payment method</option>
                                            <option value="credit_card">Credit Card</option>
                                            <option value="paypal">PayPal</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="termsCheck" required>
                                            <label class="form-check-label" for="termsCheck">
                                                I agree to the <a href="terms.php">Terms and Conditions</a>
                                            </label>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" form="upgradeForm" class="btn btn-primary">Upgrade Now</button>
                            </div>
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