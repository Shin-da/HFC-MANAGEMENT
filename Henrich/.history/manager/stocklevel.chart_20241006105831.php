<?php // Context from Code Snippet admin/admin.php:(83-88)
// admin/admin.php
// <?php
// } else {
//      header("Location: admin.php");
//      exit();
// }
// ?>

<?php

<?php
$dataPoints = array();
$items = $conn->query("SELECT productcode, onhand FROM inventory");
if ($items->num_rows > 0) {
  while ($row = $items->fetch_assoc()) {
    $dataPoints[] = array("label" => $row['productcode'], "y" => $row['onhand']);
  }
}
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>


<script>
  if (document.getElementById("chartContainer")) {
    var chart = new CanvasJS.Chart("chartContainer", {
      animationEnabled: true,
      theme: "light2",
      title: {
        text: "Current Stock Levels"
      },
      data: [{
        type: "column",
        dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
      }]
    });
    chart.render();
  }
</script>
<div id="chartContainer" style="height: 300px; width: 100%;"></div>


