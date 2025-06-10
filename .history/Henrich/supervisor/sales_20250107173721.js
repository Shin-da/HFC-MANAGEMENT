const chartData = [];
const chartLabels = window.chartLabels;
const chartLabel = window.chartLabel;
const chartBackgroundColor = chartData.map(data => data > 1000 ? 'rgba(54, 162, 235, 0.7)' : 'rgba(255, 99, 132, 0.8)');
const chartBorderColor = chartData.map(data => data > 1000 ? 'rgba(54, 162, 235, 1)' : 'rgba(255, 99, 132, 1)');

const ctx = document.getElementById('myChart').getContext('2d');
const myChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: chartLabels,
    datasets: [{
      label: 'Total Sales',
      data: chartData,
      backgroundColor: chartBackgroundColor,
      borderColor: chartBorderColor,
      borderWidth: 1,
      borderRadius: 5, // Rounded edges
      borderSkipped: false // Apply rounded edges to all bars
    }]
  },
  options: {
    plugins: {
      title: {
        display: true,
        text: chartLabel,
        color: 'black'
      },
      legend: {
        labels: {
          color: 'black'
        }
      }
    },
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          color: 'black'
        }
      },
      x: {
        ticks: {
          color: 'black'
        }
      }
    },
    responsive: true,
    maintainAspectRatio: false
  }
});

const ctxPolar = document.getElementById('polarAreaChart').getContext('2d');
const polarChart = new Chart(ctxPolar, {
  type: 'polarArea',
  data: {
    labels: chartLabelsChart.slice(0, 10),
    datasets: [{
      label: 'Top Product Sales',
      data: chartDataChart.slice(0, 10),
      backgroundColor: [
        'rgba(255, 99, 132, 0.2)',
        'rgba(54, 162, 235, 0.2)',
        'rgba(255, 206, 86, 0.2)',
        'rgba(75, 192, 192, 0.2)',
        'rgba(153, 102, 255, 0.2)',
        'rgba(255, 159, 64, 0.2)',
        'rgba(199, 199, 199, 0.2)',
        'rgba(83, 102, 255, 0.2)',
        'rgba(255, 205, 86, 0.2)',
        'rgba(75, 192, 192, 0.2)'
      ],
      borderColor: [
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)',
        'rgba(199, 199, 199, 1)',
        'rgba(83, 102, 255, 1)',
        'rgba(255, 205, 86, 1)',
        'rgba(75, 192, 192, 1)'
      ],
      borderWidth: 1
    }]
  },
  options: {
    plugins: {
      legend: {
        labels: {
          color: 'black'
        }
      }
    },
    responsive: true,
    maintainAspectRatio: false
  }
});

// Henrich/supervisor/sales.js
const combinedCtx = document.getElementById('combined-chart').getContext('2d');
const combinedChart = new Chart(combinedCtx, {
  type: 'line',
  data: {
    labels: orderdate_values,
    datasets: [{
      label: 'Sales',
      data: ordertotal_values,
      borderColor: 'rgba(255, 99, 132, 1)',
      backgroundColor: 'rgba(255, 99, 132, 0.2)',
      fill: false
    }, {
      label: 'Inventory',
      data: inventory_values,
      borderColor: 'rgba(54, 162, 235, 1)',
      backgroundColor: 'rgba(54, 162, 235, 0.2)',
      fill: false
    }]
  },
  options: {
    scales: {
      y: {
        beginAtZero: true
      }
    },
    plugins: {
      title: {
        display: true,
        text: 'Combined Sales and Inventory Chart'
      },
      legend: {
        labels: {
          color: 'black'
        }
      }
    },
    responsive: true,
    maintainAspectRatio: false
  }
});