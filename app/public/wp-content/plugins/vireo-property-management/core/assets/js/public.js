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
                    url: vmp_public.ajax_url,
                    type: 'GET',
                    data: formData + '&action=vmp_search_properties',
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
            url: vmp_public.ajax_url,
            type: 'GET',
            data: {
                action: 'vmp_live_search',
                query: query,
                nonce: vmp_public.nonce
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
                formData.append('action', 'vmp_submit_maintenance_request');
                
                $.ajax({
                    url: vmp_public.ajax_url,
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
                url: vmp_public.ajax_url,
                type: 'POST',
                data: {
                    action: 'vmp_track_document_download',
                    document_id: documentId,
                    nonce: vmp_public.nonce
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
                    url: vmp_public.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'vmp_request_lease_renewal',
                        lease_id: $(this).data('lease-id'),
                        nonce: vmp_public.nonce
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
        // Create modal HTML
        var modalHTML = '<div class="phpm-modal phpm-payment-modal" id="phpm-payment-modal">' +
            '<div class="phpm-modal-content">' +
                '<div class="phpm-modal-header">' +
                    '<h3>' + vmp_public.strings.add_payment_method + '</h3>' +
                    '<span class="phpm-modal-close">&times;</span>' +
                '</div>' +
                '<div class="phpm-modal-body">' +
                    '<div class="phpm-payment-methods">' +
                        '<div class="phpm-payment-option" data-method="bank">' +
                            '<span class="dashicons dashicons-bank"></span>' +
                            '<h4>' + vmp_public.strings.bank_account + '</h4>' +
                            '<p>' + vmp_public.strings.bank_description + '</p>' +
                        '</div>' +
                        '<div class="phpm-payment-option" data-method="card">' +
                            '<span class="dashicons dashicons-admin-site"></span>' +
                            '<h4>' + vmp_public.strings.credit_card + '</h4>' +
                            '<p>' + vmp_public.strings.card_description + '</p>' +
                        '</div>' +
                    '</div>' +
                    '<form class="phpm-payment-form" style="display: none;">' +
                        '<div class="phpm-form-group">' +
                            '<label for="payment-nickname">' + vmp_public.strings.nickname + '</label>' +
                            '<input type="text" id="payment-nickname" name="nickname" placeholder="e.g., My Checking Account" required>' +
                        '</div>' +
                        '<div class="phpm-bank-fields" style="display: none;">' +
                            '<div class="phpm-form-group">' +
                                '<label for="account-number">' + vmp_public.strings.account_number + '</label>' +
                                '<input type="text" id="account-number" name="account_number" placeholder="1234567890">' +
                            '</div>' +
                            '<div class="phpm-form-group">' +
                                '<label for="routing-number">' + vmp_public.strings.routing_number + '</label>' +
                                '<input type="text" id="routing-number" name="routing_number" placeholder="123456789">' +
                            '</div>' +
                        '</div>' +
                        '<div class="phpm-card-fields" style="display: none;">' +
                            '<div class="phpm-form-group">' +
                                '<label for="card-number">' + vmp_public.strings.card_number + '</label>' +
                                '<input type="text" id="card-number" name="card_number" placeholder="1234 5678 9012 3456">' +
                            '</div>' +
                            '<div class="phpm-form-row">' +
                                '<div class="phpm-form-group phpm-form-group-half">' +
                                    '<label for="expiry-date">' + vmp_public.strings.expiry_date + '</label>' +
                                    '<input type="text" id="expiry-date" name="expiry_date" placeholder="MM/YY">' +
                                '</div>' +
                                '<div class="phpm-form-group phpm-form-group-half">' +
                                    '<label for="cvv">' + vmp_public.strings.cvv + '</label>' +
                                    '<input type="text" id="cvv" name="cvv" placeholder="123">' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                        '<div class="phpm-form-actions">' +
                            '<button type="button" class="phpm-button phpm-button-secondary" id="phpm-cancel-payment">' + vmp_public.strings.cancel + '</button>' +
                            '<button type="submit" class="phpm-button phpm-button-primary">' + vmp_public.strings.save_payment_method + '</button>' +
                        '</div>' +
                    '</form>' +
                '</div>' +
            '</div>' +
        '</div>';
        
        // Add modal to page
        $('body').append(modalHTML);
        var $modal = $('#phpm-payment-modal');
        
        // Handle payment method selection
        $modal.on('click', '.phpm-payment-option', function() {
            var method = $(this).data('method');
            
            $('.phpm-payment-option').removeClass('selected');
            $(this).addClass('selected');
            
            $('.phpm-payment-methods').hide();
            $('.phpm-payment-form').show();
            
            if (method === 'bank') {
                $('.phpm-bank-fields').show();
                $('.phpm-card-fields').hide();
            } else if (method === 'card') {
                $('.phpm-bank-fields').hide();
                $('.phpm-card-fields').show();
            }
        });
        
        // Handle form submission
        $modal.on('submit', '.phpm-payment-form', function(e) {
            e.preventDefault();
            
            var $form = $(this);
            var formData = $form.serialize();
            
            $.ajax({
                url: vmp_public.ajax_url,
                type: 'POST',
                data: formData + '&action=vmp_add_payment_method&nonce=' + vmp_public.nonce,
                beforeSend: function() {
                    $form.find('button[type="submit"]').prop('disabled', true).text(vmp_public.strings.saving);
                },
                success: function(response) {
                    if (response.success) {
                        showMessage('success', response.data.message);
                        $modal.remove();
                        location.reload(); // Refresh to show new payment method
                    } else {
                        showMessage('error', response.data.message);
                    }
                },
                error: function() {
                    showMessage('error', vmp_public.strings.error_occurred);
                },
                complete: function() {
                    $form.find('button[type="submit"]').prop('disabled', false).text(vmp_public.strings.save_payment_method);
                }
            });
        });
        
        // Handle modal close
        $modal.on('click', '.phpm-modal-close, #phpm-cancel-payment', function() {
            $modal.remove();
        });
        
        // Close on outside click
        $modal.on('click', function(e) {
            if (e.target === this) {
                $modal.remove();
            }
        });
        
        // Show modal
        $modal.fadeIn();
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
            url: vmp_public.ajax_url,
            type: 'POST',
            data: formData + '&action=vmp_property_inquiry',
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