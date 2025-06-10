<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Device Friendly Alerts</title>
    <link rel="stylesheet" href="https://unpkg.com/sweetalert/dist/sweetalert.css">
    
    <style>
        /* .swal-overlay {
            background-color: rgba(0, 0, 0, 0.4);
        }

        .swal-button.swal-button--confirm {
            background-color: #4CAF50;
        }

        .swal-button.swal-button--confirm:hover {
            background-color: #45a049;
        }

        .swal-modal {
            width: 90%;
            max-width: 500px;
        }

        @media only screen and (min-width: 768px) {
            .swal-modal {
                width: 40%;
            }
        } */
    </style>
</head>

<body>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        function alertMe(title, text, type) {
            swal({
                title: title,
                text: text,
                icon: type,
                buttons: false,
                timer: 3000
            });
        }
    </script>
</body>

</html>
