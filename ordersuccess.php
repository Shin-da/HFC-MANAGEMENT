<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f9;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }
        .success-message {
            text-align: center;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            position: relative;
        }
        .success-message h1 {
            color: #4CAF50;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .success-message p {
            font-size: 16px;
            margin-bottom: 30px;
        }
        .progress {
            width: 100%;
            height: 4px;
            background-color: #ddd;
            border-radius: 10px;
            margin-bottom: 20px;
            position: absolute;
            top: 0;
            left: 0;
        }
        .progress-bar {
            width: 0%;
            height: 100%;
            background-color: #4CAF50;
            border-radius: 10px;
            transition: width 2s ease-in-out;
        }
        .loader {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: none;
        }
        .success-message a {
            color: #007BFF;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
            display: block;
            margin: 10px 0;
        }
        .success-message a:hover {
            color: #0056b3;
        }

        .spinner {
            --red: #d62d20;
            --blue: #0057e7;
            --green: #008744;
            --yellow: #ffa700;
            position: relative;
            width: 60px;
        }

        .spinner:before {
            content: "";
            display: block;
            padding-top: 100%;
        }

        .circular {
            animation: rotate73451 2s linear infinite;
            height: 100%;
            transform-origin: center center;
            width: 100%;
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            margin: auto;
        }

        .path {
            stroke-dasharray: 1, 200;
            stroke-dashoffset: 0;
            animation: dash0175 1.5s ease-in-out infinite, color7123 6s ease-in-out infinite;
            stroke-linecap: round;
        }

        @keyframes rotate73451 {
            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes dash0175 {
            0% {
                stroke-dasharray: 1, 200;
                stroke-dashoffset: 0;
            }

            50% {
                stroke-dasharray: 89, 200;
                stroke-dashoffset: -35px;
            }

            100% {
                stroke-dasharray: 89, 200;
                stroke-dashoffset: -124px;
            }
        }

        @keyframes color7123 {

            100%,
            0% {
                stroke: var(--red);
            }

            40% {
                stroke: var(--blue);
            }

            66% {
                stroke: var(--green);
            }

            80%,
            90% {
                stroke: var(--yellow);
            }
        }
    </style>

</head>

<body>
    <div class="success-message">
        <div class="loader">
            <div class="spinner">
                <svg viewBox="25 25 50 50" class="circular">
                    <circle stroke-miterlimit="10" stroke-width="3" fill="none" r="20" cy="50" cx="50" class="path"></circle>
                </svg>
            </div>
        </div>
        <div class="progress">
            <div class="progress-bar" id="progress-bar"></div>
        </div>
        <h1>Your order has been placed successfully!</h1>
        <p>Thank you for your purchase. Your order is being processed.</p>
        <a href="app.php">Go back to homepage</a>
        <a href="orderhistory.php">View order history</a>
    </div>
    <script>
        const progressBar = document.getElementById('progress-bar');
        const loader = document.querySelector('.loader');
        let progress = 0;
        const interval = setInterval(() => {
            progress += 2;
            progressBar.style.width = progress + '%';
            if (progress === 100) {
                clearInterval(interval);
                loader.style.display = 'block';
                setTimeout(() => {
                    window.location.href = 'orderhistory.php';
                }, 5000);
            }
        }, 50);
    </script>
</body>

</html>
