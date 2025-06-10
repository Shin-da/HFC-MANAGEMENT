<<<<<<<<<<<<<<  ✨ Codeium Command 🌟 >>>>>>>>>>>>>>>>
 <?php 
 include "./database/dbconnect.php";
 
+$sql = "SELECT Month(Datetime) AS month, Year(Datetime) AS year, Product_Name, SUM(Quantity) AS total_bought FROM tblorders GROUP BY Product_Name, YEAR(Datetime), MONTH(Datetime) ORDER BY total_bought DESC LIMIT 5";
-$sql = "SELECT  Product_Name, SUM(Quantity) AS total_bought FROM tblorders GROUP BY ProductCode ORDER BY total_bought DESC LIMIT 5";
 $result = $conn->query($sql);
 
+$months = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
+
+echo "<div class='filter-section'>";
+echo "<div class='dropdown'>";
+echo "<button class='btn btn-secondary dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
+Filters
+</button>";
+echo "<div class='dropdown-menu' aria-labelledby='dropdownMenuButton' style='background-color: var(--sidebar-color);'>";
+
+foreach ($months as $month) {
+    echo "<a class='dropdown-item' href='?month=$month'>$month</a>";
+}
+
+echo "</div>";
+echo "</button>";
+echo "</div>";
+echo "</div>";
+
+if (isset($_GET['month'])) {
+    $month = $_GET['month'];
+    $sql = "SELECT Product_Name, SUM(Quantity) AS total_bought FROM tblorders WHERE MONTH(Datetime) = (SELECT MONTH(STR_TO_DATE('$month', '%M'))) GROUP BY Product_Name ORDER BY total_bought DESC LIMIT 5";
+    $result = $conn->query($sql);
+}
+
 if ($result->num_rows > 0) {
     // output data of each row
     while($row = $result->fetch_assoc()) {
         echo "<p>" . $row["Product_Name"] . " with " . 
         $row["total_bought"] . " bought </p>";
     }
 } else {
     echo "0 results";
 }
 
 ?>
 
<<<<<<<  9ac4e9eb-0c37-45ed-95a7-2cbc13355341  >>>>>>>