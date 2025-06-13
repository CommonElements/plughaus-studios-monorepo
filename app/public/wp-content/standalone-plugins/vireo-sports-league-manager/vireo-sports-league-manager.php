<?php
/**
 * Plugin Name: Vireo Sports League Manager
 * Plugin URI: https://vireodesigns.com/plugins/vireo-sports-league-manager/
 * Description: Complete sports league management for WordPress. Manage teams, players, schedules, and statistics.
 * Version: 1.0.0
 * Author: Vireo Designs
 * Author URI: https://vireodesigns.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: vireo-sports-league-manager
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
define('VSLM_VERSION', '1.0.0');
define('VSLM_PLUGIN_FILE', __FILE__);
define('VSLM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('VSLM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('VSLM_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main plugin class - 100% standalone
 */
class VSLM_Main {
    
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
            'vireo-sports-league-manager',
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
            VSLM_PLUGIN_DIR . 'core/includes/shared/class-vsl-utilities.php',
            VSLM_PLUGIN_DIR . 'core/includes/core/class-vsl-capabilities.php',
            VSLM_PLUGIN_DIR . 'core/includes/core/class-vsl-database.php',
            VSLM_PLUGIN_DIR . 'core/includes/core/class-vsl-post-types.php',
            VSLM_PLUGIN_DIR . 'core/includes/core/class-vsl-taxonomies.php',
            VSLM_PLUGIN_DIR . 'core/includes/class-vsl-activator.php',
            VSLM_PLUGIN_DIR . 'core/includes/class-vsl-deactivator.php'
        );
        
        foreach ($core_files as $file) {
            if (file_exists($file)) {
                require_once $file;
            }
        }
        
        // Initialize post types and taxonomies
        if (class_exists('VSL_Post_Types')) {
            new VSL_Post_Types();
        }
        if (class_exists('VSL_Taxonomies')) {
            new VSL_Taxonomies();
        }
    }
    
    /**
     * Load admin functionality
     */
    private function load_admin() {
        // Include admin files using actual file structure
        $admin_files = array(
            VSLM_PLUGIN_DIR . 'core/includes/admin/class-vsl-admin.php',
            VSLM_PLUGIN_DIR . 'core/includes/admin/class-vsl-admin-settings.php',
            VSLM_PLUGIN_DIR . 'core/includes/admin/class-vsl-dashboard.php'
        );
        
        foreach ($admin_files as $file) {
            if (file_exists($file)) {
                require_once $file;
            }
        }
        
        // Initialize admin functionality
        if (class_exists('VSL_Admin')) {
            new VSL_Admin();
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
        
        // Sports league specific tables
        $table_name = $wpdb->prefix . 'vslm_teams';
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name tinytext NOT NULL,
            league_id mediumint(9),
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Store version number
        add_option('vireo_sports_league_manager_version', VSLM_VERSION);
    }
    
    /**
     * Set default options
     */
    private function set_default_options() {
        $default_options = array(
            'enable_features' => true,
            'plugin_version' => VSLM_VERSION
        );
        
        add_option('vireo_sports_league_manager_options', $default_options);
    }
}

// Initialize the plugin
VSLM_Main::get_instance();
