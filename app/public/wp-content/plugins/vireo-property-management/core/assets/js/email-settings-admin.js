/**
 * Email Settings Admin JavaScript
 */

jQuery(document).ready(function($) {
    
    // Toggle notification options based on checkbox state
    $('.notification-type input[type="checkbox"]').on('change', function() {
        var $options = $(this).closest('.notification-type').find('.notification-options');
        if ($(this).is(':checked')) {
            $options.slideDown();
        } else {
            $options.slideUp();
        }
    });
    
    // Save email settings
    $('#save-email-settings').on('click', function() {
        var $button = $(this);
        var $form = $('#phpm-email-settings-form');
        var originalText = $button.html();
        
        // Disable button and show saving state
        $button.prop('disabled', true).html('<span class="spinner is-active" style="float: none; margin-right: 5px;"></span>' + phpmEmailSettings.strings.saving);
        
        // Collect all form data
        var formData = {
            action: 'phpm_save_email_settings',
            nonce: phpmEmailSettings.nonce
        };
        
        // Get basic form fields
        $form.find('input, textarea, select').each(function() {
            var $field = $(this);
            var name = $field.attr('name');
            
            if (name) {
                if ($field.attr('type') === 'checkbox') {
                    if (name.endsWith('[]')) {
                        // Handle checkbox arrays
                        if (!formData[name]) {
                            formData[name] = [];
                        }
                        if ($field.is(':checked')) {
                            formData[name].push($field.val());
                        }
                    } else {
                        // Handle single checkboxes
                        formData[name] = $field.is(':checked') ? 1 : 0;
                    }
                } else {
                    formData[name] = $field.val();
                }
            }
        });
        
        // Get notification settings from outside the form
        $('.notification-settings input[type="checkbox"]').each(function() {
            var $field = $(this);
            var name = $field.attr('name');
            
            if (name) {
                if (name.endsWith('[]')) {
                    if (!formData[name]) {
                        formData[name] = [];
                    }
                    if ($field.is(':checked')) {
                        formData[name].push($field.val());
                    }
                } else {
                    formData[name] = $field.is(':checked') ? 1 : 0;
                }
            }
        });
        
        // Get notification-specific inputs
        $('.notification-settings input[type="number"]').each(function() {
            var $field = $(this);
            var name = $field.attr('name');
            if (name) {
                formData[name] = $field.val();
            }
        });
        
        // Get recipient settings
        $('input[name="tenant_notifications"], input[name="manager_notifications"]').each(function() {
            formData[$(this).attr('name')] = $(this).is(':checked') ? 1 : 0;
        });
        
        // Send AJAX request
        $.post(phpmEmailSettings.ajax_url, formData)
            .done(function(response) {
                if (response.success) {
                    showFeedback(phpmEmailSettings.strings.saved, 'success');
                } else {
                    showFeedback(response.data || phpmEmailSettings.strings.error, 'error');
                }
            })
            .fail(function() {
                showFeedback(phpmEmailSettings.strings.error, 'error');
            })
            .always(function() {
                // Restore button
                $button.prop('disabled', false).html(originalText);
            });
    });
    
    // Send test email
    $('#send-test-email').on('click', function() {
        var $button = $(this);
        var email = $('#test_email').val();
        var template = $('#test_template').val();
        var originalText = $button.html();
        
        if (!email) {
            alert('Please enter a test email address.');
            return;
        }
        
        // Disable button and show sending state
        $button.prop('disabled', true).html('<span class="spinner is-active" style="float: none; margin-right: 5px;"></span>' + phpmEmailSettings.strings.sending_test);
        
        // Send test email
        $.post(phpmEmailSettings.ajax_url, {
            action: 'phpm_send_test_email',
            nonce: phpmEmailSettings.nonce,
            email: email,
            template: template
        })
        .done(function(response) {
            if (response.success) {
                showFeedback(phpmEmailSettings.strings.test_sent, 'success');
            } else {
                showFeedback(response.data || phpmEmailSettings.strings.test_error, 'error');
            }
        })
        .fail(function() {
            showFeedback(phpmEmailSettings.strings.test_error, 'error');
        })
        .always(function() {
            // Restore button
            $button.prop('disabled', false).html(originalText);
        });
    });
    
    // Form validation
    $('#from_email').on('blur', function() {
        var email = $(this).val();
        if (email && !isValidEmail(email)) {
            $(this).css('border-color', '#dc3545');
            showFeedback('Please enter a valid email address.', 'error');
        } else {
            $(this).css('border-color', '');
            hideFeedback();
        }
    });
    
    // Show feedback message
    function showFeedback(message, type) {
        var $feedback = $('#email-settings-feedback');
        var $message = $('#feedback-message');
        
        $feedback.removeClass('notice-success notice-error notice-warning')
                 .addClass('notice-' + type)
                 .show();
        
        $message.text(message);
        
        // Auto-hide success messages
        if (type === 'success') {
            setTimeout(function() {
                hideFeedback();
            }, 3000);
        }
        
        // Scroll to feedback
        $('html, body').animate({
            scrollTop: $feedback.offset().top - 50
        }, 500);
    }
    
    // Hide feedback message
    function hideFeedback() {
        $('#email-settings-feedback').fadeOut();
    }
    
    // Email validation helper
    function isValidEmail(email) {
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    // Initialize tooltips (if available)
    if (typeof $.fn.tooltip === 'function') {
        $('[data-tooltip]').tooltip();
    }
    
    // Copy variable to clipboard (if supported)
    $(document).on('click', '.template-variables code', function() {
        if (navigator.clipboard) {
            var text = $(this).text();
            navigator.clipboard.writeText(text).then(function() {
                // Visual feedback
                var $code = $(this);
                var original = $code.css('background-color');
                $code.css('background-color', '#d4edda');
                setTimeout(function() {
                    $code.css('background-color', original);
                }, 1000);
            }.bind(this));
        }
    });
    
    // Form change detection
    var originalFormData = null;
    
    function getFormData() {
        var data = {};
        $('#phpm-email-settings-form input, #phpm-email-settings-form textarea, #phpm-email-settings-form select, .notification-settings input').each(function() {
            var $field = $(this);
            var name = $field.attr('name');
            
            if (name) {
                if ($field.attr('type') === 'checkbox') {
                    data[name] = $field.is(':checked');
                } else {
                    data[name] = $field.val();
                }
            }
        });
        return JSON.stringify(data);
    }
    
    // Store original form data
    setTimeout(function() {
        originalFormData = getFormData();
    }, 100);
    
    // Warn about unsaved changes
    $(window).on('beforeunload', function() {
        var currentData = getFormData();
        if (originalFormData && currentData !== originalFormData) {
            return 'You have unsaved changes. Are you sure you want to leave?';
        }
    });
    
    // Clear unsaved changes warning after save
    $(document).on('phpm_settings_saved', function() {
        originalFormData = getFormData();
    });
    
    // Preview email functionality
    $('#preview-email-template').on('click', function() {
        var template = $('#test_template').val();
        var $modal = $('<div class="phpm-modal-overlay"><div class="phpm-modal"><div class="phpm-modal-header"><h3>Email Template Preview</h3><button class="phpm-modal-close">&times;</button></div><div class="phpm-modal-body"><iframe src="" width="100%" height="400" frameborder="0"></iframe></div></div></div>');
        
        $('body').append($modal);
        
        // Load preview content
        $modal.find('iframe').attr('src', phpmEmailSettings.ajax_url + '?action=phpm_preview_email_template&template=' + template + '&nonce=' + phpmEmailSettings.nonce);
        
        // Close modal
        $modal.on('click', '.phpm-modal-close, .phpm-modal-overlay', function(e) {
            if (e.target === this) {
                $modal.remove();
            }
        });
    });
    
    // Auto-save draft functionality
    var autoSaveTimer;
    
    function autoSave() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(function() {
            // Could implement auto-save to drafts here
            console.log('Auto-save triggered');
        }, 5000);
    }
    
    // Trigger auto-save on form changes
    $('#phpm-email-settings-form input, #phpm-email-settings-form textarea, .notification-settings input').on('input change', function() {
        autoSave();
    });
});