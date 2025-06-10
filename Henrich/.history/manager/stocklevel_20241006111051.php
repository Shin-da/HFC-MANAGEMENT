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
a

</body>
<?php include_once("../reusable/footer.php"); ?>

</html>