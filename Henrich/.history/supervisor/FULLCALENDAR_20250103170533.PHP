
<style>
    /* Calendar */
    .calendar {
        width: 300px;
        max-width: 100vw;
        margin: 20px auto;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.371), 0 4px 8px 0 rgba(0, 0, 0, 0.05);
        padding: 15px;
    }

    .calendar table {
        width: 100%;
        border-collapse: collapse;
    }

    .calendar th,
    .calendar td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: center;
    }

    .calendar th {
        background-color: #f0f0f0;
    }

    .calendar .boto-prev,
    .calendar .boto-next {
        background-color: #4CAF50;
        color: white;
        border: none;
        padding: 10px;
        font-size: 16px;
        cursor: pointer;
        border-radius: 5px;
    }

    .calendar .boto-prev:hover,
    .calendar .boto-next:hover {
        background-color: #45a049;
    }

    .calendar .date {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .calendar .date .header {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .calendar .date .header i {
        font-size: 24px;
    }

    .calendar .date .header .text {
        font-size: 18px;
        font-weight: 600;
    }

    .calendar .date #day {
        font-size: 24px;
        font-weight: 600;
    }

    .calendar .date #date {
        font-size: 24px;
        font-weight: 600;
    }

    .calendar .date #clock {
        font-size: 24px;
        font-weight: 600;
    }

    .calendar .amagat-esquerra {
        left: -300px;
    }

    .calendar .amagat-dreta {
        left: 300px;
    }

    .calendar .inactiu {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 5;
        background-color: #fff;
        transition: all 0.5s ease-in-out;
    }

    .calendar .avui {
        background-color: #fcf8e3;
        color: #8a6d3b;
    }

    .calendar .fora {
        background-color: #ddd;
        color: #666;
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

            function calendar(widget, data, index) {

                var original = widget.getElementsByClassName('actiu')[index];

                if (typeof original === 'undefined') {
                    original = document.createElement('table');
                    original.className = 'actiu';
                    original.setAttribute('data-actual',
                        data.getFullYear() + '/' +
                        data.getMonth().pad(2) + '/' +
                        data.getDate().pad(2));
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
                    data.getDate().pad(2));

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
                    calendar(widget, data, index);
                };

                boto_next.onclick = function() {
                    data.setMonth(data.getMonth() + 1);
                    calendar(widget, data, index);
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
                    new Date(data.getFullYear(), data.getMonth(), 0).getDay();

                var actual = new Date(data.getFullYear(),
                    data.getMonth(),
                    1 - inici_mes);

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
            }

            var index = 0;
            var calendarData = new Date();
            calendar(document.getElementById('calendar'), calendarData, index);
            document.getElementById('calendar').addEventListener('click', function() {
                index++;
                calendar(document.getElementById('calendar'), calendarData, index);
            });
        </script>
    </div>

</div>
