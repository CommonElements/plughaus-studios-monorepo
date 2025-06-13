<?php
/**
 * Plugin Name: GymFlow - Fitness Studio Management
 * Plugin URI: https://vireodesigns.com/plugins/gymflow
 * Description: Complete fitness studio and gym management system for WordPress. Manage members, classes, trainers, equipment, and bookings with ease.
 * Version: 1.0.0
 * Author: Vireo Designs
 * Author URI: https://vireodesigns.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: gymflow
 * Domain Path: /languages
 * Requires at least: 5.8
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Network: false
 *
 * @package GymFlow
 * @author Vireo Designs
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('GYMFLOW_VERSION', '1.0.0');
define('GYMFLOW_PLUGIN_FILE', __FILE__);
define('GYMFLOW_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('GYMFLOW_PLUGIN_URL', plugin_dir_url(__FILE__));
define('GYMFLOW_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * The main GymFlow class
 * 
 * @since 1.0.0
 */
final class GymFlow {

    /**
     * Instance of this class
     * @var GymFlow
     */
    private static $instance = null;

    /**
     * Pro license status
     * @var bool
     */
    private $is_pro = false;

    /**
     * Plugin components
     * @var array
     */
    private $components = array();

    /**
     * Get instance
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
        $this->load_dependencies();
        $this->init_hooks();
        $this->init_components();
    }

    /**
     * Load required dependencies
     */
    private function load_dependencies() {
        // Core includes
        require_once GYMFLOW_PLUGIN_PATH . 'core/includes/class-gf-activator.php';
        require_once GYMFLOW_PLUGIN_PATH . 'core/includes/class-gf-deactivator.php';
        require_once GYMFLOW_PLUGIN_PATH . 'core/includes/shared/class-gf-utilities.php';
        require_once GYMFLOW_PLUGIN_PATH . 'core/includes/core/class-gf-post-types.php';
        require_once GYMFLOW_PLUGIN_PATH . 'core/includes/core/class-gf-taxonomies.php';
        require_once GYMFLOW_PLUGIN_PATH . 'core/includes/core/class-gf-capabilities.php';
        require_once GYMFLOW_PLUGIN_PATH . 'core/includes/core/class-gf-data-validation.php';
        require_once GYMFLOW_PLUGIN_PATH . 'core/includes/core/class-gf-database.php';

        // Core business logic
        require_once GYMFLOW_PLUGIN_PATH . 'core/includes/core/class-gf-member.php';
        require_once GYMFLOW_PLUGIN_PATH . 'core/includes/core/class-gf-class.php';
        require_once GYMFLOW_PLUGIN_PATH . 'core/includes/core/class-gf-trainer.php';
        require_once GYMFLOW_PLUGIN_PATH . 'core/includes/core/class-gf-booking.php';

        // Admin includes
        if (is_admin()) {
            require_once GYMFLOW_PLUGIN_PATH . 'core/includes/admin/class-gf-admin.php';
            require_once GYMFLOW_PLUGIN_PATH . 'core/includes/admin/class-gf-meta-boxes.php';
        }

        // Public includes
        require_once GYMFLOW_PLUGIN_PATH . 'core/includes/public/class-gf-shortcodes.php';

        // API includes
        require_once GYMFLOW_PLUGIN_PATH . 'core/includes/api/class-gf-rest-api.php';

        // Load Pro features if license is valid
        if ($this->check_pro_license()) {
            $this->load_pro_features();
        }
    }

    /**
     * Check if Pro license is valid
     */
    private function check_pro_license() {
        // Free version - always return false
        return false;
        
        // Pro version would check license here:
        // if (class_exists('GF_License_Manager')) {
        //     return GF_License_Manager::is_valid();
        // }
        // return false;
    }

