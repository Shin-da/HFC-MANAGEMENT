<?php
$productCode = $_GET["productcode"];
// Assuming you have a database connection and a function to retrieve product data
$productData = retrieveProductData($productCode);
echo json_encode($productData);
?>