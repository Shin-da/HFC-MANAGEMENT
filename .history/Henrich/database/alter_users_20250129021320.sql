-- Add missing columns to users table if not exists
ALTER TABLE users
MODIFY COLUMN user_id int(11) NOT NULL AUTO_INCREMENT,
MODIFY COLUMN useremail varchar(50) NOT NULL,
MODIFY COLUMN username varchar(50) NOT NULL,
MODIFY COLUMN role enum('admin','supervisor','ceo') NOT NULL,