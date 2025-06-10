document.addEventListener("DOMContentLoaded", function() {
    // Clock
<<<<<<<<<<<<<<  ✨ Codeium Command 🌟 >>>>>>>>>>>>>>>>
+    setInterval(function() {
+        var date = new Date();
+        var h = date.getHours().toString().padStart(2, '0');
+        var m = date.getMinutes().toString().padStart(2, '0');
+        var s = date.getSeconds().toString().padStart(2, '0');
+        var session = "AM";
+
+        if (h >= 12) {
+            session = "PM";
+            h = (h - 12).toString().padStart(2, '0');
+        }
+        if (h == "00") {
+            h = "12";
+        }
+        
+        var time = session + " " + h + ":" + m + ":" + s ;
+        var clockEl = document.getElementById("clock");
+        if (clockEl) {
+            clockEl.innerText = time;
+            clockEl.textContent = time;
+        }
+    }, 1000);
-    function updateClock() {
-        var date = new Date();
-        var h = date.getHours();
-        var m = date.getMinutes();
-        var s = date.getSeconds();
-        var session = "AM";
-        
-        if (h == 0) {
-            h = 12;
-        }
-
-        if (h > 12) {
-            h = h - 12;
-            session = "PM";
-        }
-        h = h < 10 ? "0" + h : h;
-        m = m < 10 ? "0" + m : m;
-        s = s < 10 ? "0" + s : s;
-        var time = session + " " + h + ":" + m + ":" + s ;
-        var clockEl = document.getElementById("clock");
-        if (clockEl) {
-            clockEl.innerText = time;
-            clockEl.textContent = time;
-        }
-    }
-    setInterval(updateClock, 1000);
<<<<<<<  3167696e-4b65-4232-9914-bdefcd3a368b  >>>>>>>

    // Date
    function updateDate() {
        var d = new Date();
        var day = d.getDate();
        var month = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'][d.getMonth()].slice(0, 3);
        var year = d.getFullYear();
        var date = month + ' ' + day + ', ' + year;
        var dateEl = document.getElementById("date");
        if (dateEl) {
            dateEl.innerHTML = date;
        }
    }
    updateDate();
    setInterval(updateDate, 1000);
});

