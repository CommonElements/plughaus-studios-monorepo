/**
 * Plugin Cards Interactive Features
 * Enhance the plugin directory with smooth interactions
 */

(function($) {
    'use strict';

    // Wait for DOM to be ready
    $(document).ready(function() {
        
        // Initialize plugin cards
        initPluginCards();
        
        // Initialize filters
        initPluginFilters();
        
        // Initialize search
        initPluginSearch();
        
        // Initialize hover effects
        initHoverEffects();
        
        // Initialize lazy loading for images
        initLazyLoading();
    });

    /**
     * Initialize plugin cards
     */
    function initPluginCards() {
        // Add loaded class for animations
        $('.plugin-card').each(function(index) {
            const card = $(this);
            setTimeout(() => {
                card.addClass('loaded');
            }, index * 100);
        });

        // Smooth scroll to card on focus
        $('.plugin-card a').on('focus', function() {
            const card = $(this).closest('.plugin-card');
            const cardTop = card.offset().top - 100;
            
            $('html, body').animate({
                scrollTop: cardTop
            }, 300);
        });
    }

    /**
     * Initialize filter functionality
     */
    function initPluginFilters() {
        const filterSelect = $('#industry-filter');
        const cards = $('.plugin-card');
        
        filterSelect.on('change', function() {
            const selectedIndustry = $(this).val();
            
            if (!selectedIndustry) {
                // Show all cards
                cards.each(function() {
                    $(this).fadeIn(300).removeClass('filtered-out');
                });
            } else {
                // Filter cards
                cards.each(function() {
                    const card = $(this);
                    const cardIndustry = card.data('industry');
                    
                    if (cardIndustry === selectedIndustry || card.hasClass('ecosystem-card')) {
                        card.fadeIn(300).removeClass('filtered-out');
                    } else {
                        card.fadeOut(300).addClass('filtered-out');
                    }
                });
            }
            
            // Update no results message
            updateNoResultsMessage();
        });
    }

    /**
     * Initialize search functionality
     */
    function initPluginSearch() {
        const searchInput = $('#plugin-search');
        const cards = $('.plugin-card');
        
        searchInput.on('input', debounce(function() {
            const searchTerm = $(this).val().toLowerCase();
            
            if (!searchTerm) {
                cards.each(function() {
                    $(this).fadeIn(300).removeClass('search-hidden');
                });
            } else {
                cards.each(function() {
                    const card = $(this);
                    const title = card.find('.plugin-title').text().toLowerCase();
                    const description = card.find('.plugin-description').text().toLowerCase();
                    const badges = card.find('.badge').text().toLowerCase();
                    
                    const matchFound = title.includes(searchTerm) || 
                                     description.includes(searchTerm) || 
                                     badges.includes(searchTerm);
                    
                    if (matchFound) {
                        card.fadeIn(300).removeClass('search-hidden');
                        // Highlight matching text
                        highlightText(card, searchTerm);
                    } else {
                        card.fadeOut(300).addClass('search-hidden');
                    }
                });
            }
            
            // Update no results message
            updateNoResultsMessage();
        }, 300));
    }

    /**
     * Reset all filters
     */
    window.resetFilters = function() {
        $('#industry-filter').val('').trigger('change');
        $('#plugin-search').val('').trigger('input');
        
        // Remove all highlights
        $('.highlight').each(function() {
            const text = $(this).text();
            $(this).replaceWith(text);
        });
    };

    /**
     * Initialize hover effects
     */
    function initHoverEffects() {
        $('.plugin-card').each(function() {
            const card = $(this);
            const icon = card.find('.plugin-icon');
            
            card.on('mouseenter', function() {
                // Add glow effect to icon
                icon.addClass('glow');
                
                // Parallax effect on mouse move
                card.on('mousemove', function(e) {
                    const rect = this.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    
                    const centerX = rect.width / 2;
                    const centerY = rect.height / 2;
                    
                    const deltaX = (x - centerX) / centerX;
                    const deltaY = (y - centerY) / centerY;
                    
                    icon.css({
                        transform: `translate(${deltaX * 5}px, ${deltaY * 5}px) scale(1.05)`
                    });
                });
            });
            
            card.on('mouseleave', function() {
                icon.removeClass('glow');
                icon.css({
                    transform: 'translate(0, 0) scale(1)'
                });
                card.off('mousemove');
            });
        });
    }

    /**
     * Initialize lazy loading for images
     */
    function initLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.add('loaded');
                        observer.unobserve(img);
                    }
                });
            });

            // Observe all lazy images
            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    }

    /**
     * Update no results message
     */
    function updateNoResultsMessage() {
        const visibleCards = $('.plugin-card:visible').length;
        let noResultsMsg = $('.no-results');
        
        if (visibleCards === 0) {
            if (noResultsMsg.length === 0) {
                noResultsMsg = $('<div class="no-results">' +
                    '<h3>No plugins found</h3>' +
                    '<p>Try adjusting your filters or search terms.</p>' +
                    '</div>');
                $('.plugins-showcase .container').append(noResultsMsg);
            }
            noResultsMsg.fadeIn(300);
        } else {
            noResultsMsg.fadeOut(300);
        }
    }

    /**
     * Highlight search text
     */
    function highlightText(card, searchTerm) {
        // Remove existing highlights
        card.find('.highlight').each(function() {
            const text = $(this).text();
            $(this).replaceWith(text);
        });
        
        // Add new highlights
        const regex = new RegExp(`(${searchTerm})`, 'gi');
        
        card.find('.plugin-title, .plugin-description').each(function() {
            const element = $(this);
            const html = element.html();
            const highlighted = html.replace(regex, '<span class="highlight">$1</span>');
            element.html(highlighted);
        });
    }

    /**
     * Debounce function for search
     */
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

    // Add CSS for highlights
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            .highlight {
                background-color: rgba(var(--primary-color-rgb), 0.2);
                padding: 0.1em 0.2em;
                border-radius: 3px;
                font-weight: 600;
            }
            
            .plugin-icon.glow {
                box-shadow: 0 0 30px rgba(var(--primary-color-rgb), 0.5);
            }
            
            .plugin-card.loaded {
                opacity: 1;
            }
            
            .plugin-card {
                opacity: 0;
                transition: opacity 0.6s ease;
            }
            
            img.loaded {
                opacity: 1;
                transition: opacity 0.3s ease;
            }
            
            img[data-src] {
                opacity: 0;
            }
        `)
        .appendTo('head');

})(jQuery);