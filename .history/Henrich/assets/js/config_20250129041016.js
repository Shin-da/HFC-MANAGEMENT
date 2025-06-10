const BASE_PATH = '/HFC MANAGEMENT/Henrich';
const BASE_URL = window.location.origin + BASE_PATH;
const API_URL = `${BASE_URL}/api`;
const ADMIN_API_URL = `${BASE_URL}/admin/api`;

// Utility function for URL construction
const buildUrl = (...parts) => {
    return parts.map(part => part.replace(/^\/+|\/+$/g, '')).join('/');
};

const WS_CONFIG = {
    host: window.location.hostname, // Dynamic host
    port: 8080,
    secure: window.location.protocol === 'https:', // Dynamic security
    reconnectAttempts: 5,
    reconnectDelay: 3000,
    fallbackToPolling: true, // Enable fallback
    pollingInterval: 5000
};

// Export configuration object
const CONFIG = {
    BASE_URL,
    API_URL,
    ADMIN_API_URL,
    WS_CONFIG,
    PATHS: {
        dashboard: buildUrl(ADMIN_API_URL, 'get-dashboard-data.php'),
        users: {
            info: buildUrl(API_URL, 'users/info.php'),
            online: buildUrl(API_URL, 'users/online.php'),
            offline: buildUrl(API_URL, 'users/offline.php')
        },
        chat: {
            messages: buildUrl(API_URL, 'chat/messages.php'),
            recent: buildUrl(API_URL, 'chat/recent.php'),
            markRead: buildUrl(API_URL, 'chat/mark-read.php')
        }
    }
};
