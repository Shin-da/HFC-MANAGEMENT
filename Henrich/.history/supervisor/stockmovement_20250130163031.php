<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';

// Setup page
Page::setTitle('Stock Movement');
Page::setBodyClass('stock-movement-page');
Page::setCurrentPage('stockmovement');

// Add required styles
Page::addStyle('/assets/css/table.css');

Page::addStyle('/assets/css/stock-movement.css');

// Add required scripts
Page::addScript('/assets/js/table.js');
Page::addScript('/assets/js/stock-movement.js');

// Get pagination parameters
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
$start = ($page - 1) * $limit;

// Fetch data
$items = $conn->query("SELECT * FROM stockmovement LIMIT $start, $limit");
$totalRecords = $conn->query("SELECT COUNT(*) FROM stockmovement")->fetch_row()[0];
$totalPages = ceil($totalRecords / $limit);

// Store data for the template
Page::set('items', $items);
Page::set('totalRecords', $totalRecords);
Page::set('currentPage', $page);
Page::set('limit', $limit);
Page::set('totalPages', $totalPages);

// Generate content
ob_start();
include '../templates/stock-movement-content.php';
$content = ob_get_clean();

// Render page
Page::render($content);
?>