/**
 * PlugHaus Studios Theme JavaScript
 */

jQuery(document).ready(function($) {
    
    // Smooth scrolling for anchor links
    $('a[href^="#"]').on('click', function(e) {
        var target = $(this.getAttribute('href'));
        if (target.length) {
            e.preventDefault();
            var offset = $('.site-header').outerHeight() + 20;
            $('html, body').animate({
                scrollTop: target.offset().top - offset
            }, 600);
        }
    });
    
    // Plugin card interactions
    $('.plugin-card').on('mouseenter', function() {
        $(this).addClass('hovered');
    }).on('mouseleave', function() {
        $(this).removeClass('hovered');
    });
    
    // Share plugin functionality
    $('.share-plugin').on('click', function(e) {
        e.preventDefault();
        
        var url = $(this).data('url');
        var title = $(this).data('title');
        
        if (navigator.share) {
            // Use native sharing if available
            navigator.share({
                title: title,
                url: url
            }).catch(function(error) {
                console.log('Sharing failed:', error);
            });
        } else {
            // Fallback to copying URL
            copyToClipboard(url);
            showNotification('Plugin URL copied to clipboard!', 'success');
        }
    });
    
    // Bookmark plugin functionality
    $('.bookmark-plugin').on('click', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var pluginId = button.data('plugin-id');
        var icon = button.find('i');
        
        // Get current bookmarks from localStorage
        var bookmarks = JSON.parse(localStorage.getItem('plughaus_bookmarks') || '[]');
        
        if (bookmarks.includes(pluginId)) {
            // Remove bookmark
            bookmarks = bookmarks.filter(function(id) { return id !== pluginId; });
            icon.removeClass('fas').addClass('far');
            showNotification('Plugin removed from bookmarks', 'info');
        } else {
            // Add bookmark
            bookmarks.push(pluginId);
            icon.removeClass('far').addClass('fas');
            showNotification('Plugin bookmarked!', 'success');
        }
        
        localStorage.setItem('plughaus_bookmarks', JSON.stringify(bookmarks));
    });
    
    // Initialize bookmark states
    initializeBookmarks();
    
    // Plugin filtering and search
    var $pluginContainer = $('.plugins-showcase');
    var $plugins = $pluginContainer.find('.plugin-card');
    
    // Add filter controls if they exist
    $('.plugin-filter').on('change', function() {
        filterPlugins();
    });
    
    $('.plugin-search').on('input', function() {
        filterPlugins();
    });
    
    // Contact form enhancements
    $('.plughaus-contact-form').on('submit', function(e) {
        var form = $(this);
        var submitButton = form.find('button[type="submit"]');
        var originalText = submitButton.html();
        
        // Show loading state
        submitButton.html('<i class="fas fa-spinner fa-spin"></i> ' + plughaus_theme.sending_text)
                   .prop('disabled', true);
        
        // Form will submit normally, but we show loading state
        setTimeout(function() {
            if (submitButton.is(':visible')) {
                submitButton.html(originalText).prop('disabled', false);
            }
        }, 3000);
    });
    
    // Newsletter signup
    $('.newsletter-signup').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var email = form.find('input[type="email"]').val();
        
        if (!isValidEmail(email)) {
            showNotification('Please enter a valid email address.', 'error');
            return;
        }
        
        // Here you would typically send the email to your newsletter service
        showNotification('Thank you for subscribing to our newsletter!', 'success');
        form[0].reset();
    });
    
    // Lazy loading for plugin cards
    if ('IntersectionObserver' in window) {
        var cardObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var card = $(entry.target);
                    card.addClass('visible');
                    cardObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '50px'
        });
        
        $('.plugin-card').each(function() {
            cardObserver.observe(this);
        });
    }
    
    // Stats counter animation
    $('.stat-number').each(function() {
        var $this = $(this);
        var finalValue = $this.text();
        
        // Only animate if the element becomes visible
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    animateCounter($this, finalValue);
                    observer.unobserve(entry.target);
                }
            });
        });
        
        observer.observe(this);
    });
    
    // Plugin card hover effects
    $('.plugin-card').on('mouseenter', function() {
        $(this).find('.plugin-icon').addClass('animated');
    }).on('mouseleave', function() {
        $(this).find('.plugin-icon').removeClass('animated');
    });
    
    // Initialize tooltips if available
    if ($.fn.tooltip) {
        $('[data-tooltip]').tooltip();
    }
    
    // Hero card animation
    $('.hero-card .plugin-item').each(function(index) {
        $(this).css('animation-delay', (index * 0.2) + 's');
    });
    
});

