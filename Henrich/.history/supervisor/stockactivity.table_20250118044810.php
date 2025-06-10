<?php
// Pagination setup
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
$start = ($page - 1) * $limit;

// Filter conditions
$search = isset($_GET['search']) ? $_GET['search'] : '';
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$activityType = isset($_GET['activity_type']) ? $_GET['activity_type'] : '';
$user = isset($_GET['user']) ? $_GET['user'] : '';

// Build WHERE clause for filters
$where = [];
if ($search) $where[] = "(productcode LIKE '%$search%' OR description LIKE '%$search%')";
if ($startDate && $endDate) $where[] = "activity_date BETWEEN '$startDate' AND '$endDate'";
if ($activityType) $where[] = "activity_type = '$activityType'";
if ($user) $where[] = "encoder = '$user'";
$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Get filtered records
$totalRecords = $conn->query("SELECT COUNT(*) FROM stockactivitylog $whereClause")->fetch_row()[0];
$totalPages = ceil($totalRecords / $limit);

// Fetch activities
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'activity_date';
$order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
$activities = $conn->query("SELECT * FROM stockactivitylog $whereClause ORDER BY $sort $order LIMIT $start, $limit");
?>

<table class="table" id="activityTable">
    <thead>
        <tr>
            <th>Date/Time</th>
            <th>Activity Type</th>
            <th>Product Code</th>
            <th>Description</th>
            <th>User</th>
            <th>Details</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($row = $activities->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['activity_date']) ?></td>
            <td><?= htmlspecialchars($row['activity_type']) ?></td>
            <td><?= htmlspecialchars($row['productcode']) ?></td>