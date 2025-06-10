<?php
require_once './database/dbconnect.php';

$sql = "SELECT productcode, productname, productcategory, productweight, productprice, productimage, piecesperbox FROM products";
$result = $conn->query($sql);

if ($result) {
    if ($result->num_rows > 0) {
        $products = array();
        while ($row = $result->fetch_assoc()) {
            // Ensure price is properly formatted as a number
            $row['productprice'] = floatval($row['productprice']);
            $products[] = $row;
        }
        echo json_encode($products);
    } else {
        echo json_encode([]); // Return an empty array if no products found
    }
} else {
    echo json_encode(["error" => "Query failed: " . $conn->error]);
}

$conn->close();
