-- Create customeraccount table if it doesn't exist
CREATE TABLE IF NOT EXISTS customeraccount (
    accountid INT PRIMARY KEY AUTO_INCREMENT,
    customername VARCHAR(100) NOT NULL,
    customeraddress TEXT NOT NULL,
    customerphonenumber VARCHAR(20) NOT NULL,
    customerid VARCHAR(50) UNIQUE NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    useremail VARCHAR(100) UNIQUE NOT NULL,
    profilepicture VARCHAR(255) DEFAULT 'default.jpg',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add status column if it doesn't exist
ALTER TABLE customeraccount
ADD COLUMN IF NOT EXISTS status ENUM('active', 'inactive') DEFAULT 'active' AFTER profilepicture;

-- Update existing records to have 'active' status if status is NULL
UPDATE customeraccount SET status = 'active' WHERE status IS NULL; 