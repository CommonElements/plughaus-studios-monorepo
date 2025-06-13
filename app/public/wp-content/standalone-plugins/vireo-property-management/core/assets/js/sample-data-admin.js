/**
 * Sample Data Admin JavaScript
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Install sample data
        $('#install-sample-data').on('click', function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var $loading = $('#sample-data-loading');
            var $loadingMessage = $('#loading-message');
            
            // Disable button and show loading
            $button.prop('disabled', true);
            $loadingMessage.text(phpmSampleData.strings.installing);
            $loading.show();
            
            $.ajax({
                url: phpmSampleData.ajax_url,
                type: 'POST',
                data: {
                    action: 'phpm_install_sample_data',
                    nonce: phpmSampleData.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        showNotice('success', response.data.message + ' ' + response.data.summary);
                        
                        // Redirect to properties page
                        if (response.data.redirect) {
                            setTimeout(function() {
                                window.location.href = response.data.redirect;
                            }, 2000);
                        } else {
                            // Reload page to show new state
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        }
                    } else {
                        showNotice('error', response.data || phpmSampleData.strings.error);
                        $button.prop('disabled', false);
                    }
                },
                error: function() {
                    showNotice('error', phpmSampleData.strings.error);
                    $button.prop('disabled', false);
                },
                complete: function() {
                    $loading.hide();
                }
            });
        });
        
        // Remove sample data
        $('#remove-sample-data').on('click', function(e) {
            e.preventDefault();
            
            if (!confirm(phpmSampleData.strings.confirm_remove)) {
                return;
            }
            
            var $button = $(this);
            var $loading = $('#sample-data-loading');
            var $loadingMessage = $('#loading-message');
            
            // Disable button and show loading
            $button.prop('disabled', true);
            $loadingMessage.text(phpmSampleData.strings.removing);
            $loading.show();
            
            $.ajax({
                url: phpmSampleData.ajax_url,
                type: 'POST',
                data: {
                    action: 'phpm_remove_sample_data',
                    nonce: phpmSampleData.nonce
                },
                success: function(response) {
                    if (response.success) {
                        showNotice('success', response.data.message);
                        
                        // Reload page to show new state
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        showNotice('error', response.data || phpmSampleData.strings.error);
                        $button.prop('disabled', false);
                    }
                },
                error: function() {
                    showNotice('error', phpmSampleData.strings.error);
                    $button.prop('disabled', false);
                },
                complete: function() {
                    $loading.hide();
                }
            });
        });
        
        // Dismiss sample data notice
        $(document).on('click', '.notice[data-notice="phpm-sample-data"] .notice-dismiss', function() {
            $.post(phpmSampleData.ajax_url, {
                action: 'phpm_dismiss_sample_data_notice',
                nonce: phpmSampleData.nonce
            });
        });
        
        /**
         * Show admin notice
         */
        function showNotice(type, message) {
            var noticeClass = 'notice-' + type;
            var $notice = $('<div class="notice ' + noticeClass + ' is-dismissible"><p>' + message + '</p></div>');
            
            // Remove existing notices
            $('.phpm-sample-data-container').prev('.notice').remove();
            
            // Insert notice
            $('.phpm-sample-data-container').before($notice);
            
            // Scroll to top
            $('html, body').animate({
                scrollTop: $('.wrap').offset().top
            }, 500);
            
            // Auto-dismiss success notices
            if (type === 'success') {
                setTimeout(function() {
                    $notice.fadeOut();
                }, 5000);
            }
        }
    });
    
})(jQuery);