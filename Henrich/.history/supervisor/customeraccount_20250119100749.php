<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');

try {
    // Get total records and calculate pages first
    $count_query = "SELECT COUNT(*) as total FROM customeraccount WHERE accountstatus = 'Active'";
    $count_result = $conn->query($count_query);
    $total_records = $count_result->fetch_assoc()['total'];
    
    // Calculate total pages before the main query
    $limit = 10;
    $total_pages = ceil($total_records / $limit);
    
    // Get current page
    $page = isset($_GET['page']) ? max(1, min($_GET['page'], $total_pages)) : 1;
    $offset = ($page - 1) * $limit;

    // Get today's registered accounts
    $today_registered_query = "SELECT COUNT(*) as count FROM customeraccount WHERE DATE(created_at) = CURDATE()";
    $today_registered = $conn->query($today_registered_query)->fetch_assoc()['count'];

    // Main query for fetching accounts
    $sql = "SELECT * FROM customeraccount 
            WHERE accountstatus = 'Active' 
            ORDER BY accountid DESC 
            LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

} catch (mysqli_sql_exception $e) {
    error_log("Error in customeraccount.php: " . $e->getMessage());
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
    <?php include '../reusable/sidebar.php'; ?>
    <section class="dashboard panel">
        <?php include '../reusable/navbarNoSearch.html'; ?>
        
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
