-- CEO Dashboard Enhancements
-- Additional tables, views, and stored procedures to support dashboard functionality

-- Create transaction for all changes
START TRANSACTION;

-- Branch Performance Metrics Table
CREATE TABLE IF NOT EXISTS branch_performance_metrics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    branch_id INT NOT NULL,
    metric_date DATE NOT NULL,
    total_sales DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    order_count INT NOT NULL DEFAULT 0,
    customer_count INT NOT NULL DEFAULT 0,
    average_order_value DECIMAL(10,2) GENERATED ALWAYS AS (CASE WHEN order_count > 0 THEN total_sales / order_count ELSE 0 END) STORED,
    profit_margin DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    inventory_value DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    employee_count INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY idx_branch_date (branch_id, metric_date),
    INDEX idx_metric_date (metric_date)
);

-- Product Performance Metrics Table
CREATE TABLE IF NOT EXISTS product_performance_metrics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id VARCHAR(20) NOT NULL,
    category_id INT NOT NULL,
    metric_date DATE NOT NULL,
    units_sold INT NOT NULL DEFAULT 0,
    revenue DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    profit DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    profit_margin DECIMAL(5,2) GENERATED ALWAYS AS (CASE WHEN revenue > 0 THEN (profit / revenue) * 100 ELSE 0 END) STORED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY idx_product_date (product_id, metric_date),
    INDEX idx_category_date (category_id, metric_date)
);

-- Category Performance Metrics Table
CREATE TABLE IF NOT EXISTS category_performance_metrics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    metric_date DATE NOT NULL,
    units_sold INT NOT NULL DEFAULT 0,
    revenue DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    profit DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    profit_margin DECIMAL(5,2) GENERATED ALWAYS AS (CASE WHEN revenue > 0 THEN (profit / revenue) * 100 ELSE 0 END) STORED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY idx_category_date (category_id, metric_date),
    INDEX idx_metric_date (metric_date)
);

-- HR Metrics Table
CREATE TABLE IF NOT EXISTS hr_metrics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    department_id INT NOT NULL,
    metric_date DATE NOT NULL,
    total_employees INT NOT NULL DEFAULT 0,
    active_employees INT NOT NULL DEFAULT 0,
    new_hires INT NOT NULL DEFAULT 0,
    terminations INT NOT NULL DEFAULT 0,
    turnover_rate DECIMAL(5,2) GENERATED ALWAYS AS (CASE WHEN total_employees > 0 THEN (terminations / total_employees) * 100 ELSE 0 END) STORED,
    avg_tenure DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    satisfaction_score DECIMAL(3,1) NOT NULL DEFAULT 0.0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY idx_dept_date (department_id, metric_date),
    INDEX idx_metric_date (metric_date)
);

-- Enhanced Financial Metrics Table (if not already existing)
CREATE TABLE IF NOT EXISTS financial_metrics_enhanced (
    id INT PRIMARY KEY AUTO_INCREMENT,
    metric_date DATE NOT NULL,
    total_revenue DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    cogs DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    gross_profit DECIMAL(15,2) GENERATED ALWAYS AS (total_revenue - cogs) STORED,
    operating_expenses DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    net_profit DECIMAL(15,2) GENERATED ALWAYS AS (total_revenue - cogs - operating_expenses) STORED,
    gross_margin DECIMAL(5,2) GENERATED ALWAYS AS (CASE WHEN total_revenue > 0 THEN (gross_profit / total_revenue) * 100 ELSE 0 END) STORED,
    net_margin DECIMAL(5,2) GENERATED ALWAYS AS (CASE WHEN total_revenue > 0 THEN (net_profit / total_revenue) * 100 ELSE 0 END) STORED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY idx_metric_date (metric_date)
);

