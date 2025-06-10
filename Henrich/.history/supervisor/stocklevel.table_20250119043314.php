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
            $whereConditions[] = "i.availablequantity <= 10";
            break;
        case 'normal':
            $whereConditions[] = "i.availablequantity > 10";
            break;
    }
}

if (!empty($whereConditions)) {
    $sql .= " WHERE " . implode(" AND ", $whereConditions);
}

$sql .= " ORDER BY i.productcode ASC";

$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!-- Records info and entries filter -->
<div class="table-controls">
    <div class="dataTables_info">
        Showing <?= $start + 1 ?> to <?= min($start + $limit, $totalRecords) ?> of <?= $totalRecords ?> entries
    </div>
    <div class="filter-box">
        <label for="limit">Show</label>
        <select id="limit" onchange="location.href='?page=1&limit=' + this.value">
            <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
            <option value="25" <?= $limit == 25 ? 'selected' : '' ?>>25</option>
            <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
            <option value="100" <?= $limit == 100 ? 'selected' : '' ?>>100</option>
        </select>
        <label for="limit">entries</label>
    </div>
</div>

<!-- Inventory Table -->
<table class="inventory-table" id="inventoryTable">
    <thead>
        <tr>
            <th>Product Code</th>
            <th>Product Name</th>
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
                        $stockClass = 'out-of-stock';
                        break;
                    case 'Low Stock':
                        $stockClass = 'low-stock';
                        break;
                    default:
                        $stockClass = 'in-stock';
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
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="viewDetails('<?= $row['productcode'] ?>')">
                            <i class='bx bx-show'></i>
                        </button>
                        <button class="btn btn-sm btn-warning" onclick="adjustStock('<?= $row['productcode'] ?>')">
                            <i class='bx bx-edit'></i>
                        </button>
                    </td>
                </tr>
                <?php
            }
        } else {
            echo "<tr><td colspan='9' class='text-center'>No inventory records found</td></tr>";
        }
        ?>
    </tbody>
</table>

<!-- Pagination -->
<div class="pagination-container">
    <ul class="pagination">
        <li><a href="?page=<?= max(1, $page - 1) ?>&limit=<?= $limit ?>&sort=<?= $sort ?>&order=<?= $order ?>" class="<?= $page == 1 ? 'disabled' : '' ?>">&laquo;</a></li>
        <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
            <li><a href="?page=<?= $i ?>&limit=<?= $limit ?>&sort=<?= $sort ?>&order=<?= $order ?>" class="<?= $page == $i ? 'active' : '' ?>"><?= $i ?></a></li>
        <?php endfor; ?>
        <li><a href="?page=<?= min($totalPages, $page + 1) ?>&limit=<?= $limit ?>&sort=<?= $sort ?>&order=<?= $order ?>" class="<?= $page == $totalPages ? 'disabled' : '' ?>">&raquo;</a></li>
    </ul>
</div>

<script>
function viewDetails(productcode) {
    window.location.href = `view.product.php?code=${productcode}`;
}

function adjustStock(productcode) {
    window.location.href = `adjust.stock.php?code=${productcode}`;
}
</script>