    /**
     * Load Pro features
     */
    private function load_pro_features() {
        if (!file_exists(GYMFLOW_PLUGIN_PATH . 'pro/')) {
            return;
        }

        // Pro admin features
        if (is_admin()) {
            $pro_files = array(
                'pro/includes/analytics/class-gf-analytics.php',
                'pro/includes/automation/class-gf-automation.php',
                'pro/includes/licensing/class-gf-license-manager.php',
                'pro/includes/reporting/class-gf-reports.php'
            );

            foreach ($pro_files as $file) {
                if (file_exists(GYMFLOW_PLUGIN_PATH . $file)) {
                    require_once GYMFLOW_PLUGIN_PATH . $file;
                }
            }
        }
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Activation and deactivation hooks
        register_activation_hook(__FILE__, array('GF_Activator', 'activate'));
        register_deactivation_hook(__FILE__, array('GF_Deactivator', 'deactivate'));

        // Plugin initialization
        add_action('init', array($this, 'init'), 0);
        add_action('plugins_loaded', array($this, 'plugins_loaded'));

        // Admin initialization
        if (is_admin()) {
            add_action('admin_init', array($this, 'admin_init'));
        }

        // Frontend initialization
        add_action('wp_enqueue_scripts', array($this, 'enqueue_public_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));

        // AJAX hooks
        add_action('wp_ajax_gf_check_class_availability', array($this, 'ajax_check_class_availability'));
        add_action('wp_ajax_nopriv_gf_check_class_availability', array($this, 'ajax_check_class_availability'));
        add_action('wp_ajax_gf_search_members', array($this, 'ajax_search_members'));
        
        // Database update check
        add_action('admin_notices', array($this, 'check_database_updates'));
    }

    /**
     * Initialize components
     */
    private function init_components() {
        $this->components = array(
            'post_types' => new GF_Post_Types(),
            'taxonomies' => new GF_Taxonomies(),
            'capabilities' => new GF_Capabilities(),
            'shortcodes' => new GF_Shortcodes(),
            'rest_api' => new GF_Rest_API()
        );

        if (is_admin()) {
            $this->components['admin'] = new GF_Admin();
            $this->components['meta_boxes'] = new GF_Meta_Boxes();
        }

        // Initialize Pro components if available
        if ($this->is_pro()) {
            $this->init_pro_components();
        }
    }

    /**
     * Initialize Pro components
     */
    private function init_pro_components() {
        if (class_exists('GF_Analytics')) {
            $this->components['analytics'] = new GF_Analytics();
        }
        if (class_exists('GF_Automation')) {
            $this->components['automation'] = new GF_Automation();
        }
        if (class_exists('GF_Reports')) {
            $this->components['reports'] = new GF_Reports();
        }
    }

    /**
     * Initialize plugin
     */
    public function init() {
        // Load text domain
        load_plugin_textdomain('gymflow', false, dirname(GYMFLOW_PLUGIN_BASENAME) . '/languages');

        // Initialize components
        foreach ($this->components as $component) {
            if (method_exists($component, 'init')) {
                $component->init();
            }
        }

        // Fire init action
        do_action('gymflow_init');
    }

    /**
     * Plugins loaded hook
     */
    public function plugins_loaded() {
        // Check if WooCommerce is active for payment features
        if (class_exists('WooCommerce')) {
            // Initialize WooCommerce integration
            require_once GYMFLOW_PLUGIN_PATH . 'core/includes/integrations/class-gf-woocommerce.php';
            new GF_WooCommerce();
        }

        do_action('gymflow_plugins_loaded');
    }

    /**
     * Admin initialization
     */
    public function admin_init() {
        // Check for database updates
        $this->maybe_update_database();

        do_action('gymflow_admin_init');
    }

    /**
     * Enqueue public scripts and styles
     */
    public function enqueue_public_scripts() {
        // Public CSS
        wp_enqueue_style(
            'gymflow-public',
            GYMFLOW_PLUGIN_URL . 'core/assets/css/public.css',
            array(),
            GYMFLOW_VERSION
        );

        // Public JavaScript
        wp_enqueue_script(
            'gymflow-public',
            GYMFLOW_PLUGIN_URL . 'core/assets/js/public.js',
            array('jquery'),
            GYMFLOW_VERSION,
            true
        );

        // Localize script
        wp_localize_script('gymflow-public', 'gymflow_public', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('gymflow_public'),
            'strings' => array(
                'error' => __('An error occurred. Please try again.', 'gymflow'),
                'success' => __('Success!', 'gymflow'),
                'loading' => __('Loading...', 'gymflow')
            )
        ));
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        // Only load on our admin pages
        $gymflow_pages = array(
            'gymflow-dashboard',
            'gymflow-members',
            'gymflow-classes', 
            'gymflow-trainers',
            'gymflow-bookings',
            'gymflow-equipment',
            'gymflow-settings'
        );

        $load_scripts = false;
        foreach ($gymflow_pages as $page) {
            if (strpos($hook, $page) !== false) {
                $load_scripts = true;
                break;
            }
        }

        // Also load on post edit pages for our post types
        global $post_type;
        if (in_array($post_type, array('gf_member', 'gf_class', 'gf_trainer', 'gf_booking', 'gf_equipment'))) {
            $load_scripts = true;
        }

        if (!$load_scripts) {
            return;
        }

        // Admin CSS
        wp_enqueue_style(
            'gymflow-admin',
            GYMFLOW_PLUGIN_URL . 'core/assets/css/admin.css',
            array(),
            GYMFLOW_VERSION
        );

        // Admin JavaScript
        wp_enqueue_script(
            'gymflow-admin',
            GYMFLOW_PLUGIN_URL . 'core/assets/js/admin.js',
            array('jquery', 'jquery-ui-datepicker', 'jquery-ui-timepicker-addon'),
            GYMFLOW_VERSION,
            true
        );

        // Chart.js for analytics (Pro feature)
        if ($this->is_pro()) {
            wp_enqueue_script(
                'chart-js',
                'https://cdn.jsdelivr.net/npm/chart.js',
                array(),
                '3.9.1',
                true
            );
        }

        // Localize admin script
        wp_localize_script('gymflow-admin', 'gymflow_admin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('gymflow_admin'),
            'currency_symbol' => GF_Utilities::get_setting('currency_symbol', '$'),
            'date_format' => GF_Utilities::get_setting('date_format', 'Y-m-d'),
            'time_format' => GF_Utilities::get_setting('time_format', 'H:i'),
            'strings' => array(
                'error' => __('An error occurred. Please try again.', 'gymflow'),
                'success' => __('Success!', 'gymflow'),
                'confirm_delete' => __('Are you sure you want to delete this item?', 'gymflow'),
                'loading' => __('Loading...', 'gymflow')
            )
        ));

        // Enqueue WordPress media uploader
        wp_enqueue_media();
    }

