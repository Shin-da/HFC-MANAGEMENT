/*************  ✨ Codeium Command 🌟  *************/
<?php
include '../database/dbconnect.php';

$productcode = $_GET['productcode'] ?? '';

$sql = "SELECT productcode, productname, productweight, productcategory, productprice FROM productlist WHERE productcode = ?";
$sql = "SELECT weight, price FROM productlist WHERE productcode = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $productcode);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode($row);
} else {
    echo json_encode(array(
        'productcode' => '',
        'productname' => '',
        'productweight' => '',
        'productcategory' => '',
        'productprice' => ''
    ));
    echo json_encode(array('weight' => '', 'price' => ''));
}

$conn->close();

/******  a7141812-eee0-4c1d-b3e6-6df7c6cf4faa  *******/