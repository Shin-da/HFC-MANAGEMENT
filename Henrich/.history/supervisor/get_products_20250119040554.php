<?php
require '../database/dbconnect.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT productcode, productname, productcategory 
        FROM products 
        WHERE productcode LIKE ? OR productname LIKE ?
        LIMIT 10";

$searchTerm = "%$search%";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

header('Content-Type: application/json');
echo json_encode($products);
