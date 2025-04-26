<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_POST['cart_id'])) {
    echo json_encode(['success' => false, 'message' => 'Cart ID is required']);
    exit();
}

$cart_id = $_POST['cart_id'];
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

    // Remove item
    $query = "DELETE FROM cart_items WHERE id = :cart_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':cart_id', $cart_id);
    $stmt->execute();

    // Get updated cart count
    $query = "SELECT SUM(quantity) as total FROM cart_items WHERE cart_id = :cart_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':cart_id', $cart_item['cart_id']);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $cart_count = $result['total'] ?? 0;

    echo json_encode(['success' => true, 'cart_count' => $cart_count]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error removing from cart']);
}
?> 