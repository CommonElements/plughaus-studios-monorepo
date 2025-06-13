/**
 * Email Settings Admin JavaScript for Knot4
 * 
 * @package Knot4
 * @since 1.0.0
 */

(function($) {
    'use strict';
    
    let currentTemplate = null;
    let templates = {};
    let editor = null;
    
    $(document).ready(function() {
        initEmailSettings();
    });
    
    /**
     * Initialize email settings functionality
     */
    function initEmailSettings() {
        loadTemplateData();
        initTemplateSelection();
        initEditorActions();
        initModalHandlers();
        initVariableInsertion();
        initFormValidation();
    }
    
    /**
     * Load template data from page
     */
    function loadTemplateData() {
        // Templates data would be localized from PHP
        if (typeof knot4Templates !== 'undefined') {
            templates = knot4Templates;
        }
    }
    
    /**
     * Initialize template selection
     */
    function initTemplateSelection() {
        $('.knot4-template-btn').on('click', function() {
            const templateKey = $(this).data('template');
            selectTemplate(templateKey);
        });
    }
    
    /**
     * Select and load a template
     */
    function selectTemplate(templateKey) {
        // Update UI
        $('.knot4-template-btn').removeClass('active');
        $(`.knot4-template-btn[data-template="${templateKey}"]`).addClass('active');
        
        // Show form and hide placeholder
        $('.knot4-template-placeholder').hide();
        $('#knot4-template-form').show();
        
        // Enable action buttons
        $('.knot4-preview-btn, .knot4-test-btn, .knot4-reset-btn').prop('disabled', false);
        
        // Load template data
        loadTemplateIntoEditor(templateKey);
        
        currentTemplate = templateKey;
    }
    
    /**
     * Load template data into editor
     */
    function loadTemplateIntoEditor(templateKey) {
        // Set current template key
        $('#current-template-key').val(templateKey);
        
        // Load template data via AJAX or from localized data
        $.ajax({
            url: knot4EmailSettings.ajaxUrl,
            type: 'POST',
            data: {
                action: 'knot4_get_template_data',
                template_key: templateKey,
                nonce: knot4EmailSettings.nonce
            },
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    
                    // Update template title
                    $('.knot4-template-title').text(data.name);
                    
                    // Set subject
                    $('#template-subject').val(data.subject);
                    
                    // Set content in editor
                    if (typeof tinyMCE !== 'undefined' && tinyMCE.get('template-content')) {
                        tinyMCE.get('template-content').setContent(data.content);
                    } else {
                        $('#template-content').val(data.content);
                    }
                    
                    // Update available variables
                    updateAvailableVariables(data.variables);
                }
            },
            error: function() {
                showMessage('error', knot4EmailSettings.strings.ajaxError);
            }
        });
    }
    
    /**
     * Update available variables display
     */
    function updateAvailableVariables(variables) {
        const $variablesList = $('.knot4-variables-list');
        $variablesList.empty();
        
        if (variables && Object.keys(variables).length > 0) {
            Object.keys(variables).forEach(function(varKey) {
                const $item = $('<div class="knot4-variable-item"></div>');
                $item.html(`
                    <span class="knot4-variable-code">${varKey}</span>
                    <span class="knot4-variable-desc">${variables[varKey]}</span>
                `);
                
                $item.on('click', function() {
                    insertVariable(varKey);
                });
                
                $variablesList.append($item);
            });
        } else {
            $variablesList.html('<p>' + knot4EmailSettings.strings.noVariables + '</p>');
        }
    }
    
    /**
     * Insert variable into editor
     */
    function insertVariable(variable) {
        if (typeof tinyMCE !== 'undefined' && tinyMCE.get('template-content')) {
            tinyMCE.get('template-content').insertContent(variable);
        } else {
            const $textarea = $('#template-content');
            const cursorPos = $textarea.prop('selectionStart');
            const textBefore = $textarea.val().substring(0, cursorPos);
            const textAfter = $textarea.val().substring(cursorPos);
            
            $textarea.val(textBefore + variable + textAfter);
            $textarea.prop('selectionStart', cursorPos + variable.length);
            $textarea.prop('selectionEnd', cursorPos + variable.length);
            $textarea.focus();
        }
        
        // Show success feedback
        showMessage('success', `Variable ${variable} inserted`, 2000);
    }
    
    /**
     * Initialize editor action buttons
     */
    function initEditorActions() {
        // Preview button
        $('.knot4-preview-btn').on('click', function() {
            if (!currentTemplate) return;
            previewEmail();
        });
        
        // Test email button
        $('.knot4-test-btn').on('click', function() {
            if (!currentTemplate) return;
            showTestEmailModal();
        });
        
        // Reset template button
        $('.knot4-reset-btn').on('click', function() {
            if (!currentTemplate) return;
            resetTemplate();
        });
    }
    
    /**
     * Preview email
     */
    function previewEmail() {
        const $button = $('.knot4-preview-btn');
        const originalText = $button.text();
        
        $button.prop('disabled', true).text(knot4EmailSettings.strings.previewing);
        
        const subject = $('#template-subject').val();
        const content = getEditorContent();
        
        $.ajax({
            url: knot4EmailSettings.ajaxUrl,
            type: 'POST',
            data: {
                action: 'knot4_preview_email',
                template_key: currentTemplate,
                subject: subject,
                content: content,
                nonce: knot4EmailSettings.nonce
            },
            success: function(response) {
                if (response.success) {
                    showPreviewModal(response.data);
                } else {
                    showMessage('error', response.data || knot4EmailSettings.strings.previewError);
                }
            },
            error: function() {
                showMessage('error', knot4EmailSettings.strings.ajaxError);
            },
            complete: function() {
                $button.prop('disabled', false).text(originalText);
            }
        });
    }
    
    /**
     * Show preview modal
     */
    function showPreviewModal(data) {
        const $modal = $('#knot4-preview-modal');
        const $content = $modal.find('.knot4-preview-content');
        
        // Create iframe with email content
        const iframe = document.createElement('iframe');
        iframe.style.width = '100%';
        iframe.style.height = '500px';
        iframe.style.border = 'none';
        
        $content.empty().append(iframe);
        
        // Write content to iframe
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        iframeDoc.open();
        iframeDoc.write(data.content);
        iframeDoc.close();
        
        $modal.show();
    }
    
    /**
     * Show test email modal
     */
    function showTestEmailModal() {
        $('#knot4-test-modal').show();
    }
    
    /**
     * Reset template to default
     */
    function resetTemplate() {
        if (!confirm(knot4EmailSettings.strings.confirmReset)) {
            return;
        }
        
        const $button = $('.knot4-reset-btn');
        const originalText = $button.text();
        
        $button.prop('disabled', true).text(knot4EmailSettings.strings.resetting);
        
        $.ajax({
            url: knot4EmailSettings.ajaxUrl,
            type: 'POST',
            data: {
                action: 'knot4_reset_template',
                template_key: currentTemplate,
                nonce: knot4EmailSettings.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Update editor with default content
                    $('#template-subject').val(response.data.subject);
                    
                    if (typeof tinyMCE !== 'undefined' && tinyMCE.get('template-content')) {
                        tinyMCE.get('template-content').setContent(response.data.content);
                    } else {
                        $('#template-content').val(response.data.content);
                    }
                    
                    // Remove modified indicator
                    $(`.knot4-template-btn[data-template="${currentTemplate}"] .knot4-modified-indicator`).remove();
                    
                    showMessage('success', knot4EmailSettings.strings.resetSuccess);
                } else {
                    showMessage('error', response.data || knot4EmailSettings.strings.resetFailed);
                }
            },
            error: function() {
                showMessage('error', knot4EmailSettings.strings.ajaxError);
            },
            complete: function() {
                $button.prop('disabled', false).text(originalText);
            }
        });
    }
    
    /**
     * Initialize modal handlers
     */
    function initModalHandlers() {
        // Close modal handlers
        $('.knot4-modal-close, .knot4-modal').on('click', function(e) {
            if (e.target === this) {
                $(this).closest('.knot4-modal').hide();
            }
        });
        
        // Prevent modal content clicks from closing modal
        $('.knot4-modal-content').on('click', function(e) {
            e.stopPropagation();
        });
        
        // Test email form submission
        $('#knot4-test-email-form').on('submit', function(e) {
            e.preventDefault();
            sendTestEmail();
        });
        
        // ESC key to close modals
        $(document).on('keyup', function(e) {
            if (e.keyCode === 27) { // ESC key
                $('.knot4-modal').hide();
            }
        });
    }
    
    /**
     * Send test email
     */
    function sendTestEmail() {
        const $form = $('#knot4-test-email-form');
        const $button = $form.find('button[type="submit"]');
        const originalText = $button.text();
        const testEmail = $('#test-email-address').val();
        
        if (!testEmail || !isValidEmail(testEmail)) {
            showMessage('error', knot4EmailSettings.strings.invalidEmail);
            return;
        }
        
        $button.prop('disabled', true).text(knot4EmailSettings.strings.sendingTest);
        
        const subject = $('#template-subject').val();
        const content = getEditorContent();
        
        $.ajax({
            url: knot4EmailSettings.ajaxUrl,
            type: 'POST',
            data: {
                action: 'knot4_test_email',
                template_key: currentTemplate,
                test_email: testEmail,
                subject: subject,
                content: content,
                nonce: knot4EmailSettings.nonce
            },
            success: function(response) {
                if (response.success) {
                    showMessage('success', knot4EmailSettings.strings.testSent);
                    $('#knot4-test-modal').hide();
                } else {
                    showMessage('error', response.data || knot4EmailSettings.strings.testFailed);
                }
            },
            error: function() {
                showMessage('error', knot4EmailSettings.strings.ajaxError);
            },
            complete: function() {
                $button.prop('disabled', false).text(originalText);
            }
        });
    }
    
    /**
     * Initialize variable insertion
     */
    function initVariableInsertion() {
        // Double-click on variable to insert
        $(document).on('dblclick', '.knot4-variable-item', function() {
            const variable = $(this).find('.knot4-variable-code').text();
            insertVariable(variable);
        });
        
        // Keyboard shortcut for variable insertion
        $(document).on('keydown', function(e) {
            if (e.ctrlKey && e.shiftKey && e.keyCode === 86) { // Ctrl+Shift+V
                e.preventDefault();
                showVariableMenu();
            }
        });
    }
    
    /**
     * Initialize form validation
     */
    function initFormValidation() {
        $('#knot4-template-form').on('submit', function(e) {
            const subject = $('#template-subject').val().trim();
            const content = getEditorContent().trim();
            
            if (!subject) {
                e.preventDefault();
                showMessage('error', knot4EmailSettings.strings.subjectRequired);
                $('#template-subject').focus();
                return false;
            }
            
            if (!content) {
                e.preventDefault();
                showMessage('error', knot4EmailSettings.strings.contentRequired);
                focusEditor();
                return false;
            }
            
            return true;
        });
        
        // Real-time validation feedback
        $('#template-subject').on('blur', function() {
            const $field = $(this);
            if (!$field.val().trim()) {
                $field.addClass('error');
            } else {
                $field.removeClass('error');
            }
        });
    }
    
    /**
     * Get editor content
     */
    function getEditorContent() {
        if (typeof tinyMCE !== 'undefined' && tinyMCE.get('template-content')) {
            return tinyMCE.get('template-content').getContent();
        } else {
            return $('#template-content').val();
        }
    }
    
    /**
     * Focus editor
     */
    function focusEditor() {
        if (typeof tinyMCE !== 'undefined' && tinyMCE.get('template-content')) {
            tinyMCE.get('template-content').focus();
        } else {
            $('#template-content').focus();
        }
    }
    
    /**
     * Validate email address
     */
    function isValidEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }
    
    /**
     * Show message to user
     */
    function showMessage(type, message, duration = 5000) {
        const $message = $(`<div class="knot4-message ${type}">${message}</div>`);
        
        // Remove existing messages
        $('.knot4-message').remove();
        
        // Add message after page title
        $('.knot4-page-title').after($message);
        
        // Auto-hide after duration
        if (duration > 0) {
            setTimeout(function() {
                $message.fadeOut(function() {
                    $message.remove();
                });
            }, duration);
        }
        
        // Scroll to message
        $('html, body').animate({
            scrollTop: $message.offset().top - 100
        }, 300);
    }
    
    /**
     * Show variable menu (future enhancement)
     */
    function showVariableMenu() {
        // Implementation for variable selection menu
        console.log('Variable menu would appear here');
    }
    
    /**
     * Auto-save functionality (future enhancement)
     */
    function initAutoSave() {
        let autoSaveTimer;
        
        function triggerAutoSave() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(function() {
                if (currentTemplate) {
                    autoSaveTemplate();
                }
            }, 30000); // Auto-save every 30 seconds
        }
        
        $('#template-subject, #template-content').on('input', triggerAutoSave);
        
        // Auto-save when editor content changes
        if (typeof tinyMCE !== 'undefined') {
            $(document).on('tinymce-editor-init', function(event, editor) {
                editor.on('input', triggerAutoSave);
            });
        }
    }
    
    /**
     * Auto-save template
     */
    function autoSaveTemplate() {
        const subject = $('#template-subject').val();
        const content = getEditorContent();
        
        $.ajax({
            url: knot4EmailSettings.ajaxUrl,
            type: 'POST',
            data: {
                action: 'knot4_autosave_template',
                template_key: currentTemplate,
                subject: subject,
                content: content,
                nonce: knot4EmailSettings.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Show subtle feedback
                    showMessage('info', knot4EmailSettings.strings.autoSaved, 2000);
                }
            }
        });
    }
    
    /**
     * Initialize drag and drop for variables (future enhancement)
     */
    function initDragDrop() {
        $('.knot4-variable-item').attr('draggable', true);
        
        $('.knot4-variable-item').on('dragstart', function(e) {
            const variable = $(this).find('.knot4-variable-code').text();
            e.originalEvent.dataTransfer.setData('text/plain', variable);
        });
        
        // Make editor droppable
        $('#template-content').on('dragover', function(e) {
            e.preventDefault();
        });
        
        $('#template-content').on('drop', function(e) {
            e.preventDefault();
            const variable = e.originalEvent.dataTransfer.getData('text/plain');
            if (variable.startsWith('{') && variable.endsWith('}')) {
                insertVariable(variable);
            }
        });
    }
    
    // Initialize additional features
    $(document).ready(function() {
        // Enable auto-save if pro
        if (typeof knot4EmailSettings.isPro !== 'undefined' && knot4EmailSettings.isPro) {
            initAutoSave();
        }
        
        // Enable drag and drop
        initDragDrop();
    });
    
})(jQuery);