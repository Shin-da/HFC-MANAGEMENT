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
                    <span class="metric-title">Total Staff</span>
                    <span class="metric-value" id="totalStaff">0</span>
                </div>
                <div class="metric">
                    <span class="metric-title">Active</span>
                    <span class="metric-value" id="activeStaff">0</span>
                </div>
                <div class="metric">
                    <span class="metric-title">Retention Rate</span>
                    <span class="metric-value" id="retentionRate">0%</span>
                </div>
            </div>
        </div>

        <div class="employee-card distribution">
            <h2>Department Distribution</h2>
            <canvas id="departmentChart"></canvas>
        </div>

        <div class="employee-card performance">
            <h2>Performance Overview</h2>
            <canvas id="performanceChart"></canvas>
        </div>

        <div class="employee-card hiring">
            <h2>Hiring Trends</h2>
            <canvas id="hiringChart"></canvas>
        </div>
    </div>

    <div class="employee-table-section">
        <h2>Employee Directory</h2>
        <div class="table-filters">
            <select id="departmentFilter">
                <option value="">All Departments</option>
            </select>
            <select id="statusFilter">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
        <div class="table-container">
            <table id="employeeTable" class="display">
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
Page::render($content);
?>
