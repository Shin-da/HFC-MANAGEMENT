<?php
// Pagination setup
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
$start = ($page - 1) * $limit;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'productcode';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Get filter values if they exist
$category = isset($_GET['category']) ? $_GET['category'] : '';
$stockStatus = isset($_GET['status']) ? $_GET['status'] : '';

// Base query for counting total records
$countSql = "SELECT COUNT(*) as total FROM inventory i";

// Base query for fetching records
$sql = "SELECT 
    i.productcode,
    i.productname,
    i.productcategory,
    i.availablequantity,
    i.onhandquantity,
    i.unit_price,
    i.dateupdated,
    CASE 
        WHEN i.availablequantity = 0 THEN 'Out of Stock'
        WHEN i.availablequantity <= 10 THEN 'Low Stock'
        ELSE 'In Stock'
    END as stock_status
FROM inventory i";

// Add filters if they exist
$whereConditions = [];
if ($category) {
    $whereConditions[] = "i.productcategory = '$category'";
}
if ($stockStatus) {
    switch($stockStatus) {
        case 'out':
            $whereConditions[] = "i.availablequantity = 0";
            break;
        case 'low':
            $whereConditions[] = "i.availablequantity <= 10";
            break;
        case 'normal':
            $whereConditions[] = "i.availablequantity > 10";
            break;
    }
}

// Apply where conditions to both queries
if (!empty($whereConditions)) {
    $whereClause = " WHERE " . implode(" AND ", $whereConditions);
    $countSql .= $whereClause;
    $sql .= $whereClause;
}

// Get total records
$totalResult = $conn->query($countSql);
$totalRecords = $totalResult->fetch_assoc()['total'];

// Calculate total pages
$totalPages = ceil($totalRecords / $limit);

// Add pagination to main query
$sql .= " ORDER BY i.$sort $order LIMIT $start, $limit";

$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!-- Records info and entries filter -->
<div class="table-controls theme-aware">
    <div class="dataTables_info">
        Showing <?= $start + 1 ?> to <?= min($start + $limit, $totalRecords) ?> of <?= $totalRecords ?> entries
    </div>
    <div class="filter-box">
        <label for="limit" class="text-secondary">Show</label>
        <select id="limit" class="themed-select" onchange="location.href='?page=1&limit=' + this.value">
            <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
            <option value="25" <?= $limit == 25 ? 'selected' : '' ?>>25</option>
            <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
            <option value="100" <?= $limit == 100 ? 'selected' : '' ?>>100</option>
        </select>
        <label for="limit" class="text-secondary">entries</label>
    </div>
</div>

<!-- Inventory Table -->
<table class="inventory-table theme-table" id="inventoryTable">
    <thead>
        <tr>
            <th>Product Code</th>
            <th>Product Name</th>
            <th></th>
            <th>Category</th>
            <th>Available Qty</th>
            <th>On Hand</th>
            <th>Unit Price</th>
            <th>Last Updated</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $stockClass = '';
                switch($row['stock_status']) {
                    case 'Out of Stock':
                        $stockClass = 'status-badge status-danger';
                        break;
                    case 'Low Stock':
                        $stockClass = 'status-badge status-warning';
                        break;
                    default:
                        $stockClass = 'status-badge status-success';
                }
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['productcode']) ?></td>
                    <td><?= htmlspecialchars($row['productname']) ?></td>
                    <td><?= htmlspecialchars($row['productcategory']) ?></td>
                    <td><?= number_format($row['availablequantity']) ?></td>
                    <td><?= number_format($row['onhandquantity']) ?></td>
                    <td>₱<?= number_format($row['unit_price'], 2) ?></td>
                    <td><?= date('Y-m-d H:i', strtotime($row['dateupdated'])) ?></td>
                    <td class="<?= $stockClass ?>"><?= $row['stock_status'] ?></td>
                    <td class="actions">
                        <button class="btn-icon btn-info" onclick="viewDetails('<?= $row['productcode'] ?>')">
                            <i class='bx bx-show'></i>
                        </button>
                        <button class="btn-icon btn-warning" onclick="adjustStock('<?= $row['productcode'] ?>')">
                            <i class='bx bx-edit'></i>
                        </button>
                    </td>
                </tr>
                <?php
            }
        } else {
            echo "<tr><td colspan='9' class='text-center text-muted'>No inventory records found</td></tr>";
        }
        ?>
    </tbody>
</table>

<!-- Pagination -->
<div class="pagination-wrapper theme-aware">
    <ul class="pagination">
        <li><a href="?page=<?= max(1, $page - 1) ?>&limit=<?= $limit ?>&sort=<?= $sort ?>&order=<?= $order ?>" class="pagination-link <?= $page == 1 ? 'disabled' : '' ?>"><i class='bx bx-chevron-left'></i></a></li>
        <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
            <li><a href="?page=<?= $i ?>&limit=<?= $limit ?>&sort=<?= $sort ?>&order=<?= $order ?>" class="pagination-link <?= $page == $i ? 'active' : '' ?>"><?= $i ?></a></li>
        <?php endfor; ?>
        <li><a href="?page=<?= min($totalPages, $page + 1) ?>&limit=<?= $limit ?>&sort=<?= $sort ?>&order=<?= $order ?>" class="pagination-link <?= $page == $totalPages ? 'disabled' : '' ?>"><i class='bx bx-chevron-right'></i></a></li>
    </ul>
</div>

<style>
/* Add these styles to match the theme */
.theme-table {
    background: var(--bg-white);
    border: 1px solid var(--sage-200);
    border-radius: 8px;
    overflow: hidden;
}

.theme-table thead th {
    background: var(--forest-primary);
    color: var(--bg-white);
    font-weight: 500;
    padding: 1rem;
    text-align: left;
}

.theme-table tbody td {
    padding: 1rem;
    border-bottom: 1px solid var(--sage-100);
    color: var(--text-primary);
}

.theme-table tbody tr:hover {
    background: var(--sage-50);
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 2rem;
    font-size: 0.875rem;
    font-weight: 500;
    text-align: center;
    display: inline-block;
}

.status-danger {
    background: var(--rust-light);
    color: var(--text-light);
}

.status-warning {
    background: var(--accent-warning);
    color: var(--text-dark);
}

.status-success {
    background: var(--forest-light);
    color: var(--text-light);
}

.btn-icon {
    padding: 0.5rem;
    border-radius: 0.5rem;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    color: var(--bg-white);
}

.btn-info {
    background: var(--forest-primary);
}

.btn-warning {
    background: var(--accent-warning);
}

.themed-select {
    padding: 0.5rem;
    border: 1px solid var(--sage-200);
    border-radius: 0.5rem;
    background: var(--bg-white);
    color: var(--text-primary);
}

.pagination-link {
    padding: 0.5rem 1rem;
    border: 1px solid var(--sage-200);
    color: var(--text-primary);
    background: var(--bg-white);
    border-radius: 0.5rem;
    transition: all 0.2s ease;
}

.pagination-link:hover,
.pagination-link.active {
    background: var(--forest-primary);
    color: var(--bg-white);
    border-color: var(--forest-primary);
}

.pagination-link.disabled {
    opacity: 0.5;
    pointer-events: none;
}

.text-secondary {
    color: var(--text-secondary);
}

.text-muted {
    color: var(--sage-400);
}
</style>

<script>
function viewDetails(productcode) {
    window.location.href = `view.product.php?code=${productcode}`;
}

function adjustStock(productcode) {
    window.location.href = `adjust.stock.php?code=${productcode}`;
}
</script>
