/*************  ✨ Codeium Command ⭐  *************/
<?php
include '../database/dbconnect.php';

$productcode = $_GET['productcode'] ?? '';

$sql = "SELECT weight, price FROM productlist WHERE productcode = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $productcode);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode($row);
} else {
    echo json_encode(array('weight' => '', 'price' => ''));
}

$conn->close();
/******  75096b2f-0c11-4ec9-a337-7c43a3799aca  *******/