<?php
header('Content-Type: application/json');

// Retrieve the available quantity from the database
$availableQuantity = // retrieve from database

// Create a JSON object with the available quantity and status message
$response = array(
    'availableQuantity' => $availableQuantity,
    'status' => ($availableQuantity > 0) ? 'in stock' : 'out of stock'
);

// Return the JSON object
echo json_encode($response);
?>