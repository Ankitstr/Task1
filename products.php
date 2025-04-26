<?php
require_once 'includes/init.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get filters from session or request
$category = isset($_GET['category']) ? (int)$_GET['category'] : (isset($_SESSION['filter_category']) ? $_SESSION['filter_category'] : 0);
$brand = isset($_GET['brand']) ? (int)$_GET['brand'] : (isset($_SESSION['filter_brand']) ? $_SESSION['filter_brand'] : 0);
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : (isset($_SESSION['filter_min_price']) ? $_SESSION['filter_min_price'] : 0);
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : (isset($_SESSION['filter_max_price']) ? $_SESSION['filter_max_price'] : 999999);
$sort = isset($_GET['sort']) ? $_GET['sort'] : (isset($_SESSION['filter_sort']) ? $_SESSION['filter_sort'] : 'newest');

// Clear all filters if clear=1 is in URL
if (isset($_GET['clear']) && $_GET['clear'] == 1) {
    $category = 0;
    $brand = 0;
    $min_price = 0;
    $max_price = 999999;
    $sort = 'newest';
    
    // Clear session variables
    unset($_SESSION['filter_category']);
    unset($_SESSION['filter_brand']);
    unset($_SESSION['filter_min_price']);
    unset($_SESSION['filter_max_price']);
    unset($_SESSION['filter_sort']);
    
    // Redirect to clean URL
    header('Location: products.php');
    exit;
}

// Save filters to session
$_SESSION['filter_category'] = $category;
$_SESSION['filter_brand'] = $brand;
$_SESSION['filter_min_price'] = $min_price;
$_SESSION['filter_max_price'] = $max_price;
$_SESSION['filter_sort'] = $sort;

// Build the query
$query = "SELECT p.*, c.name as category_name, b.name as brand_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          LEFT JOIN brands b ON p.brand_id = b.id 
          WHERE 1=1";

$params = [];

if ($category > 0) {
    $query .= " AND p.category_id = :category";
    $params[':category'] = $category;
}

if ($brand > 0) {
    $query .= " AND p.brand_id = :brand";
    $params[':brand'] = $brand;
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
    case 'name_asc':
        $query .= " ORDER BY p.name ASC";
        break;
    case 'name_desc':
        $query .= " ORDER BY p.name DESC";
        break;
    default:
        $query .= " ORDER BY p.created_at DESC";
}

