<?php
require_once 'access_control.php';
require_once '../includes/Page.php';
require_once '../includes/functions.php';

// Initialize page
Page::setTitle('Manage Account Requests - HFC Admin');
Page::setBodyClass('admin-page');
Page::setCurrentPage('manage-account-requests');
Page::setAdminPage(true);

// Add required styles
Page::addStyle('../assets/css/admin.css');
Page::addStyle('../assets/css/table.css');

// Add required scripts
Page::addScript('../assets/js/account-requests.js');

// Initialize pagination variables
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$total_records = 0;
$total_pages = 1;

ob_start();
?>

<style>
    /* Bulk Actions Styling */
    .bulk-actions {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        border: 1px solid #ddd;
    }
    
    .bulk-actions .btn-group {
        margin-bottom: 0;
    }
    
    .table-striped td:first-child {
        width: 40px;
        text-align: center;
    }
    
    .table-striped th:first-child {
        width: 40px;
        text-align: center;
    }
    
    /* Custom checkbox styling */
    input[type="checkbox"] {
        cursor: pointer;
        width: 18px;
        height: 18px;
    }
    
    input[type="checkbox"]:disabled {
        cursor: not-allowed;
        opacity: 0.5;
    }
    
    /* Highlight pending rows */
    tr.pending-request {
        background-color: rgba(255, 243, 205, 0.2);
    }
    
    .text-right {
        text-align: right;
    }
</style>

