<!-- Add Inventory -->
<?php 
    require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

    if (isset($_POST['addInventory'])) {
        $inventoryID = $_POST['inventoryID'];
        $productCode = $_POST['productCode'];
        $productDescription = $_POST['productDescription'];
        $category = $_POST['category'];
        $onHand = $_POST['onHand'];
        $dateUpdated = $_POST['dateUpdated'];
    }

    if (isset($inventoryID) && isset($productCode) && isset($productDescription) && isset($category) && isset($onHand) && isset($dateUpdated)) {

        $sql = "INSERT INTO inventory (inventoryID, productCode, productDescription, category, onHand, dateUpdated) VALUES ('$inventoryID', '$productCode', '$productDescription', '$category', '$onHand', '$dateUpdated')";
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




