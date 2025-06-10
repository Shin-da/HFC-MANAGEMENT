<!-- this is for analytics, determining what is the most bought product -->

<?php 
session_start();
include "./database/dbconnect.php";

$sql = "SELECT * FROM tblorders";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo $row["ProductCode"];
    }
} else {
    echo "0 results";
}


?>