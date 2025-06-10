<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

$inventoryIDs = $_POST['inventoryID'];
$productCodes = $_POST['productCode'];
$onHands = $_POST['onHand'];

$stmt = $conn->prepare("INSERT INTO inventory (iid, productcode, onhand) VALUES (?, ?, ?)");

foreach ($inventoryIDs as $key => $inventoryID) {
    $productCode = $productCodes[$key];
    $onHand = $onHands[$key];

    $stmt->bind_param("sss", $inventoryID, $productCode, $onHand);
    if (!$stmt->execute()) {
        // Handle error here, for example: redirect to an error page.
        header('Location: /error_page.php'); // Specify the error page
        exit();
    }
}

$stmt->close();
$conn->close();

// Redirect to inventory page after all operations are done.
header('Location: /add.inventory.php?success=Inventory added successfully'); // Specify the success page
exit();

