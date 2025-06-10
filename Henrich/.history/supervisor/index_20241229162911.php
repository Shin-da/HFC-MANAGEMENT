<?php
require '../reusable/redirect404.php';
require '../session/session.php';
include "../database/dbconnect.php";
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<!DOCTYPE html>
<html>

<head>
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>HOME</title>
     <?php include '../reusable/header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="sidebar">
            <div class="logo">
                <img src="logo.png" alt="Logo">
            </div>
            <ul>
                <li><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="#"><i class="fas fa-user"></i> Profile</a></li>
                <li><a href="#"><i class="fas fa-envelope"></i> Messages</a></li>
                <li><a href="#"><i class="fas fa-book"></i> Orders</a></li>
                <li><a href="#"><i class="fas fa-cog"></i> Settings</a></li>
            </ul>
            <div class="logout">
                <a href="#"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
        <div class="main-content">
            <div class="header">
                <div class="search">
                    <input type="text" placeholder="Search...">
                    <button><i class="fas fa-search"></i></button>
                </div>
                <div class="user">
                    <img src="user.png" alt="User">
                    <p>John Doe</p>
                </div>
            </div>
            <div class="sales-graph">
                <div class="graph">
                    <canvas id="sales-graph-canvas"></canvas>
                </div>
                <div class="sales-info">
                    <p>Total Sales: <span>$10,000</span></p>
                    <p>Today's Sales: <span>$500</span></p>
                    <p>Yesterday's Sales: <span>$300</span></p>
                </div>
            </div>
            <div class="products">
                <div class="product">
                    <img src="product1.jpg" alt="Product 1">
                    <p>Product 1</p>
                    <p>$50</p>
                </div>
                <div class="product">
                    <img src="product2.jpg" alt="Product 2">
                    <p>Product 2</p>
                    <p>$30</p>
                </div>
                <div class="product">
                    <img src="product3.jpg" alt="Product 3">
                    <p>Product 3</p>
                    <p>$20</p>
                </div>
                <div class="product">
                    <img src="product4.jpg" alt="Product 4">
                    <p>Product 4</p>
                    <p>$15</p>
                </div>
            </div>
            <div class="customers">
                <div class="customer">
                    <img src="customer1.jpg" alt="Customer 1">
                    <p>John Doe</p>
                    <p>john.doe@example.com</p>
                </div>
                <div class="customer">
                    <img src="customer2.jpg" alt="Customer 2">
                    <p>Jane Doe</p>
                    <p>jane.doe@example.com</p>
                </div>
                <div class="customer">
                    <img src="customer3.jpg" alt="Customer 3">
                    <p>Bob Smith</p>
                    <p>bob.smith@example.com</p>
                </div>
                <div class="customer">
                    <img src="customer4.jpg" alt="Customer 4">
                    <p>Alice Johnson</p>
                    <p>alice.johnson@example.com</p>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('sales-graph-canvas').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Total Sales',
                    data: [10, 20, 30, 40, 50, 60],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>

