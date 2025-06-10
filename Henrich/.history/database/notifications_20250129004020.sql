CREATE TABLE ceo_notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    message TEXT NOT NULL,
    priority ENUM('critical', 'high', 'medium', 'low') DEFAULT 'medium',
    category VARCHAR(50) NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expiry_date TIMESTAMP DEFAULT (CURRENT_TIMESTAMP + INTERVAL 7 DAY),
    action_url VARCHAR(255),
    INDEX idx_priority_date (priority, created_at),
    INDEX idx_category (category)
);

CREATE TABLE notification_settings (
    user_id INT NOT NULL,
    category VARCHAR(50) NOT NULL,
    is_enabled BOOLEAN DEFAULT TRUE,
    min_priority ENUM('critical', 'high', 'medium', 'low') DEFAULT 'medium',
    PRIMARY KEY (user_id, category),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Create trigger for auto-cleanup
DELIMITER //
CREATE EVENT cleanup_old_notifications
ON SCHEDULE EVERY 1 DAY
DO BEGIN
    DELETE FROM ceo_notifications 
    WHERE expiry_date < CURRENT_TIMESTAMP