<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    header('Location: ../login/login.php');
    exit();
}

Page::setTitle('Business Intelligence - CEO Dashboard');
Page::setBodyClass('ceo-intelligence');

ob_start(); ?>

<div class="intelligence-container">
    <div class="page-header">
        <h1>Business Intelligence</h1>
        <div class="header-actions">
            <select id="analysisRange" class="form-select">
                <option value="1m">Last Month</option>
                <option value="3m">Last Quarter</option>
                <option value="1y" selected>Last Year</option>
            </select>
            <button id="generatePredictions" class="btn primary">
                <i class="bx bx-line-chart"></i> Generate Forecast
            </button>
        </div>
    </div>

    <div class="intelligence-grid">
        <div class="intelligence-card trends">
            <h2>Market Trends</h2>
            <canvas id="trendAnalysisChart"></canvas>
        </div>

        <div class="intelligence-card predictions">
            <h2>Growth Predictions</h2>
            <div id="predictionMatrix"></div>
        </div>

        <div class="intelligence-card patterns">
            <h2>Consumer Patterns</h2>
            <canvas id="patternChart"></canvas>
        </div>

        <div class="intelligence-card recommendations">
            <h2>Strategic Recommendations</h2>
            <div id="recommendationList" class="recommendations-list"></div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
Page::render($content);
?>
