<?php
require_once '../../includes/config.php';

function createOrderNotification($orderId, $customerName, $orderTotal, $type = 'new_order', $status = null) {
    global $conn;
    
    error_log("Creating order notification with parameters:");
    error_log("OrderID: $orderId");
    error_log("CustomerName: $customerName");
    error_log("OrderTotal: $orderTotal");
    error_log("Type: $type");
    
    try {
        // Get all supervisors and admins
        $query = "SELECT user_id FROM users WHERE role IN ('supervisor', 'admin')";
        $result = $conn->query($query);
        
        if (!$result) {
            throw new Exception("Failed to get users: " . $conn->error);
        }

        $success = false;
        
        while ($user = $result->fetch_assoc()) {
            // Format message based on type
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
            
            error_log("Inserting notification for user_id: {$user['user_id']}");
            error_log("Message: $message");
            
            $sql = "INSERT INTO notifications (user_id, message, is_read, created_at, activity_id) 
                    VALUES (?, ?, 0, NOW(), ?)";
            
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                error_log("Failed to prepare statement: " . $conn->error);
                continue;
            }
            
            $stmt->bind_param("iss", 
                $user['user_id'],
                $message,
                $orderId
            );
            
            if ($stmt->execute()) {
                error_log("Successfully created notification for user {$user['user_id']}");
                $success = true;
            } else {
                error_log("Failed to create notification: " . $stmt->error);
            }
            
            $stmt->close();
        }
        
        return $success;
        
    } catch (Exception $e) {
        error_log("Error in createOrderNotification: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        return false;
    }
}
