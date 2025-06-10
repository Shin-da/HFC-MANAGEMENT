<!-- add.inventoryhistory.process -->
<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

$batchids = $_POST['batchid'];
$dateofarrivals = $_POST['dateofarrival'];
$encoders = $_POST['encoder'];
$dateencodeds = $_POST['dateencoded'];
$descriptions = $_POST['description'];
$datestockins = $_POST['datestockin'];
$datestockouts = $_POST['datestockout'];
$totalboxess = $_POST['totalboxes'];
$totalweights = $_POST['totalweight'];
$totalcosts = $_POST['totalcost'];

$stmt = $conn->prepare( "INSERT INTO  inventoryhistory (batchid, dateofarrival, encoder, dateencoded, description, datestockin, datestockout, totalboxes, totalweight, totalcost) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)" );

foreach ($batchids as $key => $batchid) {
    $dateofarrival = $dateofarrivals[$key];
    $encoder = $encoders[$key];
    $dateencoded = $dateencodeds[$key];
    $description = $descriptions[$key];
    $datestockin = $datestockins[$key];
    $datestockout = $datestockouts[$key];
    $totalboxes = $totalboxess[$key];
    $totalweight = $totalweights[$key];
    $totalcost = $totalcosts[$key];

    $stmt->bind_param("ssssssssss", $value, $dateofarrival, $encoder, $dateencoded, $description, $datestockin, $datestockout, $totalboxes, $totalweight, $totalcost);  
    $stmt->execute();
}



$stmt->close();
$conn->close();
