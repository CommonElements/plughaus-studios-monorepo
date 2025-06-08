/**
 * PlugHaus Studios - Header & Footer JavaScript
 * Enhanced interactions and functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Announcement Bar Close
    const announcementClose = document.querySelector('.announcement-close');
    const announcementBar = document.querySelector('.announcement-bar');
    
    if (announcementClose && announcementBar) {
        announcementClose.addEventListener('click', function() {
            announcementBar.style.transform = 'translateY(-100%)';
            announcementBar.style.opacity = '0';
            setTimeout(() => {
                announcementBar.style.display = 'none';
            }, 300);
            
            // Store in localStorage to remember choice
            localStorage.setItem('plughaus_announcement_closed', 'true');
        });
        
        // Check if user previously closed announcement
        if (localStorage.getItem('plughaus_announcement_closed') === 'true') {
            announcementBar.style.display = 'none';
        }
    }
    
    // Mobile Menu Toggle
    const mobileToggle = document.querySelector('.mobile-menu-toggle');
    const navContainer = document.querySelector('.nav-menu-container');
    
    if (mobileToggle) {
        mobileToggle.addEventListener('click', function() {
            const isExpanded = mobileToggle.getAttribute('aria-expanded') === 'true';
            
            mobileToggle.setAttribute('aria-expanded', !isExpanded);
            
            if (navContainer) {
                navContainer.classList.toggle('mobile-open');
            }
            
            // Animate hamburger
            mobileToggle.classList.toggle('active');
        });
    }
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
            
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Header scroll effect
    const header = document.querySelector('.site-header');
    let lastScrollY = window.scrollY;
    
    function updateHeader() {
        const currentScrollY = window.scrollY;
        
        if (header) {
            if (currentScrollY > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
            
            // Hide header on scroll down, show on scroll up
            if (currentScrollY > lastScrollY && currentScrollY > 200) {
                header.style.transform = 'translateY(-100%)';
            } else {
                header.style.transform = 'translateY(0)';
            }
        }
        
        lastScrollY = currentScrollY;
    }
    
    window.addEventListener('scroll', updateHeader, { passive: true });
    
    // Back to top button
    const backToTop = document.querySelector('.back-to-top');
    
    if (backToTop) {
        function toggleBackToTop() {
            if (window.scrollY > 300) {
                backToTop.classList.add('visible');
            } else {
                backToTop.classList.remove('visible');
            }
        }
        
        window.addEventListener('scroll', toggleBackToTop, { passive: true });
        
        backToTop.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    // Dropdown menu interactions
    const dropdowns = document.querySelectorAll('.dropdown');
    
    dropdowns.forEach(dropdown => {
        const menu = dropdown.querySelector('.dropdown-menu');
        let timeout;
        
        if (menu) {
            dropdown.addEventListener('mouseenter', function() {
                clearTimeout(timeout);
                menu.style.opacity = '1';
                menu.style.visibility = 'visible';
                menu.style.transform = 'translateY(0)';
            });
            
            dropdown.addEventListener('mouseleave', function() {
                timeout = setTimeout(() => {
                    menu.style.opacity = '0';
                    menu.style.visibility = 'hidden';
                    menu.style.transform = 'translateY(-10px)';
                }, 150);
            });
        }
    });
    
    // Enhanced button animations
    document.querySelectorAll('.btn').forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Form enhancements (if forms exist)
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, textarea, select');
        
        inputs.forEach(input => {
            // Add focus animations
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
                if (this.value) {
                    this.parentElement.classList.add('filled');
                } else {
                    this.parentElement.classList.remove('filled');
                }
            });
            
            // Check if already filled on load
            if (input.value) {
                input.parentElement.classList.add('filled');
            }
        });
    });
    
    // Social links external link handling
    document.querySelectorAll('.social-links a').forEach(link => {
        if (link.href === '#' || link.href === '') {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                // Could show a toast message about social links coming soon
            });
        }
    });
    
    // Performance optimization: Debounce scroll events
    function debounce(func, wait) {
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
    
    // Apply debounce to scroll handlers
    const debouncedHeaderUpdate = debounce(updateHeader, 10);
    window.removeEventListener('scroll', updateHeader);
    window.addEventListener('scroll', debouncedHeaderUpdate, { passive: true });
    
    // Intersection Observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);
    
    // Observe elements for animations
    document.querySelectorAll('.feature-item, .plugin-card, .testimonial-card').forEach(el => {
        observer.observe(el);
    });
    
    // Add CSS classes for animations
    const style = document.createElement('style');
    style.textContent = `
        .scrolled {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1) !important;
            backdrop-filter: blur(20px) !important;
        }
        
        .mobile-open {
            display: block !important;
        }
        
        .animate-in {
            animation: slideInUp 0.6s ease-out forwards;
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .focused {
            transform: scale(1.02);
        }
        
        .filled label {
            transform: translateY(-20px) scale(0.8);
        }
    `;
    document.head.appendChild(style);
});

// Utility functions for theme
window.PlugHausTheme = {
    // Function to show notifications
    showNotification: function(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="container">
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()">×</button>
            </div>
        `;
        
        document.body.insertBefore(notification, document.body.firstChild);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    },
    
    // Function to handle external links
    trackExternalLink: function(url, label) {
        // Could integrate with analytics here
        console.log(`External link clicked: ${label} - ${url}`);
    }
};