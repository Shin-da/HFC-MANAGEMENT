<?php 
    require '../database/dbconnect.php';
    require '../session/session.php';
    require '../session/manager_session.php';
    require '../reusable/redirect404.php';

    if (isset($_POST['submit'])) {
        $ProductCode = $_POST['ProductCode'];
        $Product_Name = $_POST['Product_Name'];
        $Quantity = $_POST['Quantity'];
        $Weight = $_POST['Weight'];
        $Price = $_POST['Price'];
        $Available_quantity = $_POST['Available_quantity'];
        $InventoryID = $_POST['InventoryID'];
    }

    if (isset($ProductCode) && isset($Product_Name) && isset($Quantity) && isset($Weight) && isset($Price) && isset($Available_quantity) && isset($InventoryID)) {

        $sql = "INSERT INTO Inventory (InventoryID, ProductCode, Product_Name, Quantity, Weight, Price, Available_quantity) VALUES ('$InventoryID', '$ProductCode', '$Product_Name', '$Quantity', '$Weight', '$Price', '$Available_quantity')";
        $result = $conn->query($sql);
    }
    else {
        echo "Error: " . $sql . "<br>" . $conn->error;
?>