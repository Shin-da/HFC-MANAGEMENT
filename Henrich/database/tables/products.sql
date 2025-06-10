CREATE TABLE IF NOT EXISTS products (
    productcode VARCHAR(50) PRIMARY KEY,
    productname VARCHAR(100) NOT NULL,
    productcategory VARCHAR(50) NOT NULL,
    weightperpiece DECIMAL(10,3) DEFAULT 0.000,
    packsperbox INT DEFAULT 0,
    price DECIMAL(10,2) DEFAULT 0.00,
    datecreated DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_category (productcategory)
);
