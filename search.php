<?php
require_once 'includes/init.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get search query and filters
$search_query = isset($_GET['query']) ? trim($_GET['query']) : '';
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 999999;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Build the query
$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE 1=1";

$params = [];

if (!empty($search_query)) {
    $query .= " AND (p.name LIKE :query OR p.description LIKE :query)";
    $params[':query'] = "%$search_query%";
}

if ($category > 0) {
    $query .= " AND p.category_id = :category";
    $params[':category'] = $category;
}

$query .= " AND p.price BETWEEN :min_price AND :max_price";
$params[':min_price'] = $min_price;
$params[':max_price'] = $max_price;

// Add sorting
switch ($sort) {
    case 'price_asc':
        $query .= " ORDER BY p.price ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY p.price DESC";
        break;
    case 'name':
        $query .= " ORDER BY p.name ASC";
        break;
    default:
        $query .= " ORDER BY p.created_at DESC";
}

// Get categories for filter
$category_query = "SELECT * FROM categories ORDER BY name";
$stmt = $db->prepare($category_query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Execute search query
$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Set page title
$page_title = empty($search_query) ? 'All Products' : 'Search Results for "' . htmlspecialchars($search_query) . '"';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - E-Commerce Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-4">
        <h1 class="mb-4"><?php echo $page_title; ?></h1>
        
        <div class="row">
            <!-- Filters -->
            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Filters</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="search.php">
                            <div class="mb-3">
                                <label class="form-label">Search</label>
                                <input type="text" name="query" class="form-control" value="<?php echo htmlspecialchars($search_query); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-select">
                                    <option value="0">All Categories</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>" <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Price Range</label>
                                <div class="row">
                                    <div class="col">
                                        <input type="number" name="min_price" class="form-control" placeholder="Min" value="<?php echo $min_price; ?>">
                                    </div>
                                    <div class="col">
                                        <input type="number" name="max_price" class="form-control" placeholder="Max" value="<?php echo $max_price; ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Sort By</label>
                                <select name="sort" class="form-select">
                                    <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Newest First</option>
                                    <option value="price_asc" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                                    <option value="price_desc" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                                    <option value="name" <?php echo $sort == 'name' ? 'selected' : ''; ?>>Name: A to Z</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Products -->
            <div class="col-md-9">
                <div class="row">
                    <?php if (empty($products)): ?>
                        <div class="col-12">
                            <div class="alert alert-info">
                                No products found matching your criteria.
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <img src="<?php echo htmlspecialchars($product['image'] ?? 'assets/images/placeholder.jpg'); ?>" 
                                         class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                        <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                                        <p class="card-text">
                                            <small class="text-muted">Category: <?php echo htmlspecialchars($product['category_name']); ?></small>
                                        </p>
                                        <p class="card-text">
                                            <strong>$<?php echo number_format($product['price'], 2); ?></strong>
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-primary">View Details</a>
                                            <form method="POST" action="add_to_cart.php" class="d-flex align-items-center">
                                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                <input type="number" name="quantity" value="1" min="1" class="form-control me-2" style="width: 70px;">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-cart-plus"></i> Add to Cart
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
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