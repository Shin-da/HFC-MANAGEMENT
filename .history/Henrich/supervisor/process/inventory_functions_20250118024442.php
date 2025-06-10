<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '/xampp/htdocs/HenrichProto/database/dbconnect.php';

// inventory_functions.php
function checkAvailableQuantity($productCode) {
    $conn = $GLOBALS['conn']; // your database connection
    $sql = "SELECT availablequantity FROM inventory WHERE productcode = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $productCode);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $availableQuantity = $row['availablequantity'];
        $stockStatus = $availableQuantity > 5 ? "In stock!" : "Low stock!";
        return array('availablequantity' => $availableQuantity, 'stockstatus' => $stockStatus);
    } else {
        return array('availablequantity' => 0, 'stockstatus' => "Out of stock!");
    }
}

?>

