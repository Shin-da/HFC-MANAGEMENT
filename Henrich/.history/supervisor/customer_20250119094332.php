<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';

error_log("customer.php: Script started");

try {
    // Get total records count from customerorder
    $total_records_query = "SELECT COUNT(DISTINCT customername) as count FROM customerorder";
    $total_records = $conn->query($total_records_query)->fetch_assoc()['count'];

    // Get today's active customers count
    $today_active_query = "SELECT COUNT(DISTINCT customername) as count FROM customerorder WHERE DATE(orderdate) = CURDATE()";
    $today_active = $conn->query($today_active_query)->fetch_assoc()['count'];

    // Check if the customer table needs to be updated
    $sql = "SELECT COUNT(*) FROM orders o LEFT JOIN customer c ON o.customer_id = c.customer_id WHERE c.customer_id IS NULL";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['COUNT(*)'] > 0) {
            error_log("customer.php: Found " . $row['COUNT(*)'] . " new customers to update");
            // Check if the customer already exists
            $sql2 = "SELECT COUNT(*) FROM customer WHERE customer_id = ?";
            $stmt = $conn->prepare($sql2);
            $stmt->bind_param('i', $customer_id);
            $stmt->execute();
            $result2 = $stmt->get_result();
            $stmt->close();

            if ($result2->num_rows > 0) {
                error_log("customer.php: Customer already exists in the customer table");
            } else {
                // Update the customer table - update column names if different
                $sql2 = "INSERT INTO customer (customer_name, address, phone_number, customer_id) VALUES (?,?,?,?)";
                $stmt = $conn->prepare($sql2);
                $stmt->bind_param('sssi', $customer_name, $address, $phone_number, $customer_id);
                $stmt->execute();
                $stmt->close();
                
                if ($conn->affected_rows > 0) {
                    error_log("customer.php: Customer table updated successfully");
                } else {
                    error_log("customer.php: Error updating customer table: " . $conn->error);
                }
            }
        }
    }
} catch (mysqli_sql_exception $e) {
    error_log("Error in customer.php: " . $e->getMessage());
    $total_records = 0;
    $today_active = 0;
}

// Update the table display query
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$sql3 = "SELECT DISTINCT customername, customeraddress, customerphonenumber, 
         MIN(orderid) as first_order_id
         FROM customerorder 
         GROUP BY customername, customeraddress, customerphonenumber
         LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql3);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result3 = $stmt->get_result();

