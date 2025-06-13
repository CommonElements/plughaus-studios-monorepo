/**
 * Main public JavaScript for Knot4
 * 
 * @package Knot4
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Knot4 Public Object
    var Knot4Public = {
        
        /**
         * Initialize public functionality
         */
        init: function() {
            this.bindEvents();
            this.initDonationForms();
            this.initEventForms();
            this.initVolunteerForms();
            this.initDonorPortal();
            this.initFormValidation();
        },

        /**
         * Bind global events
         */
        bindEvents: function() {
            // Handle form submissions
            $(document).on('submit', '.knot4-donation-form', this.handleDonationSubmission);
            $(document).on('submit', '.knot4-event-registration-form', this.handleEventRegistration);
            $(document).on('submit', '.knot4-volunteer-form', this.handleVolunteerSubmission);
            
            // Handle amount button clicks
            $(document).on('click', '.knot4-amount-btn', this.handleAmountSelection);
            
            // Handle custom amount input
            $(document).on('input', '#custom_amount', this.handleCustomAmount);
            
            // Handle fee coverage calculation
            $(document).on('change', '#cover_fees', this.calculateProcessingFees);
            
            // Handle dedication fields toggle
            $(document).on('change', '#show_dedication_fields', this.toggleDedicationFields);
            
            // Handle tab navigation
            $(document).on('click', '.knot4-tab-btn', this.handleTabNavigation);
            
            // Form field validation
            $(document).on('blur', 'input[required], textarea[required]', this.validateField);
        },

        /**
         * Initialize donation forms
         */
        initDonationForms: function() {
            $('.knot4-donation-form').each(function() {
                var $form = $(this);
                var $amountButtons = $form.find('.knot4-amount-btn');
                var $customAmount = $form.find('.knot4-custom-amount');
                var $donationAmount = $form.find('#donation_amount');
                
                // Set default amount if available
                if ($amountButtons.length > 0) {
                    $amountButtons.first().trigger('click');
                }
                
                // Initialize fee calculation
                Knot4Public.calculateProcessingFees.call($form.find('#cover_fees')[0]);
            });
        },

        /**
         * Initialize event forms
         */
        initEventForms: function() {
            $('.knot4-event-registration-form').each(function() {
                var $form = $(this);
                // Event-specific initialization
            });
        },

        /**
         * Initialize volunteer forms
         */
        initVolunteerForms: function() {
            $('.knot4-volunteer-form').each(function() {
                var $form = $(this);
                // Volunteer form specific initialization
            });
        },

        /**
         * Initialize donor portal
         */
        initDonorPortal: function() {
            // Set active tab from URL hash or default to first
            var activeTab = window.location.hash.substring(1) || 'overview';
            this.showTab(activeTab);
        },

        /**
         * Initialize form validation
         */
        initFormValidation: function() {
            // Add validation classes and attributes
            $('input[required], textarea[required], select[required]').addClass('knot4-required');
        },

        /**
         * Handle amount button selection
         */
        handleAmountSelection: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var $form = $button.closest('.knot4-donation-form');
            var amount = $button.data('amount');
            var $amountField = $form.find('#donation_amount');
            var $customAmount = $form.find('.knot4-custom-amount');
            var $customInput = $form.find('#custom_amount');
            
            // Remove active class from all buttons
            $form.find('.knot4-amount-btn').removeClass('active');
            
            if (amount === 'custom') {
                // Show custom amount field
                $customAmount.slideDown();
                $customInput.focus();
                $button.addClass('active');
                $amountField.val('');
            } else {
                // Hide custom amount field and set amount
                $customAmount.slideUp();
                $customInput.val('');
                $amountField.val(amount);
                $button.addClass('active');
                
                // Recalculate fees
                Knot4Public.calculateProcessingFees.call($form.find('#cover_fees')[0]);
            }
        },

        /**
         * Handle custom amount input
         */
        handleCustomAmount: function() {
            var $input = $(this);
            var $form = $input.closest('.knot4-donation-form');
            var $amountField = $form.find('#donation_amount');
            var amount = parseFloat($input.val()) || 0;
            
            $amountField.val(amount);
            
            // Recalculate fees
            Knot4Public.calculateProcessingFees.call($form.find('#cover_fees')[0]);
        },

        /**
         * Calculate processing fees
         */
        calculateProcessingFees: function() {
            var $checkbox = $(this);
            var $form = $checkbox.closest('.knot4-donation-form');
            var $amountField = $form.find('#donation_amount');
            var $feeDisplay = $form.find('.fee-amount');
            var amount = parseFloat($amountField.val()) || 0;
            
            if (amount <= 0) {
                $feeDisplay.hide();
                return;
            }
            
            // Calculate fee (2.9% + $0.30 for Stripe by default)
            var feeRate = 0.029;
            var feeFixed = 0.30;
            var fee = (amount * feeRate) + feeFixed;
            
            if ($checkbox.is(':checked')) {
                $feeDisplay.text('(+' + Knot4Public.formatCurrency(fee) + ' processing fee)').show();
            } else {
                $feeDisplay.hide();
            }
        },

        /**
         * Toggle dedication fields
         */
        toggleDedicationFields: function() {
            var $checkbox = $(this);
            var $form = $checkbox.closest('.knot4-donation-form');
            var $fields = $form.find('.knot4-dedication-fields');
            
            if ($checkbox.is(':checked')) {
                $fields.slideDown();
            } else {
                $fields.slideUp();
                // Clear fields
                $fields.find('input, textarea, select').val('');
            }
        },

        /**
         * Handle form submission
         */
        handleDonationSubmission: function(e) {
            e.preventDefault();
            
            var $form = $(this);
            var $submitBtn = $form.find('.knot4-submit-btn');
            var $messages = $form.find('.knot4-form-messages');
            
            // Validate form
            if (!Knot4Public.validateForm($form)) {
                return false;
            }
            
            // Show loading state
            Knot4Public.setLoadingState($submitBtn, true);
            
            // Prepare form data
            var formData = $form.serialize();
            formData += '&action=knot4_submit_donation';
            
            // Submit via AJAX
            $.ajax({
                url: knot4_public.ajax_url,
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Knot4Public.showMessage($messages, 'success', response.data.message);
                        
                        // Redirect if specified
                        if (response.data.redirect_url) {
                            setTimeout(function() {
                                window.location.href = response.data.redirect_url;
                            }, 2000);
                        } else {
                            // Reset form
                            $form[0].reset();
                            $form.find('.knot4-amount-btn').removeClass('active');
                            $form.find('.knot4-custom-amount').hide();
                        }
                    } else {
                        Knot4Public.showMessage($messages, 'error', response.data.message);
                    }
                },
                error: function() {
                    Knot4Public.showMessage($messages, 'error', knot4_public.strings.error);
                },
                complete: function() {
                    Knot4Public.setLoadingState($submitBtn, false);
                }
            });
            
            return false;
        },

        /**
         * Handle event registration
         */
        handleEventRegistration: function(e) {
            e.preventDefault();
            
            var $form = $(this);
            var $submitBtn = $form.find('.knot4-submit-btn');
            var $messages = $form.find('.knot4-form-messages');
            
            // Validate form
            if (!Knot4Public.validateForm($form)) {
                return false;
            }
            
            // Show loading state
            Knot4Public.setLoadingState($submitBtn, true);
            
            // Prepare form data
            var formData = $form.serialize();
            formData += '&action=knot4_register_event';
            
            // Submit via AJAX
            $.ajax({
                url: knot4_public.ajax_url,
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Knot4Public.showMessage($messages, 'success', response.data.message);
                        
                        // Redirect if specified
                        if (response.data.redirect_url) {
                            setTimeout(function() {
                                window.location.href = response.data.redirect_url;
                            }, 2000);
                        }
                    } else {
                        Knot4Public.showMessage($messages, 'error', response.data.message);
                    }
                },
                error: function() {
                    Knot4Public.showMessage($messages, 'error', knot4_public.strings.error);
                },
                complete: function() {
                    Knot4Public.setLoadingState($submitBtn, false);
                }
            });
            
            return false;
        },

        /**
         * Handle volunteer form submission
         */
        handleVolunteerSubmission: function(e) {
            e.preventDefault();
            
            var $form = $(this);
            var $submitBtn = $form.find('.knot4-submit-btn');
            var $messages = $form.find('.knot4-form-messages');
            
            // Validate form
            if (!Knot4Public.validateForm($form)) {
                return false;
            }
            
            // Show loading state
            Knot4Public.setLoadingState($submitBtn, true);
            
            // Prepare form data
            var formData = $form.serialize();
            formData += '&action=knot4_submit_volunteer';
            
            // Submit via AJAX
            $.ajax({
                url: knot4_public.ajax_url,
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Knot4Public.showMessage($messages, 'success', response.data.message);
                        
                        // Reset form
                        $form[0].reset();
                    } else {
                        Knot4Public.showMessage($messages, 'error', response.data.message);
                    }
                },
                error: function() {
                    Knot4Public.showMessage($messages, 'error', knot4_public.strings.error);
                },
                complete: function() {
                    Knot4Public.setLoadingState($submitBtn, false);
                }
            });
            
            return false;
        },

        /**
         * Handle tab navigation
         */
        handleTabNavigation: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var targetTab = $button.data('tab');
            
            Knot4Public.showTab(targetTab);
            
            // Update URL hash
            if (history.pushState) {
                history.pushState(null, null, '#' + targetTab);
            } else {
                window.location.hash = targetTab;
            }
        },

        /**
         * Show specific tab
         */
        showTab: function(tabId) {
            var $container = $('.knot4-portal-tabs');
            
            // Update tab buttons
            $container.find('.knot4-tab-btn').removeClass('active');
            $container.find('.knot4-tab-btn[data-tab="' + tabId + '"]').addClass('active');
            
            // Update tab panels
            $container.find('.knot4-tab-panel').removeClass('active');
            $container.find('#' + tabId).addClass('active');
        },

        /**
         * Validate form
         */
        validateForm: function($form) {
            var isValid = true;
            var $messages = $form.find('.knot4-form-messages');
            
            // Clear previous messages
            $messages.empty();
            
            // Check required fields
            $form.find('[required]').each(function() {
                var $field = $(this);
                if (!Knot4Public.validateField.call(this)) {
                    isValid = false;
                }
            });
            
            // Donation form specific validation
            if ($form.hasClass('knot4-donation-form')) {
                var amount = parseFloat($form.find('#donation_amount').val()) || 0;
                if (amount <= 0) {
                    Knot4Public.showMessage($messages, 'error', knot4_public.strings.invalid_amount);
                    isValid = false;
                }
            }
            
            return isValid;
        },

        /**
         * Validate individual field
         */
        validateField: function() {
            var $field = $(this);
            var value = $field.val().trim();
            var isValid = true;
            
            // Remove previous error styling
            $field.removeClass('knot4-field-error');
            
            // Check if required field is empty
            if ($field.prop('required') && !value) {
                isValid = false;
            }
            
            // Email validation
            if ($field.attr('type') === 'email' && value) {
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    isValid = false;
                }
            }
            
            // Number validation
            if ($field.attr('type') === 'number' && value) {
                var num = parseFloat(value);
                var min = parseFloat($field.attr('min'));
                var max = parseFloat($field.attr('max'));
                
                if (isNaN(num) || (min && num < min) || (max && num > max)) {
                    isValid = false;
                }
            }
            
            // Add error styling if invalid
            if (!isValid) {
                $field.addClass('knot4-field-error');
            }
            
            return isValid;
        },

        /**
         * Set loading state on button
         */
        setLoadingState: function($button, loading) {
            if (loading) {
                $button.prop('disabled', true);
                $button.find('.btn-text').hide();
                $button.find('.btn-spinner').show();
            } else {
                $button.prop('disabled', false);
                $button.find('.btn-text').show();
                $button.find('.btn-spinner').hide();
            }
        },

        /**
         * Show message
         */
        showMessage: function($container, type, message) {
            var messageClass = 'knot4-message knot4-message-' + type;
            var $message = $('<div class="' + messageClass + '">' + message + '</div>');
            
            $container.empty().append($message);
            
            // Auto-hide success messages
            if (type === 'success') {
                setTimeout(function() {
                    $message.fadeOut();
                }, 5000);
            }
        },

        /**
         * Format currency
         */
        formatCurrency: function(amount) {
            var currency = knot4_public.currency || {};
            var symbol = currency.symbol || '$';
            var decimals = currency.decimals || 2;
            var position = currency.position || 'before';
            
            var formatted = parseFloat(amount).toFixed(decimals);
            
            if (position === 'before') {
                return symbol + formatted;
            } else {
                return formatted + symbol;
            }
        },

        /**
         * Utility: Debounce function
         */
        debounce: function(func, wait, immediate) {
            var timeout;
            return function() {
                var context = this, args = arguments;
                var later = function() {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                var callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        }
    };

    // Add field error styling
    $('<style>')
        .prop('type', 'text/css')
        .html('.knot4-field-error { border-color: #e74c3c !important; box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1) !important; }')
        .appendTo('head');

    // Initialize when document is ready
    $(document).ready(function() {
        Knot4Public.init();
    });

    // Make Knot4Public globally available
    window.Knot4Public = Knot4Public;

})(jQuery);