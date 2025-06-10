<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    header('Location: ../login/login.php');
    exit();
}

Page::setTitle('Product Analytics - CEO Dashboard');
Page::setBodyClass('ceo-products');

ob_start(); ?>

<div class="products-container">
    <div class="page-header">
        <h1>Product Analytics</h1>
        <div class="header-actions">
            <div class="filters">
                <select id="categoryFilter" class="form-select">
                    <option value="">All Categories</option>
                </select>
                <select id="timeRange" class="form-select">
                    <option value="7">Last Week</option>
                    <option value="30" selected>Last Month</option>
                    <option value="90">Last Quarter</option>
                </select>
            </div>
            <button id="exportProductReport" class="btn secondary">
                <i class="bx bx-download"></i> Export Report
            </button>
        </div>
    </div>

    <div class="analytics-grid">
        <div class="products-card performance">
            <h2>Top Performing Products</h2>
            <div id="topProductsList" class="performance-list"></div>
        </div>

        <div class="products-card trends">
            <h2>Product Sales Trends</h2>
            <canvas id="productTrendsChart"></canvas>
        </div>

        <div class="products-card categories">
            <h2>Category Performance</h2>
            <canvas id="categoryChart"></canvas>
        </div>

        <div class="products-card inventory">
            <h2>Stock Levels</h2>
            <div id="stockLevelsGrid" class="stock-grid"></div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
Page::render($content);
?>
