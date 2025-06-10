-- Order tracking trigger
DELIMITER //
CREATE TRIGGER after_order_insert 
AFTER INSERT ON customerorder
FOR EACH ROW
BEGIN
    -- Update sales performance
    INSERT INTO sales_performance (date, total_sales, orders_count, customer_count)
    VALUES (NEW.orderdate, NEW.ordertotal, 1, 1)
    ON DUPLICATE KEY UPDATE
        total_sales = total_sales + NEW.ordertotal,
        orders_count = orders_count + 1,
        customer_count = customer_count + 1;

    -- Update financial metrics
    INSERT INTO financial_metrics (period_date, total_revenue)
    VALUES (DATE(NEW.orderdate), NEW.ordertotal)
    ON DUPLICATE KEY UPDATE
        total_revenue = total_revenue + NEW.ordertotal;
END//

-- Inventory movement trigger
CREATE TRIGGER after_stock_movement
AFTER INSERT ON stockmovement
FOR EACH ROW
BEGIN
    -- Update KPI metrics for inventory
    INSERT INTO kpi_metrics (metric_name, metric_value, target_value, period_date, status)
    SELECT 
        'inventory_turnover',
        (SELECT SUM(quantity * unit_price) FROM orderlog) / 
        (SELECT AVG(availablequantity * unit_price) FROM inventory),
        10.0, -- Target turnover rate
        CURRENT_DATE,
        CASE 
            WHEN metric_value > target_value THEN 'exceeded'
            WHEN metric_value = target_value THEN 'met'
            ELSE 'below'
        END;
END//
DELIMITER ;
