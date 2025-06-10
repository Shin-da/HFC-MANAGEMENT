<!DOCTYPE html>
<html>
<head>
    <style>
        .image {
            display: block;
            animation: hideImage 1s forwards;
        }

        @keyframes hideImage {
            0% { opacity: 1; }
            100% { opacity: 0; }
        }
    </style>
</head>
<body>
    <img class="image" src="images/hfclogo.png" alt=" logo">
    <script>
        setTimeout(function() {
            document.querySelector('.image').style.display = 'none';
        }, 1000);
    </script>
</body>
</html>
