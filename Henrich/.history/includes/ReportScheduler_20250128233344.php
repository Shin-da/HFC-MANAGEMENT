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
