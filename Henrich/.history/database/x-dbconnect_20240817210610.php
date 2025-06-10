<!-- xampp local -->
<?php
$hostname = "localhost";
$username = "root";
$password = "";
$dbname = "dbHenrichFoodCorps";
$port = 3308;
$conn = new mysqli($, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}    
?>