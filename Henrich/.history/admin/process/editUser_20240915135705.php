<<<<<<<<<<<<<<  ✨ Codeium Command 🌟  >>>>>>>>>>>>>>>>
<?php
include '../reusable/header.php';
include '../database/dbconnect.php';
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>

$uid = $_POST['uid'] ?? '';
if (empty($uid)) {
    header("Location: ../admin/admin.php");
    exit;
}

$sql = "SELECT * FROM user WHERE uid = '$uid'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
    <?php include './reusable/header.php'; ?>
</head>
<body>
    <div>
        <?php
        include '../database/dbconnect.php';
        $uid = $_POST['uid'];
        $sql = "SELECT * FROM user WHERE uid = '$uid'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
        ?>
        <form action="./process/editUserProcess.php" method="post">
            <input type="hidden" name="uid" value="<?php echo $uid; ?>">
            <label for="email">Email:</label><br>
            <input type="text" name="email" value="<?php echo $row['email']; ?>"><br>
            <label for="role">Role:</label><br>
            <select name="role">
                <option value="admin" <?php if ($row['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                <option value="user" <?php if ($row['role'] == 'user') echo 'selected'; ?>>User</option>
            </select><br><br>
            <input type="submit" value="Save">
        </form>
        <?php
            }
        } else {
            echo "0 results";
        }
        $conn->close();
        ?>

    </div>
</body>
</html>

<<<<<<<  e94bb040-eb81-43cc-a1fb-b357889e35a6  >>>>>>>