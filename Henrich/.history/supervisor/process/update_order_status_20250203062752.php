<?php
require_once '../../includes/config.php';
require_once 'order_notifications.php';

header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['orderId']) || !isset($input['status'])) {
        throw new Exception('Missing required parameters');
    }

    $orderId = $input['orderId'];
    $status = $input['status'];
    
    // First get the order details for notification
    $orderQuery = "SELECT customername, ordertotal FROM customerorder WHERE orderid = ?";
    $stmt = $conn->prepare($orderQuery);
    $stmt->bind_param("s", $orderId);
    $stmt->execute();
    $orderResult = $stmt->get_result()->fetch_assoc();
    
    // Update the order status
    $query = "UPDATE customerorder SET status = ?, datecompleted = ? WHERE orderid = ?";
    $stmt = $conn->prepare($query);
    
    $dateCompleted = ($status === 'Completed') ? date('Y-m-d') : null;
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
