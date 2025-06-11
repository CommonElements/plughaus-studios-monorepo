/**
 * Admin JavaScript for Knot4
 * 
 * @package Knot4
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Knot4 Admin Object
    var Knot4Admin = {
        
        /**
         * Initialize admin functionality
         */
        init: function() {
            this.bindEvents();
            this.initDashboard();
            this.initForms();
            this.initTables();
            this.initNotifications();
        },

        /**
         * Bind global admin events
         */
        bindEvents: function() {
            // Form submissions
            $(document).on('submit', '.knot4-admin-form', this.handleFormSubmission);
            
            // Bulk actions
            $(document).on('click', '.knot4-bulk-action', this.handleBulkAction);
            
            // Delete confirmations
            $(document).on('click', '.knot4-delete-link', this.confirmDelete);
            
            // Tab navigation
            $(document).on('click', '.knot4-nav-tab', this.handleTabNavigation);
            
            // Settings form auto-save
            $(document).on('change', '.knot4-auto-save', this.debounce(this.autoSaveSettings, 1000));
            
            // Chart refresh
            $(document).on('click', '.knot4-refresh-chart', this.refreshChart);
            
            // Export actions
            $(document).on('click', '.knot4-export-btn', this.handleExport);
            
            // Import actions
            $(document).on('click', '.knot4-import-btn', this.handleImport);
        },

        /**
         * Initialize dashboard functionality
         */
        initDashboard: function() {
            // Auto-refresh dashboard data every 5 minutes
            if ($('.knot4-dashboard').length > 0) {
                setInterval(this.refreshDashboardStats, 300000); // 5 minutes
            }
            
            // Initialize tooltips
            this.initTooltips();
        },

        /**
         * Initialize form functionality
         */
        initForms: function() {
            // Initialize date pickers
            if ($.fn.datepicker) {
                $('.knot4-datepicker').datepicker({
                    dateFormat: 'yy-mm-dd',
                    changeMonth: true,
                    changeYear: true
                });
            }
            
            // Initialize color pickers
            if ($.fn.wpColorPicker) {
                $('.knot4-color-picker').wpColorPicker();
            }
            
            // Initialize media uploaders
            this.initMediaUploaders();
            
            // Form validation
            this.initFormValidation();
        },

        /**
         * Initialize table functionality
         */
        initTables: function() {
            // Make tables sortable if DataTables is available
            if ($.fn.DataTable) {
                $('.knot4-data-table').DataTable({
                    responsive: true,
                    pageLength: 25,
                    order: [[0, 'desc']],
                    language: {
                        search: knot4_admin.strings.search || 'Search:',
                        lengthMenu: 'Show _MENU_ entries',
                        info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                        paginate: {
                            first: 'First',
                            last: 'Last',
                            next: 'Next',
                            previous: 'Previous'
                        }
                    }
                });
            }
            
            // Row selection
            $(document).on('click', '.knot4-table-row', this.handleRowSelection);
        },

        /**
         * Initialize notifications
         */
        initNotifications: function() {
            // Auto-hide success notifications
            setTimeout(function() {
                $('.knot4-admin-notice.success').fadeOut();
            }, 5000);
            
            // Dismiss buttons
            $(document).on('click', '.knot4-notice-dismiss', function() {
                $(this).closest('.knot4-admin-notice').fadeOut();
            });
        },

        /**
         * Handle form submissions
         */
        handleFormSubmission: function(e) {
            var $form = $(this);
            var $submitBtn = $form.find('input[type="submit"], button[type="submit"]');
            var originalText = $submitBtn.val() || $submitBtn.text();
            
            // Validate form
            if (!Knot4Admin.validateForm($form)) {
                e.preventDefault();
                return false;
            }
            
            // Show loading state
            $submitBtn.prop('disabled', true);
            if ($submitBtn.is('input')) {
                $submitBtn.val(knot4_admin.strings.saving || 'Saving...');
            } else {
                $submitBtn.text(knot4_admin.strings.saving || 'Saving...');
            }
            
            // If AJAX form, handle via AJAX
            if ($form.hasClass('knot4-ajax-form')) {
                e.preventDefault();
                
                $.ajax({
                    url: knot4_admin.ajax_url,
                    type: 'POST',
                    data: $form.serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Knot4Admin.showNotification('success', response.data.message);
                        } else {
                            Knot4Admin.showNotification('error', response.data.message);
                        }
                    },
                    error: function() {
                        Knot4Admin.showNotification('error', knot4_admin.strings.error);
                    },
                    complete: function() {
                        $submitBtn.prop('disabled', false);
                        if ($submitBtn.is('input')) {
                            $submitBtn.val(originalText);
                        } else {
                            $submitBtn.text(originalText);
                        }
                    }
                });
                
                return false;
            }
        },

        /**
         * Handle bulk actions
         */
        handleBulkAction: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var action = $button.data('action');
            var $table = $button.closest('.knot4-admin-table');
            var $selected = $table.find('input[type="checkbox"]:checked');
            
            if ($selected.length === 0) {
                alert('Please select items to perform this action.');
                return;
            }
            
            // Confirm dangerous actions
            if (['delete', 'remove'].includes(action)) {
                if (!confirm('Are you sure you want to ' + action + ' the selected items?')) {
                    return;
                }
            }
            
            var ids = [];
            $selected.each(function() {
                if ($(this).val() !== 'on') {
                    ids.push($(this).val());
                }
            });
            
            // Send AJAX request
            $.ajax({
                url: knot4_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'knot4_bulk_action',
                    bulk_action: action,
                    ids: ids,
                    nonce: knot4_admin.nonce
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        Knot4Admin.showNotification('error', response.data.message);
                    }
                },
                error: function() {
                    Knot4Admin.showNotification('error', knot4_admin.strings.error);
                }
            });
        },

        /**
         * Confirm delete actions
         */
        confirmDelete: function(e) {
            if (!confirm(knot4_admin.strings.confirm_delete || 'Are you sure you want to delete this?')) {
                e.preventDefault();
                return false;
            }
        },

        /**
         * Handle tab navigation
         */
        handleTabNavigation: function(e) {
            e.preventDefault();
            
            var $tab = $(this);
            var targetTab = $tab.attr('href').substring(1);
            
            // Update active tab
            $tab.closest('.nav-tab-wrapper').find('.nav-tab').removeClass('nav-tab-active');
            $tab.addClass('nav-tab-active');
            
            // Show target tab content
            $('.knot4-tab-content').hide();
            $('#' + targetTab).show();
            
            // Update URL hash
            if (history.pushState) {
                history.pushState(null, null, '#' + targetTab);
            }
        },

        /**
         * Auto-save settings
         */
        autoSaveSettings: function() {
            var $field = $(this);
            var $form = $field.closest('form');
            var fieldName = $field.attr('name');
            var fieldValue = $field.val();
            
            $.ajax({
                url: knot4_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'knot4_auto_save_setting',
                    field_name: fieldName,
                    field_value: fieldValue,
                    nonce: knot4_admin.nonce
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $field.addClass('knot4-auto-saved');
                        setTimeout(function() {
                            $field.removeClass('knot4-auto-saved');
                        }, 2000);
                    }
                }
            });
        },

        /**
         * Refresh dashboard statistics
         */
        refreshDashboardStats: function() {
            $.ajax({
                url: knot4_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'knot4_refresh_dashboard_stats',
                    nonce: knot4_admin.nonce
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Update stat cards
                        $.each(response.data.stats, function(key, value) {
                            $('.knot4-stat-' + key + ' h3').text(value);
                        });
                        
                        // Update charts if needed
                        if (response.data.charts) {
                            Knot4Admin.updateCharts(response.data.charts);
                        }
                    }
                }
            });
        },

        /**
         * Refresh chart data
         */
        refreshChart: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var chartId = $button.data('chart');
            
            $button.prop('disabled', true);
            
            $.ajax({
                url: knot4_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'knot4_refresh_chart',
                    chart_id: chartId,
                    nonce: knot4_admin.nonce
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Update chart with new data
                        if (window.Chart && window[chartId + 'Chart']) {
                            window[chartId + 'Chart'].data = response.data.chart_data;
                            window[chartId + 'Chart'].update();
                        }
                    }
                },
                complete: function() {
                    $button.prop('disabled', false);
                }
            });
        },

        /**
         * Handle export actions
         */
        handleExport: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var exportType = $button.data('export');
            var $form = $button.closest('form');
            
            // Get export parameters
            var exportData = {
                action: 'knot4_export_data',
                export_type: exportType,
                nonce: knot4_admin.nonce
            };
            
            if ($form.length > 0) {
                exportData = $.extend(exportData, Knot4Admin.serializeForm($form));
            }
            
            // Create download link
            var downloadUrl = knot4_admin.ajax_url + '?' + $.param(exportData);
            window.open(downloadUrl, '_blank');
        },

        /**
         * Handle import actions
         */
        handleImport: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var $fileInput = $button.siblings('input[type="file"]');
            
            if ($fileInput[0].files.length === 0) {
                alert('Please select a file to import.');
                return;
            }
            
            var formData = new FormData();
            formData.append('action', 'knot4_import_data');
            formData.append('import_file', $fileInput[0].files[0]);
            formData.append('nonce', knot4_admin.nonce);
            
            $button.prop('disabled', true).text('Importing...');
            
            $.ajax({
                url: knot4_admin.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Knot4Admin.showNotification('success', response.data.message);
                        location.reload();
                    } else {
                        Knot4Admin.showNotification('error', response.data.message);
                    }
                },
                error: function() {
                    Knot4Admin.showNotification('error', 'Import failed.');
                },
                complete: function() {
                    $button.prop('disabled', false).text('Import');
                }
            });
        },

        /**
         * Initialize media uploaders
         */
        initMediaUploaders: function() {
            if (typeof wp !== 'undefined' && wp.media) {
                $('.knot4-media-upload').on('click', function(e) {
                    e.preventDefault();
                    
                    var $button = $(this);
                    var $input = $button.siblings('input[type="hidden"]');
                    var $preview = $button.siblings('.knot4-media-preview');
                    
                    var mediaUploader = wp.media({
                        title: 'Select Image',
                        button: {
                            text: 'Use this image'
                        },
                        multiple: false
                    });
                    
                    mediaUploader.on('select', function() {
                        var attachment = mediaUploader.state().get('selection').first().toJSON();
                        $input.val(attachment.id);
                        $preview.html('<img src="' + attachment.url + '" style="max-width: 150px; height: auto;">');
                    });
                    
                    mediaUploader.open();
                });
                
                $('.knot4-media-remove').on('click', function(e) {
                    e.preventDefault();
                    
                    var $button = $(this);
                    var $input = $button.siblings('input[type="hidden"]');
                    var $preview = $button.siblings('.knot4-media-preview');
                    
                    $input.val('');
                    $preview.empty();
                });
            }
        },

        /**
         * Initialize form validation
         */
        initFormValidation: function() {
            // Add validation classes
            $('input[required], textarea[required], select[required]').addClass('knot4-required');
            
            // Real-time validation
            $(document).on('blur', '.knot4-required', function() {
                Knot4Admin.validateField($(this));
            });
        },

        /**
         * Initialize tooltips
         */
        initTooltips: function() {
            if ($.fn.tooltip) {
                $('.knot4-tooltip').tooltip({
                    position: { my: "center bottom-20", at: "center top", using: function( position, feedback ) {
                        $( this ).css( position );
                        $( "<div>" )
                            .addClass( "arrow" )
                            .addClass( feedback.vertical )
                            .addClass( feedback.horizontal )
                            .appendTo( this );
                    } }
                });
            }
        },

        /**
         * Handle row selection
         */
        handleRowSelection: function(e) {
            if (e.target.type === 'checkbox') {
                return;
            }
            
            var $row = $(this);
            var $checkbox = $row.find('input[type="checkbox"]');
            
            $checkbox.prop('checked', !$checkbox.prop('checked'));
            $row.toggleClass('selected', $checkbox.prop('checked'));
        },

        /**
         * Validate form
         */
        validateForm: function($form) {
            var isValid = true;
            
            $form.find('.knot4-required').each(function() {
                if (!Knot4Admin.validateField($(this))) {
                    isValid = false;
                }
            });
            
            return isValid;
        },

        /**
         * Validate individual field
         */
        validateField: function($field) {
            var value = $field.val().trim();
            var isValid = true;
            
            // Remove previous error styling
            $field.removeClass('knot4-field-error');
            
            // Check required
            if ($field.hasClass('knot4-required') && !value) {
                isValid = false;
            }
            
            // Email validation
            if ($field.attr('type') === 'email' && value) {
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    isValid = false;
                }
            }
            
            // URL validation
            if ($field.attr('type') === 'url' && value) {
                var urlRegex = /^https?:\/\/.+/;
                if (!urlRegex.test(value)) {
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
            
            // Add error styling
            if (!isValid) {
                $field.addClass('knot4-field-error');
            }
            
            return isValid;
        },

        /**
         * Show notification
         */
        showNotification: function(type, message) {
            var $notice = $('<div class="knot4-admin-notice ' + type + '">' + message + '</div>');
            
            $('.wrap').prepend($notice);
            
            if (type === 'success') {
                setTimeout(function() {
                    $notice.fadeOut();
                }, 5000);
            }
        },

        /**
         * Serialize form to object
         */
        serializeForm: function($form) {
            var data = {};
            $form.serializeArray().forEach(function(item) {
                if (data[item.name]) {
                    if (!Array.isArray(data[item.name])) {
                        data[item.name] = [data[item.name]];
                    }
                    data[item.name].push(item.value);
                } else {
                    data[item.name] = item.value;
                }
            });
            return data;
        },

        /**
         * Update charts with new data
         */
        updateCharts: function(chartData) {
            if (typeof Chart !== 'undefined') {
                $.each(chartData, function(chartId, data) {
                    if (window[chartId + 'Chart']) {
                        window[chartId + 'Chart'].data = data;
                        window[chartId + 'Chart'].update();
                    }
                });
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
        .html('.knot4-field-error { border-color: #dc3545 !important; box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.25) !important; } .knot4-auto-saved { border-color: #28a745 !important; }')
        .appendTo('head');

    // Initialize when document is ready
    $(document).ready(function() {
        Knot4Admin.init();
        
        // Handle tab navigation from URL hash
        if (window.location.hash) {
            var $tab = $('.nav-tab[href="' + window.location.hash + '"]');
            if ($tab.length) {
                $tab.trigger('click');
            }
        }
    });

    // Make Knot4Admin globally available
    window.Knot4Admin = Knot4Admin;

})(jQuery);