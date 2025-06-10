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

<?php
    $content = ob_get_clean();
    Page::render($content);
} catch (Exception $e) {
    error_log("Product details error: " . $e->getMessage());
    header('Location: products.php');
    exit();
}
?>