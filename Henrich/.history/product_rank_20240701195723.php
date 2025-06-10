<<<<<<<<<<<<<<  ✨ Codeium Command 🌟 >>>>>>>>>>>>>>>>
 <?php 
 include "./database/dbconnect.php";
 
 $sql = "SELECT  Product_Name, SUM(Quantity) AS total_bought FROM tblorders GROUP BY ProductCode ORDER BY total_bought DESC LIMIT 5";
 $result = $conn->query($sql);
 
+$rows = array();
+
 if ($result->num_rows > 0) {
     // output data of each row
     while($row = $result->fetch_assoc()) {
+        $rows[] = $row;
-        echo "<p>" . $row["Product_Name"] . " with " . 
-        $row["total_bought"] . " bought </p>";
     }
+} 
-} else {
-    echo "0 results";
-}
 
+echo json_encode($rows);
+
 ?>
 
<<<<<<<  5e17ec3c-5cee-4ca5-8cde-e4c7b4988cf7  >>>>>>>