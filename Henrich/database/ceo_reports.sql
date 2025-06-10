-- Executive reports table
CREATE TABLE executive_reports (
    report_id INT PRIMARY KEY AUTO_INCREMENT,
    report_type VARCHAR(50) NOT NULL,
    report_title VARCHAR(255) NOT NULL,
    report_data JSON,
    generated_by INT,
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    download_count INT DEFAULT 0,
    FOREIGN KEY (generated_by) REFERENCES users(user_id),
    INDEX idx_report_type (report_type, generated_at)
);

-- Report schedules table
CREATE TABLE report_schedules (
    schedule_id INT PRIMARY KEY AUTO_INCREMENT,
    report_type VARCHAR(50) NOT NULL,
    frequency ENUM('daily', 'weekly', 'monthly') NOT NULL,
    recipients TEXT NOT NULL,
    last_sent TIMESTAMP NULL,
    next_run TIMESTAMP NOT NULL,
    created_by INT,
    status ENUM('active', 'paused') DEFAULT 'active',
    FOREIGN KEY (created_by) REFERENCES users(user_id),
    INDEX idx_next_run (next_run, status)
);

-- Create procedure for generating reports
DELIMITER //
CREATE PROCEDURE sp_generate_executive_report(
    IN p_report_type VARCHAR(50),
    IN p_user_id INT
)
BEGIN
    DECLARE report_data JSON;
    
    CASE p_report_type
        WHEN 'financial' THEN
            SET report_data = (
                SELECT JSON_OBJECT(
                    'revenue', (
                        SELECT JSON_ARRAYAGG(
                            JSON_OBJECT(
                                'date', DATE_FORMAT(orderdate, '%Y-%m-%d'),
                                'total', ordertotal
                            )
                        )
                        FROM customerorder 
                        WHERE orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
                    ),
                    'inventory_value', (
                        SELECT SUM(availablequantity * unit_price)
                        FROM inventory
                    )
                )
            );
            
        WHEN 'operations' THEN
            SET report_data = (
                SELECT JSON_OBJECT(
                    'orders', (
                        SELECT COUNT(*) 
                        FROM customerorder 
                        WHERE orderdate = CURRENT_DATE
                    ),
                    'inventory', (
                        SELECT JSON_ARRAYAGG(
                            JSON_OBJECT(
                                'category', productcategory,
                                'stock_value', SUM(availablequantity * unit_price)
                            )
                        )
                        FROM inventory
                        GROUP BY productcategory
                    )
                )
            );
    END CASE;

    INSERT INTO executive_reports (report_type, report_title, report_data, generated_by)
    VALUES (
        p_report_type, 
        CONCAT(UPPER(p_report_type), ' Report - ', DATE_FORMAT(NOW(), '%Y-%m-%d')),
        report_data,
        p_user_id
    );
END //
DELIMITER ;
