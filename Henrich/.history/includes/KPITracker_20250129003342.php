<?php
class KPITracker {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function calculateKPIs(): array {
        $kpis = [];

        // Revenue Growth
        $kpis['revenue_growth'] = $this->calculateRevenueGrowth();
        
        // Inventory Turnover
        $kpis['inventory_turnover'] = $this->calculateInventoryTurnover();
        
        // Order Fulfillment
        $kpis['order_fulfillment'] = $this->calculateOrderFulfillment();

        $this->logKPIValues($kpis);
        return $kpis;
    }

    private function calculateRevenueGrowth(): float {
        $query = "SELECT 
            (THIS_MONTH.revenue - LAST_MONTH.revenue) / LAST_MONTH.revenue * 100 as growth_rate
        FROM 
            (SELECT COALESCE(SUM(ordertotal), 0) as revenue 
             FROM customerorder 
             WHERE YEAR(orderdate) = YEAR(CURRENT_DATE) 
             AND MONTH(orderdate) = MONTH(CURRENT_DATE)) THIS_MONTH,
            (SELECT COALESCE(SUM(ordertotal), 0) as revenue 
             FROM customerorder 
             WHERE YEAR(orderdate) = YEAR(DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH))
             AND MONTH(orderdate) = MONTH(DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH))) LAST_MONTH";

        $result = $this->conn->query($query);
        return $result->fetch_assoc()['growth_rate'] ?? 0;
    }

    private function logKPIValues(array $kpis): void {
        foreach ($kpis as $name => $value) {
            $this->logKPI($name, $value);
        }
    }
}
