<?php
require_once '../../includes/config.php';

function createOrderNotification($orderId, $customerName, $orderTotal) {
    global $conn;
    
    // Get all supervisors and admins
    $query = "SELECT user_id FROM users WHERE role IN ('supervisor', 'admin')";
    $result = $conn->query($query);
    
    while ($user = $result->fetch_assoc()) {
        $notification = [
            'user_id' => $user['user_id'],
            'title' => 'New Order Created',
            'message' => "New order #$orderId from $customerName (₱" . number_format($orderTotal, 2) . ")",
            'type' => 'order',
            'reference_id' => $orderId,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $sql = "INSERT INTO notifications (user_id, title, message, type, reference_id, created_at) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "isssss",
            $notification['user_id'],
            $notification['title'],
            $notification['message'],
            $notification['type'],
            $notification['reference_id'],
            $notification['created_at']
        );
        $stmt->execute();
    }
}
