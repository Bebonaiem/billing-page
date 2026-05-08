import axios from 'axios';

// ─── Theme Management ───────────────────────────────────────────
const ThemeManager = {
    init() {
        this.setupThemeToggle();
        this.loadTheme();
        this.setupMediaQuery();
    },

    setupThemeToggle() {
        window.toggleTheme = () => {
            const currentTheme = localStorage.getItem('theme') || 'dark';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            this.setTheme(newTheme);
        };
    },

    setTheme(theme) {
        localStorage.setItem('theme', theme);
        const html = document.documentElement;
        
        // Add smooth transition class
        html.classList.add('theme-transition');
        
        if (theme === 'light') {
            html.setAttribute('data-theme', 'light');
            document.body.classList.remove('dark-theme');
            document.body.classList.add('light-theme');
        } else {
            html.setAttribute('data-theme', 'dark');
            document.body.classList.remove('light-theme');
            document.body.classList.add('dark-theme');
        }

        // Update icons visibility
        this.updateThemeIcons(theme);
        
        // Remove transition class after animation
        setTimeout(() => {
            html.classList.remove('theme-transition');
        }, 300);
    },

    loadTheme() {
        const preferredTheme = localStorage.getItem('theme') || 
            (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        this.setTheme(preferredTheme);
    },

    setupMediaQuery() {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem('theme')) {
                this.setTheme(e.matches ? 'dark' : 'light');
            }
        });
    },

    updateThemeIcons(theme) {
        const darkIcon = document.querySelector('.theme-icon-dark');
        const lightIcon = document.querySelector('.theme-icon-light');
        
        if (darkIcon && lightIcon) {
            if (theme === 'light') {
                darkIcon.style.display = 'block';
                lightIcon.style.display = 'none';
            } else {
                darkIcon.style.display = 'none';
                lightIcon.style.display = 'block';
            }
        }
    }
};

// ─── UI Improvements ────────────────────────────────────────────
const UIEnhancements = {
    init() {
        this.setupMobileMenu();
        this.setupLoadingStates();
        this.setupSmoothScrolling();
        this.setupFormEnhancements();
        this.setupNotificationBadge();
    },

    setupMobileMenu() {
        document.addEventListener('click', (e) => {
            if (e.target.closest('[x-toggle-mobile-menu]')) {
                const menu = document.querySelector('[x-mobile-menu]');
                if (menu) {
                    menu.classList.toggle('hidden');
                }
            }
        });
    },

    setupLoadingStates() {
        document.addEventListener('submit', (e) => {
            if (e.target.classList.contains('auto-loading')) {
                const submitBtn = e.target.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML += ' <span class="animate-spin">⟳</span>';
                }
            }
        });
    },

    setupSmoothScrolling() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', (e) => {
                e.preventDefault();
                const target = document.querySelector(anchor.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    },

    setupFormEnhancements() {
        document.querySelectorAll('input, textarea, select').forEach(input => {
            // Add focus effects
            input.addEventListener('focus', () => {
                input.parentElement.classList.add('focused');
            });

            input.addEventListener('blur', () => {
                input.parentElement.classList.remove('focused');
            });

            // Real-time validation feedback
            if (input.hasAttribute('required')) {
                input.addEventListener('change', () => {
                    if (input.value) {
                        input.classList.remove('border-error');
                        input.classList.add('border-success');
                    }
                });
            }
        });
    },

    setupNotificationBadge() {
        const badge = document.querySelector('.notification-badge');
        if (badge) {
            badge.addEventListener('animationend', () => {
                badge.classList.add('animate-pulse-ring');
            });
        }
    }
};

// ─── Accessibility Improvements ─────────────────────────────────
const A11y = {
    init() {
        this.setupKeyboardNavigation();
        this.setupAriaLabels();
        this.setupFocusTrap();
    },

    setupKeyboardNavigation() {
        document.addEventListener('keydown', (e) => {
            // Escape key closes modals
            if (e.key === 'Escape') {
                document.querySelectorAll('[role="dialog"]').forEach(dialog => {
                    if (dialog.classList.contains('visible')) {
                        dialog.classList.remove('visible');
                    }
                });
            }

            // Tab key focus management
            if (e.key === 'Tab') {
                document.body.classList.add('keyboard-nav');
            }
        });

        document.addEventListener('mousedown', () => {
            document.body.classList.remove('keyboard-nav');
        });
    },

    setupAriaLabels() {
        // Add missing aria-labels where needed
        document.querySelectorAll('button[title]').forEach(btn => {
            if (!btn.hasAttribute('aria-label')) {
                btn.setAttribute('aria-label', btn.getAttribute('title'));
            }
        });
    },

    setupFocusTrap() {
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                const modal = document.querySelector('[role="dialog"].visible');
                if (modal) {
                    const focusables = modal.querySelectorAll(
                        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
                    );
                    const firstElement = focusables[0];
                    const lastElement = focusables[focusables.length - 1];

                    if (e.shiftKey) {
                        if (document.activeElement === firstElement) {
                            lastElement.focus();
                            e.preventDefault();
                        }
                    } else {
                        if (document.activeElement === lastElement) {
                            firstElement.focus();
                            e.preventDefault();
                        }
                    }
                }
            }
        });
    }
};

