/*************  ✨ Codeium Command 🌟  *************/
<?php
ob_start();
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

// Remove any output from the dbconnect.php file
ob_end_clean();
$searchQuery = $_GET['search']; 
$productCode = $_GET["productcode"];

function retrieveProductData($searchQuery) {
function retrieveProductData($productCode) {
  global $conn;
  $sql = "SELECT * FROM products WHERE productname LIKE '%$searchQuery%' OR productid LIKE '%$searchQuery%'";
  $result = $conn->query($sql);
  $products = array();
  while ($row = $result->fetch_assoc()) {
    $products[] = $row;
  }
  return $products;
  $sql = "SELECT productname, productcategory, productweight, productprice, piecesperbox FROM productlist WHERE productcode = ?";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "s", $productCode);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $row = mysqli_fetch_assoc($result);
  return $row;
}

$productData = retrieveProductData($searchQuery);
$productData = retrieveProductData($productCode);

echo json_encode($productData);

exit; // exit the script to prevent any further output
?>
/******  a87023cf-8ae4-4fd9-a92b-4e713ef6a6cb  *******/