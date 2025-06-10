-- Add online status columns if they don't exist
ALTER TABLE users
ADD COLUMN IF NOT EXISTS is_online BOOLEAN DEFAULT FALSE,
ADD COLUMN IF NOT EXISTS last_online TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN IF NOT EXISTS status ENUM('active', 'inactive') DEFAULT 'active';

-- Update existing rows
UPDATE users SET is_online = FALSE WHERE is_online IS NULL;
UPDATE users SET last_online = CURRENT_TIMESTAMP WHERE last_online IS NULL;
UPDATE users SET status = 'active' WHERE status IS NULL;

-- Add indexes for performance
CREATE INDEX IF NOT EXISTS idx_user_online ON users(is_online, last_online);
CREATE INDEX IF NOT EXISTS idx_user_role ON users(role);
