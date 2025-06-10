<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';
require_once './access_control.php';

// Initialize database connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASSWORD,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to get order logs with filtering
function getOrderLogs($pdo, $filters = []) {
    $query = "
        SELECT 
            ol.*,
            co.orderdate,
            co.timeoforder,
            co.ordertype,
            co.status as order_status,
            co.customername
        FROM orderlog ol
        JOIN customerorder co ON ol.orderid = co.orderid
        WHERE 1=1
    ";
    
    $params = [];
    
    if (!empty($filters['dateRange'])) {
        switch ($filters['dateRange']) {
            case 'today':
                $query .= " AND DATE(co.orderdate) = CURRENT_DATE";
                break;
            case '7':
                $query .= " AND co.orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)";
                break;
            case '30':
                $query .= " AND co.orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)";
                break;
            case '90':
                $query .= " AND co.orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 90 DAY)";
                break;
        }
    }

    if (!empty($filters['orderType'])) {
        $query .= " AND co.ordertype = :orderType";
        $params[':orderType'] = $filters['orderType'];
    }

    if (!empty($filters['status'])) {
        $query .= " AND co.status = :status";
        $params[':status'] = $filters['status'];
    }

    $query .= " ORDER BY co.orderdate DESC, co.timeoforder DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get initial data
$filters = [
    'dateRange' => $_GET['dateRange'] ?? '30',
    'orderType' => $_GET['orderType'] ?? '',
    'status' => $_GET['status'] ?? ''
];

$orderLogs = getOrderLogs($pdo, $filters);

// Configure page
Page::setTitle('Order Logs | Supervisor');
Page::setBodyClass('supervisor-body');
Page::set('current_page', 'orderedproducts');

// Add styles and scripts
Page::addStyle('../assets/css/style.css');
Page::addStyle('../assets/css/variables.css');
Page::addStyle('../assets/css/orderlogs.css');

// Start output buffering
ob_start();
?>

<div class="orderlogs-wrapper">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h1>Order Logs</h1>
            <div class="header-actions">
                <div class="filters">
                    <select id="dateRange" onchange="updateFilters()">
                        <option value="today" <?php echo $filters['dateRange'] === 'today' ? 'selected' : ''; ?>>Today</option>
                        <option value="7" <?php echo $filters['dateRange'] === '7' ? 'selected' : ''; ?>>Last 7 Days</option>
                        <option value="30" <?php echo $filters['dateRange'] === '30' ? 'selected' : ''; ?>>Last 30 Days</option>
                        <option value="90" <?php echo $filters['dateRange'] === '90' ? 'selected' : ''; ?>>Last 90 Days</option>
                    </select>
                    <select id="orderType" onchange="updateFilters()">
                        <option value="">All Order Types</option>
                ?>
                <div style=" display: flex; justify-content: space-around; align-items: center; width: 100%;"> <!--  Filter results by number of entries -->
                    <div class="dataTables_info" id="example_info" role="status" aria-live="polite">Showing <?= $start + 1 ?> to <?= $start + $limit ?> of <?= $totalRecords ?> entries</div>
                    <div class="filter-box"> <!-- Filter results by number of entries -->
                        <label for="limit">Show</label>
                        <select id="limit" onchange="location.href='?page=<?= $page ?>&limit=' + this.value">
                            <option value="10" <?php echo $limit == 10 ? 'selected' : '' ?>>10</option>
                            <option value="25" <?php echo $limit == 25 ? 'selected' : '' ?>>25</option>
                            <option value="50" <?php echo $limit == 50 ? 'selected' : '' ?>>50</option>
                            <option value="100" <?php echo $limit == 100 ? 'selected' : '' ?>>100</option>
                        </select>
                        <label for="limit">entries</label>
                    </div>
                </div>
            </div>
            <?php // pagination for stock management table
                $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
                $offset = ($page - 1) * $limit;

                $stockManagementTableSQL = "SELECT * FROM orderedproduct ORDER BY orderdate DESC LIMIT $limit OFFSET $offset";
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }
                $sql = $stockManagementTableSQL;
                $result = $conn->query($sql);
            ?>
            <div class="">
                <div class="container-fluid" style="overflow-x:Scroll;">
                    <!-- Inventory Table -->
                    <table class="table" id="myTable">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Product Code</th>
                                <th>Product Name</th>
                                <th>Weight</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Date Ordered</th>
                            </tr>
                            <tr class="filter-row">
                                <th><input type="text" placeholder="Search orderid..." id="orderid-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 0)"></th>
                                <th><input type="text" placeholder="Search productcode..." id="productcode-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 1)"></th>
                                <th><input type="text" placeholder="Search productname..." id="productname-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 2)"></th>
                                <th><input type="text" placeholder="Search weight..." id="weight-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 3)"></th>
                                <th><input type="text" placeholder="Search price..." id="price-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 4)"></th>
                                <th><input type="text" placeholder="Search quantity..." id="quantity-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 5)"></th>
                                <th><input type="text" placeholder="Search orderdate..." id="dateupdated-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 6)"></th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $orderid = $row['orderid'];
                                    $productcode = $row['productcode'];
                                    $productname = $row['productname'];
                                    $weight = $row['productweight'];
                                    $price = $row['unit_price'];
                                    $quantity = $row['quantity'];
                                    $orderdate = date('d-m-Y', strtotime($row['orderdate']));
                            ?>
                                    <tr class="clickable-row" onclick="location.href='orderdetail.php?orderid=<?= $orderid ?>'">
                                        <td><?= $orderid ?></td>
                                        <td><?= $productcode ?></td>
                                        <td><?= $productname ?></td>
                                        <td><?= $weight ?></td>
                                        <td>₱ <?= $price ?></td>
                                        <td><?= $quantity ?> </td>
                                        <td><?= $orderdate ?></td>
                                    </tr>
                            <?php
                                }
                            } else {
                                echo "<tr><td colspan='8'>0 results</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="container" style="display: flex; justify-content: center; flex-direction: column; align-items: center; "><!-- Pagination for stock management -->
                    <ul class="pagination">
                        <li><a href="?page=<?= $page - 1 <= 1 ? 1 : $page - 1 ?>" class="prev">&laquo;</a></li>
                        <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                            <li><a href="?page=<?= $i ?>" class="page <?= $page == $i ? 'active' : '' ?>"><?= $i ?></a></li>
                        <?php } ?>
                        <li><a href="?page=<?= $page + 1 > $totalPages ? $totalPages : $page + 1 ?>" class="next">&raquo;</a></li>
                    </ul>

                </div>
            </div>

        </div>
    </section>


</body>
<?php require '../reusable/footer.php'; ?>

</html>







