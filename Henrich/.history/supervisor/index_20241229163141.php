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

     <style>
          * {
    /* font-family: -apple-system, BlinkMacSystemFont, "San Francisco", Helvetica, Arial, sans-serif; */
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    /* font-weight: 300; */
    margin: 0;
    padding: 0;
}

html,
body {
    display: flex;
    flex-direction: column;
    height: 100vh;
    width: 100vw;
    align-items: center;
    justify-content: center;
    background: #f3f2f2;
    display: flex;
    align-items: center;
    justify-content: center;


}

.success {
    position: absolute;
    top: 35px;
    color : var(--white);
    padding: 10px;
    border-radius: 2px;
    background-color: var(--success-color);
    animation: fadeOut 5s forwards;
}
@keyframes fadeOut {
    0% {
        opacity: 1;
    }
    100% {
        opacity: 0;
    }
}

.error {
    color : var(--white);
    padding: 8px;
    border-radius: 2px;
    background-color: var(--danger-color);
}


.background {
    position: absolute;
    top: 0;
    left: 0;
    overflow: hidden;
    /* z-index: -1; */


    background-image: url('../images/warehouse.png');
    width: 100vw;
    height: 100vh;
    background-size: cover;
    background-repeat: no-repeat;
}
.background .img {
    background-image: url('../images/hfcbg.png');
    width: 120px;
    height: 1200px;
    rotate: -30deg;
    background-size: contain;
    background-repeat: repeat-x;
    background-repeat: repeat-y;
}
.blur {
    width: 100vw;
    height: 100vh;
    overflow: hidden;
    backdrop-filter: blur(4px);
    /* filter: blur(5px); */


    background-color: #0000007b;
    box-shadow: 0px 0px 105px 8px rgba(52,52,52,0.75) inset;
    -webkit-box-shadow: 0px 0px 105px 8px rgba(52,52,52,0.75) inset;
    -moz-box-shadow: 0px 0px 105px 8px rgba(52,52,52,0.75) inset;
    /* background: radial-gradient(ellipse at bottom, #1b1b1b00 0%, #1b1b1b 100%); */
}

.session {
    /* height: 700px; */
    display: flex;
    flex-direction: row;
    overflow: hidden;
    z-index: 999;
    /* width: 100%; */
    /* height: 100%; */
    /* margin: auto auto; */
    background: #ffffff;
    border-radius: 4px;
    box-shadow: 0px 2px 6px -1px rgba(0, 0, 0, .12);
}

