<!-- xampp local -->
<?php
$hostname = "localhost";
$username = "root";
$password = "";
$dbname = "henrich";
$port = 3306;
$conn = new mysqli($hostname, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}    
?>