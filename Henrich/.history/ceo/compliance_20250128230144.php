<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    header('Location: ../login/login.php');
    exit();
}

Page::setTitle('Compliance & Risk Management - CEO Dashboard');
Page::setBodyClass('ceo-compliance');

ob_start(); ?>

<div class="compliance-container">
    <div class="page-header">
        <h1>Compliance & Risk Management</h1>
        <div class="header-actions">
            <select id="riskLevel" class="form-select">
                <option value="all">All Risk Levels</option>
                <option value="high">High Risk</option>
                <option value="medium">Medium Risk</option>
                <option value="low">Low Risk</option>
            </select>
            <button id="generateRiskReport" class="btn secondary">
                <i class="bx bx-file"></i> Risk Report
            </button>
        </div>
    </div>

    <div class="compliance-grid">
        <div class="compliance-card overview">
            <h2>Compliance Status</h2>
            <div class="metrics-grid">
                <div class="metric">
                    <span class="metric-title">Compliance Score</span>
                    <span class="metric-value" id="complianceScore">0%</span>
                </div>
                <div class="metric">
                    <span class="metric-title">Open Issues</span>
                    <span class="metric-value" id="openIssues">0</span>
                </div>
                <div class="metric">
                    <span class="metric-title">Risk Level</span>
                    <span class="metric-value" id="riskLevel">Low</span>
                </div>
            </div>
        </div>

        <div class="compliance-card risks">
            <h2>Risk Assessment</h2>
            <canvas id="riskMatrix"></canvas>
        </div>

        <div class="compliance-card audits">
            <h2>Audit Timeline</h2>
            <div id="auditTimeline"></div>
        </div>

        <div class="compliance-card regulations">
            <h2>Regulatory Requirements</h2>
            <div id="regulationsList" class="requirements-list"></div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
Page::render($content);
?>
