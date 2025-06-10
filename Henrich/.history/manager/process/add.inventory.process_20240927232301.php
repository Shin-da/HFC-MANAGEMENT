/*************  ✨ Codeium Command 🌟  *************/
<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

$inventoryIDs = $_POST['inventoryID'];
$productCodes = $_POST['productCode'];
$productDescriptions = $_POST['productDescription'];
$categories = $_POST['category'];
$onHands = $_POST['onHand'];
$dateUpdateds = $_POST['dateUpdated'];

$stmt = $conn->prepare("INSERT INTO inventory (iid, productcode, onhand) VALUES (?, ?, ?)");
$stmt = $conn->prepare("INSERT INTO inventory where iid=? and productcode=? (iid, productcode, productdescription, category, onhand, dateupdated) VALUES (?, ?, ?, ?, ?, ?)");

foreach ($inventoryIDs as $key => $inventoryID) {
    $productCode = $productCodes[$key];
    $productDescription = $productDescriptions[$key];
    $category = $categories[$key];
    $onHand = $onHands[$key];
    $dateUpdated = $dateUpdateds[$key];

    $stmt->bind_param("sss", $inventoryID, $productCode, $onHand);
    $stmt->bind_param("ssssss", $inventoryID, $productCode, $productDescription, $category, $onHand, $dateUpdated);
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


/******  b4aae0b0-e45f-4f3a-94c2-5fd5bcc4bd51  *******/