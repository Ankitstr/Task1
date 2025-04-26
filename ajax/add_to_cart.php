<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_POST['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'Product ID is required']);
    exit();
}

$product_id = $_POST['product_id'];
$database = new Database();
$db = $database->getConnection();

try {
    // Get product details
    $query = "SELECT * FROM products WHERE id = :product_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':product_id', $product_id);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit();
    }

    // Get or create cart
    if (isset($_SESSION['user_id'])) {
        $query = "SELECT id FROM cart WHERE user_id = :user_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->execute();
        $cart = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cart) {
            $query = "INSERT INTO cart (user_id) VALUES (:user_id)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $_SESSION['user_id']);
            $stmt->execute();
            $cart_id = $db->lastInsertId();
        } else {
            $cart_id = $cart['id'];
        }
    } else {
        if (!isset($_SESSION['cart_id'])) {
            $query = "INSERT INTO cart (session_id) VALUES (:session_id)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':session_id', session_id());
            $stmt->execute();
            $_SESSION['cart_id'] = $db->lastInsertId();
        }
        $cart_id = $_SESSION['cart_id'];
    }

    // Check if product already in cart
    $query = "SELECT * FROM cart_items WHERE cart_id = :cart_id AND product_id = :product_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':cart_id', $cart_id);
    $stmt->bindParam(':product_id', $product_id);
    $stmt->execute();
    $existing_item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing_item) {
        // Update quantity
        $query = "UPDATE cart_items SET quantity = quantity + 1 WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $existing_item['id']);
        $stmt->execute();
    } else {
        // Add new item
        $query = "INSERT INTO cart_items (cart_id, product_id, quantity, price) 
                 VALUES (:cart_id, :product_id, 1, :price)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':cart_id', $cart_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':price', $product['price']);
        $stmt->execute();
    }

    // Get updated cart count
    $query = "SELECT SUM(quantity) as total FROM cart_items WHERE cart_id = :cart_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':cart_id', $cart_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $cart_count = $result['total'] ?? 0;

    echo json_encode(['success' => true, 'cart_count' => $cart_count]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error adding to cart']);
}
?> 