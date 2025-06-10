<?php
class CEONotifications {
    private $conn;
    private $priorityLevels = ['critical', 'high', 'medium', 'low'];

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAlerts(): array {
        $query = "SELECT 
            n.id,
            n.message,
            n.priority,
            n.created_at,
            n.is_read,
            n.category
        FROM ceo_notifications n
        WHERE n.expiry_date > CURRENT_TIMESTAMP
        ORDER BY 
            FIELD(n.priority, 'critical', 'high', 'medium', 'low'),
            n.created_at DESC
        LIMIT 10";

        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function generateAlerts(): void {
        $this->checkInventoryAlerts();
        $this->checkSalesAlerts();
        $this->checkFinancialAlerts();
        $this->checkEmployeeAlerts();
    }

    private function checkInventoryAlerts(): void {
        $query = "INSERT INTO ceo_notifications (message, priority, category)
            SELECT 
                CONCAT('Low stock alert: ', productname, ' (', availablequantity, ' units remaining)'),
                CASE 
                    WHEN availablequantity = 0 THEN 'critical'
                    WHEN availablequantity <= 5 THEN 'high'
                    ELSE 'medium'
                END,
                'inventory'
            FROM inventory
            WHERE availablequantity <= 10
            AND NOT EXISTS (
                SELECT 1 FROM ceo_notifications 
                WHERE category = 'inventory' 
                AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
            )";

        $this->conn->query($query);
    }
}
