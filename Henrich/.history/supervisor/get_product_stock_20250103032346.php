<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';


// Remove any output from the dbconnect.php file
ob_end_clean();
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

