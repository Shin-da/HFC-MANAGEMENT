CREATE TABLE IF NOT EXISTS inventory (
    productcode CHAR(3) NOT NULL PRIMARY KEY,
    productname VARCHAR(100) NOT NULL,
    productcategory VARCHAR(50) NOT NULL,
    availablequantity INT DEFAULT 0,
    onhandquantity INT DEFAULT 0,
    dateupdated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    unit_price DECIMAL(10,2) DEFAULT 0.00
);
