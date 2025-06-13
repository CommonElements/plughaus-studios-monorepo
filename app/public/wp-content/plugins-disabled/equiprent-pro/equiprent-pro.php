<?php
/**
 * Plugin Name: EquipRent Pro - Equipment Rental Management
 * Plugin URI: https://vireodesigns.com/plugins/equiprent-pro
 * Description: Professional equipment rental management system for WordPress. Manage inventory, bookings, customers, and rentals with ease. Perfect for tool rental, party equipment, AV gear, and construction equipment businesses.
 * Version: 1.0.0
 * Author: Vireo Designs
 * Author URI: https://vireodesigns.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: equiprent-pro
 * Domain Path: /core/languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Network: false
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('EQUIPRENT_VERSION', '1.0.0');
define('EQUIPRENT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('EQUIPRENT_PLUGIN_URL', plugin_dir_url(__FILE__));
define('EQUIPRENT_PLUGIN_BASE', plugin_basename(__FILE__));
define('EQUIPRENT_CORE_DIR', EQUIPRENT_PLUGIN_DIR . 'core/');
define('EQUIPRENT_PRO_DIR', EQUIPRENT_PLUGIN_DIR . 'pro/');

/**
 * Main EquipRent Pro Class
 * 
 * Professional equipment rental management for WordPress
 * Core Features: Inventory, Bookings, Customers, Availability Calendar
 * Pro Features: Advanced Analytics, Payment Integration, Delivery Management
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
     * Available modules
     */
    private $modules = array();
    
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
        $this->init_modules();
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
        if (!file_exists(EQUIPRENT_PRO_DIR)) {
            return false;
        }
        
        // Load license manager if not already loaded
        if (!class_exists('ER_License_Manager')) {
            $license_file = EQUIPRENT_PRO_DIR . 'includes/licensing/class-er-license-manager.php';
            if (file_exists($license_file)) {
                require_once $license_file;
            } else {
                return false;
            }
        }
        
        // Check license validity
        return class_exists('ER_License_Manager') ? ER_License_Manager::is_valid() : false;
    }
    
    /**
     * Load required dependencies
     */
    private function load_dependencies() {
        // Core functionality (always loaded)
        $required_files = array(
            EQUIPRENT_CORE_DIR . 'includes/shared/class-er-utilities.php',
            EQUIPRENT_CORE_DIR . 'includes/core/class-er-capabilities.php',
            EQUIPRENT_CORE_DIR . 'includes/core/class-er-post-types.php',
            EQUIPRENT_CORE_DIR . 'includes/core/class-er-taxonomies.php',
            EQUIPRENT_CORE_DIR . 'includes/core/class-er-database.php',
            EQUIPRENT_CORE_DIR . 'includes/core/class-er-data-validation.php',
            EQUIPRENT_CORE_DIR . 'includes/core/class-er-equipment.php',
            EQUIPRENT_CORE_DIR . 'includes/core/class-er-booking.php',
            EQUIPRENT_CORE_DIR . 'includes/core/class-er-customer.php',
        );
        
        foreach ($required_files as $file) {
            if (file_exists($file)) {
                require_once $file;
            }
        }
        
        // Admin functionality
        if (is_admin()) {
            $admin_files = array(
                EQUIPRENT_CORE_DIR . 'includes/admin/class-er-admin.php',
                EQUIPRENT_CORE_DIR . 'includes/admin/class-er-admin-settings.php',
                EQUIPRENT_CORE_DIR . 'includes/admin/class-er-dashboard.php',
                EQUIPRENT_CORE_DIR . 'includes/admin/class-er-meta-boxes.php',
                EQUIPRENT_CORE_DIR . 'includes/admin/class-er-list-tables.php',
            );
            
            foreach ($admin_files as $file) {
                if (file_exists($file)) {
                    require_once $file;
                }
            }
        }
        
        // Public functionality
        if (!is_admin()) {
            $public_files = array(
                EQUIPRENT_CORE_DIR . 'includes/public/class-er-public.php',
                EQUIPRENT_CORE_DIR . 'includes/public/class-er-shortcodes.php',
                EQUIPRENT_CORE_DIR . 'includes/public/class-er-booking-forms.php',
            );
            
            foreach ($public_files as $file) {
                if (file_exists($file)) {
                    require_once $file;
                }
            }
        }
        
        // API functionality
        $api_file = EQUIPRENT_CORE_DIR . 'includes/api/class-er-rest-api.php';
        if (file_exists($api_file)) {
            require_once $api_file;
        }
    }
    
    /**
     * Load pro features (only if licensed)
     */
    private function load_pro_features() {
        if (!file_exists(EQUIPRENT_PRO_DIR)) {
            return;
        }
        
        // Pro licensing (already loaded in check_pro_license, but initialize it)
        if (class_exists('ER_License_Manager')) {
            new ER_License_Manager();
        }
        
        // Pro features
        $pro_files = array(
            EQUIPRENT_PRO_DIR . 'includes/analytics/class-er-analytics.php',
            EQUIPRENT_PRO_DIR . 'includes/reporting/class-er-advanced-reporting.php',
            EQUIPRENT_PRO_DIR . 'includes/integrations/class-er-payment-integration.php',
            EQUIPRENT_PRO_DIR . 'includes/integrations/class-er-delivery-management.php',
            EQUIPRENT_PRO_DIR . 'includes/integrations/class-er-email-automation.php',
            EQUIPRENT_PRO_DIR . 'includes/maintenance/class-er-maintenance-tracking.php',
            EQUIPRENT_PRO_DIR . 'includes/pricing/class-er-dynamic-pricing.php',
        );
        
        foreach ($pro_files as $file) {
            if (file_exists($file)) {
                require_once $file;
            }
        }
    }
    
    /**
     * Initialize modules
     */
    private function init_modules() {
        $this->modules = array(
            'inventory' => true,      // Equipment inventory management
            'bookings' => true,       // Rental booking system
            'customers' => true,      // Customer management
            'availability' => true,   // Availability calendar
            'invoicing' => true,      // Basic invoicing
        );
        
        // Pro modules
        if ($this->is_pro) {
            $this->modules['analytics'] = true;        // Advanced analytics
            $this->modules['delivery'] = true;         // Delivery management
            $this->modules['maintenance'] = true;      // Equipment maintenance tracking
            $this->modules['pricing'] = true;          // Dynamic pricing
            $this->modules['automation'] = true;       // Email automation
            $this->modules['payments'] = true;         // Payment processing
            $this->modules['reporting'] = true;        // Advanced reporting
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
                dirname(EQUIPRENT_PLUGIN_BASE) . '/core/languages/'
            );
        });
    }
    
    /**
     * Register admin hooks
     */
    private function define_admin_hooks() {
        // Initialize core functionality first
        if (class_exists('ER_Post_Types')) {
            ER_Post_Types::init();
        }
        if (class_exists('ER_Taxonomies')) {
            ER_Taxonomies::init();
        }
        if (class_exists('ER_Capabilities')) {
            ER_Capabilities::init();
        }
        if (class_exists('ER_Database')) {
            ER_Database::init();
        }
        
        // Initialize core business logic classes
        if (class_exists('ER_Equipment')) {
            ER_Equipment::init();
        }
        if (class_exists('ER_Booking')) {
            ER_Booking::init();
        }
        if (class_exists('ER_Customer')) {
            ER_Customer::init();
        }
        
        if (class_exists('ER_Admin')) {
            $admin = new ER_Admin();
            
            add_action('admin_enqueue_scripts', array($admin, 'enqueue_styles'));
            add_action('admin_enqueue_scripts', array($admin, 'enqueue_scripts'));
            add_action('admin_menu', array($admin, 'add_admin_menus'));
        }
        
        // Initialize admin settings
        if (class_exists('ER_Admin_Settings')) {
            new ER_Admin_Settings();
        }
        
        // Initialize dashboard
        if (class_exists('ER_Dashboard')) {
            new ER_Dashboard();
        }
        
        // Initialize meta boxes
        if (class_exists('ER_Meta_Boxes')) {
            add_action('admin_init', array('ER_Meta_Boxes', 'init'));
        }
    }
    
    /**
     * Register public hooks
     */
    private function define_public_hooks() {
        if (class_exists('ER_Public')) {
            $public = new ER_Public();
            
            add_action('wp_enqueue_scripts', array($public, 'enqueue_styles'));
            add_action('wp_enqueue_scripts', array($public, 'enqueue_scripts'));
        }
        
        // Initialize shortcodes
        if (class_exists('ER_Shortcodes')) {
            ER_Shortcodes::init();
        }
        
        // Initialize booking forms
        if (class_exists('ER_Booking_Forms')) {
            ER_Booking_Forms::init();
        }
    }
    
    /**
     * Register API hooks
     */
    private function define_api_hooks() {
        // Only load API if class exists
        if (class_exists('ER_REST_API')) {
            add_action('rest_api_init', array('ER_REST_API', 'register_routes'));
        }
        
        // Pro API endpoints (if licensed)
        if ($this->is_pro && class_exists('ER_License_Manager')) {
            add_action('rest_api_init', array('ER_License_Manager', 'register_routes'));
        }
    }
    
    /**
     * Check if pro features are available
     */
    public function is_pro() {
        return $this->is_pro;
    }
    
    /**
     * Get available modules
     */
    public function get_modules() {
        return $this->modules;
    }
    
    /**
     * Check if specific module is enabled
     */
    public function is_module_enabled($module) {
        return isset($this->modules[$module]) && $this->modules[$module];
    }
    
    /**
     * Get plugin version
     */
    public function get_version() {
        return EQUIPRENT_VERSION;
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
function equiprent_pro_activate() {
    // Create database tables and setup defaults
    require_once EQUIPRENT_CORE_DIR . 'includes/class-er-activator.php';
    ER_Activator::activate();
}
register_activation_hook(__FILE__, 'equiprent_pro_activate');

/**
 * Plugin deactivation hook
 */
function equiprent_pro_deactivate() {
    require_once EQUIPRENT_CORE_DIR . 'includes/class-er-deactivator.php';
    ER_Deactivator::deactivate();
}
register_deactivation_hook(__FILE__, 'equiprent_pro_deactivate');

/**
 * Check for database updates on admin pages
 */
function equiprent_pro_check_updates() {
    if (is_admin()) {
        require_once EQUIPRENT_CORE_DIR . 'includes/class-er-activator.php';
        ER_Activator::check_for_updates();
    }
}
add_action('admin_init', 'equiprent_pro_check_updates');

/**
 * Initialize and run the plugin
 */
function run_equiprent_pro() {
    $plugin = EquipRent_Pro::get_instance();
    $plugin->run();
}
run_equiprent_pro();