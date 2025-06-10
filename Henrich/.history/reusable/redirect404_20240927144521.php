<?php
// Check if the requested page exists
if (file_exists($_SERVER['REQUEST_URI'])) {
    // Redirect to the 404 error page
    header('Location: 404.html');
    exit;
}
?>


<!DOCTYPE html>
<html>

<head>
    <style>
        .imageloading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            height: 20vh;
            object-fit: contain;
            opacity: 1;
            transition: opacity 1s ease-in-out;
            z-index: 9999;
            pointer-events: none;
            user-select: none;
            display: block;
            animation: hideImage 1s forwards;
            animation-delay: 0s;
        }

        @keyframes hideImage {
            0% {
                opacity: 1;
            }

            100% {
                opacity: 0;
            }
        }
    </style>

</head>

<body>
    <div class="container imageloading" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);" >
        <img class="imageloading" src="../resources/images/henrichlogo.png" alt=" logo">
    </div>
</body>
<script>
    setTimeout(function() {
        document.querySelector('.imageloading').style.display = 'none';
    }, 1000);
</script>

</html>

