/*************  ✨ Codeium Command 🌟  *************/
<style>
    .calendar {
        font-size: 18px;
        color: var(--text-color);
        border-radius: 10px;
        padding: 20px;
        background-color: var(--panel-color);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .calendar:hover {
        background-color: var(--border-color);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.25);
    }

    .calendar-content {
        display: none;
        position: absolute;
        top: 50px;
        right: 0;
        background-color: var(--panel-color);
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        padding: 20px;
    }

    .calendar-content.show {
        display: block;
    }

    .date {
        display: grid;
        grid-template-columns: auto auto;
        justify-content: space-between;
        align-items: center;
        margin: 20px 0;
    }

    .date .header {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .date .header i {
        font-size: 30px;
    }

    .date .text {
        font-size: 18px;
    }

    /* Calendar */
    #calendar {
        width: 300px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #fff;
        padding: 10px;
        margin: 20px auto;
    }

    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    #calendar table {
        width: 100%;
        border-collapse: collapse;
    }

    .calendar-header button {
    #calendar th,
    #calendar td {
        border: 1px solid #ddd;
        padding: 5px;
        text-align: center;
    }

    #calendar th {
        background-color: #f0f0f0;
    }

    #calendar .boto-prev,
    #calendar .boto-next {
        background-color: #4CAF50;
        color: white;
        border: none;
        padding: 5px;
        font-size: 16px;
        cursor: pointer;
    }

    .calendar-header button:hover {
    #calendar .boto-prev:hover,
    #calendar .boto-next:hover {
        background-color: #45a049;
    }

    .calendar-body {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        grid-gap: 10px;
    #calendar .actiu {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 10;
        background-color: rgba(0, 0, 0, 0.5);
        transition: all 0.5s ease-in-out;
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.371), 0 4px 8px 0 rgba(0, 0, 0, 0.05);
    }

    .calendar-day {
        background-color: #f0f0f0;
        padding: 10px;
        text-align: center;
        border-radius: 5px;
        cursor: pointer;
    #calendar .amagat-esquerra {
        left: -300px;
    }

    .calendar-day:hover {
        background-color: #ddd;
    #calendar .amagat-dreta {
        left: 300px;
    }

    .calendar-day.today {
    #calendar .inactiu {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 5;
        background-color: #fff;
        transition: all 0.5s ease-in-out;
    }

    #calendar .avui {
        background-color: #fcf8e3;
        color: #8a6d3b;
    }

    .calendar-day.outside {
    #calendar .fora {
        background-color: #ddd;
        color: #666;
    }

    /* Weather */
    .weather {
        background-color: var(--panel-color);
        padding: 15px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 15px;
        border: 2px solid var(--border-color);
    }

    .weather-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        background-color: rgba(0, 255, 255, 0.1);
        padding: 10px;
        border-radius: 10px;
    }

    .weather-icon {
        width: 50px;
        height: 50px;
    }

    .weather-info {
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }

    .weather-details {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }
</style>

<!-- Calendar -->
<div class="calendar">
    <div class="calendar-header">
        <button class="prev" onclick="prevMonth()">&#9666;</button>
        <span id="month"></span>
        <button class="next" onclick="nextMonth()">&#9656;</button>
<!-- Weather -->
<div class="weather">
    <img class="weather-icon" src="" alt="Weather Icon">
    <span class="text city"></span>
    <div class="weather-box">
        <div class="weather-info">
            <div class="temp">
                <div class="numb" id="temp"></div>
                <span class="deg">°</span>
            </div>
            <div class="weather-details">
                <div class="humidity">
                    <span>Humidity</span>
                    <i class='bx bxs-droplet-half'></i>
                    <p class="text" id="humidity"></p>
                </div>
                <div class="wind">
                    <i class='bx bxs-wind'></i>
                    <div class="text" id="wind"></div>
                    <span class="speed">m/s</span>
                </div>
            </div>
        </div>
    </div>
    <div class="calendar-body" id="calendar-body"></div>
</div>
<script>
    var months = [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December'
    ];
