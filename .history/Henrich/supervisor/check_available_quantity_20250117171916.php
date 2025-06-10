<?php
// check_available_quantity.php
require 'inventory_functions.php';

$productCode = $_GET['productcode'];
$availableQuantity = checkAvailableQuantity($productCode);

header('Content-Type: application/json');
echo json_encode(array('availablequantity' => $availableQuantity));
?>