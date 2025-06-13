<?php
/**
 * Plugin Name: Vireo Sports League Manager
 * Plugin URI: https://vireodesigns.com/plugins/sports-league
 * Description: Professional sports league management system for WordPress. Manage teams, players, schedules, standings, and statistics with ease.
 * Version: 1.0.0
 * Author: Vireo Designs
 * Author URI: https://vireodesigns.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: vireo-league
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
define('VIREO_LEAGUE_VERSION', '1.0.0');
define('VIREO_LEAGUE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('VIREO_LEAGUE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('VIREO_LEAGUE_PLUGIN_BASE', plugin_basename(__FILE__));
define('VIREO_LEAGUE_CORE_DIR', VIREO_LEAGUE_PLUGIN_DIR . 'core/');
define('VIREO_LEAGUE_PRO_DIR', VIREO_LEAGUE_PLUGIN_DIR . 'pro/');

/**
 * Main Vireo Sports League Class
 * 
 * Professional sports league management for WordPress
 * Modules: Teams, Players, Matches, Standings, Statistics
 */
class Vireo_Sports_League {
    
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
        // For now, return false (free version only)
        // This will be replaced with actual license checking
        return false;
    }
    
    /**
     * Load required dependencies
     */
    private function load_dependencies() {
        // Only load files that exist
        $required_files = array(
            VIREO_LEAGUE_CORE_DIR . 'includes/shared/class-vsl-utilities.php',
            VIREO_LEAGUE_CORE_DIR . 'includes/core/class-vsl-capabilities.php',
            VIREO_LEAGUE_CORE_DIR . 'includes/core/class-vsl-post-types.php',
            VIREO_LEAGUE_CORE_DIR . 'includes/core/class-vsl-taxonomies.php',
            VIREO_LEAGUE_CORE_DIR . 'includes/core/class-vsl-database.php',
        );
        
        foreach ($required_files as $file) {
            if (file_exists($file)) {
                require_once $file;
            }
        }
        
        // Load admin files
        if (is_admin()) {
            $admin_files = array(
                VIREO_LEAGUE_CORE_DIR . 'includes/admin/class-vsl-admin.php',
                VIREO_LEAGUE_CORE_DIR . 'includes/admin/class-vsl-admin-settings.php',
                VIREO_LEAGUE_CORE_DIR . 'includes/admin/class-vsl-dashboard.php',
            );
            
            foreach ($admin_files as $file) {
                if (file_exists($file)) {
                    require_once $file;
                }
            }
        }
        
        // Load public files
        if (!is_admin()) {
            $public_files = array(
                VIREO_LEAGUE_CORE_DIR . 'includes/public/class-vsl-public.php',
                VIREO_LEAGUE_CORE_DIR . 'includes/public/class-vsl-shortcodes.php',
            );
            
            foreach ($public_files as $file) {
                if (file_exists($file)) {
                    require_once $file;
                }
            }
        }
    }
    
    /**
     * Load core modules
     */
    private function load_core_modules() {
        // Modules will be loaded later - commented out for initial setup
        /*
        // Teams module (core feature)
        require_once VIREO_LEAGUE_CORE_DIR . 'modules/teams/class-psl-teams.php';
        
        // Players module (core feature)
        require_once VIREO_LEAGUE_CORE_DIR . 'modules/players/class-psl-players.php';
        
        // Matches module (core feature)
        require_once VIREO_LEAGUE_CORE_DIR . 'modules/matches/class-psl-matches.php';
        
        // Standings module (core feature)
        require_once VIREO_LEAGUE_CORE_DIR . 'modules/standings/class-psl-standings.php';
        
        // Statistics module (basic features in free)
        require_once VIREO_LEAGUE_CORE_DIR . 'modules/statistics/class-psl-statistics.php';
        */
    }
    
    /**
     * Load pro features (only if licensed)
     */
    private function load_pro_features() {
        if (!file_exists(VIREO_LEAGUE_PRO_DIR)) {
            return;
        }
        
        // Pro licensing
        require_once VIREO_LEAGUE_PRO_DIR . 'includes/licensing/class-vsl-license-manager.php';
        
        // Pro modules
        require_once VIREO_LEAGUE_PRO_DIR . 'modules/registrations/class-vsl-registrations.php';
        require_once VIREO_LEAGUE_PRO_DIR . 'modules/payments/class-vsl-payments.php';
        require_once VIREO_LEAGUE_PRO_DIR . 'modules/playoffs/class-vsl-playoffs.php';
        require_once VIREO_LEAGUE_PRO_DIR . 'modules/officials/class-vsl-officials.php';
        require_once VIREO_LEAGUE_PRO_DIR . 'modules/media/class-vsl-media.php';
        
        // Pro integrations
        require_once VIREO_LEAGUE_PRO_DIR . 'includes/integrations/class-vsl-email-notifications.php';
        require_once VIREO_LEAGUE_PRO_DIR . 'includes/integrations/class-vsl-calendar-sync.php';
    }
    
    /**
     * Initialize modules
     */
    private function init_modules() {
        $this->modules = array(
            'teams' => true,
            'players' => true,
            'matches' => true,
            'standings' => true,
            'statistics' => true
        );
        
        // Pro modules
        if ($this->is_pro) {
            $this->modules['registrations'] = true;
            $this->modules['payments'] = true;
            $this->modules['playoffs'] = true;
            $this->modules['officials'] = true;
            $this->modules['media'] = true;
        }
    }
    
    /**
     * Set plugin locale for translations
     */
    private function set_locale() {
        add_action('plugins_loaded', function() {
            load_plugin_textdomain(
                'vireo-league',
                false,
                dirname(VIREO_LEAGUE_PLUGIN_BASE) . '/core/languages/'
            );
        });
    }
    
    /**
     * Register admin hooks
     */
    private function define_admin_hooks() {
        // Initialize core functionality first
        if (class_exists('VSL_Post_Types')) {
            VSL_Post_Types::init();
        }
        if (class_exists('VSL_Taxonomies')) {
            VSL_Taxonomies::init();
        }
        if (class_exists('VSL_Capabilities')) {
            VSL_Capabilities::init();
        }
        if (class_exists('VSL_Database')) {
            VSL_Database::init();
        }
        
        if (class_exists('VSL_Admin')) {
            $admin = new VSL_Admin();
            
            add_action('admin_enqueue_scripts', array($admin, 'enqueue_styles'));
            add_action('admin_enqueue_scripts', array($admin, 'enqueue_scripts'));
            add_action('admin_menu', array($admin, 'add_admin_menus'));
        }
        
        // Initialize admin settings
        if (class_exists('VSL_Admin_Settings')) {
            new VSL_Admin_Settings();
        }
        
        // Initialize dashboard
        if (class_exists('VSL_Dashboard')) {
            new VSL_Dashboard();
        }
        
        // Module initialization will be added later
    }
    
    /**
     * Register public hooks
     */
    private function define_public_hooks() {
        if (class_exists('VSL_Public')) {
            $public = new VSL_Public();
            
            add_action('wp_enqueue_scripts', array($public, 'enqueue_styles'));
            add_action('wp_enqueue_scripts', array($public, 'enqueue_scripts'));
        }
        
        // Initialize shortcodes
        if (class_exists('VSL_Shortcodes')) {
            VSL_Shortcodes::init();
        }
    }
    
    /**
     * Register API hooks
     */
    private function define_api_hooks() {
        // Only load API if class exists
        if (class_exists('VSL_REST_API')) {
            add_action('rest_api_init', array('VSL_REST_API', 'register_routes'));
        }
        
        // Pro API endpoints (if licensed)
        if ($this->is_pro && class_exists('VSL_License_Manager')) {
            add_action('rest_api_init', array('VSL_License_Manager', 'register_routes'));
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
function vireo_sports_league_activate() {
    // Create database tables and setup defaults
    require_once VIREO_LEAGUE_CORE_DIR . 'includes/class-vsl-activator.php';
    VSL_Activator::activate();
}
register_activation_hook(__FILE__, 'vireo_sports_league_activate');

/**
 * Plugin deactivation hook
 */
function vireo_sports_league_deactivate() {
    require_once VIREO_LEAGUE_CORE_DIR . 'includes/class-vsl-deactivator.php';
    VSL_Deactivator::deactivate();
}
register_deactivation_hook(__FILE__, 'vireo_sports_league_deactivate');

/**
 * Initialize and run the plugin
 */
function run_vireo_sports_league() {
    $plugin = Vireo_Sports_League::get_instance();
    $plugin->run();
}
run_vireo_sports_league();