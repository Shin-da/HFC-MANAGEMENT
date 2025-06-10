<?php
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/Page.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');
Page::setCurrentPage($current_page);

try {
    // First check if the table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'customerdetails'");
    if ($table_check->num_rows == 0) {
        // Create the table if it doesn't exist
        $create_table = "CREATE TABLE IF NOT EXISTS customerdetails (
            customerid INT PRIMARY KEY AUTO_INCREMENT,
            customername VARCHAR(255) NOT NULL,
            username VARCHAR(100),
            useremail VARCHAR(255),
            customeraddress TEXT,
            customerphonenumber VARCHAR(20),
            accountstatus ENUM('Active', 'Inactive', 'Deleted') DEFAULT 'Active',
            accounttype VARCHAR(50) DEFAULT 'Customer',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            modified_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        $conn->query($create_table);
    }

    // Get total records count with error handling
    $count_query = "SELECT COUNT(*) as total FROM customerdetails";
    $count_result = $conn->query($count_query);
    if ($count_result === false) {
        throw new Exception("Error counting records: " . $conn->error);
    }
    $total_records = $count_result->fetch_assoc()['total'];

    // Calculate pagination
    $limit = 10;
    $total_pages = max(1, ceil($total_records / $limit));
    $page = isset($_GET['page']) ? min(max(1, intval($_GET['page'])), $total_pages) : 1;
    $offset = ($page - 1) * $limit;

    // Get today's registrations with error handling
    $today_query = "SELECT COUNT(*) as count FROM customerdetails 
                   WHERE DATE(created_at) = CURDATE()";
    $today_result = $conn->query($today_query);
    if ($today_result === false) {
        throw new Exception("Error counting today's records: " . $conn->error);
    }
    $today_registered = $today_result->fetch_assoc()['count'];

    // Main query for fetching customer details
    $sql = "SELECT customerid, customername, customeraddress, customerphonenumber, accountstatus 
            FROM customerdetails 
            ORDER BY customerid DESC 
            LIMIT ? OFFSET ?";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Error preparing statement: " . $conn->error);
    }
    
    $stmt->bind_param("ii", $limit, $offset);
    if (!$stmt->execute()) {
        throw new Exception("Error executing query: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    if ($result === false) {
        throw new Exception("Error getting result set: " . $stmt->error);
    }

} catch (Exception $e) {
    error_log("Error in customeraccount.php: " . $e->getMessage());
    $error_message = $e->getMessage();
    $total_records = 0;
    $today_registered = 0;
    $total_pages = 1;
    $result = null;
}

// Set page title and styles
Page::setTitle('Customer Accounts');
Page::addStyle('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css');
Page::addScript('https://cdn.jsdelivr.net/npm/sweetalert2@11');

ob_start();
?>

<div class="overview">
    <div class="dashboard-header">
        <h1>Customer Accounts</h1>
        <div class="stats-container">
            <div class="stat-card">
                <h3>Total Accounts</h3>
                <p class="stat-number"><?php echo $total_records; ?></p>
            </div>
            <div class="stat-card">
                <h3>New Today</h3>
                <p class="stat-number"><?php echo $today_registered; ?></p>
            </div>
        </div>
        
        <input type="text" id="searchAccount" class="search-box" placeholder="Search accounts...">
    </div>

    <div class="table-container">
        <?php if ($result && $result->num_rows > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th>Address</th>
                        <th>Phone Number</th>
                        <th>Account Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['customername']); ?></td>
                            <td><?php echo htmlspecialchars($row['customeraddress']); ?></td>
                            <td><?php echo htmlspecialchars($row['customerphonenumber']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($row['accountstatus']); ?>">
                                    <?php echo htmlspecialchars($row['accountstatus']); ?>
                                </span>
                            </td>
                            <td>
                                <button class="action-btn view-btn" onclick="viewCustomer(<?php echo $row['customerid']; ?>)">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="action-btn delete-btn" onclick="deleteAccount(<?php echo $row['customerid']; ?>)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No customers found.</p>
        <?php endif; ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                <button onclick="changePage(<?php echo $i; ?>)" <?php if ($i == $page) echo 'class="active"'; ?>>
                    <?php echo $i; ?>
                </button>
            <?php } ?>
        </div>
    </div>
