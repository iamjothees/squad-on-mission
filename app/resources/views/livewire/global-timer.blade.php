<div class="fixed bottom-6 right-6 z-[9999]" wire:ignore>
    <div x-data="globalTimerData(@js($timerId), @js($initialState))" 
         
            @timer-switched.window="switchTimer($event.detail)"
         class="bg-gray-900 text-white shadow-xl rounded-full px-5 py-3 flex items-center gap-4 hover:shadow-2xl transition-all border border-gray-700">
        
        <!-- Timer Display -->
        <a :href="'/timers/' + activeTimerId" class="font-mono text-xl font-bold tracking-wider tabular-nums w-24 text-center hover:text-indigo-400 transition-colors" x-text="formattedTime" title="View Logs">
            00:00:00
        </a>

        <!-- Divider -->
        <div class="w-px h-6 bg-gray-700"></div>

        <div class="flex items-center gap-2">
            <!-- Play/Pause Button -->
            <button @click="toggle" 
                    class="w-10 h-10 rounded-full flex items-center justify-center transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-gray-400"
                    :class="isRunning ? 'bg-orange-500 hover:bg-orange-600 text-white' : 'bg-green-500 hover:bg-green-600 text-white'">
                
                <!-- Play Icon -->
                <x-lucide-play x-show="!isRunning" class="w-5 h-5 ml-0.5 fill-current" />
                
                <!-- Pause Icon -->
                <x-lucide-pause x-show="isRunning" class="w-5 h-5 fill-current" style="display: none;" />
            </button>

            <!-- Stop Button -->
            <button wire:click="stopTimer" 
                    x-show="formattedTime !== '00:00:00' || isRunning"
                    class="w-10 h-10 rounded-full flex items-center justify-center transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-gray-400 bg-red-500 hover:bg-red-600 text-white" style="display: none;">
                <x-lucide-square class="w-4 h-4 fill-current" />
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
            activeTimerId: timerId,

            
            switchTimer(detail) {
                this.destroy();
                let timerId = detail.timerId;
                this.activeTimerId = timerId;
                let initialState = detail.initialState;
                this.isRunning = initialState.is_running;
                this.initTimer(timerId, initialState);
            },
            
            initTimer(timerId, initialState) {
                this.timerInstance = new window.Timer(timerId, initialState, (seconds, isRunning) => {
                    this.isRunning = isRunning;
                    this.formatSeconds(seconds);
                });
            },

            init() {
                this.initTimer(timerId, initialState);
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
