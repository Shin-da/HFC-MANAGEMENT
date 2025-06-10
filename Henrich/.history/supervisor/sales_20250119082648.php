<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
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
    <link rel="stylesheet" href="sales-a.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Add DateRangePicker -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
</head>
<body>
    <?php include '../reusable/sidebar.php'; ?>
    <section class="panel">
        <?php include '../reusable/navbarNoSearch.html'; ?>

        <div class="dashboard-wrapper">
            <!-- Analytics Controls -->
            <div class="analytics-controls">
                <div class="filters">
                    <div class="date-range-picker">
                        <input type="text" id="dateRange" name="daterange" value="<?php echo $startDate . ' - ' . $endDate; ?>">
                    </div>
                    <select id="analysisType" onchange="updateAnalysis()">
                        <option value="all">All Analytics</option>
                        <option value="sales">Sales Only</option>
                        <option value="inventory">Inventory Only</option>
                    </select>
                    <button class="btn-export" onclick="exportReport()">
                        <i class='bx bx-download'></i> Export Report
                    </button>
                </div>
            </div>

            <!-- Key Performance Indicators -->
            <div class="kpi-container">
                <div class="kpi-grid">
                    <?php foreach ($descriptiveAnalytics['sales_summary'] as $summary): ?>
                        <div class="kpi-card">
                            <div class="kpi-header">
                                <h3><?php echo $summary['ordertype']; ?> Performance</h3>
                                <span class="kpi-trend <?php echo $summary['trend'] ?? ''; ?>">
                                    <?php echo isset($summary['growth']) ? ($summary['growth'] > 0 ? '↑' : '↓') : ''; ?>
                                    <?php echo isset($summary['growth']) ? abs($summary['growth']) . '%' : ''; ?>
                                </span>
                            </div>
                            <div class="kpi-content">
                                <div class="kpi-main">
                                    <p class="kpi-value">₱<?php echo number_format($summary['total_revenue'], 2); ?></p>
                                    <p class="kpi-label">Total Revenue</p>
                                </div>
                                <div class="kpi-stats">
                                    <div class="kpi-stat">
                                        <span class="stat-value"><?php echo $summary['order_count']; ?></span>
                                        <span class="stat-label">Orders</span>
                                    </div>
                                    <div class="kpi-stat">
                                        <span class="stat-value">₱<?php echo number_format($summary['avg_order_value'], 2); ?></span>
                                        <span class="stat-label">Avg. Order</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Detailed Analytics Tabs -->
            <div class="analytics-tabs">
                <div class="tab-buttons">
                    <button class="tab-btn active" data-tab="sales">Sales Analysis</button>
                    <button class="tab-btn" data-tab="inventory">Inventory Analysis</button>
                    <button class="tab-btn" data-tab="customers">Customer Insights</button>
                </div>

                <!-- Sales Analysis Tab -->
                <div class="tab-content active" id="sales">
                    <div class="analytics-grid">
                        <div class="chart-card">
                            <h3>Sales Trends</h3>
                            <div class="chart-container">
                                <canvas id="salesTrendsChart"></canvas>
                            </div>
                        </div>
                        <div class="chart-card">
                            <h3>Sales by Category</h3>
                            <div class="chart-container">
                                <canvas id="salesByCategoryChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="data-table">
                        <h3>Detailed Sales Report</h3>
                        <table id="salesTable" class="analytics-table">
                            <!-- Sales table content -->
                        </table>
                    </div>
                </div>

                <!-- Inventory Analysis Tab -->
                <div class="tab-content" id="inventory">
                    <div class="analytics-grid">
                        <div class="chart-card">
                            <h3>Stock Levels</h3>
                            <div class="chart-container">
                                <canvas id="stockLevelsChart"></canvas>
                            </div>
                        </div>
                        <div class="reorder-suggestions">
                            <h3>Reorder Recommendations</h3>
                            <?php foreach ($inventoryInsights['reorder_suggestions'] as $item): ?>
                                <div class="reorder-item <?php echo $item['availablequantity'] <= 0 ? 'urgent' : ''; ?>">
                                    <div class="item-details">
                                        <strong><?php echo $item['productname']; ?></strong>
                                        <span class="stock-status">Current: <?php echo $item['availablequantity']; ?></span>
                                    </div>
                                    <div class="action-needed">
                                        <span>Suggested Order: <?php echo $item['suggested_reorder_qty']; ?></span>
                                        <button class="btn-reorder" onclick="createPurchaseOrder('<?php echo $item['productcode']; ?>')">
                                            Create PO
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Customer Insights Tab -->
                <div class="tab-content" id="customers">
                    <!-- Customer analytics content -->
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