
<?php
include '../database/dbconnect.php';

echo '<p> InventoryID:' . $InventoryID = $_POST['InventoryID']; '</p>';
echo '<br <p> Available Quantity:' .  $Available_quantity = $_POST["Available_quantity"];
'</p> ';


$sql = "SELECT * FROM inventory WHERE InventoryID = '$InventoryID'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    $row = $result->fetch_assoc();
    $Available_quantity_db = $row["Available_quantity"];
    $new_quantity = $Available_quantity + $Available_quantity_db;
    $sql = "UPDATE Inventory SET  Available_quantity = '$new_quantity' WHERE InventoryID = '$InventoryID'";
    if ($conn->query($sql) === TRUE) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

else {
    echo "0 results";
}



?>

