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

                    <div class="overview ">
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
                    </div>
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

                    <!--  -->
                    <div class="overview">
                         <div class="calendar">
                              <div class="header">
                                   <i class='bx bx-calendar '></i>
                              </div>
                              <!-- Day -->
                              <div class="date">
                                   <span id="day" class="text title"><?php echo date("l"); ?></span>
                                   <!-- Date -->
                                   <div id="date"> </div>

                                   <!-- Time -->
                                   <div id="clock"> </div>
                              </div>

                         </div>
                         <!-- <div id="openweathermap-widget-12"></div> -->
                         <!-- <script>
                              navigator.geolocation.getCurrentPosition(function(position) {
                                   var lat = position.coords.latitude;
                                   var lon = position.coords.longitude;
                                   console.log(lat, lon);
                                   console.log(name);
                                   window.myWidgetParam ? window.myWidgetParam : window.myWidgetParam = [];
                                   window.myWidgetParam.push({
                                        id: 12,
                                        lat: lat,
                                        lon: lon,
                                        appid: 'd189a36e54852bb0b9b7edeba90591c1',
                                        units: 'metric',
                                        containerid: 'openweathermap-widget-12',
                                   });
                                   (function() {
                                        var script = document.createElement('script');
                                        script.async = true;
                                        script.charset = "utf-8";
                                        script.src = "//openweathermap.org/themes/openweathermap/assets/vendor/owm/js/weather-widget-generator.js";
                                        var s = document.getElementsByTagName('script')[0];
                                        s.parentNode.insertBefore(script, s);
                                   })();
                              });
                         </script> -->
                         <div class="weather"> <!-- Weather -->
                              <img class="weather-icon">
                              <span class="text title city"></span>
                              <div class="weather-box">
                                   <!-- Weather -->
                                   <div class="weather-info">
                                        <div class="temp">
                                             <div class="numb" id="temp"></div>
                                             <span class="deg">°</span>
                                        </div>
                                        <div class="weather-details">
                                             <div class="humidity">
                                                  <span>humidity</span>
                                                  <i class='bx bxs-droplet-half'></i>
                                                  <p class="text" id="humidity"></p>
                                             </div>
                                             <div class="wind">
                                                  <i class='bx bxs-wind'></i>
                                                  <div class="text" id="wind"></div>
                                                  <span class="speed">m/s</span>
                                             </div>
                                        </div>
                                   </div>
                              </div>
                         </div>

                    </div>
               </div>

          </div>
     </section>

</body>

<?php include '../reusable/footer.php'; ?>

</html>