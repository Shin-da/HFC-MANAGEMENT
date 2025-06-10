<?php
// Pagination setup
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
$start = ($page - 1) * $limit;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'inventoryid';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Get filter values if they exist
$category = isset($_GET['category']) ? $_GET['category'] : '';
$stockStatus = isset($_GET['status']) ? $_GET['status'] : '';

// Base query
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
</div>
