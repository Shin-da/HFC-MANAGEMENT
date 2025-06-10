<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    header('Location: ../login/login.php');
    exit();
}

Page::setTitle('Supply Chain Overview - CEO Dashboard');
Page::setBodyClass('ceo-supply-chain');

ob_start(); ?>

<div class="supply-chain-container">
    <div class="page-header">
        <h1>Supply Chain Management</h1>
        <div class="header-actions">
            <select id="timeframeSelect" class="form-select">
                <option value="weekly">Weekly</option>
                <option value="monthly" selected>Monthly</option>
                <option value="quarterly">Quarterly</option>
            </select>
            <button id="exportSupplyChainReport" class="btn secondary">
                <i class="bx bx-download"></i> Export Report
            </button>
        </div>
    </div>

    <div class="supply-chain-grid">
        <div class="supply-chain-card overview">
            <h2>Supply Chain Overview</h2>
            <div class="metrics-grid">
                <div class="metric">
                    <span class="metric-title">On-Time Delivery</span>
                    <span class="metric-value" id="otdRate">0%</span>
                </div>
                <div class="metric">
                    <span class="metric-title">Inventory Turnover</span>
                    <span class="metric-value" id="turnoverRate">0</span>
                </div>
                <div class="metric">
                    <span class="metric-title">Order Fulfillment</span>
                    <span class="metric-value" id="fulfillmentRate">0%</span>
                </div>
            </div>
        </div>

        <div class="supply-chain-card suppliers">
            <h2>Supplier Performance</h2>
            <canvas id="supplierPerformanceChart"></canvas>
        </div>

        <div class="supply-chain-card inventory">
            <h2>Inventory Health</h2>
            <div id="inventoryAnalytics"></div>
        </div>

        <div class="supply-chain-card logistics">
            <h2>Logistics Performance</h2>
            <div id="logisticsMetrics"></div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
Page::render($content);
?>
