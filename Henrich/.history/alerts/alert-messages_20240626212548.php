 <!-- include 'dbconnect.php'; -->

 <!-- if ($conn->connect_error) { -->
 <!-- die("Connection failed: " . $conn->connect_error); }-->
 
 <style>
      /* CSS for styling the alert message */
      .alert-message {
           margin: 20px;
      }

      .alert {
           padding: 15px;
           margin-bottom: 20px;
           border: 1px solid transparent;
           border-radius: 4px;
      }

      .alert.alert-success {
           color: #155724;
           background-color: #d4edda;
           border-color: #c3e6cb;
      }

      .alert button.btn-close {
           background: transparent;
           border: none;
           float: right;
           font-size: 1.5rem;
           color: #000;
           opacity: 0.5;
           transition: opacity 0.3s;
      }

      .alert button.btn-close:hover {
           opacity: 1;
      }
 </style>




 <!-- this section is alert messages -->
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

 <!-- Successfully logged in -->

 <div class="alert-message">

      <div class="alert alert-success alert-dismissible fade show" role="alert">
           <strong>Success!</strong> You are logged in.
           <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>

 </div>




 <!-- Unable to log in -->

 <div class="alert-message">

      <div class="alert alert-danger alert-dismissible fade show" role="alert">



           <!--  -->





           <!--  -->