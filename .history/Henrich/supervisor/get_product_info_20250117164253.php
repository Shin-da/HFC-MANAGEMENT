<?php
ob_start();
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

// Remove any output from the dbconnect.php file
ob_end_clean();
$searchQuery = $_GET['search']; 

function retrieveProductData($searchQuery) {
  global $conn;
  $sql = "SELECT * FROM products WHERE productname LIKE '%$searchQuery%' OR productid LIKE '%$searchQuery%'";
  $result = $conn->query($sql);
  $products = array();
  while ($row = $result->fetch_assoc()) {
    $products[] = $row;
  }
  return $products;
}

$productData = retrieveProductData($searchQuery);

echo json_encode($productData);

exit; // exit the script to prevent any further output
?>
