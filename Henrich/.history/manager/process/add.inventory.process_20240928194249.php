<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

$inventoryIDs = $_POST['inventoryID'];
$productCodes = $_POST['productCode'];
$productDescriptions = $_POST['productDescription'];
$categories = $_POST['category'];
$onHands = $_POST['onHand'];
$dateUpdateds = $_POST['dateUpdated'];

$stmt = $conn->prepare("INSERT INTO inventory where iid=? and productcode=? (iid, productcode, productdescription, category, onhand, dateupdated) VALUES (?, ?, ?, ?, ?, ?)");

foreach ($inventoryids as $key => $inventoryid) {
    $productCode = $productCodes[$key];
    $productdescription = $productdescriptions[$key];
    $category = $categories[$key];
    $onhand = $onhands[$key];
    $dateUpdated = $dateupdateds[$key];

    $stmt->bind_param("ssssss", $inventoryID, $productcode, $productDescription, $category, $onhand, $dateupdated);
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

