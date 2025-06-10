<?php
  require '../reusable/redirect404.php';
  require '../session/session.php';
  require '../database/dbconnect.php';
?>

<!DOCTYPE html>
<html>

<head>
  <title>Stock Management</title>
  <?php require '../reusable/header.php'; ?>
  <meta http-equiv="refresh" content="120">
  <link rel="stylesheet" type="text/css" href="../resources/css/table.css">
</head>

<body>

  <?php include 'stocklevel.alert.php'; // Alerts    
  ?>

  <?php include '../reusable/sidebar.php';  // Sidebar    
  ?>

  <section class=" panel"><!-- === Inventory === -->
    <?php include '../reusable/navbarNoSearch.html'; // TOP NAVBAR 
    ?>
    <div class="container-fluid"> <!-- === graph showing stock levels === -->
      <div class="table-header">
        <div class="title">
          <h2>Stock Levels</h2>
        </div>
      </div>
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
          <span>
            <h2>INVENTORY </h2>
          </span>
          <span style="font-size: 12px;">Stock Management (display only)</span>
        </div>
      </div>

      <div class="table-header">


        <div class="search-box">
          <i class='bx bx-search-alt-2' style="font-size: 24px"></i>
          <input type="text" id="general-search" onkeyup="search()"
            placeholder="Search...">
        </div>
        <script>
          // Search table Function
          function search() {
            // Declare variables 
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("general-search");
            filter = input.value.toUpperCase();
            table = document.getElementById("myTable");
            tr = table.getElementsByTagName("tr");

            // Loop through all table rows, and hide those who don't match the search query
            for (i = 0; i < tr.length; i++) {
              for (var td of tr[i].getElementsByTagName("td")) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                  tr[i].style.display = "";
                  break;
                } else {
                  tr[i].style.display = "none";
                }
              }
            }
          }
        </script>
      </div>

      <?php
      $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
      $stockManagementTableSQL = "SELECT * FROM inventory LIMIT $limit"; // We only want to display the selected number of items
      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }
      $sql = $stockManagementTableSQL;
      $result = $conn->query($sql);
      ?>
      <div class="container-fluid">
        <!-- Inventory Tab -->
        <table class="table" id="myTable" style="overflow-x:auto">
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
              <td><input type="text" placeholder="Search ID" id="iid-filter" onkeyup="filterTable()"></td>
              <td><input type="text" placeholder="Search Product Code" id="productcode-filter" onkeyup="filterTable()"></td>
              <td><input type="text" placeholder="Search Product Description" id="productdescription-filter" onkeyup="filterTable()"></td>
              <td><input type="text" placeholder="Search Category" id="category-filter" onkeyup="filterTable()"></td>
              <td><input type="text" placeholder="Search On Hand" id="onhand-filter" onkeyup="filterTable()"></td>
              <td><input type="text" placeholder="Search Date" id="dateupdated-filter" onkeyup="filterTable()"></td>
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
        <div class="container" style="display: flex; justify-content: center; flex-direction: column; align-items: center; ">
          <div style=" display: flex; justify-content: space-between; align-items: center; width: 100%;">
            <div class="filter-box">
              <label for="limit">Show</label>
              <select id="limit" onchange="location.href='?page=<?php echo $page ?>&limit=' + this.value">
                <option value="10" <?php echo $limit == 10 ? 'selected' : '' ?>>10</option>
                <option value="25" <?php echo $limit == 25 ? 'selected' : '' ?>>25</option>
                <option value="50" <?php echo $limit == 50 ? 'selected' : '' ?>>50</option>
                <option value="100" <?php echo $limit == 100 ? 'selected' : '' ?>>100</option>
              </select>
              <label for="limit">entries</label>
            </div>
            <div class="dataTables_info" id="example_info" role="status" aria-live="polite">Showing <?= $start + 1 ?> to <?= $start + $limit ?> of <?= $totalRecords ?> entries</div>
          </div>

          <ul class="pagination"><!-- Pagination -->
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
    var iidFilter = document.getElementById("iid-filter").value.toLowerCase();
    var productcodeFilter = document.getElementById("productcode-filter").value.toLowerCase();
    var productdescriptionFilter = document.getElementById("productdescription-filter").value.toLowerCase();
    var categoryFilter = document.getElementById("category-filter").value.toLowerCase();
    var onhandFilter = document.getElementById("onhand-filter").value.toLowerCase();
    var dateupdatedFilter = document.getElementById("dateupdated-filter").value.toLowerCase();

    var tableBody = document.getElementById("table-body");
    var rows = tableBody.getElementsByTagName("tr");

    for (var i = 0; i < rows.length; i++) {
      var row = rows[i];
      var cells = row.getElementsByTagName("td");

      var iidCell = cells[0].textContent.toLowerCase();
      var productcodeCell = cells[1].textContent.toLowerCase();
      var productdescriptionCell = cells[2].textContent.toLowerCase();
      var categoryCell = cells[3].textContent.toLowerCase();
      var onhandCell = cells[4].textContent.toLowerCase();
      var dateupdatedCell = cells[5].textContent.toLowerCase();

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

      if (match) {
        row.style.display = "table-row";
      } else {
        row.style.display = "none";
      }
    }
  }

  document.getElementById("iid-filter").addEventListener("input", filterTable);
  document.getElementById("productcode-filter").addEventListener("input", filterTable);
  document.getElementById("productdescription-filter").addEventListener("input", filterTable);
  document.getElementById("category-filter").addEventListener("input", filterTable);
  document.getElementById("onhand-filter").addEventListener("input", filterTable);
  document.getElementById("dateupdated-filter").addEventListener("input", filterTable);
</script>
<?php include_once("../reusable/footer.php"); ?>

</html>