<div class="container-fluid">
    <section class="panel">
        <div class="container-fluid">
            <div class="table-header">
                <div class="title">
                    <span>
                        <h2>Manage Account Requests</h2>
                    </span>
                    <span style="font-size: 12px;">Review and process new account requests</span>
                </div>
                <div class="title">
                    <span><?php echo date('l, F jS'); ?></span>
                    <a href="rejection-history.php" class="btn btn-info btn-sm ml-2">
                        <i class="fas fa-history"></i> View Rejection History
                    </a>
                </div>
            </div>

            <!-- Search and Filter Section -->
            <div class="search-filter-section">
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" id="searchInput" class="form-control" placeholder="Search by name or email...">
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
                            <option value="pending" <?php echo isset($_GET['status']) && $_GET['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="approved" <?php echo isset($_GET['status']) && $_GET['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                            <option value="rejected" <?php echo isset($_GET['status']) && $_GET['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select id="departmentFilter" class="form-control">
                            <option value="">All Departments</option>
                            <option value="Warehouse" <?php echo isset($_GET['department']) && $_GET['department'] === 'Warehouse' ? 'selected' : ''; ?>>Warehouse</option>
                            <option value="Logistics" <?php echo isset($_GET['department']) && $_GET['department'] === 'Logistics' ? 'selected' : ''; ?>>Logistics</option>
                            <option value="Inventory" <?php echo isset($_GET['department']) && $_GET['department'] === 'Inventory' ? 'selected' : ''; ?>>Inventory</option>
                            <option value="Quality Control" <?php echo isset($_GET['department']) && $_GET['department'] === 'Quality Control' ? 'selected' : ''; ?>>Quality Control</option>
                            <option value="Administration" <?php echo isset($_GET['department']) && $_GET['department'] === 'Administration' ? 'selected' : ''; ?>>Administration</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Bulk Actions -->
            <div class="bulk-actions mb-3">
                <div class="row">
                    <div class="col-md-6">
                        <div class="btn-group">
                            <button id="selectAllBtn" class="btn btn-outline-secondary">
                                <i class="fas fa-check-square"></i> Select All
                            </button>
                            <button id="deselectAllBtn" class="btn btn-outline-secondary">
                                <i class="fas fa-square"></i> Deselect All
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 text-right">
                        <div class="btn-group">
                            <button id="bulkApproveBtn" class="btn btn-success" disabled>
                                <i class="fas fa-check"></i> Approve Selected
                            </button>
                            <button id="bulkRejectBtn" class="btn btn-danger" disabled>
                                <i class="fas fa-times"></i> Reject Selected
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-container">
                <table class="table-striped">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="masterCheckbox"></th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Status</th>
                            <th>Requested On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="requestTableBody">
                        <?php
                        try {
                            // First check if table exists to avoid fatal errors
                            $tableCheck = $GLOBALS['pdo']->query("SHOW TABLES LIKE 'account_requests'");
                            if ($tableCheck->rowCount() == 0) {
                                echo "<tr><td colspan='9'>The account_requests table does not exist. Please run the database setup script.</td></tr>";
                            } else {
                                // Build query based on filters
                                $where = [];
                                $params = [];
                                
                                if (!empty($_GET['status'])) {
                                    $where[] = "status = ?";
                                    $params[] = $_GET['status'];
                                }
                                
                                if (!empty($_GET['department'])) {
                                    $where[] = "department = ?";
                                    $params[] = $_GET['department'];
                                }
                                
                                if (!empty($_GET['search'])) {
                                    $where[] = "(firstname LIKE ? OR lastname LIKE ? OR email LIKE ?)";
                                    $search = "%{$_GET['search']}%";
                                    $params = array_merge($params, [$search, $search, $search]);
                                }
                                
                                $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

                                // Get total count
                                $countQuery = "SELECT COUNT(*) FROM account_requests $whereClause";
                                $countStmt = $GLOBALS['pdo']->prepare($countQuery);
                                $countStmt->execute($params);
                                $total_records = $countStmt->fetchColumn();
                                $total_pages = ceil($total_records / $limit);

                                // Get requests with pagination
                                $query = "
                                    SELECT * 
                                    FROM account_requests 
                                    $whereClause
                                    ORDER BY request_date DESC 
                                    LIMIT ? OFFSET ?
                                ";
                                $params[] = $limit;
                                $params[] = $offset;
                                
                                $stmt = $GLOBALS['pdo']->prepare($query);
                                $stmt->execute($params);
                                $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                if (count($requests) > 0) {
                                    foreach ($requests as $row) {
                                        // Debug: Log the status value
                                        error_log("Row status: " . ($row['status'] ?? 'NULL'));
                                        
                                        // Define status display values and colors
                                        $statusDisplay = match($row['status'] ?? '') {
                                            'pending' => [
                                                'text' => 'PENDING',
                                                'color' => '#ffc107',  // Yellow
                                                'text_color' => '#000000'  // Black text for better contrast
                                            ],
                                            'approved' => [
                                                'text' => 'APPROVED',
                                                'color' => '#28a745',  // Green
                                                'text_color' => '#ffffff'  // White text
                                            ],
                                            'rejected' => [
                                                'text' => 'REJECTED',
                                                'color' => '#dc3545',  // Red
                                                'text_color' => '#ffffff'  // White text
                                            ],
                                            default => [
                                                'text' => 'UNKNOWN',
                                                'color' => '#6c757d',  // Gray
                                                'text_color' => '#ffffff'  // White text
                                            ]
                                        };
                                        
                                        // Determine if checkbox should be disabled based on status
                                        $checkboxDisabled = $row['status'] !== 'pending' ? 'disabled' : '';
                                        
                                        echo "<tr data-id='" . htmlspecialchars($row['request_id']) . "' class='" . ($row['status'] === 'pending' ? 'pending-request' : '') . "'>
                                            <td><input type='checkbox' class='request-checkbox' data-id='" . htmlspecialchars($row['request_id']) . "' $checkboxDisabled></td>
                                            <td>" . htmlspecialchars($row['request_id']) . "</td>
                                            <td>" . htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) . "</td>
                                            <td>" . htmlspecialchars($row['email']) . "</td>
                                            <td>" . htmlspecialchars($row['department']) . "</td>
                                            <td>" . htmlspecialchars($row['position']) . "</td>
                                            <td>
                                                <div style='
                                                    display: inline-block;
                                                    padding: 5px 10px;
                                                    background-color: " . $statusDisplay['color'] . ";
                                                    color: " . $statusDisplay['text_color'] . ";
                                                    font-weight: bold;
                                                    border-radius: 4px;
                                                    text-align: center;
                                                    min-width: 80px;
                                                '>" . $statusDisplay['text'] . "</div>
                                            </td>
                                            <td>" . date('M d, Y H:i', strtotime($row['request_date'])) . "</td>
                                            <td>
                                                <button class='btn btn-sm btn-info' onclick='viewRequest(" . $row['request_id'] . ")'><i class='fas fa-eye'></i></button>
                                                <button class='btn btn-sm btn-success' onclick='approveRequest(" . $row['request_id'] . ")' " . ($row['status'] !== 'pending' ? 'disabled' : '') . "><i class='fas fa-check'></i></button>
                                                <button class='btn btn-sm btn-danger' onclick='rejectRequest(" . $row['request_id'] . ")' " . ($row['status'] !== 'pending' ? 'disabled' : '') . "><i class='fas fa-times'></i></button>
                                            </td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='9'>No account requests found</td></tr>";
                                }
                            }
                        } catch (PDOException $e) {
                            error_log("Error in manage-account-requests.php: " . $e->getMessage());
                            echo "<tr><td colspan='9'>Error: Unable to fetch account requests. Please check the error log.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation" class="mt-3">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page-1; ?>&status=<?php echo $_GET['status'] ?? ''; ?>&department=<?php echo $_GET['department'] ?? ''; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>">
                                    Previous
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo $_GET['status'] ?? ''; ?>&department=<?php echo $_GET['department'] ?? ''; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page+1; ?>&status=<?php echo $_GET['status'] ?? ''; ?>&department=<?php echo $_GET['department'] ?? ''; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>">
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
                <h5 class="modal-title" id="requestModalLabel">View Account Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="requestModalBody">
                Loading request details...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="approveRequestBtn">Approve</button>
                <button type="button" class="btn btn-danger" id="rejectRequestBtn">Reject</button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
Page::render($content);
?>
