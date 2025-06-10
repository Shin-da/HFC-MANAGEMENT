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
    </style>
</head>

<body>
    <?php include '../reusable/sidebar.php'; ?>
    <section class="dashboard panel">
        <?php include '../reusable/navbarNoSearch.html'; ?>
        <div class="overview ">
             <div class="table-container">
                 <h1>Customer Information</h1>
            <table class="table">
                    <thead>
                        <tr>
                            <th>Customer Name</th>
                            <th>Customer Address</th>
                            <th>Customer Phone</th>
                            <th>Customer ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Reconnect to the database
                        require '../database/dbconnect.php';
                        
                        $sql3 = "SELECT * FROM customerdetails";
                        $result3 = $conn->query($sql3);

                        if ($result3->num_rows > 0) {
                            while ($row3 = $result3->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row3['customername'] . "</td>";
                                echo "<td>" . $row3['customeraddress'] . "</td>";
                                echo "<td>" . $row3['customerphonenumber'] . "</td>";
                                echo "<td>" . $row3['customerid'] . "</td>";
                                echo "</tr>";
                            }
                            error_log("customer.php: Customer data displayed successfully");
                        } else {
                            echo "0 results";
                            error_log("customer.php: No customer data found");
                        }

                        $conn->close();
                        error_log("customer.php: Database connection closed after fetching customer data");
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </section>
</body>
<?php include_once("../reusable/footer.php"); ?>

<?php
    // Check if the customer table needs to be updated
    $conn2 = new mysqli($hostname, $username, $password, $dbname, $port);
    if ($conn2->connect_error) {
        error_log("customer.php: Unable to connect to database: " . $conn2->connect_error);
        exit();
    }

    $sql = "SELECT COUNT(*) FROM cus o LEFT JOIN customer c ON o.customerid = c.customerid WHERE c.customerid IS NULL";
    $result = $conn2->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['COUNT(*)'] > 0) {
            error_log("customer.php: Found " . $row['COUNT(*)'] . " new customers to update");
            // Update the customer table
            $sql2 = "INSERT INTO customer (customername, customeraddress, customerphonenumber, customerid) SELECT o.customername, o.customeraddress, o.customerphonenumber, o.customerid FROM orders o LEFT JOIN customer c ON o.customerid = c.customerid WHERE c.customerid IS NULL";
            if ($conn2->query($sql2) === TRUE) {
                error_log("customer.php: Customer table updated successfully");
            } else {
                error_log("customer.php: Error updating customer table: " . $conn2->error);
            }
        } else {
            error_log("customer.php: No new customers to update");
        }
    } else {
        error_log("customer.php: Error querying orders: " . $conn2->error);
    }

    $conn2->close();
    error_log("customer.php: Database connection closed after updating customer data");
?>


