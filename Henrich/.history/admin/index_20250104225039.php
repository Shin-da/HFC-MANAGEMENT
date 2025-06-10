<?php
require '../reusable/redirect404.php';
require '../database/dbconnect.php';
require '../session/session.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Page</title>
  <?php require '../reusable/header.php'; ?>
  <link rel="stylesheet" type="text/css" href="../resources/css/table.css">
</head>

<body>
  <?php  include 'admin-sidebar.php';?>

  <section class="panel">
    <?php include '../reusable/navbarNoSearch.html';    ?>
    <div class="container-fluid"> <!-- Stock Management -->
      <div class="table-header">
        <div class="title">
          <span>
            <h2>User Account Management </h2>
          </span>
          <span style="font-size: 12px;">Manage User Accounts of personels within the warehouse </span>
        </div>

        <div class="table-header">
          <a href="customeraccounts.php" class="btn btn-primary">Add New Customer Account</a>
        </div>
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

        <?php // pagination for stock management table
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
        $start = ($page - 1) * $limit;
        $items = $conn->query("SELECT * FROM user  LIMIT $start, $limit");
        $totalRecords = $conn->query("SELECT COUNT(*) FROM user")->fetch_row()[0];
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

      $stockManagementTableSQL = "SELECT * FROM user ORDER BY uid ASC LIMIT $limit OFFSET $offset";
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
                <th>User ID</th>
                <th>User Name</th>
                <th>User Email</th>
                <th>Role</th>
                <th>Actions</th>
              </tr>
              <tr class="filter-row">
                <th><input type="text" placeholder="Search ID..." id="uid-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 0)"></th>
                <th><input type="text" placeholder="Search Name..." id="username-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 1)"></th>
                <th><input type="text" placeholder="Search Email..." id="useremail-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 2)"></th>
                <th><input type="text" placeholder="Search Role..." id="role-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 3)"></th>
              </tr>
            </thead>

            <tbody id="table-body">
              <?php
              if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                  $uid = $row["uid"];
                  $username = $row["username"];
                  $useremail = $row["useremail"];
                  $role = $row["role"];
              ?>
                  <tr>
                    <td><?= $uid ?></td>
                    <td><?= $username ?></td>
                    <td><?= $useremail ?></td>
                    <td><?= $role ?></td>
                    <td>
                      <a href="edit-user.php?uid=<?= $uid ?>" class="edit">Edit</a>
                      <a href="delete-user.php?uid=<?= $uid ?>" class="delete">Delete</a>
                    </td>
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
        <div class="container" style="display: flex; justify-content: center; flex-direction: column; align-items: center; "><!-- Pagination for stock management -->
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

<script src="../resources/js/script.js"></script>

<!-- ======= Charts JS ====== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script src="../resources/js/chart.js"></script>

</html>