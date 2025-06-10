<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    header('Location: ../login/login.php');
    exit();
}

Page::setTitle('Inventory Overview - CEO Dashboard');
Page::setBodyClass('ceo-inventory');

ob_start(); ?>

<div class="inventory-container">
    <div class="page-header">
        <h1>Inventory Overview</h1>
        <div class="header-actions">
            <select id="periodFilter" class="form-select">
                <option value="weekly">Weekly</option>
                <option value="monthly" selected>Monthly</option>
                <option value="yearly">Yearly</option>
            </select>
            <button id="exportInventory" class="btn secondary">Export Report</button>
        </div>
    </div>

    <div class="inventory-grid">
        <div class="inventory-card summary">
            <h2>Inventory Summary</h2>
            <div class="metrics-container">
                <div class="metric" id="totalStock"></div>
                <div class="metric" id="lowStock"></div>
                <div class="metric" id="stockValue"></div>
            </div>
        </div>

        <div class="inventory-card trends">
            <h2>Stock Level Trends</h2>
            <canvas id="stockTrendsChart"></canvas>
        </div>

        <div class="inventory-card categories">
            <h2>Category Distribution</h2>
            <canvas id="categoryChart"></canvas>
        </div>

        <div class="inventory-card alerts">
            <h2>Inventory Alerts</h2>
            <div id="alertsList"></div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
Page::render($content);
?>
