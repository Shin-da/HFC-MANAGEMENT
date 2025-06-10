DELIMITER //

DROP PROCEDURE IF EXISTS sp_get_branch_metrics //

CREATE PROCEDURE sp_get_branch_metrics(
    IN p_start_date DATE,
    IN p_end_date DATE
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
        branches;
    
    -- Get revenue metrics
    SELECT 
        COALESCE(SUM(ordertotal), 0) AS total_revenue,
        COALESCE(SUM(ordertotal) / days_in_range / active_branches, 0) AS daily_avg
    INTO
        network_revenue, avg_daily_revenue
    FROM 
        customerorder
    WHERE 
        orderdate BETWEEN p_start_date AND p_end_date
        AND status != 'cancelled';
    
    -- Return branch metrics
    SELECT 
        total_branches,
        active_branches,
        avg_daily_revenue,
        network_revenue;
    
END //

DELIMITER ; 