/* From Uiverse.io by csemszepp */ 
.container {
    
    border-top-left-radius: 4px;
    border-bottom-left-radius: 4px;
    width: 100%;
    height: 100%;
    --s: 194px; /* control the size */
    --c1: #f6edb3;
    --c2: #b1443b;
  
    --_l: #0000 calc(25% / 3), var(--c1) 0 25%, #0000 0;
    --_g: conic-gradient(from 120deg at 50% 87.5%, var(--c1) 120deg, #0000 0);
  
    background: var(--_g), var(--_g) 0 calc(var(--s) / 2),
      conic-gradient(from 180deg at 75%, var(--c2) 60deg, #0000 0),
      conic-gradient(from 60deg at 75% 75%, var(--c1) 0 60deg, #0000 0),
      linear-gradient(150deg, var(--_l)) 0 calc(var(--s) / 2),
      conic-gradient(
        at 25% 25%,
        #0000 50%,
        var(--c2) 0 240deg,
        var(--c1) 0 300deg,
        var(--c2) 0
      ),
      linear-gradient(-150deg, var(--_l)) #7d302a /* third color here */;
    background-size: calc(0.866 * var(--s)) var(--s);
  }
  

.left {
    width: 320px;
    height: auto;
    min-height: 100%;
    display: flex;
    /* align-items: center; */
    justify-content: center;
    /* background-image: url("https://images.pexels.com/photos/114979/pexels-photo-114979.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=650&w=940"); */
    background-size: cover;
    border-top-left-radius: 4px;
    border-bottom-left-radius: 4px;

    svg {
        height: 40px;
        width: auto;
        /* margin:  20px;  */
    }
}

.left img {
    position: absolute;
    background-color: #fff;
    top: 45%;
    height: 90px;
    width: auto;
    object-fit: cover;
    border-top-left-radius: 4px;
    border-bottom-left-radius: 4px;
}

.title {
    text-align: center;
    margin-bottom: 20px;
}

.title h1 {
    font-size: 24px;
    font-weight: 500;
}

.title p {
    color: rgba(#000, .5);
    font-size: 14px;
    font-weight: 400;
}

.login-form {
    padding: 40px 30px;
    background: #fefefe;
    display: flex;
    flex-direction: column;
    justify-content: center; 
    align-items: center;
    padding-bottom: 20px;
}

form {
    width: 320px;
    margin: 20px;
    /* background: #3d2c2c; */
    display: flex;
    flex-direction: column;
    /* align-items: flex-start; */
    padding-bottom: 20px;

    h4 {
        margin-bottom: 20px;
        color: rgba(#000, .5);

        span {
            color: rgba(#000, 1);
            font-weight: 700;
        }
    }

    p {
        line-height: 155%;
        margin-bottom: 5px;
        font-size: 14px;
        color: #000;
        opacity: .65;
        font-weight: 400;
        max-width: 200px;
        margin-bottom: 40px;
    }
}

a.discrete {
    color: rgba(#000, .4);
    font-size: 14px;
    border-bottom: solid 1px rgba(#000, .0);
    padding-bottom: 4px;
    margin-left: auto;
    font-weight: 300;
    transition: all .3s ease;
    margin-top: 40px;

    &:hover {
        border-bottom: solid 1px rgba(#000, .2);
    }
}




/* INPUT */
.input-group {
    /* width: 100%; */
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    /* background-color: #515151; */
    gap: 4px;
}

.input-group .icon {
    padding: 10px ;
    /* border: 1px solid #515151; */
    color: #999;
}
.input-group .icon i {
    font-size: 16px;
    color: #999;
}

.wave-group {
    position: relative;
    width: 100%;
    /* background-color: #515151; */
    display: flex;
    flex-direction: column;
    /* align-items: center; */
}

.wave-group .input {
    font-size: 16px;
    padding: 10px 10px 10px 5px;
    display: block;
    /* width: 100%; */
    border: none;
    border-bottom: 1px solid #515151;
    background: transparent;
}

.wave-group .input:focus {
    outline: none;
}

.wave-group .label {
    color: #999;
    font-size: 18px;
    font-weight: normal;
    position: absolute;
    pointer-events: none;
    left: 5px;
    top: 10px;
    display: flex;
}

.wave-group .label-char {
    transition: 0.2s ease all;
    transition-delay: calc(var(--index) * .05s);
}

.wave-group .input:focus~label .label-char,
.wave-group .input:valid~label .label-char {
    transform: translateY(-20px);
    font-size: 14px;
    color: #5264AE;
}

.wave-group .bar {
    position: relative;
    display: block;
    /* width: auto; */
}

.wave-group .bar:before,
.wave-group .bar:after {
    content: '';
    height: 2px;
    width: 0;
    bottom: 1px;
    position: absolute;
    background: #5264AE;
    transition: 0.2s ease all;
    -moz-transition: 0.2s ease all;
    -webkit-transition: 0.2s ease all;
}

.wave-group .bar:before {
    left: 50%;
}

.wave-group .bar:after {
    right: 50%;
}

.wave-group .input:focus~.bar:before,
.wave-group .input:focus~.bar:after {
    width: 50%;
}

/* INPUT END */


.bottom-form {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: center;
    /* margin-bottom: 20px; */
    gap: 20px;
}

.bottom-form button {
    width: 100%;
    padding: 6px;
    margin-top: 20px;
    background: #5264AE;
    color: #fff;
    border-radius: 4px;
    border: none;
    font-size: 16px;
    cursor: pointer;
}
     </style>
</head>


<body>

     <!-- Display SweetAlert Toast for Login Success -->
     <?php if ($login_success): ?>
          <script>
               document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                         icon: 'success',
                         title: 'Signed in successfully',
                         showConfirmButton: false,
                         timer: 3000,
                         toast: true,
                         position: 'top-end'
                    });
               });
          </script>
     <?php endif; ?>
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


