CREATE TABLE kpi_definitions (
    kpi_id INT PRIMARY KEY AUTO_INCREMENT,
    kpi_name VARCHAR(50) NOT NULL,
    description TEXT,
    target_value DECIMAL(10,2),
    warning_threshold DECIMAL(5,2),
    critical_threshold DECIMAL(5,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_kpi (kpi_name)
);

CREATE TABLE kpi_logs (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    kpi_id INT NOT NULL,
    value DECIMAL(10,2) NOT NULL,
    status ENUM('exceeded', 'met', 'warning', 'critical') NOT NULL,
    logged_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kpi_id) REFERENCES kpi_definitions(kpi_id),
    INDEX idx_kpi_time (kpi_id, logged_at)
);

-- Insert default KPIs
INSERT INTO kpi_definitions 
(kpi_name, description, target_value, warning_threshold, critical_threshold) 
VALUES 
('revenue_growth', 'Monthly Revenue Growth Rate', 10.00, 5.00, 0.00),
('inventory_turnover', 'Inventory Turnover Rate', 12.00, 8.00, 6.00),
('order_fulfillment', 'Order Fulfillment Rate', 95.00, 90.00, 85.00),
('customer_satisfaction', 'Customer Satisfaction Score', 90.00, 80.00, 70.00);
