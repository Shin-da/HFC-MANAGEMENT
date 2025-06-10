<?php
require '../database/dbconnect.php';

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'No customer ID provided']);
    exit();
}

$customerId = $_GET['id'];
$response = ['success' => false];

try {
    // Get customer details from customerorder
    $sql = "SELECT DISTINCT customername, customeraddress, customerphonenumber 
            FROM customerorder 
            WHERE customername = (
                SELECT customername FROM customerorder WHERE orderid = ?
            )";
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
            'success' => true,
            'customer' => [
                'customername' => $customer['customer_name'],
                'customeraddress' => $customer['address'],
                'customerphonenumber' => $customer['phone_number']
            ],
            'orderCount' => $orderCount
        ];
    }
} catch (mysqli_sql_exception $e) {
    $response = ['success' => false, 'message' => 'Database error occurred'];
    error_log("Error in get_customer_details.php: " . $e->getMessage());
}

echo json_encode($response);
