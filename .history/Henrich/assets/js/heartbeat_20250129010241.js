class HeartbeatMonitor {
    constructor(interval = 30000) { // 30 seconds
        this.interval = interval;
        this.heartbeatTimer = null;
        this.init();
    }

    init() {
        // Start heartbeat when page loads
        this.startHeartbeat();
        
        // Add page visibility change listener
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.stopHeartbeat();
            } else {
                this.startHeartbeat();
            }
        });

        // Add before unload listener
        window.addEventListener('beforeunload', () => {
            this.markOffline();
        });
    }

    async startHeartbeat() {
        // Clear any existing timer
        if (this.heartbeatTimer) {
            clearInterval(this.heartbeatTimer);
        }

        // Start new heartbeat interval
        this.heartbeatTimer = setInterval(() => this.sendHeartbeat(), this.interval);
        
        // Send immediate heartbeat
        await this.sendHeartbeat();
    }

    stopHeartbeat() {
        if (this.heartbeatTimer) {
            clearInterval(this.heartbeatTimer);
            this.heartbeatTimer = null;
        }
    }

    async sendHeartbeat() {
        try {
            const response = await fetch('/api/users/heartbeat.php', {
                method: 'POST',
                credentials: 'same-origin'
            });
            
            if (!response.ok) {
                throw new Error('Heartbeat failed');
            }
        } catch (error) {
            console.error('Heartbeat error:', error);
        }
    }

    async markOffline() {
        try {
            await fetch('/api/users/offline.php', {
                method: 'POST',
                credentials: 'same-origin'
            });
        } catch (error) {
            console.error('Error marking user offline:', error);
        }
    }
}

// Initialize heartbeat monitor when document loads
document.addEventListener('DOMContentLoaded', () => {
    window.heartbeatMonitor = new HeartbeatMonitor();
});
