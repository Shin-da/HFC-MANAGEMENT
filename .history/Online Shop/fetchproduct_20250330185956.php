<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Disable error display for production

// Set proper headers
header('Content-Type: application/json');

require_once './database/dbconnect.php';

try {
    // Check if connection is successful
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Check if products table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'products'");
    if ($tableCheck->num_rows === 0) {
        throw new Exception("Products table does not exist");
    }

    $sql = "SELECT productcode, productname, productcategory, productweight, productprice, productimage, piecesperbox FROM products";
    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }

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
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "error" => $e->getMessage(),
        "debug" => error_get_last()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
