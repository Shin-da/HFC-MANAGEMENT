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