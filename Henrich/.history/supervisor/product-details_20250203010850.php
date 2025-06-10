<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';
require_once './access_control.php';

error_log("Product Details Page Accessed");

try {
    // Validate product ID
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception("No product ID provided");
    }

    $productId = trim($_GET['id']);
    error_log("Fetching product ID: " . $productId);

    // Initialize PDO connection with error reporting
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASSWORD,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    // Debug: Print the SQL query
    $sql = "
        SELECT 
            p.*,
            i.availablequantity,
            i.onhandquantity,
            i.packsperbox,
            i.threshold_quantity
        FROM products p
        LEFT JOIN inventory i ON p.productcode = i.productcode
        WHERE p.productcode = :id
    ";
    error_log("SQL Query: " . $sql);

    // Prepare and execute the query
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $productId]);
    
    // Debug: Check if query returned any results
    $product = $stmt->fetch();
    error_log("Query result: " . ($product ? "Product found" : "No product found"));

    if (!$product) {
        throw new Exception("Product with ID {$productId} not found");
    }

    // Continue with existing code...
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

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    $_SESSION['error'] = "Database error occurred. Please try again.";
    header('Location: products.php?error=database_error');
    exit();
} catch (Exception $e) {
    error_log("General Error: " . $e->getMessage());
    $_SESSION['error'] = $e->getMessage();
    header('Location: products.php?error=product_not_found');
    exit();
}
?>