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