<?php

require '../reusable/redirect404.php';
session_start();
if (isset($_SESSION['uid']) && isset($_SESSION['role'])) {
?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>HOME</title>
        <link rel="stylesheet" type="text/css" href="../resources/css/style.css">
        <link rel="stylesheet" type="text/css" href="../resources/css/dashboard.css">
        <link rel="stylesheet" type="text/css" href="../resources/css/sidebar.css">
        <link rel="stylesheet" type="text/css" href="../resources/css/colors.css">
        <link rel="stylesheet" type="text/css" href="../resources/css/alerts.css">
        <link rel="stylesheet" type="text/css" href="../resources/css/table.css">

        <!-- Boxicons CDN Link -->
        <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>


        <!-- For Realtime Search  -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <style>
            /* CSS to style the buttons */
            .neworder a {
                color: white;
                border: none;
                border-radius: 4px;
                text-decoration: none;
                margin-right: 10px;

                cursor: pointer;
            }

            .buttonn {
                background-color: var(--success-color);
                color: white;
                border: none;
                padding: 5px;
                border-radius: 4px;
                text-align: center;
                text-decoration: none;
                display: inline-block;
                font-size: 26px;
                cursor: pointer;
            }

            .buttonn:hover {
                background-color: var(--border-color);
                color: var(--blue);
                scale: 1.1;
                transition: 0.2s ease-in-out;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }
        </style>

    </head>


    <body>
        <?php
        // Alert-messages
        // include 'alerts/alert-messages.php';

        // Modals
        // include 'modals/modals.php';

        // Sidebar 
        include '../reusable/sidebar.php';

        // The user is logged in; continue with the page contents
        require '../database/dbconnect.php';

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM tblorders";
        $result = $conn->query($sql);

        ?>
        <!-- === Orders === -->
        <section class=" panel">
            <?php
            // TOP NAVBAR
            include '../reusable/navbar.html';
            include '../reusable/ordernav.html';
            ?>

            <div class="container ordernav" style="background-color:var(--sidebar-color) ;">
                <div class="container add-order-button">
                    <div class="neworder">
                        <div href="" style="text-decoration: none;">
                            <div style="display: flex; 
                                        background-color: var(--toggle-color); 
                                        align-items: center;
                                        border-radius: 4px;
                                        border: 1px solid var(--text-color);
                                        margin-right: 10px;">

                                <div style="font-size: 20px; 
                                            color: var(--blue);
                                            margin-right: 10px; 
                                            margin-left: 10px;">
                                    NEW
                                </div>
                                <button class="buttonn">
                                    <i class='bx bx-plus'></i>
                                </button>

                            </div>
                        </div>
                    </div>
                    <span class="title text">Order</span>
                    <div class="container filter-section">
                    <!-- date, status filter -->
                    <div class="dropdown">
                        <button class="dropbtn" onclick="filterDate()">Date <i class='bx bx-chevron-down'> </i></button>
                        <div class="dropdown-content" id="date-filter">
                            <a href="#" onclick="dateFilter('Today')">Today</a>
                            <a href="#" onclick="dateFilter('This Week')">This Week</a>
                            <a href="#" onclick="dateFilter('This Month')">This Month</a>
                            <a href="#" onclick="dateFilter('This Year')">This Year</a>
                        </div>
                    </div>
                    <div class="dropdown">
                        <button class="dropbtn" onclick="filterStatus()">Status <i class='bx bx-chevron-down'> </i></button>
                        <div class="dropdown-content" id="status-filter">
                            <a href="#" onclick="statusFilter('Pending')">Pending</a>
                            <a href="#" onclick="statusFilter('Processing')">Processing</a>
                            <a href="#" onclick="statusFilter('Completed')">Completed</a>
                        </div>
                    </div>
                </div>
                </div>
            </div>

            <style>
                .add-order-button {
                    display: flex;
                    align-items: center;
                }

                .neworder {
                    display: flex;
                    align-items: center;
                    justify-content: end;
                }

                .neworder:hover {
                    scale: 1.01;
                    transition: 0.2s ease-in-out;
                    cursor: pointer;

                    button {
                        scale: 1.1;
                        transition: 0.2s ease-in-out;
                        background-color: var(--success-color);
                        color: var(--blue);
                    }
                }

                .neworder {
                    height: 30px;
                }
            </style>
            <div class="container ">
                <!-- Filter section -->
                <style>
                    .filter-section {
                        display: flex;
                        align-items: center;
                    }

                    .filter-section .dropdown {
                        position: relative;
                        display: inline-block;
                    }

                    .filter-section .dropdown button {

                        background-color: var(--sidebar-color);
                        margin-right: 10px;
                        border: none;
                        color: var(--text-color);
                        padding: 10px;
                        font-size: 16px;
                        cursor: pointer;
                    }

                    .filter-section .dropdown .dropdown-content {
                        display: none;
                        position: absolute;
                        background-color: var(--sidebar-color);
                        min-width: 160px;
                        z-index: 1;
                        margin-left: 10px;
                    }

                </style>
                

                <!-- <div class="container add-order-tab" id="add-order">
                    <div class="card">
                        <div class="card-header">
                            <button type="button" class="close" aria-label="Close" onclick="$('#add-order').hide();">
                                <span aria-hidden="true">&times;</span>
                            </button>

                            <h3 class="card-title">Add Order</h3>

                            <button type="button" style="opacity: 0;">
                                <span aria-hidden="true">&times;</span>
                            </button>

                        </div>

                        <div class="card-body">
                            <form action="addorder.php" method="POST">
                                <div class="form-group">
                                    <label for="OrderID">Order ID:</label>
                                    <input type="text" class="form-control" id="OrderID" name="OrderID" required>
                                </div>

                                <div class="form-group">
                                    <label for="Datetime">Datetime:</label>
                                    <input type="datetime-local" class="form-control" id="Datetime" name="Datetime" required value="<?php echo date('Y-m-d\TH:i:s', time()); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="Lname">Last Name:</label>
                                    <input type="text" class="form-control" id="Lname" name="Lname" required>
                                </div>

                                <div class="form-group">
                                    <label for="Fname">First Name:</label>
                                    <input type="text" class="form-control" id="Fname" name="Fname" required>
                                </div>

                                <div class="form-group">
                                    <label for="ProductCode">Product Code:</label>
                                    <input type="text" class="form-control" id="ProductCode" name="ProductCode" required>
                                </div>

                                <div class="form-group">
                                    <label for="Quantity">Quantity:</label>
                                    <input type="number" class="form-control" id="Quantity" name="Quantity" required>
                                </div>

                                <div class="form-group">
                                    <label for="Price">Price:</label>
                                    <input type="number" class="form-control" id="Price" name="Price" required>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="button">Add Order</button>
                                    <button type="reset" class="button">Reset</button>
                                </div>
                            </form>
                        </div>

                    </div>
                    === Add Order ===
                    <script>
                        $(function() {
                            $("#Datetime").datetimepicker();
                        });
                    </script>
                </div> -->

    </body>
    <script src="../resources/js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    </html>
<?php
} else if (!isset($_SESSION['uid']) || !isset($_SESSION['role'])) {
    // The user is not logged in; redirect to the login page
    header("Location: ../login/login.php");
    exit();
} else {
    // Unknown error; redirect to 404 page
    header("Location: ../reusable/404.php");
    exit();
}
?>