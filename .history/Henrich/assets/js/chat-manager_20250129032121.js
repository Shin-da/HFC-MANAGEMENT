class ChatManager {
    constructor() {
        this.socket = null;
        this.messageContainer = document.querySelector('.chat-messages');
        this.onlineUsersInterval = null;
        this.activeTab = 'online';
        this.init();
        this.setupUnloadHandler();
    }

    // Move all the methods from chat.js to here
    // ...existing ChatManager methods...
}
