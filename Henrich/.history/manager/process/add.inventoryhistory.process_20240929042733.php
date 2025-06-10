<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

$batchid = $_POST['batchid'][0];
$productcode = $_POST['productcode'][0];
$quantity = $_POST['quantity'][0];
$weight = $_POST['weight'][0];
$price = $_POST['price'][0];
$dateEncoded = date('Y-m-d H:i:s');

// sum the quantity array and insert into inventoryhistory
$sumQuantity = array_sum($_POST['quantity']);
// sum the weight array and insert into inventoryhistory
$sumWeight = array_sum($_POST['weight']);
// sum the price array and insert into inventoryhistory
$sumPrice = array_sum($_POST['price']);

// Call add.inventoryhistory.process.php to insert data into inventoryhistory table
