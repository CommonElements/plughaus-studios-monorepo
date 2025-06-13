<?php
/**
 * Plugin Name: Vireo Property Management
 * Plugin URI: https://vireodesigns.com/plugins/property-management
 * Description: A lightweight, powerful property management solution for WordPress. Manage properties, tenants, leases, and maintenance with ease.
 * Version: 1.0.0
 * Author: Vireo Designs
 * Author URI: https://vireodesigns.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: vireo-property
 * Domain Path: /core/languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('VPM_VERSION', '1.0.0');
define('VPM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('VPM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('VPM_PLUGIN_BASE', plugin_basename(__FILE__));
define('VPM_CORE_DIR', VPM_PLUGIN_DIR . 'core/');

// Legacy compatibility constants (for existing includes)
define('PHPM_VERSION', VPM_VERSION);
define('PHPM_PLUGIN_DIR', VPM_PLUGIN_DIR);
define('PHPM_PLUGIN_URL', VPM_PLUGIN_URL);
define('PHPM_PLUGIN_BASE', VPM_PLUGIN_BASE);
define('PHPM_CORE_DIR', VPM_CORE_DIR);

/**
 * Main Vireo Property Management Class (FREE VERSION)
 */
class Vireo_Property_Management {
    
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
    }
    
    private function load_dependencies() {
        // Shared utilities
        require_once VPM_CORE_DIR . 'includes/shared/class-phpm-utilities.php';
        
        // Core functionality
        require_once VPM_CORE_DIR . 'includes/core/class-phpm-capabilities.php';
        require_once VPM_CORE_DIR . 'includes/core/class-phpm-post-types.php';
        require_once VPM_CORE_DIR . 'includes/core/class-phpm-taxonomies.php';
        require_once VPM_CORE_DIR . 'includes/core/class-phpm-data-validation.php';
        
        // Admin functionality
        require_once VPM_CORE_DIR . 'includes/admin/class-phpm-admin.php';
        require_once VPM_CORE_DIR . 'includes/admin/class-phpm-admin-menus.php';
        require_once VPM_CORE_DIR . 'includes/admin/class-phpm-admin-settings.php';
        require_once VPM_CORE_DIR . 'includes/admin/class-phpm-meta-boxes.php';
        require_once VPM_CORE_DIR . 'includes/admin/class-phpm-admin-list-tables.php';
        
        // Public functionality
        require_once VPM_CORE_DIR . 'includes/public/class-phpm-public.php';
        require_once VPM_CORE_DIR . 'includes/public/class-phpm-shortcodes.php';
        
        // API functionality
        if (file_exists(VPM_CORE_DIR . 'includes/api/class-phpm-rest-api.php')) {
            require_once VPM_CORE_DIR . 'includes/api/class-phpm-rest-api.php';
        }
        
        // Frontend settings
        require_once VPM_CORE_DIR . 'includes/admin/class-phpm-frontend-settings-admin.php';
    }
    
    private function set_locale() {
        add_action('plugins_loaded', function() {
            load_plugin_textdomain(
                'vireo-property',
                false,
                dirname(VPM_PLUGIN_BASE) . '/core/languages/'
            );
        });
    }
    
    private function define_admin_hooks() {
        PHPM_Post_Types::init();
        PHPM_Capabilities::init();
        
        $admin = new PHPM_Admin();
        add_action('admin_enqueue_scripts', array($admin, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($admin, 'enqueue_scripts'));
        add_action('admin_menu', array($admin, 'add_admin_menus'));
        
        new PHPM_Admin_Settings();
        
        if (class_exists('PHPM_Meta_Boxes')) {
            add_action('admin_init', array('PHPM_Meta_Boxes', 'init'));
        }
        
        if (class_exists('PHPM_Frontend_Settings_Admin')) {
            PHPM_Frontend_Settings_Admin::init();
        }
    }
    
    private function define_public_hooks() {
        $public = new PHPM_Public();
        add_action('wp_enqueue_scripts', array($public, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array($public, 'enqueue_scripts'));
        
        PHPM_Shortcodes::init();
    }
    
    private function define_api_hooks() {
        if (class_exists('PHPM_REST_API')) {
            add_action('rest_api_init', array('PHPM_REST_API', 'register_routes'));
        }
    }
    
    public function run() {
        // Plugin ready
    }
}

/**
 * Plugin activation
 */
function vpm_activate() {
    require_once VPM_CORE_DIR . 'includes/class-phpm-activator.php';
    PHPM_Activator::activate();
}
register_activation_hook(__FILE__, 'vpm_activate');

/**
 * Plugin deactivation
 */
function vpm_deactivate() {
    require_once VPM_CORE_DIR . 'includes/class-phpm-deactivator.php';
    PHPM_Deactivator::deactivate();
}
register_deactivation_hook(__FILE__, 'vpm_deactivate');

/**
 * Initialize plugin
 */
function run_vireo_property_management() {
    $plugin = Vireo_Property_Management::get_instance();
    $plugin->run();
}
run_vireo_property_management();