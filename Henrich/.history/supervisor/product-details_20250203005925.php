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
try {
    // Validate product ID
    if (!isset($_GET['id'])) {
        header('Location: products.php');
        exit();
    }

    // Get product details
    $productId = $_GET['id'];
    $sql = "SELECT * FROM products WHERE productcode = ? AND productstatus = 'Active'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $productId);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();

    if (!$product) {
        throw new Exception("Product not found");
    }

    ob_start();
?>

<div class="page-wrapper theme-aware">
    <div class="content-container">
        <div class="product-nav theme-container">
            <a href="products.php" class="back-btn theme-btn">
                <i class='bx bx-arrow-back'></i> Back to Products
            </a>
        </div>

        <div class="product-details theme-container">
            <div class="product-grid">
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