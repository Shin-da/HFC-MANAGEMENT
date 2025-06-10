<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

// indicator that add.stockmovement.process.php is running
echo "Running add.stockactivitylog.process.php";

$batchid = $_POST['batchid'][0];
$dateofarrival = $_POST['dateofarrival'];
$encoder =  'manager';
$description = " Stock In";
$datestockin = $dateencoded;
$weight = $_POST['weight'];
$price = $_POST['price'];
$dateencoded = date('Y-m-d');


// Calculate total boxes, total weight, and total cost for each batch
print_r($_POST); // Add this line to see what data is being submitted

$totalboxes = 0;
$totalweight = 0;
$totalcost = 0;

foreach ($_POST['quantity'] as $key => $quantity) {
    echo "Key: $key, Quantity: $quantity, Weight: " . $_POST['weight'][$key] . ", Price: " . $_POST['price'][$key] . "<br>"; // Add this line to see the values being used in the calculation
    $totalboxes += $quantity;
    $totalweight += $_POST['weight'][$key];
    $totalcost += $_POST['price'][$key];
}

echo "Total Boxes: $totalboxes, Total Weight: $totalweight, Total Cost: $totalcost"; // Add this line to see the final calculated values

// Check if batchid already exists in stockmovement table
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM stockactivitylog WHERE batchid = ?");
$stmt->bind_param("s", $batchid);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['count'] > 0) {
    // Batchid already exists, update the existing row
    $stmt = $conn->prepare("UPDATE stockactivitylog SET totalboxes = ?, totalweight = ?, totalcost = ?, dateencoded = ? WHERE batchid = ?");
    $stmt->bind_param("iiiss", $totalboxes, $totalweight, $totalcost, $dateencoded, $batchid);
    if (!$stmt->execute()) {
        echo "Error updating data in stockactivitylog table: " . $stmt->error;
        exit;
    } else {
        echo "Data updated successfully in stockactivitylog table!";
    }
} else {
    // Batchid does not exist, insert a new row
    $stmt = $conn->prepare("INSERT INTO stockactivitylog (batchid, totalboxes, totalweight, totalcost, dateencoded) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiss", $batchid, $totalboxes, $totalweight, $totalcost, $dateencoded);
    if (!$stmt->execute()) {
        echo "Error inserting data into stockactivitylog table: " . $stmt->error;
        exit;
    } else {
        echo "Data inserted successfully into  table!";
    }
}

// call update.inventory.process.php
require 'update.inventory.process.php';
?>