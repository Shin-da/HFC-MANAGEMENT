<!-- add.inventoryhistory.process -->
<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

$batchid = $_POST['batchid'];
$dateofarrival = $_POST['dateofarrival'];
$encoder = $_POST['encoder'];
$dateencoded = $_POST['dateencoded'];
$description = $_POST['description'];
$datestockin = $_POST['datestockin'];
$datestockout = $_POST['datestockout'];
$totalboxes = $_POST['totalboxes'];
$totalweight = $_POST['totalweight'];
$totalcost = $_POST['totalcost'];

$stmt = $conn->prepare( "INSERT INTO inventoryhistory (batchid, dateofarrival, encoder, dateencoded, description, datestockin, datestockout, totalboxes, totalweight, totalcost) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)" );

foreach ($batchids as $key => $) {
    $dateofarrival = $dateofarrival[$key];
    $encoder = $encoder[$key];
    $dateencoded = $dateencoded[$key];
    $description = $description[$key];
    $datestockin = $datestockin[$key];
    $datestockout = $datestockout[$key];
    $totalboxes = $totalboxes[$key];
    $totalweight = $totalweight[$key];
    $totalcost = $totalcost[$key];

    $stmt->bind_param("ssssssssss", $value, $dateofarrival, $encoder, $dateencoded, $description, $datestockin, $datestockout, $totalboxes, $totalweight, $totalcost);  
    $stmt->execute();
}

$stmt->close();
$conn->close();
