<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';
require_once '../includes/utilities.php'; // Add this line

// Setup page
Page::setTitle('Stock Activity Log');
Page::setBodyClass('stock-activity-page');
Page::setCurrentPage('stockactivitylog');

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
include 'stock-activity-content.php';
$content = ob_get_clean();

// Render page
Page::render($content);

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