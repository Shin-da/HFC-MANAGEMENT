//this is for analytics, determining what is the most bought product

<?php

include 'database/dbconnect.php';

$sql = "SELECT * FROM Orders";

$sql .= " GROUP BY ProductCode";
    
$result = $conn->query($sql);
?>