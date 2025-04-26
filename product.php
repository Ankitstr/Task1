<?php
require_once 'includes/init.php';
require_once 'config/database.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    header("Location: products.php");
    exit();
}

// Get product details
$query = "SELECT p.*, c.name as category_name, b.name as brand_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          LEFT JOIN brands b ON p.brand_id = b.id 
          WHERE p.id = :id";

$stmt = $db->prepare($query);
$stmt->bindParam(':id', $product_id);
$stmt->execute();

$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: products.php");
    exit();
}

// Prepare product image
if (empty($product['image'])) {
    $product['image'] = 'assets/images/placeholder.png';
} else {
    $image_path = 'uploads/products/' . $product['image'];
    if (!file_exists($image_path)) {
        $product['image'] = 'assets/images/placeholder.png';
    } else {
        $product['image'] = $image_path;
    }
}

// Get related products
$query = "SELECT * FROM products 
          WHERE category_id = :category_id 
          AND id != :product_id 
          LIMIT 4";

$stmt = $db->prepare($query);
$stmt->bindParam(':category_id', $product['category_id']);
$stmt->bindParam(':product_id', $product_id);
$stmt->execute();

$related_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare related products images
foreach ($related_products as &$related_product) {
    if (empty($related_product['image'])) {
        $related_product['image'] = 'assets/images/placeholder.png';
    } else {
        $image_path = 'uploads/products/' . $related_product['image'];
        if (!file_exists($image_path)) {
            $related_product['image'] = 'assets/images/placeholder.png';
        } else {
            $related_product['image'] = $image_path;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - E-Commerce Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .product-image-container {
            height: 400px;
            width: 100%;
            overflow: hidden;
            position: relative;
            background-color: #f8f9fa;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .product-image {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            transition: transform 0.3s ease;
            padding: 20px;
        }
        .product-image:hover {
            transform: scale(1.05);
        }
        .product-title {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .product-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: #007bff;
            margin-bottom: 1rem;
        }
        .product-description {
            margin-bottom: 2rem;
            color: #666;
            line-height: 1.6;
        }
        .product-meta {
            margin-bottom: 2rem;
        }
        .product-meta span {
            margin-right: 1.5rem;
            color: #666;
        }
        .product-meta i {
            margin-right: 0.5rem;
            color: #007bff;
        }
        .quantity-input {
            width: 100px;
            margin-right: 10px;
        }
        .btn-add-to-cart {
            padding: 0.8rem 2rem;
            font-weight: 600;
        }
        .related-products {
            margin-top: 4rem;
        }
        .related-product-card {
            transition: transform 0.3s;
            height: 100%;
        }
        .related-product-card:hover {
            transform: translateY(-5px);
        }
        .related-product-image {
            height: 200px;
            object-fit: contain;
            padding: 10px;
        }
        .toast-container {
            z-index: 1050;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container py-5">
        <div class="row">
            <div class="col-md-6">
                <div class="product-image-container">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                         class="product-image" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
            </div>
            <div class="col-md-6">
                <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
                <p class="product-price">$<?php echo number_format($product['price'], 2); ?></p>
                
                <div class="product-meta">
                    <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($product['category_name']); ?></span>
                    <span><i class="fas fa-industry"></i> <?php echo htmlspecialchars($product['brand_name']); ?></span>
                </div>
                
                <p class="product-description"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                
                <form id="add-to-cart-form" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo (int)$product['id']; ?>">
                    <div class="d-flex align-items-center mb-4">
                        <input type="number" class="form-control quantity-input" name="quantity" value="1" min="1" max="10">
                        <button type="submit" class="btn btn-primary btn-add-to-cart">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <?php if (!empty($related_products)): ?>
        <div class="related-products">
            <h2 class="text-center mb-4">Related Products</h2>
            <div class="row">
                <?php foreach ($related_products as $related_product): ?>
                <div class="col-md-3 mb-4">
                    <div class="card related-product-card h-100">
                        <div class="text-center p-3">
                            <img src="<?php echo htmlspecialchars($related_product['image']); ?>" 
                                 class="related-product-image" 
                                 alt="<?php echo htmlspecialchars($related_product['name']); ?>">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($related_product['name']); ?></h5>
                            <p class="card-text">$<?php echo number_format($related_product['price'], 2); ?></p>
                            <a href="product.php?id=<?php echo $related_product['id']; ?>" class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Add toast container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3"></div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            console.log('Document ready');
            console.log('Product ID:', <?php echo (int)$product['id']; ?>);
            
            // Handle form submission
            $('#add-to-cart-form').on('submit', function(e) {
                e.preventDefault();
                console.log('Form submitted');
                
                const form = $(this);
                const button = form.find('button[type="submit"]');
                const productId = form.find('input[name="product_id"]').val();
                const quantity = form.find('input[name="quantity"]').val();
                
                console.log('Product ID:', productId);
                console.log('Quantity:', quantity);
                
                if (!productId || productId <= 0) {
                    console.error('Invalid product ID');
                    showToast('Invalid product ID', 'error');
                    return;
                }
                
                if (!quantity || quantity < 1) {
                    console.error('Invalid quantity');
                    showToast('Please enter a valid quantity', 'error');
                    return;
                }
                
                // Show loading state
                button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...');
                button.prop('disabled', true);
                
                $.ajax({
                    url: 'add_to_cart.php',
                    method: 'POST',
                    data: {
                        product_id: productId,
                        quantity: quantity
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log('Server response:', response);
                        
                        if (response.success) {
                            // Update cart count
                            const cartCount = $('#cart-count');
                            if (cartCount.length) {
                                cartCount.text(response.cart_count);
                            } else {
                                console.warn('Cart count element not found');
                            }
                            
                            // Show success toast
                            showToast('Product added to cart!', 'success');
                        } else {
                            // Show error toast
                            showToast(response.message || 'Error adding to cart', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', {xhr, status, error});
                        showToast('Error adding to cart: ' + error, 'error');
                    },
                    complete: function() {
                        // Reset button state
                        button.html('<i class="fas fa-shopping-cart"></i> Add to Cart');
                        button.prop('disabled', false);
                    }
                });
            });
            
            // Toast notification function
            function showToast(message, type = 'success') {
                console.log('Showing toast:', message, type);
                const toast = $('<div class="toast align-items-center text-white bg-' + (type === 'success' ? 'success' : 'danger') + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">' +
                    '<div class="d-flex">' +
                    '<div class="toast-body">' + message + '</div>' +
                    '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>' +
                    '</div>' +
                    '</div>');
                
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