/**
 * Initialize bookmark states from localStorage
 */
function initializeBookmarks() {
    var bookmarks = JSON.parse(localStorage.getItem('plughaus_bookmarks') || '[]');
    
    jQuery('.bookmark-plugin').each(function() {
        var pluginId = jQuery(this).data('plugin-id');
        var icon = jQuery(this).find('i');
        
        if (bookmarks.includes(pluginId)) {
            icon.removeClass('far').addClass('fas');
        }
    });
}

/**
 * Filter plugins based on search and filters
 */
function filterPlugins() {
    var searchTerm = jQuery('.plugin-search').val().toLowerCase();
    var statusFilter = jQuery('.plugin-filter[data-filter="status"]').val();
    var categoryFilter = jQuery('.plugin-filter[data-filter="category"]').val();
    
    jQuery('.plugin-card').each(function() {
        var $card = jQuery(this);
        var title = $card.find('.plugin-title').text().toLowerCase();
        var description = $card.find('.plugin-description').text().toLowerCase();
        var status = $card.find('.plugin-status').attr('class').replace('plugin-status status-', '');
        var categories = $card.find('.plugin-category').map(function() {
            return jQuery(this).text().toLowerCase();
        }).get();
        
        var matchesSearch = searchTerm === '' || title.includes(searchTerm) || description.includes(searchTerm);
        var matchesStatus = statusFilter === '' || status === statusFilter;
        var matchesCategory = categoryFilter === '' || categories.includes(categoryFilter.toLowerCase());
        
        if (matchesSearch && matchesStatus && matchesCategory) {
            $card.show().addClass('filtered-visible');
        } else {
            $card.hide().removeClass('filtered-visible');
        }
    });
    
    // Show no results message if needed
    var visibleCards = jQuery('.plugin-card.filtered-visible').length;
    if (visibleCards === 0) {
        if (jQuery('.no-plugins-message').length === 0) {
            jQuery('.plugins-showcase').append('<div class="no-plugins-message"><p>No plugins found matching your criteria.</p></div>');
        }
    } else {
        jQuery('.no-plugins-message').remove();
    }
}

/**
 * Animate counter numbers
 */
function animateCounter($element, finalValue) {
    var hasPlus = finalValue.includes('+');
    var hasStar = finalValue.includes('★');
    var hasSlash = finalValue.includes('/');
    
    var numericValue;
    var suffix = '';
    
    if (hasStar) {
        numericValue = parseFloat(finalValue);
        suffix = '★';
    } else if (hasSlash) {
        numericValue = 24;
        suffix = '/7';
    } else if (hasPlus) {
        numericValue = parseInt(finalValue.replace(/[^\d]/g, ''));
        suffix = '+';
    } else if (finalValue.includes('WordPress.org')) {
        $element.text(finalValue);
        return;
    } else {
        numericValue = parseInt(finalValue.replace(/[^\d]/g, '')) || 0;
    }
    
    if (isNaN(numericValue)) {
        return;
    }
    
    var current = 0;
    var increment = numericValue / 50;
    var timer = setInterval(function() {
        current += increment;
        
        if (current >= numericValue) {
            current = numericValue;
            clearInterval(timer);
        }
        
        var displayValue;
        if (hasStar) {
            displayValue = current.toFixed(1) + suffix;
        } else if (hasSlash) {
            displayValue = Math.floor(current) + suffix;
        } else {
            displayValue = Math.floor(current).toLocaleString() + suffix;
        }
        
        $element.text(displayValue);
    }, 30);
}

