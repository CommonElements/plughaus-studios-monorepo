<?php
/**
 * Advanced Dashboard Features for Pro Version
 */

if (!defined('ABSPATH')) {
    exit;
}

class PHPM_Advanced_Dashboard {
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Only load if license is valid
        if (!PHPM_License_Manager::is_valid()) {
            return;
        }
        
        // Enhanced dashboard widgets
        add_action('wp_dashboard_setup', array($this, 'add_pro_dashboard_widgets'));
        
        // Enhanced admin menu items
        add_action('admin_menu', array($this, 'add_pro_menu_items'), 20);
        
        // Dashboard assets
        add_action('admin_enqueue_scripts', array($this, 'enqueue_dashboard_assets'));
    }
    
    /**
     * Add pro dashboard widgets
     */
    public function add_pro_dashboard_widgets() {
        wp_add_dashboard_widget(
            'phpm_pro_analytics_widget',
            __('Property Analytics (Pro)', 'plughaus-property'),
            array($this, 'render_analytics_widget')
        );
        
        wp_add_dashboard_widget(
            'phpm_pro_financial_widget',
            __('Financial Overview (Pro)', 'plughaus-property'),
            array($this, 'render_financial_widget')
        );
    }
    
    /**
     * Add pro menu items
     */
    public function add_pro_menu_items() {
        // Analytics submenu
        add_submenu_page(
            'phpm-dashboard',
            __('Analytics', 'plughaus-property'),
            __('Analytics (Pro)', 'plughaus-property'),
            'manage_options',
            'phpm-analytics',
            array($this, 'render_analytics_page')
        );
        
        // Advanced Reports submenu
        add_submenu_page(
            'phpm-reports',
            __('Advanced Reports', 'plughaus-property'),
            __('Advanced Reports (Pro)', 'plughaus-property'),
            'manage_options',
            'phpm-advanced-reports',
            array($this, 'render_advanced_reports_page')
        );
    }
    
    /**
     * Enqueue dashboard assets
     */
    public function enqueue_dashboard_assets($hook) {
        if (strpos($hook, 'phpm') === false) {
            return;
        }
        
        wp_enqueue_script(
            'phpm-pro-dashboard',
            PHPM_PLUGIN_URL . 'pro/assets/js/dashboard.js',
            array('jquery', 'chart-js'),
            PHPM_VERSION,
            true
        );
        
        wp_enqueue_style(
            'phpm-pro-dashboard',
            PHPM_PLUGIN_URL . 'pro/assets/css/dashboard.css',
            array(),
            PHPM_VERSION
        );
        
        // Chart.js for analytics
        wp_enqueue_script(
            'chart-js',
            'https://cdn.jsdelivr.net/npm/chart.js',
            array(),
            '4.4.0',
            true
        );
    }
    
    /**
     * Render analytics widget
     */
    public function render_analytics_widget() {
        ?>
        <div class="phpm-pro-analytics-widget">
            <div class="phpm-chart-container">
                <canvas id="phpm-occupancy-chart" width="400" height="200"></canvas>
            </div>
            <div class="phpm-analytics-summary">
                <div class="phpm-metric">
                    <span class="phpm-metric-value">87%</span>
                    <span class="phpm-metric-label"><?php _e('Occupancy Rate', 'plughaus-property'); ?></span>
                </div>
                <div class="phpm-metric">
                    <span class="phpm-metric-value">$24,500</span>
                    <span class="phpm-metric-label"><?php _e('Monthly Revenue', 'plughaus-property'); ?></span>
                </div>
            </div>
        </div>
        <script>
        jQuery(document).ready(function($) {
            // Initialize occupancy chart
            var ctx = document.getElementById('phpm-occupancy-chart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Occupied', 'Vacant'],
                    datasets: [{
                        data: [87, 13],
                        backgroundColor: ['#28a745', '#dc3545']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });
        </script>
        <?php
    }
    
    /**
     * Render financial widget
     */
    public function render_financial_widget() {
        ?>
        <div class="phpm-pro-financial-widget">
            <div class="phpm-financial-metrics">
                <div class="phpm-metric">
                    <span class="phpm-metric-value">$28,750</span>
                    <span class="phpm-metric-label"><?php _e('Total Income (MTD)', 'plughaus-property'); ?></span>
                </div>
                <div class="phpm-metric">
                    <span class="phpm-metric-value">$4,250</span>
                    <span class="phpm-metric-label"><?php _e('Total Expenses (MTD)', 'plughaus-property'); ?></span>
                </div>
                <div class="phpm-metric phpm-metric-positive">
                    <span class="phpm-metric-value">$24,500</span>
                    <span class="phpm-metric-label"><?php _e('Net Income (MTD)', 'plughaus-property'); ?></span>
                </div>
            </div>
            <div class="phpm-financial-actions">
                <a href="<?php echo admin_url('admin.php?page=phpm-analytics'); ?>" class="button button-primary">
                    <?php _e('View Full Analytics', 'plughaus-property'); ?>
                </a>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render analytics page
     */
    public function render_analytics_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Property Analytics', 'plughaus-property'); ?></h1>
            
            <div class="phpm-analytics-dashboard">
                <div class="phpm-analytics-row">
                    <div class="phpm-analytics-card">
                        <h3><?php _e('Occupancy Trends', 'plughaus-property'); ?></h3>
                        <canvas id="phpm-occupancy-trends" width="400" height="200"></canvas>
                    </div>
                    
                    <div class="phpm-analytics-card">
                        <h3><?php _e('Revenue Analysis', 'plughaus-property'); ?></h3>
                        <canvas id="phpm-revenue-chart" width="400" height="200"></canvas>
                    </div>
                </div>
                
                <div class="phpm-analytics-row">
                    <div class="phpm-analytics-card">
                        <h3><?php _e('Property Performance', 'plughaus-property'); ?></h3>
                        <div class="phpm-property-performance-table">
                            <?php $this->render_property_performance_table(); ?>
                        </div>
                    </div>
                    
                    <div class="phpm-analytics-card">
                        <h3><?php _e('Maintenance Costs', 'plughaus-property'); ?></h3>
                        <canvas id="phpm-maintenance-costs" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            // Initialize charts with sample data
            <?php $this->output_chart_initialization_js(); ?>
        });
        </script>
        <?php
    }
    
    /**
     * Render advanced reports page
     */
    public function render_advanced_reports_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Advanced Reports', 'plughaus-property'); ?></h1>
            
            <div class="phpm-advanced-reports">
                <div class="phpm-report-filters">
                    <h3><?php _e('Report Filters', 'plughaus-property'); ?></h3>
                    <form method="get" action="">
                        <input type="hidden" name="page" value="phpm-advanced-reports">
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php _e('Date Range', 'plughaus-property'); ?></th>
                                <td>
                                    <input type="date" name="start_date" value="<?php echo date('Y-m-01'); ?>">
                                    <span> - </span>
                                    <input type="date" name="end_date" value="<?php echo date('Y-m-t'); ?>">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Properties', 'plughaus-property'); ?></th>
                                <td>
                                    <select name="property_ids[]" multiple>
                                        <option value=""><?php _e('All Properties', 'plughaus-property'); ?></option>
                                        <?php $this->output_property_options(); ?>
                                    </select>
                                </td>
                            </tr>
                        </table>
                        
                        <p class="submit">
                            <input type="submit" class="button button-primary" value="<?php _e('Generate Report', 'plughaus-property'); ?>">
                            <input type="submit" name="export_pdf" class="button" value="<?php _e('Export to PDF', 'plughaus-property'); ?>">
                            <input type="submit" name="export_excel" class="button" value="<?php _e('Export to Excel', 'plughaus-property'); ?>">
                        </p>
                    </form>
                </div>
                
                <div class="phpm-report-results">
                    <?php $this->render_advanced_report_results(); ?>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render property performance table
     */
    private function render_property_performance_table() {
        // This would query actual property data
        $sample_data = array(
            array('property' => '123 Main St', 'occupancy' => '95%', 'revenue' => '$2,400', 'roi' => '8.5%'),
            array('property' => '456 Oak Ave', 'occupancy' => '80%', 'revenue' => '$1,800', 'roi' => '6.2%'),
            array('property' => '789 Pine Rd', 'occupancy' => '90%', 'revenue' => '$2,100', 'roi' => '7.8%'),
        );
        
        ?>
        <table class="widefat striped">
            <thead>
                <tr>
                    <th><?php _e('Property', 'plughaus-property'); ?></th>
                    <th><?php _e('Occupancy', 'plughaus-property'); ?></th>
                    <th><?php _e('Monthly Revenue', 'plughaus-property'); ?></th>
                    <th><?php _e('ROI', 'plughaus-property'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sample_data as $row): ?>
                <tr>
                    <td><?php echo esc_html($row['property']); ?></td>
                    <td><?php echo esc_html($row['occupancy']); ?></td>
                    <td><?php echo esc_html($row['revenue']); ?></td>
                    <td><?php echo esc_html($row['roi']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    }
    
    /**
     * Output chart initialization JavaScript
     */
    private function output_chart_initialization_js() {
        ?>
        // Sample data - in production this would come from the database
        var occupancyData = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Occupancy Rate (%)',
                data: [85, 87, 82, 90, 88, 87],
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4
            }]
        };
        
        var revenueData = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Monthly Revenue ($)',
                data: [22000, 24000, 21500, 26000, 24500, 24750],
                borderColor: '#007cba',
                backgroundColor: 'rgba(0, 124, 186, 0.1)',
                tension: 0.4
            }]
        };
        
        // Initialize charts
        new Chart(document.getElementById('phpm-occupancy-trends'), {
            type: 'line',
            data: occupancyData,
            options: { responsive: true, maintainAspectRatio: false }
        });
        
        new Chart(document.getElementById('phpm-revenue-chart'), {
            type: 'line',
            data: revenueData,
            options: { responsive: true, maintainAspectRatio: false }
        });
        <?php
    }
    
    /**
     * Output property options for select
     */
    private function output_property_options() {
        // This would query actual properties
        $sample_properties = array(
            1 => '123 Main St',
            2 => '456 Oak Ave',
            3 => '789 Pine Rd'
        );
        
        foreach ($sample_properties as $id => $name) {
            echo '<option value="' . esc_attr($id) . '">' . esc_html($name) . '</option>';
        }
    }
    
    /**
     * Render advanced report results
     */
    private function render_advanced_report_results() {
        echo '<p>' . __('Advanced report results would be displayed here based on the selected filters.', 'plughaus-property') . '</p>';
    }
}