<!-- this is for analytics, determining what is the most bought product -->

<?php 
session_start();
include "./database/dbconnect.php";

$sql = "SELECT * FROM tblorders";
$result = $conn->query($sql);


?>