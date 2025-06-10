<?php
// Check if the requested page exists
var_dump($_SERVER['REQUEST_URI']); // Add this line for debugging
if (file_exists($_SERVER['REQUEST_URI'])) {
    // Redirect to the 404 error page
    var_dump($_SERVER['REQUEST_URI']); // Add this line for debugging
    header('Location: /HenrichProto/404.html');
    exit;
}
?>

<<<<<<<<<<<<<<  ✨ Codeium Command 🌟 >>>>>>>>>>>>>>>>
 
 <!DOCTYPE html>
 <html>
 
 <head>
     <style>
         .imageloading {
             display: block;
+            animation: hideImage 5s forwards;
+            animation-delay: 0s;
-            animation: hideImage 1s forwards;
-            animation-delay: 1s;
             height: 20vh;
         }
 
         @keyframes hideImage {
             0% {
                 opacity: 1;
             }
 
             100% {
                 opacity: 0;
             }
         }
     </style>
 
 </head>
 
 <body>
     <div class="container" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);" >
         <img class="imageloading" src="images/hfclogo.png" alt=" logo">
 
     </div>
 </body>
 <script>
     setTimeout(function() {
         document.querySelector('.imageloading').style.display = 'none';
+    }, 5000);
-    }, 9000);
 </script>
 
 </html>
<<<<<<<  1470c817-3491-4566-a181-da2250ec0c3e  >>>>>>>