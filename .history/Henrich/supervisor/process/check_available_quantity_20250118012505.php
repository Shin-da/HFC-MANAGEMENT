/*************  ✨ Codeium Command 🌟  *************/
<?php
require '/xampp/htdocs/HFC MANAGEMENT/Henrich/database/dbconnect.php';

$productCode = $_GET['productcode'];
echo "Checking available quantity for product code: " . $productCode . "\n";

$sql = "SELECT availablequantity FROM inventory WHERE productcode = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $productCode);
$stmt->execute();

$result = $stmt->get_result();
echo "Query executed. Number of rows returned: " . $result->num_rows . "\n";

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $availableQuantity = $row['availablequantity'];
    echo "Available quantity: " . $availableQuantity . "\n";
    $stockStatus = "Low stock!";
    if ($availableQuantity > 5) {
        $stockStatus = "In stock!";
    }
    echo "Stock status: " . $stockStatus . "\n";

    $data = array('availablequantity' => $availableQuantity, 'stockstatus' => $stockStatus);
    echo json_encode($data);
    echo json_encode(array('availablequantity' => $availableQuantity, 'stockstatus' => $stockStatus));
    exit;
} else {
    echo json_encode(array('error' => 'Product not found'));
    echo "Product not found in database\n";
}
?>


/******  f76beb66-19b4-42a4-b05b-ab53c1ae5139  *******/