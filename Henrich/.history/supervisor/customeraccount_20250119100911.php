<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');

try {
    // First check if the table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'customeraccount'");
    if ($table_check->num_rows == 0) {
        // Create the table if it doesn't exist
        $create_table = "CREATE TABLE IF NOT EXISTS customeraccount (
            accountid INT PRIMARY KEY AUTO_INCREMENT,
            customername VARCHAR(255) NOT NULL,
            username VARCHAR(100) UNIQUE NOT NULL,
            useremail VARCHAR(255) UNIQUE NOT NULL,
            customeraddress TEXT,
            customerphonenumber VARCHAR(20),
            accountstatus ENUM('Active', 'Inactive', 'Deleted') DEFAULT 'Active',
            accounttype VARCHAR(50) DEFAULT 'Customer',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            modified_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        $conn->query($create_table);
    }

    // Get total active accounts count
    $count_query = "SELECT COUNT(*) as total FROM customeraccount WHERE accountstatus = 'Active'";
    $count_result = $conn->query($count_query);
    if ($count_result === false) {
        throw new Exception("Error counting total records: " . $conn->error);
    }
    $total_records = $count_result->fetch_assoc()['total'];
    
    // Calculate pagination
    $limit = 10;
    $total_pages = max(1, ceil($total_records / $limit));
    $page = isset($_GET['page']) ? min(max(1, intval($_GET['page'])), $total_pages) : 1;
    $offset = ($page - 1) * $limit;

    // Get today's registrations
    $today_query = "SELECT COUNT(*) as count FROM customeraccount 
                   WHERE DATE(created_at) = CURDATE() AND accountstatus = 'Active'";
    $today_result = $conn->query($today_query);
    if ($today_result === false) {
        throw new Exception("Error counting today's registrations: " . $conn->error);
    }
    $today_registered = $today_result->fetch_assoc()['count'];

    // Main query for fetching accounts
    $sql = "SELECT accountid, customername, username, useremail, accountstatus, accounttype 
            FROM customeraccount 
            WHERE accountstatus = 'Active' 
            ORDER BY accountid DESC 
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

} catch (Exception $e) {
    error_log("Error in customeraccount.php: " . $e->getMessage());
    $error_message = $e->getMessage();
    $total_records = 0;
    $today_registered = 0;
    $total_pages = 1;
    $result = false;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Customer Accounts</title>
    <?php require '../reusable/header.php'; ?>
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
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo htmlspecialchars($row['useremail']); ?></td>
                                <td><?php echo htmlspecialchars($row['customername']); ?></td>
                                <td><?php echo htmlspecialchars($row['accountstatus']); ?></td>
                                <td>
                                    <button class="action-btn view-btn" onclick="viewAccount(<?php echo $row['accountid']; ?>)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="action-btn edit-btn" onclick="editAccount(<?php echo $row['accountid']; ?>)">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="action-btn delete-btn" onclick="deleteAccount(<?php echo $row['accountid']; ?>)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

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
        async function viewAccount(id) {
            try {
                const response = await fetch(`get_customer_account.php?id=${id}`);
                const data = await response.json();
                
                if (data.success) {
                    Swal.fire({
                        title: 'Account Details',
                        html: `
                            <div class="account-details">
                                <p><strong>Name:</strong> ${data.account.customername}</p>
                                <p><strong>Email:</strong> ${data.account.useremail}</p>
                                <p><strong>Username:</strong> ${data.account.username}</p>
                                <p><strong>Status:</strong> ${data.account.accountstatus}</p>
                                <p><strong>Account Type:</strong> ${data.account.accounttype}</p>
                            </div>
                        `,
                        confirmButtonColor: var(--primary)
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load account details'
                });
            }
        }

        // Edit account
        function editAccount(id) {
            window.location.href = `edit_customer_account.php?id=${id}`;
        }

        // Delete account
        function deleteAccount(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This action can't be undone",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: var(--danger-color),
                cancelButtonColor: var(--secondary),
                confirmButtonText: 'Yes, delete it!'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await fetch('delete_customer_account.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ id: id })
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            Swal.fire('Deleted!', 'Account has been deleted.', 'success')
                            .then(() => window.location.reload());
                        } else {
                            throw new Error(data.message);
                        }
                    } catch (error) {
                        Swal.fire('Error!', 'Failed to delete account.', 'error');
                    }
                }
            });
        }

        // Change page
        function changePage(pageNum) {
            window.location.href = `?page=${pageNum}`;
        }
    </script>
</body>
</html>
