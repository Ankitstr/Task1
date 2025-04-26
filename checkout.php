<?php
require_once 'includes/init.php';
require_once 'config/database.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Get cart items with product details
$cart_items = [];
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
        $cart_items[] = array(
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $product['image'],
            'quantity' => $_SESSION['cart'][$product['id']]['quantity']
        );
        $total += $product['price'] * $_SESSION['cart'][$product['id']]['quantity'];
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        $required_fields = [
            'shipping_name' => 'Full Name',
            'shipping_address' => 'Address',
            'shipping_city' => 'City',
            'shipping_state' => 'State',
            'shipping_zip' => 'ZIP Code',
            'shipping_country' => 'Country',
            'payment_method' => 'Payment Method'
        ];

        $errors = [];
        foreach ($required_fields as $field => $label) {
            if (empty($_POST[$field])) {
                $errors[] = "$label is required";
            }
        }

        if (!empty($errors)) {
            throw new Exception(implode("<br>", $errors));
        }

        // Start transaction
        $db->beginTransaction();

        // Insert order
        $query = "INSERT INTO orders (user_id, total_amount, shipping_name, shipping_address, 
                  shipping_city, shipping_state, shipping_zip, shipping_country, payment_method, status) 
                  VALUES (:user_id, :total, :name, :address, :city, :state, :zip, :country, :payment, 'pending')";
        
        $stmt = $db->prepare($query);
        
        // Log the values being bound
        error_log("Order values: " . print_r([
            'user_id' => $_SESSION['user_id'],
            'total' => $total,
            'name' => $_POST['shipping_name'],
            'address' => $_POST['shipping_address'],
            'city' => $_POST['shipping_city'],
            'state' => $_POST['shipping_state'],
            'zip' => $_POST['shipping_zip'],
            'country' => $_POST['shipping_country'],
            'payment' => $_POST['payment_method']
        ], true));

        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->bindParam(':total', $total);
        $stmt->bindParam(':name', $_POST['shipping_name']);
        $stmt->bindParam(':address', $_POST['shipping_address']);
        $stmt->bindParam(':city', $_POST['shipping_city']);
        $stmt->bindParam(':state', $_POST['shipping_state']);
        $stmt->bindParam(':zip', $_POST['shipping_zip']);
        $stmt->bindParam(':country', $_POST['shipping_country']);
        $stmt->bindParam(':payment', $_POST['payment_method']);

        if (!$stmt->execute()) {
            $errorInfo = $stmt->errorInfo();
            error_log("Order insertion failed: " . implode(", ", $errorInfo));
            throw new Exception("Failed to create order: " . implode(", ", $errorInfo));
        }

        $order_id = $db->lastInsertId();
        error_log("Order created with ID: " . $order_id);

        // Insert order items
        $query = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                  VALUES (:order_id, :product_id, :quantity, :price)";
        
        $stmt = $db->prepare($query);

        foreach ($cart_items as $item) {
            $stmt->bindParam(':order_id', $order_id);
            $stmt->bindParam(':product_id', $item['id']);
            $stmt->bindParam(':quantity', $item['quantity']);
            $stmt->bindParam(':price', $item['price']);

            if (!$stmt->execute()) {
                $errorInfo = $stmt->errorInfo();
                error_log("Order item insertion failed: " . implode(", ", $errorInfo));
                throw new Exception("Failed to add order item: " . implode(", ", $errorInfo));
            }
        }

        // Commit transaction
        $db->commit();

        // Clear cart
        $_SESSION['cart'] = [];
        $_SESSION['cart_count'] = 0;

        // Redirect to success page
        header("Location: order_confirmation.php?id=" . $order_id);
        exit();

    } catch (Exception $e) {
        // Rollback transaction on error
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        
        error_log("Checkout Error: " . $e->getMessage());
        error_log("POST Data: " . print_r($_POST, true));
        error_log("Cart Data: " . print_r($_SESSION['cart'], true));
        
        $errors[] = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - E-Commerce Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container py-5">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Shipping Information</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="shipping_name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="shipping_name" name="shipping_name" 
                                           value="<?php echo isset($_POST['shipping_name']) ? htmlspecialchars($_POST['shipping_name']) : ''; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="shipping_address" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="shipping_address" name="shipping_address" 
                                           value="<?php echo isset($_POST['shipping_address']) ? htmlspecialchars($_POST['shipping_address']) : ''; ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="shipping_city" class="form-label">City</label>
                                    <input type="text" class="form-control" id="shipping_city" name="shipping_city" 
                                           value="<?php echo isset($_POST['shipping_city']) ? htmlspecialchars($_POST['shipping_city']) : ''; ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="shipping_state" class="form-label">State</label>
                                    <input type="text" class="form-control" id="shipping_state" name="shipping_state" 
                                           value="<?php echo isset($_POST['shipping_state']) ? htmlspecialchars($_POST['shipping_state']) : ''; ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="shipping_zip" class="form-label">ZIP Code</label>
                                    <input type="text" class="form-control" id="shipping_zip" name="shipping_zip" 
                                           value="<?php echo isset($_POST['shipping_zip']) ? htmlspecialchars($_POST['shipping_zip']) : ''; ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="shipping_country" class="form-label">Country</label>
                                <input type="text" class="form-control" id="shipping_country" name="shipping_country" 
                                       value="<?php echo isset($_POST['shipping_country']) ? htmlspecialchars($_POST['shipping_country']) : ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select class="form-select" id="payment_method" name="payment_method" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="credit_card" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'credit_card') ? 'selected' : ''; ?>>Credit Card</option>
                                    <option value="paypal" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'paypal') ? 'selected' : ''; ?>>PayPal</option>
                                    <option value="bank_transfer" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'bank_transfer') ? 'selected' : ''; ?>>Bank Transfer</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Place Order</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Order Summary</h4>
                    </div>
                    <div class="card-body">
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
                                <?php foreach ($cart_items as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                                    <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3">Total</th>
                                    <th>$<?php echo number_format($total, 2); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html> 