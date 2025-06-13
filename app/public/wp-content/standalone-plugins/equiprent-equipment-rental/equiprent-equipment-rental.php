<?php
/**
 * Plugin Name: EquipRent - Equipment Rental Management
 * Plugin URI: https://vireodesigns.com/plugins/equiprent-equipment-rental/
 * Description: Professional equipment rental management for WordPress. Handle inventory, bookings, customers, and billing.
 * Version: 1.0.0
 * Author: Vireo Designs
 * Author URI: https://vireodesigns.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: equiprent-equipment-rental
 * Domain Path: /languages
 * Requires at least: 5.8
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Network: false
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('EER_VERSION', '1.0.0');
define('EER_PLUGIN_FILE', __FILE__);
define('EER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('EER_PLUGIN_URL', plugin_dir_url(__FILE__));
define('EER_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main plugin class - 100% standalone
 */
class EER_Main {
    
    /**
     * Single instance
     */
    private static $instance = null;
    
    /**
     * Get single instance
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
        add_action('init', array($this, 'init'));
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Load core functionality only - no dependencies on other Vireo plugins
        $this->load_core();
        
        // Initialize admin interface
        if (is_admin()) {
            $this->load_admin();
        }
    }
    
    /**
     * Load text domain for translations
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'equiprent-equipment-rental',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages'
        );
    }
    
    /**
     * Load core functionality
     */
    private function load_core() {
        // Include core files using actual file structure
        $core_files = array(
            EER_PLUGIN_DIR . 'core/includes/shared/class-erp-utilities.php',
            EER_PLUGIN_DIR . 'core/includes/core/class-erp-capabilities.php',
            EER_PLUGIN_DIR . 'core/includes/core/class-erp-data-validation.php',
            EER_PLUGIN_DIR . 'core/includes/core/class-erp-post-types.php',
            EER_PLUGIN_DIR . 'core/includes/core/class-erp-taxonomies.php',
            EER_PLUGIN_DIR . 'core/includes/core/class-erp-equipment.php',
            EER_PLUGIN_DIR . 'core/includes/core/class-erp-booking.php',
            EER_PLUGIN_DIR . 'core/includes/core/class-erp-customer.php',
            EER_PLUGIN_DIR . 'core/includes/class-erp-activator.php',
            EER_PLUGIN_DIR . 'core/includes/class-erp-deactivator.php'
        );
        
        foreach ($core_files as $file) {
            if (file_exists($file)) {
                require_once $file;
            }
        }
        
        // Initialize post types and taxonomies
        if (class_exists('ERP_Post_Types')) {
            new ERP_Post_Types();
        }
        if (class_exists('ERP_Taxonomies')) {
            new ERP_Taxonomies();
        }
    }
    
    /**
     * Load admin functionality
     */
    private function load_admin() {
        // Include admin files using actual file structure
        $admin_files = array(
            EER_PLUGIN_DIR . 'core/includes/admin/class-erp-admin.php',
            EER_PLUGIN_DIR . 'core/includes/admin/class-erp-admin-menus.php',
            EER_PLUGIN_DIR . 'core/includes/admin/class-erp-admin-settings.php',
            EER_PLUGIN_DIR . 'core/includes/admin/class-erp-meta-boxes.php',
            EER_PLUGIN_DIR . 'core/includes/admin/class-erp-admin-list-tables.php'
        );
        
        foreach ($admin_files as $file) {
            if (file_exists($file)) {
                require_once $file;
            }
        }
        
        // Initialize admin functionality
        if (class_exists('ERP_Admin')) {
            new ERP_Admin();
        }
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Create database tables if needed
        $this->create_tables();
        
        // Set default options
        $this->set_default_options();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Create database tables
     */
    private function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Equipment rental specific tables
        $table_name = $wpdb->prefix . 'eer_equipment';
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name tinytext NOT NULL,
            category varchar(100),
            daily_rate decimal(10,2),
            status varchar(20) DEFAULT 'available',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Store version number
        add_option('equiprent_equipment_rental_version', EER_VERSION);
    }
    
    /**
     * Set default options
     */
    private function set_default_options() {
        $default_options = array(
            'enable_features' => true,
            'plugin_version' => EER_VERSION
        );
        
        add_option('equiprent_equipment_rental_options', $default_options);
    }
}

// Initialize the plugin
EER_Main::get_instance();
