<?php

/**
 * NotificationManager class
 * 
 * Handles system notifications for users including inventory alerts,
 * order updates, and system messages
 */
class NotificationManager {
    private $conn;
    
    /**
     * Constructor
     * 
     * @param mysqli $conn Database connection
     */
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Create a new notification
     * 
     * @param string $type Notification type (inventory_alert, order_update, system_message)
     * @param string $title Notification title
     * @param string $message Notification message
     * @param string $severity Notification severity (info, warning, danger)
     * @param int $user_id User ID (0 for all users)
     * @param array $metadata Additional data (optional)
     * @return int|bool Notification ID on success, false on failure
     */
    public function createNotification($type, $title, $message, $severity = 'info', $user_id = 0, $metadata = null) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO notifications (type, title, message, severity, user_id, metadata, is_read, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, 0, NOW())
            ");
            
            // Convert metadata to JSON if it's an array
            $metadataJson = ($metadata !== null) ? json_encode($metadata) : null;
            
            $stmt->bind_param("ssssss", $type, $title, $message, $severity, $user_id, $metadataJson);
            
            if ($stmt->execute()) {
                return $this->conn->insert_id;
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Error creating notification: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create an inventory alert notification
     * 
     * @param string $productCode Product code
     * @param string $productName Product name
     * @param int $quantity Current quantity
     * @param string $alertType Type of alert (low_stock, out_of_stock, expiring)
     * @return int|bool Notification ID on success, false on failure
     */
    public function createInventoryAlert($productCode, $productName, $quantity, $alertType) {
        $title = '';
        $message = '';
        $severity = 'info';
        
        switch ($alertType) {
            case 'low_stock':
                $title = 'Low Stock Alert';
                $message = "Product \"$productName\" (Code: $productCode) is running low. Current quantity: $quantity.";
                $severity = 'warning';
                break;
                
            case 'out_of_stock':
                $title = 'Product Out of Stock';
                $message = "Product \"$productName\" (Code: $productCode) is out of stock.";
                $severity = 'danger';
                break;
                
            case 'expiring':
                $title = 'Product Expiring Soon';
                $message = "Product \"$productName\" (Code: $productCode) is expiring soon.";
                $severity = 'warning';
                break;
                
            default:
                $title = 'Inventory Alert';
                $message = "Notification for product \"$productName\" (Code: $productCode). Current quantity: $quantity.";
                break;
        }
        
        $metadata = [
            'productCode' => $productCode,
            'productName' => $productName,
            'quantity' => $quantity,
            'alertType' => $alertType
        ];
        
        return $this->createNotification('inventory_alert', $title, $message, $severity, 0, $metadata);
    }
    
    /**
     * Get notifications for a user
     * 
     * @param int $userId User ID
     * @param bool $includeRead Include read notifications
     * @param int $limit Maximum number of notifications to return
     * @return array Notifications
     */
    public function getNotifications($userId, $includeRead = false, $limit = 20) {
        try {
            $sql = "
                SELECT * FROM notifications 
                WHERE (user_id = ? OR user_id = 0)
            ";
            
            if (!$includeRead) {
                $sql .= " AND is_read = 0";
            }
            
            $sql .= " ORDER BY created_at DESC LIMIT ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $userId, $limit);
            $stmt->execute();
            
            $result = $stmt->get_result();
            $notifications = [];
            
            while ($row = $result->fetch_assoc()) {
                // Decode the metadata JSON if present
                if (!empty($row['metadata'])) {
                    $row['metadata'] = json_decode($row['metadata'], true);
                }
                
                $notifications[] = $row;
            }
            
            return $notifications;
        } catch (Exception $e) {
            error_log("Error retrieving notifications: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get count of unread notifications for a user
     * 
     * @param int $userId User ID
     * @return int Count of unread notifications
     */
    public function getUnreadCount($userId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT COUNT(*) as count 
                FROM notifications 
                WHERE (user_id = ? OR user_id = 0) AND is_read = 0
            ");
            
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            return (int) $row['count'];
        } catch (Exception $e) {
            error_log("Error getting unread notification count: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Mark a notification as read
     * 
     * @param int $notificationId Notification ID
     * @param int $userId User ID
     * @return bool Success
     */
    public function markAsRead($notificationId, $userId) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE notifications 
                SET is_read = 1, read_at = NOW() 
                WHERE id = ? AND (user_id = ? OR user_id = 0)
            ");
            
            $stmt->bind_param("ii", $notificationId, $userId);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error marking notification as read: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Mark all notifications as read for a user
     * 
     * @param int $userId User ID
     * @return bool Success
     */
    public function markAllAsRead($userId) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE notifications 
                SET is_read = 1, read_at = NOW() 
                WHERE (user_id = ? OR user_id = 0) AND is_read = 0
            ");
            
            $stmt->bind_param("i", $userId);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error marking all notifications as read: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a notification
     * 
     * @param int $notificationId Notification ID
     * @return bool Success
     */
    public function deleteNotification($notificationId) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM notifications WHERE id = ?");
            $stmt->bind_param("i", $notificationId);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error deleting notification: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete old notifications
     * 
     * @param int $days Number of days to keep notifications
     * @return bool Success
     */
    public function deleteOldNotifications($days = 30) {
        try {
            $stmt = $this->conn->prepare("
                DELETE FROM notifications 
                WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)
            ");
            
            $stmt->bind_param("i", $days);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error deleting old notifications: " . $e->getMessage());
            return false;
        }
    }
} 