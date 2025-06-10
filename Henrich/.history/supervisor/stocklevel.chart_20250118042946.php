<?php
// Get only the top 10 products by quantity for better visualization
$sql = "SELECT productname, onhandquantity, availablequantity 
        FROM inventory 
        ORDER BY onhandquantity DESC 
        LIMIT 10";
$result = $conn->query($sql);

$labels = [];
$onHandData = [];
$availableData = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['productname'];
        $onHandData[] = $row['onhandquantity'];
        $availableData[] = $row['availablequantity'];
    }
}
?>

<div style="position: relative; height: 400px; width: 100%; margin-bottom: 20px;">
    <canvas id="stockChart"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('stockChart');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [
            {
                label: 'On Hand Quantity',
                data: <?= json_encode($onHandData) ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            },
            {
                label: 'Available Quantity',
        options: {
            legend: {
                display: false
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        fontColor: "rgba(0, 0, 0, 0.5)"
                    }
                },
                x: {
                    ticks: {
                        fontColor: "rgba(0, 0, 0, 0.5)"
                    }
                }
            },
            title: {
                display: true,
                text: "Stock Levels",
                fontSize: 20,
                fontColor: "rgba(0, 0, 0, 0.8)"
            },
            layout: {
                padding: {
                    left: 10,
                    right: 10,
                    top: 10,
                    bottom: 10
                }
            },
            plugins: {
                datalabels: {
                    display: true,
                    formatter: function(value, context) {
                        return context.chart.data.labels[context.dataIndex] + ": " + value + " items";
                    },
                    color: "#000",
                    font: {
                        weight: "bold",
                        size: 12
                    }
                }
            }
        }
    });
</script>

