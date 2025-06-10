-- Create branches table
CREATE TABLE branches (
    branch_id INT PRIMARY KEY AUTO_INCREMENT,
    branch_name VARCHAR(100) NOT NULL,
    branch_location VARCHAR(255) NOT NULL,
    branch_manager VARCHAR(100),
    contact_number VARCHAR(20),
    email VARCHAR(100),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_branch_status (status),
    INDEX idx_branch_name (branch_name)
);

-- Insert default branch
INSERT INTO branches (branch_name, branch_location, branch_manager, contact_number, email) 
VALUES ('Main Branch', 'San Fernando, Pampanga', 'Admin User', '+639123456789', 'admin@henrichfood.com');

-- Create branch_inventory table to track inventory per branch
CREATE TABLE branch_inventory (
    id INT PRIMARY KEY AUTO_INCREMENT,
    branch_id INT NOT NULL,
    productcode CHAR(3) NOT NULL,
    available_quantity INT DEFAULT 0,
    reorder_point INT DEFAULT 10,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (branch_id) REFERENCES branches(branch_id),
    FOREIGN KEY (productcode) REFERENCES products(productcode),
    UNIQUE KEY unique_branch_product (branch_id, productcode)
);

-- Update customer order table to include branch reference
ALTER TABLE customerorder 
ADD COLUMN branch_id INT,
ADD FOREIGN KEY (branch_id) REFERENCES branches(branch_id);

-- Update the existing orders to reference the main branch
UPDATE customerorder SET branch_id = 1 WHERE branch_id IS NULL;

-- Make branch_id required for future orders
ALTER TABLE customerorder MODIFY COLUMN branch_id INT NOT NULL;
