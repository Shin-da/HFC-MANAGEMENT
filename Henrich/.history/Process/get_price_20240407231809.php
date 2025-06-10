
<?php
include '../database/dbconnect.php';

$ProductCode = $_POST['ProductCode'];
$sql = "SELECT SRP FROM Products WHERE ProductCode = '$ProductCode'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  echo $row['SRP'];
} else {
  echo '0'; // Return a default value or an indicator that the price was not found
}

$conn->close();
?>
