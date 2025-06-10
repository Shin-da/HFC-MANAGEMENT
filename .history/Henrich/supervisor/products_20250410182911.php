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
        const errorMessages = {
            'product_not_found': 'The requested product could not be found.',
            'database_error': 'A database error occurred. Please try again.',
            'invalid_id': 'Invalid product ID provided.',
            'redirect_loop': 'Navigation error occurred. Please try again.',
            'load_failed': 'Failed to load product details. Please try again.'
        };
        
        const error = urlParams.get('error');
        if (errorMessages[error]) {
            showErrorMessage(errorMessages[error]);
        }
    }
    
    // Add throttling for product view
    let isNavigating = false;
    let navigationTimeout;

    window.viewProduct = function(productCode) {
        if (!productCode || isNavigating) return;
        
        // Set navigation flag
        isNavigating = true;
        
        // Clear any existing timeout
        if (navigationTimeout) {
            clearTimeout(navigationTimeout);
        }

        // Show loading indicator
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'loading-overlay';
        loadingDiv.innerHTML = '<div class="spinner"></div>';
        document.body.appendChild(loadingDiv);

        try {
            // Navigate to product details
            window.location.href = 'product-details.php?id=' + encodeURIComponent(productCode.trim());
        } catch (error) {
            console.error('Navigation error:', error);
            loadingDiv.remove();
            isNavigating = false;
        }

        // Cleanup after 5 seconds if navigation fails
        navigationTimeout = setTimeout(() => {
            if (document.body.contains(loadingDiv)) {
                loadingDiv.remove();
            }
            isNavigating = false;
        }, 5000);
    };

    function showErrorMessage(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class='bx bx-error-circle'></i>
                <span>${message}</span>
                <button onclick="this.parentElement.remove();">&times;</button>
            </div>
        `;
        document.querySelector('.products-header').after(errorDiv);

        // Auto-remove error message after 5 seconds
        setTimeout(() => {
            if (document.body.contains(errorDiv)) {
                errorDiv.remove();
            }
        }, 5000);
    }
    
    // Add stock level filter
    const stockFilter = document.createElement('select');
    stockFilter.id = 'stockFilter';
    stockFilter.innerHTML = `
        <option value="">All Stock Levels</option>
        <option value="in-stock">In Stock</option>
        <option value="low-stock">Low Stock (≤ 10)</option>
        <option value="out-of-stock">Out of Stock</option>
    `;
    
    document.querySelector('.filter-box').appendChild(stockFilter);
    
    stockFilter.addEventListener('change', function() {
        const selectedStock = this.value;
        const cards = document.querySelectorAll('.product-card');
        
        cards.forEach(card => {
            let stockBadge = card.querySelector('.badge');
            let stockLevel = stockBadge.textContent.trim();
            let shouldShow = true;
            
            switch(selectedStock) {
                case 'in-stock':
                    shouldShow = !stockLevel.includes('Out of stock') && 
                                 !stockBadge.classList.contains('badge-danger') && 
                                 !stockBadge.classList.contains('badge-warning');
                    break;
                case 'low-stock':
                    shouldShow = stockBadge.classList.contains('badge-warning');
                    break;
                case 'out-of-stock':
                    shouldShow = stockLevel.includes('Out of stock') || 
                                 stockBadge.classList.contains('badge-danger');
                    break;
            }
            
            if (shouldShow) {
                // Keep existing display state (might be hidden by other filters)
                if (card.style.display !== 'none') {
                    card.style.display = 'flex';
                }
            } else {
                card.style.display = 'none';
            }
        });
        
        // Update category visibility
        document.querySelectorAll('.category-section').forEach(section => {
            const hasVisibleProducts = Array.from(section.querySelectorAll('.product-card'))
                .some(card => card.style.display !== 'none');
            section.style.display = hasVisibleProducts ? 'block' : 'none';
        });
    });
    
    // Add price sorting options
    const sortSelect = document.createElement('select');
    sortSelect.id = 'priceSort';
    sortSelect.innerHTML = `
        <option value="">Sort By</option>
        <option value="price-asc">Price: Low to High</option>
        <option value="price-desc">Price: High to Low</option>
        <option value="name-asc">Name: A to Z</option>
        <option value="name-desc">Name: Z to A</option>
    `;
    
    document.querySelector('.filter-box').appendChild(sortSelect);
    
    sortSelect.addEventListener('change', function() {
        const sortValue = this.value;
        
        document.querySelectorAll('.category-section').forEach(section => {
            const productsList = section.querySelector('.products-list');
            const products = Array.from(productsList.querySelectorAll('.product-card'));
            
            products.sort((a, b) => {
                const nameA = a.querySelector('h3').textContent.trim();
                const nameB = b.querySelector('h3').textContent.trim();
                
                const priceA = parseFloat(a.querySelector('.product-price').textContent.replace('₱', '').trim());
                const priceB = parseFloat(b.querySelector('.product-price').textContent.replace('₱', '').trim());
                
                switch(sortValue) {
                    case 'price-asc':
                        return priceA - priceB;
                    case 'price-desc':
                        return priceB - priceA;
                    case 'name-asc':
                        return nameA.localeCompare(nameB);
                    case 'name-desc':
                        return nameB.localeCompare(nameA);
                    default:
                        return 0;
                }
            });
            
            // Remove all products
            products.forEach(product => product.remove());
            
            // Add sorted products back
            products.forEach(product => productsList.appendChild(product));
        });
    });
});
</script>

<style>
/* Enhanced Product Card Styles */
.product-card {
    position: relative;
    display: flex;
    flex-direction: column;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}

.product-image {
    position: relative;
    padding-top: 75%; /* 4:3 aspect ratio */
    overflow: hidden;
    background: #f8f9fa;
}

.product-image img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.product-card:hover .product-image img {
    transform: scale(1.05);
}

.product-info {
    padding: 16px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.product-info h3 {
    font-size: 1.1rem;
    margin: 0 0 10px;
    color: var(--text-primary);
    line-height: 1.3;
}

.product-weight {
    font-size: 0.85rem;
    color: var(--text-secondary);
    margin: 0 0 8px;
}

.product-price {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--primary);
    margin: 8px 0;
}

.stock-status {
    margin: 10px 0;
}

.badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.badge-success {
    background-color: rgba(76, 175, 80, 0.15);
    color: var(--success);
    border: 1px solid var(--success);
}

.badge-warning {
    background-color: rgba(255, 152, 0, 0.15);
    color: #f57c00;
    border: 1px solid #f57c00;
}

.badge-danger {
    background-color: rgba(244, 67, 54, 0.15);
    color: var(--danger);
    border: 1px solid var(--danger);
}

.product-actions {
    padding: 16px;
    border-top: 1px solid #f0f0f0;
}

.btn-view {
    width: 100%;
    padding: 8px 12px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 4px;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.btn-view:hover {
    background: var(--primary-dark);
}

/* Products Grid Enhancements */
.products-grid {
    margin-top: 30px;
}

.category-section {
    margin-bottom: 40px;
}

.category-section h2 {
    font-size: 1.4rem;
    color: var(--text-primary);
    border-bottom: 2px solid var(--primary);
    padding-bottom: 8px;
    margin-bottom: 20px;
    position: relative;
}

.products-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 24px;
}

/* Filter Box Enhancements */
.filter-box {
    display: flex;
    gap: 12px;
    align-items: center;
}

.filter-box select {
    padding: 8px 12px;
    border: 1px solid var(--border);
    border-radius: 4px;
    background: white;
    color: var(--text-primary);
    font-size: 0.9rem;
    min-width: 150px;
}

/* Responsive Enhancements */
@media (max-width: 768px) {
    .products-header {
        flex-direction: column;
        align-items: stretch;
    }
    
    .header-actions {
        flex-direction: column;
    }
    
    .search-box {
        margin-bottom: 16px;
    }
    
    .filter-box {
        flex-wrap: wrap;
    }
    
    .products-list {
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    }
}

.error-message {
    margin: 1rem 0;
}

.alert {
    padding: 1rem;
    border-radius: 4px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.alert-danger {
    background-color: var(--danger-light);
    color: var(--danger);
    border: 1px solid var(--danger);
}

.loading-overlay {
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
