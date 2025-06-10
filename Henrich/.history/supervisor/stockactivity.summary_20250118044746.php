<?php
// Get today's statistics
$today = date('Y-m-d');
$todayStats = $conn->query("SELECT 
    COUNT(*) as total_activities,
    SUM(CASE WHEN activity_type = 'add' THEN 1 ELSE 0 END) as additions,
    SUM(CASE WHEN activity_type = 'remove' THEN 1 ELSE 0 END) as removals,
    SUM(CASE WHEN activity_type = 'update' THEN 1 ELSE 0 END) as updates,
    SUM(CASE WHEN activity_type = 'transfer' THEN 1 ELSE 0 END) as transfers
    FROM stockactivitylog 
    WHERE DATE(activity_date) = '$today'")->fetch_assoc();
?>

<div class="activity-stats">
    <div class="stat-card">
        <h3>Today's Activities</h3>
        <div class="stat-value"><?= $todayStats['total_activities'] ?></div>
    </div>
    <div class="stat-details">
        <div class="stat-item">
            <span>Additions:</span>
            <span><?= $todayStats['additions'] ?></span>
        </div>
        <div class="stat-item">
            <span>Removals:</span>
            <span><?= $todayStats['removals'] ?></span>
        </div>
        <div class="stat-item">
            <span>Updates:</span>
            <span><?= $todayStats['updates'] ?></span>
        </div>
        <div class="stat-item">
            <span>Transfers:</span>
            <span><?= $todayStats['transfers'] ?></span>
        </div>
    </div>
</div>
