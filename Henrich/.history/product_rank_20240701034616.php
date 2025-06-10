<<<<<<<<<<<<<<  ✨ Codeium Command 🌟 >>>>>>>>>>>>>>>>
 <?php 
 include "./database/dbconnect.php";
 
 $sql = "SELECT ProductCode, SUM(Quantity) AS total_bought FROM tblorders GROUP BY ProductCode ORDER BY total_bought DESC LIMIT 5";
 $result = $conn->query($sql);
 
+$data = array();
+
 if ($result->num_rows > 0) {
     // output data of each row
-    echo "<ol>";
     while($row = $result->fetch_assoc()) {
+        $data[] = $row;
-        echo "<li>" . $row["ProductCode"] . " with " . 
-        $row["total_bought"] . " bought</li>";
     }
+} 
-    echo "</ol>";
-} else {
-    echo "0 results";
-}
 
+echo json_encode($data);
+
 ?>
 
<<<<<<<  04a275a2-9ab2-4b9e-9c24-f90c9f0de0d5  >>>>>>>