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