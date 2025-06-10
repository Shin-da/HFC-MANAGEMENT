<?php
require_once 'access_control.php';
require_once '../includes/Page.php';
require_once '../includes/functions.php';

// Initialize page
Page::setTitle('Customer Accounts - HFC Admin');
Page::setBodyClass('admin-page');
Page::setCurrentPage('customeraccount');

// Add required styles
Page::addStyle('../assets/css/admin.css');
Page::addStyle('../assets/css/table.css');
Page::addStyle('../assets/css/customer.css');

ob_start();
?>

<div class="container-fluid">
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
</div>

<?php
$content = ob_get_clean();
Page::render($content);
?>

