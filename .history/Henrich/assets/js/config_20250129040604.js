const WS_CONFIG = {
    host: window.location.hostname, // Dynamic host
    port: 8080,
    secure: window.location.protocol === 'https:', // Dynamic security
    reconnectAttempts: 5,
    reconnectDelay: 3000,
    fallbackToPolling: true, // Enable fallback
    pollingInterval: 5000
};

const BASE_URL = window.location.origin + '/HFC MANAGEMENT/Henrich';
