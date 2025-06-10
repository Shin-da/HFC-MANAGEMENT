<!-- Add Inventory Process -->
<?php
    require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

 if (isset($_POST['InventoryID']) && isset($_POST['ProductCode']) && isset($_POST['Product_Name']) && isset($_POST['Weight']) && isset($_POST['Available_quantity'])) {
     $inventoryID = $_POST['InventoryID'];
     $productCode = $_POST['ProductCode'];
     $productDescription = $_POST['Product_Name'];
     $category = $_POST['Weight'];
     $onHand = $_POST['Available_quantity'];
     $dateUpdated = ''; // You don't have a dateUpdated field in the form
 

        $sql = "INSERT INTO inventory (iid, productcode, productdescription, category, onhand, dateupdated) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssii", $inventoryID, $productCode, $productDescription, $category, $onHand, $dateUpdated);
        if ($stmt->execute()) {
            header("Location: ../inventory.php?success=Inventory added successfully");
            exit;
        }
        else {
            echo "Error: " . $stmt->error;
        }
    }
    else {
        echo "Error: No data posted";
    }

