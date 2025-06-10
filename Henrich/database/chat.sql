CREATE TABLE chat_messages (
    message_id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id INT,
    receiver_id INT,
    message TEXT,
    attachment_url VARCHAR(255),
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(user_id),
    FOREIGN KEY (receiver_id) REFERENCES users(user_id)
);

CREATE TABLE chat_notifications (
    notification_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    message_id INT,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (message_id) REFERENCES chat_messages(message_id)
);

-- Add online status tracking
ALTER TABLE users 
ADD COLUMN last_online TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN is_online BOOLEAN DEFAULT FALSE;

-- Add index for better performance
CREATE INDEX idx_chat_users ON chat_messages(sender_id, receiver_id);
CREATE INDEX idx_chat_timestamp ON chat_messages(created_at);
