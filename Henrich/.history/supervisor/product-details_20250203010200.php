<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';
require_once './access_control.php';

// Debug logging
error_log("Product Details Page Accessed - ID: " . ($_GET['id'] ?? 'not set'));

$current_page = basename($_SERVER['PHP_SELF'], '.php');
$_SESSION['current_page'] = $current_page;

try {
    // Validate product ID
    if (!isset($_GET['id'])) {
        throw new Exception("No product ID provided");
    }

    $productId = $_GET['id'];
    error_log("Fetching product with ID: " . $productId);

    // Initialize PDO connection
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASSWORD,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );

    // Fetch product with inventory data
    $stmt = $pdo->prepare("
        SELECT 
            p.*,
            i.availablequantity,
            i.onhandquantity,
            i.packsperbox,
            i.threshold_quantity
        FROM products p
        LEFT JOIN inventory i ON p.productcode = i.productcode
        WHERE p.productcode = :id
    ");
    
    $stmt->execute(['id' => $productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        error_log("Product not found for ID: " . $productId);
        throw new Exception("Product not found");
    }

    error_log("Product found: " . json_encode($product));

    // Configure page
    Page::setTitle($product['productname'] . ' | Product Details');
    Page::setBodyClass('product-details-page');
    Page::set('current_page', 'products');

    // Add styles
    Page::addStyle('../assets/css/style.css');
    Page::addStyle('../assets/css/variables.css');
    Page::addStyle('../assets/css/product-details.css');
    Page::addStyle('../assets/css/components/badge.css');

    ob_start();
?>

<div class="product-details-wrapper">
    <div class="product-header">
        <div class="header-left">
            <a href="products.php" class="back-button">
                <i class='bx bx-arrow-back'></i>
                Back to Products
            </a>
            <h1><?= htmlspecialchars($product['productname']) ?></h1>
        </div>
        <div class="header-actions">
            <button class="btn btn-secondary" onclick="printProduct()">
                <i class='bx bx-printer'></i> Print
            </button>
        </div>
    </div>

    <div class="product-content">
        <div class="product-main">
            <div class="product-image-section">
                <div class="product-image">
                    <img src="<?= $product['productimage'] ? '../uploads/products/' . htmlspecialchars($product['productimage']) : '../assets/images/placeholder.png' ?>" 
                         alt="<?= htmlspecialchars($product['productname']) ?>">
                </div>
            </div>

            <div class="product-info-section">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Product Code</span>
                        <span class="value"><?= htmlspecialchars($product['productcode']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Category</span>
                        <span class="value"><?= htmlspecialchars($product['productcategory']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Weight</span>
                        <span class="value"><?= htmlspecialchars($product['productweight']) ?>g</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Unit Price</span>
                        <span class="value price">₱<?= number_format($product['unit_price'], 2) ?></span>
                    </div>
                </div>

                <div class="stock-section">
                    <h3>Stock Information</h3>
                    <div class="stock-grid">
                        <div class="stock-item">
                            <span class="label">Available Stock</span>
                            <span class="value <?= getStockStatusClass($product['availablequantity'], $product['threshold_quantity']) ?>">
                                <?= number_format($product['availablequantity']) ?> units
                            </span>
                        </div>
                        <div class="stock-item">
                            <span class="label">On Hand</span>
                            <span class="value"><?= number_format($product['onhandquantity']) ?> units</span>
                        </div>
                        <div class="stock-item">
                            <span class="label">Packs per Box</span>
                            <span class="value"><?= number_format($product['packsperbox']) ?></span>
                        </div>
                        <div class="stock-item">
                            <span class="label">Threshold</span>
                            <span class="value"><?= number_format($product['threshold_quantity']) ?> units</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Add error handling for images
document.querySelectorAll('.product-image img').forEach(img => {
    img.onerror = function() {
        this.src = '../assets/images/placeholder.png';
        console.log('Image failed to load, using placeholder');
    };
});

// Print functionality
function printProduct() {
    window.print();
}
</script>

<?php
    // Helper function for stock status
    function getStockStatusClass($available, $threshold) {
        if ($available <= 0) return 'stock-out';
        if ($available <= $threshold) return 'stock-low';
        return 'stock-good';
    }

    $content = ob_get_clean();
    Page::render($content);

} catch (Exception $e) {
    // Log the error
    error_log("Product details error: " . $e->getMessage());
    
    // Set error message in session
    $_SESSION['error'] = "Error loading product: " . $e->getMessage();
    
    // Redirect with error parameter
    header('Location: products.php?error=product_not_found');
    exit();
}
?>