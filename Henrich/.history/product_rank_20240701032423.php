<<<<<<<<<<<<<<  ✨ Codeium Command 🌟 >>>>>>>>>>>>>>>>
-<!-- this is for analytics, determining what is the most bought product -->
-
 <?php 
 session_start();
 include "./database/dbconnect.php";
 
+$sql = "SELECT ProductCode, SUM(Quantity) AS total_bought FROM tblorders GROUP BY ProductCode ORDER BY total_bought DESC LIMIT 5";
-$sql = "SELECT * FROM tblorders";
 $result = $conn->query($sql);
 
 if ($result->num_rows > 0) {
     // output data of each row
+    echo "<ol>";
     while($row = $result->fetch_assoc()) {
+        echo "<li>" . $row["ProductCode"] . " with " . $row["total_bought"] . " bought</li>";
-        echo $row["ProductCode"];
     }
+    echo "</ol>";
 } else {
     echo "0 results";
 }
 
+?>
-
-?>
<<<<<<<  f577dbf0-d40c-47d7-862c-b2341fce8bc3  >>>>>>>