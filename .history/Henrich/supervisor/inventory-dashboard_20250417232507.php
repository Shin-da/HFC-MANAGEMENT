<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';
require_once './access_control.php';

// Database connection function
function getConnection() {
    global $db_host, $db_user, $db_pass, $db_name;
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

$current_page = basename($_SERVER['PHP_SELF'], '.php');
$_SESSION['current_page'] = $current_page;

// Initialize page
Page::setTitle('Inventory Dashboard');
Page::setBodyClass('supervisor-body');
Page::setCurrentPage('inventory-dashboard');

// Add required styles and scripts
Page::addStyle('../assets/css/inventory-master.css');
Page::addScript('https://code.jquery.com/jquery-3.7.0.min.js');
Page::addStyle('https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css');
Page::addScript('https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js');
Page::addScript('https://cdn.jsdelivr.net/npm/chart.js');
Page::addScript('../assets/js/inventory-dashboard.js');

// Add theme-related styles and scripts
Page::addStyle('/assets/css/themes.css');
Page::addStyle('/assets/css/theme-toggle.css');
Page::addScript('/assets/js/theme.js');
Page::addScript('../assets/js/debug-loader.js');
Page::addScript('https://cdn.jsdelivr.net/npm/sweetalert2@11');
Page::addScript('https://cdn.jsdelivr.net/npm/apexcharts');

// Start output buffering
ob_start();

// Database queries
$conn = getConnection();

// Get inventory statistics
$stats_query = "SELECT 
    COUNT(*) as total_products,
    SUM(CASE WHEN availablequantity <= 10 AND availablequantity > 0 THEN 1 ELSE 0 END) as low_stock,
    SUM(CASE WHEN availablequantity = 0 THEN 1 ELSE 0 END) as out_of_stock,
    SUM(availablequantity * unit_price) as total_value
FROM inventory";
$stats_result = $conn->query($stats_query);
$stats = $stats_result->fetch_assoc();

// Get recent stock movements
$movements_query = "SELECT 
    sm.batchid,
    sm.productcode,
    sm.productname,
    sm.numberofbox,
    sm.totalpacks,
    sm.totalweight,
    sm.movement_type,
    sm.dateencoded,
    sm.encoder
FROM stockmovement sm
ORDER BY sm.dateencoded DESC
LIMIT 10";
$movements_result = $conn->query($movements_query);

// Get category distribution
$category_query = "SELECT 
    productcategory,
    COUNT(*) as product_count,
    SUM(availablequantity) as total_quantity,
    SUM(availablequantity * unit_price) as category_value
FROM inventory
GROUP BY productcategory
ORDER BY category_value DESC";
$category_result = $conn->query($category_query);

?>

<div class="content-wrapper">
    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class='bx bx-package'></i></div>
            <div class="stat-details">
                <h3>Total Products</h3>
                <p><?= number_format($stats['total_products']) ?></p>
            </div>
        </div>
        <div class="stat-card warning">
            <div class="stat-icon"><i class='bx bx-error'></i></div>
            <div class="stat-details">
                <h3>Low Stock Items</h3>
                <p><?= number_format($stats['low_stock']) ?></p>
            </div>
        </div>
        <div class="stat-card danger">
            <div class="stat-icon"><i class='bx bx-x-circle'></i></div>
            <div class="stat-details">
                <h3>Out of Stock</h3>
                <p><?= number_format($stats['out_of_stock']) ?></p>
            </div>
        </div>
        <div class="stat-card success">
            <div class="stat-icon"><i class='bx bx-money'></i></div>
            <div class="stat-details">
                <h3>Total Value</h3>
                <p>₱<?= number_format($stats['total_value'], 2) ?></p>
            </div>
        </div>
    </div>

    <!-- Recent Stock Movements -->
    <div class="card">
        <div class="card-header">
            <h2>Recent Stock Movements</h2>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="movementsTable">
                    <thead>
                        <tr>
                            <th>Batch ID</th>
                            <th>Product</th>
                            <th>Boxes</th>
                            <th>Packs</th>
                            <th>Weight</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Encoder</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($movement = $movements_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($movement['batchid']) ?></td>
                            <td><?= htmlspecialchars($movement['productname']) ?></td>
                            <td><?= number_format($movement['numberofbox']) ?></td>
                            <td><?= number_format($movement['totalpacks']) ?></td>
                            <td><?= number_format($movement['totalweight'], 2) ?> kg</td>
                            <td>
                                <span class="badge <?= strtolower($movement['movement_type']) ?>">
                                    <?= $movement['movement_type'] ?>
                                </span>
                            </td>
                            <td><?= date('M d, Y H:i', strtotime($movement['dateencoded'])) ?></td>
                            <td><?= htmlspecialchars($movement['encoder']) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Category Distribution -->
    <div class="card">
        <div class="card-header">
            <h2>Category Distribution</h2>
        </div>
        <div class="card-body">
            <canvas id="categoryChart"></canvas>
        </div>
    </div>
</div>

<script>
// Initialize DataTables
$(document).ready(function() {
    $('#movementsTable').DataTable({
        order: [[6, 'desc']],
        pageLength: 10
    });
});

// Category Distribution Chart
const ctx = document.getElementById('categoryChart').getContext('2d');
const categoryData = {
    labels: [<?php 
        $labels = [];
        $values = [];
        $colors = [];
        mysqli_data_seek($category_result, 0);
        while ($category = $category_result->fetch_assoc()) {
            $labels[] = "'" . $category['productcategory'] . "'";
            $values[] = $category['total_quantity'];
            $colors[] = "'#" . substr(md5($category['productcategory']), 0, 6) . "'";
        }
        echo implode(',', $labels);
    ?>],
    datasets: [{
        data: [<?= implode(',', $values) ?>],
        backgroundColor: [<?= implode(',', $colors) ?>],
        borderWidth: 1
    }]
};

new Chart(ctx, {
    type: 'doughnut',
    data: categoryData,
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'right',
            },
            title: {
                display: true,
                text: 'Product Distribution by Category'
            }
        }
    }
});
</script>

<?php
$content = ob_get_clean();
Page::render($content);
?> 