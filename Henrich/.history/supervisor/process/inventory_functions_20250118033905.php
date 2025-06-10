<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '/xampp/htdocs/HFC MANAGEMENT/Henrich/database/dbconnect.php';

function checkAvailableQuantity($productCode) {
    global $conn;
    $sql = "SELECT availablequantity FROM inventory WHERE productcode = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $productCode);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $availableQuantity = intval($row['availablequantity']);
        $stockStatus = $availableQuantity > 5 ? "In stock!" : ($availableQuantity > 0 ? "Low stock!" : "Out of stock!");
        return array(
            'availablequantity' => $availableQuantity,
            'stockstatus' => $stockStatus
        );
    }
    return array('availablequantity' => 0, 'stockstatus' => "Out of stock!");
}
?>

