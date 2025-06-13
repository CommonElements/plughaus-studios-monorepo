<?php
/**
 * Plugin Name: EquipRent Pro
 * Plugin URI: https://vireodesigns.com/plugins/equiprent-pro
 * Description: Professional equipment rental management for WordPress. Manage inventory, bookings, customers, and deliveries with advanced analytics and automation.
 * Version: 1.0.0
 * Author: Vireo Designs
 * Author URI: https://vireodesigns.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: equiprent-pro
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('ERP_VERSION', '1.0.0');
define('ERP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ERP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ERP_PLUGIN_BASE', plugin_basename(__FILE__));
define('ERP_CORE_DIR', ERP_PLUGIN_DIR . 'core/');
define('ERP_PRO_DIR', ERP_PLUGIN_DIR . 'pro/');

/**
 * Main EquipRent Pro Class
 */
class EquipRent_Pro {
    
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
        // Check if pro directory exists
        if (!file_exists(ERP_PRO_DIR)) {
            return false;
        }
        
        // Load license manager if not already loaded
        if (!class_exists('ERP_License_Manager')) {
            $license_file = ERP_PRO_DIR . 'includes/licensing/class-erp-license-manager.php';
            if (file_exists($license_file)) {
                require_once $license_file;
            } else {
                return false;
            }
        }
        
        // Check license validity
        return class_exists('ERP_License_Manager') ? ERP_License_Manager::is_valid() : false;
    }
    
    /**
     * Load required dependencies
     */
    private function load_dependencies() {
        // Shared utilities (always loaded first)
        require_once ERP_CORE_DIR . 'includes/shared/class-erp-utilities.php';
        
        // Core functionality (always loaded)
        require_once ERP_CORE_DIR . 'includes/core/class-erp-capabilities.php';
        require_once ERP_CORE_DIR . 'includes/core/class-erp-post-types.php';
        require_once ERP_CORE_DIR . 'includes/core/class-erp-taxonomies.php';
        require_once ERP_CORE_DIR . 'includes/core/class-erp-data-validation.php';
        
        // Data models
        require_once ERP_CORE_DIR . 'includes/core/class-erp-equipment.php';
        require_once ERP_CORE_DIR . 'includes/core/class-erp-customer.php';
        require_once ERP_CORE_DIR . 'includes/core/class-erp-booking.php';
        
        // Admin functionality
        require_once ERP_CORE_DIR . 'includes/admin/class-erp-admin.php';
        require_once ERP_CORE_DIR . 'includes/admin/class-erp-admin-menus.php';
        require_once ERP_CORE_DIR . 'includes/admin/class-erp-admin-settings.php';
        require_once ERP_CORE_DIR . 'includes/admin/class-erp-meta-boxes.php';
        require_once ERP_CORE_DIR . 'includes/admin/class-erp-admin-list-tables.php';
        
        // Public functionality
        require_once ERP_CORE_DIR . 'includes/public/class-erp-public.php';
        require_once ERP_CORE_DIR . 'includes/public/class-erp-shortcodes.php';
        
        // Basic API functionality (free features only)
        if (file_exists(ERP_CORE_DIR . 'includes/api/class-erp-rest-api.php')) {
            require_once ERP_CORE_DIR . 'includes/api/class-erp-rest-api.php';
        }
        
        // Frontend settings
        require_once ERP_CORE_DIR . 'includes/admin/class-erp-frontend-settings-admin.php';
    }
    
    /**
     * Load pro features (only if licensed)
     */
    private function load_pro_features() {
        if (!file_exists(ERP_PRO_DIR)) {
            return;
        }
        
        // Pro licensing
        if (class_exists('ERP_License_Manager')) {
            new ERP_License_Manager();
        }
        
        // Pro features
        if (file_exists(ERP_PRO_DIR . 'includes/advanced-features/class-erp-advanced-dashboard.php')) {
            require_once ERP_PRO_DIR . 'includes/advanced-features/class-erp-advanced-dashboard.php';
        }
        
        if (file_exists(ERP_PRO_DIR . 'includes/advanced-features/class-erp-advanced-analytics.php')) {
            require_once ERP_PRO_DIR . 'includes/advanced-features/class-erp-advanced-analytics.php';
        }
        
        if (file_exists(ERP_PRO_DIR . 'includes/advanced-features/class-erp-route-optimization.php')) {
            require_once ERP_PRO_DIR . 'includes/advanced-features/class-erp-route-optimization.php';
        }
        
        // Pro integrations
        if (file_exists(ERP_PRO_DIR . 'includes/integrations/class-erp-payment-automation.php')) {
            require_once ERP_PRO_DIR . 'includes/integrations/class-erp-payment-automation.php';
        }
        
        if (file_exists(ERP_PRO_DIR . 'includes/integrations/class-erp-mobile-app.php')) {
            require_once ERP_PRO_DIR . 'includes/integrations/class-erp-mobile-app.php';
        }
        
        if (file_exists(ERP_PRO_DIR . 'includes/integrations/class-erp-qr-tracking.php')) {
            require_once ERP_PRO_DIR . 'includes/integrations/class-erp-qr-tracking.php';
        }
    }
    
    /**
     * Set plugin locale for translations
     */
    private function set_locale() {
        add_action('plugins_loaded', function() {
            load_plugin_textdomain(
                'equiprent-pro',
                false,
                dirname(ERP_PLUGIN_BASE) . '/core/languages/'
            );
        });
    }
    
    /**
     * Register admin hooks
     */
    private function define_admin_hooks() {
        // Initialize core functionality first
        ERP_Post_Types::init();
        ERP_Capabilities::init();
        
        $admin = new ERP_Admin();
        
        add_action('admin_enqueue_scripts', array($admin, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($admin, 'enqueue_scripts'));
        add_action('admin_menu', array($admin, 'add_admin_menus'));
        
        // Initialize admin settings
        new ERP_Admin_Settings();
        
        // Initialize basic admin components
        if (class_exists('ERP_Meta_Boxes')) {
            add_action('admin_init', array('ERP_Meta_Boxes', 'init'));
        }
        
        // Initialize frontend settings
        if (class_exists('ERP_Frontend_Settings_Admin')) {
            ERP_Frontend_Settings_Admin::init();
        }
    }
    
    /**
     * Register public hooks
     */
    private function define_public_hooks() {
        $public = new ERP_Public();
        
        add_action('wp_enqueue_scripts', array($public, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array($public, 'enqueue_scripts'));
        
        // Initialize shortcodes
        ERP_Shortcodes::init();
    }
    
    /**
     * Register API hooks
     */
    private function define_api_hooks() {
        // Only load API if class exists
        if (class_exists('ERP_REST_API')) {
            add_action('rest_api_init', array('ERP_REST_API', 'register_routes'));
        }
        
        // Pro API endpoints (if licensed)
        if ($this->is_pro && class_exists('ERP_License_Manager')) {
            add_action('rest_api_init', array('ERP_License_Manager', 'register_routes'));
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
function erp_activate() {
    // Create database tables if needed
    require_once ERP_CORE_DIR . 'includes/class-erp-activator.php';
    ERP_Activator::activate();
}
register_activation_hook(__FILE__, 'erp_activate');

/**
 * Plugin deactivation hook
 */
function erp_deactivate() {
    require_once ERP_CORE_DIR . 'includes/class-erp-deactivator.php';
    ERP_Deactivator::deactivate();
}
register_deactivation_hook(__FILE__, 'erp_deactivate');

/**
 * Initialize and run the plugin
 */
function run_equiprent_pro() {
    $plugin = EquipRent_Pro::get_instance();
    $plugin->run();
}
run_equiprent_pro();