<!-- add.inventoryhistory.process -->
<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

$batchid = $_POST['batchid'];
$productCode = $_POST['productCode'];
$productDescription = $_POST['productDescription'];
$categories = $_POST['category'];
$onHand = $_POST['onHand'];
$dateUpdated = $_POST['dateUpdated'];