<?php
Product code changed
add.customerorder.php:1399 Ready state changed
3add.customerorder.php:1399 Ready state changed
add.customerorder.php:1405 Error parsing response: SyntaxError: Unexpected token '<', "<br />
<b>"... is not valid JSON
add.customerorder.php:1407 Response received
VM2746:1  Uncaught SyntaxError: Unexpected token '<', "<br />
<b>"... is not valid JSON
    at JSON.parse (<anonymous>)
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

