/**
 * GymFlow Public JavaScript
 * 
 * Handles frontend interactions for shortcodes and public-facing elements
 * 
 * @package GymFlow
 * @version 1.0.0
 */

(function($) {
    'use strict';

    /**
     * Main GymFlow Public Object
     */
    const GymFlowPublic = {
        
        /**
         * Initialize public functionality
         */
        init: function() {
            this.bindEvents();
            this.initDatePickers();
            this.initFormValidation();
            this.initScheduleView();
            this.initBookingForms();
            this.initMemberDashboard();
        },

        /**
         * Bind global event handlers
         */
        bindEvents: function() {
            // Form submissions
            $(document).on('submit', '.gymflow-form', this.handleFormSubmission);
            
            // AJAX form submissions
            $(document).on('submit', '.gymflow-ajax-form', this.handleAjaxForm);
            
            // Booking actions
            $(document).on('click', '.gf-book-class-btn', this.handleClassBooking);
            $(document).on('click', '.gf-book-trainer-btn', this.handleTrainerBooking);
            $(document).on('click', '.gf-book-equipment', this.handleEquipmentBooking);
            
            // Time slot selection
            $(document).on('click', '.gf-time-slot', this.handleTimeSlotSelection);
            
            // Member actions
            $(document).on('click', '.gf-cancel-booking', this.handleBookingCancellation);
            
            // Schedule navigation
            $(document).on('click', '.gf-nav-prev, .gf-nav-next', this.handleScheduleNavigation);
            $(document).on('click', '.gf-schedule-view-toggle .gf-btn', this.handleViewToggle);
            
            // Quick actions
            $(document).on('click', '.gf-book-class', this.showClassBookingModal);
            $(document).on('click', '.gf-book-equipment', this.showEquipmentBookingModal);
            $(document).on('click', '.gf-book-training', this.showTrainingBookingModal);
            
            // Form field changes
            $(document).on('change', '#class_id, #equipment_id', this.handleItemSelection);
            $(document).on('change', '#booking_date', this.loadAvailableSlots);
            
            // Filter changes
            $(document).on('change', '#gf-category-filter', this.handleCategoryFilter);
            $(document).on('change', '#gf-booking-status-filter', this.handleStatusFilter);
        },

        /**
         * Initialize date pickers
         */
        initDatePickers: function() {
            if ($.fn.datepicker) {
                $('.gf-date-picker').datepicker({
                    dateFormat: 'yy-mm-dd',
                    minDate: 0,
                    changeMonth: true,
                    changeYear: true
                });
            }
        },

        /**
         * Initialize form validation
         */
        initFormValidation: function() {
            // Real-time validation
            $(document).on('blur', '.gf-form-field input[required]', this.validateField);
            $(document).on('blur', '.gf-form-field input[type="email"]', this.validateEmail);
            $(document).on('blur', '.gf-form-field input[type="tel"]', this.validatePhone);
        },

        /**
         * Initialize schedule view
         */
        initScheduleView: function() {
            if ($('.gf-schedule-grid').length > 0) {
                this.loadScheduleEvents();
            }
        },

        /**
         * Initialize booking forms
         */
        initBookingForms: function() {
            // Auto-populate member details if logged in
            this.populateMemberDetails();
            
            // Initialize equipment duration calculator
            this.initDurationCalculator();
        },

        /**
         * Initialize member dashboard
         */
        initMemberDashboard: function() {
            if ($('.gymflow-member-dashboard').length > 0) {
                this.loadDashboardData();
                
                // Refresh dashboard every 5 minutes
                setInterval(() => {
                    this.loadDashboardData();
                }, 300000);
            }
        },

        /**
         * Handle form submission
         */
        handleFormSubmission: function(e) {
            const $form = $(this);
            
            // Validate form
            if (!GymFlowPublic.validateForm($form)) {
                e.preventDefault();
                return false;
            }
            
            // Show loading state
            const $submit = $form.find('[type="submit"]');
            $submit.prop('disabled', true).addClass('loading');
            
            // Add loading text
            const originalText = $submit.text();
            $submit.data('original-text', originalText).text(gymflow_ajax.strings.loading);
        },

        /**
         * Handle AJAX form submission
         */
        handleAjaxForm: function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const $submit = $form.find('[type="submit"]');
            const originalText = $submit.text();
            
            // Validate form
            if (!GymFlowPublic.validateForm($form)) {
                return false;
            }
            
            // Show loading state
            $submit.prop('disabled', true).text(gymflow_ajax.strings.loading);
            
            $.ajax({
                url: gymflow_ajax.ajax_url,
                type: 'POST',
                data: $form.serialize(),
                success: function(response) {
                    if (response.success) {
                        GymFlowPublic.showMessage('success', response.data.message || gymflow_ajax.strings.success);
                        
                        // Reset form if specified
                        if (response.data.reset_form) {
                            $form[0].reset();
                        }
                        
                        // Redirect if specified
                        if (response.data.redirect_url) {
                            window.location.href = response.data.redirect_url;
                        }
                        
                        // Reload specific sections
                        if (response.data.reload_dashboard) {
                            GymFlowPublic.loadDashboardData();
                        }
                        
                        if (response.data.reload_schedule) {
                            GymFlowPublic.loadScheduleEvents();
                        }
                    } else {
                        GymFlowPublic.showMessage('error', response.data || gymflow_ajax.strings.error);
                    }
                },
                error: function() {
                    GymFlowPublic.showMessage('error', gymflow_ajax.strings.error);
                },
                complete: function() {
                    $submit.prop('disabled', false).text(originalText);
                }
            });
        },

        /**
         * Handle class booking button click
         */
        handleClassBooking: function(e) {
            e.preventDefault();
            
            const classId = $(this).data('class-id');
            
            // Redirect to booking form or show modal
            if (classId) {
                window.location.href = `#class-booking-form?class_id=${classId}`;
            } else {
                GymFlowPublic.showClassBookingModal();
            }
        },

        /**
         * Handle trainer booking button click
         */
        handleTrainerBooking: function(e) {
            e.preventDefault();
            
            const trainerId = $(this).data('trainer-id');
            
            if (trainerId) {
                window.location.href = `#trainer-booking-form?trainer_id=${trainerId}`;
            } else {
                GymFlowPublic.showTrainingBookingModal();
            }
        },

        /**
         * Handle equipment booking button click
         */
        handleEquipmentBooking: function(e) {
            e.preventDefault();
            
            const equipmentId = $(this).data('equipment-id');
            
            if (equipmentId) {
                window.location.href = `#equipment-booking-form?equipment_id=${equipmentId}`;
            } else {
                GymFlowPublic.showEquipmentBookingModal();
            }
        },

        /**
         * Handle time slot selection
         */
        handleTimeSlotSelection: function(e) {
            e.preventDefault();
            
            const $slot = $(this);
            
            if ($slot.hasClass('unavailable')) {
                return;
            }
            
            // Clear previous selections
            $('.gf-time-slot').removeClass('selected');
            
            // Select this slot
            $slot.addClass('selected');
            
            // Update hidden fields
            const scheduleId = $slot.data('schedule-id');
            const startTime = $slot.data('start-time');
            const endTime = $slot.data('end-time');
            
            $('input[name="schedule_id"]').val(scheduleId);
            $('input[name="start_time"]').val(startTime);
            $('input[name="end_time"]').val(endTime);
            
            // Enable submit button
            $('.gf-btn[type="submit"]').prop('disabled', false);
        },

        /**
         * Handle booking cancellation
         */
        handleBookingCancellation: function(e) {
            e.preventDefault();
            
            if (!confirm(gymflow_ajax.strings.cancel_booking)) {
                return;
            }
            
            const bookingId = $(this).data('booking-id');
            const $button = $(this);
            
            $button.prop('disabled', true);
            
            $.ajax({
                url: gymflow_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'gf_cancel_booking',
                    booking_id: bookingId,
                    nonce: gymflow_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        GymFlowPublic.showMessage('success', response.data.message);
                        $button.closest('tr').fadeOut();
                    } else {
                        GymFlowPublic.showMessage('error', response.data);
                    }
                },
                error: function() {
                    GymFlowPublic.showMessage('error', gymflow_ajax.strings.error);
                },
                complete: function() {
                    $button.prop('disabled', false);
                }
            });
        },

        /**
         * Handle schedule navigation
         */
        handleScheduleNavigation: function(e) {
            e.preventDefault();
            
            const direction = $(this).hasClass('gf-nav-prev') ? 'prev' : 'next';
            GymFlowPublic.navigateSchedule(direction);
        },

        /**
         * Handle view toggle
         */
        handleViewToggle: function(e) {
            e.preventDefault();
            
            const $btn = $(this);
            const view = $btn.data('view');
            
            // Update active state
            $('.gf-schedule-view-toggle .gf-btn').removeClass('active');
            $btn.addClass('active');
            
            // Update schedule view
            GymFlowPublic.setScheduleView(view);
        },

        /**
         * Handle item selection (class/equipment)
         */
        handleItemSelection: function() {
            const $select = $(this);
            const itemId = $select.val();
            const itemType = $select.attr('id');
            
            if (itemId && itemType === 'class_id') {
                // Load class details and available times
                GymFlowPublic.loadClassDetails(itemId);
            } else if (itemId && itemType === 'equipment_id') {
                // Load equipment details
                GymFlowPublic.loadEquipmentDetails(itemId);
            }
        },

        /**
         * Load available time slots
         */
        loadAvailableSlots: function() {
            const classId = $('#class_id').val();
            const date = $(this).val();
            
            if (!classId || !date) {
                return;
            }
            
            const $container = $('#available-times-container');
            const $timesContainer = $('#available-times');
            
            $timesContainer.html('<div class="gf-loading-spinner"></div>');
            $container.show();
            
            $.ajax({
                url: gymflow_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'gf_get_available_slots',
                    class_id: classId,
                    date: date,
                    nonce: gymflow_ajax.nonce
                },
                success: function(response) {
                    if (response.success && response.data.slots.length > 0) {
                        let slotsHtml = '';
                        
                        response.data.slots.forEach(slot => {
                            slotsHtml += `
                                <div class="gf-time-slot" 
                                     data-schedule-id="${slot.id}"
                                     data-start-time="${slot.time}"
                                     data-end-time="${slot.end_time}">
                                    <div class="slot-time">${slot.time}</div>
                                    <div class="slot-availability">${slot.available_spots} spots</div>
                                    ${slot.instructor ? `<div class="slot-instructor">${slot.instructor}</div>` : ''}
                                </div>
                            `;
                        });
                        
                        $timesContainer.html(slotsHtml);
                    } else {
                        $timesContainer.html('<p>No available time slots for this date.</p>');
                    }
                },
                error: function() {
                    $timesContainer.html('<p>Error loading time slots. Please try again.</p>');
                }
            });
        },

        /**
         * Handle category filter
         */
        handleCategoryFilter: function() {
            const category = $(this).val();
            // Implement category filtering logic
            console.log('Filter by category:', category);
        },

        /**
         * Handle status filter
         */
        handleStatusFilter: function() {
            const status = $(this).val();
            // Implement status filtering logic
            console.log('Filter by status:', status);
        },

        /**
         * Load schedule events
         */
        loadScheduleEvents: function() {
            const $grid = $('.gf-schedule-grid');
            
            if ($grid.length === 0) {
                return;
            }
            
            $grid.html('<div class="gf-loading">Loading schedule...</div>');
            
            const currentDate = $grid.data('current-date') || new Date().toISOString().split('T')[0];
            const view = $('.gf-schedule-view-toggle .gf-btn.active').data('view') || 'week';
            
            $.ajax({
                url: gymflow_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'gf_get_schedule_events',
                    date: currentDate,
                    view: view,
                    nonce: gymflow_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $grid.html(response.data.html);
                        $('.gf-current-period').text(response.data.period);
                    } else {
                        $grid.html('<p>Error loading schedule.</p>');
                    }
                },
                error: function() {
                    $grid.html('<p>Error loading schedule. Please refresh the page.</p>');
                }
            });
        },

        /**
         * Navigate schedule
         */
        navigateSchedule: function(direction) {
            const $grid = $('.gf-schedule-grid');
            const currentDate = new Date($grid.data('current-date') || new Date());
            const view = $('.gf-schedule-view-toggle .gf-btn.active').data('view') || 'week';
            
            // Calculate new date based on view and direction
            let newDate;
            if (view === 'day') {
                newDate = new Date(currentDate);
                newDate.setDate(currentDate.getDate() + (direction === 'next' ? 1 : -1));
            } else if (view === 'week') {
                newDate = new Date(currentDate);
                newDate.setDate(currentDate.getDate() + (direction === 'next' ? 7 : -7));
            } else if (view === 'month') {
                newDate = new Date(currentDate);
                newDate.setMonth(currentDate.getMonth() + (direction === 'next' ? 1 : -1));
            }
            
            $grid.data('current-date', newDate.toISOString().split('T')[0]);
            this.loadScheduleEvents();
        },

        /**
         * Set schedule view
         */
        setScheduleView: function(view) {
            const $grid = $('.gf-schedule-grid');
            $grid.data('view', view);
            this.loadScheduleEvents();
        },

        /**
         * Load dashboard data
         */
        loadDashboardData: function() {
            // Implementation for loading member dashboard data
            console.log('Loading dashboard data...');
        },

        /**
         * Load class details
         */
        loadClassDetails: function(classId) {
            // Implementation for loading class details
            console.log('Loading class details for:', classId);
        },

        /**
         * Load equipment details
         */
        loadEquipmentDetails: function(equipmentId) {
            // Implementation for loading equipment details
            console.log('Loading equipment details for:', equipmentId);
        },

        /**
         * Populate member details if logged in
         */
        populateMemberDetails: function() {
            // Check if member is logged in and populate form fields
            // This would integrate with the member session system
        },

        /**
         * Initialize duration calculator
         */
        initDurationCalculator: function() {
            $(document).on('change', '#start_time, #duration', function() {
                const startTime = $('#start_time').val();
                const duration = parseInt($('#duration').val());
                
                if (startTime && duration) {
                    const start = new Date(`2000-01-01 ${startTime}`);
                    const end = new Date(start.getTime() + duration * 60000);
                    const endTime = end.toTimeString().slice(0, 5);
                    
                    $('#end_time').val(endTime);
                }
            });
        },

        /**
         * Show booking modals
         */
        showClassBookingModal: function() {
            // Implementation for class booking modal
            console.log('Show class booking modal');
        },

        showEquipmentBookingModal: function() {
            // Implementation for equipment booking modal
            console.log('Show equipment booking modal');
        },

        showTrainingBookingModal: function() {
            // Implementation for training booking modal
            console.log('Show training booking modal');
        },

        /**
         * Form validation
         */
        validateForm: function($form) {
            let isValid = true;
            
            // Clear previous errors
            $form.find('.error').removeClass('error');
            $form.find('.error-message').remove();
            
            // Required field validation
            $form.find('[required]').each(function() {
                if (!GymFlowPublic.validateField.call(this)) {
                    isValid = false;
                }
            });
            
            // Email validation
            $form.find('[type="email"]').each(function() {
                if ($(this).val() && !GymFlowPublic.validateEmail.call(this)) {
                    isValid = false;
                }
            });
            
            return isValid;
        },

        /**
         * Validate individual field
         */
        validateField: function() {
            const $field = $(this);
            const value = $field.val().trim();
            const isRequired = $field.attr('required');
            
            if (isRequired && !value) {
                GymFlowPublic.showFieldError($field, gymflow_ajax.strings.required_field);
                return false;
            }
            
            GymFlowPublic.clearFieldError($field);
            return true;
        },

        /**
         * Validate email field
         */
        validateEmail: function() {
            const $field = $(this);
            const email = $field.val().trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email && !emailRegex.test(email)) {
                GymFlowPublic.showFieldError($field, gymflow_ajax.strings.invalid_email);
                return false;
            }
            
            GymFlowPublic.clearFieldError($field);
            return true;
        },

        /**
         * Validate phone field
         */
        validatePhone: function() {
            const $field = $(this);
            const phone = $field.val().trim();
            const phoneRegex = /^[\d\s\-\+\(\)]+$/;
            
            if (phone && !phoneRegex.test(phone)) {
                GymFlowPublic.showFieldError($field, 'Please enter a valid phone number.');
                return false;
            }
            
            GymFlowPublic.clearFieldError($field);
            return true;
        },

        /**
         * Show field error
         */
        showFieldError: function($field, message) {
            $field.addClass('error');
            $field.after(`<span class="error-message">${message}</span>`);
        },

        /**
         * Clear field error
         */
        clearFieldError: function($field) {
            $field.removeClass('error');
            $field.siblings('.error-message').remove();
        },

        /**
         * Show message
         */
        showMessage: function(type, message) {
            const $messageContainer = $('.gf-form-messages');
            
            if ($messageContainer.length === 0) {
                return;
            }
            
            const $message = $(`
                <div class="gf-message gf-message-${type}">
                    ${message}
                </div>
            `);
            
            $messageContainer.empty().append($message);
            
            // Auto-hide success messages
            if (type === 'success') {
                setTimeout(() => {
                    $message.fadeOut();
                }, 5000);
            }
            
            // Scroll to message
            $('html, body').animate({
                scrollTop: $messageContainer.offset().top - 100
            }, 500);
        },

        /**
         * Utility: Debounce function
         */
        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        /**
         * Utility: Format currency
         */
        formatCurrency: function(amount) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD'
            }).format(amount);
        },

        /**
         * Utility: Format date
         */
        formatDate: function(date) {
            return new Intl.DateTimeFormat('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            }).format(new Date(date));
        },

        /**
         * Utility: Format time
         */
        formatTime: function(time) {
            return new Intl.DateTimeFormat('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            }).format(new Date(`2000-01-01 ${time}`));
        }
    };

    /**
     * Initialize when document is ready
     */
    $(document).ready(function() {
        GymFlowPublic.init();
    });

    // Make GymFlowPublic globally available
    window.GymFlowPublic = GymFlowPublic;

})(jQuery);