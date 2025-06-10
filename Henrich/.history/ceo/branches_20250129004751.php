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
        <div class="header-actions">
            <select id="compareInterval" class="form-select">
                <option value="7">Last 7 Days</option>
                <option value="30" selected>Last 30 Days</option>
                <option value="90">Last Quarter</option>
            </select>
            <button id="addBranch" class="btn primary">
                <i class="bx bx-plus-circle"></i> Add New Branch
            </button>
        </div>
    </div>

    <div class="branches-grid">
        <div class="branch-card overview">
            <h2>Network Overview</h2>
            <div class="metrics-grid">
                <div class="metric">
                    <span class="metric-title">Total Branches</span>
                    <span class="metric-value" id="totalBranches">0</span>
                </div>
                <div class="metric">
                    <span class="metric-title">Active Branches</span>
                    <span class="metric-value" id="activeBranches">0</span>
                </div>
                <div class="metric">
                    <span class="metric-title">Total Revenue</span>
                    <span class="metric-value" id="networkRevenue">₱0</span>
                </div>
            </div>
        </div>

        <div class="branch-card performance">
            <h2>Branch Performance</h2>
            <canvas id="branchComparisonChart"></canvas>
        </div>

        <div class="branch-card map">
            <h2>Branch Locations</h2>
            <div id="branchMap"></div>
        </div>

        <div class="branch-card list">
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
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
Page::render($content);
?>
