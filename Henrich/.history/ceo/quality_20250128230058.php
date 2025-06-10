<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    header('Location: ../login/login.php');
    exit();
}

Page::setTitle('Quality Control - CEO Dashboard');
Page::setBodyClass('ceo-quality');

ob_start(); ?>

<div class="quality-container">
    <div class="page-header">
        <h1>Quality Control Overview</h1>
        <div class="header-actions">
            <select id="qualityPeriod" class="form-select">
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly" selected>Monthly</option>
            </select>
            <button id="exportQualityReport" class="btn secondary">
                <i class="bx bx-download"></i> Export Report
            </button>
        </div>
    </div>

    <div class="quality-grid">
        <div class="quality-card overview">
            <h2>Quality Metrics</h2>
            <div class="metrics-grid">
                <div class="metric">
                    <span class="metric-title">Quality Score</span>
                    <span class="metric-value" id="qualityScore">0%</span>
                </div>
                <div class="metric">
                    <span class="metric-title">Defect Rate</span>
                    <span class="metric-value" id="defectRate">0%</span>
                </div>
                <div class="metric">
                    <span class="metric-title">Customer Complaints</span>
                    <span class="metric-value" id="complaintCount">0</span>
                </div>
            </div>
        </div>

        <div class="quality-card incidents">
            <h2>Quality Incidents</h2>
            <div id="incidentsTimeline"></div>
        </div>

        <div class="quality-card trends">
            <h2>Quality Trends</h2>
            <canvas id="qualityTrendsChart"></canvas>
        </div>

        <div class="quality-card actions">
            <h2>Corrective Actions</h2>
            <div id="correctiveActionsList" class="action-list"></div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
Page::render($content);
?>
