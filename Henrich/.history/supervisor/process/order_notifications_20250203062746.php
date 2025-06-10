<?php
require_once '../../includes/config.php';

function createOrderNotification($orderId, $customerName, $orderTotal, $type = 'new_order', $status = null) {
    global $conn;
    
    // Get all supervisors and admins
    $query = "SELECT user_id FROM users WHERE role IN ('supervisor', 'admin')";
    $result = $conn->query($query);
    
    while ($user = $result->fetch_assoc()) {
        // Format message based on notification type
        switch ($type) {
            case 'new_order':
                $content = "New order #$orderId from $customerName (₱" . number_format($orderTotal, 2) . ")";
                break;
            case 'status_update':
                $content = "Order #$orderId has been marked as $status";
                break;
            default:
                $content = "Order #$orderId has been updated";
        }
        
        $sql = "INSERT INTO notifications (user_id, content, type, reference_id, is_read, created_at) 
                VALUES (?, ?, ?, ?, 0, CURRENT_TIMESTAMP)";
        
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $type = 'order_' . $type;
            $stmt->bind_param(
                "isss",
                $user['user_id'],
                $content,
                $type,
                $orderId
            );
            
            if (!$stmt->execute()) {
                error_log("Failed to create order notification: " . $stmt->error);