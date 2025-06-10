/*************  ✨ Codeium Command 🌟  *************/
<!-- Add Inventory Process -->
<?php
    require '../database/dbconnect.php';
    require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

    if (isset($_POST['inventoryID']) && isset($_POST['productCode']) && isset($_POST['productDescription']) && isset($_POST['category']) && isset($_POST['onHand']) && isset($_POST['dateUpdated'])) {
        $inventoryID = $_POST['inventoryID'];
        $productCode = $_POST['productCode'];
        $productDescription = $_POST['productDescription'];
        $category = $_POST['category'];
        $onHand = $_POST['onHand'];
        $dateUpdated = $_POST['dateUpdated'];
    } else {
        header("Location: ../add.inventory.php?error=No data posted");
        exit;
        $dateUpdated = $_POST['dateUpdated' ];
    }

    $sql = "INSERT INTO Inventory (iid, productCode, productDescription, category, onHand, dateUpdated) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $inventoryID, $productCode, $productDescription, $category, $onHand, $dateUpdated);
    if ($stmt->execute()) {
        header("Location: ../inventory.php?success=Inventory added successfully");
        exit;
    } else {
        echo "Error: " . $stmt->error;
        $sql = "INSERT INTO Inventory (iid, productCode, productDescription, category, onHand, dateUpdated) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $inventoryID, $productCode, $productDescription, $category, $onHand, $dateUpdated);
        if ($stmt->execute()) {
            header("Location: ../inventory.php?success=Inventory added successfully");
            exit;
        }
        else {
            echo "Error: " . $stmt->error;
        }else {
        echo "Error: No data posted";
    }

    $conn->close();
?>
/******  b6f6325b-8f2f-4108-9c2c-788e13fccc42  *******/