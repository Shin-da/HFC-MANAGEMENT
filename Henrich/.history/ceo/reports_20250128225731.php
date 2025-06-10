<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    header('Location: ../login/login.php');
    exit();
}

Page::setTitle('Executive Reports - CEO Dashboard');
Page::setBodyClass('ceo-reports');

ob_start(); ?>

<div class="reports-container">
    <div class="page-header">
        <h1>Executive Reports</h1>
        <div class="header-actions">
            <select id="reportType" class="form-select">
                <option value="consolidated">Consolidated Report</option>
                <option value="financial">Financial Report</option>
                <option value="operational">Operational Report</option>
                <option value="hr">HR Report</option>
            </select>
            <button id="generateReport" class="btn primary">
                <i class="bx bx-file"></i> Generate Report
            </button>
            <div class="report-schedule">
                <button id="scheduleReport" class="btn secondary">
                    <i class="bx bx-time"></i> Schedule Reports 
                </button>
            </div>
        </div>
    </div>

    <div class="reports-grid">
        <div class="recent-reports">
            <h2>Recent Reports</h2>
            <div class="reports-list" id="recentReports"></div>
        </div>

        <div class="report-preview">
            <h2>Report Preview</h2>
            <div id="reportPreview"></div>
        </div>

        <div class="scheduled-reports">
            <h2>Scheduled Reports</h2>
            <div id="scheduledReportsList"></div>
        </div>
    </div>
</div>

<!-- Report Schedule Modal -->
<div id="scheduleModal" class="modal">
    <div class="modal-content">
        <h3>Schedule Report</h3>
        <form id="scheduleForm">
            <div class="form-group">
                <label>Report Type</label>
                <select name="reportType" required>
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                </select>
            </div>
            <div class="form-group">
                <label>Recipients</label>
                <input type="text" name="recipients" placeholder="Email addresses (comma-separated)">
            </div>
            <button type="submit" class="btn primary">Schedule Report</button>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
Page::render($content);
?>
