/**
 * Vireo Designs - UI/UX Enhancement JavaScript
 * Interactive components and micro-interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Initialize all UI components
    initScrollProgress();
    initNotifications();
    initModals();
    initTabs();
    initAccordions();
    initTooltips();
    initSearch();
    initFormEnhancements();
    initAnimationObserver();
    initScrollToTop();
    
    /**
     * Scroll Progress Indicator
     */
    function initScrollProgress() {
        // Create scroll progress bar
        const progressContainer = document.createElement('div');
        progressContainer.className = 'scroll-indicator';
        
        const progressBar = document.createElement('div');
        progressBar.className = 'scroll-progress';
        
        progressContainer.appendChild(progressBar);
        document.body.appendChild(progressContainer);
        
        // Update progress on scroll
        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset;
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            const scrollPercent = (scrollTop / docHeight) * 100;
            
            progressBar.style.width = Math.min(scrollPercent, 100) + '%';
        });
    }
    
    /**
     * Enhanced Notifications System
     */
    function initNotifications() {
        window.VireoNotifications = {
            show: function(message, type = 'info', duration = 5000) {
                const notification = document.createElement('div');
                notification.className = `notification ${type}`;
                
                const header = document.createElement('div');
                header.className = 'notification-header';
                
                const title = document.createElement('h4');
                title.className = 'notification-title';
                title.textContent = this.getTitle(type);
                
                const closeBtn = document.createElement('button');
                closeBtn.className = 'notification-close';
                closeBtn.innerHTML = '×';
                closeBtn.addEventListener('click', () => this.hide(notification));
                
                header.appendChild(title);
                header.appendChild(closeBtn);
                
                const messageEl = document.createElement('p');
                messageEl.className = 'notification-message';
                messageEl.textContent = message;
                
                notification.appendChild(header);
                notification.appendChild(messageEl);
                
                document.body.appendChild(notification);
                
                // Show notification
                setTimeout(() => notification.classList.add('show'), 100);
                
                // Auto-hide
                if (duration > 0) {
                    setTimeout(() => this.hide(notification), duration);
                }
                
                return notification;
            },
            
            hide: function(notification) {
                notification.classList.remove('show');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            },
            
            getTitle: function(type) {
                const titles = {
                    success: 'Success!',
                    error: 'Error',
                    warning: 'Warning',
                    info: 'Info'
                };
                return titles[type] || 'Notification';
            }
        };
        
        // Example usage for testing
        window.showNotification = window.VireoNotifications.show.bind(window.VireoNotifications);
    }
    
    /**
     * Modal System
     */
    function initModals() {
        // Modal triggers
        document.addEventListener('click', function(e) {
            if (e.target.matches('[data-modal]')) {
                e.preventDefault();
                const modalId = e.target.getAttribute('data-modal');
                openModal(modalId);
            }
            
            if (e.target.matches('.modal-close') || e.target.matches('.modal-overlay')) {
                closeModal(e.target.closest('.modal-overlay'));
            }
        });
        
        // Escape key to close modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const openModal = document.querySelector('.modal-overlay.show');
                if (openModal) {
                    closeModal(openModal);
                }
            }
        });
        
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
        }
        
        function closeModal(modalOverlay) {
            modalOverlay.classList.remove('show');
            document.body.style.overflow = '';
        }
    }
    
    /**
     * Enhanced Tabs
     */
    function initTabs() {
        document.addEventListener('click', function(e) {
            if (e.target.matches('.tab-link')) {
                e.preventDefault();
                
                const tabsContainer = e.target.closest('.tabs');
                const targetPanel = e.target.getAttribute('href');
                
                // Remove active class from all tabs and panels
                tabsContainer.querySelectorAll('.tab-link').forEach(link => {
                    link.classList.remove('active');
                });
                
                tabsContainer.querySelectorAll('.tab-panel').forEach(panel => {
                    panel.classList.remove('active');
                });
                
                // Add active class to clicked tab and target panel
                e.target.classList.add('active');
                const panel = document.querySelector(targetPanel);
                if (panel) {
                    panel.classList.add('active');
                }
            }
        });
    }
    
    /**
     * Accordion Functionality
     */
    function initAccordions() {
        document.addEventListener('click', function(e) {
            if (e.target.matches('.accordion-header') || e.target.closest('.accordion-header')) {
                const header = e.target.matches('.accordion-header') ? e.target : e.target.closest('.accordion-header');
                const item = header.closest('.accordion-item');
                const content = item.querySelector('.accordion-content');
                
                // Toggle active state
                item.classList.toggle('active');
                
                // Animate height
                if (item.classList.contains('active')) {
                    const body = content.querySelector('.accordion-body');
                    content.style.maxHeight = body.scrollHeight + 'px';
                } else {
                    content.style.maxHeight = '0';
                }
            }
        });
    }
    
    /**
     * Enhanced Tooltips
     */
    function initTooltips() {
        // Add tooltip positioning logic for better placement
        document.addEventListener('mouseenter', function(e) {
            if (e.target.matches('.tooltip')) {
                const tooltip = e.target;
                const rect = tooltip.getBoundingClientRect();
                const tooltipText = tooltip.querySelector('::before');
                
                // Adjust position if tooltip would go off screen
                if (rect.left < 100) {
                    tooltip.style.setProperty('--tooltip-transform', 'translateX(0)');
                } else if (rect.right > window.innerWidth - 100) {
                    tooltip.style.setProperty('--tooltip-transform', 'translateX(-100%)');
                }
            }
        });
    }
    
    /**
     * Enhanced Search Functionality
     */
    function initSearch() {
        const searchInputs = document.querySelectorAll('.search-input');
        
        searchInputs.forEach(input => {
            const container = input.closest('.search-container');
            const resultsContainer = container.querySelector('.search-results');
            
            if (!resultsContainer) return;
            
            let searchTimeout;
            
            input.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();
                
                if (query.length < 2) {
                    resultsContainer.style.display = 'none';
                    return;
                }
                
                searchTimeout = setTimeout(() => {
                    performSearch(query, resultsContainer);
                }, 300);
            });
            
            // Hide results when clicking outside
            document.addEventListener('click', function(e) {
                if (!container.contains(e.target)) {
                    resultsContainer.style.display = 'none';
                }
            });
        });
        
        function performSearch(query, resultsContainer) {
            // Implement search logic here
            // For now, show placeholder results
            resultsContainer.innerHTML = `
                <div class="search-result">
                    <strong>Searching for:</strong> "${query}"
                </div>
                <div class="search-result">
                    No results found
                </div>
            `;
            resultsContainer.style.display = 'block';
        }
    }
    
    /**
     * Form Enhancements
     */
    function initFormEnhancements() {
        // Real-time validation
        document.addEventListener('input', function(e) {
            if (e.target.matches('.form-input')) {
                validateField(e.target);
            }
        });
        
        // Form submission with loading states
        document.addEventListener('submit', function(e) {
            if (e.target.matches('.enhanced-form')) {
                const submitBtn = e.target.querySelector('[type="submit"]');
                if (submitBtn) {
                    submitBtn.classList.add('loading');
                    submitBtn.disabled = true;
                    
                    // Re-enable after 3 seconds (adjust based on actual form processing)
                    setTimeout(() => {
                        submitBtn.classList.remove('loading');
                        submitBtn.disabled = false;
                    }, 3000);
                }
            }
        });
        
        function validateField(field) {
            const container = field.closest('.form-group');
            const errorElement = container.querySelector('.form-error');
            
            // Remove existing error styling
            field.classList.remove('error', 'success');
            if (errorElement) {
                errorElement.remove();
            }
            
            // Basic validation
            if (field.hasAttribute('required') && !field.value.trim()) {
                showFieldError(field, 'This field is required');
                return false;
            }
            
            if (field.type === 'email' && field.value && !isValidEmail(field.value)) {
                showFieldError(field, 'Please enter a valid email address');
                return false;
            }
            
            // Show success state
            field.classList.add('success');
            return true;
        }
        
        function showFieldError(field, message) {
            field.classList.add('error');
            
            const container = field.closest('.form-group');
            const errorElement = document.createElement('div');
            errorElement.className = 'form-error';
            errorElement.textContent = message;
            
            container.appendChild(errorElement);
        }
        
        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }
    }
    
    /**
     * Animation Observer for Scroll Animations
     */
    function initAnimationObserver() {
        const observerOptions = {
            root: null,
            rootMargin: '0px 0px -50px 0px',
            threshold: 0.1
        };
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const element = entry.target;
                    
                    // Add appropriate animation class
                    if (element.hasAttribute('data-animate')) {
                        const animationType = element.getAttribute('data-animate');
                        element.classList.add(animationType);
                    } else {
                        element.classList.add('fade-in');
                    }
                    
                    // Stop observing this element
                    observer.unobserve(element);
                }
            });
        }, observerOptions);
        
        // Observe elements with animation triggers
        document.querySelectorAll('[data-animate], .animate-on-scroll').forEach(element => {
            observer.observe(element);
        });
    }
    
    /**
     * Enhanced Scroll to Top
     */
    function initScrollToTop() {
        const backToTop = document.querySelector('.back-to-top');
        
        if (backToTop) {
            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    backToTop.classList.add('visible');
                } else {
                    backToTop.classList.remove('visible');
                }
            });
            
            backToTop.addEventListener('click', function(e) {
                e.preventDefault();
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }
    }
    
    /**
     * Loading State Utilities
     */
    window.VireoLoading = {
        show: function(element) {
            element.classList.add('loading');
        },
        
        hide: function(element) {
            element.classList.remove('loading');
        }
    };
    
    /**
     * Utility Functions
     */
    window.VireoUtils = {
        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },
        
        throttle: function(func, limit) {
            let inThrottle;
            return function() {
                const args = arguments;
                const context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            }
        },
        
        animate: function(element, animation, duration = 600) {
            return new Promise(resolve => {
                element.style.animationDuration = duration + 'ms';
                element.classList.add(animation);
                
                setTimeout(() => {
                    element.classList.remove(animation);
                    resolve();
                }, duration);
            });
        }
    };
    
    // Global event for custom components
    document.dispatchEvent(new CustomEvent('vireoUIReady', {
        detail: {
            notifications: window.VireoNotifications,
            loading: window.VireoLoading,
            utils: window.VireoUtils
        }
    }));
});

/**
 * Smooth scrolling for anchor links
 */
document.addEventListener('click', function(e) {
    if (e.target.matches('a[href^="#"]')) {
        e.preventDefault();
        const targetId = e.target.getAttribute('href');
        const targetElement = document.querySelector(targetId);
        
        if (targetElement) {
            targetElement.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }
});

/**
 * Copy to Clipboard Utility
 */
function copyToClipboard(text) {
    if (navigator.clipboard) {
        return navigator.clipboard.writeText(text).then(() => {
            if (window.VireoNotifications) {
                window.VireoNotifications.show('Copied to clipboard!', 'success', 2000);
            }
        });
    } else {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        
        if (window.VireoNotifications) {
            window.VireoNotifications.show('Copied to clipboard!', 'success', 2000);
        }
    }
}

// Make copy function globally available
window.copyToClipboard = copyToClipboard;