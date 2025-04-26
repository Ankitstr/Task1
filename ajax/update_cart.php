<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_POST['cart_id']) || !isset($_POST['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'Cart ID and quantity are required']);
    exit();
}

$cart_id = $_POST['cart_id'];
$quantity = (int)$_POST['quantity'];

if ($quantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Quantity must be at least 1']);
    exit();
}

$database = new Database();
$db = $database->getConnection();

try {
    // Verify cart item exists and belongs to user
    $query = "SELECT ci.*, c.user_id, c.session_id 
              FROM cart_items ci 
              JOIN cart c ON ci.cart_id = c.id 
              WHERE ci.id = :cart_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':cart_id', $cart_id);
    $stmt->execute();
    $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cart_item) {
        echo json_encode(['success' => false, 'message' => 'Cart item not found']);
        exit();
    }

    // Verify ownership
    if (isset($_SESSION['user_id'])) {
        if ($cart_item['user_id'] != $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
    } else {
        if ($cart_item['session_id'] != session_id()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
    }

    // Check product stock
    $query = "SELECT stock_quantity FROM products WHERE id = :product_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':product_id', $cart_item['product_id']);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($quantity > $product['stock_quantity']) {
        echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
        exit();
    }

    // Update quantity
    $query = "UPDATE cart_items SET quantity = :quantity WHERE id = :cart_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':quantity', $quantity);
    $stmt->bindParam(':cart_id', $cart_id);
    $stmt->execute();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error updating cart']);
}
?> 