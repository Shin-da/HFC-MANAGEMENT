<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';

// Configure page
Page::setTitle('Sales Analytics Dashboard');
Page::setBodyClass('supervisor-body sales-theme');
Page::setCurrentPage('sales');

// Add required styles and scripts
Page::addStyle('https://cdn.jsdelivr.net/npm/chart.js');
Page::addScript('https://cdn.jsdelivr.net/npm/chart.js');
Page::addScript('assets/js/sales-dashboard.js');
Page::addScript('https://cdn.jsdelivr.net/npm/apexcharts');
Page::addScript('https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js');
Page::addScript('https://cdn.jsdelivr.net/npm/apexcharts@3.41.0/dist/apexcharts.min.js');
Page::addScript('https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js');
Page::addScript('https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js');   
Page::addScript('https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css
');

require_once dirname(__DIR__, 1) . '/templates/header.php';
?>

<div class="dashboard-wrapper">
    <!-- Page Header -->
    <div class="page-header">
        <h1>Sales Analytics Dashboard</h1>
        <p>Comprehensive sales analysis and business intelligence</p>
    </div>

    <!-- Loading Spinner -->
    <div class="loading-spinner"></div>
    
    <!-- Error Container -->
    <div id="errorContainer" class="alert alert-danger" style="display: none;"></div>

    <!-- Filters Section -->
    <div class="filters-section">
        <div class="filters-container">
            <div class="filter-group">
                <label for="dateRangeSelector">Period</label>
                <select id="dateRangeSelector" class="form-control">
                    <option value="all">All Time</option>
                    <option value="year">This Year</option>
                    <option value="month" selected>Last 30 Days</option>
                    <option value="week">Last 7 Days</option>
                    <option value="custom">Custom Range</option>
                </select>
            </div>

            <form id="customDateForm" class="filter-group" style="display: none;">
                <div class="date-range">
                    <div class="date-input">
                        <label for="startDate">Start Date</label>
                        <input type="date" id="startDate" name="startDate" class="form-control">
                    </div>
                    <div class="date-input">
                        <label for="endDate">End Date</label>
                        <input type="date" id="endDate" name="endDate" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Apply</button>
                </div>
            </form>

            <div class="action-buttons">
                <button class="btn btn-secondary export-btn" data-type="csv">
                    <i class="bx bx-download"></i> Export CSV
                </button>
                <button class="btn btn-secondary export-btn" data-type="pdf">
                    <i class="bx bx-file"></i> Export PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bx bx-money"></i>
            </div>
            <div class="stat-content">
                <h3>Total Sales</h3>
                <div class="value" id="totalSales">₱0.00</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bx bx-cart"></i>
            </div>
            <div class="stat-content">
                <h3>Orders</h3>
                <div class="value" id="totalOrders">0</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bx bx-receipt"></i>
            </div>
            <div class="stat-content">
                <h3>Avg. Order Value</h3>
                <div class="value" id="avgOrderValue">₱0.00</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bx bx-line-chart"></i>
            </div>
            <div class="stat-content">
                <h3>Growth Rate</h3>
                <div class="value" id="growthRate">0%</div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-grid">
        <!-- Sales Trend Chart -->
        <div class="chart-card">
            <h3>Sales Trends</h3>
            <div class="chart-container">
                <canvas id="salesTrendChart"></canvas>
            </div>
        </div>
        
        <!-- Category Distribution Chart -->
        <div class="chart-card">
            <h3>Sales by Category</h3>
            <div class="chart-container">
                <canvas id="categoryDistributionChart"></canvas>
            </div>
        </div>
        
        <!-- Product Performance Chart -->
        <div class="chart-card">
            <h3>Top Selling Products</h3>
            <div class="chart-container">
                <canvas id="productPerformanceChart"></canvas>
            </div>
        </div>
        
        <!-- Monthly Trends Chart -->
        <div class="chart-card">
            <h3>Monthly Performance</h3>
            <div class="chart-container">
                <canvas id="monthlyTrendsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Prescriptive Analytics Section -->
    <div class="table-section">
        <h2>Recommendations & Insights</h2>
        <div id="recommendationsContainer" class="recommendations-grid">
            <!-- Populated by JavaScript -->
        </div>
    </div>

    <!-- Inventory Status Section -->
    <div class="table-section">
        <h2>Inventory Status</h2>
        <div class="inventory-stats">
            <div class="stat-mini">
                <span class="stat-label">Total Items</span>
                <span class="stat-value" id="totalItems">0</span>
            </div>
            <div class="stat-mini warning">
                <span class="stat-label">Low Stock</span>
                <span class="stat-value" id="lowStockItems">0</span>
            </div>
            <div class="stat-mini danger">
                <span class="stat-label">Out of Stock</span>
                <span class="stat-value" id="outOfStockItems">0</span>
            </div>
        </div>
        <div id="inventoryStatusContainer" class="table-responsive">
            <table class="table" id="inventoryTable">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Available</th>
                        <th>Reorder Level</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Populated by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Custom CSS for Dashboard -->
<style>
    .recommendations-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1rem;
    }
    
    .recommendation-card {
        background: #fff;
        border-radius: 8px;
        padding: 1rem;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        display: flex;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .recommendation-icon {
        font-size: 2rem;
        color: #df5c36;
    }
    
    .recommendation-content h4 {
        margin-top: 0;
        margin-bottom: 0.5rem;
        color: #4a2e1f;
    }
    
    .recommendation-content p {
        margin-bottom: 0.5rem;
        color: #6a362b;
    }
    
    .recommendation-card.seasonality {
        grid-column: 1 / -1;
        background-color: #f9f3ee;
    }
    
    .inventory-stats {
        display: flex;
        gap: 2rem;
        margin-bottom: 1rem;
    }
    
    .stat-mini {
        display: flex;
        flex-direction: column;
    }
    
    .stat-mini .stat-label {
        font-size: 0.875rem;
        color: #6a362b;
    }
    
    .stat-mini .stat-value {
        font-size: 1.5rem;
        font-weight: bold;
        color: #4a2e1f;
    }
    
    .stat-mini.warning .stat-value {
        color: #de9a45;
    }
    
    .stat-mini.danger .stat-value {
        color: #df5c36;
    }
    
    tr.out-of-stock {
        background-color: rgba(223, 92, 54, 0.05);
    }
    
    tr.low-stock {
        background-color: rgba(222, 154, 69, 0.05);
    }
    
    .date-range {
        display: flex;
        gap: 0.5rem;
        align-items: flex-end;
    }
</style>

<script>
// Initialize date range picker functionality
document.addEventListener('DOMContentLoaded', function() {
    const dateRangeSelector = document.getElementById('dateRangeSelector');
    const customDateForm = document.getElementById('customDateForm');
    
    if (dateRangeSelector) {
        dateRangeSelector.addEventListener('change', function() {
            if (this.value === 'custom') {
                customDateForm.style.display = 'block';
            } else {
                customDateForm.style.display = 'none';
            }
        });
    }
});
</script>

<?php
// Include footer
require_once dirname(__DIR__, 1) . '/templates/footer.php';
?> 