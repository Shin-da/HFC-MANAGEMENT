const WS_CONFIG = {
    host: window.location.hostname, // This will work with both localhost and production
    port: 8080,
    secure: window.location.protocol === 'https:' // Auto-detect if we should use WSS
};

const BASE_URL = '/HFC MANAGEMENT/Henrich';

// Initialize chat when document is ready
document.addEventListener('DOMContentLoaded', () => {
    if (typeof ChatManager === 'undefined') {
        console.error('ChatManager class not loaded');
        return;
    }

    try {
        window.chatManager = new ChatManager();
        console.log('Chat manager initialized successfully');
    } catch (error) {
        console.error('Error initializing chat manager:', error);
    }
});
