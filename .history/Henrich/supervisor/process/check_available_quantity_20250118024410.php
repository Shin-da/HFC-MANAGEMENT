<?php
require '/xampp/htdocs/HFC MANAGEMENT/Henrich/database/dbconnect.php';
require './inventory_functions.php';

$productCode = $_GET['productcode'];
$data = checkAvailableQuantity($productCode);
header('Content-Type: application/json');
echo json_encode($data);
?>

