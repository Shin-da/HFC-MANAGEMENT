<<<<<<<<<<<<<<  ✨ Codeium Command 🌟 >>>>>>>>>>>>>>>>
+(function() {
+    var ctx = document.getElementById('salesChart');
+
+    if (ctx) {
+        var config = {
+            type: 'bar',
+            data: {
+                labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
+                datasets: [{
+                    label: '# of Votes',
+                    data: [12, 19, 3, 5, 2, 3],
+                    backgroundColor: [
+                        'rgba(255, 99, 132, 0.2)',
+                        'rgba(54, 162, 235, 0.2)',
+                        'rgba(255, 206, 86, 0.2)',
+                        'rgba(75, 192, 192, 0.2)',
+                        'rgba(153, 102, 255, 0.2)',
+                        'rgba(255, 159, 64, 0.2)'
+                    ],
+                    borderColor: [
+                        'rgba(255, 99, 132, 1)',
+                        'rgba(54, 162, 235, 1)',
+                        'rgba(255, 206, 86, 1)',
+                        'rgba(75, 192, 192, 1)',
+                        'rgba(153, 102, 255, 1)',
+                        'rgba(255, 159, 64, 1)'    
+                    ]  
+                }]
+            },
+            options: {
+                scales: {
+                    y: {
+                        beginAtZero: true
+                    }       
+                }
+            }
+        };
+
+        var salesChart = new Chart(ctx, config);
+    } else {
+        console.warn('Chart container element not found.');
+    }
+})();
-//sales chart
-
-var ctx = document.getElementById('salesChart');
-
-if (ctx) {
-    var myChart = new Chart(ctx, {
-        type: 'bar',
-        data: {
-            labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
-            datasets: [{
-                label: '# of Votes',
-                data: [12, 19, 3, 5, 2, 3],
-                backgroundColor: [
-                    'rgba(255, 99, 132, 0.2)',
-                    'rgba(54, 162, 235, 0.2)',
-                    'rgba(255, 206, 86, 0.2)',
-                    'rgba(75, 192, 192, 0.2)',
-                    'rgba(153, 102, 255, 0.2)',
-                    'rgba(255, 159, 64, 0.2)'
-                ],
-                borderColor: [
-                    'rgba(255, 99, 132, 1)',
-                    'rgba(54, 162, 235, 1)',
-                    'rgba(255, 206, 86, 1)',
-                    'rgba(75, 192, 192, 1)',
-                    'rgba(153, 102, 255, 1)',
-                    'rgba(255, 159, 64, 1)'     
-                ]   
-            }]
-        },
-        options: {
-            scales: {
-                y: {
-                    beginAtZero: true
-                }        
-            }
-        }
-    });
-} else {
-    console.warn('Chart container element not found.');
-}
 
<<<<<<<  91e92e76-f57d-42b0-9a92-51857534716b  >>>>>>>