<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>

    <?php include './reusable/header.php'; ?>
</head>
<body>
    <div>
        <?php
        include '../database/dbconnect.php';
        $uid = $_POST['uid'];
        $email = $_POST['email'];
        $role = $_POST['role'];
        $sql = "UPDATE users SET email = '$email', role = '$role' WHERE id = '$id'";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            header("Location: ../admin/admin.php");
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }        
        ?>
    </div>
</body>
</html>