-- Inventory Status Table
CREATE TABLE IF NOT EXISTS inventory_status (
    id INT PRIMARY KEY AUTO_INCREMENT,
    branch_id INT NOT NULL,
    metric_date DATE NOT NULL,
    total_items INT NOT NULL DEFAULT 0,
    low_stock_items INT NOT NULL DEFAULT 0,
    out_of_stock_items INT NOT NULL DEFAULT 0,
    inventory_value DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    inventory_turnover DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY idx_branch_date (branch_id, metric_date),
    INDEX idx_metric_date (metric_date)
);

-- Enhanced Views for Dashboard Data

-- Branch Performance View
CREATE OR REPLACE VIEW vw_branch_performance AS
SELECT 
    b.branch_id,
    b.name AS branch_name,
    b.location,
    b.region,
    bpm.metric_date,
    bpm.total_sales,
    bpm.order_count,
    bpm.customer_count,
    bpm.average_order_value,
    bpm.profit_margin,
    bpm.inventory_value,
    bpm.employee_count
FROM branches b
LEFT JOIN branch_performance_metrics bpm ON b.branch_id = bpm.branch_id
ORDER BY bpm.metric_date DESC, bpm.total_sales DESC;

-- Product Performance View
CREATE OR REPLACE VIEW vw_product_performance AS
SELECT 
    p.productcode,
    p.productname,
    c.category_name,
    ppm.metric_date,
    ppm.units_sold,
    ppm.revenue,
    ppm.profit,
    ppm.profit_margin,
    COALESCE(
        (ppm.units_sold - LAG(ppm.units_sold) OVER (PARTITION BY p.productcode ORDER BY ppm.metric_date)) / 
        NULLIF(LAG(ppm.units_sold) OVER (PARTITION BY p.productcode ORDER BY ppm.metric_date), 0) * 100, 
        0
    ) AS growth_rate
FROM products p
JOIN categories c ON p.category_id = c.category_id
LEFT JOIN product_performance_metrics ppm ON p.productcode = ppm.product_id
ORDER BY ppm.metric_date DESC, ppm.revenue DESC;

-- Financial Performance View
CREATE OR REPLACE VIEW vw_financial_performance AS
SELECT 
    fm.metric_date,
    fm.total_revenue,
    fm.cogs,
    fm.gross_profit,
    fm.operating_expenses,
    fm.net_profit,
    fm.gross_margin,
    fm.net_margin,
    COALESCE(
        (fm.total_revenue - LAG(fm.total_revenue) OVER (ORDER BY fm.metric_date)) / 
        NULLIF(LAG(fm.total_revenue) OVER (ORDER BY fm.metric_date), 0) * 100, 
        0
    ) AS revenue_growth
FROM financial_metrics_enhanced fm
ORDER BY fm.metric_date DESC;

-- Inventory Status View
CREATE OR REPLACE VIEW vw_inventory_status AS
SELECT 
    b.branch_id,
    b.name AS branch_name,
    is.metric_date,
    is.total_items,
    is.low_stock_items,
    is.out_of_stock_items,
    is.inventory_value,
    is.inventory_turnover,
    (is.low_stock_items + is.out_of_stock_items) / NULLIF(is.total_items, 0) * 100 AS stock_alert_percentage
FROM branches b
LEFT JOIN inventory_status is ON b.branch_id = is.branch_id
ORDER BY is.metric_date DESC;

-- Enhanced Stored Procedures

DELIMITER //

