<?php
/**
 * Plugin Name: Property Management
 * Plugin URI: https://wordpress.org/plugins/property-management/
 * Description: A lightweight, powerful property management solution for WordPress. Manage properties, tenants, leases, and maintenance with ease. Perfect for small property managers and landlords.
 * Version: 1.0.0
 * Author: PlugHaus Studios
 * Author URI: https://plughausstudios.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: property-management
 * Domain Path: /languages
 * Requires at least: 5.8
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Network: false
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('PM_VERSION', '1.0.0');
define('PM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('PM_PLUGIN_BASE', plugin_basename(__FILE__));
define('PM_CORE_DIR', PM_PLUGIN_DIR . 'core/');
define('PM_PRO_DIR', PM_PLUGIN_DIR . 'pro/');

/**
 * Main Property Management Class
 */
class Property_Management {
    
    /**
     * Instance of this class
     */
    private static $instance = null;
    
    /**
     * Whether pro features are available
     */
    private $is_pro = false;
    
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
        // Check pro license status
        $this->is_pro = $this->check_pro_license();
        
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->define_api_hooks();
        
        // Load pro features if licensed
        if ($this->is_pro) {
            $this->load_pro_features();
        }
    }
    
    /**
     * Check if pro license is valid
     * Free version always returns false
     */
    private function check_pro_license() {
        // Free version - no pro features
        return false;
    }
    
    /**
     * Load required dependencies
     */
    private function load_dependencies() {
        // Shared utilities (always loaded first)
        require_once PM_CORE_DIR . 'includes/shared/class-phpm-utilities.php';
        
        // Core functionality (always loaded)
        require_once PM_CORE_DIR . 'includes/core/class-phpm-capabilities.php';
        require_once PM_CORE_DIR . 'includes/core/class-phpm-post-types.php';
        require_once PM_CORE_DIR . 'includes/core/class-phpm-taxonomies.php';
        require_once PM_CORE_DIR . 'includes/core/class-phpm-data-validation.php';
        
        // Admin functionality
        require_once PM_CORE_DIR . 'includes/admin/class-phpm-admin.php';
        require_once PM_CORE_DIR . 'includes/admin/class-phpm-admin-menus.php';
        require_once PM_CORE_DIR . 'includes/admin/class-phpm-admin-settings.php';
        require_once PM_CORE_DIR . 'includes/admin/class-phpm-meta-boxes.php';
        require_once PM_CORE_DIR . 'includes/admin/class-phpm-admin-list-tables.php';
        
        // Public functionality
        require_once PM_CORE_DIR . 'includes/public/class-phpm-public.php';
        require_once PM_CORE_DIR . 'includes/public/class-phpm-shortcodes.php';
        
        // API functionality
        require_once PM_CORE_DIR . 'includes/api/class-phpm-rest-api.php';
        
        // Sample data functionality
        require_once PM_CORE_DIR . 'includes/class-phpm-sample-data.php';
        require_once PM_CORE_DIR . 'includes/admin/class-phpm-sample-data-admin.php';
        
        // Import/Export functionality
        require_once PM_CORE_DIR . 'includes/class-phpm-import-export.php';
        require_once PM_CORE_DIR . 'includes/admin/class-phpm-import-export-admin.php';
        
        // Email notification system
        require_once PM_CORE_DIR . 'includes/class-phpm-email-notifications.php';
        require_once PM_CORE_DIR . 'includes/admin/class-phpm-email-settings-admin.php';
        
        // Frontend page settings
        require_once PM_CORE_DIR . 'includes/admin/class-phpm-frontend-settings-admin.php';
    }
    
    /**
     * Load pro features (only if licensed)
     */
    private function load_pro_features() {
        if (!file_exists(PM_PRO_DIR)) {
            return;
        }
        
        // Pro licensing
        require_once PM_PRO_DIR . 'includes/licensing/class-phpm-license-manager.php';
        
        // Pro features
        require_once PM_PRO_DIR . 'includes/advanced-features/class-phpm-advanced-dashboard.php';
        require_once PM_PRO_DIR . 'includes/advanced-features/class-phpm-advanced-reporting.php';
        
        // Pro integrations
        require_once PM_PRO_DIR . 'includes/integrations/class-phpm-payment-automation.php';
        require_once PM_PRO_DIR . 'includes/integrations/class-phpm-email-automation.php';
    }
    
    /**
     * Set plugin locale for translations
     */
    private function set_locale() {
        add_action('plugins_loaded', function() {
            load_plugin_textdomain(
                'property-management',
                false,
                dirname(PM_PLUGIN_BASE) . '/languages/'
            );
        });
    }
    
    /**
     * Register admin hooks
     */
    private function define_admin_hooks() {
        // Initialize core functionality first
        PHPM_Post_Types::init();
        PHPM_Capabilities::init();
        
        $admin = new PHPM_Admin();
        
        add_action('admin_enqueue_scripts', array($admin, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($admin, 'enqueue_scripts'));
        add_action('admin_menu', array($admin, 'add_admin_menus'));
        
        // Initialize admin settings
        new PHPM_Admin_Settings();
        
        // Initialize admin components with static init methods
        PHPM_Frontend_Settings_Admin::init();
        PHPM_Sample_Data_Admin::init();
        PHPM_Admin_List_Tables::init();
        PHPM_Email_Settings_Admin::init();
        PHPM_Import_Export_Admin::init();
        PHPM_Meta_Boxes::init();
    }
    
    /**
     * Register public hooks
     */
    private function define_public_hooks() {
        $public = new PHPM_Public();
        
        add_action('wp_enqueue_scripts', array($public, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array($public, 'enqueue_scripts'));
        
        // Initialize shortcodes
        PHPM_Shortcodes::init();
    }
    
    /**
     * Register API hooks
     */
    private function define_api_hooks() {
        add_action('rest_api_init', array('PHPM_REST_API', 'register_routes'));
        
        // Pro API endpoints (if licensed)
        if ($this->is_pro) {
            add_action('rest_api_init', array('PHPM_License_Manager', 'register_routes'));
        }
    }
    
    /**
     * Check if pro features are available
     */
    public function is_pro() {
        return $this->is_pro;
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
function pm_activate() {
    // Create database tables if needed
    require_once PM_CORE_DIR . 'includes/class-phpm-activator.php';
    PHPM_Activator::activate();
}
register_activation_hook(__FILE__, 'pm_activate');

/**
 * Plugin deactivation hook
 */
function pm_deactivate() {
    require_once PM_CORE_DIR . 'includes/class-phpm-deactivator.php';
    PHPM_Deactivator::deactivate();
}
register_deactivation_hook(__FILE__, 'pm_deactivate');

/**
 * Initialize and run the plugin
 */
function run_property_management() {
    $plugin = Property_Management::get_instance();
    $plugin->run();
}
run_property_management();