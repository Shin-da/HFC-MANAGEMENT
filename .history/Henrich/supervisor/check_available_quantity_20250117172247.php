<?php
// check_available_quantity.php
require 'inventory_functions.php';

if (isset($_GET['productcode']) && !empty($_GET['productcode'])) {
    $productCode = $_GET['productcode'];
    $availableQuantity = checkAvailableQuantity($productCode);
    header('Content-Type: application/json');
    echo json_encode(array('availablequantity' => $availableQuantity));
} else {
    header('Content-Type: application/json');
    echo json_encode(array('error' => 'Product code is required'));
}
?>