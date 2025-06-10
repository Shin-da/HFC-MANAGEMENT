-- Create requests table
CREATE TABLE IF NOT EXISTS `requests` (
    `request_id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `request_type` enum('leave','overtime','schedule_change','account','password','other') NOT NULL,
    `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    `description` text NOT NULL,
    `details` text,
    `leave_type` varchar(50) DEFAULT NULL,
    `start_date` date DEFAULT NULL,
    `end_date` date DEFAULT NULL,
    `hours` decimal(5,2) DEFAULT NULL,
    `date` date DEFAULT NULL,
    `current_schedule` varchar(100) DEFAULT NULL,
    `requested_schedule` varchar(100) DEFAULT NULL,
    `reason` text,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`request_id`),
    KEY `user_id` (`user_id`),
    CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create admin_logs table if not exists
CREATE TABLE IF NOT EXISTS `admin_logs` (
    `log_id` int(11) NOT NULL AUTO_INCREMENT,
    `admin_id` int(11) NOT NULL,
    `action` text NOT NULL,
    `ip_address` varchar(45) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`log_id`),
    KEY `admin_id` (`admin_id`),
    CONSTRAINT `admin_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 