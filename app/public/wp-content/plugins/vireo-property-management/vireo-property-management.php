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
 * Domain Path: /languages
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
define('VPM_PRO_DIR', VPM_PLUGIN_DIR . 'pro/');

// Legacy compatibility constants (for existing includes)
define('PHPM_VERSION', VPM_VERSION);
define('PHPM_PLUGIN_DIR', VPM_PLUGIN_DIR);
define('PHPM_PLUGIN_URL', VPM_PLUGIN_URL);
define('PHPM_PLUGIN_BASE', VPM_PLUGIN_BASE);
define('PHPM_CORE_DIR', VPM_CORE_DIR);
define('PHPM_PRO_DIR', VPM_PRO_DIR);

/**
 * Main Vireo Property Management Class
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
        require_once VPM_CORE_DIR . 'includes/shared/class-phpm-utilities.php';
        
        // Core functionality (always loaded)
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
        
        // Basic API functionality (free features only)
        if (file_exists(VPM_CORE_DIR . 'includes/api/class-phpm-rest-api.php')) {
            require_once VPM_CORE_DIR . 'includes/api/class-phpm-rest-api.php';
        }
        
        // Frontend page settings (essential for free version)
        require_once VPM_CORE_DIR . 'includes/admin/class-phpm-frontend-settings-admin.php';
    }
    
    /**
     * Load pro features (only if licensed)
     */
    private function load_pro_features() {
        if (!file_exists(VPM_PRO_DIR)) {
            return;
        }
        
        // Pro licensing
        require_once VPM_PRO_DIR . 'includes/licensing/class-phpm-license-manager.php';
        
        // Pro features
        require_once VPM_PRO_DIR . 'includes/advanced-features/class-phpm-advanced-dashboard.php';
        require_once VPM_PRO_DIR . 'includes/advanced-features/class-phpm-advanced-reporting.php';
        
        // Pro integrations
        require_once VPM_PRO_DIR . 'includes/integrations/class-phpm-payment-automation.php';
        require_once VPM_PRO_DIR . 'includes/integrations/class-phpm-email-automation.php';
    }
    
    /**
     * Set plugin locale for translations
     */
    private function set_locale() {
        add_action('plugins_loaded', function() {
            load_plugin_textdomain(
                'vireo-property',
                false,
                dirname(VPM_PLUGIN_BASE) . '/core/languages/'
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
        
        // Initialize basic admin components
        if (class_exists('PHPM_Meta_Boxes')) {
            add_action('admin_init', array('PHPM_Meta_Boxes', 'init'));
        }
        
        // Initialize frontend settings (essential for free version)
        if (class_exists('PHPM_Frontend_Settings_Admin')) {
            PHPM_Frontend_Settings_Admin::init();
        }
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
        // Only load API if class exists
        if (class_exists('PHPM_REST_API')) {
            add_action('rest_api_init', array('PHPM_REST_API', 'register_routes'));
        }
        
        // Pro API endpoints (if licensed)
        if ($this->is_pro && class_exists('PHPM_License_Manager')) {
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
        // Plugin initialization is already done in constructor
        // This method exists for compatibility
    }
}

/**
 * Plugin activation hook
 */
function vpm_activate() {
    // Create database tables if needed
    require_once VPM_CORE_DIR . 'includes/class-vpm-activator.php';
    VPM_Activator::activate();
}
register_activation_hook(__FILE__, 'vpm_activate');

/**
 * Plugin deactivation hook
 */
function vpm_deactivate() {
    require_once VPM_CORE_DIR . 'includes/class-vpm-deactivator.php';
    VPM_Deactivator::deactivate();
}
register_deactivation_hook(__FILE__, 'vpm_deactivate');

/**
 * Initialize and run the plugin
 */
function run_vireo_property_management() {
    $plugin = Vireo_Property_Management::get_instance();
    $plugin->run();
}
run_vireo_property_management();