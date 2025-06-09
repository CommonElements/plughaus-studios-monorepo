<?php
/**
 * Plugin Name: PlugHaus Property Management Pro
 * Plugin URI: https://plughausstudios.com/plugins/property-management-pro/
 * Description: Professional property management features and enhancements for the PlugHaus Property Management plugin. Requires the free version to be installed and activated.
 * Version: 1.0.0
 * Author: PlugHaus Studios
 * Author URI: https://plughausstudios.com
 * License: Commercial
 * License URI: https://plughausstudios.com/license/
 * Text Domain: plughaus-property-pro
 * Domain Path: /languages
 * Requires at least: 5.8
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Network: false
 * Update Server: https://plughausstudios.com/updates/
 *
 * @package PlugHaus_Property_Management_Pro
 * @version 1.0.0
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('PHPM_PRO_VERSION', '1.0.0');
define('PHPM_PRO_PLUGIN_FILE', __FILE__);
define('PHPM_PRO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PHPM_PRO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('PHPM_PRO_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main PlugHaus Property Management Pro Class
 */
class PlugHaus_Property_Management_Pro {
    
    /**
     * @var PlugHaus_Property_Management_Pro
     */
    private static $instance;
    
    /**
     * @var bool
     */
    private $license_valid = false;
    
    /**
     * @var array
     */
    private $pro_features = array(
        'Unlimited Properties',
        'Advanced Analytics Dashboard',
        'Payment Automation',
        'Email Templates & Notifications',
        'Tenant Portal Access',
        'Financial Reporting',
        'Document Management',
        'Maintenance Scheduling',
        'Rent Collection Tracking',
        'Late Fee Management',
        'Lease Templates',
        'Background Check Integration',
        'Accounting Exports',
        'Multi-Property Reports',
        'Custom Fields',
        'API Integrations',
        'Priority Support'
    );
    
