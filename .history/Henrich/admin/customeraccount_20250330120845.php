<?php
require_once 'access_control.php';
require_once '../includes/Page.php';
require_once '../includes/functions.php';

// Initialize page
Page::setTitle('Customer Accounts - HFC Admin');
Page::setBodyClass('admin-page');
Page::setCurrentPage('customeraccount');

// Add required styles
Page::addStyle('../assets/css/admin.css');
Page::addStyle('../assets/css/table.css');
Page::addStyle('../assets/css/customer.css');

// Add required scripts
Page::addScript('../assets/js/customer-management.js');

ob_start();
?>

<div class="container-fluid">
    <?php include 'admin-sidebar.php';?>

    <section class="panel">
        <?php include '../reusable/navbarNoSearch.html'; ?>
        <div class="container-fluid">
            <div class="table-header">
                <div class="title">
                    <span>
                        <h2>Customer Accounts</h2>
                    </span>
                    <span style="font-size: 12px;">List of all customer accounts</span>
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
                            <input type="text" id="searchInput" class="form-control" placeholder="Search customers...">
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
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-4 text-right">
                        <button class="btn btn-primary" onclick="showAddCustomerModal()">
                            <i class="fas fa-plus"></i> Add New Customer
                        </button>
                        <button class="btn btn-success" onclick="exportCustomers()">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-container">
                <table class="table-striped">
                    <thead>
                        <tr>
                            <th>Profile Picture</th>
                            <th>Account ID</th>
                            <th>Customer Name</th>
                            <th>Customer Address</th>
                            <th>Customer Phone Number</th>
                            <th>Customer ID</th>
                            <th>Username</th>
                            <th>Password</th>
                            <th>User Email</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="customerTableBody">
                        <?php
                        try {
                            // Pagination
                            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                            $limit = 10;
                            $offset = ($page - 1) * $limit;

                            // Get total count
                            $countStmt = $GLOBALS['pdo']->query("SELECT COUNT(*) FROM customeraccount");
                            $total_records = $countStmt->fetchColumn();
                            $total_pages = ceil($total_records / $limit);

                            // Get customers with pagination
                            $stmt = $GLOBALS['pdo']->prepare("SELECT * FROM customeraccount LIMIT ? OFFSET ?");
                            $stmt->execute([$limit, $offset]);
                            $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if (count($customers) > 0) {
                                foreach ($customers as $row) {
                                    // Set default status if not exists
                                    $status = isset($row['status']) ? $row['status'] : 'active';
                                    $statusClass = $status === 'active' ? 'success' : 'danger';
                                    
                                    echo "<tr>
                                        <td><img src='" . htmlspecialchars($row['profilepicture'] ?? 'default.jpg') . "' alt='Profile Picture' width='50' height='50'></td>
                                        <td>" . htmlspecialchars($row['accountid']) . "</td>
                                        <td>" . htmlspecialchars($row['customername']) . "</td>
                                        <td>" . htmlspecialchars($row['customeraddress']) . "</td>
                                        <td>" . htmlspecialchars($row['customerphonenumber']) . "</td>
                                        <td>" . htmlspecialchars($row['customerid']) . "</td>
                                        <td>" . htmlspecialchars($row['username']) . "</td>
                                        <td>••••••••</td>
                                        <td>" . htmlspecialchars($row['useremail']) . "</td>
                                        <td><span class='badge badge-{$statusClass}'>" . htmlspecialchars($status) . "</span></td>
                                        <td>
                                            <button class='btn btn-sm btn-info' onclick='editCustomer(" . $row['accountid'] . ")'><i class='fas fa-edit'></i></button>
                                            <button class='btn btn-sm btn-danger' onclick='deleteCustomer(" . $row['accountid'] . ")'><i class='fas fa-trash'></i></button>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='11'>0 results</td></tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='11'>Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
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

<!-- Add/Edit Customer Modal -->
<div class="modal fade" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="customerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customerModalLabel">Add New Customer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="customerForm">
                    <input type="hidden" id="accountId">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="customerName">Customer Name</label>
                                <input type="text" class="form-control" id="customerName" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="customerAddress">Customer Address</label>
                                <input type="text" class="form-control" id="customerAddress" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="customerPhone">Phone Number</label>
                                <input type="tel" class="form-control" id="customerPhone" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="customerEmail">Email</label>
                                <input type="email" class="form-control" id="customerEmail" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password">
                                <small class="form-text text-muted">Leave blank to keep current password when editing</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="profilePicture">Profile Picture</label>
                                <input type="file" class="form-control-file" id="profilePicture" accept="image/*">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveCustomer()">Save Customer</button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
Page::render($content);
?>

