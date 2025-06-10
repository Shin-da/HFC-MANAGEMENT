<?php
require '../session/session.php';
require '../database/dbconnect.php';

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

    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $newStatus, $orderId);

    if ($stmt->execute()) {
        $conn->commit();
        echo json_encode([
            'success' => true,
            'message' => "Order status updated to $newStatus successfully",
            'newStatus' => $newStatus
        ]);
    } else {
        throw new Exception("Failed to update order status");
    }

} catch (Exception $e) {
    $conn->rollback();
    error_log("Status update error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update order status: ' . $e->getMessage()
    ]);
}

$stmt->close();
$conn->close();
