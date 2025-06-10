<?php
$stats = Page::get('stats');
$activities = Page::get('activities');
$totalRecords = Page::get('totalRecords');
// ... other variables ...
?>

<div class="dashboard-wrapper">
    <!-- Page Header -->
    // ...existing header code...

    <!-- Quick Stats Cards -->
    // ...existing stats code...

    <!-- Activity Charts -->
    // ...existing charts code...

    <!-- Advanced Filters -->
    // ...existing filters code...

    <!-- Activity Log Table -->
    <?php include 'partials/activity-table.php'; ?>
</div>
