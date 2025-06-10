<?php
require '../reusable/redirect404.php';
require '../session/session.php';
include "../database/x-dbconnect.php";

?>
     <!DOCTYPE html>
     <html>

     <head>
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title>HOME</title>
          <?php require '../reusable/header.php'; ?>
     </head>

     <body>
          <?php
          // Alert-messages
          // include 'alerts/alert-messages.php';

          // Modals
          // include 'modals/modals.php';

          // Sidebar
          include '../reusable/sidebar.php'; ?>

          <!-- === Dashboard === -->
          <section class="dashboard panel">

               <?php
               // TOP NAVBAR
               include '../reusable/navbarNoSearch.html';
               ?>
               <div class="left-panel">
                    <div class="content-header">
                         <h2 style="color:var(--dark-teal)"><?php echo date('F'); ?></h2>
                    </div>
                   
                         <!-- === Sales Graph === -->
                         <div class="panel-content">
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

                                   <div class="graph">
                                        /* <?php include 'salesreport.php'; ?> */
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

                              <div class="product-ranking">
                                   <!-- Products -->
                                   <?php include 'product_rank.php'; ?>
                              </div>
                         </div>
                    </div>
               </div>
               <div class="right-panel">
                    <div class="overview">

                         <!-- Alerts -->
                         <div class="alertbox">
                              <div class="content-header">
                                   <i class='bx bx-bell'></i>
                                   <span class="text">Alerts</span>
                                   <span class="number"> 0</span>
                              </div>
                              <div class="alerts">

                                   <!-- Alerts -->
                                   <div class="alertbox-content danger">
                                        <div class="alert">
                                             <p>This is an alert message.</p>
                                        </div>
                                   </div>
                                   <div class="alertbox-content warning">
                                        <div class="alert">
                                             <p>This is a warning message.</p>
                                        </div>
                                   </div>
                                   <div class="alertbox-content warning">
                                        <div class="alert">
                                             <p>This is a warning message.</p>
                                        </div>
                                   </div>
                                   <div class="alertbox-content warning">
                                        <div class="alert">
                                             <p>This is a warning message.</p>
                                        </div>
                                   </div>
                                   <div class="alertbox-content warning">
                                        <div class="alert">
                                             <p>This is a warning message.</p>
                                        </div>
                                   </div>

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
                         <!-- Weather -->
                         <div class="weather">
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
     <script src="../resources/js/script.js"></script>
     <script src="../resources/js/chart.js"></script>
     <!-- ======= Charts JS ====== -->
     <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
     <script src="js/chartsJS.js"></script>

     </html>
