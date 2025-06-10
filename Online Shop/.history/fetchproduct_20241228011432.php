<?php
require_once './database/dbconnect.php';

$sql = "SELECT * FROM productlist";
$result = $conn->query($sql);

if ($result) {
    if ($result->num_rows > 0) {
        $products = array();
        while ($row = $result->fetch_assoc()) {
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
