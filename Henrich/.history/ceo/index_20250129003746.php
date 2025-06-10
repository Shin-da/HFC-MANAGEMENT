<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';
require_once '../includes/PageManager.php';

// Verify CEO access and page validity
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    header('Location: ../login/login.php');
    exit();
}

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
if (!PageManager::validateAccess('ceo', $currentPage)) {
    PageManager::redirectToDefault('ceo');
}

// Add navigation styles and scripts
Page::addStyle('/assets/css/ceo/navigation.css');
Page::addScript('/assets/js/ceo/navigation.js');

Page::setTitle('CEO Dashboard - HFC Management');
Page::setBodyClass('ceo-dashboard');

ob_start(); ?>

<div class="dashboard-container">
    <div class="dashboard-header">
        <h1>CEO Executive Dashboard</h1>
        <div class="date-time"><?php echo date('F j, Y'); ?></div>
    </div>

    <div class="dashboard-grid">
        <!-- Company Overview -->
        <div class="dashboard-card company-overview">
            <h2>Company Overview</h2>
            <div class="metrics-grid">
                <div class="metric">
                    <span class="metric-title">Total Revenue</span>
                    <span class="metric-value" id="totalRevenue">Loading...</span>
                </div>
                <div class="metric">
                    <span class="metric-title">Net Profit</span>
                    <span class="metric-value" id="netProfit">Loading...</span>
                </div>
                <div class="metric">
                    <span class="metric-title">Total Employees</span>
                    <span class="metric-value" id="totalEmployees">Loading...</span>
                </div>
            </div>
        </div>

        <!-- Performance Indicators -->
        <div class="dashboard-card kpi-card">
            <h2>Key Performance Indicators</h2>
            <div id="kpiChart"></div>
        </div>

        <!-- Sales Overview -->
        <div class="dashboard-card sales-overview">
            <h2>Sales Overview</h2>
            <div id="salesChart"></div>
        </div>

        <!-- Branch Performance -->
        <div class="dashboard-card branch-performance">
            <h2>Branch Performance</h2>
            <div id="branchPerformanceTable"></div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
Page::render($content);
?>

<style>
.dashboard-container {
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.dashboard-card {
    background: var(--surface);
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.metric {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.metric-title {
    font-size: 0.9rem;
    color: var(--text-secondary);
    margin-bottom: 0.5rem;
}

.metric-value {
    font-size: 1.5rem;
    font-weight: bold;
    color: var(--primary);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load KPI data
    fetchKPIData();
    document.getElementById('netProfit').textContent = '₱ 5.2M';
    document.getElementById('totalEmployees').textContent = '150';
}

function initializeDashboardCharts() {
    // Initialize charts using Chart.js
    // Implementation details would go here
}

function loadBranchPerformance() {
    // Load branch performance data
    // Implementation details would go here
}
</script>
