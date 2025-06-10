<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html>

<head>
    <title>HOME</title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" type="text/css" href="../resources/css/table.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
</head>

<body>
    <?php include '../reusable/sidebar.php'; // Sideb   ar     
    ?>

    <!-- === Sales Report === -->
    <section class=" panel">
        <?php include '../reusable/navbarNoSearch.html'; // TOP NAVBAR        
        ?>

        <div class="container-fluid"> <!-- === graph showing stock levels === -->
            <div class="table-header">
                <div class="title">
                    <h2>Sales Report</h2>
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
          $items = $conn->query("SELECT * FROM inventory  LIMIT $start, $limit");
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
            <!-- === Sales Report === -->
            <div class="container" style="background-color: white; padding: 20px; border-radius: 5px; border: 1px solid var(--border-color);">
                <!-- dito ka maglagay mike -->
                <!-- <canvas id="sales-chart" style="width: 100%; height: 300px; max-height: 300px;"></canvas> -->
                <?php include 'salesreport.php'; ?>
                <!-- <script src="../resources/js/chart.js"></script> -->
                <!-- <script>
                        var ctx = document.getElementById("sales-chart").getContext("2d");
                        var salesChart = new Chart(ctx, <?php echo json_encode($config); ?>);
                    </script> -->
            </div>
        </div>
    </section>
</body>
<?php include '../reusable/footer.php'; ?>

</html>