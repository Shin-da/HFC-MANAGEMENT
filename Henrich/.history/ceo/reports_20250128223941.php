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
        <div class="report-actions">
            <button id="generateReport" class="btn primary">Generate Report</button>
            <button id="exportPDF" class="btn secondary">Export to PDF</button>
        </div>
    </div>

    <div class="reports-grid">
        <div class="report-card sales-report">
            <h2>Sales Performance Report</h2>
            <div class="report-content" id="salesReport"></div>
        </div>

        <div class="report-card inventory-report">
            <h2>Inventory Status Report</h2>
            <div class="report-content" id="inventoryReport"></div>
        </div>

        <div class="report-card employee-report">
            <h2>Employee Performance Report</h2>
            <div class="report-content" id="employeeReport"></div>
        </div>

        <div class="report-card customer-report">
            <h2>Customer Analytics Report</h2>
            <div class="report-content" id="customerReport"></div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
Page::render($content);
?>
