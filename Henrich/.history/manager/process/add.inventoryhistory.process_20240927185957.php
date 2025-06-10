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

foreach ($batchid as $key => $value) {
    $dateofarrival = $dateofarrival[$key];