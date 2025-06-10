<?php
// Set headers first
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 0); // Disable error display for production

include "./database/dbconnect.php";
include "./session/session.php";

// Function to send JSON response
function sendJsonResponse($success, $message, $data = null, $statusCode = 200) {
    http_response_code($statusCode);
    $response = [
        'success' => $success,
        'message' => $message
    ];
    if ($data !== null) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit;
}

try {
    // Get raw input
    $rawInput = file_get_contents('php://input');
    error_log("Raw input received: " . $rawInput);
    
    // Decode JSON with error checking
    $data = json_decode($rawInput, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        sendJsonResponse(false, 'Invalid JSON: ' . json_last_error_msg(), null, 400);
    }
    
    // Debug decoded data
    error_log("Decoded data: " . print_r($data, true));

    if (!isset($_SESSION['accountid'])) {
        sendJsonResponse(false, 'User not logged in', null, 401);
    }

    if (!$data) {
        sendJsonResponse(false, 'Invalid or empty request data', null, 400);
    }

    // Validate required fields
    if (!isset($data['customerName']) || !isset($data['customerAddress']) || 
        !isset($data['customerPhone']) || !isset($data['orderDescription']) || 
        !isset($data['orderTotal'])) {
        sendJsonResponse(false, 'Missing required fields in order data', null, 400);
    }

    // Get branch_id from session or default to 1
    $branch_id = isset($_SESSION['branch_id']) ? $_SESSION['branch_id'] : 1;

    $conn->begin_transaction();

    // Basic order insert with branch_id
    $stmt = $conn->prepare("INSERT INTO customerorder (
        orderdescription,
        orderdate,
        customername,
        customeraddress,
        customerphonenumber,
        ordertotal,
        status,
        timeoforder,
        ordertype,
        branch_id
    ) VALUES (?, CURRENT_DATE(), ?, ?, ?, ?, 'Pending', CURRENT_TIME(), 'Online', ?)");

    if (!$stmt) {
        sendJsonResponse(false, "Prepare failed: " . $conn->error, null, 500);
    }

    $orderDesc = json_encode($data['orderDescription']);
    $stmt->bind_param("ssssdi", 
        $orderDesc,
        $data['customerName'],
        $data['customerAddress'],
        $data['customerPhone'],
        $data['orderTotal'],
        $branch_id
    );

    if (!$stmt->execute()) {
        sendJsonResponse(false, "Order insert failed: " . $stmt->error, null, 500);
    }

    $orderId = $conn->insert_id;

    // Insert order logs
    foreach ($data['orderDescription'] as $item) {
        if (!isset($item['productcode']) || !isset($item['productname']) || 
            !isset($item['unit_price']) || !isset($item['quantity'])) {
            sendJsonResponse(false, 'Missing required fields in order item', null, 400);
        }

        $logStmt = $conn->prepare("INSERT INTO orderlog (
            orderid, 
            productcode, 
            productname, 
            unit_price, 
            quantity, 
            orderdate, 
            timeoforder,
            branch_id
        ) VALUES (?, ?, ?, ?, ?, CURRENT_DATE(), CURRENT_TIME(), ?)");

        if (!$logStmt) {
            sendJsonResponse(false, "Log prepare failed: " . $conn->error, null, 500);
        }

        $logStmt->bind_param("issdii",
            $orderId,
            $item['productcode'],
            $item['productname'],
            $item['unit_price'],
            $item['quantity'],
            $branch_id
        );

        if (!$logStmt->execute()) {
            sendJsonResponse(false, "Log insert failed: " . $logStmt->error, null, 500);
        }
    }

    $conn->commit();
    sendJsonResponse(true, 'Order placed successfully', ['orderid' => $orderId]);

} catch (Exception $e) {
    error_log("Order error: " . $e->getMessage());
    
    if (isset($conn)) {
        $conn->rollback();
    }
    
    sendJsonResponse(false, $e->getMessage(), null, 500);
}
?>

