<<<<<<<<<<<<<<  ✨ Codeium Command 🌟 >>>>>>>>>>>>>>>>
+<!DOCTYPE html>
+<html>
+<head>
+    <style>
+        .image {
+            display: block;
+            animation: hideImage 1s forwards;
+        }
-<?php
-// Check if the requested page exists
-var_dump($_SERVER['REQUEST_URI']); // Add this line for debugging
-if (file_exists($_SERVER['REQUEST_URI'])) {
-    // Redirect to the 404 error page
-    var_dump($_SERVER['REQUEST_URI']); // Add this line for debugging
-    header('Location: /HenrichProto/404.html');
-    exit;
-}
-?>
 
+        @keyframes hideImage {
+            0% { opacity: 1; }
+            100% { opacity: 0; }
+        }
+    </style>
+</head>
+<body>
+    <img class="image" src="images/hfclogo.png" alt=" logo">
+    <script>
+        setTimeout(function() {
+            document.querySelector('.image').style.display = 'none';
+        }, 1000);
+    </script>
+</body>
+</html>
-
-<img src="images/hfclogo.png" alt=" logo">
<<<<<<<  ccb1c01a-b2b7-46da-ac31-1784dc3a8f1f  >>>>>>>