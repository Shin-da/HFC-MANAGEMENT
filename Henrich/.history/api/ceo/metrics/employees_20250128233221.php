<?php
require_once '../../../includes/config.php';
require_once '../../../includes/session.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized access']));
}

try {
    $response = [
        'overview' => getEmployeeOverview($conn),
        'performance' => getDepartmentPerformance($conn),
        'attendance' => getAttendanceMetrics($conn),
        'topPerformers' => getTopPerformers($conn)
    ];
    
    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function getEmployeeOverview($conn) {
    $query = "SELECT 
        COUNT(*) as total_employees,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_employees,
        COUNT(DISTINCT department) as departments,
        AVG(salary) as avg_salary
    FROM employees";
    
    $result = $conn->query($query);
    return $result->fetch_assoc();
}

function getTopPerformers($conn) {
    $query = "SELECT 
        e.first_name,
        e.last_name,
        e.position,
        ep.performance_score,
        COUNT(co.orderid) as orders_handled,
        SUM(co.ordertotal) as sales_generated
    FROM employees e
    LEFT JOIN employee_performance ep ON e.employee_id = ep.employee_id
    LEFT JOIN customerorder co ON e.employee_id = co.processed_by
    WHERE e.status = 'active'
    GROUP BY e.employee_id
    ORDER BY ep.performance_score DESC
    LIMIT 5";
    
    return $conn->query($query)->fetch_all(MYSQLI_ASSOC);
}
