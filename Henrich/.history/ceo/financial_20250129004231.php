<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    header('Location: ../login/login.php');
    exit();
}

Page::setTitle('Financial Overview - CEO Dashboard');
Page::setBodyClass('ceo-financial');
Page::addStyle('/assets/css/ceo/financial.css');

ob_start(); ?>

<div class="financial-container">
    <div class="page-header">
        <h1>Financial Overview</h1>
        <div class="header-actions">
            <div class="date-range">
                <input type="date" id="startDate">
                <input type="date" id="endDate">
                <button id="applyDateRange" class="btn primary">Apply</button>
            </div>
            <button id="exportFinancial" class="btn secondary">
                <i class="bx bx-download"></i> Export Report
            </button>
        </div>
    </div>

    <div class="financial-grid">
        <div class="financial-card summary">
            <h2>Financial Summary</h2>
            <div class="metrics-grid">
                <div class="metric">
                    <span class="metric-title">Gross Revenue</span>
                    <span class="metric-value" id="grossRevenue"></span>
                    <span class="metric-trend" id="revenueTrend"></span>
                </div>
                <div class="metric">
                    <span class="metric-title">Net Profit</span>
                    <span class="metric-value" id="netProfit"></span>
                    <span class="metric-trend" id="profitTrend"></span>
                </div>
                <div class="metric">
                    <span class="metric-title">Operating Costs</span>
                    <span class="metric-value" id="operatingCosts"></span>
                </div>
            </div>
        </div>

        <div class="financial-card cash-flow">
            <h2>Cash Flow</h2>
            <canvas id="cashFlowChart"></canvas>
        </div>

        <div class="financial-card revenue-streams">
            <h2>Revenue Streams</h2>
            <canvas id="revenueStreamChart"></canvas>
        </div>

        <div class="financial-card expense-analysis">
            <h2>Expense Analysis</h2>
            <canvas id="expenseChart"></canvas>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
Page::render($content);
?>