-- Stored procedure to update branch performance metrics
CREATE PROCEDURE sp_update_branch_performance(IN p_date DATE)
BEGIN
    INSERT INTO branch_performance_metrics 
        (branch_id, metric_date, total_sales, order_count, customer_count, profit_margin, inventory_value, employee_count)
    SELECT 
        b.branch_id,
        COALESCE(p_date, CURRENT_DATE) AS metric_date,
        SUM(COALESCE(co.ordertotal, 0)) AS total_sales,
        COUNT(DISTINCT co.orderid) AS order_count,
        COUNT(DISTINCT co.customername) AS customer_count,
        CASE 
            WHEN SUM(COALESCE(co.ordertotal, 0)) > 0 
            THEN (SUM(COALESCE(co.ordertotal, 0) * 0.25) / SUM(COALESCE(co.ordertotal, 0))) * 100 
            ELSE 0 
        END AS profit_margin,
        SUM(COALESCE(i.availablequantity * p.price, 0)) AS inventory_value,
        COUNT(DISTINCT e.employee_id) AS employee_count
    FROM branches b
    LEFT JOIN customerorder co ON b.branch_id = co.branch_id AND DATE(co.orderdate) = COALESCE(p_date, CURRENT_DATE)
    LEFT JOIN inventory i ON b.branch_id = i.branch_id
    LEFT JOIN products p ON i.productcode = p.productcode
    LEFT JOIN employees e ON b.branch_id = e.branch_id AND e.status = 'active'
    GROUP BY b.branch_id
    ON DUPLICATE KEY UPDATE
        total_sales = VALUES(total_sales),
        order_count = VALUES(order_count),
        customer_count = VALUES(customer_count),
        profit_margin = VALUES(profit_margin),
        inventory_value = VALUES(inventory_value),
        employee_count = VALUES(employee_count),
        updated_at = CURRENT_TIMESTAMP;
END//

-- Stored procedure to update product performance metrics
CREATE PROCEDURE sp_update_product_performance(IN p_date DATE)
BEGIN
    INSERT INTO product_performance_metrics 
        (product_id, category_id, metric_date, units_sold, revenue, profit)
    SELECT 
        p.productcode AS product_id,
        p.category_id,
        COALESCE(p_date, CURRENT_DATE) AS metric_date,
        SUM(COALESCE(ol.quantity, 0)) AS units_sold,
        SUM(COALESCE(ol.quantity * ol.unit_price, 0)) AS revenue,
        SUM(COALESCE(ol.quantity * (ol.unit_price - p.unit_price), 0)) AS profit
    FROM products p
    LEFT JOIN orderlog ol ON p.productcode = ol.productcode 
    LEFT JOIN customerorder co ON ol.orderid = co.orderid AND DATE(co.orderdate) = COALESCE(p_date, CURRENT_DATE)
    GROUP BY p.productcode, p.category_id
    ON DUPLICATE KEY UPDATE
        units_sold = VALUES(units_sold),
        revenue = VALUES(revenue),
        profit = VALUES(profit),
        updated_at = CURRENT_TIMESTAMP;
END//

-- Stored procedure to update category performance metrics
CREATE PROCEDURE sp_update_category_performance(IN p_date DATE)
BEGIN
    INSERT INTO category_performance_metrics 
        (category_id, metric_date, units_sold, revenue, profit)
    SELECT 
        c.category_id,
        COALESCE(p_date, CURRENT_DATE) AS metric_date,
        SUM(COALESCE(ol.quantity, 0)) AS units_sold,
        SUM(COALESCE(ol.quantity * ol.unit_price, 0)) AS revenue,
        SUM(COALESCE(ol.quantity * (ol.unit_price - p.unit_price), 0)) AS profit
    FROM categories c
    LEFT JOIN products p ON c.category_id = p.category_id
    LEFT JOIN orderlog ol ON p.productcode = ol.productcode
    LEFT JOIN customerorder co ON ol.orderid = co.orderid AND DATE(co.orderdate) = COALESCE(p_date, CURRENT_DATE)
    GROUP BY c.category_id
    ON DUPLICATE KEY UPDATE
        units_sold = VALUES(units_sold),
        revenue = VALUES(revenue),
        profit = VALUES(profit),
        updated_at = CURRENT_TIMESTAMP;
END//

