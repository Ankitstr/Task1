<?php
require_once 'includes/init.php';
require_once 'config/database.php';

// Initialize variables
$featured_products = [];
$categories = [];
$db_error = false;

// Try to connect to database
$database = new Database();
$db = $database->getConnection();

if ($db) {
    try {
        // Get featured products
        $query = "SELECT * FROM products ORDER BY created_at DESC LIMIT 8";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $featured_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Prepare products for display
        foreach ($featured_products as &$product) {
            if (empty($product['image'])) {
                $product['image'] = 'assets/images/placeholder.png';
            } else {
                $image_path = 'uploads/products/' . $product['image'];
                $product['image'] = file_exists($image_path) ? $image_path : 'assets/images/placeholder.png';
            }
        }

        // Get categories
        $query = "SELECT * FROM categories LIMIT 6";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch(PDOException $e) {
        $db_error = true;
        error_log("Database Query Error: " . $e->getMessage());
    }
} else {
    $db_error = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Commerce Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color:rgb(124, 97, 97);
            --light-gray: #f8f9fa;
            --dark-gray: #343a40;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-gray);
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('assets/images/hero-bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 80vh;
            display: flex;
            align-items: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(44, 62, 80, 0.7), rgba(52, 152, 219, 0.7));
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 600px;
        }

        .hero-section h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .hero-section p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        .btn-hero {
            padding: 15px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            background-color: var(--accent-color);
            border: none;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-hero:hover {
            background-color: #c0392b;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        /* Categories Section */
        .categories-section {
            padding: 80px 0;
            background-color: white;
        }

        .section-title {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 50px;
            position: relative;
            text-align: center;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background-color: var(--accent-color);
        }

        .category-list {
            list-style: none;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1.5rem;
        }

        .category-list li {
            margin: 0.5rem;
        }

        .category-list a {
            text-decoration: none;
            color: var(--primary-color);
            font-weight: 600;
            padding: 12px 25px;
            border-radius: 50px;
            background: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .category-list a:hover {
            background: var(--secondary-color);
            color: white;
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        /* Featured Products */
        .featured-section {
            padding: 80px 0;
            background-color: var(--light-gray);
        }

        .product-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            height: 100%;
            background: white;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        .product-card .card-img-top {
            height: 250px;
            object-fit: contain;
            background-color: var(--light-gray);
            padding: 20px;
            transition: transform 0.3s ease;
        }

        .product-card:hover .card-img-top {
            transform: scale(1.05);
        }

        .product-card .card-body {
            padding: 25px;
        }

        .product-card .card-title {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 10px;
        }

        .product-card .card-text {
            color: var(--accent-color);
            font-weight: 700;
            font-size: 1.2rem;
            margin-bottom: 15px;
        }

        .product-card .btn {
            background-color: var(--secondary-color);
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .product-card .btn:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }

        /* Features Section */
        .features-section {
            padding: 80px 0;
            background-color: white;
        }

        .feature-item {
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .feature-item:hover {
            transform: translateY(-10px);
        }

        .feature-item i {
            font-size: 3rem;
            color: var(--secondary-color);
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .feature-item:hover i {
            transform: scale(1.1);
            color: var(--accent-color);
        }

        .feature-item h4 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 15px;
        }

        .feature-item p {
            color: var(--dark-gray);
            margin-bottom: 0;
        }

        @media (max-width: 768px) {
            .hero-section {
                height: 60vh;
            }

            .hero-section h1 {
                font-size: 2.5rem;
            }

            .hero-section p {
                font-size: 1.1rem;
            }

            .category-list a {
                padding: 10px 20px;
            }
        }

        @media (max-width: 576px) {
            .hero-section {
                height: 50vh;
            }

            .hero-section h1 {
                font-size: 2rem;
            }

            .section-title {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <?php if ($db_error): ?>
        <div class="alert alert-danger m-3">
            <strong>Error:</strong> Unable to connect to the database. Please try again later.
        </div>
    <?php else: ?>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="hero-content">
                        <h1>Discover Amazing Products</h1>
                        <p>Shop the latest trends and find great deals on quality products. Free shipping on orders over $50.</p>
                        <a href="products.php" class="btn btn-hero">
                            <i class="fas fa-shopping-bag me-2"></i>Start Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories-section">
        <div class="container">
            <h2 class="section-title">Shop by Category</h2>
            <ul class="category-list">
                <?php foreach ($categories as $category): ?>
                <li>
                    <a href="products.php?category=<?php echo $category['id']; ?>">
                        <i class="fas fa-tag"></i>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="featured-section">
        <div class="container">
            <h2 class="section-title">Featured Products</h2>
            <div class="row">
                <?php foreach ($featured_products as $product): ?>
                <div class="col-md-3 mb-4">
                    <div class="card product-card h-100">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text">$<?php echo number_format($product['price'], 2); ?></p>
                            <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">
                                <i class="fas fa-eye me-2"></i>View Details
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="feature-item">
                        <i class="fas fa-shipping-fast"></i>
                        <h4>Free Shipping</h4>
                        <p>On orders over $50</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-item">
                        <i class="fas fa-undo"></i>
                        <h4>Easy Returns</h4>
                        <p>30-day return policy</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-item">
                        <i class="fas fa-lock"></i>
                        <h4>Secure Payment</h4>
                        <p>100% secure payment</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php include 'includes/footer.php'; ?>

    <?php include 'includes/login-modal.php'; ?>
    <?php include 'includes/register-modal.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html> 