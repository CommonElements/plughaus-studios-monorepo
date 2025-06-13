<?php
/**
 * Plugin Name: Marina Manager - Marina & Boat Slip Management
 * Plugin URI: https://vireodesigns.com/plugins/marina-manager
 * Description: Complete marina management system for WordPress. Manage boat slips, reservations, tenants, maintenance, billing, and harbor operations.
 * Version: 1.0.0
 * Author: Vireo Designs
 * Author URI: https://vireodesigns.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: marina-manager
 * Domain Path: /core/languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('MM_VERSION', '1.0.0');
define('MM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('MM_PLUGIN_BASE', plugin_basename(__FILE__));
define('MM_CORE_DIR', MM_PLUGIN_DIR . 'core/');

/**
 * Main Marina Manager Class (FREE VERSION)
 */
class Marina_Manager {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->define_api_hooks();
        $this->init_cron_jobs();
        $this->init_marina_operations();
    }
    
    private function load_dependencies() {
        // Only load files that actually exist to prevent fatal errors
        
        // Shared utilities (only existing files)
        if (file_exists(MM_CORE_DIR . 'includes/shared/class-mm-utilities.php')) {
            require_once MM_CORE_DIR . 'includes/shared/class-mm-utilities.php';
        }
        if (file_exists(MM_CORE_DIR . 'includes/shared/class-mm-billing-engine.php')) {
            require_once MM_CORE_DIR . 'includes/shared/class-mm-billing-engine.php';
        }
        if (file_exists(MM_CORE_DIR . 'includes/shared/class-mm-notification-system.php')) {
            require_once MM_CORE_DIR . 'includes/shared/class-mm-notification-system.php';
        }
        if (file_exists(MM_CORE_DIR . 'includes/shared/class-mm-weather-integration.php')) {
            require_once MM_CORE_DIR . 'includes/shared/class-mm-weather-integration.php';
        }
        
        // Core marina management (only existing files)
        if (file_exists(MM_CORE_DIR . 'includes/core/class-mm-capabilities.php')) {
            require_once MM_CORE_DIR . 'includes/core/class-mm-capabilities.php';
        }
        if (file_exists(MM_CORE_DIR . 'includes/core/class-mm-post-types.php')) {
            require_once MM_CORE_DIR . 'includes/core/class-mm-post-types.php';
        }
        if (file_exists(MM_CORE_DIR . 'includes/core/class-mm-slip-manager.php')) {
            require_once MM_CORE_DIR . 'includes/core/class-mm-slip-manager.php';
        }
        if (file_exists(MM_CORE_DIR . 'includes/core/class-mm-reservation-system.php')) {
            require_once MM_CORE_DIR . 'includes/core/class-mm-reservation-system.php';
        }
        if (file_exists(MM_CORE_DIR . 'includes/core/class-mm-tenant-manager.php')) {
            require_once MM_CORE_DIR . 'includes/core/class-mm-tenant-manager.php';
        }
        if (file_exists(MM_CORE_DIR . 'includes/core/class-mm-maintenance-tracker.php')) {
            require_once MM_CORE_DIR . 'includes/core/class-mm-maintenance-tracker.php';
        }
        if (file_exists(MM_CORE_DIR . 'includes/core/class-mm-harbor-operations.php')) {
            require_once MM_CORE_DIR . 'includes/core/class-mm-harbor-operations.php';
        }
        
        // Admin functionality (only existing files)
        if (file_exists(MM_CORE_DIR . 'includes/admin/class-mm-admin.php')) {
            require_once MM_CORE_DIR . 'includes/admin/class-mm-admin.php';
        }
        if (file_exists(MM_CORE_DIR . 'includes/admin/class-mm-meta-boxes.php')) {
            require_once MM_CORE_DIR . 'includes/admin/class-mm-meta-boxes.php';
        }
        if (file_exists(MM_CORE_DIR . 'includes/admin/class-mm-marina-dashboard.php')) {
            require_once MM_CORE_DIR . 'includes/admin/class-mm-marina-dashboard.php';
        }
        if (file_exists(MM_CORE_DIR . 'includes/admin/class-mm-dock-layout.php')) {
            require_once MM_CORE_DIR . 'includes/admin/class-mm-dock-layout.php';
        }
        if (file_exists(MM_CORE_DIR . 'includes/admin/class-mm-billing-admin.php')) {
            require_once MM_CORE_DIR . 'includes/admin/class-mm-billing-admin.php';
        }
        if (file_exists(MM_CORE_DIR . 'includes/admin/class-mm-reports.php')) {
            require_once MM_CORE_DIR . 'includes/admin/class-mm-reports.php';
        }
        
        // Public functionality (only existing files)
        if (file_exists(MM_CORE_DIR . 'includes/public/class-mm-public.php')) {
            require_once MM_CORE_DIR . 'includes/public/class-mm-public.php';
        }
        if (file_exists(MM_CORE_DIR . 'includes/public/class-mm-reservation-portal.php')) {
            require_once MM_CORE_DIR . 'includes/public/class-mm-reservation-portal.php';
        }
        if (file_exists(MM_CORE_DIR . 'includes/public/class-mm-tenant-portal.php')) {
            require_once MM_CORE_DIR . 'includes/public/class-mm-tenant-portal.php';
        }
        if (file_exists(MM_CORE_DIR . 'includes/public/class-mm-marina-directory.php')) {
            require_once MM_CORE_DIR . 'includes/public/class-mm-marina-directory.php';
        }
        if (file_exists(MM_CORE_DIR . 'includes/public/class-mm-waitlist-system.php')) {
            require_once MM_CORE_DIR . 'includes/public/class-mm-waitlist-system.php';
        }
        
        // API functionality (only existing files)
        if (file_exists(MM_CORE_DIR . 'includes/api/class-mm-rest-api.php')) {
            require_once MM_CORE_DIR . 'includes/api/class-mm-rest-api.php';
        }
        if (file_exists(MM_CORE_DIR . 'includes/api/class-mm-reservation-api.php')) {
            require_once MM_CORE_DIR . 'includes/api/class-mm-reservation-api.php';
        }
        if (file_exists(MM_CORE_DIR . 'includes/api/class-mm-harbor-api.php')) {
            require_once MM_CORE_DIR . 'includes/api/class-mm-harbor-api.php';
        }
        
        // TODO: Add additional files as they are created
    }
    
    private function set_locale() {
        add_action('plugins_loaded', function() {
            load_plugin_textdomain(
                'marina-manager',
                false,
                dirname(MM_PLUGIN_BASE) . '/core/languages/'
            );
        });
    }
    
    private function define_admin_hooks() {
        // Only initialize classes that exist
        if (class_exists('MM_Post_Types')) {
            MM_Post_Types::init();
        }
        
        if (class_exists('MM_Capabilities')) {
            MM_Capabilities::init();
        }
        
        if (class_exists('MM_Admin')) {
            $admin = new MM_Admin();
            add_action('admin_enqueue_scripts', array($admin, 'enqueue_styles'));
            add_action('admin_enqueue_scripts', array($admin, 'enqueue_scripts'));
            add_action('admin_menu', array($admin, 'add_admin_menus'));
        }
        
        // Marina dashboard and management (only if classes exist)
        if (class_exists('MM_Marina_Dashboard')) {
            new MM_Marina_Dashboard();
        }
        if (class_exists('MM_Dock_Layout')) {
            new MM_Dock_Layout();
        }
        if (class_exists('MM_Billing_Admin')) {
            new MM_Billing_Admin();
        }
        
        if (class_exists('MM_Meta_Boxes')) {
            add_action('admin_init', array('MM_Meta_Boxes', 'init'));
        }
        
        // AJAX handlers for admin (only if classes exist)
        if (class_exists('MM_Slip_Manager')) {
            add_action('wp_ajax_mm_update_slip_status', array('MM_Slip_Manager', 'ajax_update_slip_status'));
            add_action('wp_ajax_mm_assign_boat_to_slip', array('MM_Slip_Manager', 'ajax_assign_boat_to_slip'));
        }
        if (class_exists('MM_Reservation_System')) {
            add_action('wp_ajax_mm_process_reservation', array('MM_Reservation_System', 'ajax_process_reservation'));
        }
        if (class_exists('MM_Billing_Engine')) {
            add_action('wp_ajax_mm_generate_invoice', array('MM_Billing_Engine', 'ajax_generate_invoice'));
        }
        if (class_exists('MM_Maintenance_Tracker')) {
            add_action('wp_ajax_mm_update_maintenance', array('MM_Maintenance_Tracker', 'ajax_update_maintenance'));
        }
        if (class_exists('MM_Harbor_Operations')) {
            add_action('wp_ajax_mm_harbor_status_update', array('MM_Harbor_Operations', 'ajax_status_update'));
        }
        
        // TODO: Add more admin hooks when classes are created
    }
    
    private function define_public_hooks() {
        // Only initialize classes that exist
        if (class_exists('MM_Public')) {
            $public = new MM_Public();
            add_action('wp_enqueue_scripts', array($public, 'enqueue_styles'));
            add_action('wp_enqueue_scripts', array($public, 'enqueue_scripts'));
        }
        
        // Public marina interfaces (only if classes exist)
        if (class_exists('MM_Reservation_Portal')) {
            new MM_Reservation_Portal();
        }
        if (class_exists('MM_Tenant_Portal')) {
            new MM_Tenant_Portal();
        }
        if (class_exists('MM_Marina_Directory')) {
            new MM_Marina_Directory();
        }
        if (class_exists('MM_Waitlist_System')) {
            new MM_Waitlist_System();
        }
        
        // Public AJAX handlers (only if classes exist)
        if (class_exists('MM_Reservation_System')) {
            add_action('wp_ajax_mm_submit_reservation', array('MM_Reservation_System', 'ajax_submit_reservation'));
            add_action('wp_ajax_nopriv_mm_submit_reservation', array('MM_Reservation_System', 'ajax_submit_reservation'));
        }
        if (class_exists('MM_Slip_Manager')) {
            add_action('wp_ajax_mm_check_slip_availability', array('MM_Slip_Manager', 'ajax_check_availability'));
            add_action('wp_ajax_nopriv_mm_check_slip_availability', array('MM_Slip_Manager', 'ajax_check_availability'));
        }
        if (class_exists('MM_Waitlist_System')) {
            add_action('wp_ajax_mm_join_waitlist', array('MM_Waitlist_System', 'ajax_join_waitlist'));
            add_action('wp_ajax_nopriv_mm_join_waitlist', array('MM_Waitlist_System', 'ajax_join_waitlist'));
        }
        
        // TODO: Add more public hooks when classes are created
        
        // Shortcodes
        add_shortcode('marina_slip_availability', array('MM_Marina_Directory', 'render_slip_availability'));
        add_shortcode('marina_reservation_form', array('MM_Reservation_Portal', 'render_reservation_form'));
        add_shortcode('marina_tenant_portal', array('MM_Tenant_Portal', 'render_tenant_portal'));
        add_shortcode('marina_directory', array('MM_Marina_Directory', 'render_marina_directory'));
        add_shortcode('marina_waitlist', array('MM_Waitlist_System', 'render_waitlist_form'));
        add_shortcode('marina_weather', array('MM_Weather_Integration', 'render_weather_widget'));
    }
    
    private function define_api_hooks() {
        add_action('rest_api_init', array('MM_REST_API', 'register_routes'));
        add_action('rest_api_init', array('MM_Reservation_API', 'register_routes'));
        add_action('rest_api_init', array('MM_Harbor_API', 'register_routes'));
    }
    
    private function init_cron_jobs() {
        // Schedule billing cycles
        if (!wp_next_scheduled('mm_process_monthly_billing')) {
            wp_schedule_event(time(), 'monthly', 'mm_process_monthly_billing');
        }
        
        // Schedule reservation reminders
        if (!wp_next_scheduled('mm_send_reservation_reminders')) {
            wp_schedule_event(time(), 'daily', 'mm_send_reservation_reminders');
        }
        
        // Schedule maintenance alerts
        if (!wp_next_scheduled('mm_maintenance_alerts')) {
            wp_schedule_event(time(), 'daily', 'mm_maintenance_alerts');
        }
        
        // Schedule weather updates
        if (!wp_next_scheduled('mm_update_weather_data')) {
            wp_schedule_event(time(), 'hourly', 'mm_update_weather_data');
        }
        
        // Schedule lease renewals
        if (!wp_next_scheduled('mm_lease_renewal_notifications')) {
            wp_schedule_event(time(), 'daily', 'mm_lease_renewal_notifications');
        }
        
        add_action('mm_process_monthly_billing', array('MM_Billing_Engine', 'process_monthly_billing'));
        add_action('mm_send_reservation_reminders', array('MM_Notification_System', 'send_reservation_reminders'));
        add_action('mm_maintenance_alerts', array('MM_Maintenance_Tracker', 'send_maintenance_alerts'));
        add_action('mm_update_weather_data', array('MM_Weather_Integration', 'update_weather_data'));
        add_action('mm_lease_renewal_notifications', array('MM_Tenant_Manager', 'send_lease_renewal_notifications'));
    }
    
    private function init_marina_operations() {
        // Only initialize classes that exist
        if (class_exists('MM_Harbor_Operations')) {
            MM_Harbor_Operations::init();
        }
        
        if (class_exists('MM_Weather_Integration')) {
            MM_Weather_Integration::init();
        }
        
        if (class_exists('MM_Billing_Engine')) {
            MM_Billing_Engine::init();
        }
        
        // TODO: Add marina operations initialization when classes are created
    }
    
    public function run() {
        // Plugin ready
    }
}

/**
 * Plugin activation
 */
function mm_activate() {
    $activator_path = MM_CORE_DIR . 'includes/class-mm-activator.php';
    if (file_exists($activator_path)) {
        require_once $activator_path;
        MM_Activator::activate();
    }
    // TODO: Create activator class when needed
}
register_activation_hook(__FILE__, 'mm_activate');

/**
 * Plugin deactivation
 */
function mm_deactivate() {
    $deactivator_path = MM_CORE_DIR . 'includes/class-mm-deactivator.php';
    if (file_exists($deactivator_path)) {
        require_once $deactivator_path;
        MM_Deactivator::deactivate();
    }
    // TODO: Create deactivator class when needed
}
register_deactivation_hook(__FILE__, 'mm_deactivate');

/**
 * Initialize plugin
 */
function run_marina_manager() {
    $plugin = Marina_Manager::get_instance();
    $plugin->run();
}
run_marina_manager();