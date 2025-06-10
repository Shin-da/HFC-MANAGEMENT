-- Create employees table
CREATE TABLE employees (
    employee_id INT PRIMARY KEY AUTO_INCREMENT,
    branch_id INT NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    position VARCHAR(50) NOT NULL,
    department VARCHAR(50) NOT NULL,
    hire_date DATE NOT NULL,
    salary DECIMAL(10,2) NOT NULL,
    status ENUM('active', 'inactive', 'on_leave') DEFAULT 'active',
    FOREIGN KEY (branch_id) REFERENCES branches(branch_id),
    INDEX idx_emp_branch (branch_id),
    INDEX idx_emp_status (status)
);

-- Employee performance tracking
CREATE TABLE employee_performance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    employee_id INT NOT NULL,
    evaluation_date DATE NOT NULL,
    performance_score DECIMAL(5,2) NOT NULL,
    attendance_rate DECIMAL(5,2) NOT NULL,
    productivity_score DECIMAL(5,2) NOT NULL,
    evaluated_by INT NOT NULL,
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id),
    FOREIGN KEY (evaluated_by) REFERENCES users(user_id),
    INDEX idx_emp_eval (employee_id, evaluation_date)
);

-- Employee attendance tracking
CREATE TABLE employee_attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    employee_id INT NOT NULL,
    date DATE NOT NULL,
    time_in DATETIME,
    time_out DATETIME,
    status ENUM('present', 'absent', 'late', 'leave') NOT NULL,
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id),
    UNIQUE KEY unique_daily_attendance (employee_id, date)
);

-- Update views
CREATE OR REPLACE VIEW vw_employee_analytics AS
SELECT 
    e.employee_id,
    e.first_name,
    e.last_name,
    e.position,
    e.department,
    b.branch_name,
    COUNT(DISTINCT co.orderid) as orders_handled,
    SUM(co.ordertotal) as total_sales,
    ep.performance_score,
    (SELECT COUNT(*) FROM employee_attendance ea 
     WHERE ea.employee_id = e.employee_id 
     AND ea.status = 'present') as attendance_days
FROM employees e
LEFT JOIN branches b ON e.branch_id = b.branch_id
LEFT JOIN customerorder co ON e.employee_id = co.processed_by
LEFT JOIN employee_performance ep ON e.employee_id = ep.employee_id
WHERE e.status = 'active'
GROUP BY e.employee_id;

-- Insert sample data for testing
INSERT INTO employees (branch_id, first_name, last_name, position, department, hire_date, salary) 
VALUES 
(1, 'Juan', 'Dela Cruz', 'Sales Manager', 'Sales', '2023-01-01', 50000.00),
(1, 'Maria', 'Santos', 'Inventory Specialist', 'Operations', '2023-01-15', 35000.00);
