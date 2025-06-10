<?php
require_once '/xampp/htdocs/HenrichProto/database/dbconnect.php';

// inventory_functions.php
function checkAvailableQuantity($productCode) {
    $conn = // your database connection
    $sql = "SELECT availablequantity FROM inventory WHERE productcode = '$productCode'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['availablequantity'];
    } else {
        return 0;
    }
}

?>