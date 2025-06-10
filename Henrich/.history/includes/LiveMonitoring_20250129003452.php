<?php
class LiveMonitoring {
    private $conn;
    private $metrics = [];

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getLiveMetrics(): array {
        return [
            'sales' => $this->getLiveSales(),
            'inventory' => $this->getLiveInventory(),
            'orders' => $this->getLiveOrders(),
            'alerts' => $this->getActiveAlerts(),
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }

    private function getLiveSales(): array {
        $query = "SELECT 
            COUNT(*) as today_orders,
            SUM(ordertotal) as today_revenue,
            COUNT(DISTINCT customername) as today_customers
        FROM customerorder 
        WHERE DATE(orderdate) = CURRENT_DATE";
        
        return $this->conn->query($query)->fetch_assoc();
    }

    private function getLiveInventory(): array {
        $query = "SELECT 
            COUNT(*) as total_items,
            SUM(CASE WHEN availablequantity <= 10 THEN 1 ELSE 0 END) as low_stock,
            SUM(availablequantity * unit_price) as total_value
        FROM inventory";
        
        return $this->conn->query($query)->fetch_assoc();
    }
}
