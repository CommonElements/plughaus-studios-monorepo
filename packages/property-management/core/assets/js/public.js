/**
 * PlugHaus Property Management - Public JavaScript
 */

(function($) {
    'use strict';
    
    // Wait for DOM ready
    $(document).ready(function() {
        
        // Initialize property search
        initPropertySearch();
        
        // Initialize maintenance request form
        initMaintenanceForm();
        
        // Initialize property gallery
        initPropertyGallery();
        
        // Initialize tenant portal
        initTenantPortal();
        
    });
    
    /**
     * Initialize property search functionality
     */
    function initPropertySearch() {
        // Advanced search toggle
        $('.phpm-advanced-search-toggle').on('click', function(e) {
            e.preventDefault();
            $('.phpm-advanced-search-fields').slideToggle();
            $(this).toggleClass('active');
        });
        
        // Price range slider (if implemented)
        if ($('#phpm-price-range').length) {
            var $minInput = $('#phpm-search-min-rent');
            var $maxInput = $('#phpm-search-max-rent');
            
            // Initialize range slider
            $('#phpm-price-range').slider({
                range: true,
                min: 0,
                max: 5000,
                values: [$minInput.val() || 0, $maxInput.val() || 5000],
                slide: function(event, ui) {
                    $minInput.val(ui.values[0]);
                    $maxInput.val(ui.values[1]);
                    $('.phpm-price-range-display').text('$' + ui.values[0] + ' - $' + ui.values[1]);
                }
            });
        }
        
        // AJAX search
        $('.phpm-property-search').on('submit', function(e) {
            if ($(this).hasClass('ajax-search')) {
                e.preventDefault();
                
                var $form = $(this);
                var $results = $('.phpm-search-results');
                var formData = $form.serialize();
                
                $results.addClass('loading');
                
                $.ajax({
                    url: phpm_public.ajax_url,
                    type: 'GET',
                    data: formData + '&action=phpm_search_properties',
                    success: function(response) {
                        if (response.success) {
                            $results.html(response.data.html);
                        }
                    },
                    complete: function() {
                        $results.removeClass('loading');
                    }
                });
            }
        });
        
        // Live search
        var searchTimer;
        $('.phpm-live-search').on('keyup', function() {
            clearTimeout(searchTimer);
            var $input = $(this);
            var query = $input.val();
            
            if (query.length >= 3) {
                searchTimer = setTimeout(function() {
                    performLiveSearch(query);
                }, 500);
            }
        });
    }
    
    /**
     * Perform live search
     */
    function performLiveSearch(query) {
        $.ajax({
            url: phpm_public.ajax_url,
            type: 'GET',
            data: {
                action: 'phpm_live_search',
                query: query,
                nonce: phpm_public.nonce
            },
            success: function(response) {
                if (response.success) {
                    showLiveSearchResults(response.data.results);
                }
            }
        });
    }
    
    /**
     * Show live search results
     */
    function showLiveSearchResults(results) {
        var $dropdown = $('.phpm-live-search-results');
        
        if (!$dropdown.length) {
            $dropdown = $('<div class="phpm-live-search-results"></div>');
            $('.phpm-live-search').after($dropdown);
        }
        
        $dropdown.empty();
        
        if (results.length) {
            results.forEach(function(result) {
                var $item = $('<a href="' + result.url + '" class="phpm-search-result-item">' +
                    '<strong>' + result.title + '</strong>' +
                    '<span>' + result.address + '</span>' +
                '</a>');
                $dropdown.append($item);
            });
            $dropdown.show();
        } else {
            $dropdown.hide();
        }
    }
    
    /**
     * Initialize maintenance request form
     */
    function initMaintenanceForm() {
        // Form validation
        $('.phpm-maintenance-request-form').on('submit', function(e) {
            var $form = $(this);
            var isValid = true;
            
            // Check required fields
            $form.find('[required]').each(function() {
                if (!$(this).val()) {
                    $(this).addClass('error');
                    isValid = false;
                } else {
                    $(this).removeClass('error');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return false;
            }
            
            // AJAX submission
            if ($form.hasClass('ajax-form')) {
                e.preventDefault();
                
                var formData = new FormData($form[0]);
                formData.append('action', 'phpm_submit_maintenance_request');
                
                $.ajax({
                    url: phpm_public.ajax_url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $form.find('button[type="submit"]').prop('disabled', true).text('Submitting...');
                    },
                    success: function(response) {
                        if (response.success) {
                            $form[0].reset();
                            showMessage('success', response.data.message);
                        } else {
                            showMessage('error', response.data.message);
                        }
                    },
                    error: function() {
                        showMessage('error', 'An error occurred. Please try again.');
                    },
                    complete: function() {
                        $form.find('button[type="submit"]').prop('disabled', false).text('Submit Request');
                    }
                });
            }
        });
        
        // Priority change handler
        $('#phpm-maintenance-priority').on('change', function() {
            var priority = $(this).val();
            if (priority === 'emergency') {
                showMessage('warning', 'For emergencies, please also call our emergency hotline.');
            }
        });
        
        // File upload preview
        $('input[type="file"]').on('change', function() {
            var files = this.files;
            var $preview = $('.phpm-file-preview');
            
            if (!$preview.length) {
                $preview = $('<div class="phpm-file-preview"></div>');
                $(this).after($preview);
            }
            
            $preview.empty();
            
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                if (file.type.match('image.*')) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $preview.append('<img src="' + e.target.result + '" alt="Preview">');
                    };
                    reader.readAsDataURL(file);
                } else {
                    $preview.append('<p>' + file.name + '</p>');
                }
            }
        });
    }
    
    /**
     * Initialize property gallery
     */
    function initPropertyGallery() {
        // Lightbox for property images
        $('.phpm-property-gallery a').on('click', function(e) {
            e.preventDefault();
            
            var $link = $(this);
            var imageUrl = $link.attr('href');
            var $lightbox = createLightbox(imageUrl);
            
            $('body').append($lightbox);
            
            // Navigation
            var $gallery = $link.closest('.phpm-property-gallery');
            var $links = $gallery.find('a');
            var currentIndex = $links.index($link);
            
            // Previous/Next handlers
            $lightbox.on('click', '.phpm-lightbox-prev', function() {
                currentIndex = (currentIndex - 1 + $links.length) % $links.length;
                updateLightboxImage($lightbox, $links.eq(currentIndex).attr('href'));
            });
            
            $lightbox.on('click', '.phpm-lightbox-next', function() {
                currentIndex = (currentIndex + 1) % $links.length;
                updateLightboxImage($lightbox, $links.eq(currentIndex).attr('href'));
            });
        });
    }
    
    /**
     * Create lightbox element
     */
    function createLightbox(imageUrl) {
        var $lightbox = $('<div class="phpm-lightbox">' +
            '<div class="phpm-lightbox-content">' +
                '<span class="phpm-lightbox-close">&times;</span>' +
                '<img src="' + imageUrl + '" alt="">' +
                '<div class="phpm-lightbox-nav">' +
                    '<button class="phpm-lightbox-prev">&lt;</button>' +
                    '<button class="phpm-lightbox-next">&gt;</button>' +
                '</div>' +
            '</div>' +
        '</div>');
        
        // Close handlers
        $lightbox.on('click', '.phpm-lightbox-close', function() {
            $lightbox.remove();
        });
        
        $lightbox.on('click', function(e) {
            if (e.target === this) {
                $lightbox.remove();
            }
        });
        
        // Keyboard navigation
        $(document).on('keydown.lightbox', function(e) {
            if (e.key === 'Escape') {
                $lightbox.remove();
                $(document).off('keydown.lightbox');
            }
        });
        
        return $lightbox;
    }
    
    /**
     * Update lightbox image
     */
    function updateLightboxImage($lightbox, imageUrl) {
        $lightbox.find('img').attr('src', imageUrl);
    }
    
    /**
     * Initialize tenant portal
     */
    function initTenantPortal() {
        // Tab navigation
        $('.phpm-portal-tabs a').on('click', function(e) {
            e.preventDefault();
            
            var $tab = $(this);
            var target = $tab.attr('href');
            
            // Update active states
            $('.phpm-portal-tabs a').removeClass('active');
            $tab.addClass('active');
            
            // Show/hide content
            $('.phpm-portal-tab-content').removeClass('active');
            $(target).addClass('active');
        });
        
        // Document download tracking
        $('.phpm-document-download').on('click', function() {
            var documentId = $(this).data('document-id');
            
            $.ajax({
                url: phpm_public.ajax_url,
                type: 'POST',
                data: {
                    action: 'phpm_track_document_download',
                    document_id: documentId,
                    nonce: phpm_public.nonce
                }
            });
        });
        
        // Payment method management
        $('.phpm-add-payment-method').on('click', function(e) {
            e.preventDefault();
            // Open payment method modal
            showPaymentMethodModal();
        });
        
        // Lease renewal request
        $('.phpm-request-renewal').on('click', function(e) {
            e.preventDefault();
            
            if (confirm('Would you like to request a lease renewal?')) {
                $.ajax({
                    url: phpm_public.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'phpm_request_lease_renewal',
                        lease_id: $(this).data('lease-id'),
                        nonce: phpm_public.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            showMessage('success', response.data.message);
                        } else {
                            showMessage('error', response.data.message);
                        }
                    }
                });
            }
        });
    }
    
    /**
     * Show message to user
     */
    function showMessage(type, message) {
        var $message = $('<div class="phpm-message phpm-message-' + type + '">' + message + '</div>');
        
        $('.phpm-messages').append($message);
        
        setTimeout(function() {
            $message.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }
    
    /**
     * Show payment method modal
     */
    function showPaymentMethodModal() {
        // Implementation for payment method modal
        // This would integrate with Stripe or other payment processors
    }
    
    /**
     * Initialize smooth scrolling for anchor links
     */
    $('a[href*="#"]:not([href="#"])').on('click', function() {
        if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && location.hostname === this.hostname) {
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
            
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 1000);
                return false;
            }
        }
    });
    
    /**
     * Handle property inquiry forms
     */
    $('.phpm-inquiry-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var formData = $form.serialize();
        
        $.ajax({
            url: phpm_public.ajax_url,
            type: 'POST',
            data: formData + '&action=phpm_property_inquiry',
            beforeSend: function() {
                $form.find('button[type="submit"]').prop('disabled', true).text('Sending...');
            },
            success: function(response) {
                if (response.success) {
                    $form[0].reset();
                    showMessage('success', 'Your inquiry has been sent. We will contact you soon!');
                } else {
                    showMessage('error', response.data.message || 'An error occurred. Please try again.');
                }
            },
            complete: function() {
                $form.find('button[type="submit"]').prop('disabled', false).text('Send Inquiry');
            }
        });
    });
    
})(jQuery);