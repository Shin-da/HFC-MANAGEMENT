<?php

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
        return $row['availablequantity'];
    } else {
        return 0;
    }
}

?>

