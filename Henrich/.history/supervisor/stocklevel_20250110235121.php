<script>
  const tableBody = document.querySelector("#table-body");
  const rows = Array.from(tableBody.rows);
  rows.sort(naturalSort);
  tableBody.append(...rows);
</script>

<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
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
    <div class="container-fluid" style="display: flex; flex-direction: row; justify-content: space-between; align-items: flex-start;">

      <div style="width: auto;">
        <div class="table-header">
          <div class="title">
            <h2>Stock Levels</h2>
          </div>
        </div>
        
        <div class="chart" style="">
          <?php include 'stocklevel.chart.php'; // Chart 
          ?>
        </div>
      </div>
      <div style="width: auto; border: solid 1px #ff4 ">
        <?php include 'stocklevel.alert.php'; // Stock Alerts 
        ?>
      </div>
    </div>

    <div class="container-fluid"> <!-- Stock Management -->
      <div class="table-header">
        <div class="title">
          <span>
            <h2>INVENTORY</h2>
          </span>

          <span style="font-size: 12px;">Stock Management (display only)</span>
        </div>
        
        <!-- <a class="btn add-btn" href="add.stockmovement.php"> <i class="i bx bx-plus"></i>Encode to Inventory </a> -->
      </div>
      <div class="table-header">
          <!-- <div style=" display: flex; justify-content: space-around; align-items: center; width: 100%;"> -->
            <div>
              <a class="btn add-btn" href="add.stockmovement.php"> <i class="i bx bx-plus"></i> Encode to Inventory </a>
            </div>
          <!-- </div> -->
        </div>

      <div class="table-header">
        <form class="form">
          <button>
            <svg width="17" height="16" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-labelledby="search">
              <path d="M7.667 12.667A5.333 5.333 0 107.667 2a5.333 5.333 0 000 10.667zM14.334 14l-2.9-2.9" stroke="currentColor" stroke-width="1.333" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
          </button>
          <input class="input" id="general-search" onkeyup="search()" placeholder="Search the table..." required="" type="text">
          <button class="reset" type="reset">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </form>

        <?php // pagination
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
        $start = ($page - 1) * $limit;
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'inventoryid';
        $order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

        $items = $conn->query("SELECT * FROM inventory ORDER BY $sort $order LIMIT $start, $limit");
        $totalRecords = $conn->query("SELECT COUNT(*) FROM inventory")->fetch_row()[0];
        $totalPages = ceil($totalRecords / $limit);
        ?>
        <div style=" display: flex; justify-content: space-around; align-items: center; width: 100%;"> <!--  -->

          <div class="dataTables_info" id="example_info" role="status" aria-live="polite">Showing <?= $start + 1 ?> to <?= $start + $limit ?> of <?= $totalRecords ?> entries</div>
          <div class="filter-box"> <!-- Filter results by number of entries -->
            <label for="limit">Show</label>
            <select id="limit" onchange="location.href='?page=<?= $page ?>&limit=' + this.value">
              <option value="10" <?php echo $limit == 10 ? 'selected' : '' ?>>10</option>
              <option value="25" <?php echo $limit == 25 ? 'selected' : '' ?>>25</option>
              <option value="50" <?php echo $limit == 50 ? 'selected' : '' ?>>50</option>
              <option value="100" <?php echo $limit == 100 ? 'selected' : '' ?>>100</option>
            </select>
            <label for="limit">entries</label>
          </div>
        </div>
      </div>
      <?php // pagination for stock management table
      $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
      $offset = ($page - 1) * $limit;

      $stockManagementTableSQL = "SELECT * FROM inventory ORDER BY $sort $order LIMIT $limit OFFSET $offset";
      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }
      $sql = $stockManagementTableSQL;
      $result = $conn->query($sql);
      ?>
      <div class="">
        <div class="container-fluid" style="overflow-x:Scroll;">

          <!-- Inventory Table -->
          <table class="table" id="myTable">
            <thead>
              <tr>
                <th><a href="?sort=inventoryid&order=<?= $sort === 'inventoryid' && $order === 'ASC' ? 'DESC' : 'ASC' ?>">Inventory ID</a></th>
                <th><a href="?sort=productcode&order=<?= $sort === 'productcode' && $order === 'ASC' ? 'DESC' : 'ASC' ?>">Product Code</a></th>
                <th><a href="?sort=productname&order=<?= $sort === 'productname' && $order === 'ASC' ? 'DESC' : 'ASC' ?>">Product Name</a></th>
                <th><a href="?sort=productcategory&order=<?= $sort === 'productcategory' && $order === 'ASC' ? 'DESC' : 'ASC' ?>">Product Category</a></th>
                <th><a href="?sort=onhandquantity&order=<?= $sort === 'onhandquantity' && $order === 'ASC' ? 'DESC' : 'ASC' ?>">On Hand (Pieces)</a></th>
                <th><a href="?sort=dateupdated&order=<?= $sort === 'dateupdated' && $order === 'ASC' ? 'DESC' : 'ASC' ?>">Date Updated</a></th>
              </tr>
              <tr class="filter-row">
                <th><input type="text" placeholder="Search ID..." id="inventoryid-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 0)"></th>
                <th><input type="text" placeholder="Search Product Code..." id="productcode-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 1)"></th>
                <th><input type="text" placeholder="Search Product Name..." id="productname-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 2)"></th>
                <th><input type="text" placeholder="Search Category..." id="productcategory-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 3)"></th>
                <th><input type="text" placeholder="Search On Hand..." id="onhandquantity-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 4)"></th>
                <th><input type="text" placeholder="Search Date Updated..." id="dateupdated-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 5)"></th>
              </tr>
            </thead>

            <tbody id="table-body">
              <?php
              if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                  $inventoryid = $row['inventoryid'];
                  $productcode = $row['productcode'];
                  $productname = $row['productname'];
                  $productcategory = $row['productcategory'];
                  $onhandquantity = $row['onhandquantity'];
                  $dateupdated = $row['dateupdated'];
              ?>
                  <tr>
                    <td><?= $inventoryid ?></td>
                    <td><?= $productcode ?></td>
                    <td><?= $productname ?></td>
                    <td><?= $productcategory ?></td>
                    <td><?= $onhandquantity ?></td>
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
        </div>
        <div class="container" style="display: flex; justify-content: center; flex-direction: column; align-items: center; background-color: var(--h);"><!-- Pagination for stock management -->
          <ul class="pagination">
            <li><a href="?page=<?= $page - 1 <= 1 ? 1 : $page - 1 ?>&sort=<?= $sort ?>&order=<?= $order ?>" class="prev">&laquo;</a></li>
            <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
              <li><a href="?page=<?= $i ?>&sort=<?= $sort ?>&order=<?= $order ?>" class="page <?= $page == $i ? 'active' : '' ?>"><?= $i ?></a></li>
            <?php } ?>
            <li><a href="?page=<?= $page + 1 > $totalPages ? $totalPages : $page + 1 ?>&sort=<?= $sort ?>&order=<?= $order ?>" class="next">&raquo;</a></li>
          </ul>
        </div>
      </div>

    </div>
  </section>

</body>

<script src="../resources/js/table.js"></script>
<?php include_once("../reusable/footer.php"); ?>

</html>