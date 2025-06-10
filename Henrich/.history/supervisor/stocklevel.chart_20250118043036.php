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
                data: <?= json_encode($availableData) ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            title: {
                display: true,
                text: 'Top 10 Products by Quantity',
                font: {
                    size: 16
                }
            },
            legend: {
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Quantity'
                }
            },
            x: {
                ticks: {
                    maxRotation: 45,
                    minRotation: 45
                }
            }
        }
    }
});
</script>

