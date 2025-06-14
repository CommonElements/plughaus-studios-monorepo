<?php
/**
 * Plugin Name: Vireo Property Management
 * Plugin URI: https://vireodesigns.com/plugins/property-management
 * Description: Professional property management solution for WordPress
 * Version: 1.0.0
 * Author: Vireo Designs
 * Author URI: https://vireodesigns.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: vireo-property
 * Domain Path: /languages
 *
 * @package Vireo_Property_Management
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('PHPM_VERSION', '1.0.0');
define('PHPM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PHPM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('PHPM_PLUGIN_BASE', plugin_basename(__FILE__));
define('PHPM_CORE_DIR', PHPM_PLUGIN_DIR . 'core/');
define('PHPM_PRO_DIR', PHPM_PLUGIN_DIR . 'pro/');

/**
 * Main Vireo Property Management Class
 *
 * @since 1.0.0
 */
class Vireo_Property_Management {
    
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
     * Initialize the plugin
     */
    public function __construct() {
        $this->define_constants();
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->define_api_hooks();
        
        // Check pro license status
        $this->is_pro = $this->check_pro_license();
        
        // Load pro features if licensed
        if ($this->is_pro) {
            $this->load_pro_features();
        }
    }
    
    /**
     * Check if pro license is valid
     */
    private function check_pro_license() {
        // For now, return false (free version only)
        // This will be replaced with actual license checking
        return false;
    }
    
    /**
     * Load required dependencies
     */
    private function load_dependencies() {
        // Shared utilities (always loaded first)
        require_once PHPM_CORE_DIR . 'includes/shared/class-phpm-utilities.php';
        
        // Core functionality (always loaded)
        require_once PHPM_CORE_DIR . 'includes/core/class-phpm-capabilities.php';
        require_once PHPM_CORE_DIR . 'includes/core/class-phpm-post-types.php';
        require_once PHPM_CORE_DIR . 'includes/core/class-phpm-taxonomies.php';
        
        // Admin functionality
        require_once PHPM_CORE_DIR . 'includes/admin/class-phpm-admin.php';
        require_once PHPM_CORE_DIR . 'includes/admin/class-phpm-admin-menus.php';
        require_once PHPM_CORE_DIR . 'includes/admin/class-phpm-admin-settings.php';
        
        // Public functionality
        require_once PHPM_CORE_DIR . 'includes/public/class-phpm-public.php';
        require_once PHPM_CORE_DIR . 'includes/public/class-phpm-shortcodes.php';
        
        // API functionality
        require_once PHPM_CORE_DIR . 'includes/api/class-phpm-rest-api.php';
    }
    
    /**
     * Load pro features (only if licensed)
     */
    private function load_pro_features() {
        if (!file_exists(PHPM_PRO_DIR)) {
            return;
        }
        
        // Pro licensing
        require_once PHPM_PRO_DIR . 'includes/licensing/class-phpm-license-manager.php';
        
        // Pro features
        require_once PHPM_PRO_DIR . 'includes/advanced-features/class-phpm-advanced-dashboard.php';
        require_once PHPM_PRO_DIR . 'includes/advanced-features/class-phpm-advanced-reporting.php';
        
        // Pro integrations
        require_once PHPM_PRO_DIR . 'includes/integrations/class-phpm-payment-automation.php';
        require_once PHPM_PRO_DIR . 'includes/integrations/class-phpm-email-automation.php';
    }
    
    /**
     * Load the plugin text domain for translation.
     */
    public function set_locale() {
        load_plugin_textdomain(
            'vireo-property',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
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
function phpm_activate() {
    // Create database tables if needed
    require_once PHPM_CORE_DIR . 'includes/class-phpm-activator.php';
    PHPM_Activator::activate();
}
register_activation_hook(__FILE__, 'phpm_activate');

/**
 * Plugin deactivation hook
 */
function phpm_deactivate() {
    require_once PHPM_CORE_DIR . 'includes/class-phpm-deactivator.php';
    PHPM_Deactivator::deactivate();
}
register_deactivation_hook(__FILE__, 'phpm_deactivate');

/**
 * Begins execution of the plugin.
 */
function run_vireo_property_management() {
    $plugin = Vireo_Property_Management::get_instance();
    $plugin->run();
}
run_vireo_property_management();