    /**
     * AJAX: Check class availability
     */
    public function ajax_check_class_availability() {
        check_ajax_referer('gymflow_admin', 'nonce');

        $class_id = intval($_POST['class_id']);
        $date = sanitize_text_field($_POST['date']);
        $time = sanitize_text_field($_POST['time']);

        $available_spots = GF_Class::get_available_spots($class_id, $date, $time);

        wp_send_json_success(array(
            'available_spots' => $available_spots,
            'is_available' => $available_spots > 0
        ));
    }

    /**
     * AJAX: Search members
     */
    public function ajax_search_members() {
        check_ajax_referer('gymflow_admin', 'nonce');

        $query = sanitize_text_field($_POST['query']);
        $limit = intval($_POST['limit']) ?: 10;

        $members = GF_Member::search($query, $limit);

        wp_send_json_success($members);
    }

    /**
     * Check if database needs updates
     */
    public function check_database_updates() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $db_version = get_option('gymflow_db_version', '0');
        if (version_compare($db_version, GYMFLOW_VERSION, '<')) {
            echo '<div class="notice notice-warning"><p>';
            echo __('GymFlow database needs to be updated. ', 'gymflow');
            echo '<a href="' . admin_url('admin.php?page=gymflow-settings&action=update_db') . '">';
            echo __('Update Now', 'gymflow');
            echo '</a></p></div>';
        }
    }

    /**
     * Maybe update database
     */
    private function maybe_update_database() {
        if (isset($_GET['action']) && $_GET['action'] === 'update_db' && current_user_can('manage_options')) {
            GF_Database::update_database();
            wp_redirect(admin_url('admin.php?page=gymflow-settings&updated=1'));
            exit;
        }
    }

    /**
     * Get component
     */
    public function get_component($name) {
        return isset($this->components[$name]) ? $this->components[$name] : null;
    }

    /**
     * Is Pro version
     */
    public function is_pro() {
        return $this->is_pro;
    }

    /**
     * Get plugin version
     */
    public function get_version() {
        return GYMFLOW_VERSION;
    }

    /**
     * Get plugin path
     */
    public function get_plugin_path() {
        return GYMFLOW_PLUGIN_PATH;
    }

    /**
     * Get plugin URL
     */
    public function get_plugin_url() {
        return GYMFLOW_PLUGIN_URL;
    }
}

/**
 * Initialize GymFlow
 */
function gymflow() {
    return GymFlow::get_instance();
}

// Initialize the plugin
add_action('plugins_loaded', 'gymflow', 1);

/**
 * Activation function
 */
function gymflow_activate() {
    GF_Activator::activate();
}

/**
 * Deactivation function  
 */
function gymflow_deactivate() {
    GF_Deactivator::deactivate();
}