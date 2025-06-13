/**
 * StudioSnap Booking Form JavaScript
 * Handles all frontend booking functionality
 */

jQuery(document).ready(function($) {
    'use strict';
    
    var bookingForm = {
        
        init: function() {
            this.bindEvents();
            this.initDatePicker();
            this.validateForm();
        },
        
        bindEvents: function() {
            // Package selection
            $(document).on('click', '.ss-select-package-btn', this.selectPackage);
            
            // Form field changes
            $('#ss_session_date').on('change', this.onDateChange);
            $('#ss_session_type').on('change', this.onSessionTypeChange);
            $('#ss_session_location').on('change', this.updateSummary);
            $('#ss_session_time').on('change', this.updateSummary);
            
            // Form submission
            $('#studiosnap-booking-form').on('submit', this.submitForm);
            
            // Real-time validation
            $('.ss-booking-form input, .ss-booking-form select, .ss-booking-form textarea').on('blur', this.validateField);
        },
        
        initDatePicker: function() {
            $('#ss_session_date').datepicker({
                dateFormat: 'yy-mm-dd',
                minDate: 1, // Tomorrow
                maxDate: '+3m', // 3 months out
                beforeShowDay: function(date) {
                    var day = date.getDay();
                    // Disable Sundays (0) by default - can be customized
                    return [(day !== 0), ''];
                },
                onSelect: function(dateText) {
                    bookingForm.checkAvailability(dateText);
                }
            });
        },
        
        selectPackage: function(e) {
            e.preventDefault();
            
            var packageKey = $(this).data('package');
            
            // Update visual selection
            $('.ss-package-card').removeClass('selected');
            $(this).closest('.ss-package-card').addClass('selected');
            
            // Update form selection
            $('#ss_session_type').val(packageKey).trigger('change');
            
            // Scroll to form
            $('html, body').animate({
                scrollTop: $('.ss-booking-form').offset().top - 100
            }, 500);
        },
        
        onDateChange: function() {
            var selectedDate = $(this).val();
            if (selectedDate) {
                bookingForm.checkAvailability(selectedDate);
            }
        },
        
        onSessionTypeChange: function() {
            var selectedDate = $('#ss_session_date').val();
            if (selectedDate) {
                bookingForm.checkAvailability(selectedDate);
            }
            bookingForm.updateSummary();
        },
        
        checkAvailability: function(date) {
            var sessionType = $('#ss_session_type').val();
            
            if (!sessionType) {
                bookingForm.showError('#ss_session_type', studiosnap_booking.messages.required_field);
                return;
            }
            
            var $timeSelect = $('#ss_session_time');
            var $loadingDiv = $('.ss-availability-loading');
            
            // Show loading
            $loadingDiv.show();
            $timeSelect.prop('disabled', true).html('<option value="">' + studiosnap_booking.messages.checking_availability + '</option>');
            
            $.ajax({
                url: studiosnap_booking.ajax_url,
                type: 'POST',
                data: {
                    action: 'ss_check_availability',
                    nonce: studiosnap_booking.nonce,
                    date: date,
                    session_type: sessionType
                },
                success: function(response) {
                    $loadingDiv.hide();
                    
                    if (response.success && response.data.length > 0) {
                        var options = '<option value="">' + 'Select time...' + '</option>';
                        
                        $.each(response.data, function(index, slot) {
                            options += '<option value="' + slot.time + '">' + slot.display + '</option>';
                        });
                        
                        $timeSelect.html(options).prop('disabled', false);
                        bookingForm.clearError('#ss_session_time');
                    } else {
                        $timeSelect.html('<option value="">' + studiosnap_booking.messages.no_slots_available + '</option>');
                        bookingForm.showError('#ss_session_date', studiosnap_booking.messages.no_slots_available);
                    }
                },
                error: function() {
                    $loadingDiv.hide();
                    $timeSelect.html('<option value="">Error checking availability</option>');
                }
            });
        },
        
        updateSummary: function() {
            var sessionType = $('#ss_session_type').val();
            var sessionDate = $('#ss_session_date').val();
            var sessionTime = $('#ss_session_time').val();
            var sessionLocation = $('#ss_session_location').val();
            
            if (sessionType && sessionDate && sessionTime) {
                var $summaryDiv = $('.ss-booking-summary');
                
                // Get package info
                var packages = {
                    'portrait': { name: 'Portrait Session', price: 200 },
                    'family': { name: 'Family Session', price: 300 },
                    'headshot': { name: 'Professional Headshots', price: 150 },
                    'event': { name: 'Event Photography', price: 500 },
                    'product': { name: 'Product Photography', price: 250 }
                };
                
                var packageInfo = packages[sessionType] || { name: 'Unknown', price: 0 };
                var totalPrice = packageInfo.price;
                
                // Add location fee
                if (sessionLocation === 'on_location') {
                    totalPrice += 50;
                }
                
                // Check for rush booking (less than 48 hours)
                var bookingDateTime = new Date(sessionDate + ' ' + sessionTime);
                var now = new Date();
                var hoursDiff = (bookingDateTime - now) / (1000 * 60 * 60);
                
                if (hoursDiff < 48) {
                    totalPrice = Math.round(totalPrice * 1.2); // 20% rush fee
                }
                
                // Update summary
                $('#summary-session-type').text(packageInfo.name);
                $('#summary-datetime').text(bookingForm.formatDateTime(sessionDate, sessionTime));
                $('#summary-location').text(sessionLocation === 'on_location' ? 'On Location' : 'Studio');
                $('#summary-total').text('$' + totalPrice.toFixed(0));
                
                $summaryDiv.slideDown();
            } else {
                $('.ss-booking-summary').slideUp();
            }
        },
        
        formatDateTime: function(date, time) {
            var dateObj = new Date(date + ' ' + time);
            var options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: 'numeric',
                minute: '2-digit'
            };
            return dateObj.toLocaleDateString('en-US', options);
        },
        
        validateForm: function() {
            var form = $('#studiosnap-booking-form');
            var isValid = true;
            
            // Clear previous errors
            $('.ss-field-error').hide().text('');
            $('.ss-form-field input, .ss-form-field select').removeClass('error');
            
            // Required fields validation
            var requiredFields = [
                { id: '#ss_client_name', message: studiosnap_booking.messages.required_field },
                { id: '#ss_client_email', message: studiosnap_booking.messages.required_field },
                { id: '#ss_session_type', message: studiosnap_booking.messages.required_field },
                { id: '#ss_session_date', message: studiosnap_booking.messages.required_field },
                { id: '#ss_session_time', message: studiosnap_booking.messages.required_field }
            ];
            
            $.each(requiredFields, function(index, field) {
                var $field = $(field.id);
                if (!$field.val().trim()) {
                    bookingForm.showError(field.id, field.message);
                    isValid = false;
                }
            });
            
            // Email validation
            var email = $('#ss_client_email').val();
            if (email && !bookingForm.isValidEmail(email)) {
                bookingForm.showError('#ss_client_email', studiosnap_booking.messages.invalid_email);
                isValid = false;
            }
            
            // Phone validation (if provided)
            var phone = $('#ss_client_phone').val();
            if (phone && !bookingForm.isValidPhone(phone)) {
                bookingForm.showError('#ss_client_phone', studiosnap_booking.messages.invalid_phone);
                isValid = false;
            }
            
            return isValid;
        },
        
        validateField: function() {
            var $field = $(this);
            var fieldId = '#' + $field.attr('id');
            var value = $field.val().trim();
            
            // Clear previous error
            bookingForm.clearError(fieldId);
            
            // Required field check
            if ($field.prop('required') && !value) {
                bookingForm.showError(fieldId, studiosnap_booking.messages.required_field);
                return false;
            }
            
            // Email validation
            if (fieldId === '#ss_client_email' && value && !bookingForm.isValidEmail(value)) {
                bookingForm.showError(fieldId, studiosnap_booking.messages.invalid_email);
                return false;
            }
            
            // Phone validation
            if (fieldId === '#ss_client_phone' && value && !bookingForm.isValidPhone(value)) {
                bookingForm.showError(fieldId, studiosnap_booking.messages.invalid_phone);
                return false;
            }
            
            return true;
        },
        
        submitForm: function(e) {
            e.preventDefault();
            
            if (!bookingForm.validateForm()) {
                bookingForm.showMessage('error', 'Please correct the errors above and try again.');
                return false;
            }
            
            var $form = $(this);
            var $submitBtn = $('.ss-submit-btn');
            var $btnText = $('.ss-btn-text');
            var $btnLoading = $('.ss-btn-loading');
            
            // Disable form and show loading
            $submitBtn.prop('disabled', true);
            $btnText.hide();
            $btnLoading.show();
            
            // Serialize form data
            var formData = $form.serialize();
            formData += '&action=ss_submit_booking';
            
            $.ajax({
                url: studiosnap_booking.ajax_url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        bookingForm.showMessage('success', response.data.message);
                        $form[0].reset();
                        $('.ss-booking-summary').hide();
                        $('.ss-packages-display .ss-package-card').removeClass('selected');
                        
                        // Scroll to success message
                        $('html, body').animate({
                            scrollTop: $('.ss-success-message').offset().top - 100
                        }, 500);
                        
                    } else {
                        bookingForm.showMessage('error', response.data || studiosnap_booking.messages.booking_error);
                    }
                },
                error: function() {
                    bookingForm.showMessage('error', studiosnap_booking.messages.booking_error);
                },
                complete: function() {
                    // Re-enable form
                    $submitBtn.prop('disabled', false);
                    $btnText.show();
                    $btnLoading.hide();
                }
            });
            
            return false;
        },
        
        showError: function(fieldId, message) {
            var $field = $(fieldId);
            var $errorDiv = $field.closest('.ss-form-field').find('.ss-field-error');
            
            $field.addClass('error');
            $errorDiv.text(message).show();
        },
        
        clearError: function(fieldId) {
            var $field = $(fieldId);
            var $errorDiv = $field.closest('.ss-form-field').find('.ss-field-error');
            
            $field.removeClass('error');
            $errorDiv.hide().text('');
        },
        
        showMessage: function(type, message) {
            var $messageDiv = type === 'success' ? $('.ss-success-message') : $('.ss-error-message');
            var $otherDiv = type === 'success' ? $('.ss-error-message') : $('.ss-success-message');
            
            $otherDiv.hide();
            $messageDiv.text(message).show();
            
            // Auto-hide after 10 seconds
            setTimeout(function() {
                $messageDiv.fadeOut();
            }, 10000);
        },
        
        isValidEmail: function(email) {
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        },
        
        isValidPhone: function(phone) {
            // Basic phone validation - allows various formats
            var phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
            var cleanPhone = phone.replace(/[\s\-\(\)\.]/g, '');
            return phoneRegex.test(cleanPhone) && cleanPhone.length >= 10;
        }
    };
    
    // Initialize booking form
    if ($('.studiosnap-booking-form-container').length > 0) {
        bookingForm.init();
    }
    
    // Add CSS for error states
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            .ss-form-field input.error,
            .ss-form-field select.error {
                border-color: #e74c3c !important;
                box-shadow: 0 0 0 2px rgba(231, 76, 60, 0.1) !important;
            }
            
            .ss-field-error {
                animation: fadeIn 0.3s ease-in;
            }
            
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(-10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            .ss-booking-summary {
                animation: slideDown 0.5s ease-out;
            }
            
            @keyframes slideDown {
                from { opacity: 0; transform: translateY(-20px); }
                to { opacity: 1; transform: translateY(0); }
            }
        `)
        .appendTo('head');
});