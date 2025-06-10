<?php 
include "./database/dbconnect.php";

$sql = "SELECT ProductCode, SUM(Quantity) AS total_bought FROM tblorders GROUP BY ProductCode ORDER BY total_bought DESC LIMIT 5";
$result = $conn->query($sql);

$data = array();

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
} 

echo json_encode($data);

?>

