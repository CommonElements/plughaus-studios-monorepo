/**
 * Navigation Dropdown JavaScript for Vireo Designs
 * Handles mobile menu toggle and dropdown interactions
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        initNavigationDropdowns();
        initMobileMenu();
    });

    /**
     * Initialize dropdown navigation functionality
     */
    function initNavigationDropdowns() {
        const $menuItems = $('.nav-menu .menu-item-has-children, .nav-menu .dropdown');
        
        // Add ARIA attributes for accessibility
        $menuItems.each(function() {
            const $this = $(this);
            const $link = $this.children('a');
            const $submenu = $this.children('.sub-menu, .dropdown-menu');
            
            // Set up ARIA attributes
            $link.attr('aria-haspopup', 'true');
            $link.attr('aria-expanded', 'false');
            $submenu.attr('aria-hidden', 'true');
            
            // Add unique IDs for ARIA labeling
            const submenuId = 'submenu-' + Math.random().toString(36).substr(2, 9);
            $submenu.attr('id', submenuId);
            $link.attr('aria-controls', submenuId);
        });

        // Desktop hover interactions
        if (window.innerWidth > 768) {
            $menuItems.on('mouseenter', function() {
                const $this = $(this);
                const $link = $this.children('a');
                const $submenu = $this.children('.sub-menu, .dropdown-menu');
                
                $link.attr('aria-expanded', 'true');
                $submenu.attr('aria-hidden', 'false');
            });

            $menuItems.on('mouseleave', function() {
                const $this = $(this);
                const $link = $this.children('a');
                const $submenu = $this.children('.sub-menu, .dropdown-menu');
                
                $link.attr('aria-expanded', 'false');
                $submenu.attr('aria-hidden', 'true');
            });
        }

        // Mobile click interactions
        if (window.innerWidth <= 768) {
            $menuItems.children('a').on('click', function(e) {
                const $link = $(this);
                const $parent = $link.parent();
                const $submenu = $parent.children('.sub-menu, .dropdown-menu');
                
                // Prevent default navigation for parent items
                if ($submenu.length > 0) {
                    e.preventDefault();
                    
                    // Toggle the dropdown
                    $parent.toggleClass('mobile-expanded');
                    
                    // Update ARIA attributes
                    const isExpanded = $parent.hasClass('mobile-expanded');
                    $link.attr('aria-expanded', isExpanded);
                    $submenu.attr('aria-hidden', !isExpanded);
                    
                    // Close other open dropdowns
                    $menuItems.not($parent).removeClass('mobile-expanded');
                    $menuItems.not($parent).children('a').attr('aria-expanded', 'false');
                    $menuItems.not($parent).children('.sub-menu, .dropdown-menu').attr('aria-hidden', 'true');
                }
            });
        }

        // Keyboard navigation
        $menuItems.children('a').on('keydown', function(e) {
            const $link = $(this);
            const $parent = $link.parent();
            const $submenu = $parent.children('.sub-menu, .dropdown-menu');
            
            // Enter or Space to toggle dropdown
            if (e.which === 13 || e.which === 32) {
                if ($submenu.length > 0) {
                    e.preventDefault();
                    $parent.toggleClass('mobile-expanded');
                    
                    const isExpanded = $parent.hasClass('mobile-expanded');
                    $link.attr('aria-expanded', isExpanded);
                    $submenu.attr('aria-hidden', !isExpanded);
                    
                    // Focus first submenu item if opened
                    if (isExpanded) {
                        $submenu.find('a').first().focus();
                    }
                }
            }
            
            // Escape to close dropdown
            if (e.which === 27) {
                $parent.removeClass('mobile-expanded');
                $link.attr('aria-expanded', 'false');
                $submenu.attr('aria-hidden', 'true');
                $link.focus();
            }
        });

        // Close dropdowns when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.nav-menu').length) {
                $menuItems.removeClass('mobile-expanded');
                $menuItems.children('a').attr('aria-expanded', 'false');
                $menuItems.children('.sub-menu, .dropdown-menu').attr('aria-hidden', 'true');
            }
        });
    }

    /**
     * Initialize mobile menu functionality
     */
    function initMobileMenu() {
        const $toggle = $('.mobile-menu-toggle');
        const $menu = $('.nav-menu');
        
        // Set up ARIA attributes
        $toggle.attr('aria-expanded', 'false');
        $toggle.attr('aria-controls', 'primary-menu');
        $menu.attr('id', 'primary-menu');
        
        $toggle.on('click', function() {
            const $this = $(this);
            const isExpanded = $this.attr('aria-expanded') === 'true';
            
            // Toggle menu visibility
            $menu.toggleClass('mobile-open');
            
            // Update ARIA attributes
            $this.attr('aria-expanded', !isExpanded);
            
            // Toggle body scroll lock
            if (!isExpanded) {
                $('body').addClass('mobile-menu-open');
            } else {
                $('body').removeClass('mobile-menu-open');
            }
        });

        // Close mobile menu when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.main-navigation').length && $menu.hasClass('mobile-open')) {
                $menu.removeClass('mobile-open');
                $toggle.attr('aria-expanded', 'false');
                $('body').removeClass('mobile-menu-open');
            }
        });

        // Close mobile menu when pressing Escape
        $(document).on('keydown', function(e) {
            if (e.which === 27 && $menu.hasClass('mobile-open')) {
                $menu.removeClass('mobile-open');
                $toggle.attr('aria-expanded', 'false');
                $('body').removeClass('mobile-menu-open');
                $toggle.focus();
            }
        });

        // Handle window resize
        $(window).on('resize', function() {
            if (window.innerWidth > 768) {
                $menu.removeClass('mobile-open');
                $toggle.attr('aria-expanded', 'false');
                $('body').removeClass('mobile-menu-open');
                
                // Reset mobile expanded states
                $('.nav-menu .menu-item-has-children, .nav-menu .dropdown')
                    .removeClass('mobile-expanded')
                    .children('a').attr('aria-expanded', 'false')
                    .siblings('.sub-menu, .dropdown-menu').attr('aria-hidden', 'true');
            }
        });
    }

    /**
     * Smooth scroll for anchor links within dropdowns
     */
    $('.nav-menu a[href*="#"]').on('click', function(e) {
        const $this = $(this);
        const href = $this.attr('href');
        const hash = href.split('#')[1];
        
        if (hash && $('#' + hash).length) {
            e.preventDefault();
            
            // Close mobile menu if open
            $('.nav-menu').removeClass('mobile-open');
            $('.mobile-menu-toggle').attr('aria-expanded', 'false');
            $('body').removeClass('mobile-menu-open');
            
            // Smooth scroll to target
            $('html, body').animate({
                scrollTop: $('#' + hash).offset().top - 100
            }, 500);
        }
    });

})(jQuery);