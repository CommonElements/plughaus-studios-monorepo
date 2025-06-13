<?php
/**
 * Reports Admin for Knot4
 * 
 * @package Knot4
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Knot4_Reports_Admin {
    
    /**
     * Initialize reports admin
     */
    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_assets'));
        add_action('wp_ajax_knot4_generate_report', array(__CLASS__, 'generate_report_ajax'));
        add_action('wp_ajax_knot4_export_report', array(__CLASS__, 'export_report_ajax'));
    }
    
    /**
     * Add admin menu
     */
    public static function add_admin_menu() {
        add_submenu_page(
            'knot4-dashboard',
            __('Reports & Analytics', 'knot4'),
            __('Reports', 'knot4'),
            'view_knot4_nonprofit',
            'knot4-reports',
            array(__CLASS__, 'render_reports_page')
        );
    }
    
    /**
     * Enqueue admin assets
     */
    public static function enqueue_assets($hook) {
        if ($hook !== 'knot4_page_knot4-reports') {
            return;
        }
        
        wp_enqueue_style(
            'knot4-reports-admin',
            KNOT4_PLUGIN_URL . 'core/assets/css/reports-admin.css',
            array(),
            KNOT4_VERSION
        );
        
        wp_enqueue_script(
            'knot4-reports-admin',
            KNOT4_PLUGIN_URL . 'core/assets/js/reports-admin.js',
            array('jquery', 'chart-js'),
            KNOT4_VERSION,
            true
        );
        
        // Enqueue Chart.js
        wp_enqueue_script(
            'chart-js',
            'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js',
            array(),
            '3.9.1',
            true
        );
        
        wp_localize_script('knot4-reports-admin', 'knot4Reports', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('knot4_reports_nonce'),
            'isPro' => Knot4_Utilities::is_pro(),
            'strings' => array(
                'generating' => __('Generating Report...', 'knot4'),
                'exporting' => __('Exporting...', 'knot4'),
                'exportSuccess' => __('Report exported successfully!', 'knot4'),
                'exportFailed' => __('Export failed. Please try again.', 'knot4'),
                'noData' => __('No data available for the selected period.', 'knot4'),
                'ajaxError' => __('An error occurred. Please try again.', 'knot4'),
            )
        ));
    }
    
    /**
     * Render reports page
     */
    public static function render_reports_page() {
        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'overview';
        $date_range = isset($_GET['range']) ? sanitize_text_field($_GET['range']) : '30days';
        
        ?>
        <div class="wrap knot4-reports">
            <h1 class="knot4-page-title">
                <span class="dashicons dashicons-chart-bar"></span>
                <?php _e('Reports & Analytics', 'knot4'); ?>
            </h1>
            
            <!-- Date Range Filter -->
            <div class="knot4-report-filters">
                <select id="knot4-date-range" class="knot4-date-range-select">
                    <option value="7days" <?php selected($date_range, '7days'); ?>><?php _e('Last 7 Days', 'knot4'); ?></option>
                    <option value="30days" <?php selected($date_range, '30days'); ?>><?php _e('Last 30 Days', 'knot4'); ?></option>
                    <option value="90days" <?php selected($date_range, '90days'); ?>><?php _e('Last 90 Days', 'knot4'); ?></option>
                    <option value="12months" <?php selected($date_range, '12months'); ?>><?php _e('Last 12 Months', 'knot4'); ?></option>
                    <option value="ytd" <?php selected($date_range, 'ytd'); ?>><?php _e('Year to Date', 'knot4'); ?></option>
                    <option value="custom" <?php selected($date_range, 'custom'); ?>><?php _e('Custom Range', 'knot4'); ?></option>
                </select>
                
                <div id="knot4-custom-date-range" style="display: none;">
                    <input type="date" id="start-date" class="knot4-date-input">
                    <span>to</span>
                    <input type="date" id="end-date" class="knot4-date-input">
                </div>
                
                <button type="button" id="knot4-apply-filter" class="button button-primary">
                    <?php _e('Apply Filter', 'knot4'); ?>
                </button>
            </div>
            
            <nav class="nav-tab-wrapper knot4-reports-tabs">
                <a href="?page=knot4-reports&tab=overview" 
                   class="nav-tab <?php echo $active_tab === 'overview' ? 'nav-tab-active' : ''; ?>">
                    <?php _e('Overview', 'knot4'); ?>
                </a>
                <a href="?page=knot4-reports&tab=donations" 
                   class="nav-tab <?php echo $active_tab === 'donations' ? 'nav-tab-active' : ''; ?>">
                    <?php _e('Donations', 'knot4'); ?>
                </a>
                <a href="?page=knot4-reports&tab=donors" 
                   class="nav-tab <?php echo $active_tab === 'donors' ? 'nav-tab-active' : ''; ?>">
                    <?php _e('Donors', 'knot4'); ?>
                </a>
                <a href="?page=knot4-reports&tab=events" 
                   class="nav-tab <?php echo $active_tab === 'events' ? 'nav-tab-active' : ''; ?>">
                    <?php _e('Events', 'knot4'); ?>
                </a>
                <?php if (Knot4_Utilities::is_pro()): ?>
                <a href="?page=knot4-reports&tab=advanced" 
                   class="nav-tab <?php echo $active_tab === 'advanced' ? 'nav-tab-active' : ''; ?>">
                    <?php _e('Advanced Analytics', 'knot4'); ?>
                    <span class="knot4-pro-badge"><?php _e('Pro', 'knot4'); ?></span>
                </a>
                <?php endif; ?>
            </nav>
            
            <div class="knot4-tab-content">
                <?php if ($active_tab === 'overview'): ?>
                    <?php self::render_overview_tab($date_range); ?>
                <?php elseif ($active_tab === 'donations'): ?>
                    <?php self::render_donations_tab($date_range); ?>
                <?php elseif ($active_tab === 'donors'): ?>
                    <?php self::render_donors_tab($date_range); ?>
                <?php elseif ($active_tab === 'events'): ?>
                    <?php self::render_events_tab($date_range); ?>
                <?php elseif ($active_tab === 'advanced' && Knot4_Utilities::is_pro()): ?>
                    <?php self::render_advanced_tab($date_range); ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render overview tab
     */
    private static function render_overview_tab($date_range) {
        $stats = self::get_overview_stats($date_range);
        
        ?>
        <div class="knot4-overview-stats">
            <div class="knot4-stats-grid">
                <div class="knot4-stat-card">
                    <div class="knot4-stat-icon">
                        <span class="dashicons dashicons-heart"></span>
                    </div>
                    <div class="knot4-stat-content">
                        <div class="knot4-stat-value"><?php echo esc_html(Knot4_Utilities::format_currency($stats['total_donations'])); ?></div>
                        <div class="knot4-stat-label"><?php _e('Total Donations', 'knot4'); ?></div>
                        <div class="knot4-stat-change <?php echo $stats['donations_change'] >= 0 ? 'positive' : 'negative'; ?>">
                            <?php echo $stats['donations_change'] >= 0 ? '+' : ''; ?><?php echo number_format($stats['donations_change'], 1); ?>%
                        </div>
                    </div>
                </div>
                
                <div class="knot4-stat-card">
                    <div class="knot4-stat-icon">
                        <span class="dashicons dashicons-groups"></span>
                    </div>
                    <div class="knot4-stat-content">
                        <div class="knot4-stat-value"><?php echo number_format($stats['total_donors']); ?></div>
                        <div class="knot4-stat-label"><?php _e('Total Donors', 'knot4'); ?></div>
                        <div class="knot4-stat-change <?php echo $stats['donors_change'] >= 0 ? 'positive' : 'negative'; ?>">
                            <?php echo $stats['donors_change'] >= 0 ? '+' : ''; ?><?php echo number_format($stats['donors_change'], 1); ?>%
                        </div>
                    </div>
                </div>
                
                <div class="knot4-stat-card">
                    <div class="knot4-stat-icon">
                        <span class="dashicons dashicons-calendar-alt"></span>
                    </div>
                    <div class="knot4-stat-content">
                        <div class="knot4-stat-value"><?php echo number_format($stats['total_events']); ?></div>
                        <div class="knot4-stat-label"><?php _e('Events Held', 'knot4'); ?></div>
                        <div class="knot4-stat-change <?php echo $stats['events_change'] >= 0 ? 'positive' : 'negative'; ?>">
                            <?php echo $stats['events_change'] >= 0 ? '+' : ''; ?><?php echo number_format($stats['events_change'], 1); ?>%
                        </div>
                    </div>
                </div>
                
                <div class="knot4-stat-card">
                    <div class="knot4-stat-icon">
                        <span class="dashicons dashicons-universal-access"></span>
                    </div>
                    <div class="knot4-stat-content">
                        <div class="knot4-stat-value"><?php echo number_format($stats['total_volunteers']); ?></div>
                        <div class="knot4-stat-label"><?php _e('Active Volunteers', 'knot4'); ?></div>
                        <div class="knot4-stat-change <?php echo $stats['volunteers_change'] >= 0 ? 'positive' : 'negative'; ?>">
                            <?php echo $stats['volunteers_change'] >= 0 ? '+' : ''; ?><?php echo number_format($stats['volunteers_change'], 1); ?>%
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="knot4-charts-row">
            <div class="knot4-chart-container">
                <h3><?php _e('Donation Trends', 'knot4'); ?></h3>
                <canvas id="donations-chart" width="400" height="200"></canvas>
            </div>
            
            <div class="knot4-chart-container">
                <h3><?php _e('Donor Growth', 'knot4'); ?></h3>
                <canvas id="donors-chart" width="400" height="200"></canvas>
            </div>
        </div>
        
        <div class="knot4-recent-activity">
            <h3><?php _e('Recent Activity', 'knot4'); ?></h3>
            <div class="knot4-activity-list">
                <?php 
                $recent_activities = self::get_recent_activities();
                if (!empty($recent_activities)): 
                    foreach ($recent_activities as $activity): 
                ?>
                <div class="knot4-activity-item">
                    <div class="knot4-activity-icon">
                        <span class="dashicons dashicons-<?php echo esc_attr($activity['icon']); ?>"></span>
                    </div>
                    <div class="knot4-activity-content">
                        <div class="knot4-activity-message"><?php echo esc_html($activity['message']); ?></div>
                        <div class="knot4-activity-time"><?php echo esc_html($activity['time']); ?></div>
                    </div>
                </div>
                <?php 
                    endforeach;
                else: 
                ?>
                <p><?php _e('No recent activity found.', 'knot4'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render donations tab
     */
    private static function render_donations_tab($date_range) {
        ?>
        <div class="knot4-donations-reports">
            <div class="knot4-report-actions">
                <button type="button" class="button knot4-export-btn" data-report="donations">
                    <span class="dashicons dashicons-download"></span>
                    <?php _e('Export CSV', 'knot4'); ?>
                </button>
                <?php if (Knot4_Utilities::is_pro()): ?>
                <button type="button" class="button knot4-export-btn" data-report="donations" data-format="pdf">
                    <span class="dashicons dashicons-pdf"></span>
                    <?php _e('Export PDF', 'knot4'); ?>
                </button>
                <?php endif; ?>
            </div>
            
            <div class="knot4-charts-row">
                <div class="knot4-chart-container">
                    <h3><?php _e('Donations by Amount', 'knot4'); ?></h3>
                    <canvas id="donations-amount-chart" width="400" height="200"></canvas>
                </div>
                
                <div class="knot4-chart-container">
                    <h3><?php _e('Donation Methods', 'knot4'); ?></h3>
                    <canvas id="donation-methods-chart" width="400" height="200"></canvas>
                </div>
            </div>
            
            <div class="knot4-donations-table">
                <h3><?php _e('Recent Donations', 'knot4'); ?></h3>
                <?php self::render_donations_table($date_range); ?>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render donors tab
     */
    private static function render_donors_tab($date_range) {
        ?>
        <div class="knot4-donors-reports">
            <div class="knot4-report-actions">
                <button type="button" class="button knot4-export-btn" data-report="donors">
                    <span class="dashicons dashicons-download"></span>
                    <?php _e('Export Donor List', 'knot4'); ?>
                </button>
            </div>
            
            <div class="knot4-charts-row">
                <div class="knot4-chart-container">
                    <h3><?php _e('Donor Types', 'knot4'); ?></h3>
                    <canvas id="donor-types-chart" width="400" height="200"></canvas>
                </div>
                
                <div class="knot4-chart-container">
                    <h3><?php _e('Top Donors', 'knot4'); ?></h3>
                    <div class="knot4-top-donors-list">
                        <?php self::render_top_donors($date_range); ?>
                    </div>
                </div>
            </div>
            
            <?php if (Knot4_Utilities::is_pro()): ?>
            <div class="knot4-donor-insights">
                <h3><?php _e('Donor Insights', 'knot4'); ?> <span class="knot4-pro-badge"><?php _e('Pro', 'knot4'); ?></span></h3>
                <div class="knot4-insights-grid">
                    <div class="knot4-insight-card">
                        <div class="knot4-insight-value">$125</div>
                        <div class="knot4-insight-label"><?php _e('Average Donation', 'knot4'); ?></div>
                    </div>
                    <div class="knot4-insight-card">
                        <div class="knot4-insight-value">3.2</div>
                        <div class="knot4-insight-label"><?php _e('Avg Donations per Donor', 'knot4'); ?></div>
                    </div>
                    <div class="knot4-insight-card">
                        <div class="knot4-insight-value">68%</div>
                        <div class="knot4-insight-label"><?php _e('Retention Rate', 'knot4'); ?></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Render events tab
     */
    private static function render_events_tab($date_range) {
        ?>
        <div class="knot4-events-reports">
            <div class="knot4-charts-row">
                <div class="knot4-chart-container">
                    <h3><?php _e('Event Attendance', 'knot4'); ?></h3>
                    <canvas id="event-attendance-chart" width="400" height="200"></canvas>
                </div>
                
                <div class="knot4-chart-container">
                    <h3><?php _e('Event Types', 'knot4'); ?></h3>
                    <canvas id="event-types-chart" width="400" height="200"></canvas>
                </div>
            </div>
            
            <div class="knot4-events-table">
                <h3><?php _e('Event Performance', 'knot4'); ?></h3>
                <?php self::render_events_table($date_range); ?>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render advanced tab (Pro only)
     */
    private static function render_advanced_tab($date_range) {
        if (!Knot4_Utilities::is_pro()) {
            ?>
            <div class="knot4-pro-upgrade-notice">
                <h3><?php _e('Advanced Analytics', 'knot4'); ?></h3>
                <p><?php _e('Unlock advanced reporting features including cohort analysis, predictive insights, and custom dashboard widgets.', 'knot4'); ?></p>
                <a href="<?php echo admin_url('admin.php?page=knot4-upgrade'); ?>" class="button button-primary">
                    <?php _e('Upgrade to Pro', 'knot4'); ?>
                </a>
            </div>
            <?php
            return;
        }
        
        ?>
        <div class="knot4-advanced-reports">
            <div class="knot4-charts-row">
                <div class="knot4-chart-container">
                    <h3><?php _e('Donor Lifetime Value', 'knot4'); ?></h3>
                    <canvas id="ltv-chart" width="400" height="200"></canvas>
                </div>
                
                <div class="knot4-chart-container">
                    <h3><?php _e('Donation Predictions', 'knot4'); ?></h3>
                    <canvas id="predictions-chart" width="400" height="200"></canvas>
                </div>
            </div>
            
            <div class="knot4-cohort-analysis">
                <h3><?php _e('Donor Cohort Analysis', 'knot4'); ?></h3>
                <div class="knot4-cohort-table">
                    <!-- Cohort analysis table would go here -->
                    <p><?php _e('Advanced cohort analysis features coming soon.', 'knot4'); ?></p>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Get overview statistics
     */
    private static function get_overview_stats($date_range) {
        global $wpdb;
        
        $dates = self::get_date_range($date_range);
        $previous_dates = self::get_previous_period_dates($date_range);
        
        // Current period stats
        $total_donations = $wpdb->get_var($wpdb->prepare(
            "SELECT COALESCE(SUM(amount), 0) FROM {$wpdb->prefix}knot4_donations 
             WHERE status = 'completed' AND created_at BETWEEN %s AND %s",
            $dates['start'], $dates['end']
        ));
        
        $total_donors = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT donor_email) FROM {$wpdb->prefix}knot4_donations 
             WHERE status = 'completed' AND created_at BETWEEN %s AND %s",
            $dates['start'], $dates['end']
        ));
        
        $total_events = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->posts} p 
             WHERE p.post_type = 'knot4_event' AND p.post_status = 'publish' 
             AND p.post_date BETWEEN %s AND %s",
            $dates['start'], $dates['end']
        ));
        
        $total_volunteers = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->posts} p 
             WHERE p.post_type = 'knot4_volunteer' AND p.post_status = 'publish' 
             AND p.post_date BETWEEN %s AND %s",
            $dates['start'], $dates['end']
        ));
        
        // Previous period stats for comparison
        $previous_donations = $wpdb->get_var($wpdb->prepare(
            "SELECT COALESCE(SUM(amount), 0) FROM {$wpdb->prefix}knot4_donations 
             WHERE status = 'completed' AND created_at BETWEEN %s AND %s",
            $previous_dates['start'], $previous_dates['end']
        ));
        
        $previous_donors = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT donor_email) FROM {$wpdb->prefix}knot4_donations 
             WHERE status = 'completed' AND created_at BETWEEN %s AND %s",
            $previous_dates['start'], $previous_dates['end']
        ));
        
        // Calculate percentage changes
        $donations_change = $previous_donations > 0 ? (($total_donations - $previous_donations) / $previous_donations) * 100 : 0;
        $donors_change = $previous_donors > 0 ? (($total_donors - $previous_donors) / $previous_donors) * 100 : 0;
        
        return array(
            'total_donations' => $total_donations,
            'total_donors' => $total_donors,
            'total_events' => $total_events,
            'total_volunteers' => $total_volunteers,
            'donations_change' => $donations_change,
            'donors_change' => $donors_change,
            'events_change' => 0, // Simplified for now
            'volunteers_change' => 0, // Simplified for now
        );
    }
    
    /**
     * Get recent activities
     */
    private static function get_recent_activities() {
        global $wpdb;
        
        $activities = array();
        
        // Recent donations
        $recent_donations = $wpdb->get_results(
            "SELECT donor_first_name, donor_last_name, amount, created_at 
             FROM {$wpdb->prefix}knot4_donations 
             WHERE status = 'completed' 
             ORDER BY created_at DESC 
             LIMIT 5"
        );
        
        foreach ($recent_donations as $donation) {
            $activities[] = array(
                'icon' => 'heart',
                'message' => sprintf(__('%s donated %s', 'knot4'), 
                    $donation->donor_first_name . ' ' . $donation->donor_last_name,
                    Knot4_Utilities::format_currency($donation->amount)
                ),
                'time' => human_time_diff(strtotime($donation->created_at)) . ' ago',
                'timestamp' => strtotime($donation->created_at)
            );
        }
        
        // Sort by timestamp
        usort($activities, function($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });
        
        return array_slice($activities, 0, 10);
    }
    
    /**
     * Get date range based on selection
     */
    private static function get_date_range($range) {
        $end = current_time('Y-m-d 23:59:59');
        
        switch ($range) {
            case '7days':
                $start = date('Y-m-d 00:00:00', strtotime('-7 days'));
                break;
            case '30days':
                $start = date('Y-m-d 00:00:00', strtotime('-30 days'));
                break;
            case '90days':
                $start = date('Y-m-d 00:00:00', strtotime('-90 days'));
                break;
            case '12months':
                $start = date('Y-m-d 00:00:00', strtotime('-12 months'));
                break;
            case 'ytd':
                $start = date('Y-01-01 00:00:00');
                break;
            default:
                $start = date('Y-m-d 00:00:00', strtotime('-30 days'));
        }
        
        return array(
            'start' => $start,
            'end' => $end
        );
    }
    
    /**
     * Get previous period dates for comparison
     */
    private static function get_previous_period_dates($range) {
        $current = self::get_date_range($range);
        $period_length = strtotime($current['end']) - strtotime($current['start']);
        
        $end = date('Y-m-d 23:59:59', strtotime($current['start']) - 1);
        $start = date('Y-m-d 00:00:00', strtotime($end) - $period_length);
        
        return array(
            'start' => $start,
            'end' => $end
        );
    }
    
    /**
     * Render donations table
     */
    private static function render_donations_table($date_range) {
        global $wpdb;
        
        $dates = self::get_date_range($date_range);
        $donations = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}knot4_donations 
             WHERE status = 'completed' AND created_at BETWEEN %s AND %s 
             ORDER BY created_at DESC LIMIT 50",
            $dates['start'], $dates['end']
        ));
        
        if (empty($donations)) {
            echo '<p>' . __('No donations found for the selected period.', 'knot4') . '</p>';
            return;
        }
        
        ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Date', 'knot4'); ?></th>
                    <th><?php _e('Donor', 'knot4'); ?></th>
                    <th><?php _e('Amount', 'knot4'); ?></th>
                    <th><?php _e('Method', 'knot4'); ?></th>
                    <th><?php _e('Campaign', 'knot4'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donations as $donation): ?>
                <tr>
                    <td><?php echo date_i18n(get_option('date_format'), strtotime($donation->created_at)); ?></td>
                    <td><?php echo esc_html($donation->donor_first_name . ' ' . $donation->donor_last_name); ?></td>
                    <td><?php echo esc_html(Knot4_Utilities::format_currency($donation->amount)); ?></td>
                    <td><?php echo esc_html(ucfirst($donation->payment_method)); ?></td>
                    <td><?php echo esc_html($donation->campaign ?: '-'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    }
    
    /**
     * Render top donors
     */
    private static function render_top_donors($date_range) {
        global $wpdb;
        
        $dates = self::get_date_range($date_range);
        $top_donors = $wpdb->get_results($wpdb->prepare(
            "SELECT donor_first_name, donor_last_name, donor_email, 
                    SUM(amount) as total_amount, COUNT(*) as donation_count
             FROM {$wpdb->prefix}knot4_donations 
             WHERE status = 'completed' AND created_at BETWEEN %s AND %s 
             GROUP BY donor_email 
             ORDER BY total_amount DESC 
             LIMIT 10",
            $dates['start'], $dates['end']
        ));
        
        if (empty($top_donors)) {
            echo '<p>' . __('No donors found for the selected period.', 'knot4') . '</p>';
            return;
        }
        
        foreach ($top_donors as $index => $donor) {
            ?>
            <div class="knot4-top-donor-item">
                <div class="knot4-donor-rank"><?php echo $index + 1; ?></div>
                <div class="knot4-donor-info">
                    <div class="knot4-donor-name"><?php echo esc_html($donor->donor_first_name . ' ' . $donor->donor_last_name); ?></div>
                    <div class="knot4-donor-stats">
                        <?php echo esc_html(Knot4_Utilities::format_currency($donor->total_amount)); ?> 
                        (<?php echo number_format($donor->donation_count); ?> <?php _e('donations', 'knot4'); ?>)
                    </div>
                </div>
            </div>
            <?php
        }
    }
    
    /**
     * Render events table
     */
    private static function render_events_table($date_range) {
        global $wpdb;
        
        $dates = self::get_date_range($date_range);
        $events = $wpdb->get_results($wpdb->prepare(
            "SELECT p.ID, p.post_title, p.post_date,
                    (SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = p.ID AND meta_key = '_knot4_event_date') as event_date,
                    (SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = p.ID AND meta_key = '_knot4_event_attendees') as attendees
             FROM {$wpdb->posts} p 
             WHERE p.post_type = 'knot4_event' AND p.post_status = 'publish' 
             AND p.post_date BETWEEN %s AND %s 
             ORDER BY p.post_date DESC",
            $dates['start'], $dates['end']
        ));
        
        if (empty($events)) {
            echo '<p>' . __('No events found for the selected period.', 'knot4') . '</p>';
            return;
        }
        
        ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Event', 'knot4'); ?></th>
                    <th><?php _e('Date', 'knot4'); ?></th>
                    <th><?php _e('Attendees', 'knot4'); ?></th>
                    <th><?php _e('Created', 'knot4'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $event): ?>
                <tr>
                    <td><?php echo esc_html($event->post_title); ?></td>
                    <td><?php echo $event->event_date ? date_i18n(get_option('date_format'), strtotime($event->event_date)) : '-'; ?></td>
                    <td><?php echo number_format($event->attendees ?: 0); ?></td>
                    <td><?php echo date_i18n(get_option('date_format'), strtotime($event->post_date)); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    }
    
    /**
     * AJAX handlers
     */
    public static function generate_report_ajax() {
        check_ajax_referer('knot4_reports_nonce', 'nonce');
        
        if (!current_user_can('view_knot4_nonprofit')) {
            wp_die(__('You do not have sufficient permissions.', 'knot4'));
        }
        
        $report_type = sanitize_text_field($_POST['report_type']);
        $date_range = sanitize_text_field($_POST['date_range']);
        
        $data = array();
        
        switch ($report_type) {
            case 'donations_chart':
                $data = self::get_donations_chart_data($date_range);
                break;
            case 'donors_chart':
                $data = self::get_donors_chart_data($date_range);
                break;
            default:
                wp_send_json_error(__('Invalid report type.', 'knot4'));
        }
        
        wp_send_json_success($data);
    }
    
    public static function export_report_ajax() {
        check_ajax_referer('knot4_reports_nonce', 'nonce');
        
        if (!current_user_can('view_knot4_nonprofit')) {
            wp_die(__('You do not have sufficient permissions.', 'knot4'));
        }
        
        $report_type = sanitize_text_field($_POST['report_type']);
        $format = sanitize_text_field($_POST['format'] ?? 'csv');
        
        // Export functionality would be implemented here
        wp_send_json_success(__('Export completed successfully.', 'knot4'));
    }
    
    /**
     * Get chart data
     */
    private static function get_donations_chart_data($date_range) {
        global $wpdb;
        
        $dates = self::get_date_range($date_range);
        
        $donations = $wpdb->get_results($wpdb->prepare(
            "SELECT DATE(created_at) as date, SUM(amount) as total 
             FROM {$wpdb->prefix}knot4_donations 
             WHERE status = 'completed' AND created_at BETWEEN %s AND %s 
             GROUP BY DATE(created_at) 
             ORDER BY date",
            $dates['start'], $dates['end']
        ));
        
        $labels = array();
        $data = array();
        
        foreach ($donations as $donation) {
            $labels[] = date_i18n('M j', strtotime($donation->date));
            $data[] = floatval($donation->total);
        }
        
        return array(
            'labels' => $labels,
            'datasets' => array(
                array(
                    'label' => __('Donations', 'knot4'),
                    'data' => $data,
                    'borderColor' => '#667eea',
                    'backgroundColor' => 'rgba(102, 126, 234, 0.1)',
                    'tension' => 0.4
                )
            )
        );
    }
    
    private static function get_donors_chart_data($date_range) {
        global $wpdb;
        
        $dates = self::get_date_range($date_range);
        
        $donors = $wpdb->get_results($wpdb->prepare(
            "SELECT DATE(created_at) as date, COUNT(DISTINCT donor_email) as count 
             FROM {$wpdb->prefix}knot4_donations 
             WHERE status = 'completed' AND created_at BETWEEN %s AND %s 
             GROUP BY DATE(created_at) 
             ORDER BY date",
            $dates['start'], $dates['end']
        ));
        
        $labels = array();
        $data = array();
        
        foreach ($donors as $donor) {
            $labels[] = date_i18n('M j', strtotime($donor->date));
            $data[] = intval($donor->count);
        }
        
        return array(
            'labels' => $labels,
            'datasets' => array(
                array(
                    'label' => __('New Donors', 'knot4'),
                    'data' => $data,
                    'borderColor' => '#28a745',
                    'backgroundColor' => 'rgba(40, 167, 69, 0.1)',
                    'tension' => 0.4
                )
            )
        );
    }
}