/*************  ✨ Codeium Command 🌟  *************/
<!-- Add Inventory -->
<?php 
    require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

    if (isset($_POST['addInventory'])) {
        $iid = $_POST['inventoryID'];
        $productcode = $_POST['productCode'];
        $productdescription = $_POST['productDescription'];
        $inventoryID = $_POST['inventoryID'];
        $productCode = $_POST['productCode'];
        $productDescription = $_POST['productDescription'];
        $category = $_POST['category'];
        $onhand = $_POST['onHand'];
        $dateupdated = $_POST['dateUpdated'];
        $onHand = $_POST['onHand'];
        $dateUpdated = $_POST['dateUpdated'];
    }

    if (isset($iid) && isset($productcode) && isset($productdescription) && isset($category) && isset($onhand) && isset($dateupdated)) {
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





/******  2b27a3f7-43ef-4f68-84fd-52137dda9079  *******/