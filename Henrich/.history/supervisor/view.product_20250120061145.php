<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Get product code from URL
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
    sm.totalpieces,
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
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Product</title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" type="text/css" href="../resources/css/form.css">
    <style>
        .product-details {
            padding: 20px;
            margin: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .detail-row {
            display: flex;
            margin-bottom: 15px;
        }
        .detail-label {
            width: 200px;
            font-weight: bold;
        }
        .stock-history {
            margin-top: 30px;
        }
        .stock-status {
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
        }
        .in-stock { background: #dff0d8; color: #3c763d; }
        .low-stock { background: #fcf8e3; color: #8a6d3b; }
        .out-of-stock { background: #f2dede; color: #a94442; }
    </style>
</head>
<body>
<?php include '../includes/sidebar.php'; ?>
    <?php include '../includes/navbar.php'; ?>
    <section class="panel">
        <div class="container-fluid">
            <div class="table-header">
                <a href="stocklevel.php" class="btn btn-secondary">Back</a>
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
                                <th>Pieces</th>
                                <th>Weight</th>
                                <th>Encoder</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($history = $historyResult->fetch_assoc()): ?>
                            <tr>
                                <td><?= date('Y-m-d', strtotime($history['dateencoded'])) ?></td>
                                <td><?= number_format($history['numberofbox']) ?></td>
                                <td><?= number_format($history['totalpieces']) ?></td>
                                <td><?= number_format($history['totalweight'], 2) ?> kg</td>
                                <td><?= htmlspecialchars($history['encoder']) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php else: ?>
            <div class="alert alert-danger">
                Product not found.
            </div>
            <?php endif; ?>
        </div>
    </section>
</body>
</html>
