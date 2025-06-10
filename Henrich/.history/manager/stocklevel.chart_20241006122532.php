/*************  ✨ Codeium Command 🌟  *************/
<?php // stocklevel.chart.php

        $sql = "SELECT * FROM inventory";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $onhand[] = $row['onhand'];
                $productcode[] = $row['productcode'];
            }
        }

        // Create a canvas element to render the chart
        echo '<canvas id="stock-chart" style="width: 100%; height: 300px;"></canvas>';

        // Include the Chart.js library
        echo '<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>';

        // Create the chart using Chart.js
        echo '<script>';
        echo 'var ctx = document.getElementById("stock-chart").getContext("2d");';
        echo 'var chart = new Chart(ctx, {';
        echo '  type: "bar",';
        echo '  data: {';
        echo '    labels: ' . json_encode($productcode) . ',';
        echo '    datasets: [{';
        echo '      label: "Stock Levels",';
        echo '      data: ' . json_encode($onhand) . ',';
        echo '      backgroundColor: [';
        foreach ($productcode as $key => $value) {
            echo '"rgba('. rand(0,255) . ',' . rand(0,255) . ',' . rand(0,255) . ', 0.2)"';
            if ($key < count($productcode) - 1) {
                echo ',';
            }
        }
        echo '      ],';
        echo '      borderColor: [';
        foreach ($productcode as $key => $value) {
            echo '"rgba(0, 0, 12, 1)"';
            if ($key < count($productcode) - 1) {
                echo ',';
            }
        }
        echo '      ],';
        echo '      borderWidth: 1';
        echo '    }]';
        echo '  },';
        echo '  options: {';
        echo '    scales: {';
        echo '      y: {';
        echo '        beginAtZero: true';
        echo '      }';
        echo '    }';
        echo '  }';
        echo '});';
        echo '</script>';



/******  6ccc3bed-4496-44b3-82a1-192e03014853  *******/