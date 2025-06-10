<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';
require_once './access_control.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');
$_SESSION['current_page'] = $current_page;

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASSWORD,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );

    if (!isset($_GET['id'])) {
        throw new Exception("No product ID specified");
    }

    // Fetch product with inventory data
    $stmt = $pdo->prepare("
        SELECT p.*, 
               i.availablequantity,
               i.onhandquantity,
               i.packsperbox,
               i.threshold_quantity
        FROM products p
        LEFT JOIN inventory i ON p.productcode = i.productcode
        WHERE p.productcode = :id AND p.productstatus = 'Active'
    ");
    
    $stmt->execute(['id' => $_GET['id']]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        throw new Exception("Product not found");
    }

    // Configure page
    Page::setTitle($product['productname'] . ' | Product Details');
    Page::setBodyClass('product-details-page');
    Page::set('current_page', 'products');

    // Add styles
                <div class="product-image">
                    <img src="<?= $product['productimage'] ? 'uploads/products/' . $product['productimage'] : 'https://picsum.photos/500/500' ?>"
                         alt="<?= htmlspecialchars($product['productname']) ?>">
                </div>
                
                <div class="product-info">
                    <h1><?= htmlspecialchars($product['productname']) ?></h1>
                    <div class="info-group">
                        <label>Category:</label>
                        <span class="category"><?= htmlspecialchars($product['productcategory']) ?></span>
                    </div>
                    <div class="info-group">
                        <label>Weight:</label>
                        <span class="weight"><?= htmlspecialchars($product['productweight']) ?> g</span>
                    </div>
                    <div class="info-group">
                        <label>Price:</label>
                        <span class="price">₱<?= number_format($product['unit_price'], 2) ?></span>
                    </div>
                    <div class="info-group">
                        <label>packs per box:</label>
                        <span class="packs"><?= htmlspecialchars($product['packsperbox']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    $content = ob_get_clean();
    Page::render($content);
} catch (Exception $e) {
    error_log("Product details error: " . $e->getMessage());
    header('Location: products.php');
    exit();
}
?>