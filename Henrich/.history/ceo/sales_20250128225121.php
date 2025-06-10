<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    header('Location: ../login/login.php');
    exit();
}

Page::setTitle('Sales Analytics - CEO Dashboard');
Page::setBodyClass('ceo-sales');

ob_start(); ?>

<div class="sales-container">
    <div class="page-header">
        <h1>Sales Analytics</h1>
        <div class="header-actions">
            <div class="date-range-picker">
                <input type="date" id="startDate">
                <input type="date" id="endDate">
                <button id="applyDateRange" class="btn primary">Apply</button>
            </div>
            <button id="downloadSalesReport" class="btn secondary">
                <i class="bx bx-download"></i> Export Report
            </button>
        </div>
    </div>

    <div class="sales-grid">
        <div class="sales-card overview">
            <h2>Sales Overview</h2>
            <div class="metrics-grid">
                <div class="metric">
                    <span class="metric-title">Total Sales</span>
                    <span class="metric-value" id="totalSales">₱0</span>
                    <span class="metric-trend" id="salesTrend">+0%</span>
                </div>
                <div class="metric">
                    <span class="metric-title">Average Order Value</span>
                    <span class="metric-value" id="avgOrderValue">₱0</span>
                </div>
                <div class="metric">
                    <span class="metric-title">Conversion Rate</span>
                    <span class="metric-value" id="conversionRate">0%</span>
                </div>
            </div>
        </div>

        <div class="sales-card trends">
            <h2>Sales Trends</h2>
            <canvas id="salesTrendsChart"></canvas>
        </div>

        <div class="sales-card products">
            <h2>Top Products</h2>
            <div id="topProductsList"></div>
        </div>

        <div class="sales-card forecast">
            <h2>Sales Forecast</h2>
            <canvas id="forecastChart"></canvas>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
Page::render($content);
?>
