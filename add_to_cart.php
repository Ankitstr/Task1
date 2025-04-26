<?php
require_once 'includes/init.php';
require_once 'config/database.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set header to JSON
header('Content-Type: application/json');

try {
    // Log the incoming request
    error_log('Add to Cart Request: ' . print_r($_POST, true));
    error_log('Session Data: ' . print_r($_SESSION, true));

    // Check if it's a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        error_log('Invalid request method: ' . $_SERVER['REQUEST_METHOD']);
        throw new Exception('Invalid request method');
    }

    // Get and validate product ID
    if (!isset($_POST['product_id'])) {
        error_log('Product ID not set in POST data');
        throw new Exception('Product ID is required');
    }

    $product_id = (int)$_POST['product_id'];
    if ($product_id <= 0) {
        error_log('Invalid product ID: ' . $product_id);
        throw new Exception('Invalid product ID');
    }

    // Get and validate quantity
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    if ($quantity < 1) {
        error_log('Invalid quantity: ' . $quantity);
        throw new Exception('Invalid quantity');
    }

    // Connect to database
    $database = new Database();
    $db = $database->getConnection();
    error_log('Database connection established');

    // Check if product exists
    $query = "SELECT id, name, price, image FROM products WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $product_id);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        error_log('Product not found with ID: ' . $product_id);
        throw new Exception('Product not found');
    }

    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    error_log('Product found: ' . print_r($product, true));

    // Initialize cart if not exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
        error_log('Cart initialized');
    }

    // Add or update product in cart
    if (isset($_SESSION['cart'][$product_id])) {
        // Update quantity if product already in cart
        $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        error_log('Updated existing cart item');
    } else {
        // Add new product to cart
        $_SESSION['cart'][$product_id] = array(
            'id' => $product_id,
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $product['image'],
            'quantity' => $quantity
        );
        error_log('Added new item to cart');
    }

    // Calculate total items in cart
    $total_items = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_items += $item['quantity'];
    }
    $_SESSION['cart_count'] = $total_items;
    error_log('Total items in cart: ' . $total_items);

    // Return success response
    $response = [
        'success' => true,
        'message' => 'Product added to cart successfully',
        'cart_count' => $total_items,
        'cart' => $_SESSION['cart']
    ];
    error_log('Sending response: ' . print_r($response, true));
    echo json_encode($response);

} catch (Exception $e) {
    error_log('Error in add_to_cart.php: ' . $e->getMessage());
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 