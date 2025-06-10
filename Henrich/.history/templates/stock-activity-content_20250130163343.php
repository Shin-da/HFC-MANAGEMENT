<?php
$stats = Page::get('stats');
$activities = Page::get('activities');
$totalRecords = Page::get('totalRecords');
$page = Page::get('currentPage');
$limit = Page::get('limit');
$offset = Page::get('offset');
$totalPages = ceil($totalRecords / $limit);
?>

<div class="dashboard-wrapper">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h1>Stock Activity Dashboard</h1>
            <div class="header-actions">
                <button class="btn-export" onclick="exportToExcel()">
                    <i class='bx bx-export'></i> Export Report
                </button>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="stats-grid">
        <!-- ...Stats cards using $stats array... -->
    </div>

    <!-- Activity Log Table -->
    <div class="table-section animate-fade-in delay-3">
        <div class="table-container theme-aware">
            <!-- Table header and filters -->
            <?php include '../templates/partials/activity-table-header.php'; ?>
            
            <!-- Table content -->
            <div class="container-fluid" style="overflow-x: auto;">
                <?php include '../templates/partials/activity-table-content.php'; ?>
            </div>

            <!-- Pagination -->
            <?php include '../templates/partials/activity-pagination.php'; ?>
        </div>
    </div>
</div>