// ─── Initialize Everything ──────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    ThemeManager.init();
    UIEnhancements.init();
    A11y.init();

    // Add animation to page load
    document.documentElement.classList.add('animate-fade-up');
});

// ─── Export for use in other modules ───────────────────────────
window.ThemeManager = ThemeManager;
window.UIEnhancements = UIEnhancements;
window.A11y = A11y;
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Enhanced JavaScript functionality for billing system
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips and popovers
    initializeTooltips();
    
    // Initialize magnetic buttons
    initializeMagneticButtons();
    
    // Initialize smooth scrolling
    initializeSmoothScroll();
    
    // Initialize form enhancements
    initializeFormEnhancements();
    
    // Initialize notifications system
    initializeNotifications();
});

function initializeTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', function(e) {
            showTooltip(e.target, e.target.dataset.tooltip);
        });
        
        element.addEventListener('mouseleave', function() {
            hideTooltip();
        });
    });
}

function showTooltip(element, text) {
    const tooltip = document.createElement('div');
    tooltip.className = 'absolute z-50 px-2 py-1 text-xs text-white bg-gray-900 rounded shadow-lg pointer-events-none';
    tooltip.textContent = text;
    tooltip.style.top = (element.offsetTop - 30) + 'px';
    tooltip.style.left = (element.offsetLeft + element.offsetWidth / 2 - 30) + 'px';
    tooltip.id = 'tooltip';
    document.body.appendChild(tooltip);
}

function hideTooltip() {
    const tooltip = document.getElementById('tooltip');
    if (tooltip) {
        tooltip.remove();
    }
}

function initializeMagneticButtons() {
    const magneticButtons = document.querySelectorAll('.magnetic-button');
    
    magneticButtons.forEach(button => {
        button.addEventListener('mousemove', function(e) {
            const rect = button.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;
            
            button.style.transform = `translate(${x * 0.2}px, ${y * 0.2}px) scale(1.05)`;
        });
        
        button.addEventListener('mouseleave', function() {
            button.style.transform = 'translate(0, 0) scale(1)';
        });
    });
}

function initializeSmoothScroll() {
    const links = document.querySelectorAll('a[href^="#"]');
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
}

function initializeFormEnhancements() {
    // Auto-save functionality
    const forms = document.querySelectorAll('form[data-auto-save]');
    forms.forEach(form => {
        let timeout;
        const inputs = form.querySelectorAll('input, textarea, select');
        
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    autoSaveForm(form);
                }, 1000);
            });
        });
    });
    
    // Form validation enhancements
    const requiredInputs = document.querySelectorAll('input[required], textarea[required]');
    requiredInputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
    });
}

function autoSaveForm(form) {
    const formData = new FormData(form);
    const url = form.dataset.autoSave;
    
    axios.post(url, formData)
        .then(response => {
            showNotification('Auto-saved', 'success');
        })
        .catch(error => {
            console.error('Auto-save failed:', error);
        });
}

function validateField(field) {
    const isValid = field.checkValidity();
    
    if (!isValid) {
        field.classList.add('border-red-500');
        field.classList.remove('border-green-500');
    } else {
        field.classList.add('border-green-500');
        field.classList.remove('border-red-500');
    }
}

function initializeNotifications() {
    // Notification container
    if (!document.getElementById('notification-container')) {
        const container = document.createElement('div');
        container.id = 'notification-container';
        container.className = 'fixed top-4 right-4 z-50 space-y-2';
        document.body.appendChild(container);
    }
}

window.showNotification = function(message, type = 'info') {
    const container = document.getElementById('notification-container');
    const notification = document.createElement('div');
    
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500'
    };
    
    notification.className = `${colors[type]} text-white px-4 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
    notification.textContent = message;
    
    container.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
        notification.classList.add('translate-x-0');
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
};

// Enhanced theme toggle with localStorage
window.toggleTheme = function() {
    const html = document.documentElement;
    const currentTheme = html.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    html.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    
    // Update theme toggle icon
    const themeIcon = document.querySelector('[onclick="toggleTheme()"] svg');
    if (themeIcon) {
        if (newTheme === 'light') {
            themeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>';
        } else {
            themeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0018 9a9.003 9.003 0 01-4.646 4.646z"></path>';
        }
    }
    
    showNotification(`Theme changed to ${newTheme}`, 'info');
};

// Load saved theme on page load
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme') || 'dark';
    document.documentElement.setAttribute('data-theme', savedTheme);
    
    // Update theme toggle icon based on saved theme
    const themeIcon = document.querySelector('[onclick="toggleTheme()"] svg');
    if (themeIcon && savedTheme === 'light') {
        themeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>';
    }
});

// Utility functions for common operations
window.formatCurrency = function(amount, currency = 'USD') {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency
    }).format(amount);
};

window.formatDate = function(date, format = 'short') {
    const options = {
        short: { year: 'numeric', month: 'short', day: 'numeric' },
        long: { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }
    };
    
    return new Date(date).toLocaleDateString('en-US', options[format] || options.short);
};

window.debounce = function(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
};
