document.addEventListener("DOMContentLoaded", function() {
    // Clock
    function updateClock() {
        var date = new Date();
        var h = date.getHours();
        var m = date.getMinutes();
        var s = date.getSeconds();
        var session = "AM";
        if (h == 0) {
            h = 12;
        }
        if (h > 12) {
            h = h - 12;
            session = "PM";
        }
        h = h < 10 ? "0" + h : h;
        m = m < 10 ? "0" + m : m;
        s = s < 10 ? "0" + s : s;
        var time = h + ":" + m + ":" + s + " " + session;
        var clockEl = document.getElementById("clock");
        if (clockEl) {
            clockEl.innerText = time;
            clockEl.textContent = time;
        }
    }
    setInterval(updateClock, 1000);

    // Day
 
    // Date
    function updateDate() {
        var d = new Date();
        var day = d.getDate();
        var month = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'][d.getMonth()];
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
