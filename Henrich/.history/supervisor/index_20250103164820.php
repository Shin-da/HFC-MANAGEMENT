<?php
require '../reusable/redirect404.php';
require '../session/session.php';
include "../database/dbconnect.php";
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<!DOCTYPE html>
<html>

<head>
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>HOME</title>
     <?php include '../reusable/header.php'; ?>

</head>


<body>

     <?php include '../reusable/sidebar.php';  ?>

     <section class=" panel"> <!-- === Dashboard === -->
          <?php include '../reusable/navbarNoSearch.html';           // TOP NAVBAR            
          ?>
          <div class="dashboard">

               <div class="left-panel">

                    <div class="panel-content"> <!-- === Sales Graph === -->
                         <div class="container sales-report">
                              <div class="content-header">
                                   <div class="title ">
                                        <i class='bx bx-tachometer'></i>
                                        <span class="text">Sales Report</span>
                                   </div>

                                   <div class="dropdown">
                                        <i class='bx bx-chevron-down'></i>
                                        <div class="dropdown-content"> </div>
                                   </div>
                              </div>
                              <div class="content-header">
                                   <h2 style="color:var(--dark-teal)"><?php echo date('F'); ?></h2>
                              </div>
                              <div class="graph">
                                   <!-- === Sales Report === -->

                              </div>

                         </div>
                    </div>
                    <div class="overview ">

                         <div class="boxes">
                              <a href="orders.php" class="box box1">
                                   <!-- Sale  -->
                                   <i class='bx bx-cart'></i>
                                   <span class="text">Pending Orders</span>
                                   <span class="number">
                                        <?php
                                        $pendingOrders = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'Pending'")->fetch_row()[0];
                                        echo $pendingOrders;
                                        ?>
                                   </span>
                                   <!-- <span class="arrow"> <i class='bx bx-right-arrow-alt'></i></span> -->
                              </a>
                              <a href="orders.php" class="box box2">
                                   <i class='bx bx-check-circle'></i>
                                   <span class="text">Completed Orders</span>
                                   <span class="number">
                                        <?php
                                        $completedOrders = $conn->query("SELECT COUNT(*) FROM orderhistory WHERE status = 'Completed'")->fetch_row()[0];
                                        echo $completedOrders;
                                        ?>
                                   </span>
                              </a>
                              <!-- <a href="orders.php" class="box box3">
                                   <i class='bx bx-cart'></i>
                                   <span class="text"></span>
                                   <span class="number">0</span>
                              </a> -->
                         </div>

                    </div>
                    <div class="overview ">
                         <div class="panel-content top-products">
                              <div class="content-header">
                                   <div class="title"></div>
                                   <i class='bx bx-trending-up'></i>
                                   <span class="text">Top Products</span>
                              </div>

                              <div class="product-ranking"><!-- === Top Products === -->
                                   <p>

                                   </p>
                              </div>
                         </div>

                         <!-- button to test sweet alert and toast -->

                         <button class="btn btn-primary" onclick="swal('Hello world!')">Test Alert</button>

                    </div>

                    <!-- <div class="overview ">
                         <h1>How to use and customize <img src="https://sweetalert2.github.io/images/swal2-logo.png"></h1>
                         <div>
                              <h4>Modal Type</h4>
                              <p>Sweet alert with modal type and customize message alert with html and css</p>
                              <button id="success">Success</button>
                              <button id="error">Error</button>
                              <button id="warning">Warning</button>
                              <button id="info">Info</button>
                              <button id="question">Question</button>
                         </div>
                         <br>
                         <div>
                              <h4>Custom image and alert size</h4>
                              <p>Alert with custom icon and background icon</p>
                              <button id="icon">Custom Icon</button>
                              <button id="image">Custom Background Image</button>
                         </div>
                         <br>
                         <div>
                              <h4>Alert with input type</h4>
                              <p>Sweet Alert with Input and loading button</p>
                              <button id="subscribe">Subscribe</button>
                         </div>
                         <br>
                         <div>
                              <h4>Redirect to visit another site</h4>
                              <p>Alert to visit a link to another site</p>
                              <button id="link">Redirect to Utopian</button>
                         </div>
                    </div> -->
               </div>

               <div class="right-panel">
                    <div class="overview">
                         <div class="alertbox"><!-- Alerts -->
                              <div class="content-header">
                                   <i class='bx bx-bell'></i>
                                   <span class="text">Low Stock Alert</span>
                                   <?php
                                   $alerts = $conn->query("SELECT COUNT(*) FROM inventory WHERE onhand <= 10")->fetch_row()[0];
                                   ?>
                                   <span class="number"> <?php echo $alerts; ?> </span>
                              </div>
                              <?php
                              $sql = "SELECT  productname, onhand FROM inventory WHERE onhand <= 10 ORDER BY onhand ASC";
                              $result = $conn->query($sql);
                              ?>
                              <div class="alerts">
                                   <?php
                                   if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {

                                             if ($row['onhand'] <= 5) {
                                                  $color = 'danger';
                                             } else if ($row['onhand'] <= 10) {
                                                  $color = 'warning';
                                             } else {
                                                  $color = 'legend';
                                             }
                                   ?>
                                             <div class="alertbox-content <?php echo $color; ?>">
                                                  <div class="alert">
                                                       <p><?php echo $row['productname']; ?> has <?php echo $row['onhand'] >= 0 ? $row['onhand'] : ''; ?> items left.</p>
                                                  </div>
                                             </div>
                                        <?php
                                        }
                                   } else {
                                        ?>
                                        <div class="alertbox-content " style="background-color: var(--sidebar-color);">
                                             <div class="alert">
                                                  <p>No products with low stock.</p>
                                             </div>
                                        </div>
                                   <?php
                                   }
                                   ?>
                              </div>
                         </div>
                    </div>
                    <div class="overview">

                          <?php include 'calendar.php?>
                          
                         
                    </div>

               </div>

          </div>
     </section>

</body>

<?php include '../reusable/footer.php'; ?>

</html>