<?php
require '../database/dbconnect.php';

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'No customer ID provided']);
    exit();
}

$customerId = $_GET['id'];
$response = ['success' => false];

// Get customer details
$sql = "SELECT * FROM customerdetails WHERE customerid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customerId);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();

if ($customer) {
    // Get customer's order count
    $sql = "SELECT COUNT(*) as orderCount FROM customerorder WHERE customerid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    $orderResult = $stmt->get_result();
    $orderCount = $orderResult->fetch_assoc()['orderCount'];

    $response = [
        'success' => true,
        'customer' => $customer,
        'orderCount' => $orderCount
    ];
}

echo json_encode($response);
