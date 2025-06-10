<?php
require_once '../reusable/redirect404.php';
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once 'access_control.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');

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
?>
<!DOCTYPE html>
<html>
<head>
    <title>Customer Accounts</title>
    <?php require '../reusable/head.php'; ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        .dashboard-header {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .table-container {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            background: var(--primary);
            color: var(--light);
            padding: 12px;
            text-align: left;
        }

        .table td {
            padding: 12px;
            border-bottom: 1px solid var(--border);
        }

        .table tr:hover {
            background: var(--surface);
        }

        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .view-btn {
            background: var(--primary);
            color: var(--light);
        }

        .edit-btn {
            background: var(--warning);
            color: var(--dark);
        }

        .delete-btn {
            background: var(--danger-color);
            color: var(--light);
        }

        .search-box {
            padding: 10px;
            border: 1px solid var(--border);
            border-radius: 4px;
            width: 300px;
            margin-bottom: 20px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-top: 20px;
        }

        .pagination button {
            padding: 8px 12px;
            border: 1px solid var(--border);
            background: var(--card-bg);
            cursor: pointer;
            border-radius: 4px;
        }

        .pagination button.active {
            background: var(--primary);
            color: var(--light);
        }
    </style>
</head>
<body>
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
            Error: <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>
    <?php include '../reusable/sidebar.php'; ?>
    <?php include '../reusable/navbar.php'; ?>
    <section class="dashboard panel">

        
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
                                <th>Username</th>
                                <th>Email</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['customername']); ?></td>
                                    <td><?php echo htmlspecialchars($row['customeraddress']); ?></td>
                                    <td><?php echo htmlspecialchars($row['customerphonenumber']); ?></td>
                                    <td><?php echo htmlspecialchars($row['accountstatus']); ?></td>
                                    <td>
                                        <button class="action-btn view-btn" onclick="viewCustomer(<?php echo $row['customerid']; ?>)">
                                            <i class="bi bi-eye"></i>
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
    </section>

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
                                    <label>Total Orders:</label>
                                    <span>${data.orderCount}</span>
                                </div>
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
</body>
</html>
