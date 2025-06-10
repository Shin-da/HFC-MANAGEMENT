<style>
    .calendar {
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
    }

    .calendar-header button {
        background-color: #4CAF50;
        color: white;
        border: none;
        padding: 5px;
        font-size: 16px;
        cursor: pointer;
    }

    .calendar-header button:hover {
        background-color: #45a049;
    }

    .calendar-body {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        grid-gap: 10px;
    }

    .calendar-day {
        background-color: #f0f0f0;
        padding: 10px;
        text-align: center;
        border-radius: 5px;
        cursor: pointer;
    }

    .calendar-day:hover {
        background-color: #ddd;
    }

    .calendar-day.today {
        background-color: #fcf8e3;
        color: #8a6d3b;
    }

    .calendar-day.outside {
        background-color: #ddd;
        color: #666;
    }
</style>

<!-- Calendar -->
<div class="calendar">
    <div class="calendar-header">
        <button class="prev" onclick="prevMonth()">&#9666;</button>
        <span id="month"></span>
        <button class="next" onclick="nextMonth()">&#9656;</button>
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

    var date = new Date();

    function prevMonth() {
        date.setMonth(date.getMonth() - 1);
        renderCalendar();
    }

    function nextMonth() {
        date.setMonth(date.getMonth() + 1);
        renderCalendar();
    }

    function renderCalendar() {
        var calendarBody = document.getElementById('calendar-body');

        var firstDayOfMonth = new Date(date.getFullYear(), date.getMonth(), 1).getDay();

        var lastDayOfMonth = new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();

        var day = 1;

        calendarBody.innerHTML = '';

        for (var i = 0; i < 6; i++) {
            for (var j = 0; j < 7; j++) {
                var dayElement = document.createElement('div');

                dayElement.className = 'calendar-day';

                if (day > lastDayOfMonth) {
                    dayElement.className += ' outside';
                } else if (day === date.getDate()) {
                    dayElement.className += ' today';
                }

                dayElement.innerHTML = day;

                if (j < firstDayOfMonth) {
                    dayElement.className += ' outside';
                } else if (day <= lastDayOfMonth) {
                    day++;
                }

                calendarBody.appendChild(dayElement);
            }
        }

        document.getElementById('month').innerHTML = months[date.getMonth()] + ' ' + date.getFullYear();
    }

    renderCalendar();
</script>
