<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

$productCode = $_GET["productcode"];

function retrieveProductStock($productCode) {
    global $conn;
    $sql = "SELECT productcode, onhand FROM inventory WHERE productcode = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $productCode);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row;
}

$productStockData = retrieveProductStock($productCode);

echo json_encode($productStockData);

exit;
?>

run get_product_stock.php when productcode or product name is selected