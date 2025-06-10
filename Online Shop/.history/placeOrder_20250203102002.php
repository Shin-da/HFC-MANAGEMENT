<?php
require './database/dbconnect.php';
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start output buffering
ob_start();

// Check if POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderData = json_decode(file_get_contents('php://input'), true);

    // Get account ID from session
    $accountId = $_SESSION['accountid'];

    // Ensure that order data is present
    if (isset($orderData['customerName'], $orderData['customerAddress'], $orderData['customerPhone'], $orderData['orderTotal'], $orderData['orderDescription'])) {
        try {
            confirmOrder($conn, $accountId, $orderData['customerName'], $orderData['customerAddress'], $orderData['customerPhone'], $orderData['orderDescription'], $orderData['orderTotal']);
            $response = array('success' => true, 'message' => 'Order placed successfully!');
            echo "<script>window.location.href='ordersuccess.php';</script>";  // Redirect to order success page

            // Redirect to app.js function to clear cart
            echo "<script>window.location.href='app.js?clearCart=true';</script>";
            
        } catch (Exception $e) {
            $response = array('success' => false, 'message' => 'Error placing order: ' . $e->getMessage());
        }
    } else {
        $response = array('success' => false, 'message' => 'Missing required order data');
    }

    // Clean output buffer before returning the response
    ob_end_clean();

    // Set the response header and output the JSON
    header('Content-Type: application/json');
    echo json_encode($response);

} else {
    $response = array('success' => false, 'message' => 'Invalid request method');
    header('Content-Type: application/json');
    echo json_encode($response);
}

// End script execution
exit;

// Function to process the order
function confirmOrder($conn, $accountId, $customerName, $customerAddress, $customerPhone, $orderDescription, $orderTotal) {
    // Retrieve customer details from the database
    $query = "SELECT customername, customeraddress, customerphonenumber, customerid FROM customeraccount WHERE accountid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $accountId);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result->fetch_assoc();

    if (!$customer) {
        throw new Exception("Customer details not found!");
    }
    // Prepare order details
    $orderDate = date('Y-m-d H:i:s');
    $status = "Pending";
    $salesperson = "online";

    // Ensure order description is an array
    if (!is_array($orderDescription)) {
        throw new Exception("Invalid order description format: order description must be an array");
    }

    // Format order description string
    $orderDescriptionStr =  implode(", ", array_map(function($item) {
        return $item['productname'] . ' ' . $item['productweight'] . 'kg ' . $item['quantity'] . 'pcs';
    }, $orderDescription));

    // Insert order into the database
    $orderQuery = "INSERT INTO orders (customername, customeraddress, customerphonenumber, orderdescription, ordertotal, orderdate, status, salesperson, customerid) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $orderStmt = $conn->prepare($orderQuery);
    $orderStmt->bind_param(
        "sssssssss",
        $customer['customername'],
        $customer['customeraddress'],
        $customer['customerphonenumber'],
        $orderDescriptionStr,
        $orderTotal,
        $orderDate,
        $status,
        $salesperson,
        $customer['customerid']
    );
    if (!$orderStmt->execute()) {
        throw new Exception("Error placing order: " . $orderStmt->error);
    }

    // Close statements
    $orderStmt->close();
}

