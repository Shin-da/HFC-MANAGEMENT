<?php
session_start();
if (isset($_SESSION['uid']) && isset($_SESSION['email'])) {
?>

  <!DOCTYPE html>
  <html>

  <head>
    
    <title>INVENTORY</title>

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="../resources/css/style.css">
    <link rel="stylesheet" type="text/css" href="../resources/css/sidebar.css">
    <link rel="stylesheet" type="text/css" href="../resources/css/dashboard.css">
    <link rel="stylesheet" type="text/css" href="../resources/css/colors.css">
    <link rel="stylesheet" type="text/css" href="../resources/css/alerts.css">
    <!-- Boxicons CDN Link -->
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>

    <!-- For Realtime Search  -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
            /* CSS to style the table */
            .table {
                width: 100%;
                border-collapse: collapse;
            }

            .table th,
            .table td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
                vertical-align: middle;
                white-space: nowrap;
                max-width: 100%;
            }

            .table th {
                background-color: #f2f2f2;
            }

            .table tr:nth-child(even) {
                background-color: #f2f2f2;
            }

            .table .clickable-row:hover {
                background-color: var(--border-color);
                color: var(--text-color);
                cursor: pointer;
                border: 1px solid var(--text-color);
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                transform: scale(1.01);
            }

            .table tbody tr:hover {
                background-color: var(--border-color);
                color: var(--text-color);
            }
        </style>
  </head>

  <body>
    <?php
    // Alert-messages
    // include 'alerts/alert-messages.php';

    // Modals
    // include 'modals/modals.php';

    // Sidebar 
    include '../reusable/sidebar.php';

    // The user is logged in; continue with the page contents
    include '../databases/database/dbconnect.php';

    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
    $sql = "SELECT * FROM Inventory";
    $result = $conn->query($sql);
    ?>

    <div class="alert-message">
      <!-- This section shows if there are products that are low on Available_quantity -->
      <?php
      $sql_low_on_stock = "SELECT InventoryID, Product_Name, Available_quantity FROM Inventory WHERE Available_quantity < 10";
      $result_low_on_stock = $conn->query($sql_low_on_stock);

      if ($result_low_on_stock->num_rows > 0) {
      ?>
        <div class="alert error" role="alert">
           <strong>Warning!</strong>
          <div class="alert-message">
          <p> Products below are low on stock: </p><br>

      <?php
        while ($row = $result_low_on_stock->fetch_assoc()) {
          echo '
            ' . $row["InventoryID"] . " - " . $row["Product_Name"] . " - " . $row["Available_quantity"] . "<br>";
        }
      ?>
        </div>
        
        </div>
      <?php
      }
      ?>
    </div>


    <!-- === Inventory === -->
    <section class=" panel">
      <?php
      // TOP NAVBAR
      include 'navbar.html';
      // include 'inventorynav.html';
      ?>


      <div class="container title">
        <i class='bx bx-tachometer'></i>
        <span class="text">Inventory</span>
      </div>

      <div class="panel-content">
        <!-- INVENTORY TAB -->
        <div class=" overview">
          <script>
            $(document).ready(function() {
              $("#myInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#myTable tbody tr").filter(function() {
                  $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
              });
            });
          </script>

          <table class="table table-striped table-hover table-bordered table-responsive w-100" id="myTable">
            <thead>
              <tr>
                <th>Product Code</th>
                <th>Product Description</th>
                <th>Weight</th>
                <th>Available Quantity</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                  echo "<tr>
                <td>" . $row["ProductCode"] . "</td>
                <td>" . $row["Product_Name"] . "</td>
                <td>" . $row["Weight"] . "</td>
                <td>" . $row["Available_quantity"] . "</td>
            </tr>";
                }
              } else {
                echo "0 results";
              }
              $conn->close();
              ?>
            </tbody>
          </table>
        </div>

        <!-- Inventory Tab -->
      </div>
    </section>

    <script src="../resources/js/script.js"></script>

  </body>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

  </html>

<?php
} else {
  // The user is not logged in; redirect to the login page
  header("Location: ../login/login.php");
  exit();
}
?>