/**
 * Copy text to clipboard
 */
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text);
    } else {
        // Fallback for older browsers
        var textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
    }
}

/**
 * Show notification message
 */
function showNotification(message, type) {
    type = type || 'info';
    
    var notification = jQuery('<div class="notification notification-' + type + '">')
        .html('<div class="container"><i class="fas fa-' + getNotificationIcon(type) + '"></i> ' + message + '</div>')
        .hide()
        .prependTo('body')
        .fadeIn(300);
    
    setTimeout(function() {
        notification.fadeOut(300, function() {
            notification.remove();
        });
    }, 4000);
}

/**
 * Get notification icon based on type
 */
function getNotificationIcon(type) {
    var icons = {
        success: 'check-circle',
        error: 'exclamation-circle',
        warning: 'exclamation-triangle',
        info: 'info-circle'
    };
    return icons[type] || 'info-circle';
}

/**
 * Validate email address
 */
function isValidEmail(email) {
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Initialize page-specific functionality
 */
jQuery(document).ready(function($) {
    var currentPage = $('body').attr('class');
    
    // Home page specific
    if (currentPage.includes('page-template-page-home')) {
        // Add any home page specific JavaScript here
    }
    
    // Plugin archive page specific
    if (currentPage.includes('post-type-archive-phstudios_plugin')) {
        // Add any plugin archive specific JavaScript here
    }
    
    // Single plugin page specific
    if (currentPage.includes('single-phstudios_plugin')) {
        // Add any single plugin specific JavaScript here
    }
    
    // Stripe Checkout functionality
    $('.checkout-btn').on('click', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var priceId = button.data('price-id');
        var productName = button.data('product-name');
        var originalText = button.html();
        
        if (!priceId) {
            showNotification('Product configuration error. Please contact support.', 'error');
            return;
        }
        
        // Show loading state
        button.html('<i class="fas fa-spinner fa-spin"></i> Processing...').prop('disabled', true);
        
        // Create checkout session
        $.ajax({
            url: plughaus_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'create_checkout_session',
                price_id: priceId,
                product_name: productName,
                nonce: plughaus_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Redirect to Stripe Checkout
                    window.location.href = response.data.checkout_url;
                } else {
                    showNotification('Checkout failed: ' + (response.data || 'Unknown error'), 'error');
                    button.html(originalText).prop('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                showNotification('Network error. Please try again.', 'error');
                button.html(originalText).prop('disabled', false);
            }
        });
    });
});

// Add CSS animations
jQuery(document).ready(function($) {
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            .plugin-card {
                opacity: 0;
                transform: translateY(30px);
                transition: all 0.6s ease;
            }
            
            .plugin-card.visible {
                opacity: 1;
                transform: translateY(0);
            }
            
            .plugin-icon.animated {
                transform: scale(1.1) rotate(5deg);
                transition: transform 0.3s ease;
            }
            
            .hero-card .plugin-item {
                opacity: 0;
                animation: slideInUp 0.6s ease forwards;
            }
            
            @keyframes slideInUp {
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .notification {
                position: fixed;
                top: 100px;
                left: 50%;
                transform: translateX(-50%);
                z-index: 10000;
                max-width: 500px;
                width: 90%;
                border-radius: var(--radius);
                color: white;
                font-weight: 500;
                box-shadow: var(--shadow-lg);
            }
            
            .notification-success { background-color: var(--success-color); }
            .notification-error { background-color: var(--danger-color); }
            .notification-warning { background-color: var(--warning-color); }
            .notification-info { background-color: var(--primary-color); }
            
            .no-plugins-message {
                grid-column: 1 / -1;
                text-align: center;
                padding: var(--spacing-16);
                color: var(--gray-600);
            }
            
            .filtered-visible {
                display: block !important;
            }
            
            @media (max-width: 768px) {
                .notification {
                    top: 80px;
                    width: 95%;
                }
            }
        `)
        .appendTo('head');
});