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