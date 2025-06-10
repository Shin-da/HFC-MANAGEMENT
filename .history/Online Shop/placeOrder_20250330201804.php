<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "./database/dbconnect.php";
include "./session/session.php";

try {
    // Get raw input
    $rawInput = file_get_contents('php://input');
    error_log("Raw input received: " . $rawInput);
    
    // Decode JSON with error checking
    $data = json_decode($rawInput, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON: ' . json_last_error_msg());
    }
    
    // Debug decoded data
    error_log("Decoded data: " . print_r($data, true));

    if (!isset($_SESSION['accountid'])) {
        throw new Exception('User not logged in');
    }

    if (!$data) {
        throw new Exception('Invalid or empty request data');
    }

    // Validate required fields
    if (!isset($data['customerName']) || !isset($data['customerAddress']) || 
        !isset($data['customerPhone']) || !isset($data['orderDescription']) || 
        !isset($data['orderTotal'])) {
        throw new Exception('Missing required fields in order data');
    }

    $conn->begin_transaction();

    // Basic order insert
    $stmt = $conn->prepare("INSERT INTO customerorder (
        orderdescription,
        orderdate,
        customername,
        customeraddress,
        customerphonenumber,
        ordertotal,
        status,
        timeoforder,
        ordertype
    ) VALUES (?, CURRENT_DATE(), ?, ?, ?, ?, 'Pending', CURRENT_TIME(), 'Online')");

    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $orderDesc = json_encode($data['orderDescription']);
    $stmt->bind_param("ssssd", 
        $orderDesc,
        $data['customerName'],
        $data['customerAddress'],
        $data['customerPhone'],
        $data['orderTotal']
    );

    if (!$stmt->execute()) {
        throw new Exception("Order insert failed: " . $stmt->error);
    }

    $orderId = $conn->insert_id;

    // Insert order logs
    foreach ($data['orderDescription'] as $item) {
        if (!isset($item['productcode']) || !isset($item['productname']) || 
            !isset($item['unit_price']) || !isset($item['quantity'])) {
            throw new Exception('Missing required fields in order item');
        }

        $logStmt = $conn->prepare("INSERT INTO orderlog (
            orderid, 
            productcode, 
            productname, 
            unit_price, 
            quantity, 
            orderdate, 
            timeoforder
        ) VALUES (?, ?, ?, ?, ?, CURRENT_DATE(), CURRENT_TIME())");

        if (!$logStmt) {
            throw new Exception("Log prepare failed: " . $conn->error);
        }

        $logStmt->bind_param("issdi",
            $orderId,
            $item['productcode'],
            $item['productname'],
            $item['unit_price'],
            $item['quantity']
        );

        if (!$logStmt->execute()) {
            throw new Exception("Log insert failed: " . $logStmt->error);
        }
    }

    $conn->commit();
    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully',
        'orderid' => $orderId
    ]);

} catch (Exception $e) {
    error_log("Order error: " . $e->getMessage());
    
    if (isset($conn)) {
        $conn->rollback();
    }
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug' => error_get_last()
    ]);
}
?>

