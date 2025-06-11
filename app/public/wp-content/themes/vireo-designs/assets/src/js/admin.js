/**
 * Admin JavaScript for Vireo Designs Theme
 * 
 * Handles admin-specific functionality and interfaces
 */

(function($) {
    'use strict';

    // Admin initialization
    const VireoAdmin = {
        init() {
            this.setupAdminEnhancements();
            this.setupColorPickers();
            this.setupMediaUploaders();
            this.setupAjaxHandlers();
        },

        setupAdminEnhancements() {
            // Enhanced admin interface styling
            if ($('body').hasClass('wp-admin')) {
                this.enhanceAdminMetaBoxes();
                this.setupTabInterface();
            }
        },

        enhanceAdminMetaBoxes() {
            // Add enhanced styling to meta boxes
            $('.postbox').each(function() {
                const $metabox = $(this);
                if (!$metabox.hasClass('vireo-enhanced')) {
                    $metabox.addClass('vireo-enhanced');
                }
            });
        },

        setupTabInterface() {
            // Tab navigation for settings pages
            $('.vireo-admin-tabs').each(function() {
                const $tabContainer = $(this);
                const $tabs = $tabContainer.find('.tab-nav a');
                const $panels = $tabContainer.find('.tab-panel');

                $tabs.on('click', function(e) {
                    e.preventDefault();
                    const targetPanel = $(this).attr('href');
                    
                    // Update active tab
                    $tabs.removeClass('active');
                    $(this).addClass('active');
                    
                    // Show target panel
                    $panels.removeClass('active');
                    $(targetPanel).addClass('active');
                });
            });
        },

        setupColorPickers() {
            // Initialize WordPress color pickers
            if ($.fn.wpColorPicker) {
                $('.vireo-color-picker').wpColorPicker({
                    change: function(event, ui) {
                        // Handle color change
                        const newColor = ui.color.toString();
                        $(event.target).trigger('vireo:colorChanged', [newColor]);
                    }
                });
            }
        },

        setupMediaUploaders() {
            // Setup WordPress media uploader
            $('.vireo-media-upload').on('click', function(e) {
                e.preventDefault();
                
                const $button = $(this);
                const $input = $button.siblings('input[type="url"]');
                const $preview = $button.siblings('.media-preview');
                
                const mediaUploader = wp.media({
                    title: 'Select Media',
                    button: { text: 'Use this media' },
                    multiple: false
                });
                
                mediaUploader.on('select', function() {
                    const attachment = mediaUploader.state().get('selection').first().toJSON();
                    $input.val(attachment.url);
                    
                    if ($preview.length) {
                        $preview.html(`<img src="${attachment.url}" alt="${attachment.alt}" style="max-width: 200px; height: auto;" />`);
                    }
                });
                
                mediaUploader.open();
            });
        },

        setupAjaxHandlers() {
            // Generic AJAX handler for admin actions
            $('.vireo-ajax-action').on('click', function(e) {
                e.preventDefault();
                
                const $button = $(this);
                const action = $button.data('action');
                const nonce = $button.data('nonce');
                
                if (!action || !nonce) {
                    console.error('Missing action or nonce for AJAX request');
                    return;
                }
                
                $button.prop('disabled', true).text('Processing...');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: action,
                        nonce: nonce,
                        data: $button.data()
                    },
                    success: function(response) {
                        if (response.success) {
                            VireoAdmin.showNotice('success', response.data.message || 'Action completed successfully');
                        } else {
                            VireoAdmin.showNotice('error', response.data.message || 'An error occurred');
                        }
                    },
                    error: function() {
                        VireoAdmin.showNotice('error', 'Network error occurred');
                    },
                    complete: function() {
                        $button.prop('disabled', false).text($button.data('original-text') || 'Submit');
                    }
                });
            });
        },

        showNotice(type, message) {
            // Show admin notice
            const noticeClass = type === 'success' ? 'notice-success' : 'notice-error';
            const $notice = $(`
                <div class="notice ${noticeClass} is-dismissible">
                    <p>${message}</p>
                    <button type="button" class="notice-dismiss">
                        <span class="screen-reader-text">Dismiss this notice.</span>
                    </button>
                </div>
            `);
            
            $('.wrap h1').after($notice);
            
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                $notice.fadeOut(400, function() {
                    $(this).remove();
                });
            }, 5000);
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        VireoAdmin.init();
    });

    // Expose to global scope for external use
    window.VireoAdmin = VireoAdmin;

})(jQuery);