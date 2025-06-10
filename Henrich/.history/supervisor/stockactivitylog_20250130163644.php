<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';
require_once '../includes/utilities.php'; // Add this line

// Setup page
Page::setTitle('Stock Activity Log');
Page::setBodyClass('stock-activity-page');

// Add required styles
Page::addStyle('/assets/css/table.css');
Page::addStyle('/assets/css/stock-activity.css');
Page::addStyle('/assets/css/dashboard.css');
Page::addStyle('/assets/css/animations.css');

// Add required scripts
Page::addScript('/assets/js/stock-activity.js');
Page::addScript('https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js');
Page::addScript('https://cdn.jsdelivr.net/npm/chart.js');

// Fetch activity statistics
try {
    $stats = [
        'today' => $conn->query("SELECT COUNT(*) FROM stockactivitylog WHERE DATE(dateencoded) = CURRENT_DATE()")->fetch_row()[0],
        'week' => $conn->query("SELECT COUNT(*) FROM stockactivitylog WHERE dateencoded >= DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY)")->fetch_row()[0],
        'month' => $conn->query("SELECT COUNT(*) FROM stockactivitylog WHERE MONTH(dateencoded) = MONTH(CURRENT_DATE()) AND YEAR(dateencoded) = YEAR(CURRENT_DATE())")->fetch_row()[0]
    ];
    
    Page::set('stats', $stats);
} catch (Exception $e) {
    error_log("Error fetching activity statistics: " . $e->getMessage());
    Page::set('stats', ['today' => 0, 'week' => 0, 'month' => 0]);
}

// Pagination setup
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

// Store pagination data
Page::set('currentPage', $page);
Page::set('limit', $limit);
Page::set('offset', $offset);
Page::set('totalRecords', $conn->query("SELECT COUNT(*) FROM stockactivitylog")->fetch_row()[0]);

// Fetch records
$result = $conn->query("SELECT * FROM stockactivitylog ORDER BY dateencoded DESC LIMIT $offset, $limit");
Page::set('activities', $result);

// Generate content
ob_start();
include '../templates/stock-activity-content.php';
$content = ob_get_clean();

// Render page
Page::render($content);
