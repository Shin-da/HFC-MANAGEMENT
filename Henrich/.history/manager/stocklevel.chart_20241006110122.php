/*************  ✨ Codeium Command 🌟  *************/
<?php

if (isset($_SESSION['uid']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'supervisor') {
        $onhand = array();
        $productcode = array();
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
        echo '}' . ']';
        echo '},';

        echo 'options: {';
        echo 'scales: {';
        echo 'yAxes: [{';
        echo 'ticks: {';
        echo 'beginAtZero: true';
        echo '},';
        echo '}]';
        echo '},';
        echo '}';

        echo '});';

        echo '</script>';

    } else {
        header("Location: ../index.php");
        exit();
    }
}



/******  523ae867-67b6-4ec2-a842-8024c1eba6f3  *******/