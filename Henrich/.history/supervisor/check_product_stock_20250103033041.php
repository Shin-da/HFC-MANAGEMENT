/*************  ✨ Codeium Command ⭐  *************/
<?php
ob_start();
require '../database/dbconnect.php';

// Remove any output from the dbconnect.php file
ob_end_clean();

$productCode = $_GET["productcode"];

$stmt = $conn->prepare("SELECT productname, stock FROM productlist WHERE productcode = ?");
$stmt->bind_param("s", $productCode);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo json_encode($row);

exit; // exit the script to prevent any further output
?>
/******  b02b18de-769e-4a7f-904d-6e4d9fba4b2d  *******/