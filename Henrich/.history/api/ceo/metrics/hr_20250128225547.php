<?php
require_once '../../../includes/config.php';
require_once '../../../includes/session.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized access']));
}

$period = $_GET['period'] ?? 'month';

try {
    $response = [
        'workforce' => getWorkforceMetrics($conn, $period),
        'recruitment' => getRecruitmentMetrics($conn),
        'performance' => getPerformanceMatrix($conn),
        'departments' => getDepartmentHealth($conn)
    ];
    
    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function getWorkforceMetrics($conn, $period) {
    $query = "SELECT 
        COUNT(*) as total_employees,
        (SELECT COUNT(*) FROM employee_exits WHERE exit_date >= DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH)) / COUNT(*) * 100 as turnover_rate,
        (SELECT AVG(satisfaction_score) FROM employee_surveys WHERE survey_date >= DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH)) as satisfaction
    FROM employees
    WHERE status = 'active'";
    
    $result = $conn->query($query);
    return $result->fetch_assoc();
}
