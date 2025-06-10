<?php
require_once 'access_control.php';
require_once '../includes/Page.php';
require_once '../includes/functions.php';

// Initialize page
Page::setTitle('Manage Requests - HFC Admin');
Page::setBodyClass('admin-page');
Page::setCurrentPage('manage-requests');
Page::setAdminPage(true);

// Add required styles
Page::addStyle('../assets/css/admin.css');
Page::addStyle('../assets/css/table.css');

// Add required scripts
Page::addScript('../assets/js/requests.js');

ob_start();
?>

<div class="container-fluid">
    <section class="panel">
        <div class="container-fluid">
            <div class="table-header">
                <div class="title">
                    <span>
                        <h2>Manage Requests</h2>
                    </span>
                    <span style="font-size: 12px;">List of all requests and their status</span>
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
                            <input type="text" id="searchInput" class="form-control" placeholder="Search requests...">
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
                            <option value="account">Account</option>
                            <option value="password">Password</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="table-container">
                <table class="table-striped">
                    <thead>
                        <tr>
                            <th>Request ID</th>
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
                        try {
                            // Pagination
                            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                            $limit = 10;
                            $offset = ($page - 1) * $limit;

                            // Get total count
                            $countStmt = $GLOBALS['pdo']->query("SELECT COUNT(*) FROM requests");
                            $total_records = $countStmt->fetchColumn();
                            $total_pages = ceil($total_records / $limit);

                            // Get requests with pagination
                            $stmt = $GLOBALS['pdo']->prepare("
                                SELECT r.*, u.username, u.email 
                                FROM requests r 
                                LEFT JOIN users u ON r.user_id = u.user_id 
                                ORDER BY r.created_at DESC 
                                LIMIT ? OFFSET ?
                            ");
                            $stmt->execute([$limit, $offset]);
                            $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if (count($requests) > 0) {
                                foreach ($requests as $row) {
                                    $statusClass = match($row['status']) {
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        default => 'secondary'
                                    };
                                    
                                    $typeClass = match($row['type']) {
                                        'account' => 'info',
                                        'password' => 'primary',
                                        default => 'secondary'
                                    };
                                    
                                    echo "<tr>
                                        <td>" . htmlspecialchars($row['request_id']) . "</td>
                                        <td><span class='badge badge-{$typeClass}'>" . htmlspecialchars($row['type']) . "</span></td>
                                        <td>" . htmlspecialchars($row['username']) . "</td>
                                        <td>" . htmlspecialchars($row['description']) . "</td>
                                        <td><span class='badge badge-{$statusClass}'>" . htmlspecialchars($row['status']) . "</span></td>
                                        <td>" . date('M d, Y H:i', strtotime($row['created_at'])) . "</td>
                                        <td>
                                            <button class='btn btn-sm btn-info' onclick='viewRequest(" . $row['request_id'] . ")'><i class='fas fa-eye'></i></button>
                                            <button class='btn btn-sm btn-success' onclick='approveRequest(" . $row['request_id'] . ")'><i class='fas fa-check'></i></button>
                                            <button class='btn btn-sm btn-danger' onclick='rejectRequest(" . $row['request_id'] . ")'><i class='fas fa-times'></i></button>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7'>0 results</td></tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='7'>Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation" class="mt-3">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
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
                <h5 class="modal-title" id="requestModalLabel">View Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Request Type</label>
                            <p id="viewType"></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status</label>
                            <p id="viewStatus"></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>User</label>
                            <p id="viewUser"></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email</label>
                            <p id="viewEmail"></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Description</label>
                            <p id="viewDescription"></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Requested On</label>
                            <p id="viewRequestedOn"></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Last Updated</label>
                            <p id="viewLastUpdated"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" onclick="approveRequest()">Approve</button>
                <button type="button" class="btn btn-danger" onclick="rejectRequest()">Reject</button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
Page::render($content);
?> 