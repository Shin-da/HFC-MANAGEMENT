-- Financial Metrics Table
CREATE TABLE financial_metrics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    period_date DATE NOT NULL,
    total_revenue DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    net_profit DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    operating_costs DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sales Performance Table
CREATE TABLE sales_performance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    date DATE NOT NULL,
    total_sales DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    orders_count INT NOT NULL DEFAULT 0,
    average_order_value DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    customer_count INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_date (date)
);

-- Department Performance Table
CREATE TABLE department_performance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    department VARCHAR(50) NOT NULL,
    performance_score DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    target_achievement DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    period_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_dept_date (department, period_date)
);

-- KPI Metrics Table
CREATE TABLE kpi_metrics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    metric_name VARCHAR(50) NOT NULL,
    metric_value DECIMAL(10,2) NOT NULL,
    target_value DECIMAL(10,2) NOT NULL,
    period_date DATE NOT NULL,
    status ENUM('exceeded', 'met', 'below') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_metric_date (metric_name, period_date)
);

-- Views for aggregated data
CREATE VIEW vw_monthly_performance AS
SELECT 
    DATE_FORMAT(co.orderdate, '%Y-%m') as period,
    COUNT(DISTINCT co.orderid) as total_orders,
    SUM(ol.quantity * ol.unit_price) as total_revenue,
    COUNT(DISTINCT co.customername) as unique_customers,
    AVG(ol.quantity * ol.unit_price) as avg_order_value
FROM customerorder co
JOIN orderlog ol ON co.orderid = ol.orderid
GROUP BY DATE_FORMAT(co.orderdate, '%Y-%m');

-- View for inventory value
CREATE VIEW vw_inventory_value AS
SELECT 
    i.productcategory,
    SUM(i.availablequantity * i.unit_price) as inventory_value,
    COUNT(DISTINCT i.productcode) as product_count,
    SUM(i.availablequantity) as total_quantity
FROM inventory i
GROUP BY i.productcategory;
