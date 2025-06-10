<?php
require_once '../includes/session.php';
require '../includes/config.php';
require 'analytics.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Initialize analytics
$salesAnalytics = new SalesAnalytics($conn);
$inventoryAnalytics = new InventoryAnalytics($conn);

// Get time period from request or default to 30 days
$timeframe = isset($_GET['timeframe']) ? $_GET['timeframe'] : '30';
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Get analytics data
$descriptiveAnalytics = $salesAnalytics->getDescriptiveAnalytics($timeframe);
$prescriptiveAnalytics = $salesAnalytics->getPrescriptiveAnalytics();
$inventoryInsights = $inventoryAnalytics->getInventoryInsights();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Analytics | Supervisor</title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="sales-analytics.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Add DateRangePicker -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="js/sales-handlers.js"></script>
</head>
<body>
<?php require '../reusable/sidebar.php'; ?>
    <?php include '../reusable/navbar.html'; ?>
    <section class="panel">

        <div class="analytics-dashboard">
            <!-- Enhanced Header Section -->
            <div class="analytics-header">
                <div class="header-content">
                    <h1>Sales Analytics Dashboard</h1>
                    <div class="period-selector">
                        <div class="date-picker">
                            <input type="text" id="dateRange" class="date-range" value="<?php echo $startDate . ' - ' . $endDate; ?>">
                            <i class='bx bx-calendar'></i>
                        </div>
                        <div class="quick-filters">
                            <button class="filter-btn active" data-days="7">Week</button>
                            <button class="filter-btn" data-days="30">Month</button>
                            <button class="filter-btn" data-days="90">Quarter</button>
                            <button class="filter-btn" data-days="365">Year</button>
                        </div>
                    </div>
                </div>
                <div class="action-buttons">
                    <button class="btn-refresh" onclick="refreshData()">
                        <i class='bx bx-refresh'></i> Refresh
                    </button>
                    <button class="btn-export" onclick="exportReport()">
                        <i class='bx bx-download'></i> Export Report
                    </button>
                </div>
            </div>

            <!-- Enhanced KPI Cards -->
            <div class="kpi-overview">
                <?php foreach ($descriptiveAnalytics['sales_summary'] as $summary): ?>
                    <div class="kpi-card <?php echo strtolower($summary['ordertype']); ?>">
                        <div class="kpi-icon">
                            <i class='bx <?php echo $summary['ordertype'] === 'Walk-in' ? 'bx-store' : 'bx-shopping-bag'; ?>'></i>
                        </div>
                        <div class="kpi-details">
                            <h3><?php echo $summary['ordertype']; ?> Sales</h3>
                            <div class="kpi-numbers">
                                <span class="amount">₱<?php echo number_format($summary['total_revenue'], 2); ?></span>
                                <span class="growth <?php echo isset($summary['growth']) && $summary['growth'] > 0 ? 'positive' : 'negative'; ?>">
                                    <?php echo isset($summary['growth']) ? ($summary['growth'] > 0 ? '↑' : '↓') . abs($summary['growth']) . '%' : ''; ?>
                                </span>
                            </div>
                            <div class="kpi-stats">
                                <div class="stat-item">
                                    <span class="stat-label">Orders</span>
                                    <span class="stat-value"><?php echo $summary['order_count']; ?></span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Avg. Order</span>
                                    <span class="stat-value">₱<?php echo number_format($summary['avg_order_value'], 2); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Analytics Content Tabs -->
            <div class="analytics-content">
                <div class="tab-navigation">
                    <button class="tab-btn active" data-tab="overview">Overview</button>
                    <button class="tab-btn" data-tab="sales">Sales Analysis</button>
                    <button class="tab-btn" data-tab="inventory">Inventory</button>
                    <button class="tab-btn" data-tab="customers">Customers</button>
                </div>

                <!-- Tab Contents -->
                <div class="tab-content-wrapper">
                    <div class="tab-content active" id="overview">
                        <div class="chart-grid">
                            <!-- Sales Trend Chart -->
                            <div class="chart-card wide">
                                <div class="chart-header">
                                    <h3>Sales Trends</h3>
                                    <div class="chart-controls">
                                        <select class="chart-type-selector">
                                            <option value="daily">Daily</option>
                                            <option value="weekly">Weekly</option>
                                            <option value="monthly">Monthly</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="chart-container">
                                    <canvas id="salesTrendsChart"></canvas>
                                </div>
                            </div>

                            <!-- Category Performance -->
                            <div class="chart-card">
                                <h3>Top Categories</h3>
                                <div class="chart-container">
                                    <canvas id="categoryChart"></canvas>
                                </div>
                            </div>

                            <!-- Customer Segments -->
                            <div class="chart-card">
                                <h3>Customer Segments</h3>
                                <div class="chart-container">
                                    <canvas id="customerSegmentsChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional tab contents here -->
                </div>
            </div>
        </div>

        <?php include_once("../reusable/footer.php"); ?>
    </section>

    <!-- Pass PHP data to JavaScript -->
    <script>
        const analyticsData = <?php echo json_encode([
            'descriptive' => $descriptiveAnalytics,
            'prescriptive' => $prescriptiveAnalytics,
            'inventory' => $inventoryInsights
        ]); ?>;
    </script>
    <script src="sales-analytics.js"></script>
</body>
</html>