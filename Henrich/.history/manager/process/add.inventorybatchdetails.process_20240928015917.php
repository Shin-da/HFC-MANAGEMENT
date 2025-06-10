/*************  ✨ Codeium Command 🌟  *************/
<!-- add.inventorybatchdetails.process -->
<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

// Prepare and bind
if (isset($_POST['ibdid'])) {
    $ibdids = $_POST['ibdid'];
    $batchids = $_POST['batchid'] ?? [];
    $productCodes = $_POST['productCode'] ?? [];
    $quantities = $_POST['quantity'] ?? [];
    $weights = $_POST['weight'] ?? [];
    $prices = $_POST['price'] ?? [];
$ibdids = $_POST['ibdid'];
$batchids = $_POST['batchid'];
$productCodes = $_POST['productCode'];
$quantities = $_POST['quantity'];
$weights = $_POST['weight'];
$prices = $_POST['price'];

    $stmt = $conn->prepare("INSERT INTO inventorybatchdetails (ibdid, batchid, productcode, quantity, weight, price) VALUES (?, ?, ?, ?, ?, ?)");
$stmt = $conn->prepare( "INSERT INTO inventorybatchdetails where ibdid=? and productcode=? (ibdid, productcode, quantity, weight, price) VALUES (?, ?, ?, ?, ?)");

    foreach ($batchids as $key => $batchid) {
        $productCode = $productCodes[$key] ?? '';
        $quantity = $quantities[$key] ?? 0;
        $weight = $weights[$key] ?? 0;
        $price = $prices[$key] ?? 0;
foreach ($batchids as $key => $batchid) {
    $productCode = $productCodes[$key];
    $quantity = $quantities[$key];
    $weight = $weights[$key];
    $price = $prices[$key];

        $stmt->bind_param("isssss", $ibdids[$key], $batchid, $productCode, $quantity, $weight, $price);
        if (!$stmt->execute()) {
            // Handle error here, for example: redirect to an error page.
            header('Location: /error_page.php'); // Specify the error page
            exit();
        }
    $stmt->bind_param("ssssss", $batchid, $productCode, $quantity, $weight, $price);
    if (!$stmt->execute()) {
        // Handle error here, for example: redirect to an error page.
        header('Location: /error_page.php'); // Specify the error page
        exit();
    }
}



$stmt->close();
$conn->close();

/******  e1a4da51-c98e-4fd4-9f3a-5de86707a8b5  *******/