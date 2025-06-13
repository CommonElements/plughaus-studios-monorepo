<?php
/**
 * Plugin Name: DealerEdge - Auto Shop & Dealer Management
 * Plugin URI: https://vireodesigns.com/plugins/dealeredge
 * Description: Complete auto shop and small car dealership management system for WordPress. Manage inventory, customers, work orders, sales, and more.
 * Version: 1.0.0
 * Author: Vireo Designs
 * Author URI: https://vireodesigns.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: dealeredge
 * Domain Path: /core/languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('DE_VERSION', '1.0.0');
define('DE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('DE_PLUGIN_BASE', plugin_basename(__FILE__));
define('DE_CORE_DIR', DE_PLUGIN_DIR . 'core/');

/**
 * Main DealerEdge Class (FREE VERSION)
 */
class DealerEdge {
    
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
        // Only load files that actually exist to prevent fatal errors
        
        // Shared utilities
        if (file_exists(DE_CORE_DIR . 'includes/shared/class-de-utilities.php')) {
            require_once DE_CORE_DIR . 'includes/shared/class-de-utilities.php';
        }
        
        // Core functionality (only existing files)
        if (file_exists(DE_CORE_DIR . 'includes/core/class-de-capabilities.php')) {
            require_once DE_CORE_DIR . 'includes/core/class-de-capabilities.php';
        }
        if (file_exists(DE_CORE_DIR . 'includes/core/class-de-post-types.php')) {
            require_once DE_CORE_DIR . 'includes/core/class-de-post-types.php';
        }
        if (file_exists(DE_CORE_DIR . 'includes/core/class-de-taxonomies.php')) {
            require_once DE_CORE_DIR . 'includes/core/class-de-taxonomies.php';
        }
        if (file_exists(DE_CORE_DIR . 'includes/core/class-de-data-validation.php')) {
            require_once DE_CORE_DIR . 'includes/core/class-de-data-validation.php';
        }
        
        // Admin functionality (only existing files)
        if (file_exists(DE_CORE_DIR . 'includes/admin/class-de-admin.php')) {
            require_once DE_CORE_DIR . 'includes/admin/class-de-admin.php';
        }
        if (file_exists(DE_CORE_DIR . 'includes/admin/class-de-admin-menus.php')) {
            require_once DE_CORE_DIR . 'includes/admin/class-de-admin-menus.php';
        }
        if (file_exists(DE_CORE_DIR . 'includes/admin/class-de-admin-settings.php')) {
            require_once DE_CORE_DIR . 'includes/admin/class-de-admin-settings.php';
        }
        if (file_exists(DE_CORE_DIR . 'includes/admin/class-de-meta-boxes.php')) {
            require_once DE_CORE_DIR . 'includes/admin/class-de-meta-boxes.php';
        }
        if (file_exists(DE_CORE_DIR . 'includes/admin/class-de-admin-list-tables.php')) {
            require_once DE_CORE_DIR . 'includes/admin/class-de-admin-list-tables.php';
        }
        
        // Public functionality (only existing files)
        if (file_exists(DE_CORE_DIR . 'includes/public/class-de-public.php')) {
            require_once DE_CORE_DIR . 'includes/public/class-de-public.php';
        }
        if (file_exists(DE_CORE_DIR . 'includes/public/class-de-shortcodes.php')) {
            require_once DE_CORE_DIR . 'includes/public/class-de-shortcodes.php';
        }
        
        // API functionality
        if (file_exists(DE_CORE_DIR . 'includes/api/class-de-rest-api.php')) {
            require_once DE_CORE_DIR . 'includes/api/class-de-rest-api.php';
        }
        
        // TODO: Add additional files as they are created
    }
    
    private function set_locale() {
        add_action('plugins_loaded', function() {
            load_plugin_textdomain(
                'dealeredge',
                false,
                dirname(DE_PLUGIN_BASE) . '/core/languages/'
            );
        });
    }
    
    private function define_admin_hooks() {
        // Only initialize classes that exist
        if (class_exists('DE_Post_Types')) {
            DE_Post_Types::init();
        }
        
        if (class_exists('DE_Capabilities')) {
            DE_Capabilities::init();
        }
        
        if (class_exists('DE_Admin')) {
            $admin = new DE_Admin();
            add_action('admin_enqueue_scripts', array($admin, 'enqueue_styles'));
            add_action('admin_enqueue_scripts', array($admin, 'enqueue_scripts'));
            add_action('admin_menu', array($admin, 'add_admin_menus'));
        }
        
        if (class_exists('DE_Admin_Settings')) {
            new DE_Admin_Settings();
        }
        
        if (class_exists('DE_Meta_Boxes')) {
            add_action('admin_init', array('DE_Meta_Boxes', 'init'));
        }
    }
    
    private function define_public_hooks() {
        // Only initialize classes that exist
        if (class_exists('DE_Public')) {
            $public = new DE_Public();
            add_action('wp_enqueue_scripts', array($public, 'enqueue_styles'));
            add_action('wp_enqueue_scripts', array($public, 'enqueue_scripts'));
        }
        
        if (class_exists('DE_Shortcodes')) {
            DE_Shortcodes::init();
        }
        
        // TODO: Add more public hooks when classes are created
    }
    
    private function define_api_hooks() {
        if (class_exists('DE_REST_API')) {
            add_action('rest_api_init', array('DE_REST_API', 'register_routes'));
        }
    }
    
    public function run() {
        // Plugin ready
    }
}

/**
 * Plugin activation
 */
function de_activate() {
    $activator_path = DE_CORE_DIR . 'includes/class-de-activator.php';
    if (file_exists($activator_path)) {
        require_once $activator_path;
        DE_Activator::activate();
    }
    // TODO: Create activator class when needed
}
register_activation_hook(__FILE__, 'de_activate');

/**
 * Plugin deactivation
 */
function de_deactivate() {
    $deactivator_path = DE_CORE_DIR . 'includes/class-de-deactivator.php';
    if (file_exists($deactivator_path)) {
        require_once $deactivator_path;
        DE_Deactivator::deactivate();
    }
    // TODO: Create deactivator class when needed
}
register_deactivation_hook(__FILE__, 'de_deactivate');

/**
 * Initialize plugin
 */
function run_dealeredge() {
    $plugin = DealerEdge::get_instance();
    $plugin->run();
}
run_dealeredge();