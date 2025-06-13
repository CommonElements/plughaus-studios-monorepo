<?php
/**
 * Plugin Name: StudioSnap - Photography Studio Management
 * Plugin URI: https://vireodesigns.com/plugins/studiosnap
 * Description: Complete photography studio management system for WordPress. Manage clients, bookings, sessions, galleries, contracts, and payments.
 * Version: 1.0.0
 * Author: Vireo Designs
 * Author URI: https://vireodesigns.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: studiosnap
 * Domain Path: /core/languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('SS_VERSION', '1.0.0');
define('SS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SS_PLUGIN_BASE', plugin_basename(__FILE__));
define('SS_CORE_DIR', SS_PLUGIN_DIR . 'core/');

/**
 * Main StudioSnap Class (FREE VERSION)
 */
class StudioSnap {
    
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
    }
    
    private function load_dependencies() {
        // Only load files that actually exist to prevent fatal errors
        
        // Shared utilities (existing files)
        if (file_exists(SS_CORE_DIR . 'includes/shared/class-ss-utilities.php')) {
            require_once SS_CORE_DIR . 'includes/shared/class-ss-utilities.php';
        }
        
        if (file_exists(SS_CORE_DIR . 'includes/shared/class-ss-post-types.php')) {
            require_once SS_CORE_DIR . 'includes/shared/class-ss-post-types.php';
        }
        
        if (file_exists(SS_CORE_DIR . 'includes/shared/class-ss-capabilities.php')) {
            require_once SS_CORE_DIR . 'includes/shared/class-ss-capabilities.php';
        }
        
        if (file_exists(SS_CORE_DIR . 'includes/shared/class-ss-activator.php')) {
            require_once SS_CORE_DIR . 'includes/shared/class-ss-activator.php';
        }
        
        // Core functionality (only existing files)
        if (file_exists(SS_CORE_DIR . 'includes/core/class-ss-booking-system.php')) {
            require_once SS_CORE_DIR . 'includes/core/class-ss-booking-system.php';
        }
        
        // Public functionality (only existing files)
        if (file_exists(SS_CORE_DIR . 'includes/public/class-ss-booking-form.php')) {
            require_once SS_CORE_DIR . 'includes/public/class-ss-booking-form.php';
        }
        
        if (file_exists(SS_CORE_DIR . 'includes/public/class-ss-client-portal.php')) {
            require_once SS_CORE_DIR . 'includes/public/class-ss-client-portal.php';
        }
        
        // Admin functionality (only existing files)
        if (file_exists(SS_CORE_DIR . 'includes/admin/class-ss-admin.php')) {
            require_once SS_CORE_DIR . 'includes/admin/class-ss-admin.php';
        }
        
        // TODO: Add additional files as they are created
    }
    
    private function set_locale() {
        add_action('plugins_loaded', function() {
            load_plugin_textdomain(
                'studiosnap',
                false,
                dirname(SS_PLUGIN_BASE) . '/core/languages/'
            );
        });
    }
    
    private function define_admin_hooks() {
        // Only initialize classes that exist
        if (class_exists('SS_Post_Types')) {
            SS_Post_Types::init();
        }
        
        if (class_exists('SS_Capabilities')) {
            SS_Capabilities::init();
        }
        
        // Admin interface is automatically initialized by the class itself
        // TODO: Add additional admin hooks when more admin classes are created
    }
    
    private function define_public_hooks() {
        // Only initialize classes that exist
        if (class_exists('SS_Booking_Form')) {
            new SS_Booking_Form();
            add_shortcode('studiosnap_booking_form', array('SS_Booking_Form', 'render_booking_form'));
        }
        
        if (class_exists('SS_Client_Portal')) {
            new SS_Client_Portal();
            add_shortcode('studiosnap_client_portal', array('SS_Client_Portal', 'render_client_portal'));
        }
        
        // AJAX handlers (only if classes exist)
        if (class_exists('SS_Booking_System')) {
            add_action('wp_ajax_ss_submit_booking', array('SS_Booking_System', 'ajax_submit_booking'));
            add_action('wp_ajax_nopriv_ss_submit_booking', array('SS_Booking_System', 'ajax_submit_booking'));
            add_action('wp_ajax_ss_check_availability', array('SS_Booking_System', 'ajax_check_availability'));
            add_action('wp_ajax_nopriv_ss_check_availability', array('SS_Booking_System', 'ajax_check_availability'));
        }
        
        // TODO: Add more public hooks when classes are created
    }
    
    private function define_api_hooks() {
        // Only initialize API classes that exist
        if (class_exists('SS_REST_API')) {
            add_action('rest_api_init', array('SS_REST_API', 'register_routes'));
        }
        
        if (class_exists('SS_Booking_API')) {
            add_action('rest_api_init', array('SS_Booking_API', 'register_routes'));
        }
        
        // TODO: Add API hooks when API classes are created
    }
    
    private function init_cron_jobs() {
        // Only schedule cron jobs if the handler classes exist
        if (class_exists('SS_Email_Handler')) {
            // Schedule booking reminders
            if (!wp_next_scheduled('ss_send_booking_reminders')) {
                wp_schedule_event(time(), 'daily', 'ss_send_booking_reminders');
            }
            
            // Schedule follow-up emails
            if (!wp_next_scheduled('ss_send_followup_emails')) {
                wp_schedule_event(time(), 'daily', 'ss_send_followup_emails');
            }
            
            add_action('ss_send_booking_reminders', array('SS_Email_Handler', 'send_booking_reminders'));
            add_action('ss_send_followup_emails', array('SS_Email_Handler', 'send_followup_emails'));
        }
        
        // TODO: Add cron jobs when email handler is created
    }
    
    public function run() {
        // Plugin ready
    }
}

/**
 * Plugin activation
 */
function ss_activate() {
    $activator_path = SS_CORE_DIR . 'includes/shared/class-ss-activator.php';
    if (file_exists($activator_path)) {
        require_once $activator_path;
        SS_Activator::activate();
    }
}
register_activation_hook(__FILE__, 'ss_activate');

/**
 * Plugin deactivation
 */
function ss_deactivate() {
    $deactivator_path = SS_CORE_DIR . 'includes/class-ss-deactivator.php';
    if (file_exists($deactivator_path)) {
        require_once $deactivator_path;
        SS_Deactivator::deactivate();
    }
    // TODO: Create deactivator class when needed
}
register_deactivation_hook(__FILE__, 'ss_deactivate');

/**
 * Initialize plugin
 */
function run_studiosnap() {
    $plugin = StudioSnap::get_instance();
    $plugin->run();
}
run_studiosnap();