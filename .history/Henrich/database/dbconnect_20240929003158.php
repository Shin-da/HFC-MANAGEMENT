<!-- xampp local -->
<?php
$hostname = "localhost";
$username = "root";
$password = "";
$dbname = "dbhenrichfoodcorps";
$port = 3306;
$conn = new mysqli($hostname, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}    
?>