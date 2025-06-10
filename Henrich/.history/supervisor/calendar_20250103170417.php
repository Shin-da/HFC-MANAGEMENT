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
        <?php include 'fullcalendar.php'; ?>
    </div>
</div>