/**
 * UI Enhancement JavaScript for Vireo Designs Theme
 * 
 * Modern UI interactions and animations
 */

(function() {
    'use strict';

    // UI Enhancement module
    const VireoUI = {
        init() {
            this.setupSmoothScrolling();
            this.setupParallaxEffects();
            this.setupIntersectionObserver();
            this.setupModalHandlers();
            this.setupTooltips();
            this.setupFormEnhancements();
            this.setupAnimations();
        },

        setupSmoothScrolling() {
            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    const targetId = this.getAttribute('href');
                    const targetElement = document.querySelector(targetId);
                    
                    if (targetElement && targetId !== '#') {
                        e.preventDefault();
                        
                        targetElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                        
                        // Update URL without jumping
                        history.pushState(null, null, targetId);
                    }
                });
            });
        },

        setupParallaxEffects() {
            // Simple parallax effect for hero sections
            const parallaxElements = document.querySelectorAll('.parallax-element');
            
            if (parallaxElements.length && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                window.addEventListener('scroll', () => {
                    const scrolled = window.pageYOffset;
                    
                    parallaxElements.forEach(element => {
                        const speed = element.dataset.speed || 0.5;
                        const translateY = scrolled * speed;
                        element.style.transform = `translateY(${translateY}px)`;
                    });
                });
            }
        },

        setupIntersectionObserver() {
            // Fade in animations on scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            // Observe elements with fade-in-up class
            document.querySelectorAll('.fade-in-up, .animate-on-scroll').forEach(el => {
                observer.observe(el);
            });

            // Lazy load images
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.classList.add('loaded');
                            imageObserver.unobserve(img);
                        }
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        },

        setupModalHandlers() {
            // Modal functionality
            document.addEventListener('click', (e) => {
                // Open modal
                if (e.target.matches('[data-modal-trigger]')) {
                    e.preventDefault();
                    const modalId = e.target.getAttribute('data-modal-trigger');
                    this.openModal(modalId);
                }
                
                // Close modal
                if (e.target.matches('[data-modal-close]') || 
                    e.target.matches('.modal-backdrop')) {
                    this.closeModal();
                }
            });

            // Close modal with Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.closeModal();
                }
            });
        },

        openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                document.body.style.overflow = 'hidden';
                modal.classList.add('active');
                
                // Focus management
                const focusableElements = modal.querySelectorAll(
                    'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
                );
                if (focusableElements.length) {
                    focusableElements[0].focus();
                }
            }
        },

        closeModal() {
            const activeModal = document.querySelector('.modal.active');
            if (activeModal) {
                document.body.style.overflow = '';
                activeModal.classList.remove('active');
            }
        },

        setupTooltips() {
            // Simple tooltip functionality
            document.querySelectorAll('[data-tooltip]').forEach(element => {
                let tooltip;
                
                element.addEventListener('mouseenter', function() {
                    const text = this.getAttribute('data-tooltip');
                    const position = this.getAttribute('data-tooltip-position') || 'top';
                    
                    tooltip = document.createElement('div');
                    tooltip.className = `tooltip tooltip-${position}`;
                    tooltip.textContent = text;
                    document.body.appendChild(tooltip);
                    
                    const rect = this.getBoundingClientRect();
                    const tooltipRect = tooltip.getBoundingClientRect();
                    
                    let left = rect.left + (rect.width / 2) - (tooltipRect.width / 2);
                    let top = rect.top - tooltipRect.height - 10;
                    
                    if (position === 'bottom') {
                        top = rect.bottom + 10;
                    }
                    
                    tooltip.style.left = Math.max(10, left) + 'px';
                    tooltip.style.top = top + 'px';
                    tooltip.classList.add('visible');
                });
                
                element.addEventListener('mouseleave', function() {
                    if (tooltip) {
                        tooltip.remove();
                        tooltip = null;
                    }
                });
            });
        },

        setupFormEnhancements() {
            // Enhanced form interactions
            document.querySelectorAll('.enhanced-form').forEach(form => {
                // Floating labels
                form.querySelectorAll('input, textarea').forEach(input => {
                    const wrapper = input.closest('.form-group');
                    if (!wrapper) return;
                    
                    const updateLabel = () => {
                        if (input.value || input === document.activeElement) {
                            wrapper.classList.add('has-value');
                        } else {
                            wrapper.classList.remove('has-value');
                        }
                    };
                    
                    input.addEventListener('focus', updateLabel);
                    input.addEventListener('blur', updateLabel);
                    input.addEventListener('input', updateLabel);
                    
                    // Initial check
                    updateLabel();
                });
                
                // Form validation
                form.addEventListener('submit', function(e) {
                    let isValid = true;
                    
                    this.querySelectorAll('[required]').forEach(field => {
                        if (!field.value.trim()) {
                            isValid = false;
                            field.classList.add('error');
                            
                            // Remove error class after user starts typing
                            field.addEventListener('input', function() {
                                this.classList.remove('error');
                            }, { once: true });
                        }
                    });
                    
                    if (!isValid) {
                        e.preventDefault();
                        this.classList.add('has-errors');
                    }
                });
            });
        },

        setupAnimations() {
            // CSS animation utilities
            const animateElements = document.querySelectorAll('[data-animate]');
            
            animateElements.forEach(element => {
                const animation = element.getAttribute('data-animate');
                const delay = element.getAttribute('data-animate-delay') || 0;
                const duration = element.getAttribute('data-animate-duration') || 300;
                
                setTimeout(() => {
                    element.style.animationDuration = `${duration}ms`;
                    element.classList.add(`animate-${animation}`);
                }, delay);
            });
        },

        // Utility methods
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
        },

        throttle(func, limit) {
            let inThrottle;
            return function() {
                const args = arguments;
                const context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        }
    };

    // Initialize when DOM is loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => VireoUI.init());
    } else {
        VireoUI.init();
    }

    // Expose to global scope
    window.VireoUI = VireoUI;

})();