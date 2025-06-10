<?php
include '../database/dbconnect.php';

$ProductCode = $_POST['ProductCode'];
$Quantity = $_POST['Quantity'];

$sql = "SELECT Available_quantity FROM Inventory WHERE ProductCode = '$ProductCode'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

$availableQuantity = (int)$row['Available_quantity'];

$newAvailableQuantity = $availableQuantity - $Quantity;

$updateSql = "UPDATE Inventory SET Available_quantity = '$newAvailableQuantity' WHERE ProductCode = '$ProductCode'";

if ($conn->query($updateSql) === TRUE) {
    echo "Quantity updated successfully";
} else {
    echo "Error updating quantity: " . $conn->error;
}

$conn->close();
?>
