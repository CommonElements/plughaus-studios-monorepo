/**
 * Reports Admin JavaScript for Knot4
 * 
 * @package Knot4
 * @since 1.0.0
 */

(function($) {
    'use strict';
    
    let charts = {};
    let currentDateRange = '30days';
    
    $(document).ready(function() {
        initReports();
    });
    
    /**
     * Initialize reports functionality
     */
    function initReports() {
        initDateRangeFilter();
        initCharts();
        initExportButtons();
        initRealtimeUpdates();
    }
    
    /**
     * Initialize date range filter
     */
    function initDateRangeFilter() {
        $('#knot4-date-range').on('change', function() {
            const range = $(this).val();
            
            if (range === 'custom') {
                $('#knot4-custom-date-range').show();
            } else {
                $('#knot4-custom-date-range').hide();
                currentDateRange = range;
            }
        });
        
        $('#knot4-apply-filter').on('click', function() {
            const range = $('#knot4-date-range').val();
            
            if (range === 'custom') {
                const startDate = $('#start-date').val();
                const endDate = $('#end-date').val();
                
                if (!startDate || !endDate) {
                    showMessage('error', 'Please select both start and end dates.');
                    return;
                }
                
                if (new Date(startDate) > new Date(endDate)) {
                    showMessage('error', 'Start date must be before end date.');
                    return;
                }
                
                currentDateRange = range;
            } else {
                currentDateRange = range;
            }
            
            updateReports();
        });
    }
    
    /**
     * Initialize charts
     */
    function initCharts() {
        if (typeof Chart === 'undefined') {
            console.warn('Chart.js not loaded');
            return;
        }
        
        // Set default Chart.js options
        Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
        Chart.defaults.color = '#666';
        Chart.defaults.borderColor = '#e1e1e1';
        Chart.defaults.backgroundColor = 'rgba(102, 126, 234, 0.1)';
        
        initDonationsChart();
        initDonorsChart();
        initDonationMethodsChart();
        initDonorTypesChart();
        initEventChartsIfExists();
    }
    
    /**
     * Initialize donations chart
     */
    function initDonationsChart() {
        const canvas = document.getElementById('donations-chart');
        if (!canvas) return;
        
        const ctx = canvas.getContext('2d');
        
        charts.donations = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Donations',
                    data: [],
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
        
        // Load data
        loadChartData('donations_chart', 'donations');
    }
    
    /**
     * Initialize donors chart
     */
    function initDonorsChart() {
        const canvas = document.getElementById('donors-chart');
        if (!canvas) return;
        
        const ctx = canvas.getContext('2d');
        
        charts.donors = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'New Donors',
                    data: [],
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
        
        // Load data
        loadChartData('donors_chart', 'donors');
    }
    
    /**
     * Initialize donation methods chart
     */
    function initDonationMethodsChart() {
        const canvas = document.getElementById('donation-methods-chart');
        if (!canvas) return;
        
        const ctx = canvas.getContext('2d');
        
        charts.donationMethods = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Credit Card', 'PayPal', 'Bank Transfer', 'Other'],
                datasets: [{
                    data: [65, 25, 8, 2],
                    backgroundColor: [
                        '#667eea',
                        '#28a745',
                        '#ffc107',
                        '#dc3545'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
    }
    
    /**
     * Initialize donor types chart
     */
    function initDonorTypesChart() {
        const canvas = document.getElementById('donor-types-chart');
        if (!canvas) return;
        
        const ctx = canvas.getContext('2d');
        
        charts.donorTypes = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Individual', 'Business', 'Foundation', 'Organization'],
                datasets: [{
                    data: [70, 20, 7, 3],
                    backgroundColor: [
                        '#667eea',
                        '#28a745',
                        '#ffc107',
                        '#dc3545'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
    }
    
    /**
     * Initialize event charts if they exist
     */
    function initEventChartsIfExists() {
        // Event attendance chart
        const attendanceCanvas = document.getElementById('event-attendance-chart');
        if (attendanceCanvas) {
            const ctx = attendanceCanvas.getContext('2d');
            
            charts.eventAttendance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Attendees',
                        data: [120, 85, 160, 200, 175, 145],
                        backgroundColor: 'rgba(102, 126, 234, 0.8)',
                        borderColor: '#667eea',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
        
        // Event types chart
        const typesCanvas = document.getElementById('event-types-chart');
        if (typesCanvas) {
            const ctx = typesCanvas.getContext('2d');
            
            charts.eventTypes = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Fundraising', 'Educational', 'Social', 'Volunteer', 'Other'],
                    datasets: [{
                        data: [40, 25, 15, 15, 5],
                        backgroundColor: [
                            '#667eea',
                            '#28a745',
                            '#ffc107',
                            '#17a2b8',
                            '#dc3545'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });
        }
    }
    
    /**
     * Load chart data via AJAX
     */
    function loadChartData(reportType, chartKey) {
        $.ajax({
            url: knot4Reports.ajaxUrl,
            type: 'POST',
            data: {
                action: 'knot4_generate_report',
                report_type: reportType,
                date_range: currentDateRange,
                nonce: knot4Reports.nonce
            },
            success: function(response) {
                if (response.success && charts[chartKey]) {
                    charts[chartKey].data = response.data;
                    charts[chartKey].update();
                }
            },
            error: function() {
                showMessage('error', knot4Reports.strings.ajaxError);
            }
        });
    }
    
    /**
     * Initialize export buttons
     */
    function initExportButtons() {
        $('.knot4-export-btn').on('click', function() {
            const $button = $(this);
            const reportType = $button.data('report');
            const format = $button.data('format') || 'csv';
            const originalText = $button.text();
            
            // Check if this is a Pro feature
            if (format === 'pdf' && !knot4Reports.isPro) {
                showProUpgradeNotice();
                return;
            }
            
            $button.prop('disabled', true).text(knot4Reports.strings.exporting);
            
            $.ajax({
                url: knot4Reports.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'knot4_export_report',
                    report_type: reportType,
                    format: format,
                    date_range: currentDateRange,
                    nonce: knot4Reports.nonce
                },
                success: function(response) {
                    if (response.success) {
                        showMessage('success', knot4Reports.strings.exportSuccess);
                        
                        // In a real implementation, this would trigger a file download
                        if (response.data && response.data.download_url) {
                            window.location.href = response.data.download_url;
                        }
                    } else {
                        showMessage('error', response.data || knot4Reports.strings.exportFailed);
                    }
                },
                error: function() {
                    showMessage('error', knot4Reports.strings.ajaxError);
                },
                complete: function() {
                    $button.prop('disabled', false).text(originalText);
                }
            });
        });
    }
    
    /**
     * Initialize realtime updates
     */
    function initRealtimeUpdates() {
        // Update charts every 5 minutes
        setInterval(function() {
            if (document.visibilityState === 'visible') {
                updateCharts();
            }
        }, 300000); // 5 minutes
        
        // Update when page becomes visible
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                updateCharts();
            }
        });
    }
    
    /**
     * Update all charts
     */
    function updateCharts() {
        if (charts.donations) {
            loadChartData('donations_chart', 'donations');
        }
        if (charts.donors) {
            loadChartData('donors_chart', 'donors');
        }
    }
    
    /**
     * Update reports based on date range
     */
    function updateReports() {
        showMessage('info', knot4Reports.strings.generating);
        
        // Update charts
        updateCharts();
        
        // Reload page with new date range (simplified approach)
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('range', currentDateRange);
        
        if (currentDateRange === 'custom') {
            urlParams.set('start_date', $('#start-date').val());
            urlParams.set('end_date', $('#end-date').val());
        }
        
        window.location.search = urlParams.toString();
    }
    
    /**
     * Show message to user
     */
    function showMessage(type, message, duration = 5000) {
        // Remove existing messages
        $('.knot4-message').remove();
        
        const $message = $(`<div class="knot4-message ${type}">${message}</div>`);
        
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
     * Show Pro upgrade notice
     */
    function showProUpgradeNotice() {
        const $notice = $(`
            <div class="knot4-pro-upgrade-notice" style="margin: 20px 0;">
                <h3>Pro Feature</h3>
                <p>PDF exports are available in the Pro version. Upgrade to access advanced reporting features.</p>
                <a href="#" class="button button-primary">Upgrade to Pro</a>
            </div>
        `);
        
        $notice.insertAfter('.knot4-page-title');
        
        setTimeout(function() {
            $notice.fadeOut(function() {
                $notice.remove();
            });
        }, 10000);
    }
    
    /**
     * Format currency for display
     */
    function formatCurrency(amount) {
        return '$' + parseFloat(amount).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
    
    /**
     * Format number for display
     */
    function formatNumber(number) {
        return parseInt(number).toLocaleString('en-US');
    }
    
    /**
     * Resize charts on window resize
     */
    $(window).on('resize', function() {
        Object.keys(charts).forEach(function(key) {
            if (charts[key]) {
                charts[key].resize();
            }
        });
    });
    
    /**
     * Export chart as image (Pro feature)
     */
    function exportChartAsImage(chartKey, filename) {
        if (!knot4Reports.isPro) {
            showProUpgradeNotice();
            return;
        }
        
        if (charts[chartKey]) {
            const canvas = charts[chartKey].canvas;
            const link = document.createElement('a');
            link.download = filename || 'chart.png';
            link.href = canvas.toDataURL();
            link.click();
        }
    }
    
    /**
     * Print report
     */
    function printReport() {
        window.print();
    }
    
    // Add keyboard shortcuts
    $(document).on('keydown', function(e) {
        // Ctrl/Cmd + P for print
        if ((e.ctrlKey || e.metaKey) && e.keyCode === 80) {
            e.preventDefault();
            printReport();
        }
        
        // Ctrl/Cmd + E for export
        if ((e.ctrlKey || e.metaKey) && e.keyCode === 69) {
            e.preventDefault();
            $('.knot4-export-btn').first().click();
        }
    });
    
    // Expose some functions globally for use in other scripts
    window.knot4Reports = {
        updateCharts: updateCharts,
        showMessage: showMessage,
        formatCurrency: formatCurrency,
        formatNumber: formatNumber,
        exportChartAsImage: exportChartAsImage,
        printReport: printReport
    };
    
})(jQuery);