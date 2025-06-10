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