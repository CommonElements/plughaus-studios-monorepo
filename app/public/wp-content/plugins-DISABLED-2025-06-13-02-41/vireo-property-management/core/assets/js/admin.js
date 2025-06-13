/**
 * PlugHaus Property Management - Admin JavaScript
 */

(function($) {
    'use strict';

    // Wait for DOM ready
    $(document).ready(function() {
        
        // Initialize dashboard widgets
        initDashboardWidgets();
        
        // Initialize property meta boxes
        initPropertyMetaBoxes();
        
        // Initialize import/export
        initImportExport();
        
        // Initialize reports
        initReports();
        
        // Initialize license management
        initLicenseManagement();
        
    });
    
    /**
     * Initialize dashboard widgets
     */
    function initDashboardWidgets() {
        // Refresh dashboard stats
        $('.phpm-refresh-stats').on('click', function(e) {
            e.preventDefault();
            var $button = $(this);
            var $widget = $button.closest('.phpm-widget');
            
            $button.prop('disabled', true);
            $widget.find('.phpm-stats-grid').css('opacity', '0.5');
            
            $.ajax({
                url: phpm_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'phpm_refresh_dashboard_stats',
                    nonce: phpm_admin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $widget.find('.phpm-stats-grid').html(response.data.html);
                    }
                },
                complete: function() {
                    $button.prop('disabled', false);
                    $widget.find('.phpm-stats-grid').css('opacity', '1');
                }
            });
        });
    }
    
    /**
     * Initialize property meta boxes
     */
    function initPropertyMetaBoxes() {
        // Unit number change handler
        $('#phpm_property_units').on('change', function() {
            var units = parseInt($(this).val());
            if (units > 0) {
                // Could trigger unit creation UI here
                console.log('Property will have ' + units + ' units');
            }
        });
        
        // Address autocomplete (if using Google Places API)
        if (typeof google !== 'undefined' && google.maps && google.maps.places) {
            var addressInput = document.getElementById('phpm_property_address');
            if (addressInput) {
                var autocomplete = new google.maps.places.Autocomplete(addressInput);
                
                autocomplete.addListener('place_changed', function() {
                    var place = autocomplete.getPlace();
                    
                    // Parse address components
                    for (var i = 0; i < place.address_components.length; i++) {
                        var component = place.address_components[i];
                        
                        if (component.types.includes('locality')) {
                            $('#phpm_property_city').val(component.long_name);
                        }
                        if (component.types.includes('administrative_area_level_1')) {
                            $('#phpm_property_state').val(component.short_name);
                        }
                        if (component.types.includes('postal_code')) {
                            $('#phpm_property_zip').val(component.short_name);
                        }
                    }
                });
            }
        }
    }
    
    /**
     * Initialize import/export functionality
     */
    function initImportExport() {
        // Import form validation
        $('form[name="phpm_import"]').on('submit', function(e) {
            var fileInput = $('#import_file')[0];
            
            if (!fileInput.files.length) {
                e.preventDefault();
                alert(phpm_admin.strings.error + ' Please select a file to import.');
                return false;
            }
            
            var file = fileInput.files[0];
            var fileExt = file.name.split('.').pop().toLowerCase();
            
            if (fileExt !== 'csv') {
                e.preventDefault();
                alert(phpm_admin.strings.error + ' Please select a valid CSV file.');
                return false;
            }
            
            // Show loading state
            $(this).find('input[type="submit"]').val(phpm_admin.strings.saving + '...').prop('disabled', true);
        });
        
        // Export handler
        $('form[name="phpm_export"]').on('submit', function(e) {
            // Show loading state
            $(this).find('input[type="submit"]').val('Generating...').prop('disabled', true);
            
            // Re-enable after a delay (form will submit and page will reload)
            setTimeout(function() {
                $('form[name="phpm_export"] input[type="submit"]').val('Export Data').prop('disabled', false);
            }, 3000);
        });
    }
    
    /**
     * Initialize reports
     */
    function initReports() {
        // Report generation
        $('.phpm-generate-report').on('click', function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var reportType = $button.data('report-type');
            var $card = $button.closest('.phpm-report-card');
            
            $button.text('Generating...').prop('disabled', true);
            
            $.ajax({
                url: phpm_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'phpm_generate_report',
                    report_type: reportType,
                    nonce: phpm_admin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Open report in new window or show in modal
                        if (response.data.url) {
                            window.open(response.data.url, '_blank');
                        } else if (response.data.html) {
                            showReportModal(response.data.html);
                        }
                    } else {
                        alert(response.data.message || phpm_admin.strings.error);
                    }
                },
                error: function() {
                    alert(phpm_admin.strings.error);
                },
                complete: function() {
                    $button.text('Generate Report').prop('disabled', false);
                }
            });
        });
    }
    
    /**
     * Show report in modal
     */
    function showReportModal(html) {
        // Simple modal implementation
        var $modal = $('<div class="phpm-modal-overlay">' +
            '<div class="phpm-modal">' +
                '<div class="phpm-modal-header">' +
                    '<h2>Report</h2>' +
                    '<button class="phpm-modal-close">&times;</button>' +
                '</div>' +
                '<div class="phpm-modal-content">' + html + '</div>' +
                '<div class="phpm-modal-footer">' +
                    '<button class="button phpm-print-report">Print</button>' +
                    '<button class="button phpm-download-report">Download</button>' +
                    '<button class="button phpm-modal-close">Close</button>' +
                '</div>' +
            '</div>' +
        '</div>');
        
        $('body').append($modal);
        
        // Close handlers
        $modal.on('click', '.phpm-modal-close', function() {
            $modal.remove();
        });
        
        // Print handler
        $modal.on('click', '.phpm-print-report', function() {
            window.print();
        });
        
        // Download handler
        $modal.on('click', '.phpm-download-report', function() {
            // Implement download functionality
            var content = $modal.find('.phpm-modal-content').html();
            downloadReport(content);
        });
    }
    
    /**
     * Download report as file
     */
    function downloadReport(content) {
        var blob = new Blob([content], { type: 'text/html' });
        var url = window.URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = 'property-report-' + Date.now() + '.html';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    }
    
    /**
     * Settings page enhancements
     */
    if ($('body').hasClass('phpm_page_phpm-settings')) {
        // Email notification checkboxes - select all/none
        var $emailCheckboxes = $('input[name="phpm_settings[email_notifications][]"]');
        
        if ($emailCheckboxes.length > 0) {
            $('<p><a href="#" id="phpm-select-all-notifications">Select All</a> | <a href="#" id="phpm-select-none-notifications">Select None</a></p>')
                .insertBefore($emailCheckboxes.first().closest('label'));
            
            $('#phpm-select-all-notifications').on('click', function(e) {
                e.preventDefault();
                $emailCheckboxes.prop('checked', true);
            });
            
            $('#phpm-select-none-notifications').on('click', function(e) {
                e.preventDefault();
                $emailCheckboxes.prop('checked', false);
            });
        }
    }
    
    /**
     * License management functionality
     */
    function initLicenseManagement() {
        // License validation button
        $('#phpm-validate-license').on('click', function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var licenseKey = $('#phpm_license_key').val();
            
            if (!licenseKey || licenseKey.indexOf('*') !== -1) {
                alert(vmp_admin.strings.enter_license_key || 'Please enter a valid license key.');
                return;
            }
            
            $button.prop('disabled', true).text(vmp_admin.strings.validating || 'Validating...');
            
            $.ajax({
                url: vmp_admin.api_url + '../phls/v1/validate',
                type: 'POST',
                data: {
                    license_key: licenseKey
                },
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', vmp_admin.nonce);
                },
                success: function(response) {
                    if (response.success) {
                        showLicenseMessage('success', response.message);
                        // Reload page to show updated license status
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        showLicenseMessage('error', response.message || 'License validation failed.');
                    }
                },
                error: function(xhr, status, error) {
                    var errorMessage = 'License validation failed.';
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {
                        // Use default error message
                    }
                    showLicenseMessage('error', errorMessage);
                },
                complete: function() {
                    $button.prop('disabled', false).text(vmp_admin.strings.validate_license || 'Validate License');
                }
            });
        });
        
        // License deactivation button
        $('#phpm-deactivate-license').on('click', function(e) {
            e.preventDefault();
            
            if (!confirm(vmp_admin.strings.confirm_deactivate || 'Are you sure you want to deactivate your license?')) {
                return;
            }
            
            var $button = $(this);
            $button.prop('disabled', true).text(vmp_admin.strings.deactivating || 'Deactivating...');
            
            $.ajax({
                url: vmp_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'phpm_deactivate_license',
                    nonce: vmp_admin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        showLicenseMessage('success', response.data.message);
                        // Reload page to show updated license status
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        showLicenseMessage('error', response.data.message || 'License deactivation failed.');
                    }
                },
                error: function() {
                    showLicenseMessage('error', 'License deactivation failed.');
                },
                complete: function() {
                    $button.prop('disabled', false).text(vmp_admin.strings.deactivate_license || 'Deactivate License');
                }
            });
        });
        
        // License status check
        if ($('#phpm_license_key').length) {
            checkLicenseStatus();
        }
    }
    
    /**
     * Show license message
     */
    function showLicenseMessage(type, message) {
        var $container = $('#phpm_license_key').closest('td');
        
        // Remove existing messages
        $container.find('.phpm-license-message').remove();
        
        // Add new message
        var messageClass = type === 'success' ? 'notice-success' : 'notice-error';
        var $message = $('<div class="notice ' + messageClass + ' inline phpm-license-message"><p>' + message + '</p></div>');
        
        $container.append($message);
        
        // Auto-remove after 5 seconds
        setTimeout(function() {
            $message.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }
    
    /**
     * Check license status
     */
    function checkLicenseStatus() {
        $.ajax({
            url: vmp_admin.api_url + '../phls/v1/status',
            type: 'GET',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', vmp_admin.nonce);
            },
            success: function(response) {
                if (response.is_valid) {
                    $('.phpm-license-section .notice').removeClass('notice-warning').addClass('notice-success');
                    $('.phpm-license-section .notice p').html('<strong>' + (vmp_admin.strings.license_status || 'License Status:') + '</strong> ' + (vmp_admin.strings.active || 'Active'));
                    
                    if (response.expires) {
                        $('.phpm-license-section .notice p').append('<br><small>' + (vmp_admin.strings.expires || 'Expires:') + ' ' + response.expires + '</small>');
                    }
                } else {
                    $('.phpm-license-section .notice').removeClass('notice-success').addClass('notice-warning');
                    $('.phpm-license-section .notice p').html('<strong>' + (vmp_admin.strings.license_status || 'License Status:') + '</strong> ' + (vmp_admin.strings.not_active || 'Not Active'));
                }
            },
            error: function() {
                console.log('Could not check license status');
            }
        });
    }
    
    /**
     * Quick edit functionality for properties
     */
    if ($('body').hasClass('edit-php') && $('body').hasClass('post-type-phpm_property')) {
        // Add quick edit fields
        $(document).on('click', '.editinline', function() {
            var $row = $(this).closest('tr');
            var postId = $row.attr('id').replace('post-', '');
            
            // Populate quick edit fields with current values
            // This would require adding custom fields to quick edit
        });
    }
    
})(jQuery);