</div>

<script>
    // Define color variables for JavaScript
    const colors = {
        primary: '#4CAF50',
        danger: '#f44336',
        secondary: '#598777'
    };

    // Search functionality
    document.getElementById('searchAccount').addEventListener('input', function(e) {
        let searchText = e.target.value.toLowerCase();
        let rows = document.querySelectorAll('.table tbody tr');
        
        rows.forEach(row => {
            let text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchText) ? '' : 'none';
        });
    });

    // View account details
    async function viewCustomer(id) {
        try {
            Swal.fire({
                title: 'Loading customer details',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const response = await fetch(`get_customer_account.php?id=${id}`);
            const data = await response.json();
            
            if (data.success) {
                Swal.fire({
                    title: 'Customer Details',
                    html: `
                        <div class="customer-details-grid">
                            <div class="detail-item">
                                <label>Name:</label>
                                <span>${data.account.customername}</span>
                            </div>
                            <div class="detail-item">
                                <label>Address:</label>
                                <span>${data.account.customeraddress}</span>
                            </div>
                            <div class="detail-item">
                                <label>Phone:</label>
                                <span>${data.account.customerphonenumber}</span>
                            </div>
                            <div class="detail-item">
                                <label>Status:</label>
                                <span class="status-${data.account.accountstatus.toLowerCase()}">${data.account.accountstatus}</span>
                            </div>
                            <div class="detail-item">
                                <label>Total Orders:</label>
                                <span>${data.orderCount}</span>
                            </div>
                            <div class="detail-item">
                                <label>Account Type:</label>
                                <span>${data.account.accounttype || 'Customer'}</span>
                            </div>
                            <div class="detail-item">
                                <label>Created:</label>
                                <span>${new Date(data.account.created_at).toLocaleDateString()}</span>
                            </div>
                        </div>
                        <div class="detail-actions">
                            <button class="btn-edit" onclick="editCustomer(${id})">Edit Account</button>
                            <button class="btn-delete" onclick="deleteAccount(${id})">Delete Account</button>
                        </div>
                    `,
                    width: '600px',
                    confirmButtonColor: colors.primary,
                    confirmButtonText: 'Close'
                });
            } else {
                throw new Error(data.message || 'Failed to load customer details');
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: error.message,
                confirmButtonColor: colors.danger
            });
        }
    }

    // Edit customer function
    function editCustomer(id) {
        // Close current modal
        Swal.close();
        
        // Fetch customer data for editing
        fetch(`get_customer_account.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Edit Customer',
                        html: `
                            <form id="editCustomerForm" class="swal-form">
                                <div class="form-group">
                                    <label for="customername">Name:</label>
                                    <input type="text" id="customername" class="swal2-input" value="${data.account.customername}">
                                </div>
                                <div class="form-group">
                                    <label for="customeraddress">Address:</label>
                                    <input type="text" id="customeraddress" class="swal2-input" value="${data.account.customeraddress}">
                                </div>
                                <div class="form-group">
                                    <label for="customerphonenumber">Phone:</label>
                                    <input type="text" id="customerphonenumber" class="swal2-input" value="${data.account.customerphonenumber}">
                                </div>
                                <div class="form-group">
                                    <label for="accountstatus">Status:</label>
                                    <select id="accountstatus" class="swal2-input">
                                        <option value="Active" ${data.account.accountstatus === 'Active' ? 'selected' : ''}>Active</option>
                                        <option value="Inactive" ${data.account.accountstatus === 'Inactive' ? 'selected' : ''}>Inactive</option>
                                    </select>
                                </div>
                            </form>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Update',
                        confirmButtonColor: colors.primary,
                        cancelButtonColor: colors.secondary,
                        preConfirm: () => {
                            return {
                                id: id,
                                customername: document.getElementById('customername').value,
                                customeraddress: document.getElementById('customeraddress').value,
                                customerphonenumber: document.getElementById('customerphonenumber').value,
                                accountstatus: document.getElementById('accountstatus').value
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Send update request
                            fetch('update_customer_account.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify(result.value)
                            })
                            .then(response => response.json())
                            .then(updateData => {
                                if (updateData.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Updated!',
                                        text: 'Customer information has been updated.',
                                        confirmButtonColor: colors.primary
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    throw new Error(updateData.message || 'Failed to update customer');
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: error.message,
                                    confirmButtonColor: colors.danger
                                });
                            });
                        }
                    });
                } else {
                    throw new Error(data.message || 'Failed to load customer data for editing');
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: error.message,
                    confirmButtonColor: colors.danger
                });
            });
    }

    // Delete account
    function deleteAccount(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This action can't be undone",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: colors.danger,
            cancelButtonColor: colors.secondary,
            confirmButtonText: 'Yes, delete it!'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch('delete_customer_account.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: id })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        await Swal.fire('Deleted!', 'Account has been deleted.', 'success');
                        window.location.reload();
                    } else {
                        throw new Error(data.message);
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to delete account.',
                        confirmButtonColor: colors.danger
                    });
                }
            }
        });
    }

    // Change page
    function changePage(pageNum) {
        window.location.href = `?page=${pageNum}`;
    }

    // Initialize search with debouncing
    const searchInput = document.getElementById('searchAccount');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                let searchText = searchInput.value.toLowerCase();
                let rows = document.querySelectorAll('.table tbody tr');
                
                rows.forEach(row => {
                    let text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchText) ? '' : 'none';
                });
            }, 300);
        });
    }
