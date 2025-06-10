<?php
require_once 'access_control.php';
require_once '../includes/Page.php';
require_once '../includes/functions.php';

// Initialize page
Page::setTitle('Rejection History - HFC Admin');
Page::setBodyClass('admin-page');
Page::setCurrentPage('rejection-history');
Page::setAdminPage(true);

// Add required styles
Page::addStyle('../assets/css/admin.css');
Page::addStyle('../assets/css/table.css');

// Add required scripts
Page::addScript('../assets/js/rejection-history.js');

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
                        <h2>Account Request Rejection History</h2>
                    </span>
                    <span style="font-size: 12px;">View history of rejected account requests and their reasons</span>
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
                            <input type="text" id="searchInput" class="form-control" placeholder="Search by name, email, or reason...">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="searchButton">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select id="dateFilter" class="form-control">
                            <option value="">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">Last 7 Days</option>
                            <option value="month">Last 30 Days</option>
                            <option value="year">This Year</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select id="departmentFilter" class="form-control">
                            <option value="">All Departments</option>
                            <option value="Warehouse">Warehouse</option>
                            <option value="Logistics">Logistics</option>
                            <option value="Inventory">Inventory</option>
                            <option value="Quality Control">Quality Control</option>
                            <option value="Administration">Administration</option>
                        </select>
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
                            <th>Rejection Reason</th>
                            <th>Rejected On</th>
                            <th>Rejected By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="rejectionTableBody">
                        <?php
                        try {
                            // First check if table exists to avoid fatal errors
                            $tableCheck = $GLOBALS['pdo']->query("SHOW TABLES LIKE 'account_requests'");
                            if ($tableCheck->rowCount() == 0) {
                                echo "<tr><td colspan='9'>The account_requests table does not exist. Please run the database setup script.</td></tr>";
                            } else {
                                // Build query based on filters
                                $where = ["status = 'rejected'"]; // Always filter for rejected requests
                                $params = [];
                                
                                if (!empty($_GET['department'])) {
                                    $where[] = "department = ?";
                                    $params[] = $_GET['department'];
                                }
                                
                                if (!empty($_GET['search'])) {
                                    $where[] = "(firstname LIKE ? OR lastname LIKE ? OR email LIKE ? OR rejection_reason LIKE ?)";
                                    $search = "%{$_GET['search']}%";
                                    $params = array_merge($params, [$search, $search, $search, $search]);
                                }
                                
                                // Date filtering
                                if (!empty($_GET['date'])) {
                                    switch ($_GET['date']) {
                                        case 'today':
                                            $where[] = "DATE(processed_date) = CURDATE()";
                                            break;
                                        case 'week':
                                            $where[] = "processed_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                                            break;
                                        case 'month':
                                            $where[] = "processed_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
                                            break;
                                        case 'year':
                                            $where[] = "YEAR(processed_date) = YEAR(CURDATE())";
                                            break;
                                    }
                                }
                                
                                $whereClause = "WHERE " . implode(" AND ", $where);

                                // Get total count
                                $countQuery = "SELECT COUNT(*) FROM account_requests $whereClause";
                                $countStmt = $GLOBALS['pdo']->prepare($countQuery);
                                $countStmt->execute($params);
                                $total_records = $countStmt->fetchColumn();
                                $total_pages = ceil($total_records / $limit);

                                // Get rejected requests with pagination
                                $query = "
                                    SELECT r.*, 
                                           u.first_name AS processor_first_name, 
                                           u.last_name AS processor_last_name 
                                    FROM account_requests r
                                    LEFT JOIN users u ON r.processed_by = u.id
                                    $whereClause
                                    ORDER BY r.processed_date DESC 
                                    LIMIT ? OFFSET ?
                                ";
                                $params[] = $limit;
                                $params[] = $offset;
                                
                                $stmt = $GLOBALS['pdo']->prepare($query);
                                $stmt->execute($params);
                                $rejections = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                if (count($rejections) > 0) {
                                    foreach ($rejections as $row) {
                                        $rejectionReason = !empty($row['rejection_reason']) ? 
                                            htmlspecialchars($row['rejection_reason']) : 
                                            '<em>No reason provided</em>';
                                        
                                        $rejectedBy = !empty($row['processor_first_name']) ? 
                                            htmlspecialchars($row['processor_first_name'] . ' ' . $row['processor_last_name']) : 
                                            '<em>Unknown</em>';
                                        
                                        echo "<tr>
                                            <td>" . htmlspecialchars($row['request_id']) . "</td>
                                            <td>" . htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) . "</td>
                                            <td>" . htmlspecialchars($row['email']) . "</td>
                                            <td>" . htmlspecialchars($row['department']) . "</td>
                                            <td>" . htmlspecialchars($row['position']) . "</td>
                                            <td>" . $rejectionReason . "</td>
                                            <td>" . date('M d, Y H:i', strtotime($row['processed_date'])) . "</td>
                                            <td>" . $rejectedBy . "</td>
                                            <td>
                                                <button class='btn btn-sm btn-info' onclick='viewRejection(" . $row['request_id'] . ")'><i class='fas fa-eye'></i></button>
                                                <button class='btn btn-sm btn-secondary' onclick='exportRejection(" . $row['request_id'] . ")'><i class='fas fa-download'></i></button>
                                            </td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='9'>No rejected account requests found</td></tr>";
                                }
                            }
                        } catch (PDOException $e) {
                            error_log("Error in rejection-history.php: " . $e->getMessage());
                            echo "<tr><td colspan='9'>Error: Unable to fetch rejection history. Please check the error log.</td></tr>";
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
                                <a class="page-link" href="?page=<?php echo $page-1; ?>&date=<?php echo $_GET['date'] ?? ''; ?>&department=<?php echo $_GET['department'] ?? ''; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>">
                                    Previous
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&date=<?php echo $_GET['date'] ?? ''; ?>&department=<?php echo $_GET['department'] ?? ''; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page+1; ?>&date=<?php echo $_GET['date'] ?? ''; ?>&department=<?php echo $_GET['department'] ?? ''; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>">
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

<!-- View Rejection Modal -->
<div class="modal fade" id="rejectionModal" tabindex="-1" role="dialog" aria-labelledby="rejectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectionModalLabel">View Rejected Request Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="rejectionModalBody">
                Loading request details...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="exportBtn">Export to PDF</button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
Page::render($content);
?> 