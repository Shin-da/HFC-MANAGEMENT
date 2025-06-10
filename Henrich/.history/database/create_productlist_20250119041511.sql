CREATE TABLE IF NOT EXISTS products (
    productcode CHAR(3) NOT NULL,
    productname VARCHAR(100) NOT NULL,
    productweight DECIMAL(5,2) NOT NULL,
    productcategory VARCHAR(50) NOT NULL,
    productprice DECIMAL(10,2) NOT NULL,
    piecesperbox INT NOT NULL DEFAULT 25,
    productimage VARCHAR(100) DEFAULT 'placeholder-image.png',
    productstatus VARCHAR(30) DEFAULT 'Active',
    PRIMARY KEY (productcode)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add constraint to ensure product code is always 3 digits
ALTER TABLE products
ADD CONSTRAINT check_productcode_format 
CHECK (productcode REGEXP '^[0-9]{3}$');

-- Add constraint to ensure product price is always positive
ALTER TABLE products
ADD CONSTRAINT check_productprice_positive