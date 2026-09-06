<div class="fixed bottom-6 right-6 z-[9999]" wire:ignore>
    <div x-data="globalTimerData(@js($timerId), @js($initialState))" 
         class="bg-gray-900 text-white shadow-xl rounded-full px-5 py-3 flex items-center gap-4 hover:shadow-2xl transition-all border border-gray-700">
        
        <!-- Timer Display -->
        <div class="font-mono text-xl font-bold tracking-wider tabular-nums w-24 text-center" x-text="formattedTime">
            00:00:00
        </div>

        <!-- Divider -->
        <div class="w-px h-6 bg-gray-700"></div>

        <div class="flex items-center gap-2">
            <!-- Play/Pause Button -->
            <button @click="toggle" 
                    class="w-10 h-10 rounded-full flex items-center justify-center transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-gray-400"
                    :class="isRunning ? 'bg-orange-500 hover:bg-orange-600 text-white' : 'bg-green-500 hover:bg-green-600 text-white'">
                
                <!-- Play Icon -->
                <svg x-show="!isRunning" class="w-5 h-5 ml-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"></path>
                </svg>
                
                <!-- Pause Icon -->
                <svg x-show="isRunning" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" style="display: none;">
                    <path d="M5 4h3v12H5V4zm7 0h3v12h-3V4z"></path>
                </svg>
            </button>

            <!-- Stop Button -->
            <button @click="stop" 
                    x-show="formattedTime !== '00:00:00' || isRunning"
                    class="w-10 h-10 rounded-full flex items-center justify-center transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-gray-400 bg-red-500 hover:bg-red-600 text-white" style="display: none;">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <rect x="5" y="5" width="10" height="10"></rect>
                </svg>
            </button>
        </div>
    </div>
</div>

@once
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('globalTimerData', (timerId, initialState) => ({
            timerInstance: null,
            formattedTime: '00:00:00',
            isRunning: initialState.is_running,

            init() {
                // Initialize the robust vanilla JS class we created
                this.timerInstance = new window.Timer(timerId, initialState, (seconds, isRunning) => {
                    this.isRunning = isRunning;
                    this.formatSeconds(seconds);
                });
            },

            formatSeconds(totalSeconds) {
                const hours = Math.floor(totalSeconds / 3600);
                const minutes = Math.floor((totalSeconds % 3600) / 60);
                const seconds = totalSeconds % 60;
                
                this.formattedTime = [hours, minutes, seconds]
                    .map(v => v < 10 ? "0" + v : v)
                    .join(":");
            },

            toggle() {
                this.timerInstance.toggle();
                this.isRunning = this.timerInstance.is_running;
            },

            stop() {
                this.timerInstance.stop();
                this.isRunning = false;
                this.formatSeconds(0);
            },

            destroy() {
                if (this.timerInstance) {
                    this.timerInstance.destroy();
                }
            }
        }));
    });
</script>
@endonce
