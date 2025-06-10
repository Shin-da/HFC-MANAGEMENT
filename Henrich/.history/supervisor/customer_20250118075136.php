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
    </style>
</head>

<body>
    <?php include '../reusable/sidebar.php'; ?>
    <section class="dashboard panel">
        <?php include '../reusable/navbarNoSearch.html'; ?>
        <div class="overview">
            <div class="filters-container">
                <input type="text" id="searchCustomer" class="search-box" placeholder="Search customers...">
                <select id="filterBy" class="search-box">
                    <option value="">Filter by...</option>
                    <option value="name">Name</option>
                    <option value="address">Address</option>
                    <option value="phone">Phone</option>
                </select>
                <button class="export-btn" onclick="exportToExcel()">Export to Excel</button>
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
                        