<!-- calendar -->
<div class="calendar" onclick="toggleCalendar()">
    <div class="date">
        <div class="header">
            <i class='bx bx-calendar'></i>
            <span id="day" class="text"><?php echo date("l"); ?></span>
        </div>
        <div>
            <div id="date"></div>
            <div id="clock"></div>
        </div>
    </div>
    <div class="calendar-content">
        <div id="calendar"></div>
        <script>
            function toggleCalendar() {
                var calendarContent = document.querySelector('.calendar-content');
                calendarContent.classList.toggle('show');
            }

    var days = [
        'Sunday',
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday'
    ];
            var months = [
                'January',
                'February',
                'March',
                'April',
                'May',
                'June',
                'July',
                'August',
                'September',
                'October',
                'November',
                'December'
            ];

    var days_abr = [
        'Su',
        'Mo',
        'Tu',
        'We',
        'Th',
        'Fr',
        'Sa'
    ];
            var days = [
                'Sunday',
                'Monday',
                'Tuesday',
                'Wednesday',
                'Thursday',
                'Friday',
                'Saturday'
            ];

    var date = new Date();
            var days_abr = [
                'Su',
                'Mo',
                'Tu',
                'We',
                'Th',
                'Fr',
                'Sa'
            ];

    function prevMonth() {
        date.setMonth(date.getMonth() - 1);
        renderCalendar();
    }
            Number.prototype.pad = function(num) {
                var str = '';
                for (var i = 0; i < (num - this.toString().length); i++)
                    str += '0';
                return str += this.toString();
            }

    function nextMonth() {
        date.setMonth(date.getMonth() + 1);
        renderCalendar();
    }
            function calendar(widget, data) {

    function renderCalendar() {
        var calendarBody = document.getElementById('calendar-body');
                var original = widget.getElementsByClassName('actiu')[0];

        var firstDayOfMonth = new Date(date.getFullYear(), date.getMonth(), 1).getDay();
                if (typeof original === 'undefined') {
                    original = document.createElement('table');
                    original.setAttribute('data-actual',
                        data.getFullYear() + '/' +
                        data.getMonth().pad(2) + '/' +
                        data.getDate().pad(2))
                    widget.appendChild(original);
                }

        var lastDayOfMonth = new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();
                var diff = data - new Date(original.getAttribute('data-actual'));

        var day = 1;
                diff = new Date(diff).getMonth();

        calendarBody.innerHTML = '';
                var e = document.createElement('table');

        for (var i = 0; i < 6; i++) {
            for (var j = 0; j < 7; j++) {
                var dayElement = document.createElement('div');
                e.className = diff === 0 ? 'amagat-esquerra' : 'amagat-dreta';
                e.innerHTML = '';

                dayElement.className = 'calendar-day';
                widget.appendChild(e);

                if (day > lastDayOfMonth) {
                    dayElement.className += ' outside';
                } else if (day === date.getDate()) {
                    dayElement.className += ' today';
                e.setAttribute('data-actual',
                    data.getFullYear() + '/' +
                    data.getMonth().pad(2) + '/' +
                    data.getDate().pad(2))

                var fila = document.createElement('tr');
                var titol = document.createElement('th');
                titol.setAttribute('colspan', 7);

                var boto_prev = document.createElement('button');
                boto_prev.className = 'boto-prev';
                boto_prev.innerHTML = '&#9666;';

                var boto_next = document.createElement('button');
                boto_next.className = 'boto-next';
                boto_next.innerHTML = '&#9656;';

                titol.appendChild(boto_prev);
                titol.appendChild(document.createElement('span')).innerHTML =
                    months[data.getMonth()] + '<span class="any">' + data.getFullYear() + '</span>';

                titol.appendChild(boto_next);

                boto_prev.onclick = function() {
                    data.setMonth(data.getMonth() - 1);
                    calendar(widget, data);
                };

                boto_next.onclick = function() {
                    data.setMonth(data.getMonth() + 1);
                    calendar(widget, data);
                };

                fila.appendChild(titol);
                e.appendChild(fila);

                fila = document.createElement('tr');

                for (var i = 0; i < 7; i++) {
                    fila.innerHTML += '<th>' + days_abr[i] + '</th>';
                }

                dayElement.innerHTML = day;
                e.appendChild(fila);

                if (j < firstDayOfMonth) {
                    dayElement.className += ' outside';
                } else if (day <= lastDayOfMonth) {
                    day++;
                /* Obtinc el dia que va acabar el mes anterior */
                var inici_mes =
                    new Date(data.getFullYear(), data.getMonth(), -1).getDay();

                var actual = new Date(data.getFullYear(),
                    data.getMonth(),
                    -inici_mes);

                /* 6 setmanes per cobrir totes les posiblitats
                 *  Quedaria mes consistent alhora de mostrar molts mesos 
                 *  en una quadricula */
                for (var s = 0; s < 6; s++) {
                    var fila = document.createElement('tr');

                    for (var d = 0; d < 7; d++) {
                        var cela = document.createElement('td');
                        var span = document.createElement('span');

                        cela.appendChild(span);

                        span.innerHTML = actual.getDate();

                        if (actual.getMonth() !== data.getMonth())
                            cela.className = 'fora';

                        /* Si es avui el decorem */
                        if (data.getDate() == actual.getDate() &&
                            data.getMonth() == actual.getMonth())
                            cela.className = 'avui';

                        actual.setDate(actual.getDate() + 1);
                        fila.appendChild(cela);
                    }

                    e.appendChild(fila);
                }

                calendarBody.appendChild(dayElement);
                setTimeout(function() {
                    e.className = 'actiu';
                    original.className +=
                        diff === 0 ? ' amagat-dreta' : ' amagat-esquerra';
                }, 20);

                original.className = 'inactiu';

                setTimeout(function() {
                    var inactius = document.getElementsByClassName('inactiu');
                    for (var i = 0; i < inactius.length; i++)
                        widget.removeChild(inactius[i]);
                }, 1000);

            }
        }

        document.getElementById('month').innerHTML = months[date.getMonth()] + ' ' + date.getFullYear();
    }

    renderCalendar();
</script>
            calendar(document.getElementById('calendar'), new Date());
        </script>
    </div>
</div>
/******  06141e54-f926-45ba-9477-f1883cc72807  *******/