<?php

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'No customer ID provided']);
    exit();
}

$customerId = $_GET['id'];
$response = ['success' => false];

try {
    // Get customer details from customerorder
    $sql = "SELECT DISTINCT cd.customername, cd.customeraddress, cd.customerphonenumber 
            FROM customerdetails cd 
            WHERE cd.customerid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result->fetch_assoc();

    if ($customer) {
        // Get customer's order count
        $sql = "SELECT COUNT(*) as orderCount 
                FROM customerorder 
                WHERE customername = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $customer['customername']);
        $stmt->execute();
        $orderResult = $stmt->get_result();
        $orderCount = $orderResult->fetch_assoc()['orderCount'];

        $response = [
            'success' => true,
            'customer' => [
                'customername' => htmlspecialchars_decode($customer['customername']),
                'customeraddress' => htmlspecialchars_decode($customer['customeraddress']),
                'customerphonenumber' => htmlspecialchars_decode($customer['customerphonenumber'])
            ],
            'orderCount' => $orderCount
        ];
    } else {
        $response['message'] = 'Customer not found';
    }
} catch (Exception $e) {
    $response['message'] = 'Database error occurred';
    error_log("Error in get_customer_details.php: " . $e->getMessage());
}

echo json_encode($response, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
