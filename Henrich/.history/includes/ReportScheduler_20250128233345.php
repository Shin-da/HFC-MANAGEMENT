<?php
class ReportScheduler {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function scheduleReport($type, $frequency, $recipients) {
        $nextRun = $this->calculateNextRun($frequency);
        
        $stmt = $this->conn->prepare("
            INSERT INTO report_schedules 
            (report_type, frequency, recipients, next_run, created_by)
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $stmt->bind_param('ssssi', 
            $type, 
            $frequency, 
            $recipients, 
            $nextRun, 
            $_SESSION['user_id']
        );
        
        return $stmt->execute();
    }

    private function calculateNextRun($frequency) {
        $now = new DateTime();
        switch ($frequency) {
            case 'daily':
                return $now->modify('+1 day')->format('Y-m-d 06:00:00');
            case 'weekly':
                return $now->modify('next monday')->format('Y-m-d 06:00:00');
            case 'monthly':
                return $now->modify('first day of next month')->format('Y-m-d 06:00:00');
        }
    }
}
