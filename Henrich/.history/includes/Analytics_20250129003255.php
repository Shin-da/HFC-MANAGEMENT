<?php
class Analytics {
    private $conn;
    private $startDate;
    private $endDate;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->setDefaultDateRange();
    }

    private function setDefaultDateRange() {
        $this->startDate = date('Y-m-d', strtotime('-30 days'));
        $this->endDate = date('Y-m-d');
    }

    public function getBusinessMetrics() {
        $query = "SELECT 
            (SELECT SUM(ordertotal) FROM customerorder 
             WHERE orderdate BETWEEN ? AND ?) as total_revenue,
            (SELECT COUNT(*) FROM customerorder 
             WHERE orderdate BETWEEN ? AND ?) as total_orders,
            (SELECT COUNT(DISTINCT productcode) FROM inventory) as total_products,
            (SELECT SUM(availablequantity * unit_price) FROM inventory) as inventory_value";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ssss', $this->startDate, $this->endDate, 
                                 $this->startDate, $this->endDate);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getSalesAnalytics() {
        $query = "SELECT 
            DATE_FORMAT(orderdate, '%Y-%m-%d') as date,
            COUNT(*) as order_count,
            SUM(ordertotal) as daily_revenue,
            AVG(ordertotal) as avg_order_value
        FROM customerorder 
        WHERE orderdate BETWEEN ? AND ?
        GROUP BY DATE_FORMAT(orderdate, '%Y-%m-%d')
        ORDER BY date";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ss', $this->startDate, $this->endDate);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
