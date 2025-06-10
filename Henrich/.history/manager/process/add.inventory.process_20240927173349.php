<!-- Add Inventory Process -->
<?php
    require '/xampp/htdocs/HenrichProto/DA';

    if (isset($_POST['inventoryID']) && isset($_POST['productCode']) && isset($_POST['productDescription']) && isset($_POST['category']) && isset($_POST['onHand']) && isset($_POST['dateUpdated'])) {
        $inventoryID = $_POST['inventoryID'];
        $productCode = $_POST['productCode'];
        $productDescription = $_POST['productDescription'];
        $category = $_POST['category'];
        $onHand = $_POST['onHand'];
        $dateUpdated = $_POST['dateUpdated'];

        $sql = "INSERT INTO inventory (iid, productcode, productdescription, category, onhand, dateupdated) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssii", $inventoryID, $productCode, $productDescription, $category, $onHand, $dateUpdated);
        if ($stmt->execute()) {
            header("Location: ../manager/add.inventory.php?success");
            exit;
        }
        else {
            echo "Error: " . $stmt->error;
        }
    }
    else {
        echo "Error: No data posted";
    }

