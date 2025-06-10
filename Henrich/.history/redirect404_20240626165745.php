<?php
// Check if the requested page exists
var_dump($_SERVER['REQUEST_URI']); // Add this line for debugging
if (file_exists($_SERVER['REQUEST_URI'])) {
    // Redirect to the 404 error page
    var_dump($_SERVER['REQUEST_URI']); // Add this line for debugging
    header('Location: /HenrichProto/404.html');
    exit;
}
?>


<!DOCTYPE html>
<html>

<head>
    <style>
        .imageloading {
            display: block;
            animation: hideImage 1s forwards;
            animation-delay: 1s;

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
    <div class="container">

        <img class="imageloading" src="images/hfclogo.png" alt=" logo">
        <script>
            setTimeout(function() {
                document.querySelector('.imageloading').style.display = 'none';
            }, 1000);
        </script>
    </div>
</body>

</html>