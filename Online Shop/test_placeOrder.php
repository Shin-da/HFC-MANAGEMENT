<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include session handling
include "./session/session.php";

// Set session variables (simulate a logged in user)
$_SESSION['accountid'] = 1;
$_SESSION['branch_id'] = 1;

// Test JSON data - this simulates what would be sent from the frontend
$testJson = json_encode([
    'customerName' => 'Test Customer',
    'customerAddress' => '123 Test Street, Test City',
    'customerPhone' => '+63 999 999 9999',
    'orderTotal' => 500.00,
    'orderDescription' => [
        [
            'productcode' => 'CHI', // Make sure this exists in your inventory
            'productname' => 'Chicken',
            'unit_price' => 250.00,
            'quantity' => 2
        ]
    ]
]);

// Display the test data
echo "<h3>Test JSON Data:</h3>";
echo "<pre>" . htmlspecialchars($testJson) . "</pre>";

// Create cURL request to the placeOrder.php script
$ch = curl_init('http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/placeOrder.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $testJson);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($testJson)
]);
curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());

// Execute the request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

// Display the results
echo "<h3>Response Status: " . $httpCode . "</h3>";

if ($error) {
    echo "<h3>cURL Error:</h3>";
    echo "<pre>" . htmlspecialchars($error) . "</pre>";
}

echo "<h3>Response Body:</h3>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

// Parse and display JSON response if possible
if ($response) {
    $jsonResponse = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "<h3>Decoded Response:</h3>";
        echo "<pre>" . print_r($jsonResponse, true) . "</pre>";
    } else {
        echo "<h3>JSON Decode Error:</h3>";
        echo "<pre>" . json_last_error_msg() . "</pre>";
    }
}
?> 