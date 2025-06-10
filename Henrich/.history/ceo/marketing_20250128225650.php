<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    header('Location: ../login/login.php');
    exit();
}

Page::setTitle('Marketing Analytics - CEO Dashboard');
Page::setBodyClass('ceo-marketing');

ob_start(); ?>

<div class="marketing-container">
    <div class="page-header">
        <h1>Marketing Analytics</h1>
        <div class="header-actions">
            <select id="campaignFilter" class="form-select">
                <option value="all">All Campaigns</option>
                <option value="active">Active Campaigns</option>
                <option value="completed">Completed</option>
            </select>
            <button id="exportMarketingReport" class="btn secondary">
                <i class="bx bx-download"></i> Export Analytics
            </button>
        </div>
    </div>

    <div class="marketing-grid">
        <div class="marketing-card overview">
            <h2>Campaign Performance</h2>
            <div class="metrics-grid">
                <div class="metric">
                    <span class="metric-title">Active Campaigns</span>
                    <span class="metric-value" id="activeCampaigns">0</span>
                </div>
                <div class="metric">
                    <span class="metric-title">Total ROI</span>
                    <span class="metric-value" id="totalRoi">0%</span>
                </div>
                <div class="metric">
                    <span class="metric-title">Lead Conversion</span>
                    <span class="metric-value" id="conversionRate">0%</span>
                </div>
            </div>
        </div>

        <div class="marketing-card campaigns">
            <h2>Campaign ROI Analysis</h2>
            <canvas id="roiChart"></canvas>
        </div>

        <div class="marketing-card channels">
            <h2>Channel Performance</h2>
            <canvas id="channelChart"></canvas>
        </div>

        <div class="marketing-card budget">
            <h2>Marketing Budget Allocation</h2>
            <div id="budgetBreakdown"></div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
Page::render($content);
?>
