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
?>