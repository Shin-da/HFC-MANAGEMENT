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
FROM productlist p
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
    <?php include '../reusable/sidebar.php'; ?>
    <section class="panel">
        <?php include '../reusable/navbarNoSearch.html'; ?>
        
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