    /**
     * Get singleton instance
     */
    public static function instance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init();
    }
    
    /**
     * Initialize the plugin
     */
    private function init() {
        // Check dependencies
        add_action('admin_init', array($this, 'check_dependencies'));
        
        // Only proceed if free version is active
        if (!$this->is_free_version_active()) {
            add_action('admin_notices', array($this, 'free_version_required_notice'));
            return;
        }
        
        // Check license
        $this->license_valid = $this->check_license();
        
        if (!$this->license_valid) {
            add_action('admin_notices', array($this, 'license_required_notice'));
            add_action('admin_menu', array($this, 'add_license_menu'));
            return;
        }
        
        // Load plugin if licensed
        $this->load_plugin();
    }
    
    /**
     * Check if free version is active
     */
    private function is_free_version_active() {
        return class_exists('PlugHaus_Property_Management') || 
               is_plugin_active('plughaus-property-management/plughaus-property-management.php');
    }
    
    /**
     * Check license validity
     */
    private function check_license() {
        $license_key = get_option('phpm_pro_license_key');
        $license_status = get_option('phpm_pro_license_status');
        
        if (empty($license_key)) {
            return false;
        }
        
        // Check cached license status
        $last_check = get_option('phpm_pro_license_last_check');
        $cache_duration = 24 * HOUR_IN_SECONDS; // Check daily
        
        if ($license_status === 'valid' && $last_check && (time() - $last_check) < $cache_duration) {
            return true;
        }
        
        // Validate license with server
        return $this->validate_license_with_server($license_key);
    }
    
    /**
     * Validate license with server
     */
    private function validate_license_with_server($license_key) {
        $response = wp_remote_post('https://plughausstudios.com/wp-json/lmfwc/v2/licenses/validate', array(
            'timeout' => 30,
            'body' => array(
                'license_key' => $license_key,
                'product_id' => 'property-management-pro',
                'domain' => home_url()
            )
        ));
        
        if (is_wp_error($response)) {
            // Network error - maintain current status
            return get_option('phpm_pro_license_status') === 'valid';
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        $is_valid = isset($data['success']) && $data['success'] === true;
        
        // Update license status
        update_option('phpm_pro_license_status', $is_valid ? 'valid' : 'invalid');
        update_option('phpm_pro_license_last_check', time());
        
        if (!$is_valid) {
            update_option('phpm_pro_license_error', $data['message'] ?? 'License validation failed');
        } else {
            delete_option('phpm_pro_license_error');
        }
        
        return $is_valid;
    }
    
    /**
     * Load the plugin
     */
    private function load_plugin() {
        // Load text domain
        add_action('init', array($this, 'load_textdomain'));
        
        // Include files
        $this->include_files();
        
        // Initialize hooks
        $this->init_hooks();
        
        // Register activation/deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    /**
     * Include required files
     */
    private function include_files() {
        // Core classes
        require_once PHPM_PRO_PLUGIN_DIR . 'includes/class-phpm-pro-analytics.php';
        require_once PHPM_PRO_PLUGIN_DIR . 'includes/class-phpm-pro-automation.php';
        require_once PHPM_PRO_PLUGIN_DIR . 'includes/class-phpm-pro-reports.php';
        require_once PHPM_PRO_PLUGIN_DIR . 'includes/class-phpm-pro-tenant-portal.php';
        require_once PHPM_PRO_PLUGIN_DIR . 'includes/class-phpm-pro-admin.php';
        require_once PHPM_PRO_PLUGIN_DIR . 'includes/class-phpm-pro-integrations.php';
        require_once PHPM_PRO_PLUGIN_DIR . 'includes/class-phpm-pro-license.php';
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Admin hooks
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_public_assets'));
        
        // Extend free version functionality
        add_filter('phpm_free_get_pro_features', array($this, 'get_pro_features'));
        add_filter('phpm_free_is_pro_active', '__return_true');
        add_filter('phpm_free_property_limit', array($this, 'remove_property_limit'));
        
        // Add Pro admin menus
        add_action('admin_menu', array('PHPM_Pro_Admin', 'add_pro_menus'), 20);
        
        // Initialize Pro classes
        PHPM_Pro_Analytics::init();
        PHPM_Pro_Automation::init();
        PHPM_Pro_Reports::init();
        PHPM_Pro_Tenant_Portal::init();
        PHPM_Pro_Integrations::init();
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'phpm') === false) {
            return;
        }
        
        wp_enqueue_style(
            'phpm-pro-admin',
            PHPM_PRO_PLUGIN_URL . 'assets/css/admin.css',
            array('phpm-free-admin'),
            PHPM_PRO_VERSION
        );
        
        wp_enqueue_script(
            'phpm-pro-admin',
            PHPM_PRO_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'phpm-free-admin'),
            PHPM_PRO_VERSION,
            true
        );
        
        // Enqueue Chart.js for analytics
        wp_enqueue_script(
            'chart-js',
            'https://cdn.jsdelivr.net/npm/chart.js',
            array(),
            '3.9.1',
            true
        );
        
        wp_localize_script('phpm-pro-admin', 'phpm_pro_admin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('phpm_pro_admin_nonce'),
            'license_status' => $this->license_valid ? 'valid' : 'invalid',
        ));
    }
    
    /**
     * Enqueue public assets
     */
    public function enqueue_public_assets() {
        wp_enqueue_style(
            'phpm-pro-public',
            PHPM_PRO_PLUGIN_URL . 'assets/css/public.css',
            array('phpm-free-public'),
            PHPM_PRO_VERSION
        );
        
        wp_enqueue_script(
            'phpm-pro-public',
            PHPM_PRO_PLUGIN_URL . 'assets/js/public.js',
            array('jquery', 'phpm-free-public'),
            PHPM_PRO_VERSION,
            true
        );
    }
    
    /**
     * Get Pro features list
     */
    public function get_pro_features() {
        return $this->pro_features;
    }
    
    /**
     * Remove property limit for Pro version
     */
    public function remove_property_limit($limit) {
        return -1; // Unlimited
    }
    
    /**
     * Load text domain
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'plughaus-property-pro',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages'
        );
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Check dependencies
        if (!$this->is_free_version_active()) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die(__('PlugHaus Property Management Pro requires the free version to be installed and activated first.', 'plughaus-property-pro'));
        }
        
        // Create Pro-specific database tables
        $this->create_pro_tables();
        
        // Set activation flag
        update_option('phpm_pro_activated', true);
        
        // Clear any caches
        wp_cache_flush();
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Clear scheduled events
        wp_clear_scheduled_hook('phpm_pro_daily_reports');
        wp_clear_scheduled_hook('phpm_pro_payment_reminders');
        
        // Set deactivation flag
        update_option('phpm_pro_activated', false);
    }
    
    /**
     * Create Pro-specific database tables
     */
    private function create_pro_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Analytics table
        $table_name = $wpdb->prefix . 'phpm_pro_analytics';
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            property_id bigint(20) NOT NULL,
            metric_type varchar(50) NOT NULL,
            metric_value decimal(10,2) NOT NULL,
            date_recorded date NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY property_id (property_id),
            KEY metric_type (metric_type),
            KEY date_recorded (date_recorded)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Payment tracking table
        $table_name = $wpdb->prefix . 'phpm_pro_payments';
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            lease_id bigint(20) NOT NULL,
            amount decimal(10,2) NOT NULL,
            payment_date date NOT NULL,
            due_date date NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending',
            payment_method varchar(50),
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY lease_id (lease_id),
            KEY payment_date (payment_date),
            KEY status (status)
        ) $charset_collate;";
        
        dbDelta($sql);
        
        // Update version
        update_option('phpm_pro_db_version', PHPM_PRO_VERSION);
    }
    
    /**
     * Check dependencies
     */
    public function check_dependencies() {
        if (!$this->is_free_version_active()) {
            add_action('admin_notices', array($this, 'free_version_required_notice'));
            deactivate_plugins(plugin_basename(__FILE__));
        }
    }
    
    /**
     * Free version required notice
     */
    public function free_version_required_notice() {
        ?>
        <div class="notice notice-error">
            <p>
                <strong><?php _e('PlugHaus Property Management Pro', 'plughaus-property-pro'); ?></strong> 
                <?php _e('requires the free version of PlugHaus Property Management to be installed and activated.', 'plughaus-property-pro'); ?>
                <a href="<?php echo admin_url('plugin-install.php?s=plughaus+property+management&tab=search&type=term'); ?>">
                    <?php _e('Install Free Version', 'plughaus-property-pro'); ?>
                </a>
            </p>
        </div>
        <?php
    }
    
    /**
     * License required notice
     */
    public function license_required_notice() {
        $error = get_option('phpm_pro_license_error');
        ?>
        <div class="notice notice-warning">
            <p>
                <strong><?php _e('PlugHaus Property Management Pro', 'plughaus-property-pro'); ?></strong> 
                <?php _e('requires a valid license key to function.', 'plughaus-property-pro'); ?>
                <?php if ($error): ?>
                    <br><em><?php echo esc_html($error); ?></em>
                <?php endif; ?>
                <a href="<?php echo admin_url('admin.php?page=phpm-pro-license'); ?>">
                    <?php _e('Enter License Key', 'plughaus-property-pro'); ?>
                </a>
            </p>
        </div>
        <?php
    }
    
    /**
     * Add license menu
     */
    public function add_license_menu() {
        add_menu_page(
            __('Pro License', 'plughaus-property-pro'),
            __('Pro License', 'plughaus-property-pro'),
            'manage_options',
            'phpm-pro-license',
            array('PHPM_Pro_License', 'render_license_page'),
            'dashicons-admin-network',
            99
        );
    }
    
    /**
     * Get license status
     */
    public function is_licensed() {
        return $this->license_valid;
    }
}

/**
 * Initialize the plugin
 */
function phpm_pro() {
    return PlugHaus_Property_Management_Pro::instance();
}

// Start the plugin
phpm_pro();