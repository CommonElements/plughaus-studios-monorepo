<?php
/**
 * Admin functionality for Knot4
 * 
 * @package Knot4
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Knot4_Admin {
    
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
        // Add dashboard widgets
        add_action('wp_dashboard_setup', array($this, 'add_dashboard_widgets'));
        
        // Add admin bar menu
        add_action('admin_bar_menu', array($this, 'add_admin_bar_menu'), 100);
        
        // AJAX handlers
        add_action('wp_ajax_knot4_create_page', array('Knot4_Admin_Settings', 'create_page_ajax_handler'));
    }
    
    /**
     * Enqueue admin styles
     */
    public function enqueue_styles($hook) {
        // Only load on Knot4 pages
        if (strpos($hook, 'knot4') === false && 
            !in_array(get_post_type(), array('knot4_donor', 'knot4_event', 'knot4_campaign', 'knot4_form'))) {
            return;
        }
        
        wp_enqueue_style(
            'knot4-admin',
            KNOT4_PLUGIN_URL . 'core/assets/css/admin.css',
            array(),
            KNOT4_VERSION
        );
        
        // Enqueue Chart.js for dashboard charts
        wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '3.9.1', true);
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueue_scripts($hook) {
        // Only load on Knot4 pages
        if (strpos($hook, 'knot4') === false && 
            !in_array(get_post_type(), array('knot4_donor', 'knot4_event', 'knot4_campaign', 'knot4_form'))) {
            return;
        }
        
        wp_enqueue_script(
            'knot4-admin',
            KNOT4_PLUGIN_URL . 'core/assets/js/admin.js',
            array('jquery', 'wp-api'),
            KNOT4_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script('knot4-admin', 'knot4_admin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('knot4_admin_nonce'),
            'api_url' => home_url('/wp-json/knot4/v1/'),
            'currency_symbol' => Knot4_Utilities::format_currency(0),
            'strings' => array(
                'confirm_delete' => __('Are you sure you want to delete this?', 'knot4'),
                'saving' => __('Saving...', 'knot4'),
                'saved' => __('Saved!', 'knot4'),
                'error' => __('An error occurred. Please try again.', 'knot4'),
                'donation_processed' => __('Donation processed successfully!', 'knot4'),
                'invalid_amount' => __('Please enter a valid donation amount.', 'knot4'),
                'invalid_email' => __('Please enter a valid email address.', 'knot4'),
            )
        ));
    }
    
    /**
     * Add admin menus
     */
    public function add_admin_menus() {
        // Main dashboard page
        add_menu_page(
            __('Knot4 Nonprofit Management', 'knot4'),
            __('Knot4', 'knot4'),
            'manage_knot4_nonprofit',
            'knot4-dashboard',
            array($this, 'render_dashboard'),
            'dashicons-heart',
            25
        );
        
        // Dashboard submenu (duplicate to show as first item)
        add_submenu_page(
            'knot4-dashboard',
            __('Dashboard', 'knot4'),
            __('Dashboard', 'knot4'),
            'manage_knot4_nonprofit',
            'knot4-dashboard',
            array($this, 'render_dashboard')
        );
        
        // Donations submenu
        add_submenu_page(
            'knot4-dashboard',
            __('Donations', 'knot4'),
            __('Donations', 'knot4'),
            'view_knot4_nonprofit',
            'knot4-donations',
            array($this, 'render_donations')
        );
        
        // Forms submenu
        add_submenu_page(
            'knot4-dashboard',
            __('Donation Forms', 'knot4'),
            __('Forms', 'knot4'),
            'manage_knot4_nonprofit',
            'knot4-forms',
            array($this, 'render_forms')
        );
        
        // Reports submenu
        add_submenu_page(
            'knot4-dashboard',
            __('Reports', 'knot4'),
            __('Reports', 'knot4'),
            'view_knot4_nonprofit',
            'knot4-reports',
            array($this, 'render_reports')
        );
        
        // Settings submenu
        add_submenu_page(
            'knot4-dashboard',
            __('Settings', 'knot4'),
            __('Settings', 'knot4'),
            'manage_knot4_nonprofit',
            'knot4-settings',
            array($this, 'render_settings')
        );
        
        // Pro features submenu (if not pro)
        if (!Knot4_Utilities::is_pro()) {
            add_submenu_page(
                'knot4-dashboard',
                __('Upgrade to Pro', 'knot4'),
                '<span style="color: #fcb92c;">⭐ ' . __('Upgrade to Pro', 'knot4') . '</span>',
                'view_knot4_nonprofit',
                'knot4-upgrade',
                array($this, 'render_upgrade')
            );
        }
    }
    
    /**
     * Render dashboard page
     */
    public function render_dashboard() {
        global $wpdb;
        
        // Get dashboard statistics
        $stats = $this->get_dashboard_stats();
        
        ?>
        <div class="wrap knot4-dashboard">
            <h1 class="knot4-page-title">
                <span class="dashicons dashicons-heart"></span>
                <?php _e('Nonprofit Dashboard', 'knot4'); ?>
            </h1>
            
            <div class="knot4-dashboard-widgets">
                <!-- Quick Stats -->
                <div class="knot4-stat-cards">
                    <div class="knot4-stat-card donations">
                        <div class="stat-icon">
                            <span class="dashicons dashicons-money-alt"></span>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo Knot4_Utilities::format_currency($stats['total_donations']); ?></h3>
                            <p><?php _e('Total Donations', 'knot4'); ?></p>
                            <span class="stat-period"><?php _e('All time', 'knot4'); ?></span>
                        </div>
                    </div>
                    
                    <div class="knot4-stat-card donors">
                        <div class="stat-icon">
                            <span class="dashicons dashicons-groups"></span>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo number_format($stats['total_donors']); ?></h3>
                            <p><?php _e('Total Donors', 'knot4'); ?></p>
                            <span class="stat-period"><?php printf(__('%d this month', 'knot4'), $stats['new_donors_month']); ?></span>
                        </div>
                    </div>
                    
                    <div class="knot4-stat-card events">
                        <div class="stat-icon">
                            <span class="dashicons dashicons-calendar-alt"></span>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo number_format($stats['upcoming_events']); ?></h3>
                            <p><?php _e('Upcoming Events', 'knot4'); ?></p>
                            <span class="stat-period"><?php _e('Next 30 days', 'knot4'); ?></span>
                        </div>
                    </div>
                    
                    <div class="knot4-stat-card average">
                        <div class="stat-icon">
                            <span class="dashicons dashicons-chart-line"></span>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo Knot4_Utilities::format_currency($stats['average_donation']); ?></h3>
                            <p><?php _e('Average Donation', 'knot4'); ?></p>
                            <span class="stat-period"><?php _e('Last 12 months', 'knot4'); ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Charts Row -->
                <div class="knot4-charts-row">
                    <div class="knot4-chart-widget">
                        <h3><?php _e('Donation Trends', 'knot4'); ?></h3>
                        <canvas id="donationTrendsChart" width="400" height="200"></canvas>
                    </div>
                    
                    <div class="knot4-chart-widget">
                        <h3><?php _e('Donor Growth', 'knot4'); ?></h3>
                        <canvas id="donorGrowthChart" width="400" height="200"></canvas>
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div class="knot4-recent-activity">
                    <h3><?php _e('Recent Activity', 'knot4'); ?></h3>
                    <?php $this->render_recent_activity(); ?>
                </div>
                
                <!-- Quick Actions -->
                <div class="knot4-quick-actions">
                    <h3><?php _e('Quick Actions', 'knot4'); ?></h3>
                    <div class="action-buttons">
                        <a href="<?php echo admin_url('post-new.php?post_type=knot4_donor'); ?>" class="button button-primary">
                            <span class="dashicons dashicons-plus"></span>
                            <?php _e('Add Donor', 'knot4'); ?>
                        </a>
                        <a href="<?php echo admin_url('post-new.php?post_type=knot4_event'); ?>" class="button button-primary">
                            <span class="dashicons dashicons-plus"></span>
                            <?php _e('Create Event', 'knot4'); ?>
                        </a>
                        <a href="<?php echo admin_url('admin.php?page=knot4-forms'); ?>" class="button button-secondary">
                            <span class="dashicons dashicons-feedback"></span>
                            <?php _e('Manage Forms', 'knot4'); ?>
                        </a>
                        <a href="<?php echo admin_url('admin.php?page=knot4-reports'); ?>" class="button button-secondary">
                            <span class="dashicons dashicons-chart-area"></span>
                            <?php _e('View Reports', 'knot4'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            // Initialize dashboard charts
            const donationData = <?php echo json_encode($this->get_donation_trends_data()); ?>;
            const donorData = <?php echo json_encode($this->get_donor_growth_data()); ?>;
            
            // Donation trends chart
            new Chart(document.getElementById('donationTrendsChart'), {
                type: 'line',
                data: donationData,
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
            
            // Donor growth chart
            new Chart(document.getElementById('donorGrowthChart'), {
                type: 'bar',
                data: donorData,
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
        </script>
        <?php
    }
    
    /**
     * Get dashboard statistics
     */
    private function get_dashboard_stats() {
        global $wpdb;
        
        // Total donations
        $total_donations = $wpdb->get_var("
            SELECT SUM(amount) FROM {$wpdb->prefix}knot4_donations 
            WHERE status = 'completed'
        ") ?: 0;
        
        // Total donors
        $total_donors = $wpdb->get_var("
            SELECT COUNT(*) FROM {$wpdb->posts} 
            WHERE post_type = 'knot4_donor' AND post_status = 'publish'
        ") ?: 0;
        
        // New donors this month
        $new_donors_month = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*) FROM {$wpdb->posts} 
            WHERE post_type = 'knot4_donor' 
            AND post_status = 'publish'
            AND post_date >= %s
        ", date('Y-m-01'))) ?: 0;
        
        // Upcoming events
        $upcoming_events = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*) FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
            WHERE p.post_type = 'knot4_event' 
            AND p.post_status = 'publish'
            AND pm.meta_key = '_knot4_event_date'
            AND pm.meta_value >= %s
        ", date('Y-m-d'))) ?: 0;
        
        // Average donation
        $average_donation = $wpdb->get_var("
            SELECT AVG(amount) FROM {$wpdb->prefix}knot4_donations 
            WHERE status = 'completed'
            AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        ") ?: 0;
        
        return array(
            'total_donations' => (float) $total_donations,
            'total_donors' => (int) $total_donors,
            'new_donors_month' => (int) $new_donors_month,
            'upcoming_events' => (int) $upcoming_events,
            'average_donation' => (float) $average_donation,
        );
    }
    
    /**
     * Get donation trends data for chart
     */
    private function get_donation_trends_data() {
        global $wpdb;
        
        $data = array();
        $labels = array();
        
        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-{$i} months"));
            $labels[] = date('M Y', strtotime("-{$i} months"));
            
            $amount = $wpdb->get_var($wpdb->prepare("
                SELECT SUM(amount) FROM {$wpdb->prefix}knot4_donations 
                WHERE status = 'completed'
                AND DATE_FORMAT(created_at, '%%Y-%%m') = %s
            ", $month)) ?: 0;
            
            $data[] = (float) $amount;
        }
        
        return array(
            'labels' => $labels,
            'datasets' => array(
                array(
                    'label' => __('Donations', 'knot4'),
                    'data' => $data,
                    'borderColor' => '#007cba',
                    'backgroundColor' => 'rgba(0, 124, 186, 0.1)',
                    'tension' => 0.4,
                )
            )
        );
    }
    
    /**
     * Get donor growth data for chart
     */
    private function get_donor_growth_data() {
        global $wpdb;
        
        $data = array();
        $labels = array();
        
        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-{$i} months"));
            $labels[] = date('M Y', strtotime("-{$i} months"));
            
            $count = $wpdb->get_var($wpdb->prepare("
                SELECT COUNT(*) FROM {$wpdb->posts} 
                WHERE post_type = 'knot4_donor'
                AND post_status = 'publish'
                AND DATE_FORMAT(post_date, '%%Y-%%m') = %s
            ", $month)) ?: 0;
            
            $data[] = (int) $count;
        }
        
        return array(
            'labels' => $labels,
            'datasets' => array(
                array(
                    'label' => __('New Donors', 'knot4'),
                    'data' => $data,
                    'backgroundColor' => '#28a745',
                )
            )
        );
    }
    
    /**
     * Render recent activity
     */
    private function render_recent_activity() {
        global $wpdb;
        
        $activities = $wpdb->get_results($wpdb->prepare("
            SELECT * FROM {$wpdb->prefix}knot4_activity_log 
            ORDER BY created_at DESC 
            LIMIT %d
        ", 10));
        
        if (empty($activities)) {
            echo '<p>' . __('No recent activity.', 'knot4') . '</p>';
            return;
        }
        
        echo '<ul class="knot4-activity-list">';
        foreach ($activities as $activity) {
            $user = get_user_by('id', $activity->user_id);
            $user_name = $user ? $user->display_name : __('System', 'knot4');
            
            echo '<li>';
            echo '<div class="activity-content">';
            echo '<strong>' . esc_html($activity->message) . '</strong>';
            echo '<span class="activity-meta">';
            echo sprintf(__('by %s on %s', 'knot4'), 
                esc_html($user_name), 
                date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($activity->created_at))
            );
            echo '</span>';
            echo '</div>';
            echo '</li>';
        }
        echo '</ul>';
    }
    
    /**
     * Render donations page
     */
    public function render_donations() {
        ?>
        <div class="wrap">
            <h1><?php _e('Donations', 'knot4'); ?></h1>
            <p><?php _e('Manage and view all donations to your organization.', 'knot4'); ?></p>
            <!-- Donations list table would go here -->
        </div>
        <?php
    }
    
    /**
     * Render forms page
     */
    public function render_forms() {
        ?>
        <div class="wrap">
            <h1><?php _e('Donation Forms', 'knot4'); ?></h1>
            <p><?php _e('Create and manage donation forms for your website.', 'knot4'); ?></p>
            <!-- Forms management interface would go here -->
        </div>
        <?php
    }
    
    /**
     * Render reports page
     */
    public function render_reports() {
        ?>
        <div class="wrap">
            <h1><?php _e('Reports', 'knot4'); ?></h1>
            <p><?php _e('Generate reports and analyze your nonprofit data.', 'knot4'); ?></p>
            <!-- Reports interface would go here -->
        </div>
        <?php
    }
    
    /**
     * Render settings page
     */
    public function render_settings() {
        // Enqueue settings-specific assets
        wp_enqueue_style(
            'knot4-admin-settings',
            KNOT4_PLUGIN_URL . 'core/assets/css/admin-settings.css',
            array(),
            KNOT4_VERSION
        );
        
        wp_enqueue_script(
            'knot4-admin-settings',
            KNOT4_PLUGIN_URL . 'core/assets/js/admin-settings.js',
            array('jquery'),
            KNOT4_VERSION,
            true
        );
        
        wp_localize_script('knot4-admin-settings', 'knot4AdminSettings', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('knot4_admin_nonce'),
            'strings' => array(
                'creating' => __('Creating...', 'knot4'),
                'pageCreated' => __('Page created successfully!', 'knot4'),
                'createError' => __('Failed to create page.', 'knot4'),
                'ajaxError' => __('An error occurred. Please try again.', 'knot4'),
                'viewPage' => __('View', 'knot4'),
                'recreatePage' => __('Recreate Page', 'knot4'),
                'validationError' => __('Please correct the errors below.', 'knot4'),
                'stripeKeyWarning' => __('Invalid Stripe key format. Please check your key.', 'knot4'),
            )
        ));
        
        ?>
        <div class="wrap knot4-settings-wrap">
            <div class="knot4-settings-header">
                <div class="knot4-logo">K4</div>
                <div>
                    <h1><?php _e('Knot4 Settings', 'knot4'); ?></h1>
                    <p class="subtitle"><?php _e('Configure your nonprofit management settings', 'knot4'); ?></p>
                </div>
            </div>
            
            <form method="post" action="options.php">
                <?php
                settings_fields('knot4_settings_group');
                do_settings_sections('knot4_settings');
                submit_button(__('Save Settings', 'knot4'), 'primary knot4-save-settings');
                ?>
            </form>
        </div>
        <?php
    }
    
    /**
     * Render upgrade page
     */
    public function render_upgrade() {
        ?>
        <div class="wrap knot4-upgrade-page">
            <h1><?php _e('Upgrade to Knot4 Pro', 'knot4'); ?></h1>
            
            <div class="knot4-upgrade-hero">
                <h2><?php _e('Unlock the Full Power of Nonprofit Management', 'knot4'); ?></h2>
                <p><?php _e('Take your nonprofit to the next level with advanced features, unlimited events, and priority support.', 'knot4'); ?></p>
            </div>
            
            <div class="knot4-pro-features">
                <div class="feature-grid">
                    <div class="feature-item">
                        <span class="dashicons dashicons-yes"></span>
                        <h3><?php _e('Unlimited Events', 'knot4'); ?></h3>
                        <p><?php _e('Host as many events as you need with advanced ticketing and registration.', 'knot4'); ?></p>
                    </div>
                    
                    <div class="feature-item">
                        <span class="dashicons dashicons-yes"></span>
                        <h3><?php _e('Advanced CRM', 'knot4'); ?></h3>
                        <p><?php _e('Custom fields, donor segmentation, and automated communications.', 'knot4'); ?></p>
                    </div>
                    
                    <div class="feature-item">
                        <span class="dashicons dashicons-yes"></span>
                        <h3><?php _e('Campaign Management', 'knot4'); ?></h3>
                        <p><?php _e('Create fundraising campaigns with goals, progress tracking, and team features.', 'knot4'); ?></p>
                    </div>
                    
                    <div class="feature-item">
                        <span class="dashicons dashicons-yes"></span>
                        <h3><?php _e('Member Portal', 'knot4'); ?></h3>
                        <p><?php _e('Self-service donor portal with giving history and profile management.', 'knot4'); ?></p>
                    </div>
                    
                    <div class="feature-item">
                        <span class="dashicons dashicons-yes"></span>
                        <h3><?php _e('Advanced Reports', 'knot4'); ?></h3>
                        <p><?php _e('Detailed analytics, 990-EZ ready reports, and custom dashboards.', 'knot4'); ?></p>
                    </div>
                    
                    <div class="feature-item">
                        <span class="dashicons dashicons-yes"></span>
                        <h3><?php _e('Priority Support', 'knot4'); ?></h3>
                        <p><?php _e('Email and phone support with faster response times.', 'knot4'); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="knot4-upgrade-cta">
                <a href="https://plughausstudios.com/knot4-pro/" target="_blank" class="button button-primary button-hero">
                    <?php _e('Upgrade to Pro Today', 'knot4'); ?>
                </a>
                <p><?php _e('Starting at $199/year - Cancel anytime', 'knot4'); ?></p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Add dashboard widgets
     */
    public function add_dashboard_widgets() {
        wp_add_dashboard_widget(
            'knot4_dashboard_widget',
            __('Nonprofit Overview', 'knot4'),
            array($this, 'render_dashboard_widget')
        );
    }
    
    /**
     * Render dashboard widget
     */
    public function render_dashboard_widget() {
        $stats = $this->get_dashboard_stats();
        
        ?>
        <div class="knot4-dashboard-widget">
            <div class="knot4-widget-stats">
                <div class="stat-item">
                    <span class="dashicons dashicons-money-alt"></span>
                    <div>
                        <strong><?php echo Knot4_Utilities::format_currency($stats['total_donations']); ?></strong>
                        <span><?php _e('Total Donations', 'knot4'); ?></span>
                    </div>
                </div>
                <div class="stat-item">
                    <span class="dashicons dashicons-groups"></span>
                    <div>
                        <strong><?php echo number_format($stats['total_donors']); ?></strong>
                        <span><?php _e('Total Donors', 'knot4'); ?></span>
                    </div>
                </div>
            </div>
            <p><a href="<?php echo admin_url('admin.php?page=knot4-dashboard'); ?>"><?php _e('View Full Dashboard', 'knot4'); ?></a></p>
        </div>
        <?php
    }
    
    /**
     * Add admin bar menu
     */
    public function add_admin_bar_menu($wp_admin_bar) {
        if (!Knot4_Utilities::current_user_can_view_nonprofit()) {
            return;
        }
        
        $wp_admin_bar->add_menu(array(
            'id' => 'knot4-menu',
            'title' => '<span class="ab-icon dashicons dashicons-heart"></span>' . __('Knot4', 'knot4'),
            'href' => admin_url('admin.php?page=knot4-dashboard'),
        ));
        
        $wp_admin_bar->add_menu(array(
            'parent' => 'knot4-menu',
            'id' => 'knot4-dashboard',
            'title' => __('Dashboard', 'knot4'),
            'href' => admin_url('admin.php?page=knot4-dashboard'),
        ));
        
        $wp_admin_bar->add_menu(array(
            'parent' => 'knot4-menu',
            'id' => 'knot4-donations',
            'title' => __('Donations', 'knot4'),
            'href' => admin_url('admin.php?page=knot4-donations'),
        ));
    }
}