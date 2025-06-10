<?php
require '../../reusable/redirect404.php';
require '../../session/session.php';
require '../../database/dbconnect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get and decode the JSON data
$data = json_decode(file_get_contents('php://input'), true);
$orderId = $data['orderId'] ?? null;
$newStatus = $data['status'] ?? null;

// Debug logging
error_log("Received request - OrderID: $orderId, New Status: $newStatus");

if (!$orderId || !$newStatus) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

try {
    $conn->begin_transaction();

    // Base query
    $query = "UPDATE customerorder SET status = ?";
    $params = [$newStatus];
    $types = "s";

    // Add date completed if status is Completed
    if ($newStatus === 'Completed') {
        $query .= ", datecompleted = CURRENT_DATE()";
    } elseif ($newStatus === 'Pending' || $newStatus === 'Processing' || $newStatus === 'Cancelled') {
        $query .= ", datecompleted = NULL";
    }

    // Add where clause
    $query .= " WHERE orderid = ?";
    $params[] = $orderId;
    $types .= "s";

    // Debug logging
    error_log("Executing query: $query");
    error_log("Parameters: " . print_r($params, true));

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param($types, ...$params);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    if ($stmt->affected_rows === 0) {
        throw new Exception("No order found with ID: $orderId");
    }

    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => "Order status updated to $newStatus successfully",
        'newStatus' => $newStatus,
        'orderId' => $orderId
    ]);
} catch (Exception $e) {
    $conn->rollback();
    error_log("Status update error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update order status: ' . $e->getMessage()
    ]);
}

if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
