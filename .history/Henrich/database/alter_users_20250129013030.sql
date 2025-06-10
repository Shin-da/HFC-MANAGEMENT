ALTER TABLE users 
ADD COLUMN is_online BOOLEAN DEFAULT FALSE,
ADD COLUMN last_online TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD INDEX idx_online_status (is_online, last_online);
