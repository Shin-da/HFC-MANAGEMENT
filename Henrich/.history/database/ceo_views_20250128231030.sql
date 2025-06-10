-- Create views for CEO dashboard

CREATE OR REPLACE VIEW vw_branch_performance AS
SELECT 
    b.branch_id,
    b.branch_name,
    COUNT(o.order_id) as total_orders,
    SUM(o.total_amount) as total_revenue,
    AVG(o.total_amount) as avg_order_value,
    COUNT(DISTINCT o.customer_id) as unique_customers
FROM branches b
LEFT JOIN orders o ON b.branch_id = o.branch_id
WHERE o.order_date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
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