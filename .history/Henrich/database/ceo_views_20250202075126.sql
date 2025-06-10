-- Create views for CEO dashboard

DROP VIEW IF EXISTS vw_branch_performance;
CREATE VIEW vw_branch_performance AS
SELECT 
    b.branch_id,
    b.branch_name,
    COUNT(co.orderid) as total_orders,
    SUM(co.ordertotal) as total_revenue,
    AVG(co.ordertotal) as avg_order_value,
    COUNT(DISTINCT co.customername) as unique_customers,
    b.status as branch_status
FROM branches b
LEFT JOIN customerorder co ON b.branch_id = co.branch_id
WHERE co.orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
    OR co.orderdate IS NULL
GROUP BY b.branch_id;

CREATE OR REPLACE VIEW vw_employee_performance AS
SELECT 
    e.employee_id,
    e.first_name,
    e.last_name,
    e.branch_id,
    COUNT(o.order_id) as orders_processed,
    SUM(o.total_amount) as total_sales
FROM employees e
LEFT JOIN orders o ON e.employee_id = o.processed_by
WHERE o.order_date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
GROUP BY e.employee_id;

CREATE OR REPLACE VIEW vw_inventory_status AS
SELECT 
    b.branch_id,
    b.branch_name,
    COUNT(p.product_id) as total_products,
    SUM(CASE WHEN p.stock_level <= p.reorder_point THEN 1 ELSE 0 END) as low_stock_items
FROM branches b
LEFT JOIN products p ON b.branch_id = p.branch_id
GROUP BY b.branch_id;

-- Branch inventory status view
CREATE OR REPLACE VIEW vw_branch_inventory_status AS
SELECT 
    b.branch_id,
    b.branch_name,
    COUNT(bi.productcode) as total_products,
    SUM(CASE WHEN bi.available_quantity <= bi.reorder_point THEN 1 ELSE 0 END) as low_stock_items,
    SUM(bi.available_quantity * p.unit_price) as inventory_value
FROM branches b
LEFT JOIN branch_inventory bi ON b.branch_id = bi.branch_id
LEFT JOIN products p ON bi.productcode = p.productcode
GROUP BY b.branch_id;

-- Branch sales performance view
CREATE OR REPLACE VIEW vw_branch_sales_performance AS
SELECT 
    b.branch_id,
    b.branch_name,
    DATE_FORMAT(co.orderdate, '%Y-%m') as month,
    COUNT(co.orderid) as total_orders,
    SUM(co.ordertotal) as monthly_revenue,
    COUNT(DISTINCT co.customername) as unique_customers
FROM branches b
LEFT JOIN customerorder co ON b.branch_id = co.branch_id
WHERE co.orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 12 MONTH)
GROUP BY b.branch_id, DATE_FORMAT(co.orderdate, '%Y-%m')
ORDER BY b.branch_id, month;

-- Sales Performance View
CREATE VIEW vw_sales_performance AS
SELECT 
    DATE_FORMAT(co.orderdate, '%Y-%m-%d') as sale_date,
    COUNT(DISTINCT co.orderid) as orders_count,
    SUM(ol.quantity * ol.unit_price) as daily_revenue,
    COUNT(DISTINCT co.customername) as unique_customers,
    SUM(ol.quantity) as units_sold,
    AVG(ol.unit_price) as avg_unit_price
FROM customerorder co
JOIN orderlog ol ON co.orderid = ol.orderid
GROUP BY DATE_FORMAT(co.orderdate, '%Y-%m-%d');

-- Product Performance View
CREATE VIEW vw_product_performance AS
SELECT 
    p.productcode,
    p.productname,
    p.productcategory,
    COUNT(ol.orderid) as order_count,
    SUM(ol.quantity) as total_quantity_sold,
    SUM(ol.quantity * ol.unit_price) as total_revenue,
    i.availablequantity as current_stock
FROM products p
LEFT JOIN orderlog ol ON p.productcode = ol.productcode
LEFT JOIN inventory i ON p.productcode = i.productcode
GROUP BY p.productcode;

-- Stock Movement Analysis View
CREATE VIEW vw_stock_movement AS
SELECT 
    sm.productcode,
    p.productname,
    p.productcategory,
    SUM(CASE WHEN sm.movement_type = 'IN' THEN sm.totalpacks ELSE 0 END) as total_in,
    SUM(CASE WHEN sm.movement_type = 'OUT' THEN sm.totalpacks ELSE 0 END) as total_out,
    COUNT(DISTINCT sm.batchid) as batch_count,
    MAX(sm.dateencoded) as last_movement
FROM stockmovement sm
JOIN products p ON sm.productcode = p.productcode
GROUP BY sm.productcode;

-- Customer Insights View
CREATE VIEW vw_customer_insights AS
SELECT 
    co.customername,
    COUNT(DISTINCT co.orderid) as order_count,
    SUM(ol.quantity * ol.unit_price) as total_spent,
    AVG(ol.quantity * ol.unit_price) as avg_order_value,
    MAX(co.orderdate) as last_order_date,
    MIN(co.orderdate) as first_order_date
FROM customerorder co
JOIN orderlog ol ON co.orderid = ol.orderid
GROUP BY co.customername;
