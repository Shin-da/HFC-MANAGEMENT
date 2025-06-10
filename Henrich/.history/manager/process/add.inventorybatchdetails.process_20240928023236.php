/*************  ✨ Codeium Command 🌟  *************/
<!-- add.inventorybatchdetails.process -->
<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

$ibdids = $_POST['ibdid'] ?? [];
$batchids = $_POST['batchid'] ?? [];
$productCodes = $_POST['productCode'] ?? [];
$quantities = $_POST['quantity'] ?? [];
$weights = $_POST['weight'] ?? [];
$prices = $_POST['price'] ?? [];
$ibdids = $_POST['ibdid']; 
$batchids = $_POST['batchid'];
$productCodes = $_POST['productCode'];
$quantities = $_POST['quantity'] ;
$weights = $_POST['weight'];
$prices = $_POST['price'];

$stmt = $conn->prepare("INSERT INTO inventorybatchdetails (ibdid, batchid, productcode, quantity, weight, price) VALUES (?, ?, ?, ?, ?, ?)");
$stmt = $conn->prepare("INSERT INTO inventorybatchdetails where ibdid=? and batchid=? (ibdid, batchid, productcode, quantity, weight, price) VALUES (?, ?, ?, ?, ?, ?)");

foreach ($ibdids as $key => $ibdid) {
    $batchid = $batchids[$key] ?? '';
    $productCode = $productCodes[$key] ?? '';
    $quantity = $quantities[$key] ?? 0;
    $weight = $weights[$key] ?? 0;
    $price = $prices[$key] ?? 0;
    $batchid = $batchids[$key];
    $productCode = $productCodes[$key];
    $quantity = $quantities[$key];
    $weight = $weights[$key];
    $price = $prices[$key];

    $stmt->bind_param("isiiid", $ibdid, $batchid, $productCode, $quantity, $weight, $price);

    if (!$stmt->execute()) {
        echo "Error inserting data: " . $stmt->error;
        exit();
    }
}

$stmt->close();
$conn->close();

header('Location: /manager/inventory.php');
exit();

/******  8125d941-2c7e-49df-9ab5-01fbcff73863  *******/