<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';
require_once './access_control.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');
$_SESSION['current_page'] = $current_page;

// Initialize database connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASSWORD,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Fetch products with prepared statement
function getProducts($pdo) {
    try {
        $stmt = $pdo->query("
            SELECT p.*, i.availablequantity, i.onhandquantity 
            FROM products p 
            LEFT JOIN inventory i ON p.productcode = i.productcode 
            WHERE p.productstatus = 'Active'
            ORDER BY p.productcategory, p.productname
        ");
        
        $products = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $products[$row['productcategory']][] = $row;
        }
        return $products;
    } catch (PDOException $e) {
        error_log("Error fetching products: " . $e->getMessage());
        return [];
    }
}

// Get products data
$products = getProducts($pdo);

// Configure page
Page::setTitle('Products | Supervisor');
Page::setBodyClass('products-page');
Page::set('current_page', 'products');

// Add styles
Page::addStyle('../assets/css/style.css');
Page::addStyle('../assets/css/variables.css');
Page::addStyle('../assets/css/products.css');
Page::addStyle('../assets/css/components/badge.css');

// Start output buffering
ob_start();
?>

<div class="products-wrapper">
    <div class="products-header">
        <h1>Products Catalog</h1>
        <div class="header-actions">
            <div class="search-box">
                <input type="text" id="productSearch" placeholder="Search products...">
                <i class='bx bx-search'></i>
            </div>
            <div class="filter-box">
                <select id="categoryFilter">
                    <option value="">All Categories</option>
                    <?php foreach (array_keys($products) as $category): ?>
                        <option value="<?= htmlspecialchars($category) ?>">
                            <?= htmlspecialchars($category) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <div class="products-grid">
        <?php foreach ($products as $category => $items): ?>
            <div class="category-section" data-category="<?= htmlspecialchars($category) ?>">
                <h2><?= htmlspecialchars($category) ?></h2>
                <div class="products-list">
                    <?php foreach ($items as $product): ?>
                        <div class="product-card" data-product-code="<?= htmlspecialchars($product['productcode']) ?>">
                            <div class="product-image">
                                <img src="<?= $product['productimage'] ? '../uploads/products/' . htmlspecialchars($product['productimage']) : '../assets/images/placeholder.png' ?>" 
                                     alt="<?= htmlspecialchars($product['productname']) ?>">
                            </div>
                            <div class="product-info">
                                <h3><?= htmlspecialchars($product['productname']) ?></h3>
                                <p class="product-weight"><?= htmlspecialchars($product['productweight']) ?>g</p>
                                <p class="product-price">₱<?= number_format($product['unit_price'], 2) ?></p>
                                <div class="stock-status">
                                    <span class="badge <?= ($product['availablequantity'] > 10) ? 'badge-success' : 
                                        (($product['availablequantity'] > 0) ? 'badge-warning' : 'badge-danger') ?>">
                                        <?= ($product['availablequantity'] > 0) ? 
                                            $product['availablequantity'] . ' in stock' : 'Out of stock' ?>
                                    </span>
                                </div>
                            </div>
                            <div class="product-actions">
                                <button class="btn-view" onclick="viewProduct('<?= $product['productcode'] ?>')">
                                    View Details
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('productSearch');
    const categoryFilter = document.getElementById('categoryFilter');
    
    function filterProducts() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCategory = categoryFilter.value.toLowerCase();
        
        document.querySelectorAll('.product-card').forEach(card => {
            const productName = card.querySelector('h3').textContent.toLowerCase();
            const category = card.closest('.category-section').dataset.category.toLowerCase();
            
            const matchesSearch = productName.includes(searchTerm);
            const matchesCategory = !selectedCategory || category === selectedCategory;
            
            card.style.display = (matchesSearch && matchesCategory) ? 'flex' : 'none';
        });
        
        // Show/hide category sections
        document.querySelectorAll('.category-section').forEach(section => {
            const hasVisibleProducts = Array.from(section.querySelectorAll('.product-card'))
                .some(card => card.style.display !== 'none');
            section.style.display = hasVisibleProducts ? 'block' : 'none';
        });
    }
    
    searchInput.addEventListener('input', filterProducts);
    categoryFilter.addEventListener('change', filterProducts);

    // Enhanced error handling
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('error')) {
        const error = urlParams.get('error');
        const errorMessages = {
            'product_not_found': 'The requested product could not be found.',
            'database_error': 'A database error occurred. Please try again.',
            'invalid_id': 'Invalid product ID provided.'
        };
        
        if (errorMessages[error]) {
            // Create and show error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class='bx bx-error-circle'></i>
                    <span>${errorMessages[error]}</span>
                    <button onclick="this.parentElement.remove();">&times;</button>
                </div>
            `;
            document.querySelector('.products-header').after(errorDiv);
        }
    }
});

// Replace the existing viewProduct function with this:
function viewProduct(productCode) {
    if (!productCode) {
        console.error('No product code provided');
        return;
    }

    // Prevent multiple clicks
    if (window.isNavigating) return;
    window.isNavigating = true;

    // Show loading indicator
    const loadingDiv = document.createElement('div');
    loadingDiv.className = 'loading-overlay';
    loadingDiv.innerHTML = '<div class="spinner"></div>';
    document.body.appendChild(loadingDiv);

    try {
        console.log('Navigating to product:', productCode);
        window.location.href = 'product-details.php?id=' + encodeURIComponent(productCode.trim());
    } catch (error) {
        console.error('Navigation error:', error);
        alert('Error loading product details. Please try again.');
        loadingDiv.remove();
        window.isNavigating = false;
    }

    // Cleanup after 2 seconds if navigation hasn't occurred
    setTimeout(() => {
        if (document.body.contains(loadingDiv)) {
            loadingDiv.remove();
        }
        window.isNavigating = false;
    }, 2000);
}
</script>

<style>
.error-message {
    margin: 1rem 0;
}

.alert {
    padding: 1rem;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid var(--primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<?php
// Render the page
Page::render(ob_get_clean());
?>
