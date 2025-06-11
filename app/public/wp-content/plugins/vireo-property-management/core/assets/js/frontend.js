/**
 * Vireo Property Management - Frontend JavaScript
 */

(function($) {
    'use strict';
    
    // Initialize when document is ready
    $(document).ready(function() {
        VireoPropertyManagement.init();
    });
    
    var VireoPropertyManagement = {
        
        init: function() {
            this.initPropertySearch();
            this.initPropertyCards();
            this.initForms();
            this.initTenantDashboard();
        },
        
        initPropertySearch: function() {
            $('.vpm-property-search form').on('submit', function(e) {
                e.preventDefault();
                VireoPropertyManagement.performSearch($(this));
            });
            
            $('.vpm-search-filters input, .vpm-search-filters select').on('change', function() {
                VireoPropertyManagement.performSearch($(this).closest('form'));
            });
        },
        
        initPropertyCards: function() {
            $('.vpm-property-card').on('click', function(e) {
                if (!$(e.target).is('a, button')) {
                    var link = $(this).find('.vpm-property-link').attr('href');
                    if (link) {
                        window.location.href = link;
                    }
                }
            });
        },
        
        initForms: function() {
            // Form validation
            $('.vpm-form').on('submit', function(e) {
                var form = $(this);
                var isValid = VireoPropertyManagement.validateForm(form);
                
                if (!isValid) {
                    e.preventDefault();
                    return false;
                }
            });
            
            // Real-time validation
            $('.vpm-form input, .vpm-form textarea').on('blur', function() {
                VireoPropertyManagement.validateField($(this));
            });
        },
        
        initTenantDashboard: function() {
            // Maintenance request form
            $('.vpm-maintenance-form').on('submit', function(e) {
                e.preventDefault();
                VireoPropertyManagement.submitMaintenanceRequest($(this));
            });
            
            // Payment history toggle
            $('.vpm-payment-toggle').on('click', function() {
                $('.vpm-payment-details').slideToggle();
            });
        },
        
        performSearch: function(form) {
            var formData = form.serialize();
            var resultsContainer = $('.vpm-search-results');
            
            resultsContainer.addClass('loading');
            
            $.ajax({
                url: vpm_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'vpm_property_search',
                    nonce: vpm_ajax.nonce,
                    search_data: formData
                },
                success: function(response) {
                    if (response.success) {
                        resultsContainer.html(response.data.html);
                        VireoPropertyManagement.initPropertyCards();
                    } else {
                        VireoPropertyManagement.showMessage('Search failed. Please try again.', 'error');
                    }
                },
                error: function() {
                    VireoPropertyManagement.showMessage('Search failed. Please try again.', 'error');
                },
                complete: function() {
                    resultsContainer.removeClass('loading');
                }
            });
        },
        
        validateForm: function(form) {
            var isValid = true;
            
            form.find('input[required], textarea[required], select[required]').each(function() {
                if (!VireoPropertyManagement.validateField($(this))) {
                    isValid = false;
                }
            });
            
            return isValid;
        },
        
        validateField: function(field) {
            var value = field.val().trim();
            var isValid = true;
            var errorMessage = '';
            
            // Required field validation
            if (field.attr('required') && !value) {
                isValid = false;
                errorMessage = 'This field is required.';
            }
            
            // Email validation
            if (field.attr('type') === 'email' && value) {
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    isValid = false;
                    errorMessage = 'Please enter a valid email address.';
                }
            }
            
            // Phone validation
            if (field.attr('type') === 'tel' && value) {
                var phoneRegex = /^[\d\s\-\(\)\+\.]+$/;
                if (!phoneRegex.test(value)) {
                    isValid = false;
                    errorMessage = 'Please enter a valid phone number.';
                }
            }
            
            // Show/hide error message
            var errorContainer = field.next('.vpm-field-error');
            if (!isValid) {
                if (errorContainer.length === 0) {
                    field.after('<div class="vpm-field-error">' + errorMessage + '</div>');
                } else {
                    errorContainer.text(errorMessage);
                }
                field.addClass('error');
            } else {
                errorContainer.remove();
                field.removeClass('error');
            }
            
            return isValid;
        },
        
        submitMaintenanceRequest: function(form) {
            var formData = new FormData(form[0]);
            formData.append('action', 'vpm_submit_maintenance_request');
            formData.append('nonce', vpm_ajax.nonce);
            
            $.ajax({
                url: vpm_ajax.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        VireoPropertyManagement.showMessage('Maintenance request submitted successfully!', 'success');
                        form[0].reset();
                    } else {
                        VireoPropertyManagement.showMessage(response.data.message || 'Failed to submit request.', 'error');
                    }
                },
                error: function() {
                    VireoPropertyManagement.showMessage('Failed to submit request. Please try again.', 'error');
                }
            });
        },
        
        showMessage: function(message, type) {
            var messageClass = 'vpm-message vpm-message-' + type;
            var messageHtml = '<div class="' + messageClass + '">' + message + '</div>';
            
            // Remove existing messages
            $('.vpm-message').remove();
            
            // Add new message
            $('body').prepend(messageHtml);
            
            // Auto-hide after 5 seconds
            setTimeout(function() {
                $('.vpm-message').fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        }
    };
    
    // Make available globally
    window.VireoPropertyManagement = VireoPropertyManagement;
    
})(jQuery);