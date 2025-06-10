<?php require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php'; ?>

<!DOCTYPE html>
<html>

<head>
  <title>Stock Management</title>
  <?php require '../reusable/header.php'; ?>
  <link rel="stylesheet" type="text/css" href="../resources/css/table.css">
</head>

<body>
  <?php include '../reusable/sidebar.php';  // Sidebar  
  ?>



  <section class=" panel"><!-- === Inventory === -->
    <?php include '../reusable/navbarNoSearch.html'; // TOP NAVBAR 
    ?>
    <div class="container-fluid"> <!-- === graph showing stock levels === -->
      <?php include 'stocklevel.chart.php'; // Chart     
      ?>
    </div>

    <?php // pagination for stock management table
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $limit = 10;
    $start = ($page - 1) * $limit;
    $items = $conn->query("SELECT * FROM inventory  LIMIT $start, $limit");
    $totalRecords = $conn->query("SELECT COUNT(*) FROM inventory")->fetch_row()[0];
    $totalPages = ceil($totalRecords / $limit);
    ?>
    <!-- Stock Management -->
    <div class="container-fluid">
      <div class="table-header">
        <div class="title">
          <h2>INVENTORY</h2>
          <h3>Stock Management (display only)</h3>
        </div>

        <div class="search-box">
          <i class='bx bx-search-alt-2' style="font-size: 24px"></i>
          <input type="text" id="myInput" onkeyup="searchTable()" placeholder="Search...">
        </div>

        <div class="filter-box">
      <table>
        <tr>
          <th>IID</th>
          <th>Product Code</th>
          <th>Product Description</th>
          <th>Category</th>
          <th>On Hand</th>
          <th>Date Updated</th>
        </tr>
        <tr>
          <td><input type="text" id="iid-filter" onkeyup="filterTable()"></td>
          <td><input type="text" id="productcode-filter" onkeyup="filterTable()"></td>
          <td><input type="text" id="productdescription-filter" onkeyup="filterTable()"></td>
          <td><input type="text" id="category-filter" onkeyup="filterTable()"></td>
          <td><input type="text" id="onhand-filter" onkeyup="filterTable()"></td>
          <td><input type="text" id="dateupdated-filter" onkeyup="filterTable()"></td>
        </tr>
      </table>
    </div>
      </div>


      <?php
  $stockManagementTableSQL = "SELECT * FROM inventory LIMIT 10"; // We only want to display 10 items for now
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
  $sql = $stockManagementTableSQL;
  $result = $conn->query($sql);
  ?>
      <div class="container-fluid">
        <!-- Inventory Tab -->
        <table>
    <thead>
      <tr>
        <th>IID</th>
        <th>Product Code</th>
        <th>Product Description</th>
        <th>Category</th>
        <th>On Hand</th>
        <th>Date Updated</th>
      </tr>
    </thead>
    <tbody id="table-body">
      <?php
      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          $iid = $row['iid'];

          $productcode = $row['productcode'];
          $productdescription = $row['productdescription'];
          $category = $row['category'];
          $onhand = $row['onhand'];
          $dateupdated = $row['dateupdated'];
      ?>
          <tr>
            <td><?= $iid ?></td>
            <td><?= $productcode ?></td>
            <td><?= $productdescription ?></td>
            <td><?= $category ?></td>
            <td><?= $onhand ?></td>
            <td><?= $dateupdated ?></td>
          </tr>
      <?php
        }
      } else {
        echo "<tr><td colspan='8'>0 results</td></tr>";
      }
      ?>
    </tbody>
  </table>
        <div class="container">
          <ul class="pagination">
            <li><a href="?page=<?= $page - 1 <= 1 ? 1 : $page - 1 ?>" class="prev">&laquo;</a></li>
            <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
              <li><a href="?page=<?= $i ?>" class="page <?= $page == $i ? 'active' : '' ?>"><?= $i ?></a></li>
            <?php } ?>
            <li><a href="?page=<?= $page + 1 > $totalPages ? $totalPages : $page + 1 ?>" class="next">&raquo;</a></li>
          </ul>
        </div>
      </div>
    </div>

  </section>

</body>

<script>
 function filterTable() {
   var iidFilter = document.getElementById("iid-filter").value;
   var productcodeFilter = document.getElementById("productcode-filter").value;
   var productdescriptionFilter = document.getElementById("productdescription-filter").value;
   var categoryFilter = document.getElementById("category-filter").value;
   var onhandFilter = document.getElementById("onhand-filter").value;
   var dateupdatedFilter = document.getElementById("dateupdated-filter").value;
 
   var tableBody = document.getElementById("table-body");
   var rows = tableBody.getElementsByTagName("tr");
 
   for (var i = 0; i < rows.length; i++) {
     var row = rows[i];
     var cells = row.getElementsByTagName("td");
 
     var iidCell = cells[0].textContent;
     var productcodeCell = cells[1].textContent;
     var productdescriptionCell = cells[2].textContent;
     var categoryCell = cells[3].textContent;
     var onhandCell = cells[4].textContent;
     var dateupdatedCell = cells[5].textContent;
 
     var match = true;
 
     if (iidFilter !== "" && !iidCell.includes(iidFilter)) {
       match = false;
     }
     if (productcodeFilter !== "" && !productcodeCell.includes(productcodeFilter)) {
       match = false;
     }
     if (productdescriptionFilter !== "" && !productdescriptionCell.includes(productdescriptionFilter)) {
       match = false;
     }
     if (categoryFilter !== "" && !categoryCell.includes(categoryFilter)) {
       match = false;
     }
     if (onhandFilter !== "" && !onhandCell.includes(onhandFilter)) {
       match = false;
     }
     if (dateupdatedFilter !== "" && !dateupdatedCell.includes(dateupdatedFilter)) {
       match = false;
     }
 
     if (!match) {
       row.style.display = "none";
     } else {
       row.style.display = "";
     }
   }
 }
  
  function searchTable() {
    var searchInput = document.getElementById("myInput");
    var filterText = searchInput.value;
    var tableBody = document.getElementById("table-body");
    var rows = tableBody.getElementsByTagName("tr");
  
    for (var i = 0; i < rows.length; i++) {
      var row = rows[i];
      var cells = row.getElementsByTagName("td");
  
      var match = false;
      for (var j = 0; j < cells.length; j++) {
        if (cells[j].textContent.toLowerCase().includes(filterText.toLowerCase())) {
          match = true;
          break;
        }
      }
      if (!match) {
        row.style.display = "none";
      } else {
        row.style.display = "";
      }
    }
  }
</script>

<?php include_once("../reusable/footer.php"); ?>

</html>