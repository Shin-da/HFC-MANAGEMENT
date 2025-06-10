<?php

require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
?>
<!DOCTYPE html>
<html>

<head>
    <title>HOME</title>
    <?php require '../reusable/header.php'; ?>

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


        <div class="container">
            <div class="panel-content">
                <div class="content-header">
                    <div class="title ">
                        <i class='bx bx-tachometer'></i>
                        <span class="text">Sales</span>
                    </div>
                </div>

                <div class="container" style="background-color: white; padding: 20px; border-radius: 5px; border: 1px solid var(--border-color);">
                    <!-- dito ka maglagay mike -->
                <?php include 'pages'; ?>
                </div>

            </div>

        </div>

    </section>

</body>
<script src="../resources/js/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    import {
        useState,
        useEffect
    } from 'react'

    export default function Component() {
        const [salesData, setSalesData] = useState(initialSalesData)
        const [productPerformance, setProductPerformance] = useState(initialProductPerformance)
        const [prescriptiveActions, setPrescriptiveActions] = useState(initialPrescriptiveActions)

        useEffect(() => {
            // Simulating data fetch/update every 5 seconds
            const intervalId = setInterval(() => {
                // In a real application, you would fetch new data here
                // For this example, we'll just modify the existing data
                setSalesData(currentData =>
                    currentData.map(item => ({
                        ...item,
                        revenue: item.revenue * (1 + Math.random() * 0.1)
                    }))
                )
                setProductPerformance(currentData =>
                    currentData.map(item => ({
                        ...item,
                        value: item.value * (1 + Math.random() * 0.1)
                    }))
                )
                setPrescriptiveActions(currentData =>
                    currentData.map(item => ({
                        ...item,
                        impact: item.impact * (1 + Math.random() * 0.1)
                    }))
                )
            }, 5000)

            return () => clearInterval(intervalId) // Cleanup on component unmount
        }, [])

        // Rest of the component code using salesData, productPerformance, and prescriptiveActions...
    }
</script>

</html>