<?php
ob_start();
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

// Remove any output from the dbconnect.php file
ob_end_clean();
$productCode = $_GET["productcode"];

function retrieveProductData($productCode) {
  global $conn;
  $sql = "SELECT productname, productcategory, productweight, productprice, piecesperbox FROM productlist WHERE productcode = ?";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "s", $productCode);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $row = mysqli_fetch_assoc($result);
  return $row;
}

$productData = retrieveProductData($productCode);

echo json_encode($productData);

exit; // exit the script to prevent any further output
?>get the json encoded available quantity value and show it on the readonly input fieldProduct code changed
add.customerorder.php:1401 Checking available quantity for product code: 015
VM8447:1  Uncaught SyntaxError: Unexpected token '<', "<!-- xampp"... is not valid JSON
    at JSON.parse (<anonymous>)
    at xhttp.onreadystatechange (add.customerorder.php:1402:53)
xhttp.onreadystatechange @ add.customerorder.php:1402
XMLHttpRequest.send
(anonymous) @ add.customerorder.php:1411
handleMouseUp_ @ unknown
add.customerorder.php:1411 XHR finished loading: GET "http://localhost/HFC%20MANAGEMENT/Henrich/supervisor/process/check_available_quantity.php?productcode=015".
(anonymous) @ add.customerorder.php:1411
handleMouseUp_ @ unknown
add.customerorder.php:1052 XHR finished loading: GET "http://localhost/HFC%20MANAGEMENT/Henrich/supervisor/process/get_product_info.php?productcode=015".