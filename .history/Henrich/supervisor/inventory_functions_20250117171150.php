/*************  ✨ Codeium Command 🌟  *************/
<?php
require_once '/xampp/htdocs/HenrichProto/database/dbconnect.php';

// inventory_functions.php
function checkAvailableQuantity($productCode) {
    global $conn;
    $sql = "SELECT availablequantity FROM inventory WHERE productcode = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $productCode);
    $stmt->execute();
    $result = $stmt->get_result();
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
/******  96523f68-94ef-432b-80d7-6384efbd282f  *******/