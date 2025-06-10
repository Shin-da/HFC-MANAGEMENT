const WS_CONFIG = {
    host: '127.0.0.1',
    port: 8080,
    secure: false
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
