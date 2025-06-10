<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    header('Location: ../login/login.php');
    exit();
}

Page::setTitle('System Monitor - CEO Dashboard');
Page::setBodyClass('ceo-monitor');

ob_start(); ?>

<div class="monitor-container">
    <div class="page-header">
        <h1>System Monitor</h1>
        <div class="header-actions">
            <select id="refreshInterval" class="form-select">
                <option value="5000">5 seconds</option>
                <option value="15000" selected>15 seconds</option>
                <option value="30000">30 seconds</option>
            </select>
            <button id="pauseMonitoring" class="btn secondary">
                <i class="bx bx-pause"></i> Pause
            </button>
        </div>
    </div>

    <div class="monitor-grid">
        <div class="monitor-card system-status">
            <h2>System Status</h2>
            <div class="status-indicators">
                <div class="status-item" id="serverStatus">
                    <i class="bx bx-server"></i>
                    <span class="status-label">Server</span>
                    <span class="status-value">Checking...</span>
                </div>
                <div class="status-item" id="databaseStatus">
                    <i class="bx bx-data"></i>
                    <span class="status-label">Database</span>
                    <span class="status-value">Checking...</span>
                </div>
            </div>
        </div>

        <div class="monitor-card active-users">
            <h2>Active Users</h2>
            <div id="activeUsersList"></div>
        </div>

        <div class="monitor-card performance">
            <h2>System Performance</h2>
            <canvas id="performanceChart"></canvas>
        </div>

        <div class="monitor-card activity-log">
            <h2>Recent Activity</h2>
            <div id="activityStream"></div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
Page::render($content);
?>
