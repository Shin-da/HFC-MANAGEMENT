<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';

// Initialize page
Page::setTitle('View Product');
Page::setBodyClass('supervisor-body');
Page::setCurrentPage('view-product');

// Add theme and core styles
Page::addStyle('/assets/css/themes.css');
Page::addStyle('/assets/css/product-view.css');

// Get product data
try {
    $productcode = isset($_GET['code']) ? $_GET['code'] : '';
    
    // Fetch product details
    $sql = "SELECT 
        p.*,
        i.availablequantity,
        i.onhandquantity,
        i.unit_price,
        i.dateupdated
    FROM products p
    LEFT JOIN inventory i ON p.productcode = i.productcode
    WHERE p.productcode = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $productcode);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    // Fetch stock movement history
    $historySql = "SELECT 
        sm.dateencoded,
        sm.numberofbox,
        sm.totalpacks,
        sm.totalweight,
        sm.encoder
    FROM stockmovement sm
    WHERE sm.productcode = ?
    ORDER BY sm.dateencoded DESC
    LIMIT 10";

    $historyStmt = $conn->prepare($historySql);
    $historyStmt->bind_param("s", $productcode);
    $historyStmt->execute();
    $historyResult = $historyStmt->get_result();

    ob_start();
?>

<div class="page-wrapper theme-aware">
    <div class="content-container">
        <div class="product-view theme-container">
            <!-- Header Section -->
            <div class="view-header">
                <a href="stocklevel.php" class="back-btn theme-btn">
                    <i class='bx bx-arrow-back'></i> Back
                </a>
                <h2>Product Details</h2>
            </div>

            <?php if ($product): ?>
                <div class="product-details">
                    <div class="detail-row">
                        <span class="detail-label">Product Code:</span>
                        <span><?= htmlspecialchars($product['productcode']) ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Product Name:</span>
                        <span><?= htmlspecialchars($product['productname']) ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Category:</span>
                        <span><?= htmlspecialchars($product['productcategory']) ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Weight per piece:</span>
                        <span><?= number_format($product['productweight'], 2) ?> kg</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Price:</span>
                        <span>₱<?= number_format($product['unit_price'], 2) ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Available Quantity:</span>
                        <span><?= number_format($product['availablequantity'] ?? 0) ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">On Hand:</span>
                        <span><?= number_format($product['onhandquantity'] ?? 0) ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Status:</span>
                        <span class="stock-status <?php 
                            if (!$product['availablequantity']) echo 'out-of-stock';
                            else if ($product['availablequantity'] <= 10) echo 'low-stock';
                            else echo 'in-stock';
                        ?>">
                            <?php 
                            if (!$product['availablequantity']) echo 'Out of Stock';
                            else if ($product['availablequantity'] <= 10) echo 'Low Stock';
                            else echo 'In Stock';
                            ?>
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Last Updated:</span>
                        <span><?= $product['dateupdated'] ? date('Y-m-d H:i', strtotime($product['dateupdated'])) : 'Never' ?></span>
                    </div>

                    <div class="stock-history">
                        <h3>Stock Movement History</h3>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Boxes</th>
                                    <th>packs</th>
                                    <th>Weight</th>
                                    <th>Encoder</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($history = $historyResult->fetch_assoc()): ?>
                                <tr>
                                    <td><?= date('Y-m-d', strtotime($history['dateencoded'])) ?></td>
                                    <td><?= number_format($history['numberofbox']) ?></td>
                                    <td><?= number_format($history['totalpacks']) ?></td>
                                    <td><?= number_format($history['totalweight'], 2) ?> kg</td>
                                    <td><?= htmlspecialchars($history['encoder']) ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <div class="error-message theme-error">
                    Product not found.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
    $content = ob_get_clean();
    Page::render($content);
} catch (Exception $e) {
    error_log("Product view error: " . $e->getMessage());
    echo "An error occurred while loading the product details.";
}
?>
