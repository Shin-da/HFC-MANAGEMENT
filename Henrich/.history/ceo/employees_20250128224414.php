<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    header('Location: ../login/login.php');
    exit();
}

Page::setTitle('Employee Overview - CEO Dashboard');
Page::setBodyClass('ceo-employees');

ob_start(); ?>

<div class="employees-container">
    <div class="page-header">
        <h1>Employee Overview</h1>
        <div class="header-actions">
            <button class="btn secondary" id="downloadReport">
                <i class="bx bx-download"></i> Download Report
            </button>
        </div>
    </div>

    <div class="employees-grid">
        <div class="employee-card summary">
            <h2>Workforce Summary</h2>
            <div class="metrics-grid">
                <div class="metric">
            <h2>Workforce Metrics</h2>
            <div class="metrics-container">
                <div class="metric">
                    <span class="metric-value" id="totalStaff">0</span>
                    <span class="metric-label">Total Staff</span>
                </div>
                <div class="metric">
                    <span class="metric-value" id="departments">0</span>
                    <span class="metric-label">Departments</span>
                </div>
                <div class="metric">
                    <span class="metric-value" id="retention">0%</span>
                    <span class="metric-label">Retention Rate</span>
                </div>
            </div>
        </div>

        <div class="employee-card performance">
            <h2>Performance Overview</h2>
            <canvas id="performanceChart"></canvas>
        </div>

        <div class="employee-card directory">
            <h2>Employee Directory</h2>
            <div class="directory-filters">
                <select id="departmentFilter">
                    <option value="">All Departments</option>
                </select>
                <select id="branchFilter">
                    <option value="">All Branches</option>
                </select>
            </div>
            <div class="employee-table-container">
                <table id="employeeTable" class="display">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Position</th>
                            <th>Department</th>
                            <th>Branch</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
Page::render($content);
?>
