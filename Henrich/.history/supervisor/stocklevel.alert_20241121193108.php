<style>
  .alert {
    /* position: fixed; */
    top: 40%;
    right: 10px;
    background-color: var(--sidebar-color);
    padding: 25px;
    border-radius: 10px;
    z-index: 9999;
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
    width: 30vw;
  }
  .alert h4 {
    margin-top: 0;
    font-weight: bold;
    color: #fff;
  }
  .alert ul {
    list-style: none;
    padding: 0;
    margin: 0;
  }
  .alert li {
    padding: 5px 0;
    border-bottom: 1px solid rgba(0,0,0,0.1);
  }
  .alert li:last-child {
    border-bottom: none;
  }
  .alert .product-name {
    font-weight: bold;
  }
  .alert .close {
    position: absolute;
    top: 0;
    right: 0;
    padding: 10px;
    color: var(--accent-color);
    cursor: pointer;
  }
</style>
<?php
  require '../database/dbconnect.php';

  $items = $conn->query("SELECT * FROM inventory WHERE onhand <= 10 ORDER BY onhand ASC");
  if ($items->num_rows > 0) {
    echo '<div class="alert">';
    echo '<h4>Low Stock Alert!</h4>';
    echo '<span class="close" onclick="closeAlert()">x</span>';
    echo '<ul>';
    while ($row = $items->fetch_assoc()) {
      $quantity = $row['onhand'];
      $legend = "";
      if ($quantity == 0) {
        $legend = "<span style='color: red'>CRITICAL</span>";
      } else if ($quantity <= 5) {
        $legend = "<span style='color: orange'>LOW</span>";
      }
      echo '<li><span class="product-name">' . $row['productname'] . '</span> - <span class="quantity">' . $row['onhand'] . '</span> units left ' . $legend . '</li>';
    }
    echo '</ul>';
    echo '</div>';
  }

?>
<script>
  function closeAlert() {
    document.querySelector('.alert').style.display = 'none';
  }
  </script>