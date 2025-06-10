DELIMITER //

CREATE TRIGGER after_stockmovement_insert 
AFTER INSERT ON stockmovement
FOR EACH ROW
BEGIN
    DECLARE inventory_updated INT DEFAULT 0;
    DECLARE low_stock_threshold INT DEFAULT 0;
    DECLARE current_stock INT DEFAULT 0;

    -- Update inventory quantities
    IF NEW.movement_type = 'IN' THEN
        UPDATE inventory 
        SET availablequantity = availablequantity + NEW.totalpacks,
            onhandquantity = onhandquantity + NEW.totalpacks,
            dateupdated = CURRENT_TIMESTAMP
        WHERE productcode = NEW.productcode;

        SET inventory_updated = ROW_COUNT();
    ELSEIF NEW.movement_type = 'OUT' THEN
        UPDATE inventory 
        SET availablequantity = availablequantity - NEW.totalpacks,
            onhandquantity = onhandquantity - NEW.totalpacks,
            dateupdated = CURRENT_TIMESTAMP
        WHERE productcode = NEW.productcode;
        
        SET inventory_updated = ROW_COUNT();
    END IF;

    -- Insert audit log record
    INSERT INTO stockactivitylog (
        activity_type,
        productcode,
        quantity_changed,
        batchid,
        movement_type,
        performed_by,
        details
    ) VALUES (
        'INVENTORY_UPDATE',
        NEW.productcode,
        NEW.totalpacks,
        NEW.batchid,
        NEW.movement_type,
        NEW.encoder,
        CONCAT('Inventory updated via ', NEW.movement_type, ' movement of ', NEW.totalpacks, ' packs')
    );
    
    -- Check for low stock levels after an OUT movement
    IF NEW.movement_type = 'OUT' THEN
        SELECT availablequantity, reorder_point INTO current_stock, low_stock_threshold
        FROM inventory
        WHERE productcode = NEW.productcode;
        
        IF current_stock <= low_stock_threshold THEN
            -- Insert low stock notification
            INSERT INTO notifications (
                user_id,
                notification_type,
                message,
                related_id,
                is_read
            ) VALUES (
                1, -- Admin user ID or a specific inventory manager ID
                'LOW_STOCK',
                CONCAT('Low stock alert for product: ', NEW.productname, ' (', NEW.productcode, '). Current stock: ', current_stock),
                NEW.productcode,
                0
            );
        END IF;
    END IF;
END //

CREATE TRIGGER before_stockmovement_insert
BEFORE INSERT ON stockmovement
FOR EACH ROW
BEGIN
    DECLARE current_stock INT;
    DECLARE product_exists INT DEFAULT 0;
    
    -- Check if product exists
    SELECT COUNT(*) INTO product_exists
    FROM products
    WHERE productcode = NEW.productcode;
    
    IF product_exists = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Product does not exist in the database';
    END IF;
    
    -- Check sufficient stock for OUT movements
    IF NEW.movement_type = 'OUT' THEN
        SELECT availablequantity INTO current_stock
        FROM inventory
        WHERE productcode = NEW.productcode;
        
        IF current_stock IS NULL THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Product not found in inventory';
        ELSEIF current_stock < NEW.totalpacks THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Insufficient stock available';
        END IF;
    END IF;
END //

DELIMITER ;
