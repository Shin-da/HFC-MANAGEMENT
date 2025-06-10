<?php
require_once '../../includes/config.php';

function createOrderNotification($orderId, $customerName, $orderTotal, $type = 'new_order', $status = null) {
    global $conn;
    
    // Debug log
    error_log("Creating order notification: OrderID: $orderId, Type: $type");
    
    // Get all supervisors and admins
    $query = "SELECT user_id FROM users WHERE role IN ('supervisor', 'admin')";
    $result = $conn->query($query);
    
    if (!$result) {
        error_log("Failed to get users: " . $conn->error);
        return false;
    }
    
    while ($user = $result->fetch_assoc()) {
        // Format message based on notification type
        switch ($type) {
            case 'new_order':
                $message = "New order #$orderId from $customerName (₱" . number_format($orderTotal, 2) . ")";
                break;
            case 'status_update':
                $message = "Order #$orderId has been marked as $status";
                break;
            default:
                $message = "Order #$orderId has been updated";
        }
        
        // Debug log
        error_log("Inserting notification for user_id: {$user['user_id']}, Message: $message");
        
            );
            
            if (!$stmt->execute()) {
                error_log("Failed to create order notification: " . $stmt->error);
            }
            
            $stmt->close();
        }
    }
}
