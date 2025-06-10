<?php
require
?>
    <!DOCTYPE html>
    <html>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- Favicon -->
        <link rel="icon" href="images/henrichlogo.png">

        <title>HOME</title>
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <link rel="stylesheet" type="text/css" href="css/dashboard.css">
        <link rel="stylesheet" type="text/css" href="css/sidebar.css">
        <link rel="stylesheet" type="text/css" href="css/calendar.css">

        <!-- Boxicons CDN Link -->
        <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>

        <!-- datetime -->
        <script src="js/datetime.js"></script>

        <!-- JS for search -->
        <script src="js/search.js"> </script>
        <script>
            function toggleDropdown() {
                document.getElementById("myDropdown").classList.toggle("show");
            }

            // Close the dropdown if the user clicks outside of it
            window.onclick = function(event) {
                if (!event.target.matches('.dropbtn')) {
                    var dropdowns = document.getElementsByClassName("dropdown-content");
                    var i;
                    for (i = 0; i < dropdowns.length; i++) {
                        var openDropdown = dropdowns[i];
                        if (openDropdown.classList.contains('show')) {
                            openDropdown.classList.remove('show');
                        }
                    }
                }
            }
        </script>

    </head>

    <body>
        <?php
        // Alert-messages
        // include 'alerts/alert-messages.php';

        // Modals
        // include 'modals/modals.php';

        // Sidebar 
        include 'sidebar.php';

        include 'database/dbconnect.php';

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $OrderID = $_GET['OrderID'];

        $sql = "SELECT * FROM orderdetails WHERE OrderID = '$OrderID'";

        $result = $conn->query($sql);
        ?>

        <!-- ===  === -->
        <section class=" panel">
            <?php
            // TOP NAVBAR
            include 'navbarNoSearch.html';
            ?>

            <div class="overview container ">
                <div class="title">
                    <a href="orders.php">
                        <i class='bx bx-arrow-back'></i>
                    </a>

                    <span class="text">Order # <a href="view_order.php?OrderID=<?php echo $OrderID; ?>"> <?php echo $OrderID; ?></a></span>
                </div>
            </div>

            <div class="panel-content">
                <div class="blank-content">

                    <!-- Order Details -->
                    <div class="order-details">

                        <div class="customer-info">
                            <div class="name">
                                <span class="text">Name:</span>
                                <span class="value">---</span>
                            </div>

                            <div class="address">
                                <span class="text">Address:</span>
                                <span class="value">---</span>
                            </div>
                        </div>

                        <div class="product-info">
                            <div class="product-code">
                                <span class="text">Product Code:</span>
                                <span class="value">---</span>
                            </div>

                            <div class="product-name">
                                <span class="text">Product Name:</span>
                                <span class="value">---</span>
                            </div>

                            <div class="price">
                                <span class="text">Price:</span>
                                <span class="value">---</span>
                            </div>
                        </div>

                        <div class="datetime">
                            <span class="text">Datetime:</span>
                            <span class="value">---</span>
                        </div>

                    </div>

                </div>
            </div>

        </section>

    </body>
    <script src="js/script.js"></script>

    </html>

<?php
} else {
    header("Location: index.php");
    exit();
}
?>