<?php
/**
 * Plugin Name: PlugHaus Property Management
 * Plugin URI: https://plughausstudios.com/plugins/property-management
 * Description: A lightweight, powerful property management solution for WordPress. Manage properties, tenants, leases, and maintenance with ease.
 * Version: 1.0.0
 * Author: PlugHaus Studios
 * Author URI: https://plughausstudios.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: plughaus-property
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('PHPM_VERSION', '1.0.0');
define('PHPM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PHPM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('PHPM_PLUGIN_BASE', plugin_basename(__FILE__));

/**
 * Main PlugHaus Property Management Class
 */
class PlugHaus_Property_Management {
    
    /**
     * Instance of this class
     */
    private static $instance = null;
    
    /**
     * Get instance of this class
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->define_api_hooks();
    }
    
    /**
     * Load required dependencies
     */
    private function load_dependencies() {
        // Core functionality
        require_once PHPM_PLUGIN_DIR . 'includes/core/class-phpm-post-types.php';
        require_once PHPM_PLUGIN_DIR . 'includes/core/class-phpm-taxonomies.php';
        require_once PHPM_PLUGIN_DIR . 'includes/core/class-phpm-capabilities.php';
        
        // Admin functionality
        require_once PHPM_PLUGIN_DIR . 'includes/admin/class-phpm-admin.php';
        require_once PHPM_PLUGIN_DIR . 'includes/admin/class-phpm-admin-menus.php';
        require_once PHPM_PLUGIN_DIR . 'includes/admin/class-phpm-admin-settings.php';
        
        // Public functionality
        require_once PHPM_PLUGIN_DIR . 'includes/public/class-phpm-public.php';
        require_once PHPM_PLUGIN_DIR . 'includes/public/class-phpm-shortcodes.php';
        
        // API functionality
        require_once PHPM_PLUGIN_DIR . 'includes/api/class-phpm-rest-api.php';
    }
    
    /**
     * Set plugin locale for translations
     */
    private function set_locale() {
        add_action('plugins_loaded', function() {
            load_plugin_textdomain(
                'plughaus-property',
                false,
                dirname(PHPM_PLUGIN_BASE) . '/languages/'
            );
        });
    }
    
    /**
     * Register admin hooks
     */
    private function define_admin_hooks() {
        $admin = new PHPM_Admin();
        
        add_action('admin_enqueue_scripts', array($admin, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($admin, 'enqueue_scripts'));
        add_action('admin_menu', array($admin, 'add_admin_menus'));
    }
    
    /**
     * Register public hooks
     */
    private function define_public_hooks() {
        $public = new PHPM_Public();
        
        add_action('wp_enqueue_scripts', array($public, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array($public, 'enqueue_scripts'));
    }
    
    /**
     * Register API hooks
     */
    private function define_api_hooks() {
        add_action('rest_api_init', array('PHPM_REST_API', 'register_routes'));
    }
    
    /**
     * Run the plugin
     */
    public function run() {
        // Plugin is initialized through hooks
    }
}

/**
 * Plugin activation hook
 */
function phpm_activate() {
    // Create database tables if needed
    require_once PHPM_PLUGIN_DIR . 'includes/class-phpm-activator.php';
    PHPM_Activator::activate();
}
register_activation_hook(__FILE__, 'phpm_activate');

/**
 * Plugin deactivation hook
 */
function phpm_deactivate() {
    require_once PHPM_PLUGIN_DIR . 'includes/class-phpm-deactivator.php';
    PHPM_Deactivator::deactivate();
}
register_deactivation_hook(__FILE__, 'phpm_deactivate');

/**
 * Initialize and run the plugin
 */
function run_plughaus_property_management() {
    $plugin = PlugHaus_Property_Management::get_instance();
    $plugin->run();
}
run_plughaus_property_management();