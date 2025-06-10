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
