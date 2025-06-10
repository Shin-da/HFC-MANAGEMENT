CREATE TABLE IF NOT EXISTS inventory (
    inventoryid INT AUTO_INCREMENT PRIMARY KEY,
    productcode VARCHAR(50) UNIQUE NOT NULL,
    productname VARCHAR(100) NOT NULL,
    productcategory VARCHAR(50) NOT NULL,
    availablequantity INT NOT NULL DEFAULT 0,
    onhandquantity INT NOT NULL DEFAULT 0,
    reorder_point INT DEFAULT 10,
    dateupdated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,