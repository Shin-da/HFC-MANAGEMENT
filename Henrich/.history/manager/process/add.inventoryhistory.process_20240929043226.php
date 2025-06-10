<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

$batchid = $_POST['batchid'][0];
$productcode = $_POST['productcode'][0];
$totalquantity = $_POST['totalquantity'][0];
$totalweight = $_POST['totalweight'][0];
$totalprice = $_POST['totalprice'][0];
$dateencoded = $_POST['dateencoded'][0];
// sum the quantity array and insert into inventoryhistory
$sumQuantity = array_sum($_POST['totalquantity']);
// sum the weight array and insert into inventoryhistory
$sumWeight = array_sum($_POST['totalweight']);
// sum the price array and insert into inventoryhistory
$sumPrice = array_sum($_POST['totalprice']);

