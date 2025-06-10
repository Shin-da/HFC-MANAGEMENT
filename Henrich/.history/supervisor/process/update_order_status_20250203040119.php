<?php
require_once '../../includes/config.php';

// Set header to return JSON
header('Content-Type: application/json');

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['orderId']) || !isset($input['status'])) {
        throw new Exception('Missing required parameters');
    }

    $orderId = $input['orderId'];
    $status = $input['status'];
    
    // Validate status
    $allowedStatuses = ['Pending', 'Processing', 'Completed', 'Cancelled'];
    if (!in_array($status, $allowedStatuses)) {
        throw new Exception('Invalid status');
    }

    // Update the order status
    $updateFields = "status = '$status'";
    
    // Add completion date if status is Completed
    if ($status === 'Completed') {
        $updateFields .= ", datecompleted = CURRENT_DATE()";
    }

    $query = "UPDATE customerorder SET $updateFields WHERE orderid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $orderId);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Order status updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update order status');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
