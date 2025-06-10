document.addEventListener("DOMContentLoaded", function() {
    // Clock
    setInterval(function() {
        var date = new Date();
        var h = date.getHours().toString().padStart(2, '0');
        var m = date.getMinutes().toString().padStart(2, '0');
        var s = date.getSeconds().toString().padStart(2, '0');
        var session = "AM";

        if (h >= 12) {
            session = "PM";
            h = (h - 12).toString().padStart(2, '0');
        }
        if (h == "00") {
            h = "12";
        }
        
        var time = session + " " + h + ":" + m + ":" + s ;
        var clockEl = document.getElementById("clock");
        if (clockEl) {
            clockEl.innerText = time;
            clockEl.textContent = time;
        }
    }, 1000);

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

