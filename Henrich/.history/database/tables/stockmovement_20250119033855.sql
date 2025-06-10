CREATE TABLE IF NOT EXISTS stockmovement (
    ibdid INT AUTO_INCREMENT PRIMARY KEY,
    batchid VARCHAR(50) NOT NULL,
    productcode VARCHAR(50) NOT NULL,
    productname VARCHAR(100) NOT NULL,
    numberofbox INT NOT NULL DEFAULT 0,
    totalpieces INT NOT NULL DEFAULT 0,
    totalweight DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    dateencoded DATETIME DEFAULT CURRENT_TIMESTAMP,
    encoder VARCHAR(50),
    movement_type ENUM('IN', 'OUT', 'ADJUSTMENT') DEFAULT 'IN',
    FOREIGN KEY (productcode) REFERENCES products(productcode),
    INDEX idx_batchid (batchid),
    INDEX idx_date (dateencoded)
);
