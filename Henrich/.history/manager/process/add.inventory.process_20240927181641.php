<?php
    require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

    $inventoryIDs = $_POST['inventoryID'];
    $productCodes = $_POST['productCode'];
    $productDescriptions = $_POST['productDescription'];
    $categories = $_POST['category'];
    $onHands = $_POST['onHand'];
    $dateUpdateds = $_POST['dateUpdated'];
    
    $stmt = $conn->prepare("INSERT INTO inventory (inventoryID, productCode, productDescription, category, onHand, dateUpdated) VALUES (?, ?, ?, ?, ?, ?)");
    
    foreach ($inventoryIDs as $key => $inventoryID) {
        $productCode = $productCodes[$key];
        $productDescription = $productDescriptions[$key];
        $category = $categories[$key];
        $onHand = $onHands[$key];
        $dateUpdated = $dateUpdateds[$key];
    
        $stmt->bind_param("ssssss", $inventoryID, $productCode, $productDescription, $category, $onHand, $dateUpdated);
        $stmt->execute();
    }

    $stmt->close();
    $conn->close();
?>
