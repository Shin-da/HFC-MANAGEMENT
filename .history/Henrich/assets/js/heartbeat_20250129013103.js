class HeartbeatMonitor {
    constructor(interval = 30000) { // 30 seconds
        this.interval = interval;
        this.init();
    }

    init() {
        // Send heartbeat immediately
        this.sendHeartbeat();
        
        // Set up regular heartbeat
        setInterval(() => this.sendHeartbeat(), this.interval);
        
        // Handle page visibility changes
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                this.sendHeartbeat();
            }
        });

        // Add before unload listener
        window.addEventListener('beforeunload', () => {
            this.markOffline();
        });
    }

    async sendHeartbeat() {
        try {
            const response = await fetch('/api/users/heartbeat.php');
            const data = await response.json();
            if (!data.success) {
                console.error('Heartbeat failed:', data.error);
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
