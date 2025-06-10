/*************  ✨ Codeium Command ⭐  *************/
<?php
ob_start();
require '../database/dbconnect.php';

// Remove any output from the dbconnect.php file
ob_end_clean();
$productCode = $_GET["productcode"];

$sql = "SELECT SUM(totalweight) as totalweight FROM stockmovement WHERE productcode = ? AND dateencoded >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $productCode);
$stmt->execute();
$result = $stmt->get_result();
$productStock = $result->fetch_assoc();

echo json_encode($productStock);

exit; // exit the script to prevent any further output
?>
/******  dc4fe9ec-dad0-4745-a79a-e9249c2c357d  *******/