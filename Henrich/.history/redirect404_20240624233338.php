<<<<<<<<<<<<<<  ✨ Codeium Command 🌟 >>>>>>>>>>>>>>>>
 <?php
 // Check if the requested page exists
+$uri = $_SERVER['REQUEST_URI'];
+$page = basename($uri);
+
+if ($page !== 'home.php') {
-var_dump($_SERVER['REQUEST_URI']); // Add this line for debugging
-if (file_exists($_SERVER['REQUEST_URI'])) {
     // Redirect to the 404 error page
-    var_dump($_SERVER['REQUEST_URI']); // Add this line for debugging
     header('Location: /HenrichProto/404.html');
     exit;
+}
-}
-?>
<<<<<<<  3b5f5bf5-72f1-4a02-8107-95a173d2eb9b  >>>>>>>