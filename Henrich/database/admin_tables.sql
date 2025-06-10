-- Create leave_requests table
CREATE TABLE IF NOT EXISTS `leave_requests` (
    `leave_id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `leave_type` enum('sick','vacation','personal','other') NOT NULL,
    `start_date` date NOT NULL,
    `end_date` date NOT NULL,
    `reason` text NOT NULL,
    `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    `approved_by` int(11) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`leave_id`),
    KEY `user_id` (`user_id`),
    KEY `approved_by` (`approved_by`),
    CONSTRAINT `leave_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
    CONSTRAINT `leave_requests_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create overtime_requests table
CREATE TABLE IF NOT EXISTS `overtime_requests` (
    `overtime_id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `date` date NOT NULL,
    `hours` decimal(5,2) NOT NULL,
    `reason` text NOT NULL,
    `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    `approved_by` int(11) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`overtime_id`),
    KEY `user_id` (`user_id`),
    KEY `approved_by` (`approved_by`),
    CONSTRAINT `overtime_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
    CONSTRAINT `overtime_requests_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create schedule_requests table
CREATE TABLE IF NOT EXISTS `schedule_requests` (
    `schedule_id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `current_schedule` varchar(100) NOT NULL,
    `requested_schedule` varchar(100) NOT NULL,
    `reason` text NOT NULL,
    `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    `approved_by` int(11) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`schedule_id`),
    KEY `user_id` (`user_id`),
    KEY `approved_by` (`approved_by`),
    CONSTRAINT `schedule_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
    CONSTRAINT `schedule_requests_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create system_settings table
CREATE TABLE IF NOT EXISTS `system_settings` (
    `setting_id` int(11) NOT NULL AUTO_INCREMENT,
    `setting_key` varchar(50) NOT NULL,
    `setting_value` text NOT NULL,
    `setting_description` text,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`setting_id`),
    UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default system settings
INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_description`) VALUES
('site_name', 'HFC Management System', 'The name of the system'),
('site_email', 'admin@hfc.com', 'System email address'),
('max_leave_days', '15', 'Maximum number of leave days per year'),
('max_overtime_hours', '40', 'Maximum overtime hours per month'),
('notification_email', 'notifications@hfc.com', 'Email address for system notifications'),
('maintenance_mode', '0', 'System maintenance mode (0: Off, 1: On)'); 