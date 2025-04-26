<?php
session_start();

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Function to update cart count
function updateCartCount() {
    $count = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $count += $item['quantity'];
        }
    }
    $_SESSION['cart_count'] = $count;
}

// Function to add item to cart
function addToCart($product_id, $quantity = 1) {
    if (!isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] = array(
            'quantity' => $quantity
        );
    } else {
        $_SESSION['cart'][$product_id]['quantity'] += $quantity;
    }
    updateCartCount();
}

// Function to remove item from cart
function removeFromCart($product_id) {
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
        updateCartCount();
    }
}

// Function to update item quantity
function updateCartQuantity($product_id, $quantity) {
    if (isset($_SESSION['cart'][$product_id])) {
        if ($quantity <= 0) {
            removeFromCart($product_id);
        } else {
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
        }
        updateCartCount();
    }
}

// Function to clear cart
function clearCart() {
    $_SESSION['cart'] = array();
    $_SESSION['cart_count'] = 0;
}

// Function to get cart items
function getCartItems() {
    return isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
}

// Function to get cart total
function getCartTotal() {
    $total = 0;
    if (isset($_SESSION['cart'])) {
        require_once 'config/database.php';
        $database = new Database();
        $db = $database->getConnection();
        
        foreach ($_SESSION['cart'] as $product_id => $item) {
            $query = "SELECT price FROM products WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(":id", $product_id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $total += $row['price'] * $item['quantity'];
            }
        }
    }
    return $total;
}
?> 