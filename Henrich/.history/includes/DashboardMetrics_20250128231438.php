<?php
class DashboardMetrics {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getSalesMetrics($period = '30') {
        $query = "SELECT 
            SUM(daily_revenue) as total_revenue,
            SUM(orders_count) as total_orders,
            COUNT(DISTINCT sale_date) as active_days,
            AVG(daily_revenue) as avg_daily_revenue
        FROM vw_sales_performance
        WHERE sale_date >= DATE_SUB(CURRENT_DATE, INTERVAL ? DAY)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $period);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getInventoryMetrics() {
        $query = "SELECT 
            SUM(inventory_value) as total_value,
            SUM(product_count) as total_products,
            productcategory,
            SUM(total_quantity) as total_stock
        FROM vw_inventory_value
        GROUP BY productcategory";
        
        return $this->conn->query($query)->fetch_all(MYSQLI_ASSOC);
    }

    public function getTopProducts($limit = 5) {
        $query = "SELECT 
            productname,
            total_quantity_sold,
            total_revenue,
            current_stock
        FROM vw_product_performance
        ORDER BY total_revenue DESC
        LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
