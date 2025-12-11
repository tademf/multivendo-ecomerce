import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// CSRF Token
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found');
}

// Global event bus (simple implementation)
window.EventBus = {
    events: {},
    
    emit(event, data = null) {
        if (this.events[event]) {
            this.events[event].forEach(callback => callback(data));
        }
    },
    
    on(event, callback) {
        if (!this.events[event]) {
            this.events[event] = [];
        }
        this.events[event].push(callback);
    },
    
    off(event, callback) {
        if (this.events[event]) {
            this.events[event] = this.events[event].filter(cb => cb !== callback);
        }
    }
};

// Global helper functions
window.helpers = {
    formatPrice(price) {
        return '$' + parseFloat(price).toFixed(2);
    },
    
    truncate(text, length = 100) {
        if (text.length <= length) return text;
        return text.substring(0, length) + '...';
    },
    
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('E-Commerce App Loaded');
    
    // Set loading state
    const appElement = document.getElementById('app');
    if (appElement) {
        appElement.classList.add('page-enter-active');
    }
});