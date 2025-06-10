/*************  ✨ Codeium Command 🌟  *************/
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '/xampp/htdocs/HenrichProto/database/dbconnect.php';

// inventory_functions.php
function checkAvailableQuantity($productCode) {
    $conn = $GLOBALS['conn']; // your database connection
    $sql = "SELECT availablequantity FROM inventory WHERE productcode = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productCode);
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


/******  1f7e86a8-24e2-46e8-b807-e7e845bac2c4  *******/