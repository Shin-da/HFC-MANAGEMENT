<<<<<<<<<<<<<<  ✨ Codeium Command 🌟 >>>>>>>>>>>>>>>>
 // Reference the Chart.js script
 document.addEventListener("DOMContentLoaded", function() {
     var script = document.createElement("script");
     script.src = "https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js";
     script.onload = function() {
         var ctx = document.getElementById('salesChart');
 
         if (ctx) {
             var config = {
                 type: 'line',
                 data: {
+                    labels: [],
-                    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                     datasets: [{
                         label: 'Monthly Sales',
+                        data: [],
-                        data: [12, 19, 3, 5, 2, 3, 15, 10, 11, 14, 9, 15],
                         borderColor: [
                             'rgba(255, 99, 132, 1)',
                             'rgba(54, 162, 235, 1)',
                             'rgba(255, 206, 86, 1)',
                             'rgba(75, 192, 192, 1)',
                             'rgba(153, 102, 255, 1)',
                             'rgba(255, 159, 64, 1)',    
                         ],
                         borderWidth: 2,
                         backgroundColor: 'rgba(0, 0, 0, 0)',
                         pointBackgroundColor: 'rgba(0, 0, 0, 0)',
                         pointBorderColor: 'rgba(0, 0, 0, 0)',
                         fill: false
                     }]
                 },
                 options: {
                     scales: {
                         y: {
                             beginAtZero: true
                         }       
                     }
                 }
             };
 
+            var fetchSalesData = function() {
+                var sql = "SELECT MONTH(Order_DateTime) as Month, SUM(Quantity) as Sales FROM tblorders GROUP BY MONTH(Order_DateTime) ORDER BY Month ASC";
+                $.ajax({
+                    url: 'database/dbconnect.php',
+                    type: 'POST',
+                    data: { sql: sql },
+                    success: function(response) {
+                        var salesData = JSON.parse(response);
+                        config.data.labels = salesData.map(item => item.Month);
+                        config.data.datasets[0].data = salesData.map(item => item.Sales);
+                        var salesChart = new Chart(ctx, config);
+                    }
+                });
+            };
+            fetchSalesData();
-            var salesChart = new Chart(ctx, config);
         } else {
             console.warn('Chart container element not found.');
         }
     };
     document.head.appendChild(script);
 });
 
 
 
<<<<<<<  3cfc84ee-972a-4851-9f9d-c07f09f0d9ae  >>>>>>>