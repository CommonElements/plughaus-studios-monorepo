<?php
/**
 * Plugin Name: Knot4 - Nonprofit Management Platform
 * Plugin URI: https://plughausstudios.com/plugins/knot4
 * Description: Tied to your mission. Complete nonprofit management platform for donations, members, events, and CRM - all from WordPress.
 * Version: 1.0.0
 * Author: PlugHaus Studios
 * Author URI: https://plughausstudios.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: knot4
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('KNOT4_VERSION', '1.0.0');
define('KNOT4_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('KNOT4_PLUGIN_URL', plugin_dir_url(__FILE__));
define('KNOT4_PLUGIN_BASE', plugin_basename(__FILE__));
define('KNOT4_CORE_DIR', KNOT4_PLUGIN_DIR . 'core/');
define('KNOT4_PRO_DIR', KNOT4_PLUGIN_DIR . 'pro/');

/**
 * Main Knot4 Class
 * 
 * Nonprofit management platform for WordPress
 * Modules: Donations, CRM, Events, Communications, Reports
 */
class Knot4 {
    
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
        // Shared utilities (always loaded first)
        require_once KNOT4_CORE_DIR . 'includes/shared/class-knot4-utilities.php';
        
        // Core functionality (always loaded)
        require_once KNOT4_CORE_DIR . 'includes/core/class-knot4-capabilities.php';
        require_once KNOT4_CORE_DIR . 'includes/core/class-knot4-post-types.php';
        require_once KNOT4_CORE_DIR . 'includes/core/class-knot4-taxonomies.php';
        require_once KNOT4_CORE_DIR . 'includes/core/class-knot4-database.php';
        
        // Email system
        require_once KNOT4_CORE_DIR . 'includes/class-knot4-email-manager.php';
        
        // Admin functionality
        require_once KNOT4_CORE_DIR . 'includes/admin/class-knot4-admin.php';
        require_once KNOT4_CORE_DIR . 'includes/admin/class-knot4-admin-settings.php';
        require_once KNOT4_CORE_DIR . 'includes/admin/class-knot4-email-settings-admin.php';
        require_once KNOT4_CORE_DIR . 'includes/admin/class-knot4-reports-admin.php';
        require_once KNOT4_CORE_DIR . 'includes/admin/class-knot4-dashboard.php';
        
        // Public functionality
        require_once KNOT4_CORE_DIR . 'includes/public/class-knot4-public.php';
        require_once KNOT4_CORE_DIR . 'includes/public/class-knot4-shortcodes.php';
        
        // Core modules (free features)
        $this->load_core_modules();
        
        // Payment gateways
        $this->load_payment_gateways();
        
