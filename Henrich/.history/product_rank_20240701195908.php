<?php 
include "./database/dbconnect.php";

$sql = "SELECT  Product_Name, SUM(Quantity) AS total_bought FROM tblorders GROUP BY ProductCode ORDER BY total_bought DESC LIMIT 5";
$result = $conn->query($sql);

$rows = array();

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
<<<<<<<<<<<<<<  ✨ Codeium Command 🌟 >>>>>>>>>>>>>>>>
+        echo "<script>console.log(" . json_encode($row) . ")</script>";
         $rows[] = $row;
<<<<<<<  1c95d60c-1628-441a-8c0e-7b9a867d3621  >>>>>>>
    }

} 

echo json_encode($rows);

$conn->close();

?>