// Get categories for filter
$category_query = "SELECT * FROM categories ORDER BY name";
$stmt = $db->prepare($category_query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get brands for filter
$brand_query = "SELECT * FROM brands ORDER BY name";
$stmt = $db->prepare($brand_query);
$stmt->execute();
$brands = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Execute product query
$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare products for display
foreach ($products as &$product) {
    // Set default image if none exists
    if (empty($product['image'])) {
        $product['image'] = 'assets/images/placeholder.jpg';
    } else {
        // Ensure the image path is correct
        $product['image'] = 'uploads/products/' . $product['image'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - E-Commerce Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-gray: #f8f9fa;
            --dark-gray: #343a40;
        }

        body {
            background-color: var(--light-gray);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .filter-sidebar {
            position: sticky;
            top: 20px;
            height: calc(100vh - 40px);
            overflow-y: auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            padding: 20px;
        }

        .filter-sidebar .card {
            border: none;
            box-shadow: none;
        }

        .filter-sidebar .card-header {
            background: none;
            border-bottom: 2px solid var(--light-gray);
            padding: 15px 0;
        }

        .filter-sidebar .card-header h5 {
            color: var(--primary-color);
            font-weight: 600;
        }

        .filter-sidebar .form-label {
            color: var(--dark-gray);
            font-weight: 500;
            margin-bottom: 8px;
        }

        .filter-sidebar .form-select,
        .filter-sidebar .form-control {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }

        .filter-sidebar .form-select:focus,
        .filter-sidebar .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .btn-primary {
            background-color: var(--secondary-color);
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }

        .btn-outline-secondary {
            border: 2px solid var(--secondary-color);
            color: var(--secondary-color);
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline-secondary:hover {
            background-color: var(--secondary-color);
            color: white;
            transform: translateY(-2px);
        }

        .products-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            padding: 25px;
        }

        .products-container h1 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--light-gray);
        }

        .product-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .product-image-container {
            height: 250px;
            background: var(--light-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .product-image {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-image {
            transform: scale(1.05);
        }

        .card-body {
            padding: 20px;
        }

        .card-title {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 10px;
        }

        .price-container {
            margin: 15px 0;
        }

        .text-danger {
            color: var(--accent-color) !important;
        }

        .member-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 1;
        }

        .member-badge .badge {
            background-color: var(--accent-color);
            padding: 8px 12px;
            font-weight: 500;
            border-radius: 6px;
        }

        .toast-container {
            z-index: 1050;
        }

        .toast {
            background-color: var(--primary-color);
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .filter-sidebar {
                height: auto;
                margin-bottom: 20px;
            }
            
            .product-image-container {
                height: 200px;
            }
        }

        @media (max-width: 576px) {
            .product-image-container {
                height: 180px;
            }
            
            .products-container {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Filters - Fixed Sidebar -->
            <div class="col-md-3">
                <div class="filter-sidebar">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Filters</h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="products.php">
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
                                    <label class="form-label">Brand</label>
                                    <select name="brand" class="form-select">
                                        <option value="0">All Brands</option>
                                        <?php foreach ($brands as $br): ?>
                                            <option value="<?php echo $br['id']; ?>" <?php echo $brand == $br['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($br['name']); ?>
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
                                        <option value="name_asc" <?php echo $sort == 'name_asc' ? 'selected' : ''; ?>>Name: A to Z</option>
                                        <option value="name_desc" <?php echo $sort == 'name_desc' ? 'selected' : ''; ?>>Name: Z to A</option>
                                    </select>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                                <a href="products.php?clear=1" class="btn btn-outline-secondary w-100 mt-2">Clear All Filters</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Products - Scrollable Content -->
            <div class="col-md-9">
                <div class="products-container">
                    <h1 class="mb-4">All Products</h1>
                    
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
                                        <div class="position-relative">
                                            <div class="product-image-container">
                                                <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                                     class="product-image" 
                                                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                                            </div>
                                            <?php if ($product['is_member_exclusive']): ?>
                                                <div class="member-badge">
                                                    <span class="badge bg-warning">Member Exclusive</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                            <div class="price-container">
                                                <?php if (isset($_SESSION['is_member']) && $_SESSION['is_member'] && $product['member_price']): ?>
                                                    <div class="d-flex align-items-center">
                                                        <span class="text-muted text-decoration-line-through me-2">$<?php echo number_format($product['price'], 2); ?></span>
                                                        <span class="text-danger fw-bold">$<?php echo number_format($product['member_price'], 2); ?></span>
                                                        <span class="badge bg-success ms-2">Member Price</span>
                                                    </div>
                                                <?php else: ?>
                                                    <p class="card-text">$<?php echo number_format($product['price'], 2); ?></p>
                                                    <?php if ($product['member_price']): ?>
                                                        <small class="text-muted">Member Price: $<?php echo number_format($product['member_price'], 2); ?></small>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                            <div class="mt-2">
                                                <?php if ($product['is_member_exclusive'] && (!isset($_SESSION['is_member']) || !$_SESSION['is_member'])): ?>
                                                    <button class="btn btn-warning w-100" disabled>
                                                        <i class="fas fa-lock me-2"></i>Member Exclusive
                                                    </button>
                                                    <small class="text-muted d-block mt-2">
                                                        <a href="profile.php" class="text-decoration-none">Upgrade to Premium</a> to access this product
                                                    </small>
                                                <?php else: ?>
                                                    <form class="add-to-cart-form" method="POST" action="add_to_cart.php">
                                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                        <div class="input-group mb-2">
                                                            <input type="number" name="quantity" class="form-control" value="1" min="1" style="width: 80px;">
                                                            <button type="submit" class="btn btn-primary">
                                                                <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                                            </button>
                                                        </div>
                                                    </form>
                                                    <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-primary w-100">View Details</a>
                                                <?php endif; ?>
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
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/js/main.js"></script>
    <div class="toast-container position-fixed bottom-0 end-0 p-3"></div>
    <script>
        $(document).ready(function() {
            // Handle add to cart form submission
            $('.add-to-cart-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const button = form.find('button[type="submit"]');
                const productId = form.find('input[name="product_id"]').val();
                const quantity = form.find('input[name="quantity"]').val();
                
                // Show loading state
                button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...');
                button.prop('disabled', true);
                
                // Log the data being sent
                console.log('Adding to cart:', { productId, quantity });
                
                $.ajax({
                    url: 'add_to_cart.php',
                    method: 'POST',
                    data: {
                        product_id: productId,
                        quantity: quantity
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log('Cart response:', response);
                        
                        if (response.success) {
                            // Update cart count in navbar
                            $('#cart-count').text(response.cart_count);
                            
                            // Show success toast
                            showToast('Product added to cart!', 'success');
                            
                            // Update cart count in navbar
                            if (response.cart_count) {
                                $('#cart-count').text(response.cart_count);
                            }
                        } else {
                            // Show error toast
                            showToast(response.message || 'Error adding to cart', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Cart error:', { xhr, status, error });
                        showToast('Error adding to cart: ' + error, 'error');
                    },
                    complete: function() {
                        // Reset button state
                        button.html('<i class="fas fa-shopping-cart me-2"></i>Add to Cart');
                        button.prop('disabled', false);
                    }
                });
            });
            
            // Toast notification function
            function showToast(message, type = 'success') {
                const toast = $(`
                    <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" 
                         role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                ${message}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                `);
                
                $('.toast-container').append(toast);
                const bsToast = new bootstrap.Toast(toast);
                bsToast.show();
                
                // Remove toast after it's hidden
                toast.on('hidden.bs.toast', function() {
                    $(this).remove();
                });
            }
        });
    </script>
</body>
</html> 