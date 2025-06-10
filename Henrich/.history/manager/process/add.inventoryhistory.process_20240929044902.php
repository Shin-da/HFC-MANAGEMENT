<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

$batchid = $_POST['batchid'];
$quantity = $_POST['quantity'];
$weight = $_POST['weight'];
$price = $_POST['price'];
$dateencoded = date('Y-m-d');

$totalboxes = 0;
$totalweight = 0;
$total; = 0;

if (isset($_POST['quantity']) && isset($_POST['weight']) && isset($_POST['price'])) {
    $quantity = $_POST['quantity'];
    $weight = $_POST['weight'];
    $price = $_POST['price'];

    $count = count($quantity);
    for ($i = 0; $i < $count; $i++) {
        $totalboxes += $quantity[$i];
        $totalweight += $weight[$i];
        $totalprice += $price[$i];
    }
}

print "$totalboxes, $totalweight, $totalprice";

$stmt = $conn->prepare("INSERT INTO inventoryhistory (batchid, totalboxes, totalweight, totalprice, dateencoded) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iiiii", $batchid, $totalboxes, $totalweight, $totalprice, $dateencoded);
if (!$stmt->execute()) {
    echo "Error inserting data into inventoryhistory table: " . $stmt->error;
    exit;
} else {
    echo "Data inserted successfully into inventoryhistory table!";
}
?>