<!-- Modern Notification System -->
<div x-data="notificationSystem" x-init="init()" class="fixed top-4 right-4 z-50 space-y-2">
    <template x-for="notification in notifications" :key="notification.id">
        <div x-show="notification.visible" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-full"
             x-transition:enter-end="opacity-100 transform translate-x-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform translate-x-0"
             x-transition:leave-end="opacity-0 transform translate-x-full"
             :class="getNotificationClasses(notification.type)"
             class="glass-effect-3d rounded-xl p-4 min-w-[320px] max-w-md shadow-glow animate-bounce-in">
            
            <!-- Notification Header -->
            <div class="flex items-start justify-between mb-2">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center mr-3" 
                         :class="getIconClasses(notification.type)">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  :d="getIconPath(notification.type)"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-white text-sm" x-text="notification.title"></h4>
                        <p class="text-xs text-secondary" x-text="notification.time"></p>
                    </div>
                </div>
                <button @click="dismiss(notification.id)" 
                        class="text-secondary hover:text-white transition-colors duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Notification Content -->
            <p class="text-sm text-white mb-3" x-text="notification.message"></p>
            
            <!-- Notification Actions -->
            <div x-show="notification.actions" class="flex gap-2">
                <template x-for="action in notification.actions" :key="action.id">
                    <button @click="handleAction(notification.id, action)" 
                            :class="getActionClasses(action.type)"
                            class="px-3 py-1 rounded-lg text-xs font-medium transition-all duration-200">
                        <span x-text="action.label"></span>
                    </button>
                </template>
            </div>
            
            <!-- Progress Bar (for progress notifications) -->
            <div x-show="notification.progress" class="mt-3">
                <div class="w-full bg-card/30 rounded-full h-1">
                    <div class="bg-accent h-1 rounded-full transition-all duration-300 animate-pulse"
                         :style="`width: ${notification.progress}%`"></div>
                </div>
                <p class="text-xs text-secondary mt-1">
                    <span x-text="notification.progress"></span>% Complete
                </p>
            </div>
        </div>
    </template>
</div>

<script>
function notificationSystem() {
    return {
        notifications: [],
        notificationId: 0,
        
        init() {
            // Listen for Livewire events
            window.addEventListener('notification', (event) => {
                this.add(event.detail);
            });
            
            // Listen for custom events
            window.addEventListener('success', (event) => {
                this.success(event.detail.message, event.detail.title);
            });
            
            window.addEventListener('error', (event) => {
                this.error(event.detail.message, event.detail.title);
            });
            
            window.addEventListener('warning', (event) => {
                this.warning(event.detail.message, event.detail.title);
            });
            
            window.addEventListener('info', (event) => {
                this.info(event.detail.message, event.detail.title);
            });
        },
        
        add(notification) {
            const id = ++this.notificationId;
            const newNotification = {
                id,
                ...notification,
                visible: false,
                time: new Date().toLocaleTimeString()
            };
            
            this.notifications.push(newNotification);
            
            // Trigger animation
            setTimeout(() => {
                newNotification.visible = true;
            }, 10);
            
            // Auto dismiss after duration
            if (notification.duration !== false) {
                setTimeout(() => {
                    this.dismiss(id);
                }, notification.duration || 5000);
            }
        },
        
        success(message, title = 'Success') {
            this.add({
                type: 'success',
                title,
                message,
                duration: 4000
            });
        },
        
        error(message, title = 'Error') {
            this.add({
                type: 'error',
                title,
                message,
                duration: 6000
            });
        },
        
        warning(message, title = 'Warning') {
            this.add({
                type: 'warning',
                title,
                message,
                duration: 5000
            });
        },
        
        info(message, title = 'Info') {
            this.add({
                type: 'info',
                title,
                message,
                duration: 4000
            });
        },
        
        progress(message, progress, title = 'Progress') {
            const existing = this.notifications.find(n => n.type === 'progress' && !n.complete);
            
            if (existing) {
                existing.message = message;
                existing.progress = progress;
                
                if (progress >= 100) {
                    existing.complete = true;
                    setTimeout(() => this.dismiss(existing.id), 2000);
                }
            } else {
                this.add({
                    type: 'progress',
                    title,
                    message,
                    progress,
                    duration: false
                });
            }
        },
        
        dismiss(id) {
            const notification = this.notifications.find(n => n.id === id);
            if (notification) {
                notification.visible = false;
                setTimeout(() => {
                    this.notifications = this.notifications.filter(n => n.id !== id);
                }, 300);
            }
        },
        
        handleAction(notificationId, action) {
            if (action.handler) {
                action.handler();
            }
            
            if (action.dismissOnClick !== false) {
                this.dismiss(notificationId);
            }
        },
        
        getNotificationClasses(type) {
            const baseClasses = 'border-l-4';
            const typeClasses = {
                success: 'border-success/50 bg-success/10',
                error: 'border-error/50 bg-error/10',
                warning: 'border-warning/50 bg-warning/10',
                info: 'border-accent/50 bg-accent/10',
                progress: 'border-accent/50 bg-accent/10'
            };
            
            return `${baseClasses} ${typeClasses[type] || typeClasses.info}`;
        },
        
        getIconClasses(type) {
            const typeClasses = {
                success: 'success-bg',
                error: 'error-bg',
                warning: 'warning-bg',
                info: 'accent-bg',
                progress: 'accent-bg'
            };
            
            return typeClasses[type] || typeClasses.info;
        },
        
        getIconPath(type) {
            const paths = {
                success: 'M5 13l4 4L19 7',
                error: 'M6 18L18 6M6 6l12 12',
                warning: 'M12 9v2m0 4h.01m-6.938 4h.138M12 9v2m0 4h.01m-6.938 4h.138',
                info: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                progress: 'M13 10V3L4 14h7v7m9 0v-7h-7v7m-7 0l7-7v7'
            };
            
            return paths[type] || paths.info;
        },
        
        getActionClasses(type) {
            const typeClasses = {
                primary: 'accent-bg text-white hover:opacity-90',
                secondary: 'bg-card/50 text-white hover:bg-card/70',
                danger: 'error-bg text-white hover:opacity-90'
            };
            
            return typeClasses[type] || typeClasses.secondary;
        }
    };
}

// Global notification helper
window.showNotification = function(type, message, title) {
    window.dispatchEvent(new CustomEvent(type, { 
        detail: { message, title } 
    }));
};
</script>
