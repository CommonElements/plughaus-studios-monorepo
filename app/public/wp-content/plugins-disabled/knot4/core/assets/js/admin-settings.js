/**
 * Admin Settings JavaScript for Knot4
 * 
 * @package Knot4
 * @since 1.0.0
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        initPageCreation();
        initTabSwitching();
    });
    
    /**
     * Initialize page creation functionality
     */
    function initPageCreation() {
        $('.knot4-create-page').on('click', function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const pageType = $button.data('page-type');
            const fieldId = $button.data('field-id');
            const originalText = $button.text();
            
            // Disable button and show loading state
            $button.prop('disabled', true).text(knot4AdminSettings.strings.creating);
            
            // Create page via AJAX
            $.ajax({
                url: knot4AdminSettings.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'knot4_create_page',
                    page_type: pageType,
                    nonce: knot4AdminSettings.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Update the select dropdown
                        const $select = $('#' + fieldId);
                        const $option = $('<option></option>')
                            .attr('value', response.data.page_id)
                            .text(response.data.page_title)
                            .prop('selected', true);
                        
                        $select.append($option);
                        
                        // Show success message
                        showNotice('success', knot4AdminSettings.strings.pageCreated + ' ' + response.data.page_title);
                        
                        // Add view link
                        const $container = $button.closest('.knot4-page-select-container');
                        const $viewLink = $('<p class="description"></p>')
                            .html('<a href="' + response.data.page_url + '" target="_blank">' + 
                                  knot4AdminSettings.strings.viewPage + ': ' + response.data.page_title + '</a>');
                        
                        $container.after($viewLink);
                        
                        // Update button text
                        $button.text(knot4AdminSettings.strings.recreatePage);
                        
                    } else {
                        showNotice('error', response.data || knot4AdminSettings.strings.createError);
                    }
                },
                error: function() {
                    showNotice('error', knot4AdminSettings.strings.ajaxError);
                },
                complete: function() {
                    $button.prop('disabled', false);
                    if ($button.text() === knot4AdminSettings.strings.creating) {
                        $button.text(originalText);
                    }
                }
            });
        });
    }
    
    /**
     * Initialize settings tab switching
     */
    function initTabSwitching() {
        $('.knot4-settings-tabs').on('click', '.nav-tab', function(e) {
            e.preventDefault();
            
            const $tab = $(this);
            const target = $tab.data('tab');
            
            // Switch active tab
            $('.nav-tab').removeClass('nav-tab-active');
            $tab.addClass('nav-tab-active');
            
            // Switch tab content
            $('.knot4-tab-content').hide();
            $('#knot4-tab-' + target).show();
        });
    }
    
    /**
     * Show admin notice
     */
    function showNotice(type, message) {
        const $notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
        
        $('.wrap h1').after($notice);
        
        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            $notice.fadeOut(function() {
                $notice.remove();
            });
        }, 5000);
        
        // Handle dismiss button
        $notice.on('click', '.notice-dismiss', function() {
            $notice.remove();
        });
    }
    
    /**
     * Handle settings form validation
     */
    $('form[action="options.php"]').on('submit', function(e) {
        const $form = $(this);
        let hasErrors = false;
        
        // Validate required fields
        $form.find('input[required], select[required]').each(function() {
            const $field = $(this);
            if (!$field.val().trim()) {
                $field.addClass('error');
                hasErrors = true;
            } else {
                $field.removeClass('error');
            }
        });
        
        // Validate email fields
        $form.find('input[type="email"]').each(function() {
            const $field = $(this);
            const email = $field.val().trim();
            
            if (email && !isValidEmail(email)) {
                $field.addClass('error');
                hasErrors = true;
            } else {
                $field.removeClass('error');
            }
        });
        
        if (hasErrors) {
            e.preventDefault();
            showNotice('error', knot4AdminSettings.strings.validationError);
            
            // Scroll to first error
            const $firstError = $form.find('.error').first();
            if ($firstError.length) {
                $('html, body').animate({
                    scrollTop: $firstError.offset().top - 100
                }, 500);
                $firstError.focus();
            }
        }
    });
    
    /**
     * Validate email address
     */
    function isValidEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }
    
    /**
     * Handle Stripe key validation
     */
    $('input[name*="stripe_"]').on('blur', function() {
        const $field = $(this);
        const value = $field.val().trim();
        const fieldName = $field.attr('name');
        
        if (value) {
            let isValid = false;
            
            if (fieldName.includes('publishable_key')) {
                isValid = value.startsWith('pk_test_') || value.startsWith('pk_live_');
            } else if (fieldName.includes('secret_key')) {
                isValid = value.startsWith('sk_test_') || value.startsWith('sk_live_');
            }
            
            if (!isValid) {
                $field.addClass('error');
                showNotice('warning', knot4AdminSettings.strings.stripeKeyWarning);
            } else {
                $field.removeClass('error');
            }
        }
    });
    
    /**
     * Handle test mode toggle
     */
    $('input[name*="test_mode"]').on('change', function() {
        const $testMode = $(this);
        const $testFields = $('input[name*="stripe_test_"]').closest('tr');
        const $liveFields = $('input[name*="stripe_live_"]').closest('tr');
        
        if ($testMode.is(':checked')) {
            $testFields.show();
            $liveFields.hide();
        } else {
            $testFields.hide();
            $liveFields.show();
        }
    });
    
    // Initialize test mode visibility
    $('input[name*="test_mode"]').trigger('change');
    
    /**
     * Add tooltips for help text
     */
    $('.description').each(function() {
        const $desc = $(this);
        const $field = $desc.prev('input, select, textarea');
        
        if ($field.length) {
            $field.attr('title', $desc.text());
        }
    });
    
})(jQuery);