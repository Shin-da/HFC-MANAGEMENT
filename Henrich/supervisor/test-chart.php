<!DOCTYPE html>
<html>
<head>
    <title>Chart Test</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container { width: 500px; height: 300px; margin: 20px; }
    </style>
</head>
<body>
    <div class="chart-container">
        <canvas id="testChart"></canvas>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ctx = document.getElementById('testChart');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['A', 'B', 'C', 'D', 'E'],
                    datasets: [{
                        label: 'Test Data',
                        data: [12, 19, 3, 5, 2],
                        borderColor: 'rgb(75, 192, 192)',
                    }]
                }
            });
        });
    </script>
</body>
</html>