?>
<!DOCTYPE html>
<html>
<head>
    <title>CUSTOMER</title>
    <?php require '../reusable/header.php'; ?>
    <!-- Add Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <!-- Add Toast notifications -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .dashboard {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .overview {
            width: 100%;
            background-color: #f2f2f2;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .table-container {
            overflow-x: auto;
        }

        .table {
            border-collapse: collapse;
            width: 100%;
        }

        .table th, .table td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        .table th {
            background-color: #4CAF50;
            color: white;
        }

        .table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .table tr:hover {
            background-color: #ddd;
        }

        @media only screen and (max-width: 600px) {
            .table {
                width: 100%;
            }

            .table thead {
                display: none;
            }

            .table tr {
                display: block;
                border-bottom: 1px solid #ddd;
            }

            .table td {
                display: block;
                text-align: right;
                border-bottom: 1px solid #ddd;
            }

            .table td:before {
                content: attr(data-label);
                float: left;
                font-weight: bold;
            }
        }

        .filters-container {
            margin: 20px 0;
            padding: 15px;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12);
        }

        .search-box {
            padding: 8px;
            margin-right: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 250px;
        }

        .export-btn {
            background-color: #4CAF50;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .pagination {
            margin: 20px 0;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .pagination button {
            padding: 5px 10px;
            border: 1px solid #ddd;
            background: #fff;
            cursor: pointer;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
        }

        .modal-content {
            background: #fff;
            margin: 15% auto;
            padding: 20px;
            width: 70%;
            border-radius: 5px;
        }

        .dashboard-header {
            background: #fff;
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
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .search-box {
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            width: 300px;
            transition: all 0.3s;
            background: white url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="gray" class="bi bi-search" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>') no-repeat 95% center;
            padding-right: 40px;
        }

        .search-box:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.2);
            outline: none;
        }

        .action-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 500;
        }

        .view-btn {
            background: #4CAF50;
            color: white;
        }

        .view-btn:hover {
            background: #45a049;
            transform: translateY(-2px);
        }

        .pagination {
            margin: 20px 0;
            display: flex;
            justify-content: center;
            gap: 5px;
        }

        .pagination button {
            padding: 8px 12px;
            border: 1px solid #ddd;
            background: white;
            cursor: pointer;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .pagination button.active {
            background: #4CAF50;
            color: white;
            border-color: #4CAF50;
        }

        .pagination button:hover {
            background: #f5f5f5;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
            z-index: 1000;
        }

        .modal-content {
            background: white;
            margin: 10% auto;
            padding: 25px;
            width: 50%;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            position: relative;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from { transform: translateY(-100px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .close {
            position: absolute;
            right: 15px;
            top: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #666;
            transition: color 0.2s;
        }

        .close:hover {
            color: #333;
        }

        .loading-spinner {
            display: none;
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #4CAF50;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Add these new styles */
        .customer-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .detail-item {
            background: var(--card-bg);
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .detail-item label {
            display: block;
            color: var(--text-secondary);
            margin-bottom: 5px;
            font-size: 0.9em;
        }

        .detail-item span {
            font-size: 1.1em;
            color: var(--text-primary);
            font-weight: 500;
        }

        .error {
            color: var(--danger-color);
            padding: 10px;
            border-radius: 4px;
            background-color: rgba(255,0,0,0.1);
            margin: 10px 0;
        }

        .sync-btn {
            background-color: var(--primary);
            margin-left: 10px;
        }

        .sync-btn:hover {
            background-color: var(--secondary);
        }
    </style>
</head>
<body>
    <?php include '../reusable/sidebar.php'; ?>
    <section class="dashboard panel">
        <?php include '../reusable/navbarNoSearch.html'; ?>
        
        <div class="overview">
            <div class="dashboard-header">
                <h1>Customer Management</h1>
                <div class="stats-container">
                    <div class="stat-card">
                        <h3>Total Customers</h3>
                        <p class="stat-number"><?php echo $total_records; ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Active Today</h3>
                        <p class="stat-number">
                            <?php 
                                echo $today_active;
                            ?>
                        </p>
                    </div>
                </div>
                
                <div class="filters-container">
                    <input type="text" id="searchCustomer" class="search-box" placeholder="Search customers...">
                    <div class="action-buttons">
                        <button class="action-btn export-btn" onclick="exportToExcel()">
                            <i class="bi bi-download"></i> Export
                        </button>
                        <button class="action-btn sync-btn" onclick="syncCustomers()">
                            <i class="bi bi-arrow-repeat"></i> Sync Customers
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-container">
                <h1>Customer Information</h1>
                <table class="table" id="customerTable">
                    <thead>
                        <tr>
                            <th onclick="sortTable(0)">Customer Name ↕</th>
                            <th onclick="sortTable(1)">Customer Address ↕</th>
                            <th onclick="sortTable(2)">Customer Phone ↕</th>
                            <th>Customer ID</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        require '../database/dbconnect.php';
                        
                        $page = isset($_GET['page']) ? $_GET['page'] : 1;
                        $limit = 10;
                        $offset = ($page - 1) * $limit;
                        
                        $sql3 = "SELECT * FROM customerdetails LIMIT ? OFFSET ?";
                        $stmt = $conn->prepare($sql3);
                        $stmt->bind_param("ii", $limit, $offset);
                        $stmt->execute();
                        $result3 = $stmt->get_result();

                        if ($result3->num_rows > 0) {
                            while ($row3 = $result3->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row3['customername']) . "</td>";
                                echo "<td>" . htmlspecialchars($row3['customeraddress']) . "</td>";
                                echo "<td>" . htmlspecialchars($row3['customerphonenumber']) . "</td>";
                                echo "<td>" . htmlspecialchars($row3['customerid']) . "</td>";
                                echo "<td><button onclick='viewCustomer(" . $row3['customerid'] . ")'>View</button></td>";
                                echo "</tr>";
                            }
                        }
                        // Get total records for pagination
                        $total_pages = ceil($total_records / $limit);
                    ?>
                    </tbody>
                </table>
                <div class="pagination">
                    <?php
                    for ($i = 1; $i <= $total_pages; $i++) {
                        echo "<button onclick='changePage($i)'>$i</button>";
                    }
                    ?>
                </div>
            </div>
            Toastify({
                text: message,
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: type === 'success' ? "#4CAF50" : "#f44336"
            }).showToast();
        }

        function showLoading(show = true) {
            document.getElementById('loading-spinner').style.display = show ? 'block' : 'none';
        }

        // Enhanced search with debouncing
        let searchTimeout;
        document.getElementById('searchCustomer').addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchCustomers();
            }, 300);
        });

        // Enhanced view customer function
        async function viewCustomer(customerId) {
            const modal = document.getElementById('customerModal');
            const detailsContainer = document.getElementById('customerDetails');
            const loadingSpinner = document.getElementById('loading-spinner');
            
            try {
                loadingSpinner.style.display = 'block';
                modal.style.display = 'block';
                
                const response = await fetch(`get_customer_details.php?id=${customerId}`);
                const data = await response.json();
                
                if (data.success) {
                    detailsContainer.innerHTML = `
                        <div class="customer-details-grid">
                            <div class="detail-item">
                                <label>Name:</label>
                                <span>${data.customer.customername}</span>
                            </div>
                            <div class="detail-item">
                                <label>Address:</label>
                                <span>${data.customer.customeraddress}</span>
                            </div>
                            <div class="detail-item">
                                <label>Phone:</label>
                                <span>${data.customer.customerphonenumber}</span>
                            </div>
                            <div class="detail-item">
                                <label>Total Orders:</label>
                                <span>${data.orderCount}</span>
                            </div>
                        </div>
                    `;
                } else {
                    detailsContainer.innerHTML = '<p class="error">Failed to load customer details</p>';
                }
            } catch (error) {
                detailsContainer.innerHTML = '<p class="error">An error occurred while loading customer details</p>';
            } finally {
                loadingSpinner.style.display = 'none';
            }
        }

        function searchCustomers() {
            const input = document.getElementById('searchCustomer');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('customerTable');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName('td');
                let textValue = '';
                for (let j = 0; j < td.length - 1; j++) {
                    textValue += td[j].textContent || td[j].innerText;
                }
                tr[i].style.display = textValue.toUpperCase().indexOf(filter) > -1 ? '' : 'none';
            }
        }

        function exportToExcel() {
            const table = document.getElementById('customerTable');
            const ws = XLSX.utils.table_to_sheet(table);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Customers');
            XLSX.writeFile(wb, 'customers.xlsx');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == document.getElementById('customerModal')) {
                document.getElementById('customerModal').style.display = "none";
            }
        }

        // Close modal when clicking X
        document.querySelector('.close').onclick = function() {
            document.getElementById('customerModal').style.display = "none";
        }
    </script>
</body>
<?php include_once("../reusable/footer.php"); ?>


