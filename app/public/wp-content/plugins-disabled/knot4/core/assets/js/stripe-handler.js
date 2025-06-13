/**
 * Stripe Payment Handler for Knot4
 * 
 * @package Knot4
 * @since 1.0.0
 */

(function($) {
    'use strict';
    
    var Knot4Stripe = {
        
        stripe: null,
        elements: null,
        cardElement: null,
        
        /**
         * Initialize Stripe
         */
        init: function() {
            if (typeof Stripe === 'undefined' || !knot4_stripe.publishable_key) {
                console.error('Stripe.js not loaded or publishable key missing');
                return;
            }
            
            this.stripe = Stripe(knot4_stripe.publishable_key);
            this.elements = this.stripe.elements();
            
            this.setupCardElement();
            this.bindEvents();
        },
        
        /**
         * Setup card element
         */
        setupCardElement: function() {
            var elementStyles = {
                base: {
                    color: '#32325d',
                    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                    fontSmoothing: 'antialiased',
                    fontSize: '16px',
                    '::placeholder': {
                        color: '#aab7c4'
                    }
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a'
                }
            };
            
            this.cardElement = this.elements.create('card', {
                style: elementStyles,
                hidePostalCode: false
            });
            
            // Mount card element
            if ($('#knot4-card-element').length) {
                this.cardElement.mount('#knot4-card-element');
                
                // Handle card element events
                this.cardElement.addEventListener('change', function(event) {
                    var displayError = document.getElementById('knot4-card-errors');
                    if (event.error) {
                        displayError.textContent = event.error.message;
                        displayError.style.display = 'block';
                    } else {
                        displayError.textContent = '';
                        displayError.style.display = 'none';
                    }
                });
            }
        },
        
        /**
         * Bind events
         */
        bindEvents: function() {
            var self = this;
            
            // Handle donation form submission
            $(document).on('submit', '.knot4-donation-form', function(e) {
                e.preventDefault();
                self.handleDonationSubmission($(this));
            });
            
            // Handle amount button clicks
            $(document).on('click', '.knot4-amount-btn', function(e) {
                e.preventDefault();
                self.selectAmount($(this));
            });
            
            // Handle custom amount input
            $(document).on('input', '.knot4-custom-amount', function() {
                self.updateCustomAmount($(this));
            });
            
            // Handle frequency changes
            $(document).on('change', '.knot4-frequency-input', function() {
                self.updateFrequency($(this));
            });
        },
        
        /**
         * Handle donation form submission
         */
        handleDonationSubmission: function($form) {
            var self = this;
            var $submitBtn = $form.find('.knot4-submit-btn');
            var originalText = $submitBtn.text();
            
            // Validate form
            if (!this.validateDonationForm($form)) {
                return false;
            }
            
            // Disable submit button
            $submitBtn.prop('disabled', true).text(knot4_stripe.strings.processing);
            
            // Clear previous errors
            this.clearErrors($form);
            
            // Get donation data
            var donationData = this.getDonationData($form);
            
            // Create donation record first
            this.createDonationRecord(donationData)
                .then(function(response) {
                    if (response.success) {
                        donationData.donation_id = response.data.donation_id;
                        return self.createPaymentIntent(donationData);
                    } else {
                        throw new Error(response.data.message || 'Failed to create donation record');
                    }
                })
                .then(function(response) {
                    if (response.success) {
                        return self.confirmPayment(response.data.client_secret, donationData);
                    } else {
                        throw new Error(response.data.message || 'Failed to create payment intent');
                    }
                })
                .then(function(result) {
                    if (result.error) {
                        throw new Error(result.error.message);
                    } else {
                        self.handlePaymentSuccess(result, donationData);
                    }
                })
                .catch(function(error) {
                    self.handlePaymentError(error, $form);
                })
                .finally(function() {
                    $submitBtn.prop('disabled', false).text(originalText);
                });
        },
        
        /**
         * Validate donation form
         */
        validateDonationForm: function($form) {
            var isValid = true;
            var self = this;
            
            // Check amount
            var amount = parseFloat($form.find('.knot4-amount-input').val());
            if (!amount || amount <= 0) {
                this.showError($form, 'amount', 'Please enter a valid donation amount.');
                isValid = false;
            }
            
            // Check required fields
            $form.find('[required]').each(function() {
                var $field = $(this);
                var value = $field.val().trim();
                
                if (!value) {
                    var fieldName = $field.attr('name').replace('donor_', '').replace('_', ' ');
                    self.showError($form, $field.attr('name'), 'Please enter your ' + fieldName + '.');
                    isValid = false;
                }
            });
            
            // Validate email
            var email = $form.find('input[name="donor_email"]').val();
            if (email && !this.isValidEmail(email)) {
                this.showError($form, 'donor_email', 'Please enter a valid email address.');
                isValid = false;
            }
            
            return isValid;
        },
        
        /**
         * Get donation data from form
         */
        getDonationData: function($form) {
            var formData = new FormData($form[0]);
            var data = {};
            
            for (var pair of formData.entries()) {
                data[pair[0]] = pair[1];
            }
            
            return data;
        },
        
        /**
         * Create donation record
         */
        createDonationRecord: function(donationData) {
            return $.ajax({
                url: knot4_stripe.ajax_url,
                type: 'POST',
                data: {
                    action: 'knot4_create_donation',
                    nonce: knot4_stripe.nonce,
                    ...donationData
                }
            });
        },
        
        /**
         * Create payment intent
         */
        createPaymentIntent: function(donationData) {
            return $.ajax({
                url: knot4_stripe.ajax_url,
                type: 'POST',
                data: {
                    action: 'knot4_create_payment_intent',
                    nonce: knot4_stripe.nonce,
                    ...donationData
                }
            });
        },
        
        /**
         * Confirm payment with Stripe
         */
        confirmPayment: function(clientSecret, donationData) {
            var self = this;
            
            var paymentData = {
                payment_method: {
                    card: this.cardElement,
                    billing_details: {
                        name: donationData.donor_first_name + ' ' + donationData.donor_last_name,
                        email: donationData.donor_email,
                    }
                },
                receipt_email: donationData.donor_email,
            };
            
            // Add billing address if provided
            if (donationData.donor_address) {
                paymentData.payment_method.billing_details.address = {
                    line1: donationData.donor_address,
                    city: donationData.donor_city,
                    state: donationData.donor_state,
                    postal_code: donationData.donor_zip,
                    country: donationData.donor_country || 'US'
                };
            }
            
            return this.stripe.confirmCardPayment(clientSecret, paymentData);
        },
        
        /**
         * Handle payment success
         */
        handlePaymentSuccess: function(result, donationData) {
            var self = this;
            
            // Confirm payment on server
            $.ajax({
                url: knot4_stripe.ajax_url,
                type: 'POST',
                data: {
                    action: 'knot4_confirm_payment',
                    nonce: knot4_stripe.nonce,
                    payment_intent_id: result.paymentIntent.id,
                    donation_id: donationData.donation_id
                }
            }).done(function(response) {
                if (response.success) {
                    // Show success message
                    self.showSuccessMessage(response.data.message);
                    
                    // Redirect to thank you page after delay
                    setTimeout(function() {
                        if (response.data.redirect_url) {
                            window.location.href = response.data.redirect_url;
                        }
                    }, 2000);
                } else {
                    self.showError(null, 'general', response.data.message);
                }
            }).fail(function() {
                self.showError(null, 'general', 'Payment confirmation failed. Please contact support.');
            });
        },
        
        /**
         * Handle payment error
         */
        handlePaymentError: function(error, $form) {
            console.error('Payment error:', error);
            
            var message = error.message || knot4_stripe.strings.payment_failed;
            
            // Handle specific Stripe error types
            if (error.type === 'card_error') {
                message = knot4_stripe.strings.card_error;
            } else if (error.type === 'api_connection_error') {
                message = knot4_stripe.strings.network_error;
            }
            
            this.showError($form, 'general', message);
        },
        
        /**
         * Select donation amount
         */
        selectAmount: function($btn) {
            var $form = $btn.closest('.knot4-donation-form');
            var amount = $btn.data('amount');
            
            // Update active button
            $form.find('.knot4-amount-btn').removeClass('active');
            $btn.addClass('active');
            
            // Update amount input
            $form.find('.knot4-amount-input').val(amount);
            
            // Clear custom amount
            $form.find('.knot4-custom-amount').val('');
            
            this.updateDonationSummary($form);
        },
        
        /**
         * Update custom amount
         */
        updateCustomAmount: function($input) {
            var $form = $input.closest('.knot4-donation-form');
            var amount = parseFloat($input.val());
            
            if (amount > 0) {
                // Clear amount buttons
                $form.find('.knot4-amount-btn').removeClass('active');
                
                // Update amount input
                $form.find('.knot4-amount-input').val(amount);
                
                this.updateDonationSummary($form);
            }
        },
        
        /**
         * Update frequency
         */
        updateFrequency: function($input) {
            var $form = $input.closest('.knot4-donation-form');
            this.updateDonationSummary($form);
        },
        
        /**
         * Update donation summary
         */
        updateDonationSummary: function($form) {
            var amount = parseFloat($form.find('.knot4-amount-input').val()) || 0;
            var frequency = $form.find('.knot4-frequency-input:checked').val() || 'once';
            var $summary = $form.find('.knot4-donation-summary');
            
            if ($summary.length && amount > 0) {
                var currency = knot4_stripe.currency.toUpperCase();
                var formattedAmount = this.formatCurrency(amount, currency);
                var frequencyText = this.getFrequencyText(frequency);
                
                var summaryText = formattedAmount;
                if (frequency !== 'once') {
                    summaryText += ' ' + frequencyText;
                }
                
                $summary.find('.amount').text(summaryText);
                $summary.show();
            }
        },
        
        /**
         * Format currency
         */
        formatCurrency: function(amount, currency) {
            var symbols = {
                'USD': '$',
                'EUR': '€',
                'GBP': '£',
                'CAD': 'C$'
            };
            
            var symbol = symbols[currency] || '$';
            return symbol + amount.toFixed(2);
        },
        
        /**
         * Get frequency text
         */
        getFrequencyText: function(frequency) {
            var texts = {
                'once': '',
                'weekly': 'per week',
                'monthly': 'per month',
                'quarterly': 'per quarter',
                'annually': 'per year'
            };
            
            return texts[frequency] || '';
        },
        
        /**
         * Show error message
         */
        showError: function($form, field, message) {
            var $errorContainer;
            
            if ($form && field !== 'general') {
                $errorContainer = $form.find('.knot4-field-error[data-field="' + field + '"]');
                if (!$errorContainer.length) {
                    var $field = $form.find('[name="' + field + '"]');
                    $errorContainer = $('<div class="knot4-field-error" data-field="' + field + '"></div>');
                    $field.after($errorContainer);
                }
            } else {
                $errorContainer = $('.knot4-general-error');
                if (!$errorContainer.length) {
                    $errorContainer = $('<div class="knot4-general-error"></div>');
                    if ($form) {
                        $form.prepend($errorContainer);
                    } else {
                        $('.knot4-donation-form').first().prepend($errorContainer);
                    }
                }
            }
            
            $errorContainer.html(message).show();
            
            // Auto-hide after 5 seconds
            setTimeout(function() {
                $errorContainer.fadeOut();
            }, 5000);
        },
        
        /**
         * Show success message
         */
        showSuccessMessage: function(message) {
            var $successContainer = $('.knot4-success-message');
            if (!$successContainer.length) {
                $successContainer = $('<div class="knot4-success-message"></div>');
                $('.knot4-donation-form').first().prepend($successContainer);
            }
            
            $successContainer.html(message).show();
        },
        
        /**
         * Clear errors
         */
        clearErrors: function($form) {
            if ($form) {
                $form.find('.knot4-field-error, .knot4-general-error').hide();
            } else {
                $('.knot4-field-error, .knot4-general-error').hide();
            }
        },
        
        /**
         * Validate email
         */
        isValidEmail: function(email) {
            var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        }
    };
    
    // Initialize when document is ready
    $(document).ready(function() {
        if (typeof knot4_stripe !== 'undefined') {
            Knot4Stripe.init();
        }
    });
    
    // Make available globally
    window.Knot4Stripe = Knot4Stripe;
    
})(jQuery);