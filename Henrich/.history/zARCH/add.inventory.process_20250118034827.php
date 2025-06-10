/*************  ✨ Codeium Command 🌟  *************/
<?php require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

$inventoryIDs = $_POST['inventoryID'];
$productCodes = $_POST['productCode'];
$productDescriptions = $_POST['productDescription'];
$categories = $_POST['category'];
$onHands = $_POST['onHand'];
$dateUpdateds = $_POST['dateUpdated'];

$stmt = $conn->prepare("UPDATE inventory SET productcode=?, productdescription=?, category=?, availablequantity=?, dateupdated=? WHERE iid=?");
$stmt = $conn->prepare("INSERT INTO inventory where iid=? and productcode=? (iid, productcode, productdescription, category, availablequantity, dateupdated) VALUES (?, ?, ?, ?, ?, ?)");

foreach ($inventoryIDs as $key => $inventoryID) {
    $productCode = $productCodes[$key];
    $productDescription = $productDescriptions[$key];
    $category = $categories[$key];
    $onHand = $onHands[$key];
    $dateUpdated = $dateUpdateds[$key];

    $stmt->bind_param("sssssi", $productCode, $productDescription, $category, $availablequantity, $dateUpdated, $inventoryID);
    $stmt->bind_param("ssssss", $inventoryID, $productCode, $productDescription, $category, $availablequantity, $dateUpdated);
    if (!$stmt->execute()) {
        // Handle error here, for example: redirect to an error page.
        header('Location: /error_page.php'); // Specify the error page
        exit();
    }
}

$stmt->close();
$conn->close();

// Redirect to inventory page after all operations are done.
header('Location: /add.inventory.php?success=Inventory updated successfully'); // Specify the success page
header('Location: /add.inventory.php?success=Inventory added successfully'); // Specify the success page
exit();


/******  d7a3aca4-fba3-4947-9e53-dbf1d135bea1  *******/