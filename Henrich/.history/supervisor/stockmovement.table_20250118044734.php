<?php
// Pagination setup
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
$start = ($page - 1) * $limit;

// Search and filter conditions
$search = isset($_GET['search']) ? $_GET['search'] : '';
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$movementType = isset($_GET['movement_type']) ? $_GET['movement_type'] : '';

// Build WHERE clause
$where = [];
if ($search) {
    $where[] = "(productcode LIKE '%$search%' OR productname LIKE '%$search%' OR batchid LIKE '%$search%')";
}
if ($startDate && $endDate) {
    $where[] = "dateencoded BETWEEN '$startDate' AND '$endDate'";
}
if ($movementType) {
    $where[] = "movementtype = '$movementType'";
}
$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Get total records with filters
$totalRecords = $conn->query("SELECT COUNT(*) FROM stockmovement $whereClause")->fetch_row()[0];
$totalPages = ceil($totalRecords / $limit);

// Fetch stock movements with sorting
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'dateencoded';
$order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
$movements = $conn->query("SELECT * FROM stockmovement $whereClause ORDER BY $sort $order LIMIT $start, $limit");
?>

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

<table class="table" id="movementTable">
    <thead>
        <tr>
                    <td><?= htmlspecialchars($row['productname']) ?></td>
                    <td><?= htmlspecialchars($row['numberofbox']) ?> boxes</td>
                    <td><?= htmlspecialchars($row['totalpieces']) ?> pcs</td>
                    <td><?= number_format($row['totalweight'], 2) ?> kg</td>
                    <td><?= htmlspecialchars($row['dateencoded']) ?></td>
                </tr>
                <?php
            }
        } else {
            echo "<tr><td colspan='8' class='no-records'>No stock movements found</td></tr>";
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
