/*************  ✨ Codeium Command 🌟  *************/
<?php // Context from Code Snippet admin/admin.php:(83-88)
// admin/admin.php
// <?php
// } else {
//      header("Location: admin.php");
//      exit();
// }
// ?>

<?php

if (isset($_SESSION['uid']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'supervisor') {   

        $sql = "SELECT * FROM inventory";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $onhand = array();
            $productcode = array();
            while ($row = $result->fetch_assoc()) {
                $onhand[] = $row['onhand'];
                $productcode[] = $row['productcode'];
            }
        }
        else {
            $onhand = array();
            $productcode = array();
        }

        echo '<canvas id="myChart"></canvas>';
        echo '<script> ';
        echo 'var ctx = document.getElementById("myChart").getContext("2d");';
        echo 'var myChart = new Chart(ctx, {';
        echo 'type: "bar",';
        echo 'data: {';
        echo 'labels: ' . json_encode($productcode) . ',';
        echo 'datasets: [{';
        echo 'label: "On Hand",';
        echo 'data: ' . json_encode($onhand) . ',';
        echo 'backgroundColor: [';
        echo '"rgba(255, 99, 132, 0.2)"';
        echo '],';
        echo 'borderColor: [';
        echo '"rgba(255, 99, 132, 1)"';
        echo '],';
        echo 'borderWidth: 1';
        echo '}'
        echo ']}';
        echo '});';
        echo '</script>';
        echo json_encode($onhand);
        echo json_encode($productcode);


    } else {
        header("Location: ../index.php");
        exit();
    }
}


/******  683423f3-e78c-4ce7-aba8-08c42ecc0cca  *******/