-- Stored procedure to update financial metrics
CREATE PROCEDURE sp_update_financial_metrics(IN p_date DATE)
BEGIN
    INSERT INTO financial_metrics_enhanced 
        (metric_date, total_revenue, cogs, operating_expenses)
    SELECT 
        COALESCE(p_date, CURRENT_DATE) AS metric_date,
        SUM(COALESCE(co.ordertotal, 0)) AS total_revenue,
        SUM(COALESCE(ol.quantity * p.unit_price, 0)) AS cogs,
        (SELECT SUM(salary) FROM employees WHERE status = 'active') + 
        (SELECT 5000) AS operating_expenses -- Placeholder for fixed costs
    FROM customerorder co
    LEFT JOIN orderlog ol ON co.orderid = ol.orderid
    LEFT JOIN products p ON ol.productcode = p.productcode
    WHERE DATE(co.orderdate) = COALESCE(p_date, CURRENT_DATE)
    ON DUPLICATE KEY UPDATE
        total_revenue = VALUES(total_revenue),
        cogs = VALUES(cogs),
        operating_expenses = VALUES(operating_expenses),
        updated_at = CURRENT_TIMESTAMP;
END//

-- Stored procedure to update inventory status
CREATE PROCEDURE sp_update_inventory_status(IN p_date DATE)
BEGIN
    INSERT INTO inventory_status 
        (branch_id, metric_date, total_items, low_stock_items, out_of_stock_items, inventory_value, inventory_turnover)
    SELECT 
        b.branch_id,
        COALESCE(p_date, CURRENT_DATE) AS metric_date,
        COUNT(i.productcode) AS total_items,
        SUM(CASE WHEN i.availablequantity < 10 AND i.availablequantity > 0 THEN 1 ELSE 0 END) AS low_stock_items,
        SUM(CASE WHEN i.availablequantity = 0 THEN 1 ELSE 0 END) AS out_of_stock_items,
        SUM(i.availablequantity * p.price) AS inventory_value,
        COALESCE(
            (SELECT SUM(ol.quantity) 
             FROM orderlog ol 
             JOIN customerorder co ON ol.orderid = co.orderid 
             WHERE co.branch_id = b.branch_id AND DATE(co.orderdate) BETWEEN DATE_SUB(COALESCE(p_date, CURRENT_DATE), INTERVAL 30 DAY) AND COALESCE(p_date, CURRENT_DATE)
            ) / NULLIF(SUM(i.availablequantity), 0),
            0
        ) AS inventory_turnover
    FROM branches b
    LEFT JOIN inventory i ON b.branch_id = i.branch_id
    LEFT JOIN products p ON i.productcode = p.productcode
    GROUP BY b.branch_id
    ON DUPLICATE KEY UPDATE
        total_items = VALUES(total_items),
        low_stock_items = VALUES(low_stock_items),
        out_of_stock_items = VALUES(out_of_stock_items),
        inventory_value = VALUES(inventory_value),
        inventory_turnover = VALUES(inventory_turnover),
        updated_at = CURRENT_TIMESTAMP;
END//

-- Stored procedure to update all dashboard metrics
CREATE PROCEDURE sp_update_all_dashboard_metrics(IN p_date DATE)
BEGIN
    DECLARE target_date DATE;
    SET target_date = COALESCE(p_date, CURRENT_DATE);
    
    CALL sp_update_branch_performance(target_date);
    CALL sp_update_product_performance(target_date);
    CALL sp_update_category_performance(target_date);
    CALL sp_update_financial_metrics(target_date);
    CALL sp_update_inventory_status(target_date);
    
    -- Log the update
    INSERT INTO activity_logs (user_id, action, description, entity_type, entity_id)
    VALUES (
        (SELECT id FROM users WHERE role = 'system' LIMIT 1),
        'update',
        CONCAT('Updated all dashboard metrics for ', target_date),
        'dashboard_metrics',
        NULL
    );
END//

DELIMITER ;

-- Add indexes to improve query performance
CREATE INDEX IF NOT EXISTS idx_orderlog_productcode ON orderlog(productcode);
CREATE INDEX IF NOT EXISTS idx_customerorder_orderdate ON customerorder(orderdate);
CREATE INDEX IF NOT EXISTS idx_inventory_availability ON inventory(availablequantity);
CREATE INDEX IF NOT EXISTS idx_employee_status ON employees(status);
CREATE INDEX IF NOT EXISTS idx_product_category ON products(category_id);

-- Commit transaction
COMMIT; 