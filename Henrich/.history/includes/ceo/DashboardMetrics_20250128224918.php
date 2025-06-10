<?php
class DashboardMetrics {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getOverallPerformance() {
        $query = "SELECT 
            SUM(total_revenue) as total_revenue,
            AVG(avg_order_value) as avg_order_value,
            SUM(unique_customers) as total_customers
        FROM vw_branch_performance";

        $result = $this->conn->query($query);
        return $result->fetch_assoc();
    }

    public function getBranchComparison() {
        $query = "SELECT 
            branch_name,
            total_revenue,
            total_orders,
            unique_customers
        FROM vw_branch_performance
        ORDER BY total_revenue DESC";

        return $this->conn->query($query)->fetch_all(MYSQLI_ASSOC);
    }
}
