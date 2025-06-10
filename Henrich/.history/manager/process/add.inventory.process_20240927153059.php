/*************  ✨ Codeium Command 🌟  *************/
<!-- Add Customer Order -->
<!-- Add Inventory -->
<?php 
    require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

    if (isset($_POST['addCustomerOrder'])) {
        $customerName = $_POST['customerName'];
        $customerEmail = $_POST['customerEmail'];
        $customerAddress = $_POST['customerAddress'];
        $customerPhone = $_POST['customerPhone'];
        $orderName = $_POST['orderName'];
        $quantityType = $_POST['quantityType'];
        $quantity = $_POST['quantity'];
        $price = $_POST['price'];
        $date = $_POST['date'];
        $time = $_POST['time'];
        $description = $_POST['description'];
        $status = $_POST['status'];
    if (isset($_POST['submit'])) {
        $ProductCode = $_POST['ProductCode'];
        $Product_Name = $_POST['Product_Name'];
        $Quantity = $_POST['Quantity'];
        $Weight = $_POST['Weight'];
        $Price = $_POST['Price'];
        $Available_quantity = $_POST['Available_quantity'];
        $InventoryID = $_POST['InventoryID'];
    }

    if (isset($customerName) && isset($customerEmail) && isset($customerAddress) && isset($customerPhone) && isset($orderName) && isset($quantityType) && isset($quantity) && isset($price) && isset($date) && isset($time) && isset($description) && isset($status)) {
    if (isset($ProductCode) && isset($Product_Name) && isset($Quantity) && isset($Weight) && isset($Price) && isset($Available_quantity) && isset($InventoryID)) {

        $sql = "INSERT INTO Inventory (InventoryID, ProductCode, Product_Name, Quantity, Weight, Price, Available_quantity) VALUES ('$InventoryID', '$ProductCode', '$Product_Name', '$Quantity', '$Weight', '$Price', '$Available_quantity')";
        if ($conn->query($sql)) {
            header("Location: ../manager/add.inventory.php?success");
            exit;
        }
        else {
            echo "Error: 1" . $conn->error;
        }
    }
    else {
        echo "Error:2" . $conn->error;
    }



/******  4251a362-de5e-4dee-8d98-f195f20f464f  *******/