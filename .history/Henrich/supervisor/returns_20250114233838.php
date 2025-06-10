<?php
include_once '../database/dbconnect.php';
require '../reusable/redirect404.php';
require '../session/session.php';

if (isset($_POST['submit'])) {
    $order_id = $_POST['order_id'];
    $reason = $_POST['reason'];
    $message = $_POST['message'];
    $user_id = $_SESSION['user_id'];

    $query = "INSERT INTO returns (order_id, user_id, reason, message) VALUES ('$order_id', '$user_id', '$reason', '$message')";
    $result = mysqli_query($conn, $query);

    if ($result) {
        header('Location: returns.php?success=1');
        exit;
    } else {
        header('Location: returns.php?error=1');
        exit;
    }
}

if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success = 'Thank you for submitting your return request. We will contact you soon.';
} elseif (isset($_GET['error']) && $_GET['error'] == 1) {
    $error = 'Something went wrong. Please try again.';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Returns</title>
    <style>
        form {
            width: 300px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.3);
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
        }

        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .error {
            color: red;
        }

        .success {
            color: green;
        }
    </style>
</head>
<body>

<h2>Returns</h2>

<?php if (isset($success)) { ?>
    <div class="success"><?php echo $success; ?></div>
<?php } elseif (isset($error)) { ?>
    <div class="error"><?php echo $error; ?></div>
<?php } ?>

<form action="returns.php" method="post">
    <label>Order ID:</label>
    <input type="text" name="order_id" required><br><br>

    <label>Reason:</label>
    <select name="reason" required>
        <option value="">Select</option>
        <option value="Bad quality">Bad quality</option>
        <option value="Wrong item">Wrong item</option>
        <option value="Other">Other</option>
    </select><br><br>

    <label>Message:</label>
    <textarea name="message" required></textarea><br><br>

    <input type="submit" name="submit" value="Submit">
</form>

</body>
</html>

