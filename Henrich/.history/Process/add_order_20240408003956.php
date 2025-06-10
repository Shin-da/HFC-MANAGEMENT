<!-- This file will add an order -->
<?php
include '../database/dbconnect.php';

$OrderID = $_POST["OrderID"];
$Lname = $_POST["Lname"];
$Fname = $_POST["Fname"];
$ProductCode = $_POST["ProductCode"];
$Quantity = $_POST["Quantity"];
$Price = $_POST["Price"];

if (isset($_POST["Datetime"])) {
    $Datetime = date("Y-m-d H:i:s", strtotime($_POST["Datetime"]));
} else {
    $Datetime = date("Y-m-d H:i:s");
}

$sql_inv = "SELECT Available_quantity FROM inventory WHERE ProductCode = '$ProductCode'";
$result_inv = $conn->query($sql_inv);

if ($result_inv->num_rows > 0) {
    // output data of each row
    while ($row_inv = $result_inv->fetch_assoc()) {
        $InvQuantity = $row_inv["Available_quantity"];
    }
    if ($InvQuantity <= 0) {
        echo '<script>alert("Can\'t add order because stock is 0");</script>';
        exit();
    }
} else {
    echo "0 results";
}



$sql = "INSERT INTO tblorders (OrderID, Datetime, Lname, Fname, ProductCode, Quantity, Price) values ('$OrderID', '$Datetime', '$Lname', '$Fname', '$ProductCode', '$Quantity', '$Price')";

if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

include 'subtract_inventory.php';
?>