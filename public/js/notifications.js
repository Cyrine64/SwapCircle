/**
 * SwapCircle Real-time Notifications
 * Handles Mercure notifications for the SwapCircle back office
 */

class NotificationsManager {
    constructor() {
        this.notificationCount = 0;
        this.notifications = [];
        this.notificationBadge = document.querySelector('.icon-badge');
        this.notificationsList = document.querySelector('.notifications-list');
        this.setupMercureConnection();
    }

    // Setup Mercure connection for real-time updates
    setupMercureConnection() {
        const url = new URL('http://localhost:3000/.well-known/mercure');
        url.searchParams.append('topic', 'https://swapcircle.com/notifications');
        
        try {
            const eventSource = new EventSource(url);
            
            eventSource.onmessage = (event) => {
                try {
                    const data = JSON.parse(event.data);
                    this.addNotification(data);
                } catch (err) {
                    console.error('Error processing message:', err);
                }
            };
            
            eventSource.onerror = (err) => {
                console.error('Mercure EventSource error:', err);
                // Close the current connection
                eventSource.close();
                
                // Wait 5 seconds and try to reconnect
                setTimeout(() => {
                    console.log('Attempting to reconnect to Mercure...');
                    this.setupMercureConnection();
                }, 5000);
            };
            
            // Add an event listener for when the window comes back online
            window.addEventListener('online', () => {
                console.log('Back online, reconnecting to Mercure...');
                eventSource.close();
                this.setupMercureConnection();
            });
            
        } catch (err) {
            console.error('Error setting up Mercure connection:', err);
            // If we fail to setup the connection, try again in 10 seconds
            setTimeout(() => {
                this.setupMercureConnection();
            }, 10000);
        }
    }

    // Add a new notification
    addNotification(notification) {
        try {
            // Validate the notification object
            if (!notification || typeof notification !== 'object') {
                console.error('Invalid notification received:', notification);
                return;
            }

            // Ensure required properties exist
            if (!notification.type || !notification.message) {
                console.error('Notification missing required fields:', notification);
                return;
            }
            
            // Insert at the beginning
            this.notifications.unshift(notification);
            
            // Keep only the latest 10 notifications
            if (this.notifications.length > 10) {
                this.notifications = this.notifications.slice(0, 10);
            }
            
            this.notificationCount++;
            this.updateNotificationBadge();
            this.updateNotificationList();
            
            // Show a toast notification
            this.showToast(notification);
        } catch (err) {
            console.error('Error adding notification:', err, notification);
        }
    }

    // Update the notification badge count
    updateNotificationBadge() {
        if (this.notificationBadge) {
            this.notificationBadge.textContent = this.notificationCount > 99 ? '99+' : this.notificationCount;
            
            // Show/hide badge based on count
            if (this.notificationCount > 0) {
                this.notificationBadge.classList.remove('d-none');
            } else {
                this.notificationBadge.classList.add('d-none');
            }
        }
    }

    // Update the notifications dropdown list
    updateNotificationList() {
        if (this.notificationsList) {
            // Clear existing notifications
            this.notificationsList.innerHTML = '';
            
            if (this.notifications.length === 0) {
                const emptyItem = document.createElement('div');
                emptyItem.className = 'dropdown-item';
                emptyItem.innerHTML = '<div class="notification-item p-3">Aucune notification</div>';
                this.notificationsList.appendChild(emptyItem);
                return;
            }
            
            // Add notifications to the list
            this.notifications.forEach(notification => {
                const item = document.createElement('div');
                item.className = 'dropdown-item';
                
                // Format the date
                const date = new Date(notification.timestamp);
                const formattedDate = date.toLocaleDateString('fr-FR', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                
                // Determine icon based on notification type
                let icon = 'bi-bell';
                let iconClass = 'bg-primary';
                
                if (notification.type === 'new_object') {
                    icon = 'bi-box-seam';
                    iconClass = 'bg-success';
                } else if (notification.type === 'new_exchange') {
                    icon = 'bi-arrow-left-right';
                    iconClass = 'bg-warning';
                } else if (notification.type === 'exchange_accepted') {
                    icon = 'bi-check-circle';
                    iconClass = 'bg-success';
                } else if (notification.type === 'exchange_refused') {
                    icon = 'bi-x-circle';
                    iconClass = 'bg-danger';
                }
                
                item.innerHTML = `
                    <div class="notification-item p-3">
                        <div class="row gx-2">
                            <div class="col-auto">
                                <div class="notification-icon ${iconClass}">
                                    <i class="bi ${icon}"></i>
                                </div>
                            </div>
                            <div class="col">
                                <div class="notification-content">
                                    <div class="notification-title">${notification.message}</div>
                                    <div class="notification-meta">${formattedDate}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                this.notificationsList.appendChild(item);
            });
            
            // Add read all button
            const markAllReadItem = document.createElement('div');
            markAllReadItem.className = 'dropdown-item text-center';
            markAllReadItem.innerHTML = '<button id="mark-all-read" class="btn btn-sm btn-light"><i class="bi bi-check-all me-1"></i>Marquer tout comme lu</button>';
            this.notificationsList.appendChild(markAllReadItem);
            
            // Add event listener for mark all as read
            const markAllReadButton = markAllReadItem.querySelector('#mark-all-read');
            if (markAllReadButton) {
                markAllReadButton.addEventListener('click', () => {
                    this.clearAllNotifications();
                });
            }
        }
    }

    // Clear all notifications
    clearAllNotifications() {
        this.notifications = [];
        this.notificationCount = 0;
        this.updateNotificationBadge();
        this.updateNotificationList();
    }

    // Show a toast notification
    showToast(notification) {
        // Create a toast container if it doesn't exist
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            document.body.appendChild(toastContainer);
        }
        
        // Create the toast
        const toastId = 'toast-' + new Date().getTime();
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.id = toastId;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        let iconClass = 'bg-primary';
        let icon = 'bi-bell';
        
        if (notification.type === 'new_object') {
            icon = 'bi-box-seam';
            iconClass = 'bg-success';
        } else if (notification.type === 'new_exchange') {
            icon = 'bi-arrow-left-right';
            iconClass = 'bg-warning';
        } else if (notification.type === 'exchange_accepted') {
            icon = 'bi-check-circle';
            iconClass = 'bg-success';
        } else if (notification.type === 'exchange_refused') {
            icon = 'bi-x-circle';
            iconClass = 'bg-danger';
        }
        
        toast.innerHTML = `
            <div class="toast-header">
                <div class="notification-icon-small ${iconClass} me-2">
                    <i class="bi ${icon}"></i>
                </div>
                <strong class="me-auto">SwapCircle</strong>
                <small>À l'instant</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                ${notification.message}
            </div>
        `;
        
        toastContainer.appendChild(toast);
        
        // Initialize the Bootstrap toast
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        // Remove toast after it's hidden
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }
}

// Initialize the notifications manager when the DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    const notificationsManager = new NotificationsManager();
});
