<?php
require '/xampp/htdocs/HFC MANAGEMENT/Henrich/database/dbconnect.php';
require './inventory_functions.php';

$productCode = $_GET['productcode'];
echo "Checking available quantity for product code: " . $productCode . "\n";

$data = checkAvailableQuantity($productCode);
echo json_encode($data);
?>

