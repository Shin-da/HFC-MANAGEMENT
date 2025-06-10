class ExecutiveCalendar {
    constructor() {
        this.calendar = null;
        this.events = [];
        this.init();
    }

    async init() {
        await this.loadEvents();
        this.initializeCalendar();
        this.initializeMiniCalendar();
        this.setupEventListeners();
    }

    async loadEvents() {
        try {
            const response = await fetch('/api/ceo/calendar/events.php?action=list');
            if (!response.ok) throw new Error('Failed to fetch events');
            const data = await response.json();
            this.events = data.events;
            this.updateUpcomingEvents();
        } catch (error) {
            showErrorToast('Error loading calendar events');
            console.error(error);
        }
    }

    initializeCalendar() {
        this.calendar = new FullCalendar.Calendar(document.getElementById('mainCalendar'), {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: this.events,
            editable: true,
            selectable: true,
            select: this.handleDateSelect.bind(this),
            eventClick: this.handleEventClick.bind(this)
        });

        this.calendar.render();
    }

    async handleEventSave(eventData) {
        try {
            const response = await fetch('/api/ceo/calendar/events.php?action=add', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(eventData)
            });

            if (!response.ok) throw new Error('Failed to save event');
            await this.loadEvents();
            this.calendar.refetchEvents();
            showSuccessToast('Event saved successfully');
        } catch (error) {
            showErrorToast('Error saving event');
            console.error(error);
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new ExecutiveCalendar();
});
