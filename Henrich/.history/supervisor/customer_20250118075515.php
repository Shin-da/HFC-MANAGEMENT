<?php
require '../reusable/redirect404.php';

require '../session/session.php';
require '../database/dbconnect.php';

error_log("customer.php: Script started");

// Check if the customer table needs to be updated
$sql = "SELECT COUNT(*) FROM customerorder o LEFT JOIN customerdetails c ON o.customerid = c.customerid WHERE c.customerid IS NULL";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if ($row['COUNT(*)'] > 0) {
        error_log("customer.php: Found " . $row['COUNT(*)'] . " new customers to update");
        // Check if the customer already exists in the customer table
        $sql2 = "SELECT COUNT(*) FROM customerdetails WHERE customerid = ?";
        $stmt = $conn->prepare($sql2);
        $stmt->bind_param('i', $row3['customerid']);
        $stmt->execute();
        $result2 = $stmt->get_result();
        $stmt->close();
        if ($result2->num_rows > 0) {
            error_log("customer.php: Customer already exists in the customer table");
        } else {
            // Update the customer table
            $sql2 = "INSERT INTO customerdetails (customername, customeraddress, customerphonenumber, customerid) VALUES (?,?,?,?)";
            $stmt = $conn->prepare($sql2);
            $stmt->bind_param('sssi', $row3['customername'], $row3['customeraddress'], $row3['customerphonenumber'], $row3['customerid']);
            $stmt->execute();
            $stmt->close();
            if ($conn->affected_rows > 0) {
                error_log("customer.php: Customer table updated successfully");
            } else {
                error_log("customer.php: Error updating customer table: " . $conn->error);
            }
        }
    } else {
        error_log("customer.php: No new customers to update");
    }
} else {
    error_log("customer.php: Error querying orders: " . $conn->error);
}

// $conn->close();
// error_log("customer.php: Database connection closed");

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
        }

        function searchCustomers() {
            const input = document.getElementById('searchCustomer');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('customerTable');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName('td');
                let txtValue = '';
                for (let j = 0; j < td.length - 1; j++) {
                    txtValue += td[j].textContent || td[j].innerText;
                }
                tr[i].style.display = txtValue.toUpperCase().indexOf(filter) > -1 ? '' : 'none';
            }
        }

        function exportToExcel() {
            // Add export logic
        }

        function viewCustomer(customerId) {
            // Add customer details view logic
        }

        function changePage(page) {
            window.location.href = `?page=${page}`;
        }

        // Event listeners
        document.getElementById('searchCustomer').addEventListener('keyup', searchCustomers);
    </script>
</body>
<?php include_once("../reusable/footer.php"); ?>


