<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';
require_once '../includes/utilities.php'; // Make sure this line exists

// Setup page
Page::setTitle('Stock Activity Log');
Page::setBodyClass('stock-activity-page');
Page::setCurrentPage('stockactivitylog');

// Add required styles
Page::addStyle('/assets/css/table.css');
Page::addStyle('/assets/css/stock-activity.css');
Page::addStyle('/assets/css/dashboard.css');
Page::addStyle('/assets/css/animations.css');
Page::addStyle('/assets/css/stockactivitylog.css');
Page::addStyle('../assets/css/inventory-master.css');
Page::addStyle('https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css');
Page::addStyle('https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css');

// Add required scripts (make sure these are added in this order)
Page::addScript('https://code.jquery.com/jquery-3.6.0.min.js'); // Add jQuery first
Page::addScript('https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js');
Page::addScript('https://cdn.jsdelivr.net/npm/apexcharts@3.41.0/dist/apexcharts.min.js'); // Specific version
Page::addScript('https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js');
Page::addScript('https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js');
Page::addScript('/HFC MANAGEMENT/assets/js/stockactivitylog.js'); // Fix path

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
include 'stock-activity-content.php';
$content = ob_get_clean();

// Render page
Page::render($content);
