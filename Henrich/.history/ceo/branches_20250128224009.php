<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    header('Location: ../login/login.php');
    exit();
}

Page::setTitle('Branch Management - CEO Dashboard');
Page::setBodyClass('ceo-branches');

ob_start(); ?>

<div class="branches-container">
    <div class="page-header">
        <h1>Branch Management</h1>
        <button class="btn primary" id="addBranch">
            <i class="bx bx-plus"></i> Add New Branch
        </button>
    </div>

    <div class="branches-grid">
        <div class="branch-card analytics">
            <h2>Branch Analytics</h2>
            <canvas id="branchPerformanceChart"></canvas>
        </div>

        <div class="branch-card listing">
            <h2>Branch Directory</h2>
            <div class="branch-table-container">
                <table id="branchTable" class="display">
                    <thead>
                        <tr>
                            <th>Branch Name</th>
                            <th>Location</th>
                            <th>Manager</th>
                            <th>Performance</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded dynamically -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
Page::render($content);
?>
