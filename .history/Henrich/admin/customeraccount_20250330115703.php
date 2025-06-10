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
                        try {
                            $stmt = $GLOBALS['pdo']->query("SELECT * FROM customeraccount");
                            $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if (count($customers) > 0) {
                                foreach ($customers as $row) {
                                    echo "<tr>
                                        <td><img src='" . htmlspecialchars($row['profilepicture']) . "' alt='Profile Picture' width='50' height='50'></td>
                                        <td>" . htmlspecialchars($row['accountid']) . "</td>
                                        <td>" . htmlspecialchars($row['customername']) . "</td>
                                        <td>" . htmlspecialchars($row['customeraddress']) . "</td>
                                        <td>" . htmlspecialchars($row['customerphonenumber']) . "</td>
                                        <td>" . htmlspecialchars($row['customerid']) . "</td>
                                        <td>" . htmlspecialchars($row['username']) . "</td>
                                        <td>" . htmlspecialchars($row['password']) . "</td>
                                        <td>" . htmlspecialchars($row['useremail']) . "</td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='9'>0 results</td></tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='9'>Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
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

