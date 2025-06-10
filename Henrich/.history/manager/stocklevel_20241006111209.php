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
      <?php include 'stocklevel.chart.php'; // Chart     ?>
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
          <input type="text" id="myInput" onkeyup="search()"
            placeholder="Search...">
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
        <table class="table" id="myTable">
          <thead>
            <tr>
              <th>Inventory ID</th>
              <th>Product Code</th>
              <th>Product Description</th>
              <th>Category</th>
              <th>On Hand</th>
              <th>Date Updated</th>
            </tr>
            <tr>
              <td><input type="text" onkeyup="search()" id="iid" placeholder="Search..."></td>
              <td><input type="text" onkeyup="search()" id="productcode" placeholder="Search..."></td>
              <td><input type="text" onkeyup="search()" id="productdescription" placeholder="Search..."></td>
              <td><input type="text" onkeyup="search()" id="category" placeholder="Search..."></td>
              <td><input type="text" onkeyup="search()" id="onhand" placeholder="Search..."></td>
              <td><input type="text" onkeyup="search()" id="dateupdated" placeholder="Search..."></td>
            </tr>
          </thead>
          <tbody>
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

  </section>

<script>
  function search() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("myInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("myTable");
    tr = table.getElementsByTagName("tr");

    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[0];
      if (td) {
        txtValue = td.textContent || td.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
          tr[i].style.display = "";
        } else {
          tr[i].style.display = "none";
        }
      }
    }
  }
</script>

</body>
<?php include_once("../reusable/footer.php"); ?>

</html>