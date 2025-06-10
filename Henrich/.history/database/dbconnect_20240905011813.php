<!-- xampp local -->
<?php
$hostname = "localhost";
$username = "root";
$password = "";
$dbname = "dbHenrichFoodCorps";
$port = 3306;
$conn = new mysqli($hostname, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}    
?>              









<!-- mariadb -->
<?php
$servername = "localhost";
$username = "root";
$password = 12345678;
$dbname = "dbHenrichFoodCorps";
$port = 3307;
$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>









