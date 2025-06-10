DELIMITER //

-- Daily metrics aggregation
CREATE PROCEDURE sp_aggregate_daily_metrics()
BEGIN
    INSERT INTO financial_metrics (period_date, total_revenue, net_profit, operating_costs)
    SELECT 
        CURRENT_DATE,
        SUM(ol.quantity * ol.unit_price) as revenue,
        SUM(ol.quantity * (ol.unit_price - p.unit_price)) as profit,
        (SELECT SUM(availablequantity * unit_price) * 0.1 FROM inventory) as costs
    FROM orderlog ol
    JOIN products p ON ol.productcode = p.productcode
    WHERE DATE(ol.orderdate) = CURRENT_DATE
    ON DUPLICATE KEY UPDATE
        total_revenue = VALUES(total_revenue),
        net_profit = VALUES(net_profit),
        operating_costs = VALUES(operating_costs);
END//

-- Performance metrics calculation
CREATE PROCEDURE sp_calculate_performance()
BEGIN
    INSERT INTO department_performance (department, performance_score, target_achievement, period_date)
    SELECT 
        'Sales',
        (COUNT(*) * 100.0) / MAX(target_orders) as score,
        SUM(ordertotal) / MAX(target_revenue) * 100 as achievement,
        CURRENT_DATE
    FROM customerorder
    CROSS JOIN (
        SELECT 100 as target_orders, 1000000 as target_revenue
    ) as targets
    WHERE orderdate = CURRENT_DATE;
END//

DELIMITER ;
