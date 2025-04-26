<?php
require_once 'includes/init.php';
require_once 'config/database.php';

// Debug session state
error_log("Session state in navbar: " . print_r($_SESSION, true));

// Get current page for active state
$current_page = basename($_SERVER['PHP_SELF']);

// Initialize cart count if not set
if (!isset($_SESSION['cart_count'])) {
    $_SESSION['cart_count'] = 0;
}

// Get categories for dropdown
$database = new Database();
$db = $database->getConnection();
$category_query = "SELECT * FROM categories ORDER BY name";
$stmt = $db->prepare($category_query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Add Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
.navbar {
    background: rgba(255, 255, 255, 0.95) !important;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 0.8rem 0;
    position: relative;
    z-index: 1000;
}

.navbar-brand {
    font-size: 1.5rem;
    font-weight: 700;
    margin-left: -40px;
    color: #333 !important;
    transition: all 0.3s ease;
}

.navbar-brand:hover {
    transform: translateY(-2px);
}

.nav-link {
    color: #333 !important;
    font-weight: 500;
    padding: 0.5rem 1rem !important;
    margin: 0 0.2rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.nav-link:hover {
    background: rgba(0, 123, 255, 0.1);
    color: #007bff !important;
    transform: translateY(-2px);
}

.nav-link.active {
    background: #007bff;
    color: white !important;
}

.nav-link i {
    margin-right: 5px;
}

.dropdown-menu {
    border: none;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    padding: 0.5rem;
    margin-top: 0.5rem;
    min-width: 200px;
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    position: absolute;
    z-index: 1001;
}

.dropdown-item {
    padding: 0.6rem 1.2rem;
    border-radius: 8px;
    transition: all 0.3s ease;
    color: #333;
    font-weight: 500;
    display: flex;
    align-items: center;
}

.dropdown-item i {
    width: 20px;
    text-align: center;
    margin-right: 0.5rem;
    color: #666;
}

.dropdown-item:hover {
    background: rgba(0, 123, 255, 0.1);
    transform: translateX(5px);
    color: #007bff;
}

.dropdown-item:hover i {
    color: #007bff;
}

.dropdown-divider {
    margin: 0.5rem 0;
    border-color: rgba(0, 0, 0, 0.1);
}

.dropdown-item.text-danger {
    color: #dc3545 !important;
}

.dropdown-item.text-danger:hover {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545 !important;
}

.dropdown-item.text-danger i {
    color: #dc3545;
}

.nav-link.dropdown-toggle::after {
    display: inline-block;
    margin-left: 0.5rem;
    vertical-align: middle;
    content: "";
    border-top: 0.3em solid;
    border-right: 0.3em solid transparent;
    border-bottom: 0;
    border-left: 0.3em solid transparent;
    transition: transform 0.3s ease;
}

.nav-link.dropdown-toggle.show::after {
    transform: rotate(180deg);
}

.search-form {
    position: relative;
    margin: 0 1rem;
}

.search-form .form-control {
    border-radius: 20px;
    padding: 0.6rem 1.2rem;
    border: 1px solid rgba(0, 0, 0, 0.1);
    background: rgba(0, 0, 0, 0.03);
    transition: all 0.3s ease;
}

.search-form .form-control:focus {
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
    border-color: rgba(0, 123, 255, 0.3);
}

.search-form .btn {
    position: absolute;
    right: 5px;
    top: 50%;
    transform: translateY(-50%);
    border-radius: 50%;
    width: 35px;
    height: 35px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #007bff;
    border: none;
    transition: all 0.3s ease;
}

.search-form .btn:hover {
    background: #0056b3;
    transform: translateY(-50%) scale(1.1);
}

.badge {
    position: absolute;
    top: -5px;
    right: -5px;
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
    border-radius: 10px;
}

.navbar-toggler {
    border: none;
    padding: 0.5rem;
}

.navbar-toggler:focus {
    box-shadow: none;
}

.navbar-toggler-icon {
    background-image: none;
    position: relative;
    width: 24px;
    height: 2px;
    background-color: #333;
}

.navbar-toggler-icon::before,
.navbar-toggler-icon::after {
    content: '';
    position: absolute;
    width: 24px;
    height: 2px;
    background-color: #333;
}

.navbar-toggler-icon::before {
    top: -6px;
}

.navbar-toggler-icon::after {
    bottom: -6px;
}

@media (max-width: 991.98px) {
    .navbar-collapse {
        background: rgba(255, 255, 255, 0.98);
        padding: 1rem;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        margin-top: 1rem;
        position: relative;
        z-index: 1000;
    }
    
    .nav-link {
        padding: 0.8rem 1rem !important;
        margin: 0.2rem 0;
    }
    
    .search-form {
        margin: 1rem 0;
    }
    
    .dropdown-menu {
        border: none;
        box-shadow: none;
        background: transparent;
        padding: 0;
        margin: 0;
        position: static;
    }
    
    .dropdown-item {
        padding: 0.8rem 1rem;
        border-radius: 0;
    }
    
    .dropdown-item:hover {
        transform: none;
        background: rgba(0, 123, 255, 0.1);
    }
}
</style>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-shopping-bag"></i> E-Shop
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>" href="index.php">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'products.php' ? 'active' : ''; ?>" href="products.php">
                        <i class="fas fa-shopping-bag"></i> Products
                    </a>
                </li>
            </ul>
            <form class="search-form" action="products.php" method="GET">
                <input class="form-control" type="search" name="query" placeholder="Search products..." value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>">
                <button class="btn" type="submit">
                    <i class="fas fa-search text-white"></i>
                </button>
            </form>
            <ul class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li>
                                <a class="dropdown-item" href="profile.php">
                                    <i class="fas fa-user me-2"></i> Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="orders.php">
                                    <i class="fas fa-shopping-bag me-2"></i> My Orders
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="wishlist.php">
                                    <i class="fas fa-heart me-2"></i> Wishlist
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="settings.php">
                                    <i class="fas fa-cog me-2"></i> Settings
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link btn-login" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#loginModal">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#registerModal">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    </li>
                <?php endif; ?>
                <li class="nav-item position-relative">
                    <a class="nav-link" href="cart.php">
                        <i class="fas fa-shopping-cart"></i> Cart
                        <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                            <span class="badge bg-danger"><?php echo count($_SESSION['cart']); ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<?php include 'includes/login-modal.php'; ?>
<?php include 'includes/register-modal.php'; ?>

<script>
// Initialize all dropdowns
document.addEventListener('DOMContentLoaded', function() {
    var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    var dropdownList = dropdownElementList.map(function(dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl);
    });

    // Initialize modals
    const loginModal = new bootstrap.Modal(document.getElementById('loginModal'), {
        backdrop: true,
        keyboard: true
    });

    const registerModal = new bootstrap.Modal(document.getElementById('registerModal'), {
        backdrop: true,
        keyboard: true
    });

    // Add click handler for login button
    const loginButton = document.querySelector('.btn-login');
    if (loginButton) {
        loginButton.addEventListener('click', function(e) {
            e.preventDefault();
            loginModal.show();
        });
    }

    // Handle modal switching
    const switchToRegister = document.getElementById('switchToRegister');
    if (switchToRegister) {
        switchToRegister.addEventListener('click', function(e) {
            e.preventDefault();
            loginModal.hide();
            
            // Clean up any existing backdrops
            const existingBackdrops = document.querySelectorAll('.modal-backdrop');
            existingBackdrops.forEach(backdrop => backdrop.remove());
            
            // Reset body styles
            document.body.classList.remove('modal-open');
            document.body.style.overflow = 'auto';
            document.body.style.paddingRight = '0';
            
            // Show register modal after a short delay
            setTimeout(function() {
                registerModal.show();
            }, 300);
        });
    }

    // Handle modal closing
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function () {
            // Remove modal-open class and reset body styles
            document.body.classList.remove('modal-open');
            document.body.style.overflow = 'auto';
            document.body.style.paddingRight = '0';
            
            // Remove any remaining backdrop
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => backdrop.remove());
        });
    });
});
</script> 