<!-- Add Inventory Process -->
<?php 
    require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

    
    if (isset($_POST['addInventory'])) {
        $iid = $_POST['inventoryID'];
        $productcode = $_POST['productCode'];
        $productdescription = $_POST['productDescription'];
        $category = $_POST['category'];
        $onhand = $_POST['onHand'];
        $dateupdated = $_POST['dateUpdated'];
    }

    if (isset($iid) && isset($productcode) && isset($productdescription) && isset($category) && isset($onhand) && isset($dateupdated)) {

        $sql = "INSERT INTO inventory (iid, productcode, productdescription, category, onhand, dateupdated) VALUES ('$iid', '$productcode', '$productdescription', '$category', '$onhand', '$dateupdated')";
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




