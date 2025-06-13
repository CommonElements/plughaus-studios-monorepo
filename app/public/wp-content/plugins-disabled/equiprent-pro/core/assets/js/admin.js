/**
 * EquipRent Pro Admin JavaScript
 */

(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        EquipRentAdmin.init();
    });

    /**
     * Main admin object
     */
    window.EquipRentAdmin = {

        /**
         * Initialize admin functionality
         */
        init: function() {
            this.initDashboard();
            this.initEquipmentForm();
            this.initBookingForm();
            this.initCustomerForm();
            this.initDatePickers();
            this.initAjaxHandlers();
            this.initConfirmDialogs();
        },

        /**
         * Initialize dashboard widgets
         */
        initDashboard: function() {
            // Auto-refresh dashboard widgets every 5 minutes
            if ($('.equiprent-dashboard-widgets').length) {
                setInterval(function() {
                    EquipRentAdmin.refreshDashboardWidgets();
                }, 300000); // 5 minutes
            }
        },

        /**
         * Initialize equipment form functionality
         */
        initEquipmentForm: function() {
            // Auto-generate SKU if empty
            $('#title').on('blur', function() {
                var title = $(this).val();
                var skuField = $('#equipment_sku');
                
                if (title && !skuField.val()) {
                    var sku = title.toUpperCase()
                        .replace(/[^A-Z0-9]/g, '')
                        .substring(0, 10);
                    skuField.val(sku);
                }
            });

            // Calculate weekly/monthly rates based on daily rate
            $('#equipment_daily_rate').on('change', function() {
                var dailyRate = parseFloat($(this).val()) || 0;
                var weeklyField = $('#equipment_weekly_rate');
                var monthlyField = $('#equipment_monthly_rate');

                if (dailyRate > 0) {
                    if (!weeklyField.val()) {
                        weeklyField.val((dailyRate * 6).toFixed(2)); // 6 days for weekly discount
                    }
                    if (!monthlyField.val()) {
                        monthlyField.val((dailyRate * 25).toFixed(2)); // 25 days for monthly discount
                    }
                }
            });

            // Validate numeric fields
            $('.equiprent-numeric').on('input', function() {
                var value = $(this).val();
                if (value && !/^\d*\.?\d*$/.test(value)) {
                    $(this).addClass('error');
                } else {
                    $(this).removeClass('error');
                }
            });
        },

        /**
         * Initialize booking form functionality
         */
        initBookingForm: function() {
            // Date validation
            $('#booking_start_date, #booking_end_date').on('change', function() {
                EquipRentAdmin.validateBookingDates();
                EquipRentAdmin.calculateBookingTotal();
            });

            // Equipment selection change
            $(document).on('change', '.booking-equipment-select', function() {
                EquipRentAdmin.calculateBookingTotal();
                EquipRentAdmin.checkEquipmentAvailability();
            });

            // Add equipment item
            $(document).on('click', '.add-equipment-item', function(e) {
                e.preventDefault();
                EquipRentAdmin.addEquipmentItem();
            });

            // Remove equipment item
            $(document).on('click', '.remove-equipment-item', function(e) {
                e.preventDefault();
                $(this).closest('.equipment-item-row').remove();
                EquipRentAdmin.calculateBookingTotal();
            });

            // Customer search
            $('#customer_search').on('keyup', function() {
                EquipRentAdmin.searchCustomers($(this).val());
            });
        },

        /**
         * Initialize customer form functionality
         */
        initCustomerForm: function() {
            // Toggle business fields
            $('#customer_type').on('change', function() {
                var isIndividual = $(this).val() === 'individual';
                $('.business-fields').toggle(!isIndividual);
                $('.individual-fields').toggle(isIndividual);
            }).trigger('change');

            // Format phone number
            $('#customer_phone, #customer_alt_phone').on('input', function() {
                var value = $(this).val().replace(/\D/g, '');
                if (value.length >= 10) {
                    var formatted = value.substring(0, 3) + '-' + 
                                  value.substring(3, 6) + '-' + 
                                  value.substring(6, 10);
                    $(this).val(formatted);
                }
            });
        },

        /**
         * Initialize date pickers
         */
        initDatePickers: function() {
            // Set minimum date to today for future bookings
            $('input[type="date"]').each(function() {
                if ($(this).hasClass('future-date-only')) {
                    var today = new Date().toISOString().split('T')[0];
                    $(this).attr('min', today);
                }
            });
        },

        /**
         * Initialize AJAX handlers
         */
        initAjaxHandlers: function() {
            // Global AJAX error handler
            $(document).ajaxError(function(event, xhr, settings, error) {
                if (xhr.status !== 0) { // Ignore aborted requests
                    EquipRentAdmin.showNotice('error', equiprent_admin.strings.error);
                }
            });

            // Show loading indicator for AJAX requests
            $(document).ajaxStart(function() {
                $('.equiprent-loading-indicator').show();
            }).ajaxStop(function() {
                $('.equiprent-loading-indicator').hide();
            });
        },

        /**
         * Initialize confirmation dialogs
         */
        initConfirmDialogs: function() {
            $(document).on('click', '.confirm-delete', function(e) {
                if (!confirm(equiprent_admin.strings.confirm_delete)) {
                    e.preventDefault();
                    return false;
                }
            });
        },

        /**
         * Validate booking dates
         */
        validateBookingDates: function() {
            var startDate = $('#booking_start_date').val();
            var endDate = $('#booking_end_date').val();
            var errorDiv = $('#booking-date-error');

            if (startDate && endDate) {
                var start = new Date(startDate);
                var end = new Date(endDate);
                var today = new Date();
                today.setHours(0, 0, 0, 0);

                if (start < today) {
                    errorDiv.text('Start date cannot be in the past.').show();
                    return false;
                } else if (end <= start) {
                    errorDiv.text('End date must be after start date.').show();
                    return false;
                } else {
                    errorDiv.hide();
                    return true;
                }
            }

            errorDiv.hide();
            return true;
        },

        /**
         * Calculate booking total
         */
        calculateBookingTotal: function() {
            var startDate = $('#booking_start_date').val();
            var endDate = $('#booking_end_date').val();
            var totalDiv = $('#booking-total-calculation');

            if (!startDate || !endDate || !EquipRentAdmin.validateBookingDates()) {
                totalDiv.html('<p>Please select valid dates.</p>');
                return;
            }

            var days = Math.ceil((new Date(endDate) - new Date(startDate)) / (1000 * 60 * 60 * 24));
            var subtotal = 0;

            $('.equipment-item-row').each(function() {
                var rate = parseFloat($(this).find('.equipment-daily-rate').val()) || 0;
                var quantity = parseInt($(this).find('.equipment-quantity').val()) || 1;
                subtotal += rate * quantity * days;
            });

            var taxRate = parseFloat(equiprent_admin.tax_rate || 0);
            var taxAmount = subtotal * (taxRate / 100);
            var total = subtotal + taxAmount;

            var html = '<div class="booking-calculation">';
            html += '<p><strong>Days:</strong> ' + days + '</p>';
            html += '<p><strong>Subtotal:</strong> ' + EquipRentAdmin.formatCurrency(subtotal) + '</p>';
            if (taxAmount > 0) {
                html += '<p><strong>Tax (' + taxRate + '%):</strong> ' + EquipRentAdmin.formatCurrency(taxAmount) + '</p>';
            }
            html += '<p class="total"><strong>Total:</strong> ' + EquipRentAdmin.formatCurrency(total) + '</p>';
            html += '</div>';

            totalDiv.html(html);
        },

        /**
         * Check equipment availability
         */
        checkEquipmentAvailability: function() {
            var startDate = $('#booking_start_date').val();
            var endDate = $('#booking_end_date').val();

            if (!startDate || !endDate) {
                return;
            }

            $('.equipment-item-row').each(function() {
                var row = $(this);
                var equipmentId = row.find('.booking-equipment-select').val();
                var quantity = parseInt(row.find('.equipment-quantity').val()) || 1;

                if (equipmentId) {
                    $.ajax({
                        url: equiprent_admin.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'check_equipment_availability',
                            equipment_id: equipmentId,
                            start_date: startDate,
                            end_date: endDate,
                            quantity: quantity,
                            nonce: equiprent_admin.nonce
                        },
                        success: function(response) {
                            if (response.success) {
                                row.find('.availability-status').removeClass('unavailable').addClass('available')
                                   .text('Available (' + response.data.available + ' units)');
                            } else {
                                row.find('.availability-status').removeClass('available').addClass('unavailable')
                                   .text('Not available');
                            }
                        }
                    });
                }
            });
        },

        /**
         * Add equipment item to booking
         */
        addEquipmentItem: function() {
            var template = $('.equipment-item-template').clone();
            template.removeClass('equipment-item-template').addClass('equipment-item-row').show();
            $('.equipment-items-container').append(template);
        },

        /**
         * Search customers
         */
        searchCustomers: function(query) {
            if (query.length < 2) {
                $('#customer-search-results').hide();
                return;
            }

            $.ajax({
                url: equiprent_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'search_customers',
                    query: query,
                    nonce: equiprent_admin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        var html = '';
                        $.each(response.data, function(i, customer) {
                            html += '<div class="customer-result" data-customer-id="' + customer.id + '">';
                            html += '<strong>' + customer.name + '</strong><br>';
                            html += customer.email + ' | ' + customer.phone;
                            html += '</div>';
                        });
                        $('#customer-search-results').html(html).show();
                    }
                }
            });
        },

        /**
         * Refresh dashboard widgets
         */
        refreshDashboardWidgets: function() {
            $.ajax({
                url: equiprent_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'refresh_dashboard_widgets',
                    nonce: equiprent_admin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $('.equiprent-dashboard-widgets').html(response.data.html);
                    }
                }
            });
        },

        /**
         * Format currency
         */
        formatCurrency: function(amount) {
            var symbol = equiprent_admin.currency_symbol || '$';
            var formatted = parseFloat(amount).toFixed(2);
            return symbol + formatted;
        },

        /**
         * Show admin notice
         */
        showNotice: function(type, message) {
            var noticeClass = 'notice-' + type;
            var notice = $('<div class="notice ' + noticeClass + ' is-dismissible"><p>' + message + '</p></div>');
            $('.wrap > h1').after(notice);
            
            // Auto-dismiss after 5 seconds
            setTimeout(function() {
                notice.fadeOut();
            }, 5000);
        },

        /**
         * Show loading state
         */
        showLoading: function(element) {
            $(element).addClass('equiprent-loading');
        },

        /**
         * Hide loading state
         */
        hideLoading: function(element) {
            $(element).removeClass('equiprent-loading');
        }
    };

    // Global helper functions
    window.equiprentConfirm = function(message, callback) {
        if (confirm(message)) {
            if (typeof callback === 'function') {
                callback();
            }
            return true;
        }
        return false;
    };

    window.equiprentAlert = function(message, type) {
        EquipRentAdmin.showNotice(type || 'info', message);
    };

})(jQuery);