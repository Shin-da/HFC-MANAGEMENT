<?php
ob_clean();
require '/xampp/htdocs/HFC MANAGEMENT/Henrich/database/dbconnect.php';
require './inventory_functions.php';

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

$productCode = $_GET['productcode'] ?? '';
$data = checkAvailableQuantity($productCode);
echo json_encode($data);
exit;