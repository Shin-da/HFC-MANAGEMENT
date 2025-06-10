<?php
require_once '../../includes/config.php';

function createOrderNotification($orderId, $customerName, $orderTotal, $type = 'new_order', $status = null) {
    global $conn;
    
    error_log("Starting notification creation for order: $orderId");
    
    try {
        // Get all supervisors and admins
        $query = "SELECT user_id FROM users WHERE role IN ('supervisor', 'admin')";
        $result = $conn->query($query);
        
        if (!$result) {
            throw new Exception("Failed to get users: " . $conn->error);
        }

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

            // Insert notification
            $sql = "INSERT INTO notifications (user_id, message, is_read, activity_id) VALUES (?, ?, 0, ?)";
            $stmt = $conn->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Failed to prepare statement: " . $conn->error);
            }

            $stmt->bind_param("iss", $user['user_id'], $message, $orderId);
            
            if (!$stmt->execute()) {
                error_log("Failed to create notification for user {$user['user_id']}: " . $stmt->error);
            } else {
                error_log("Successfully created notification for user {$user['user_id']}");
            }
            
            $stmt->close();
        }
        
        return true;
        
    } catch (Exception $e) {
        error_log("Error in createOrderNotification: " . $e->getMessage());
        return false;
    }
}
