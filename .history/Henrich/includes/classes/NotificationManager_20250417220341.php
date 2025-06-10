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
     * @param string $title Short title of the notification
     * @param string $message Detailed notification message
     * @param string $severity Severity level (info, warning, danger)
     * @param int $user_id Target user ID (0 for all users)
     * @param array $metadata Additional data related to the notification
     * @return int|bool The ID of the created notification or false on failure
     */
    public function createNotification($type, $title, $message, $severity = 'info', $user_id = 0, $metadata = []) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO notifications (
                    type, title, message, severity, user_id, metadata, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            
            // Convert metadata to JSON
            $metadataJson = json_encode($metadata);
            
            $stmt->bind_param(
                'ssssss', 
                $type, 
                $title, 
                $message, 
                $severity,
                $user_id,
                $metadataJson
            );
            
            if ($stmt->execute()) {
                return $stmt->insert_id;
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
     * @param string $alertType Type of alert (low_stock, out_of_stock)
     * @param int $user_id Target user ID (0 for all users)
     * @return int|bool The ID of the created notification or false on failure
     */
    public function createInventoryAlert($productCode, $productName, $quantity, $alertType, $user_id = 0) {
        $title = '';
        $message = '';
        $severity = '';
        
        switch ($alertType) {
            case 'out_of_stock':
                $title = 'Product Out of Stock';
                $message = "Product '$productName' (Code: $productCode) is out of stock.";
                $severity = 'danger';
                break;
                
            case 'low_stock':
                $title = 'Low Stock Alert';
                $message = "Product '$productName' (Code: $productCode) is running low. Current quantity: $quantity.";
                $severity = 'warning';
                break;
                
            case 'reorder':
                $title = 'Reorder Recommended';
                $message = "Product '$productName' (Code: $productCode) needs to be reordered soon. Current quantity: $quantity.";
                $severity = 'info';
                break;
        }
        
        $metadata = [
            'productCode' => $productCode,
            'productName' => $productName,
            'quantity' => $quantity,
            'alertType' => $alertType
        ];
        
        return $this->createNotification('inventory_alert', $title, $message, $severity, $user_id, $metadata);
    }
    
    /**
     * Get notifications for a user
     * 
     * @param int $user_id User ID
     * @param bool $includeGlobal Whether to include global notifications (sent to all users)
     * @param int $limit Maximum number of notifications to retrieve
     * @param int $offset Offset for pagination
     * @param bool $unreadOnly Whether to get only unread notifications
     * @return array Array of notification objects
     */
    public function getNotificationsForUser($user_id, $includeGlobal = true, $limit = 10, $offset = 0, $unreadOnly = false) {
        try {
            $sql = "
                SELECT * FROM notifications 
                WHERE (user_id = ?) " . 
                ($includeGlobal ? "OR user_id = 0 " : "") .
                ($unreadOnly ? "AND is_read = 0 " : "") . 
                "ORDER BY created_at DESC
                LIMIT ? OFFSET ?
            ";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('iii', $user_id, $limit, $offset);
            $stmt->execute();
            
            $result = $stmt->get_result();
            $notifications = [];
            
            while ($row = $result->fetch_assoc()) {
                // Parse metadata from JSON
                if (!empty($row['metadata'])) {
                    $row['metadata'] = json_decode($row['metadata'], true);
                } else {
                    $row['metadata'] = [];
                }
                
                $notifications[] = $row;
            }
            
            return $notifications;
        } catch (Exception $e) {
            error_log("Error getting notifications: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get unread notification count for a user
     * 
     * @param int $user_id User ID
     * @param bool $includeGlobal Whether to include global notifications (sent to all users)
     * @return int Number of unread notifications
     */
    public function getUnreadCount($user_id, $includeGlobal = true) {
        try {
            $sql = "
                SELECT COUNT(*) as count FROM notifications 
                WHERE is_read = 0 AND (user_id = ? " . 
                ($includeGlobal ? "OR user_id = 0" : "") . ")
            ";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            return (int)$row['count'];
        } catch (Exception $e) {
            error_log("Error getting unread notification count: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Mark a notification as read
     * 
     * @param int $notification_id Notification ID
     * @param int $user_id User ID (for validation)
     * @return bool True on success, false on failure
     */
    public function markAsRead($notification_id, $user_id) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE notifications
                SET is_read = 1, read_at = NOW()
                WHERE id = ? AND (user_id = ? OR user_id = 0)
            ");
            
            $stmt->bind_param('ii', $notification_id, $user_id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error marking notification as read: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Mark all notifications for a user as read
     * 
     * @param int $user_id User ID
     * @param bool $includeGlobal Whether to include global notifications (sent to all users)
     * @return bool True on success, false on failure
     */
    public function markAllAsRead($user_id, $includeGlobal = true) {
        try {
            $sql = "
                UPDATE notifications
                SET is_read = 1, read_at = NOW()
                WHERE is_read = 0 AND (user_id = ? " . 
                ($includeGlobal ? "OR user_id = 0" : "") . ")
            ";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $user_id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error marking all notifications as read: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a notification
     * 
     * @param int $notification_id Notification ID
     * @param int $user_id User ID (for validation)
     * @return bool True on success, false on failure
     */
    public function deleteNotification($notification_id, $user_id) {
        try {
            // Only allow deletion if the notification belongs to the user or is a global notification
            $stmt = $this->conn->prepare("
                DELETE FROM notifications
                WHERE id = ? AND (user_id = ? OR user_id = 0)
            ");
            
            $stmt->bind_param('ii', $notification_id, $user_id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error deleting notification: " . $e->getMessage());
            return false;
        }
    }
} 