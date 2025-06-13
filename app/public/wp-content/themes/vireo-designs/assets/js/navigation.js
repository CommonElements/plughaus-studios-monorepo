/**
 * Navigation JavaScript for Vireo Designs Theme
 * Handles mobile menu and navigation interactions
 */

(function() {
    'use strict';

    // Mobile menu toggle
    function initMobileMenu() {
        const mobileToggle = document.querySelector('.mobile-menu-toggle');
        const navigation = document.querySelector('.main-navigation');
        
        if (mobileToggle && navigation) {
            mobileToggle.addEventListener('click', function() {
                navigation.classList.toggle('active');
                this.setAttribute('aria-expanded', navigation.classList.contains('active'));
            });
        }
    }

    // Smooth scrolling for anchor links
    function initSmoothScrolling() {
        const links = document.querySelectorAll('a[href^="#"]');
        
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    e.preventDefault();
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initMobileMenu();
        initSmoothScrolling();
    });

})();