</script>

<?php
$content = ob_get_clean();
Page::render($content);
?>

<style>
/* Status Badges */
.status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 500;
    display: inline-block;
}

.status-active {
    background-color: rgba(76, 175, 80, 0.2);
    color: #2e7d32;
    border: 1px solid #2e7d32;
}

.status-inactive {
    background-color: rgba(158, 158, 158, 0.2);
    color: #616161;
    border: 1px solid #616161;
}

.status-deleted {
    background-color: rgba(244, 67, 54, 0.2);
    color: #d32f2f;
    border: 1px solid #d32f2f;
}

/* Action Buttons */
.action-btn {
    background: none;
    border: none;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    margin-right: 5px;
}

.view-btn {
    color: #4CAF50;
    background-color: rgba(76, 175, 80, 0.1);
}

.view-btn:hover {
    background-color: rgba(76, 175, 80, 0.2);
}

.delete-btn {
    color: #f44336;
    background-color: rgba(244, 67, 54, 0.1);
}

.delete-btn:hover {
    background-color: rgba(244, 67, 54, 0.2);
}

/* Customer Details Grid */
.customer-details-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin-bottom: 20px;
}

.detail-item {
    padding: 12px;
    background-color: #f5f5f5;
    border-radius: 6px;
}

.detail-item label {
    display: block;
    font-size: 0.85rem;
    color: #757575;
    margin-bottom: 5px;
}

.detail-item span {
    font-size: 1rem;
    font-weight: 500;
}

/* Detail Actions */
.detail-actions {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
    border-top: 1px solid #e0e0e0;
    padding-top: 20px;
}

.detail-actions button {
    padding: 8px 16px;
    border-radius: 4px;
    border: none;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-edit {
    background-color: #4CAF50;
    color: white;
}

.btn-edit:hover {
    background-color: #43a047;
}

.btn-delete {
    background-color: #f44336;
    color: white;
}

.btn-delete:hover {
    background-color: #e53935;
}

/* SweetAlert Form Styles */
.swal-form {
    text-align: left;
    margin: 20px 0;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    font-size: 0.9rem;
    color: #616161;
    margin-bottom: 5px;
    text-align: left;
}

.swal2-input {
    width: 100% !important;
    margin: 0 !important;
}

/* Responsive Design */
@media (max-width: 768px) {
    .customer-details-grid {
        grid-template-columns: 1fr;
    }
    
    .detail-actions {
        flex-direction: column;
        gap: 10px;
    }
    
    .detail-actions button {
        width: 100%;
    }
}
</style>
