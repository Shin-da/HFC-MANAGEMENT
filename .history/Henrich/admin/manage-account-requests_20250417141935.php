<?php
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors to browser
ini_set('log_errors', 1); // Log errors instead

// require_once 'access_control.php'; // Temporarily comment out to isolate error
// require_once '../includes/Page.php'; // Temporarily comment out Page class
// require_once '../includes/functions.php'; // Temporarily comment out functions include

// Initialize page - Temporarily comment out Page calls
// Page::setTitle('Manage User Requests - HFC Admin'); 
// Page::setBodyClass('admin-page');
// Page::setCurrentPage('manage-requests'); // Keep original page identifier?
// Page::setAdminPage(true);

// Add required styles
// Page::addStyle('../assets/css/admin.css');
// Page::addStyle('../assets/css/table.css');

// Add required scripts - Use requests.js
// Page::addScript('../assets/js/requests.js');

ob_start();

// Get all fields from account_request
$result = $conn->query("SELECT 
    user_id, usermail, username, role, created_at, updated_at, 
    first_name, last_name, status, last_online, is_online 
    FROM account_request 
    ORDER BY created_at DESC");

// Debug any SQL errors
if (!$result) {
    die("Error fetching requests: " . $conn->error);
}

// Add validation query to verify role consistency
$validation_query = "SELECT ar.role as request_role, aa.role as approved_role 
                    FROM account_request ar 
                    LEFT JOIN approved_account aa ON ar.username = aa.username 
                    WHERE aa.user_id IS NOT NULL";
$validation_result = $conn->query($validation_query);
while ($row = $validation_result->fetch_assoc()) {
    if ($row['request_role'] !== $row['approved_role']) {
        error_log("Role mismatch found: Request role: {$row['request_role']}, Approved role: {$row['approved_role']}");
    }
}

// Check history record
$history_result = $conn->query("SELECT * FROM approvedaccount_history ORDER BY approved_at DESC LIMIT 1");

// Check approved account
$approved_result = $conn->query("SELECT * FROM approved_account ORDER BY created_at DESC LIMIT 1");

?>

<div class="container-fluid">
    <section class="panel">
        <div class="container-fluid">
            <div class="table-header">
                <div class="title">
                    <span>
                        <h2>Manage User Requests</h2>
                    </span>
                    <span style="font-size: 12px;">List of leave, overtime, and other user requests</span>
                </div>
                <div class="title">
                    <span><?php echo date('l, F jS'); ?></span>
                </div>
            </div>

            <!-- Search and Filter Section -->
            <div class="search-filter-section">
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" id="searchInput" class="form-control" placeholder="Search by user or description...">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="searchButton">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select id="statusFilter" class="form-control">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select id="typeFilter" class="form-control">
                            <option value="">All Types</option>
                            <option value="leave">Leave</option>
                            <option value="overtime">Overtime</option>
                            <option value="schedule_change">Schedule Change</option>
                            <option value="password">Password</option>
                            <option value="account">Account Change</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="table-container">
                <table class="table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>User</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Requested On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="requestTableBody">
                        <?php
                        /* // Temporarily comment out the data fetching block
                        try {
                            // ... (existing data fetching code from line 307 to 409) ...
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='7'>Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>"; // Updated colspan
                        }
                        */
                        echo "<tr><td colspan='7'>Data fetching temporarily disabled for debugging.</td></tr>"; // Placeholder message
                        ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation" class="mt-3">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page-1; ?>&status=<?php echo $_GET['status'] ?? ''; ?>&type=<?php echo $_GET['type'] ?? ''; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>">
                                    Previous
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo $_GET['status'] ?? ''; ?>&type=<?php echo $_GET['type'] ?? ''; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page+1; ?>&status=<?php echo $_GET['status'] ?? ''; ?>&type=<?php echo $_GET['type'] ?? ''; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>">
                                    Next
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<!-- View Request Modal -->
<div class="modal fade" id="requestModal" tabindex="-1" role="dialog" aria-labelledby="requestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="requestModalLabel">View User Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="requestModalBody">
                Loading request details...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
// Page::render($content);
?>