        // API functionality
        if (file_exists(KNOT4_CORE_DIR . 'includes/api/class-knot4-rest-api.php')) {
            require_once KNOT4_CORE_DIR . 'includes/api/class-knot4-rest-api.php';
        }
    }
    
    /**
     * Load core modules
     */
    private function load_core_modules() {
        // Donations module (core feature)
        require_once KNOT4_CORE_DIR . 'modules/donations/class-knot4-donations.php';
        require_once KNOT4_CORE_DIR . 'modules/donations/class-knot4-donation-forms.php';
        
        // CRM module (basic features in free)
        require_once KNOT4_CORE_DIR . 'modules/crm/class-knot4-crm.php';
        require_once KNOT4_CORE_DIR . 'modules/crm/class-knot4-donors.php';
        
        // Events module (limited in free)
        require_once KNOT4_CORE_DIR . 'modules/events/class-knot4-events.php';
        
        // Communications module (basic)
        require_once KNOT4_CORE_DIR . 'modules/communications/class-knot4-communications.php';
        
        // Reports module (basic)
        require_once KNOT4_CORE_DIR . 'modules/reports/class-knot4-reports.php';
    }
    
    /**
     * Load payment gateways
     */
    private function load_payment_gateways() {
        // Stripe gateway (always available)
        if (file_exists(KNOT4_CORE_DIR . 'modules/payments/class-knot4-stripe-gateway.php')) {
            require_once KNOT4_CORE_DIR . 'modules/payments/class-knot4-stripe-gateway.php';
        }
        
        // PayPal gateway (pro feature)
        if ($this->is_pro && file_exists(KNOT4_PRO_DIR . 'modules/payments/class-knot4-paypal-gateway.php')) {
            require_once KNOT4_PRO_DIR . 'modules/payments/class-knot4-paypal-gateway.php';
        }
    }
    
    /**
     * Load pro features (only if licensed)
     */
    private function load_pro_features() {
        if (!file_exists(KNOT4_PRO_DIR)) {
            return;
        }
        
        // Pro licensing
        require_once KNOT4_PRO_DIR . 'includes/licensing/class-knot4-license-manager.php';
        
        // Pro modules
        require_once KNOT4_PRO_DIR . 'modules/advanced-crm/class-knot4-advanced-crm.php';
        require_once KNOT4_PRO_DIR . 'modules/campaigns/class-knot4-campaigns.php';
        require_once KNOT4_PRO_DIR . 'modules/member-portal/class-knot4-member-portal.php';
        require_once KNOT4_PRO_DIR . 'modules/advanced-reports/class-knot4-advanced-reports.php';
        
        // Pro integrations
        require_once KNOT4_PRO_DIR . 'includes/integrations/class-knot4-payment-gateways.php';
        require_once KNOT4_PRO_DIR . 'includes/integrations/class-knot4-email-providers.php';
    }
    
    /**
     * Initialize modules
     */
    private function init_modules() {
        $this->modules = array(
            'donations' => true,
            'crm' => true,
            'events' => true,
            'communications' => true,
            'reports' => true
        );
        
        // Pro modules
        if ($this->is_pro) {
            $this->modules['campaigns'] = true;
            $this->modules['member_portal'] = true;
            $this->modules['advanced_reports'] = true;
        }
    }
    
    /**
     * Set plugin locale for translations
     */
    private function set_locale() {
        add_action('plugins_loaded', function() {
            load_plugin_textdomain(
                'knot4',
                false,
                dirname(KNOT4_PLUGIN_BASE) . '/core/languages/'
            );
        });
    }
    
    /**
     * Register admin hooks
     */
    private function define_admin_hooks() {
        // Initialize core functionality first
        Knot4_Post_Types::init();
        Knot4_Taxonomies::init();
        Knot4_Capabilities::init();
        Knot4_Database::init();
        
        $admin = new Knot4_Admin();
        
        add_action('admin_enqueue_scripts', array($admin, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($admin, 'enqueue_scripts'));
        add_action('admin_menu', array($admin, 'add_admin_menus'));
        
        // Initialize admin settings
        new Knot4_Admin_Settings();
        
        // Initialize dashboard
        new Knot4_Dashboard();
        
        // Initialize email system
        Knot4_Email_Manager::init();
        Knot4_Email_Settings_Admin::init();
        
        // Initialize reports system
        Knot4_Reports_Admin::init();
        
        // Initialize modules
        if (isset($this->modules['donations']) && $this->modules['donations']) {
            Knot4_Donations::init();
        }
        if (isset($this->modules['crm']) && $this->modules['crm']) {
            Knot4_CRM::init();
        }
        if (isset($this->modules['events']) && $this->modules['events']) {
            Knot4_Events::init();
        }
        
        // Initialize payment gateways
        if (class_exists('Knot4_Stripe_Gateway')) {
            new Knot4_Stripe_Gateway();
        }
    }
    
    /**
     * Register public hooks
     */
    private function define_public_hooks() {
        $public = new Knot4_Public();
        
        add_action('wp_enqueue_scripts', array($public, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array($public, 'enqueue_scripts'));
        
        // Initialize shortcodes
        Knot4_Shortcodes::init();
    }
    
    /**
     * Register API hooks
     */
    private function define_api_hooks() {
        // Only load API if class exists
        if (class_exists('Knot4_REST_API')) {
            add_action('rest_api_init', array('Knot4_REST_API', 'register_routes'));
        }
        
        // Pro API endpoints (if licensed)
        if ($this->is_pro && class_exists('Knot4_License_Manager')) {
            add_action('rest_api_init', array('Knot4_License_Manager', 'register_routes'));
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
function knot4_activate() {
    // Create database tables and setup defaults
    require_once KNOT4_CORE_DIR . 'includes/class-knot4-activator.php';
    Knot4_Activator::activate();
}
register_activation_hook(__FILE__, 'knot4_activate');

/**
 * Plugin deactivation hook
 */
function knot4_deactivate() {
    require_once KNOT4_CORE_DIR . 'includes/class-knot4-deactivator.php';
    Knot4_Deactivator::deactivate();
}
register_deactivation_hook(__FILE__, 'knot4_deactivate');

/**
 * Initialize and run the plugin
 */
function run_knot4() {
    $plugin = Knot4::get_instance();
    $plugin->run();
}
run_knot4();