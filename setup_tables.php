<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    // Create orders table
    $query = "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        shipping_name VARCHAR(100) NOT NULL,
        shipping_address TEXT NOT NULL,
        shipping_city VARCHAR(100) NOT NULL,
        shipping_state VARCHAR(100) NOT NULL,
        shipping_zip VARCHAR(20) NOT NULL,
        shipping_country VARCHAR(100) NOT NULL,
        payment_method VARCHAR(50) NOT NULL,
        status VARCHAR(20) DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $db->exec($query);
    echo "Orders table created successfully<br>";

    // Create order_items table
    $query = "CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $db->exec($query);
    echo "Order items table created successfully<br>";

    // Verify tables exist
    $tables = ['orders', 'order_items'];
    foreach ($tables as $table) {
        $stmt = $db->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "Table '$table' exists<br>";
        } else {
            echo "Table '$table' does not exist<br>";
        }
    }

    // Verify columns in orders table
    $stmt = $db->query("SHOW COLUMNS FROM orders");
    echo "<br>Columns in orders table:<br>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . "<br>";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 