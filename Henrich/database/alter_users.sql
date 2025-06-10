-- Add missing columns to users table if not exists
ALTER TABLE users
MODIFY COLUMN user_id int(11) NOT NULL AUTO_INCREMENT,
MODIFY COLUMN useremail varchar(50) NOT NULL,
MODIFY COLUMN username varchar(50) NOT NULL,
MODIFY COLUMN role enum('admin','supervisor','ceo') NOT NULL,
MODIFY COLUMN password varchar(255) NOT NULL,
MODIFY COLUMN created_at timestamp NOT NULL DEFAULT current_timestamp(),
MODIFY COLUMN updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
ADD COLUMN IF NOT EXISTS first_name varchar(50),
ADD COLUMN IF NOT EXISTS last_name varchar(50),
ADD COLUMN IF NOT EXISTS department varchar(50),
MODIFY COLUMN status enum('active','inactive') DEFAULT 'active',
MODIFY COLUMN last_online timestamp NOT NULL DEFAULT current_timestamp(),
MODIFY COLUMN is_online tinyint(1) DEFAULT 0;

-- Add indexes for better performance
CREATE INDEX IF NOT EXISTS idx_user_status ON users(status);
CREATE INDEX IF NOT EXISTS idx_user_role ON users(role);
CREATE INDEX IF NOT EXISTS idx_user_online ON users(is_online, last_online);
