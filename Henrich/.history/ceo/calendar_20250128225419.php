<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    header('Location: ../login/login.php');
    exit();
}

Page::setTitle('Executive Calendar - CEO Dashboard');
Page::setBodyClass('ceo-calendar');

ob_start(); ?>

<div class="calendar-container">
    <div class="page-header">
        <h1>Executive Calendar</h1>
        <div class="header-actions">
            <button id="addEvent" class="btn primary">
                <i class="bx bx-plus"></i> Add Event
            </button>
            <button id="syncCalendar" class="btn secondary">
                <i class="bx bx-sync"></i> Sync Calendar
            </button>
        </div>
    </div>

    <div class="calendar-grid">
        <div class="calendar-sidebar">
            <div class="mini-calendar" id="miniCalendar"></div>
            <div class="upcoming-events">
                <h3>Upcoming Events</h3>
                <div id="upcomingEventsList"></div>
            </div>
        </div>

        <div class="calendar-main">
            <div class="calendar-view" id="mainCalendar"></div>
        </div>
    </div>
</div>

<!-- Event Modal Template -->
<div id="eventModal" class="modal">
    <!-- Modal content will be dynamically inserted -->
</div>

<?php
$content = ob_get_clean();
Page::render($content);
?>
