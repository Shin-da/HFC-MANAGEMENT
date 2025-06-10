<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');
$_SESSION['current_page'] = $current_page;
// Setup page
Page::setTitle('Stock Movement');
Page::setBodyClass('stock-movement-page');
Page::setCurrentPage('stockmovement');

// Add required styles and scripts
Page::addStyle('../assets/css/stock-pages.css');
Page::addStyle('../assets/css/stocklevel.css');
Page::addStyle('../assets/css/table.css');
Page::addStyle('../assets/css/dashboard.css');
Page::addStyle('../assets/css/stockmovement.enhanced.css');

// Add jQuery before other scripts
Page::addScript('https://code.jquery.com/jquery-3.7.0.min.js');
Page::addStyle('https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css');
Page::addScript('https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js');

// Add required scripts
Page::addScript('/assets/js/table.js');
Page::addScript('/assets/js/stock-movement.js');
Page::addScript('https://cdn.jsdelivr.net/npm/chart.js');
Page::addScript('https://cdn.jsdelivr.net/npm/sweetalert2@11');
Page::addScript('https://cdn.jsdelivr.net/npm/apexcharts');
Page::addScript('../assets/js/stockmovement.enhanced.js');

// Get pagination parameters
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
$start = ($page - 1) * $limit;

// Fetch data
$items = $conn->query("SELECT * FROM stockmovement LIMIT $start, $limit");
$totalRecords = $conn->query("SELECT COUNT(*) FROM stockmovement")->fetch_row()[0];
$totalPages = ceil($totalRecords / $limit);

// Get movement statistics - Updated query with correct column names
$stats_query = "SELECT 
    COUNT(*) as total_movements,
    SUM(CASE WHEN movement_type = 'IN' THEN 1 ELSE 0 END) as stock_in,
    SUM(CASE WHEN movement_type = 'OUT' THEN 1 ELSE 0 END) as stock_out,
    SUM(numberofbox) as total_boxes,
    SUM(totalpacks) as total_packs,
    SUM(totalweight) as total_weight
FROM stockmovement 
WHERE DATE(dateencoded) = CURRENT_DATE";

$stats_result = $conn->query($stats_query);
$stats = $stats_result->fetch_assoc();

// Get recent movements - Updated query with correct column names
$recent_query = "SELECT 
    sm.ibdid,
    sm.batchid,
    sm.productcode,
    sm.productname,
    sm.numberofbox,
    sm.totalpacks,
    sm.totalweight,
    sm.dateencoded,
    sm.encoder,
    sm.movement_type
FROM stockmovement sm
ORDER BY sm.dateencoded DESC 
LIMIT 10";

$recent_movements = $conn->query($recent_query);

// Store data for the template
Page::set('items', $items);
Page::set('totalRecords', $totalRecords);
Page::set('currentPage', $page);
Page::set('limit', $limit);
Page::set('totalPages', $totalPages);

// Generate content
ob_start();
?>

