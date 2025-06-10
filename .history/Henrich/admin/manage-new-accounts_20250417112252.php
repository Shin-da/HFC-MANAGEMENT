<?php
require_once 'access_control.php';
require_once '../includes/Page.php';
require_once '../includes/functions.php';

// Initialize page
Page::setTitle('Manage New Account Requests - HFC Admin');
Page::setBodyClass('admin-page');
Page::setCurrentPage('manage-new-accounts'); // New page identifier
Page::setAdminPage(true);

// Add required styles
Page::addStyle('../assets/css/admin.css');
Page::addStyle('../assets/css/table.css');

// Add required scripts
Page::addScript('../assets/js/new-account-requests.js'); // Needs a new JS file

// Initialize pagination variables
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$total_records = 0;
$total_pages = 1;

ob_start();
?>

<div class="container-fluid">
    <section class="panel">
        <div class="container-fluid">
            <div class="table-header">
                <div class="title">
                    <span>
                        <h2>Manage New Account Requests</h2>
                    </span>
                    <span style="font-size: 12px;">List of all pending new account applications</span>
                </div>
                <div class="title">
                    <span><?php echo date('l, F jS'); ?></span>
                </div>
            </div>

            <!-- Search and Filter Section (Adapt as needed) -->
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
                            <option value="pending" selected>Pending</option> <!-- Default to pending -->
                            <option value="processed">Processed</option> <!-- Assuming status might change -->
                        </select>
                    </div>
                     <div class="col-md-4">
                        <!-- Add other filters if needed, e.g., by department -->
                    </div>
                </div>
            </div>

            <div class="table-container">
                <table class="table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Position</th> 
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Requested On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="requestTableBody">
                        <?php
                        try {
                            // Build query based on filters - Reading from account_requests (plural)
                            $where = [];
                            $params = [];
                            
                            // Default to pending unless specified otherwise
                            $status_filter = $_GET['status'] ?? 'pending';
                            $where[] = "status = ?";
                            $params[] = $status_filter;
                                                        
                            if (!empty($_GET['search'])) {
                                $where[] = "(firstname LIKE ? OR lastname LIKE ? OR email LIKE ?)";
                                $search = "%{$_GET['search']}%";
                                $params = array_merge($params, [$search, $search, $search]);
                            }
                            
                            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

                            // Get total count from account_requests
                            $countQuery = "SELECT COUNT(*) FROM account_requests $whereClause";
                            $countStmt = $GLOBALS['pdo']->prepare($countQuery);
                            $countStmt->execute($params);
                            $total_records = $countStmt->fetchColumn();
                            $total_pages = ceil($total_records / $limit);

                            // Get requests with pagination from account_requests
                            // Assuming PK is 'request_id', adjust if different (e.g., 'id')
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
                                    $statusClass = ($row['status'] == 'pending') ? 'warning' : 'secondary';
                                    // Assuming PK is 'request_id', adjust if needed
                                    $requestPk = $row['request_id'] ?? $row['id']; // Adjust based on actual PK column name

                                    echo "<tr>
                                        <td>" . htmlspecialchars($requestPk) . "</td>
                                        <td>" . htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) . "</td>
                                        <td>" . htmlspecialchars($row['email']) . "</td>
                                        <td>" . htmlspecialchars($row['department']) . "</td>
                                        <td>" . htmlspecialchars($row['position']) . "</td>
                                        <td>" . htmlspecialchars($row['reason']) . "</td>
                                        <td><span class='badge badge-{$statusClass}'>" . htmlspecialchars($row['status']) . "</span></td>
                                        <td>" . date('M d, Y H:i', strtotime($row['request_date'])) . "</td>
                                        <td>
                                            <!-- <button class='btn btn-sm btn-info' onclick='viewNewAccountRequest(" . $requestPk . ")'><i class='fas fa-eye'></i></button> -->
                                            <button class='btn btn-sm btn-success' onclick='approveNewAccountRequest(" . $requestPk . ")'><i class='fas fa-check'></i></button>
                                            <button class='btn btn-sm btn-danger' onclick='rejectNewAccountRequest(" . $requestPk . ")'><i class='fas fa-times'></i></button>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='9'>No pending new account requests found</td></tr>";
                            }
                        } catch (PDOException $e) {
                            error_log("Error fetching new account requests: " . $e->getMessage());
                            echo "<tr><td colspan='9'>Error fetching data. Please check logs.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <!-- Pagination (similar structure to manage-requests.php) -->
                 <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation" class="mt-3">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page-1; ?>&status=<?php echo $status_filter; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>">
                                    Previous
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page+1; ?>&status=<?php echo $status_filter; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>">
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

<?php
$content = ob_get_clean();
// Page::setContent($content); // Keep this removed
Page::render($content); // Pass the captured content as an argument
?> 