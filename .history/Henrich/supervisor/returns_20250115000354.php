/*************  ✨ Codeium Command 🌟  *************/
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

if (isset($_POST['search'])) {
    $search = $_POST['search'];
    $query = "SELECT hid, customername, orderdescription, ordertotal, status FROM customerorder WHERE hid LIKE '%$search%' OR customername LIKE '%$search%' OR orderdescription LIKE '%$search%' OR ordertotal LIKE '%$search%' OR status LIKE '%$search%'";
    $query = "SELECT hid, customername, orderdescription, ordertotal, status FROM customerorder WHERE hid LIKE '%$search%' OR customername LIKE '%$search%' OR orderdescription LIKE '%$search%'";
} else {
    $query = "SELECT hid, customername, orderdescription, ordertotal, status FROM customerorder";
}
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Returns</title>
    <script>
        function searchTable() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("search");
            filter = input.value.toUpperCase();
            table = document.getElementById("myTable");
            tr = table.getElementsByTagName("tr");
</head>
<body>

            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td");
                for (var j = 0; j < td.length; j++) {
                    txtValue = td[j].textContent || td[j].innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                        break;
                    } else {
                        tr[i].style.display = "none";
                    }
<h2>Returns</h2>

<?php if (isset($success)) { ?>
    <div style="color: green;"><?php echo $success; ?></div>
<?php } elseif (isset($error)) { ?>
    <div style="color: red;"><?php echo $error; ?></div>
<?php } ?>

<form action="returns.php" method="post">
    <label>Search Order:</label>
    <input type="text" name="search" value="<?php echo isset($search) ? $search : ''; ?>">
    <input type="submit" name="search" value="Search"><br><br>
</form>

<table>
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Customer Name</th>
            <th>Order Description</th>
            <th>Order Total</th>
            <th>Status</th>
            <th>Select</th>
        </tr>
    </thead>
    <tbody>
        <?php
 $query = "SELECT hid, customername, orderdescription, ordertotal, status FROM customerorder";
 $result = mysqli_query($conn, $query);            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<tr>' .
                        '<td>' . $row['hid'] . '</td>' .
                        '<td>' . $row['customername'] . '</td>' .
                        '<td>' . $row['orderdescription'] . '</td>' .
                        '<td>$' . $row['ordertotal'] . '</td>' .
                        '<td>' . $row['status'] . '</td>' .
                        '<td><input type="radio" name="order_id" value="' . $row['hid'] . '"></td>' .
                        '</tr>';
                }
            }
        ?>
    </tbody>
</table>

            if (tr.length == 0) {
<form action="returns.php" method="post">
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


/******  9aea1863-82e1-4142-b0b9-4ded789e2dbf  *******/