<?php 
include "./database/dbconnect.php";

$sql = "SELECT  Product_Name, SUM(Quantity) AS total_bought FROM tblorders GROUP BY ProductCode ORDER BY total_bought DESC LIMIT 5";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    echo "";
    while($row = $result->fetch_assoc()) {
        echo "<p>" . $row["Product_Name"] . " with " . 
        $row["total_bought"] . " bought<>";
    }
    echo "</ol>";
} else {
    echo "0 results";
}

?>
