<?php
require '/xampp/htdocs/HFC MANAGEMENT/Henrich/database/dbconnect.php';
require './inventory_functions.php';

header('Content-Type: application/json');

$productCode = $_GET['productcode'] ?? '';
$data = checkAvailableQuantity($productCode);
echo json_encode($data);
exit;