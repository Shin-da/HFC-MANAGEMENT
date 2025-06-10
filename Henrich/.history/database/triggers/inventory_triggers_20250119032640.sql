DELIMITER //

CREATE TRIGGER after_stockmovement_insert 
AFTER INSERT ON stockmovement
FOR EACH ROW
BEGIN
    -- Update inventory quantities
    IF NEW.movement_type = 'IN' THEN
        UPDATE inventory 
        SET availablequantity = availablequantity + NEW.totalpieces,
            onhandquantity = onhandquantity + NEW.totalpieces,
            dateupdated = CURRENT_TIMESTAMP
        WHERE productcode = NEW.productcode;
    ELSEIF NEW.movement_type = 'OUT' THEN
        UPDATE inventory 
        SET availablequantity = availablequantity - NEW.totalpieces,
            onhandquantity = onhandquantity - NEW.totalpieces,
            dateupdated = CURRENT_TIMESTAMP
        WHERE productcode = NEW.productcode;
    END IF;
END //

CREATE TRIGGER before_stockmovement_insert
BEFORE INSERT ON stockmovement
FOR EACH ROW
BEGIN
    DECLARE current_stock INT;
    
    IF NEW.movement_type = 'OUT' THEN
        SELECT availablequantity INTO current_stock
        FROM inventory
        WHERE productcode = NEW.productcode;
        
        IF current_stock < NEW.totalpieces THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Insufficient stock available';
        END IF;
    END IF;
END //

DELIMITER ;
