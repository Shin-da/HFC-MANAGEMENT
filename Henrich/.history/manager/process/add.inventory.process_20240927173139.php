/*************  ✨ Codeium Command 🌟  *************/
<!-- Add Inventory Process -->
<?php
    require '../database/dbconnect.php';
<?php 
    require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

    if (isset($_POST['inventoryID']) && isset($_POST['productCode']) && isset($_POST['productDescription']) && isset($_POST['category']) && isset($_POST['onHand']) && isset($_POST['dateUpdated'])) {
        $inventoryID = $_POST['inventoryID'];
        $productCode = $_POST['productCode'];
        $productDescription = $_POST['productDescription'];
        $category = $_POST['category'];
        $onHand = $_POST['onHand'];
        $dateUpdated = $_POST['dateUpdated'];
    } 

        $sql = "INSERT INTO inventory (iid, productcode, productdescription, category, onhand, dateupdated) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssii", $inventoryID, $productCode, $productDescription, $category, $onHand, $dateUpdated);
        if ($stmt->execute()) {
    if (isset($iid) && isset($productcode) && isset($productdescription) && isset($category) && isset($onhand) && isset($dateupdated)) {
        $sql = "INSERT INTO inventory (iid, productcode, productdescription, category, onhand, dateupdated) VALUES ('$iid', '$productcode', '$productdescription', '$category', '$onhand', '$dateupdated')";
        if ($conn->query($sql)) {
            header("Location: ../manager/add.inventory.php?success");
            exit;
        }
        else {
            echo "Error: " . $stmt->error;
            echo "Error: 1" . $conn->error;
        }
    }
    else {
        echo "Error: No data posted";
        echo "Error:2" . $conn->error;
    }





/******  82b61454-de2e-46fd-b6fe-265612ab70ed  *******/