<div class="stock-management-wrapper theme-aware">
    <div class="dashboard-header">
        <div class="welcome-section">
            <div class="title-section">
                <h1>Stock Movement Management</h1>
                <p class="subtitle">Track and manage inventory movements</p>
            </div>
            <div class="header-actions">
                <a href="add.stockmovement.php" class="btn-primary">
                    <i class='bx bx-plus'></i>
                    New Stock Movement
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class='bx bx-transfer'></i></div>
            <h3>Today's Movements</h3>
            <div class="value"><?= number_format($stats['total_movements']) ?></div>
        </div>
        <div class="stat-card success">
            <div class="stat-icon"><i class='bx bx-log-in-circle'></i></div>
            <h3>Stock In</h3>
            <div class="value"><?= number_format($stats['stock_in']) ?></div>
        </div>
        <div class="stat-card warning">
            <div class="stat-icon"><i class='bx bx-log-out-circle'></i></div>
            <h3>Stock Out</h3>
            <div class="value"><?= number_format($stats['stock_out']) ?></div>
        </div>
        <div class="stat-card info">
            <div class="stat-icon"><i class='bx bx-package'></i></div>
            <h3>Total Moved</h3>
            <div class="value"><?= number_format($stats['total_packs']) ?> packs</div>
            <div class="sub-value">
                <?= number_format($stats['total_boxes']) ?> boxes /
                <?= number_format($stats['total_weight'], 2) ?> kg
            </div>
        </div>
    </div>

    <!-- Movement Overview -->
    <div class="movement-overview">
        <div class="chart-card">
            <div class="card-header">
                <h3>Movement Trends</h3>
                <div class="chart-controls">
                    <select id="trendPeriod">
                        <option value="7">Last 7 days</option>
                        <option value="30" selected>Last 30 days</option>
                        <option value="90">Last 90 days</option>
                    </select>
                </div>
            </div>
            <div id="movementTrendsChart"></div>
        </div>

        <!-- Recent Movements -->
        <div class="recent-movements">
            <div class="section-header">
                <h3>Recent Movements</h3>
            </div>
            <div class="movements-list">
                <?php while ($movement = $recent_movements->fetch_assoc()): ?>
                    <div class="movement-item <?= strtolower($movement['movement_type']) ?>">
                        <div class="movement-icon">
                            <i class='bx bx-<?= $movement['movement_type'] == 'IN' ? 'log-in' : 'log-out' ?>'></i>
                        </div>
                        <div class="movement-details">
                            <h4><?= htmlspecialchars($movement['productname']) ?></h4>
                            <div class="movement-meta">
                                <span class="quantity">
                                    <?= number_format($movement['totalpacks']) ?> packs
                                    (<?= number_format($movement['numberofbox']) ?> boxes)
                                </span>
                                <span class="weight"><?= number_format($movement['totalweight'], 2) ?> kg</span>
                                <span class="time"><?= date('M d, H:i', strtotime($movement['dateencoded'])) ?></span>
                            </div>
                            <div class="movement-user">
                                by <?= htmlspecialchars($movement['encoder']) ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>


    <!-- Add Stock Movement Table Section -->
    <div class="movement-table-section theme-container">
        <div class="table-header">
            <div class="title-section">
                <h2>Stock Movement History</h2>
                <div class="filters">
                    <select id="movementTypeFilter">
                        <option value="">All Types</option>
                        <option value="IN">Stock In</option>
                        <option value="OUT">Stock Out</option>
                    </select>
                    <input type="date" id="dateFilter" class="date-input">
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table id="movementTable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>IBD ID</th>
                        <th>Batch ID</th>
                        <th>Product Code</th>
                        <th>Product Name</th>
                        <th>No. of Boxes</th>
                        <th>Total packs</th>
                        <th>Total Weight</th>
                        <th>Movement Type</th>
                        <th>Date</th>
                        <th>Encoder</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Reset the pointer of $items result set
                    $items->data_seek(0);
                    while ($item = $items->fetch_assoc()):
                    ?>
                        <tr class="movement-row <?= strtolower($item['movement_type']) ?>">
                            <td><?= htmlspecialchars($item['ibdid']) ?></td>
                            <td><?= htmlspecialchars($item['batchid']) ?></td>
                            <td><?= htmlspecialchars($item['productcode']) ?></td>
                            <td><?= htmlspecialchars($item['productname']) ?></td>
                            <td><?= number_format($item['numberofbox']) ?></td>
                            <td><?= number_format($item['totalpacks']) ?></td>
                            <td><?= number_format($item['totalweight'], 2) ?> kg</td>
                            <td>
                                <span class="movement-badge <?= strtolower($item['movement_type']) ?>">
                                    <?= $item['movement_type'] ?>
                                </span>
                            </td>
                            <td><?= date('M d, Y H:i', strtotime($item['dateencoded'])) ?></td>
                            <td><?= htmlspecialchars($item['encoder']) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-icon" onclick="viewMovement('<?= $item['ibdid'] ?>')">
                                        <i class='bx bx-show'></i>
                                    </button>
                                    <?php if (strtotime($item['dateencoded']) > strtotime('-24 hours')): ?>
                                        <button class="btn-icon" onclick="editMovement('<?= $item['ibdid'] ?>')">
                                            <i class='bx bx-edit'></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <!-- <div class="pagination-wrapper">
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    </div>
</div> -->

        <?php
        $content = ob_get_clean();
        Page::render($content);
        ?>