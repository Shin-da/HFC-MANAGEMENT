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

            #calendar table {
                width: 100%;
                border-collapse: collapse;
            }

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

            #calendar .boto-prev:hover,
            #calendar .boto-next:hover {
                background-color: #45a049;
            }

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

            #calendar .amagat-esquerra {
                left: -300px;
            }

            #calendar .amagat-dreta {
                left: 300px;
            }

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
</div>
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
        <style>
            body {
                font-family: sans-serif;
                background-color: #f5f5f5;
            }

      
        </style>
        <div id="calendar"></div>
        <script>
            function toggleCalendar() {
                var calendarContent = document.querySelector('.calendar-content');
                calendarContent.classList.toggle('show');
            }

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

            var days = [
                'Sunday',
                'Monday',
                'Tuesday',
                'Wednesday',
                'Thursday',
                'Friday',
                'Saturday'
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

            Number.prototype.pad = function(num) {
                var str = '';
                for (var i = 0; i < (num - this.toString().length); i++)
                    str += '0';
                return str += this.toString();
            }

            function calendar(widget, data) {

                var original = widget.getElementsByClassName('actiu')[0];

                if (typeof original === 'undefined') {
                    original = document.createElement('table');
                    original.setAttribute('data-actual',
                        data.getFullYear() + '/' +
                        data.getMonth().pad(2) + '/' +
                        data.getDate().pad(2))
                    widget.appendChild(original);
                }

                var diff = data - new Date(original.getAttribute('data-actual'));

                diff = new Date(diff).getMonth();

                var e = document.createElement('table');

                e.className = diff === 0 ? 'amagat-esquerra' : 'amagat-dreta';
                e.innerHTML = '';

                widget.appendChild(e);

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

                e.appendChild(fila);

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

            calendar(document.getElementById('calendar'), new Date());
        </script>
    </div>
</div>


