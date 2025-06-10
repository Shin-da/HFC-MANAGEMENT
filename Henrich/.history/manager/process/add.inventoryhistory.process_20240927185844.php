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
$sql = "INSERT INTO inventoryhistory (batchid, dateofarrival, encoder, dateencoded, description, datestockin, datestockout, totalboxes, totalweight, totalcost) VALUES ('$batchid', '$dateofarrival', '$encoder', '$dateencoded', '$description', '$datestockin', '$datestockout', '$totalboxes', '$totalweight', '$totalcost')";

for ($i = 0; $i < count($); $i++) {
    $batchid = $batchid[$i];
    $dateofarrival = $dateofarrival[$i];
    $encoder = $encoder[$i];
    $dateencoded = $dateencoded[$i];
    $description = $description[$i];
    $datestockin = $datestockin[$i];
    $datestockout = $datestockout[$i];
    $totalboxes = $totalboxes[$i];
    $totalweight = $totalweight[$i];
    $totalcost = $totalcost[$i];


    if ($conn->query($sql) === TRUE) {
        header("Location: ../add.inventoryhistory.php?success=Inventory Batch Added");
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}