<?php

require '../reusable/redirect404.php';
session_start();
if (isset($_SESSION['uid']) && isset($_SESSION['role'])) {
?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>HOME</title>
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <link rel="stylesheet" type="text/css" href="css/dashboard.css">
        <link rel="stylesheet" type="text/css" href="css/sidebar.css">
        <link rel="stylesheet" type="text/css" href="css/colors.css">
        <link rel="stylesheet" type="text/css" href="css/alerts.css">
        <link rel="stylesheet" type="text/css" href="css/table.css">

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
                

                <script>
                    var dateFilterButton = document.getElementById("date-filter");
                    var statusFilterButton = document.getElementById("status-filter");

                    function filterDate() {
                        if (dateFilterButton.style.display === "none") {
                            dateFilterButton.style.display = "block";
                            statusFilterButton.style.display = "none";
                        } else {
                            dateFilterButton.style.display = "none";
                        }
                    }

                    function filterStatus() {
                        if (statusFilterButton.style.display === "none") {
                            statusFilterButton.style.display = "block";
                            dateFilterButton.style.display = "none";
                        } else {
                            statusFilterButton.style.display = "none";
                        }
                    }

                    function dateFilter(date) {
                        // Perform date filter logic here
                        // For example, you can update the URL with the date parameter
                        // window.location.href = "orders.php?date=" + date;
                        alert("Date filter set to: " + date);
                        statusFilterButton.style.display = "none";
                    }

                    function statusFilter(status) {
                        // Perform status filter logic here
                        // For example, you can update the URL with the status parameter
                        // window.location.href = "orders.php?status=" + status;
                        alert("Status filter set to: " + status);
                        dateFilterButton.style.display = "none";
                    }
                </script>
                <!-- Orders table -->
                <div class=" table-container">

                    <div class="" id="nav-price" role="tabpanel" aria-labelledby="nav-price-tab">
                        <script>
                            $(document).ready(function() {
                                $("#myInput").on("keyup", function() {
                                    var value = $(this).val().toLowerCase();
                                    $("#myTable tbody tr").filter(function() {
                                        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                                    });
                                });
                            });
                        </script>

                        <table class="table " id="myTable">

                            <thead>
                                <tr>
                                    <th>OrderID</th>
                                    <th>Datetime</th>
                                    <th>Lname</th>
                                    <th>Fname</th>
                                    <th>ProductCode</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr class='clickable-row' onclick='window.location.href=\"view_order.php?OrderID=" . $row["OrderID"] . "\"'>
                                        <td>" . $row["OrderID"] . "</td>
                                        <td>" . $row["Datetime"] . "</td>
                                        <td>" . $row["Lname"] . "</td>
                                        <td>" . $row["Fname"] . "</td>
                                        <td>" . $row["ProductCode"] . "</td>
                                        <td>" . $row["Quantity"] . "</td>
                                        <td>₱" . number_format((float)$row["Price"], 2, '.', '') . "</td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7'>No orders found.</td></tr>";
                                }
                                $conn->close();
                                ?>
                            </tbody>
                        </table>
                    </div>

                </div>
                <!-- Orders table -->

                <!-- === Add Order === -->
                <style>
                    .add-order-tab {
                        position: fixed;
                        top: 50px;
                        right: 0;
                        width: 30vw;
                        height: 100%;
                        background-color: rgba(0, 0, 0, 0.1);
                        backdrop-filter: blur(10px);
                        z-index: 100;
                        overflow-y: auto;
                    }

                    .card {
                        width: 100%;
                        margin-bottom: 20px;
                        border-radius: 10px;
                        box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.15);
                        background-color: var(--panel-color);
                        z-index: 111;
                    }

                    .card-header {
                        padding: 10px;
                        border-top-left-radius: 10px;
                        border-top-right-radius: 10px;
                        background-color: var(--red);
                        color: var(--sidebar-color);
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                    }

                    .card-header .close {
                        padding: 0px 10px;
                        border: none;
                        background-color: var(--sidebar-color);
                        color: var(--accent-color);
                        margin-left: 10px;
                        cursor: pointer;
                    }

                    .card-header .close:focus {
                        outline: none;
                    }

                    .card-header .close:hover {
                        background-color: var(--accent-color);
                        color: var(--white);
                        /* border: 1px solid var(--sidebar-color); */
                    }

                    .card-header .close:hover {
                        color: var(--text-color);
                    }

                    .card-body {
                        padding: 20px;
                    }

                    .card-header h3 {
                        margin: 0;
                    }

                    .card-header .close {
                        font-size: 20px;
                        cursor: pointer;
                    }

                    .form-group {
                        display: flex;
                        flex-direction: column;
                        margin-bottom: 20px;
                    }
                </style>

                <div class="container add-order-tab" id="add-order">
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
                    <!-- === Add Order === -->
                    <script>
                        $(function() {
                            // $("#Datetime").datetimepicker();
                        });
                    </script>
                </div>
    </body>
    <script src="js/script.js"></script>
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