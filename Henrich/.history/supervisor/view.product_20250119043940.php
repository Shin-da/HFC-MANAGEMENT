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