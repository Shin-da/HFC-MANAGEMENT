<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Accounts</title>
    <?php require '../reusable/header.php'; ?>
    
    <style>
        .table-container {
            overflow-x: auto;
        }
        .table-striped {
            width: 100%;
            border-collapse: collapse;
        }
        .table-striped th, .table-striped td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .table-striped tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .table-striped tr:hover {
            background-color: #ddd;
        }
        .table-striped th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #4CAF50;
            color: white;
        }
    </style>
</head>

<body>
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
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM customeraccount";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td><img src='" . $row['profilepicture'] . "' alt='Profile Picture' width='50' height='50'></td>
                                    <td>" . $row['accountid'] . "</td>
                                    <td>" . $row['customername'] . "</td>
                                    <td>" . $row['customeraddress'] . "</td>
                                    <td>" . $row['customerphonenumber'] . "</td>
                                    <td>" . $row['customerid'] . "</td>
                                    <td>" . $row['username'] . "</td>
                                    <td>" . $row['password'] . "</td>
                                    <td>" . $row['useremail'] . "</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='9'>0 results</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <?php require '../reusable/footer.php'; ?>
</body>

</html>

