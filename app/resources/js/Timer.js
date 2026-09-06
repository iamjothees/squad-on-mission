export default class Timer {
    constructor(timerId, initialState, onTick) {
        this.timerId = timerId;
        
        // State
        this.accumulated_seconds = initialState.accumulated_seconds || 0;
        this.is_running = initialState.is_running || false;
        this.last_started_at = initialState.last_started_at || null;
        
        // Callbacks
        this.onTick = onTick; // Called every second to update UI
        
        // Internal
        this.uiInterval = null;
        this.broadcastChannel = new BroadcastChannel('timer_sync_' + this.timerId);
        
        // Listen for cross-tab events
        this.broadcastChannel.onmessage = (event) => {
            this.syncState(event.data, false);
        };

        // Listen for cross-device events (assuming window.Echo is available)
        if (window.Echo) {
            window.Echo.channel('timers.' + this.timerId)
                .listen('.TimerUpdated', (e) => {
                    this.syncState(e, false);
                });
        }
        
        this.startUITicker();
    }

    // Sync state received from server or other tabs
    syncState(newState, broadcast = true) {
        this.accumulated_seconds = newState.accumulated_seconds;
        this.is_running = newState.is_running;
        this.last_started_at = newState.last_started_at;
        
        if (broadcast) {
            this.broadcastChannel.postMessage({
                accumulated_seconds: this.accumulated_seconds,
                is_running: this.is_running,
                last_started_at: this.last_started_at
            });
        }
        
        // Force an immediate UI tick
        this.tick();
    }

    // Calculate precise current time
    getCurrentSeconds() {
        let currentSeconds = this.accumulated_seconds;
        
        if (this.is_running && this.last_started_at) {
            const elapsed = Math.floor((Date.now() - this.last_started_at) / 1000);
            currentSeconds += elapsed;
        }
        
        return currentSeconds;
    }

    // Toggle logic (called only on user interaction)
    async toggle() {
        if (this.is_running) {
            // Pause
            const elapsed = Math.floor((Date.now() - this.last_started_at) / 1000);
            this.accumulated_seconds += elapsed;
            this.is_running = false;
            this.last_started_at = null;
        } else {
            // Play
            this.is_running = true;
            this.last_started_at = Date.now();
        }

        // Instantly update local state and broadcast to other tabs
        this.syncState({
            accumulated_seconds: this.accumulated_seconds,
            is_running: this.is_running,
            last_started_at: this.last_started_at
        }, true);

        // Persist to server (no polling, only on action)
        try {
            await fetch(`/api/timers/${this.timerId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    // Assuming CSRF token is handled, e.g. via meta tag
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify({
                    accumulated_seconds: this.accumulated_seconds,
                    is_running: this.is_running,
                    last_started_at: this.last_started_at
                })
            });
        } catch (error) {
            console.error('Failed to sync timer state with server:', error);
        }
    }

    async stop() {
        this.accumulated_seconds = 0;
        this.is_running = false;
        this.last_started_at = null;

        this.syncState({
            accumulated_seconds: this.accumulated_seconds,
            is_running: this.is_running,
            last_started_at: this.last_started_at
        }, true);

        try {
            await fetch(`/api/timers/${this.timerId}/stop`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });
        } catch (error) {
            console.error('Failed to sync timer state with server:', error);
        }
    }

    // UI Ticker only reads the state, never mutates it
    startUITicker() {
        if (this.uiInterval) clearInterval(this.uiInterval);
        
        // Trigger initial tick immediately
        this.tick();
        
        this.uiInterval = setInterval(() => {
            this.tick();
        }, 1000);
    }
    
    tick() {
        if (typeof this.onTick === 'function') {
            this.onTick(this.getCurrentSeconds(), this.is_running);
        }
    }
    
    // Cleanup if timer is destroyed (SPA navigation)
    destroy() {
        if (this.uiInterval) clearInterval(this.uiInterval);
        this.broadcastChannel.close();
        if (window.Echo) {
            window.Echo.leave('timers.' + this.timerId);
        }
    }
}
