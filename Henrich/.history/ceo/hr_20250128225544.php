<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    header('Location: ../login/login.php');
    exit();
}

Page::setTitle('HR Overview - CEO Dashboard');
Page::setBodyClass('ceo-hr');

ob_start(); ?>

<div class="hr-container">
    <div class="page-header">
        <h1>HR Analytics</h1>
        <div class="header-actions">
            <select id="hrPeriod" class="form-select">
                <option value="month">This Month</option>
                <option value="quarter">This Quarter</option>
                <option value="year">This Year</option>
            </select>
            <button id="generateHrReport" class="btn secondary">Generate Report</button>
        </div>
    </div>

    <div class="hr-grid">
        <div class="hr-card workforce">
            <h2>Workforce Overview</h2>
            <div class="metrics-grid">
                <div class="metric">
                    <span class="metric-title">Total Employees</span>
                    <span class="metric-value" id="totalEmployees">0</span>
                </div>
                <div class="metric">
                    <span class="metric-title">Turnover Rate</span>
                    <span class="metric-value" id="turnoverRate">0%</span>
                </div>
                <div class="metric">
                    <span class="metric-title">Employee Satisfaction</span>
                    <span class="metric-value" id="satisfaction">0%</span>
                </div>
            </div>
        </div>

        <div class="hr-card hiring">
            <h2>Recruitment Status</h2>
            <canvas id="recruitmentChart"></canvas>
        </div>

        <div class="hr-card performance">
            <h2>Performance Matrix</h2>
            <div id="performanceMatrix"></div>
        </div>

        <div class="hr-card departments">
            <h2>Department Health</h2>
            <div id="departmentHealthTable"></div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
Page::render($content);
?>
