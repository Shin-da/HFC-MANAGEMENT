DELIMITER //

DROP PROCEDURE IF EXISTS sp_branch_performance //

CREATE PROCEDURE sp_branch_performance(
    IN p_start_date DATE,
    IN p_end_date DATE,
    IN p_region VARCHAR(50)
)
BEGIN
    -- Declare variables
    DECLARE total_branches INT;
    DECLARE active_branches INT;
    DECLARE avg_daily_revenue DECIMAL(16,2);
    DECLARE network_revenue DECIMAL(16,2);
    
    -- Calculate total days in date range for daily averages
    DECLARE days_in_range INT;
    SET days_in_range = DATEDIFF(p_end_date, p_start_date) + 1;
    
    -- Get branch counts
    SELECT 
        COUNT(*) AS total,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS active
    INTO
        total_branches, active_branches
    FROM 
        branches
    WHERE
        (p_region = 'all' OR region = p_region);
    
    -- Get revenue metrics
    SELECT 
        COALESCE(SUM(total_amount), 0) AS total_revenue,
        COALESCE(SUM(total_amount) / days_in_range, 0) AS daily_avg
    INTO
        network_revenue, avg_daily_revenue
    FROM 
        customerorder
    WHERE 
        order_date BETWEEN p_start_date AND p_end_date
        AND status != 'cancelled'
        AND (p_region = 'all' OR branch_id IN (
            SELECT id FROM branches WHERE region = p_region
        ));
    
    -- Return branch metrics
    SELECT 
        total_branches,
        active_branches,
        avg_daily_revenue,
        network_revenue;
    
    -- Return detailed branch performance data
    SELECT 
        b.id,
        b.branch_name,
        b.region,
        b.province,
        b.city,
        b.status,
        COALESCE(SUM(o.total_amount), 0) AS revenue,
        COUNT(o.id) AS orders,
        COALESCE(SUM(o.profit), 0) AS profit,
        -- Calculate performance score based on weighted metrics
        CASE 
            WHEN b.status = 'inactive' THEN 0
            ELSE 
                ROUND(
                    (LEAST(COALESCE(SUM(o.total_amount), 0) / 500000 * 100, 100) * 0.4) + 
                    (LEAST(COUNT(o.id) / 300 * 100, 100) * 0.3) +
                    (LEAST(COALESCE(SUM(o.profit), 0) / 200000 * 100, 100) * 0.3)
                ) 
        END AS performance_score
    FROM 
        branches b
    LEFT JOIN 
        customerorder o ON b.id = o.branch_id 
        AND o.order_date BETWEEN p_start_date AND p_end_date
        AND o.status != 'cancelled'
    WHERE 
        (p_region = 'all' OR b.region = p_region)
    GROUP BY 
        b.id, b.branch_name, b.region, b.province, b.city, b.status
    ORDER BY 
        performance_score DESC, revenue DESC;
    
    -- Return branch trends (compared to previous period of equal length)
    SELECT
        COALESCE(
            (
                (current.total_revenue - previous.total_revenue) / 
                NULLIF(previous.total_revenue, 0) * 100
            ),
            0
        ) AS revenue_trend,
        COALESCE(
            (
                (current.total_orders - previous.total_orders) / 
                NULLIF(previous.total_orders, 0) * 100
            ),
            0
        ) AS orders_trend,
        COALESCE(
            (
                (current.active_branches - previous.active_branches) / 
                NULLIF(previous.active_branches, 0) * 100
            ),
            0
        ) AS branches_trend
    FROM
        (
            -- Current period metrics
            SELECT 
                COUNT(DISTINCT CASE WHEN b.status = 'active' THEN b.id ELSE NULL END) AS active_branches,
                COALESCE(SUM(o.total_amount), 0) AS total_revenue,
                COUNT(DISTINCT o.id) AS total_orders
            FROM 
                branches b
            LEFT JOIN 
                customerorder o ON b.id = o.branch_id 
                AND o.order_date BETWEEN p_start_date AND p_end_date
                AND o.status != 'cancelled'
            WHERE 
                (p_region = 'all' OR b.region = p_region)
        ) AS current,
        (
            -- Previous period metrics (same duration)
            SELECT 
                COUNT(DISTINCT CASE WHEN b.status = 'active' THEN b.id ELSE NULL END) AS active_branches,
                COALESCE(SUM(o.total_amount), 0) AS total_revenue,
                COUNT(DISTINCT o.id) AS total_orders
            FROM 
                branches b
            LEFT JOIN 
                customerorder o ON b.id = o.branch_id 
                AND o.order_date BETWEEN DATE_SUB(p_start_date, INTERVAL days_in_range DAY) AND DATE_SUB(p_end_date, INTERVAL days_in_range DAY)
                AND o.status != 'cancelled'
            WHERE 
                (p_region = 'all' OR b.region = p_region)
        ) AS previous;
END //

DELIMITER ; 