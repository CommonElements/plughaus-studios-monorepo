/**
 * Frontend Settings Admin JavaScript
 */

jQuery(document).ready(function($) {
    
    // Save frontend settings
    $('#save-frontend-settings').on('click', function() {
        var $button = $(this);
        var originalText = $button.html();
        
        // Disable button and show saving state
        $button.prop('disabled', true).html('<span class="spinner is-active" style="float: none; margin-right: 5px;"></span>' + phpmFrontendSettings.strings.saving);
        
        // Collect all form data
        var formData = {
            action: 'phpm_save_frontend_settings',
            nonce: phpmFrontendSettings.nonce
        };
        
        // Get page assignments
        $('.page-select').each(function() {
            formData[$(this).attr('name')] = $(this).val();
        });
        
        // Get checkbox settings
        $('input[type="checkbox"]').each(function() {
            formData[$(this).attr('name')] = $(this).is(':checked') ? 1 : 0;
        });
        
        // Get numeric settings
        $('input[type="number"]').each(function() {
            formData[$(this).attr('name')] = $(this).val();
        });
        
        // Send AJAX request
        $.post(phpmFrontendSettings.ajax_url, formData)
            .done(function(response) {
                if (response.success) {
                    showFeedback(phpmFrontendSettings.strings.saved, 'success');
                    
                    // Update page select options if any pages were created
                    updatePageSelects();
                } else {
                    showFeedback(response.data || phpmFrontendSettings.strings.error, 'error');
                }
            })
            .fail(function() {
                showFeedback(phpmFrontendSettings.strings.error, 'error');
            })
            .always(function() {
                // Restore button
                $button.prop('disabled', false).html(originalText);
            });
    });
    
    // Create new page
    $('.create-page-btn').on('click', function() {
        var $button = $(this);
        var pageType = $button.data('page-type');
        var $select = $button.siblings('.page-select');
        var originalText = $button.text();
        
        // Confirm with user
        if (!confirm(phpmFrontendSettings.strings.confirm_create)) {
            return;
        }
        
        // Disable button and show creating state
        $button.prop('disabled', true).text(phpmFrontendSettings.strings.creating_page);
        
        // Create the page
        $.post(phpmFrontendSettings.ajax_url, {
            action: 'phpm_create_frontend_page',
            nonce: phpmFrontendSettings.nonce,
            page_type: pageType
        })
        .done(function(response) {
            if (response.success) {
                var data = response.data;
                
                // Add new option to select
                $select.append('<option value="' + data.page_id + '" selected>' + data.page_title + '</option>');
                
                // Show success message with links
                var message = phpmFrontendSettings.strings.page_created + ' ';
                message += '<a href="' + data.edit_url + '" target="_blank">Edit Page</a> | ';
                message += '<a href="' + data.view_url + '" target="_blank">View Page</a>';
                
                showFeedback(message, 'success', true);
                
                // Highlight the new selection
                $select.effect('highlight', {}, 1000);
                
            } else {
                showFeedback(response.data || phpmFrontendSettings.strings.page_error, 'error');
            }
        })
        .fail(function() {
            showFeedback(phpmFrontendSettings.strings.page_error, 'error');
        })
        .always(function() {
            // Restore button
            $button.prop('disabled', false).text(originalText);
        });
    });
    
    // Show/hide shortcode details
    $('.shortcode-item details').on('toggle', function() {
        $(this).closest('.shortcode-item').toggleClass('expanded', this.open);
    });
    
    // Copy shortcode to clipboard
    $('.shortcode-item code').on('click', function() {
        var text = $(this).text();
        
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(function() {
                // Visual feedback
                $(this).addClass('copied');
                setTimeout(function() {
                    $(this).removeClass('copied');
                }.bind(this), 1000);
            }.bind(this));
        } else {
            // Fallback for older browsers
            var $temp = $('<input>');
            $('body').append($temp);
            $temp.val(text).select();
            document.execCommand('copy');
            $temp.remove();
            
            $(this).addClass('copied');
            setTimeout(function() {
                $(this).removeClass('copied');
            }.bind(this), 1000);
        }
    });
    
    // Toggle dependent settings
    $('input[name="enable_property_listings"]').on('change', function() {
        var $dependent = $('input[name="show_available_only"], input[name="show_property_images"], input[name="properties_per_page"]').closest('label');
        $dependent.toggle($(this).is(':checked'));
    }).trigger('change');
    
    $('input[name="show_property_map"]').on('change', function() {
        var $dependent = $('input[name="default_map_zoom"]').closest('label');
        $dependent.toggle($(this).is(':checked'));
    }).trigger('change');
    
    // Page selection change handler
    $('.page-select').on('change', function() {
        var $select = $(this);
        var pageId = $select.val();
        var $button = $select.siblings('.create-page-btn');
        
        if (pageId && pageId !== '0') {
            $button.text('Create Another Page');
            
            // Add view/edit links
            var $links = $select.siblings('.page-links');
            if ($links.length === 0) {
                $links = $('<div class="page-links"></div>');
                $select.after($links);
            }
            
            var editUrl = phpmFrontendSettings.admin_url + 'post.php?post=' + pageId + '&action=edit';
            var viewUrl = phpmFrontendSettings.site_url + '?page_id=' + pageId;
            
            $links.html(
                '<a href="' + editUrl + '" target="_blank" class="button button-small">Edit Page</a> ' +
                '<a href="' + viewUrl + '" target="_blank" class="button button-small">View Page</a>'
            );
        } else {
            $button.text('Create New Page');
            $select.siblings('.page-links').remove();
        }
    });
    
    // Initialize page links for pre-selected pages
    $('.page-select').trigger('change');
    
    // Show feedback message
    function showFeedback(message, type, allowHtml) {
        var $feedback = $('#frontend-settings-feedback');
        var $message = $('#feedback-message');
        
        $feedback.removeClass('notice-success notice-error notice-warning')
                 .addClass('notice-' + type)
                 .show();
        
        if (allowHtml) {
            $message.html(message);
        } else {
            $message.text(message);
        }
        
        // Auto-hide success messages
        if (type === 'success') {
            setTimeout(function() {
                hideFeedback();
            }, 5000);
        }
        
        // Scroll to feedback
        $('html, body').animate({
            scrollTop: $feedback.offset().top - 50
        }, 500);
    }
    
    // Hide feedback message
    function hideFeedback() {
        $('#frontend-settings-feedback').fadeOut();
    }
    
    // Update page select options (in case new pages were created elsewhere)
    function updatePageSelects() {
        // This could be enhanced to refresh the page options via AJAX
        // For now, we'll just trigger change events to update UI
        $('.page-select').trigger('change');
    }
    
    // Form validation
    function validateSettings() {
        var hasErrors = false;
        
        // Check if tenant portal is enabled but no page is selected
        if ($('input[name="enable_tenant_portal"]').is(':checked') && 
            $('select[name="tenant_portal_page"]').val() === '0') {
            
            showFeedback('Please select a page for the tenant portal or disable the feature.', 'warning');
            hasErrors = true;
        }
        
        // Check if property listings are enabled but no page is selected
        if ($('input[name="enable_property_listings"]').is(':checked') && 
            $('select[name="property_listing_page"]').val() === '0') {
            
            showFeedback('Please select a page for property listings or disable the feature.', 'warning');
            hasErrors = true;
        }
        
        return !hasErrors;
    }
    
    // Validate before saving
    $('#save-frontend-settings').on('click', function(e) {
        if (!validateSettings()) {
            e.preventDefault();
            return false;
        }
    });
    
    // Warning about unsaved changes
    var originalFormData = null;
    
    function getFormData() {
        var data = {};
        $('.page-select, input[type="checkbox"], input[type="number"]').each(function() {
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
    $(document).on('phpm_frontend_settings_saved', function() {
        originalFormData = getFormData();
    });
    
    // Bulk create pages functionality
    $('#bulk-create-pages').on('click', function() {
        var $button = $(this);
        var pagesToCreate = [];
        
        // Find unassigned page types
        $('.page-select').each(function() {
            if ($(this).val() === '0') {
                pagesToCreate.push($(this).attr('name').replace('_page', ''));
            }
        });
        
        if (pagesToCreate.length === 0) {
            alert('All pages are already assigned.');
            return;
        }
        
        if (!confirm('This will create ' + pagesToCreate.length + ' new pages. Continue?')) {
            return;
        }
        
        var originalText = $button.text();
        $button.prop('disabled', true).text('Creating pages...');
        
        // Create pages sequentially
        var createNext = function(index) {
            if (index >= pagesToCreate.length) {
                $button.prop('disabled', false).text(originalText);
                showFeedback('All pages created successfully!', 'success');
                return;
            }
            
            var pageType = pagesToCreate[index];
            
            $.post(phpmFrontendSettings.ajax_url, {
                action: 'phpm_create_frontend_page',
                nonce: phpmFrontendSettings.nonce,
                page_type: pageType
            })
            .done(function(response) {
                if (response.success) {
                    var data = response.data;
                    var $select = $('select[name="' + pageType + '_page"]');
                    $select.append('<option value="' + data.page_id + '" selected>' + data.page_title + '</option>');
                    $select.trigger('change');
                }
                createNext(index + 1);
            })
            .fail(function() {
                createNext(index + 1);
            });
        };
        
        createNext(0);
    });
    
    // Enhanced UI interactions
    $('.card').hover(
        function() { $(this).addClass('hover'); },
        function() { $(this).removeClass('hover'); }
    );
    
    // Search functionality for pages
    $('#page-search').on('input', function() {
        var search = $(this).val().toLowerCase();
        $('.page-assignments tr').each(function() {
            var text = $(this).find('th label').text().toLowerCase();
            $(this).toggle(text.indexOf(search) !== -1);
        });
    });
});