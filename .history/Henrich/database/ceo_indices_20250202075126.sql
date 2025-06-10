-- Performance optimization indices
ALTER TABLE customerorder ADD INDEX idx_order_date_total (orderdate, ordertotal);
ALTER TABLE orderlog ADD INDEX idx_product_metrics (productcode, quantity, unit_price);
ALTER TABLE inventory ADD INDEX idx_stock_value (productcode, availablequantity, unit_price);
ALTER TABLE stockmovement ADD INDEX idx_movement_analysis (productcode, movement_type, totalpacks);

-- Composite indices for faster aggregation
ALTER TABLE financial_metrics ADD INDEX idx_financial_period (period_date, total_revenue);
ALTER TABLE sales_performance ADD INDEX idx_sales_metrics (date, total_sales, orders_count);
ALTER TABLE kpi_metrics ADD INDEX idx_kpi_tracking (metric_name, period_date, status);
