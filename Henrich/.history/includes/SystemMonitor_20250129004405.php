<?php
class SystemMonitor {
    private $conn;
    private $activityLimit = 50;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getServerStatus(): array {
        return [
            'status' => 'online',
            'uptime' => $this->getServerUptime(),
            'memory_usage' => $this->getMemoryUsage(),
            'cpu_load' => $this->getCPULoad()
        ];
    }

    public function getActiveUsers(): array {
        $query = "SELECT 
            u.username,
            u.role,
            s.last_activity,
            s.ip_address
        FROM users u
        JOIN active_sessions s ON u.user_id = s.user_id
        WHERE s.last_activity >= DATE_SUB(NOW(), INTERVAL 15 MINUTE)
        ORDER BY s.last_activity DESC";

        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    private function getServerUptime(): string {
        if (PHP_OS === 'Linux') {
            $uptime = shell_exec('uptime -p');
            return trim($uptime);
        }
        return 'Unavailable';
    }

    private function getMemoryUsage(): array {
        $memory = [
            'total' => memory_get_peak_usage(true),
            'used' => memory_get_usage(true)
        ];
        $memory['percentage'] = round(($memory['used'] / $memory['total']) * 100, 2);
        return $memory;
    }
}
