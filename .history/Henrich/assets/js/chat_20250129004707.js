class ChatManager {
    constructor() {
        this.socket = null;
        this.messageContainer = document.querySelector('.chat-messages');
        this.init();
    }

    init() {
        this.initializeWebSocket();