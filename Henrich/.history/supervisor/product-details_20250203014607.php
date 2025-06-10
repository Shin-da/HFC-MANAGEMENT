<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';
require_once './access_control.php';

// Debug flag - set to false in production
$debug = true;

function debugLog($message) {
    global $debug;
    if ($debug) {
        error_log("[Product Details Debug] " . $message);
    }
}

try {
    // Basic validation
    if (empty($_GET['id'])) {
        debugLog("No product ID provided");
        throw new Exception("Product ID is required");
    }

    $productId = trim($_GET['id']);
    debugLog("Processing request for product ID: " . $productId);

    // Database connection
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASSWORD,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

    // Single query to get all needed data
    $sql = "
        SELECT 
            p.productcode,
            p.productname,
            p.productweight,
            p.productcategory,
            p.unit_price,
            p.piecesperbox,
            p.productimage,
            p.productstatus,
            p.reorderpoint,
            i.availablequantity,
            i.onhandquantity,
            i.dateupdated
        FROM products p
        LEFT JOIN inventory i ON p.productcode = i.productcode
        WHERE p.productcode = :id
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $productId]);
    $product = $stmt->fetch();

    if (!$product) {
        debugLog("Product not found in database");
        throw new Exception("Product not found");
    }

    debugLog("Product found: " . $product['productname']);

    // Add additional metadata
    $pageTitle = htmlspecialchars($product['productname']) . ' | Product Details';
    $metaDescription = "Details for " . htmlspecialchars($product['productname']) . 
                      " - " . htmlspecialchars($product['productcategory']);

    Page::setTitle($pageTitle);
    Page::setBodyClass('product-details-page');
    Page::addMetaTag('description', $metaDescription);

    Page::set('current_page', 'products');

    // Add required styles
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
                         alt="<?= htmlspecialchars($product['productname']) ?>"
                         onerror="this.src='../assets/images/placeholder.png'">
                </div>
            </div>

            <div class="product-info-section">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Product Code</span>
                        <span class="value product-code"><?= htmlspecialchars($product['productcode']) ?></span>
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
                            <span class="value <?= getStockStatusClass($product['availablequantity'], $product['reorderpoint']) ?>">
                                <?= number_format($product['availablequantity'] ?? 0) ?> units
                            </span>
                        </div>
                        <div class="stock-item">
                            <span class="label">On Hand</span>
                            <span class="value"><?= number_format($product['onhandquantity'] ?? 0) ?> units</span>
                        </div>
                        <div class="stock-item">
                            <span class="label">Pieces per Box</span>
                            <span class="value"><?= number_format($product['piecesperbox'] ?? 0) ?></span>
                        </div>
                        <div class="stock-item">
                            <span class="label">Reorder Point</span>
                            <span class="value"><?= number_format($product['reorderpoint'] ?? 0) ?> units</span>
                        </div>
                        <div class="stock-item">
                            <span class="label">Last Updated</span>
                            <span class="value"><?= $product['dateupdated'] ? date('M j, Y g:i A', strtotime($product['dateupdated'])) : 'Not available' ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let isLoading = false;

    // Image zoom functionality
    const productImage = document.querySelector('.product-image img');
    if (productImage) {
        productImage.addEventListener('mousemove', function(e) {
            const x = e.clientX - e.target.offsetLeft;
            const y = e.clientY - e.target.offsetTop;
            
            e.target.style.transformOrigin = `${x}px ${y}px`;
            e.target.style.transform = 'scale(1.5)';
        });

        productImage.addEventListener('mouseleave', function(e) {
            e.target.style.transform = 'scale(1)';
        });
    }

    // Print functionality with preview
    window.printProduct = function() {
        const content = document.querySelector('.product-content').innerHTML;
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head>
                    <title>${document.title}</title>
                    <link rel="stylesheet" href="../assets/css/product-details.css">
                    <style>
                        @media print {
                            body { padding: 20px; }
                            .no-print { display: none; }
                        }
                    </style>
                </head>
                <body>
                    <div class="print-content">${content}</div>
                </body>
            </html>
        `);
        printWindow.document.close();
        
        // Wait for styles to load
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 250);
    };

    // Add copy button for product code
    const productCode = document.querySelector('.product-code');
    if (productCode) {
        const copyBtn = document.createElement('button');
        copyBtn.className = 'copy-btn';
        copyBtn.innerHTML = '<i class="bx bx-copy"></i>';
        copyBtn.onclick = function() {
            navigator.clipboard.writeText(productCode.textContent);
            this.innerHTML = '<i class="bx bx-check"></i>';
            setTimeout(() => {
                this.innerHTML = '<i class="bx bx-copy"></i>';
            }, 2000);
        };
        productCode.appendChild(copyBtn);
    }

    // Animate numbers
    document.querySelectorAll('.stock-item .value').forEach(el => {
        const value = parseInt(el.textContent);
        if (!isNaN(value)) {
            let current = 0;
            const increment = value / 20;
            const timer = setInterval(() => {
                current += increment;
                if (current >= value) {
                    current = value;
                    clearInterval(timer);
                }
                el.textContent = Math.round(current).toLocaleString() + ' units';
            }, 50);
        }
    });

    // Prevent multiple form submissions
    window.printProduct = function() {
        if (isLoading) return;
        isLoading = true;
        window.print();
        setTimeout(() => isLoading = false, 1000);
    };

    // Handle image errors
    document.querySelectorAll('img').forEach(img => {
        img.onerror = function() {
            this.src = '../assets/images/placeholder.png';
            this.onerror = null; // Prevent infinite loop
        };
    });
});
</script>

<?php
    $content = ob_get_clean();
    Page::render($content);

} catch (PDOException $e) {
    debugLog("Database Error: " . $e->getMessage());
    $_SESSION['error'] = "Database error occurred";
    header("Location: products.php");
    exit();
} catch (Exception $e) {
    debugLog("General Error: " . $e->getMessage());
    $_SESSION['error'] = $e->getMessage();
    header("Location: products.php");
    exit();
}
?>