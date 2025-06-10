/*************  ✨ Codeium Command 🌟  *************/
<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<!DOCTYPE html>
<html>

<head>
    <title>Customer Accounts</title>
    <?php require '../reusable/header.php'; ?>
    <link type="text/css" href="../resources/css/table.css" rel="stylesheet">
</head>

<body>
    <?php require '../reusable/sidebar.php'; ?>

    <section class="panel">
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
                            <th>Account ID</th>
                            <th>Customer Name</th>
                            <th>Customer Address</th>
                            <th>Customer Phone Number</th>
                            <th>Customer ID</th>
                            <th>Full Name</th>
                            <th>Username</th>
                            <th>Password</th>
                            <th>User Email</th>
                            <th>Profile Picture</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM customeraccount";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td>" . $row['accountid'] . "</td>
                                    <td>" . $row['customername'] . "</td>
                                    <td>" . $row['customeraddress'] . "</td>
                                    <td>" . $row['customerphonenumber'] . "</td>
                                    <td>" . $row['customerid'] . "</td>
                                    <td>" . $row['account_id'] . "</td>
                                    <td>" . $row['full_name'] . "</td>
                                    <td>" . $row['username'] . "</td>
                                    <td>" . $row['password'] . "</td>
                                    <td>" . $row['useremail'] . "</td>
                                    <td>" . $row['profilepicture'] . "</td>
                                    <td>" . $row['email'] . "</td>
                                    <td>" . $row['phone_number'] . "</td>
                                    <td>" . $row['address'] . "</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='9'>0 results</td></tr>";
                            echo "<tr><td colspan='6'>0 results</td></tr>";
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

/******  aff73921-539f-47d9-9e7e-12820b36ad73  *******/