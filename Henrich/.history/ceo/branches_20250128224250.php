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
            <button class="btn secondary" id="exportBranchData">
                <i class="bx bx-download"></i> Export Data
            </button>
            <button class="btn primary" id="addBranch">
                <i class="bx bx-plus"></i> Add New Branch
            </button>
        </div>
    </div>

    <div class="branches-grid">
        <div class="branch-card overview">
            <h2>Branch Network Overview</h2>
            <div class="metrics-container">
                <div class="metric">
                    <span class="metric-value" id="totalBranches">0</span>
                    <span class="metric-label">Total Branches</span>
                </div>
                <div class="metric">
                    <span class="metric-value" id="activeBranches">0</span>
                    <span class="metric-label">Active</span>
                </div>
                <div class="metric">
                    <span class="metric-value" id="avgPerformance">0%</span>
                    <span class="metric-label">Avg. Performance</span>
                </div>
            </div>
        </div>

        <div class="branch-card performance">
            <h2>Performance Map</h2>
            <div id="branchMap"></div>
        </div>

        <div class="branch-card directory">
$content = ob_get_clean();
Page::render($content);
?>
