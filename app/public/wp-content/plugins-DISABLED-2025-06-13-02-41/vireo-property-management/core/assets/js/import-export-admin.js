/**
 * Import/Export Admin JavaScript
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Export form submission
        $('#export-form').on('submit', function(e) {
            e.preventDefault();
            
            var formData = new FormData(this);
            formData.append('action', 'phpm_export_data');
            formData.append('nonce', phpmImportExport.nonce);
            
            // Show progress
            showProgress(phpmImportExport.strings.exporting);
            
            $.ajax({
                url: phpmImportExport.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        if (response.data.download_url) {
                            // ZIP file - direct download
                            downloadFile(response.data.download_url, response.data.filename);
                            showSuccess(phpmImportExport.strings.export_success);
                        } else {
                            // Direct content - trigger download
                            downloadContent(response.data.content, response.data.filename, response.data.mimetype);
                            showSuccess(phpmImportExport.strings.export_success);
                        }
                    } else {
                        showError(response.data || phpmImportExport.strings.error);
                    }
                },
                error: function() {
                    showError(phpmImportExport.strings.error);
                },
                complete: function() {
                    hideProgress();
                }
            });
        });
        
        // Import form submission
        $('#import-form').on('submit', function(e) {
            e.preventDefault();
            
            var fileInput = $('#import-file')[0];
            if (!fileInput.files.length) {
                showError(phpmImportExport.strings.select_file);
                return;
            }
            
            if (!confirm(phpmImportExport.strings.confirm_import)) {
                return;
            }
            
            var formData = new FormData(this);
            formData.append('action', 'phpm_import_data');
            formData.append('nonce', phpmImportExport.nonce);
            
            // Show progress
            showProgress(phpmImportExport.strings.importing);
            
            $.ajax({
                url: phpmImportExport.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        showImportResults(response.data);
                        
                        // Reset form
                        $('#import-form')[0].reset();
                    } else {
                        showError(response.data || phpmImportExport.strings.error);
                    }
                },
                error: function() {
                    showError(phpmImportExport.strings.error);
                },
                complete: function() {
                    hideProgress();
                }
            });
        });
        
        // Template download buttons
        $('.download-template').on('click', function(e) {
            e.preventDefault();
            
            var dataType = $(this).data('type');
            var $button = $(this);
            
            $button.prop('disabled', true);
            showProgress(phpmImportExport.strings.downloading);
            
            $.ajax({
                url: phpmImportExport.ajax_url,
                type: 'POST',
                data: {
                    action: 'phpm_download_template',
                    data_type: dataType,
                    nonce: phpmImportExport.nonce
                },
                success: function(response) {
                    if (response.success) {
                        downloadContent(response.data.content, response.data.filename, response.data.mimetype);
                    } else {
                        showError(response.data || phpmImportExport.strings.error);
                    }
                },
                error: function() {
                    showError(phpmImportExport.strings.error);
                },
                complete: function() {
                    $button.prop('disabled', false);
                    hideProgress();
                }
            });
        });
        
        // Export data type change - show/hide filters
        $('#export-data-type').on('change', function() {
            var dataType = $(this).val();
            var $filtersRow = $('.export-filters-row');
            var $statusFilters = $('.status-filters');
            
            if (dataType && dataType !== 'all') {
                $filtersRow.show();
                
                // Show status filters for applicable data types
                if (['tenants', 'leases', 'maintenance'].indexOf(dataType) !== -1) {
                    $statusFilters.show();
                    updateStatusOptions(dataType);
                } else {
                    $statusFilters.hide();
                }
            } else {
                $filtersRow.hide();
            }
        });
        
        // File input validation
        $('#import-file').on('change', function() {
            var file = this.files[0];
            if (file) {
                var maxSize = 10 * 1024 * 1024; // 10MB
                var allowedTypes = ['text/csv', 'application/json'];
                
                if (file.size > maxSize) {
                    showError('File size exceeds 10MB limit.');
                    this.value = '';
                    return;
                }
                
                var fileExtension = file.name.split('.').pop().toLowerCase();
                if (['csv', 'json'].indexOf(fileExtension) === -1) {
                    showError('Please select a CSV or JSON file.');
                    this.value = '';
                    return;
                }
                
                // Show file info
                var fileInfo = file.name + ' (' + formatFileSize(file.size) + ')';
                $(this).next('.description').html('Selected: <strong>' + fileInfo + '</strong>');
            }
        });
        
        /**
         * Helper functions
         */
        
        function showProgress(message) {
            $('#progress-message').text(message);
            $('#import-export-progress').show();
            $('#import-export-results').hide();
            
            // Scroll to progress
            $('html, body').animate({
                scrollTop: $('#import-export-progress').offset().top - 100
            }, 500);
        }
        
        function hideProgress() {
            $('#import-export-progress').hide();
        }
        
        function showSuccess(message) {
            showNotice('success', message);
        }
        
        function showError(message) {
            showNotice('error', message);
        }
        
        function showNotice(type, message) {
            var noticeClass = 'notice-' + type;
            var $notice = $('<div class="notice ' + noticeClass + ' is-dismissible"><p>' + message + '</p></div>');
            
            // Remove existing notices
            $('.wrap .notice').not('#import-export-progress').remove();
            
            // Insert notice
            $('.wrap h1').after($notice);
            
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
        
        function downloadFile(url, filename) {
            var link = document.createElement('a');
            link.href = url;
            link.download = filename;
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
        
        function downloadContent(content, filename, mimetype) {
            var blob = new Blob([content], { type: mimetype });
            var url = window.URL.createObjectURL(blob);
            downloadFile(url, filename);
            window.URL.revokeObjectURL(url);
        }
        
        function showImportResults(results) {
            var $resultsDiv = $('#import-export-results');
            var $resultsContent = $('#results-content');
            
            var html = '';
            
            // Handle single data type results
            if (typeof results.imported !== 'undefined') {
                html += '<div class="import-summary">';
                html += '<h3>' + phpmImportExport.strings.import_success + '</h3>';
                html += '<p><strong>Imported:</strong> ' + results.imported + ' records</p>';
                
                if (results.total_rows) {
                    html += '<p><strong>Total rows processed:</strong> ' + results.total_rows + '</p>';
                } else if (results.total_items) {
                    html += '<p><strong>Total items processed:</strong> ' + results.total_items + '</p>';
                }
                
                if (results.errors && results.errors.length > 0) {
                    html += '<div class="import-errors">';
                    html += '<h4>Errors (' + results.errors.length + '):</h4>';
                    html += '<ul>';
                    results.errors.forEach(function(error) {
                        html += '<li>' + error + '</li>';
                    });
                    html += '</ul>';
                    html += '</div>';
                }
                html += '</div>';
            } else {
                // Handle full import results (multiple data types)
                html += '<div class="import-summary">';
                html += '<h3>' + phpmImportExport.strings.import_success + '</h3>';
                
                var totalImported = 0;
                var totalErrors = 0;
                
                for (var dataType in results) {
                    if (results.hasOwnProperty(dataType)) {
                        var typeResult = results[dataType];
                        totalImported += typeResult.imported;
                        totalErrors += typeResult.errors.length;
                        
                        html += '<div class="data-type-result">';
                        html += '<h4>' + capitalizeFirst(dataType) + '</h4>';
                        html += '<p>Imported: ' + typeResult.imported + ' of ' + typeResult.total_items + '</p>';
                        
                        if (typeResult.errors.length > 0) {
                            html += '<details>';
                            html += '<summary>Errors (' + typeResult.errors.length + ')</summary>';
                            html += '<ul>';
                            typeResult.errors.forEach(function(error) {
                                html += '<li>' + error + '</li>';
                            });
                            html += '</ul>';
                            html += '</details>';
                        }
                        html += '</div>';
                    }
                }
                
                html += '<div class="total-summary">';
                html += '<p><strong>Total imported:</strong> ' + totalImported + ' records</p>';
                if (totalErrors > 0) {
                    html += '<p><strong>Total errors:</strong> ' + totalErrors + '</p>';
                }
                html += '</div>';
                html += '</div>';
            }
            
            $resultsContent.html(html);
            $resultsDiv.show();
            
            // Scroll to results
            $('html, body').animate({
                scrollTop: $resultsDiv.offset().top - 100
            }, 500);
        }
        
        function updateStatusOptions(dataType) {
            var $statusSelect = $('select[name="status"]');
            var options = '<option value="">All Statuses</option>';
            
            switch (dataType) {
                case 'tenants':
                    options += '<option value="current">Current</option>';
                    options += '<option value="former">Former</option>';
                    break;
                case 'leases':
                    options += '<option value="active">Active</option>';
                    options += '<option value="pending">Pending</option>';
                    options += '<option value="expired">Expired</option>';
                    options += '<option value="terminated">Terminated</option>';
                    break;
                case 'maintenance':
                    options += '<option value="open">Open</option>';
                    options += '<option value="in_progress">In Progress</option>';
                    options += '<option value="completed">Completed</option>';
                    options += '<option value="closed">Closed</option>';
                    break;
            }
            
            $statusSelect.html(options);
        }
        
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            
            var k = 1024;
            var sizes = ['Bytes', 'KB', 'MB', 'GB'];
            var i = Math.floor(Math.log(bytes) / Math.log(k));
            
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
        
        function capitalizeFirst(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }
    });
    
})(jQuery);