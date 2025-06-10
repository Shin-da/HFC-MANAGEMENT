//this is for analytics, determining what is the most bought product

<?php

include 'database/dbconnect.php';

$sql = "SELECT * FROM Orders";

$result = $conn->query($sql);
?>