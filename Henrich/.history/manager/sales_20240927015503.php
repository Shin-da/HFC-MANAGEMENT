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
                <?php include 'react.js'; ?>
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