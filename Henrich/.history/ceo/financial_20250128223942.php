<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';

// Verify CEO access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    header('Location: ../login/login.php');
    exit();
}

Page::setTitle('Financial Overview - CEO Dashboard');
Page::setBodyClass('ceo-financial');

ob_start(); ?>

<div class="financial-container">
    <div class="page-header">
        <h1>Financial Overview</h1>
        <div class="period-selector">
            <select id="periodSelect">
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly" selected>Monthly</option>
                <option value="yearly">Yearly</option>
            </select>
        </div>
    </div>

    <div class="financial-grid">
        <div class="financial-card revenue-analysis">
            <h2>Revenue Analysis</h2>
            <canvas id="revenueChart"></canvas>
        </div>

        <div class="financial-card expense-breakdown">
            <h2>Expense Breakdown</h2>
            <canvas id="expenseChart"></canvas>
        </div>

        <div class="financial-card profit-margins">
            <h2>Profit Margins</h2>
            <canvas id="profitChart"></canvas>
        </div>

        <div class="financial-card cash-flow">
            <h2>Cash Flow Statement</h2>
            <div id="cashFlowTable"></div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
Page::render($content);
?>
