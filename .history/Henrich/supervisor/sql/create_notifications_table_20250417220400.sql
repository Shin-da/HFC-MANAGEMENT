-- Drop table if it exists
DROP TABLE IF EXISTS `notifications`;

-- Create notifications table
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL COMMENT 'Type of notification: inventory_alert, order_update, system_message',
  `title` varchar(255) NOT NULL COMMENT 'Short notification title',
  `message` text NOT NULL COMMENT 'Detailed notification message',
  `severity` varchar(20) NOT NULL DEFAULT 'info' COMMENT 'Notification severity: info, warning, danger',
  `user_id` int(11) NOT NULL DEFAULT 0 COMMENT '0 for all users, otherwise specific user ID',
  `metadata` text DEFAULT NULL COMMENT 'Additional data as JSON',
  `is_read` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Whether notification has been read',
  `created_at` datetime NOT NULL COMMENT 'When the notification was created',
  `read_at` datetime DEFAULT NULL COMMENT 'When the notification was read',
  PRIMARY KEY (`id`),
  KEY `idx_user_read` (`user_id`, `is_read`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='System notifications for users';

-- Insert some example notifications
INSERT INTO `notifications` (`type`, `title`, `message`, `severity`, `user_id`, `metadata`, `is_read`, `created_at`) VALUES
('system_message', 'Welcome to HFC Management', 'Welcome to the new inventory notification system. You will now receive alerts for low stock and other important events.', 'info', 0, NULL, 0, NOW()),
('inventory_alert', 'Low Stock Alert', 'Product "Butter" (Code: BUT001) is running low. Current quantity: 5.', 'warning', 0, '{"productCode":"BUT001","productName":"Butter","quantity":5,"alertType":"low_stock"}', 0, NOW()),
('inventory_alert', 'Product Out of Stock', 'Product "Flour" (Code: FLO001) is out of stock.', 'danger', 0, '{"productCode":"FLO001","productName":"Flour","quantity":0,"alertType":"out_of_stock"}', 0, NOW()); 