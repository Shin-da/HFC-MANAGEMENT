<?php 
include "../database/dbconnect.php";

$sql = "SELECT  Product_Name, SUM(Quantity) AS total_bought FROM tblorders GROUP BY ProductCode ORDER BY total_bought DESC LIMIT 5";
$result = $conn->query($sql);

$rows = array();

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "<script>console.log(" . json_encode($row) . ")</script>";
        $rows[] = $row;
    }
} 

echo json_encode($rows);

$conn->close();

?>

