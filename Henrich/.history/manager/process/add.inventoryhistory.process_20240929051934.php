<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

// indicator that add.inventoryhistory.process.php is running
echo "Running add.inventoryhistory.process.php";

$batchid = $_POST['batchid'][0];
$dateofarrival = date('Y-m-d');
$encoder = $session->role;
$description = $_POST['description'];
$quantity = $_POST['quantity'];
$weight = $_POST['weight'];
$price = $_POST['price'];
$dateencoded = date('Y-m-d');

$totalboxes = 0;
$totalweight = 0;
$totalcost = 0;

if (isset($_POST['quantity']) && isset($_POST['weight']) && isset($_POST['price'])) {
    $quantity = $_POST['quantity'];
    $weight = $_POST['weight'];
    $price = $_POST['price'];

    $count = count($quantity);
    for ($i = 0; $i < $count; $i++) {
        $totalboxes += $quantity[$i];
        $totalweight += $weight[$i];
        $totalcost += $price[$i];
    }
}

print "$totalboxes, $totalweight, $totalcost";

// Check if batchid already exists in inventoryhistory table
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM inventoryhistory WHERE batchid = ?");
$stmt->bind_param("s", $batchid);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['count'] > 0) {
    // Batchid already exists, update the existing row
    $stmt = $conn->prepare("UPDATE inventoryhistory SET totalboxes = ?, totalweight = ?, totalcost = ?, dateencoded = ? WHERE batchid = ?");
    $stmt->bind_param("iiiss", $totalboxes, $totalweight, $totalcost, $dateencoded, $batchid);
    if (!$stmt->execute()) {
        echo "Error updating data in inventoryhistory table: " . $stmt->error;
        exit;
    } else {
        echo "Data updated successfully in inventoryhistory table!";
    }
} else {
    // Batchid does not exist, insert a new row
    $stmt = $conn->prepare("INSERT INTO inventoryhistory (batchid, totalboxes, totalweight, totalcost, dateencoded) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiss", $batchid, $totalboxes, $totalweight, $totalcost, $dateencoded);
    if (!$stmt->execute()) {
        echo "Error inserting data into inventoryhistory table: " . $stmt->error;
        exit;
    } else {
        echo "Data inserted successfully into inventoryhistory table!";
    }
}
?>