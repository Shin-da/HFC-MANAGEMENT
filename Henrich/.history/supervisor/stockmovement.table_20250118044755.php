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
            <th><a href="?sort=ibdid&order=<?= $sort === 'ibdid' && $order === 'ASC' ? 'DESC' : 'ASC' ?>">#</a></th>
            <th><a href="?sort=batchid&order=<?= $sort === 'batchid' && $order === 'ASC' ? 'DESC' : 'ASC' ?>">Batch ID</a></th>
            <th><a href="?sort=productcode&order=<?= $sort === 'productcode' && $order === 'ASC' ? 'DESC' : 'ASC' ?>">Product Code</a></th>
            <th><a href="?sort=productname&order=<?= $sort === 'productname' && $order === 'ASC' ? 'DESC' : 'ASC' ?>">Product Name</a></th>
            <th><a href="?sort=numberofbox&order=<?= $sort === 'numberofbox' && $order === 'ASC' ? 'DESC' : 'ASC' ?>">Number of Box</a></th>
            <th><a href="?sort=totalpieces&order=<?= $sort === 'totalpieces' && $order === 'ASC' ? 'DESC' : 'ASC' ?>">Total Pieces</a></th>
            <th><a href="?sort=totalweight&order=<?= $sort === 'totalweight' && $order === 'ASC' ? 'DESC' : 'ASC' ?>">Total Weight (Kg)</a></th>
            <th><a href="?sort=dateencoded&order=<?= $sort === 'dateencoded' && $order === 'ASC' ? 'DESC' : 'ASC' ?>">Date Encoded</a></th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($movements->num_rows > 0) {
            while ($row = $movements->fetch_assoc()) {
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['ibdid']) ?></td>
                    <td><?= htmlspecialchars($row['batchid']) ?></td>
                    <td><?= htmlspecialchars($row['productcode']) ?></td>
                    <td><?= htmlspecialchars($row['productname']) ?></td>
                    <td><?= htmlspecialchars($row['numberofbox']) ?> boxes</td>
                    <td><?= htmlspecialchars($row['totalpieces']) ?> pcs</td>
                    <td><?= number_format($row['totalweight'], 2) ?> kg</td>
                    <td><?= htmlspecialchars($row['dateencoded']) ?></td>
                </tr>
                <?php
            }