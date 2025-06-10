<?php require '../reusable/redirect404.php';
require '../session/session.php';
include "../database/dbconnect.php"; ?>
<!DOCTYPE html>
<html>

<head>
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>HOME</title>
     <?php include '../reusable/header.php'; ?>
</head>

<body>
     <?php include '../reusable/sidebar.php';  ?>

     <section class="dashboard panel"> <!-- === Dashboard === -->
          <?php include '../reusable/navbarNoSearch.html'; // TOP NAVBAR  
          ?>
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
                              <span class="number"> 0</span>
                              <span class="arrow"> <i class='bx bx-right-arrow-alt'></i></span>
                         </a>
                         <a href="orders.php" class="box box2">
                              <i class='bx bx-cart-alt'></i>
                              <span class="text"></span>
                              <span class="number">0</span>
                         </a>
                         <a href="orders.php" class="box box3">
                              <i class='bx bx-cart'></i>
                              <span class="text"></span>
                              <span class="number">0</span>
                         </a>
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
               </div>
          </div>

          <div class="right-panel">
               <div class="overview">
                    <div class="alertbox"><!-- Alerts -->
                    <div class="content-header">
                              <i class='bx bx-bell'></i>
                              <span class="text">Low Stock Alert</span>
/*************  ✨ Codeium Command 🌟  *************/
                              <?php
                              $alerts = $conn->query("SELECT COUNT(*) FROM inventory WHERE onhand <= 5")->fetch_row()[0];
                              ?>
                              <span class="number"> <?php echo $alerts; ?> </span>
                              <span class="number"> 0</span>
/******  93f5ff7d-663a-4dfe-a7ea-fcaf7f470808  *******/
                         </div>
                         <?php
                         $sql = "SELECT productcode, productdescription, onhand FROM inventory WHERE onhand <= 5";
                         $result = $conn->query($sql);
                         ?>
                         <div class="alerts">
                              <?php
                              if ($result->num_rows > 0) {
                                   while ($row = $result->fetch_assoc()) {
                              ?>
                              <div class="alertbox-content warning">
                                   <div class="alert">
                                        <p>Product Code: <?php echo $row['productcode']; ?> - <?php echo $row['productdescription']; ?> has <?php echo $row['onhand']; ?> items left.</p>
                                   </div>
                              </div>
                              <?php
                                   }
                              } else {
                              ?>
                              <div class="alertbox-content warning">
                                   <div class="alert">
                                        <p>No low stock alert.</p>
                                   </div>
                              </div>
                              <?php
                              }
                              ?>

                         </div>
                    </div>
               </div>

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
     </section>

</body>

<?php include '../reusable/footer.php'; ?>

</html>