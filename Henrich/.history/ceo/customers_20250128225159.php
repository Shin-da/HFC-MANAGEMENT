<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    header('Location: ../login/login.php');
    exit();
}

Page::setTitle('Customer Analytics - CEO Dashboard');
Page::setBodyClass('ceo-customers');

ob_start(); ?>

<div class="customers-container">
    <div class="page-header">
        <h1>Customer Analytics</h1>
        <div class="header-actions">
            <select id="analysisRange" class="form-select">
                <option value="30">Last 30 Days</option>
                <option value="90">Last Quarter</option>
                <option value="365">Last Year</option>
            </select>
            <button id="exportCustomerReport" class="btn secondary">
                <i class="bx bx-download"></i> Export Analysis
            </button>
        </div>
    </div>

    <div class="analytics-grid">
        <div class="analytics-card metrics">
            <h2>Customer Metrics</h2>
            <div class="metrics-grid">
                <div class="metric">
                    <span class="metric-title">Total Customers</span>
                    <span class="metric-value" id="totalCustomers">0</span>
                    <span class="metric-trend" id="customerGrowth">+0%</span>
                </div>
                <div class="metric">
                    <span class="metric-title">Avg. Customer Value</span>
                    <span class="metric-value" id="avgCustomerValue">₱0</span>
                </div>
                <div class="metric">
                    <span class="metric-title">Retention Rate</span>
                    <span class="metric-value" id="retentionRate">0%</span>
                </div>
            </div>
        </div>

        <div class="analytics-card segmentation">
            <h2>Customer Segments</h2>
            <canvas id="segmentationChart"></canvas>
        </div>

        <div class="analytics-card loyalty">
            <h2>Customer Loyalty</h2>
            <canvas id="loyaltyChart"></canvas>
        </div>

        <div class="analytics-card region">
            <h2>Regional Distribution</h2>
            <div id="regionMap"></div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
Page::render($content);
?>
