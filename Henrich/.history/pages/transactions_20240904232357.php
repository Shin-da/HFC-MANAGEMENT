<?php

require '../reusable/redirect404.php';
require '../session/session.php';
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

        ?>
        <!-- === Orders === -->
        <section class=" panel">
            <?php
               include '../reusable/navbar.html'; // TOP NAVBAR
            ?>


            <div class="content">
                <div class="container">
                    <div class="content-header">
                        <div class="title">
                            <i class='bx bx-tachometer'></i>
                            <span class="text">Transactions</span>
                        </div>
                    
                    </div>

                    <div class="table-data">
                        <div class="order">
                            <div class="head">
                                <h3>Order</h3>
                                <i class='bx bx-search'></i>
                                <i class='bx bx-filter-alt'></i>
                            </div>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer Name</th>
                                        <th>Product Name</th>
                                        <th>Quantity</th>
                                        
        </section>

    </body>
    <script src="../resources/js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    </html>
