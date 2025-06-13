<?php
/**
 * Plugin Name: StorageFlow - Self Storage Management System
 * Plugin URI: https://vireodesigns.com/plugins/storageflow
 * Description: Complete self storage management system for WordPress. Manage units, tenants, rentals, payments, and facility operations.
 * Version: 1.0.0
 * Author: Vireo Designs
 * Author URI: https://vireodesigns.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: storageflow
 * Domain Path: /core/languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('SF_VERSION', '1.0.0');
define('SF_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SF_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SF_PLUGIN_BASE', plugin_basename(__FILE__));
define('SF_CORE_DIR', SF_PLUGIN_DIR . 'core/');

/**
 * Main StorageFlow Class (FREE VERSION)
 */
class StorageFlow {
    
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
        $this->init_cron_jobs();
        $this->init_storage_operations();
    }
    
    private function load_dependencies() {
        // Shared utilities
        if (file_exists(SF_CORE_DIR . 'includes/shared/class-sf-utilities.php')) {
            require_once SF_CORE_DIR . 'includes/shared/class-sf-utilities.php';
        } // TODO: Create SF_Utilities class
        
        if (file_exists(SF_CORE_DIR . 'includes/shared/class-sf-billing-engine.php')) {
            require_once SF_CORE_DIR . 'includes/shared/class-sf-billing-engine.php';
        } // TODO: Create SF_Billing_Engine class
        
        if (file_exists(SF_CORE_DIR . 'includes/shared/class-sf-notification-system.php')) {
            require_once SF_CORE_DIR . 'includes/shared/class-sf-notification-system.php';
        } // TODO: Create SF_Notification_System class
        
        if (file_exists(SF_CORE_DIR . 'includes/shared/class-sf-payment-processor.php')) {
            require_once SF_CORE_DIR . 'includes/shared/class-sf-payment-processor.php';
        } // TODO: Create SF_Payment_Processor class
        
        // Core storage management
        if (file_exists(SF_CORE_DIR . 'includes/core/class-sf-capabilities.php')) {
            require_once SF_CORE_DIR . 'includes/core/class-sf-capabilities.php';
        } // TODO: Create SF_Capabilities class
        
        if (file_exists(SF_CORE_DIR . 'includes/core/class-sf-post-types.php')) {
            require_once SF_CORE_DIR . 'includes/core/class-sf-post-types.php';
        } // TODO: Create SF_Post_Types class
        
        if (file_exists(SF_CORE_DIR . 'includes/core/class-sf-unit-manager.php')) {
            require_once SF_CORE_DIR . 'includes/core/class-sf-unit-manager.php';
        } // TODO: Create SF_Unit_Manager class
        
        if (file_exists(SF_CORE_DIR . 'includes/core/class-sf-rental-system.php')) {
            require_once SF_CORE_DIR . 'includes/core/class-sf-rental-system.php';
        } // TODO: Create SF_Rental_System class
        
        if (file_exists(SF_CORE_DIR . 'includes/core/class-sf-tenant-manager.php')) {
            require_once SF_CORE_DIR . 'includes/core/class-sf-tenant-manager.php';
        } // TODO: Create SF_Tenant_Manager class
        
        if (file_exists(SF_CORE_DIR . 'includes/core/class-sf-access-control.php')) {
            require_once SF_CORE_DIR . 'includes/core/class-sf-access-control.php';
        } // TODO: Create SF_Access_Control class
        
        if (file_exists(SF_CORE_DIR . 'includes/core/class-sf-facility-operations.php')) {
            require_once SF_CORE_DIR . 'includes/core/class-sf-facility-operations.php';
        } // TODO: Create SF_Facility_Operations class
        
        // Admin functionality
        if (file_exists(SF_CORE_DIR . 'includes/admin/class-sf-admin.php')) {
            require_once SF_CORE_DIR . 'includes/admin/class-sf-admin.php';
        } // TODO: Create SF_Admin class
        
        if (file_exists(SF_CORE_DIR . 'includes/admin/class-sf-meta-boxes.php')) {
            require_once SF_CORE_DIR . 'includes/admin/class-sf-meta-boxes.php';
        } // TODO: Create SF_Meta_Boxes class
        
        if (file_exists(SF_CORE_DIR . 'includes/admin/class-sf-facility-dashboard.php')) {
            require_once SF_CORE_DIR . 'includes/admin/class-sf-facility-dashboard.php';
        } // TODO: Create SF_Facility_Dashboard class
        
        if (file_exists(SF_CORE_DIR . 'includes/admin/class-sf-unit-layout.php')) {
            require_once SF_CORE_DIR . 'includes/admin/class-sf-unit-layout.php';
        } // TODO: Create SF_Unit_Layout class
        
        if (file_exists(SF_CORE_DIR . 'includes/admin/class-sf-billing-admin.php')) {
            require_once SF_CORE_DIR . 'includes/admin/class-sf-billing-admin.php';
        } // TODO: Create SF_Billing_Admin class
        
        if (file_exists(SF_CORE_DIR . 'includes/admin/class-sf-reports.php')) {
            require_once SF_CORE_DIR . 'includes/admin/class-sf-reports.php';
        } // TODO: Create SF_Reports class
        
        // Public functionality
        if (file_exists(SF_CORE_DIR . 'includes/public/class-sf-public.php')) {
            require_once SF_CORE_DIR . 'includes/public/class-sf-public.php';
        } // TODO: Create SF_Public class
        
        if (file_exists(SF_CORE_DIR . 'includes/public/class-sf-rental-portal.php')) {
            require_once SF_CORE_DIR . 'includes/public/class-sf-rental-portal.php';
        } // TODO: Create SF_Rental_Portal class
        
        if (file_exists(SF_CORE_DIR . 'includes/public/class-sf-tenant-portal.php')) {
            require_once SF_CORE_DIR . 'includes/public/class-sf-tenant-portal.php';
        } // TODO: Create SF_Tenant_Portal class
        
        if (file_exists(SF_CORE_DIR . 'includes/public/class-sf-unit-directory.php')) {
            require_once SF_CORE_DIR . 'includes/public/class-sf-unit-directory.php';
        } // TODO: Create SF_Unit_Directory class
        
        if (file_exists(SF_CORE_DIR . 'includes/public/class-sf-payment-gateway.php')) {
            require_once SF_CORE_DIR . 'includes/public/class-sf-payment-gateway.php';
        } // TODO: Create SF_Payment_Gateway class
        
        // API functionality
        if (file_exists(SF_CORE_DIR . 'includes/api/class-sf-rest-api.php')) {
            require_once SF_CORE_DIR . 'includes/api/class-sf-rest-api.php';
        } // TODO: Create SF_REST_API class
        
        if (file_exists(SF_CORE_DIR . 'includes/api/class-sf-rental-api.php')) {
            require_once SF_CORE_DIR . 'includes/api/class-sf-rental-api.php';
        } // TODO: Create SF_Rental_API class
        
        if (file_exists(SF_CORE_DIR . 'includes/api/class-sf-access-api.php')) {
            require_once SF_CORE_DIR . 'includes/api/class-sf-access-api.php';
        } // TODO: Create SF_Access_API class
    }
    
    private function set_locale() {
        add_action('plugins_loaded', function() {
            load_plugin_textdomain(
                'storageflow',
                false,
                dirname(SF_PLUGIN_BASE) . '/core/languages/'
            );
        });
    }
    
    private function define_admin_hooks() {
        if (class_exists('SF_Post_Types')) {
            SF_Post_Types::init();
        } // TODO: Create SF_Post_Types class
        
        if (class_exists('SF_Capabilities')) {
            SF_Capabilities::init();
        } // TODO: Create SF_Capabilities class
        
        if (class_exists('SF_Admin')) {
            $admin = new SF_Admin();
            add_action('admin_enqueue_scripts', array($admin, 'enqueue_styles'));
            add_action('admin_enqueue_scripts', array($admin, 'enqueue_scripts'));
            add_action('admin_menu', array($admin, 'add_admin_menus'));
        } // TODO: Create SF_Admin class
        
        // Storage facility dashboard and management
        if (class_exists('SF_Facility_Dashboard')) {
            new SF_Facility_Dashboard();
        } // TODO: Create SF_Facility_Dashboard class
        
        if (class_exists('SF_Unit_Layout')) {
            new SF_Unit_Layout();
        } // TODO: Create SF_Unit_Layout class
        
        if (class_exists('SF_Billing_Admin')) {
            new SF_Billing_Admin();
        } // TODO: Create SF_Billing_Admin class
        
        if (class_exists('SF_Meta_Boxes')) {
            add_action('admin_init', array('SF_Meta_Boxes', 'init'));
        } // TODO: Create SF_Meta_Boxes class
        
        // AJAX handlers for admin
        if (class_exists('SF_Unit_Manager')) {
            add_action('wp_ajax_sf_update_unit_status', array('SF_Unit_Manager', 'ajax_update_unit_status'));
            add_action('wp_ajax_sf_assign_tenant', array('SF_Unit_Manager', 'ajax_assign_tenant'));
        } // TODO: Create SF_Unit_Manager class
        
        if (class_exists('SF_Rental_System')) {
            add_action('wp_ajax_sf_process_rental', array('SF_Rental_System', 'ajax_process_rental'));
        } // TODO: Create SF_Rental_System class
        
        if (class_exists('SF_Billing_Engine')) {
            add_action('wp_ajax_sf_generate_invoice', array('SF_Billing_Engine', 'ajax_generate_invoice'));
        } // TODO: Create SF_Billing_Engine class
        
        if (class_exists('SF_Access_Control')) {
            add_action('wp_ajax_sf_update_access_code', array('SF_Access_Control', 'ajax_update_access_code'));
        } // TODO: Create SF_Access_Control class
        
        if (class_exists('SF_Facility_Operations')) {
            add_action('wp_ajax_sf_facility_status_update', array('SF_Facility_Operations', 'ajax_status_update'));
        } // TODO: Create SF_Facility_Operations class
    }
    
    private function define_public_hooks() {
        if (class_exists('SF_Public')) {
            $public = new SF_Public();
            add_action('wp_enqueue_scripts', array($public, 'enqueue_styles'));
            add_action('wp_enqueue_scripts', array($public, 'enqueue_scripts'));
        } // TODO: Create SF_Public class
        
        // Public storage interfaces
        if (class_exists('SF_Rental_Portal')) {
            new SF_Rental_Portal();
        } // TODO: Create SF_Rental_Portal class
        
        if (class_exists('SF_Tenant_Portal')) {
            new SF_Tenant_Portal();
        } // TODO: Create SF_Tenant_Portal class
        
        if (class_exists('SF_Unit_Directory')) {
            new SF_Unit_Directory();
        } // TODO: Create SF_Unit_Directory class
        
        if (class_exists('SF_Payment_Gateway')) {
            new SF_Payment_Gateway();
        } // TODO: Create SF_Payment_Gateway class
        
        // Public AJAX handlers
        if (class_exists('SF_Rental_System')) {
            add_action('wp_ajax_sf_submit_rental', array('SF_Rental_System', 'ajax_submit_rental'));
            add_action('wp_ajax_nopriv_sf_submit_rental', array('SF_Rental_System', 'ajax_submit_rental'));
        } // TODO: Create SF_Rental_System class
        
        if (class_exists('SF_Unit_Manager')) {
            add_action('wp_ajax_sf_check_unit_availability', array('SF_Unit_Manager', 'ajax_check_availability'));
            add_action('wp_ajax_nopriv_sf_check_unit_availability', array('SF_Unit_Manager', 'ajax_check_availability'));
        } // TODO: Create SF_Unit_Manager class
        
        if (class_exists('SF_Payment_Gateway')) {
            add_action('wp_ajax_sf_process_payment', array('SF_Payment_Gateway', 'ajax_process_payment'));
            add_action('wp_ajax_nopriv_sf_process_payment', array('SF_Payment_Gateway', 'ajax_process_payment'));
        } // TODO: Create SF_Payment_Gateway class
        
        // Shortcodes
        if (class_exists('SF_Unit_Directory')) {
            add_shortcode('storage_unit_availability', array('SF_Unit_Directory', 'render_unit_availability'));
            add_shortcode('storage_unit_directory', array('SF_Unit_Directory', 'render_unit_directory'));
            add_shortcode('storage_facility_map', array('SF_Unit_Directory', 'render_facility_map'));
        } // TODO: Create SF_Unit_Directory class
        
        if (class_exists('SF_Rental_Portal')) {
            add_shortcode('storage_rental_form', array('SF_Rental_Portal', 'render_rental_form'));
        } // TODO: Create SF_Rental_Portal class
        
        if (class_exists('SF_Tenant_Portal')) {
            add_shortcode('storage_tenant_portal', array('SF_Tenant_Portal', 'render_tenant_portal'));
        } // TODO: Create SF_Tenant_Portal class
        
        if (class_exists('SF_Payment_Gateway')) {
            add_shortcode('storage_payment_form', array('SF_Payment_Gateway', 'render_payment_form'));
        } // TODO: Create SF_Payment_Gateway class
    }
    
    private function define_api_hooks() {
        if (class_exists('SF_REST_API')) {
            add_action('rest_api_init', array('SF_REST_API', 'register_routes'));
        } // TODO: Create SF_REST_API class
        
        if (class_exists('SF_Rental_API')) {
            add_action('rest_api_init', array('SF_Rental_API', 'register_routes'));
        } // TODO: Create SF_Rental_API class
        
        if (class_exists('SF_Access_API')) {
            add_action('rest_api_init', array('SF_Access_API', 'register_routes'));
        } // TODO: Create SF_Access_API class
    }
    
    private function init_cron_jobs() {
        // Schedule billing cycles
        if (!wp_next_scheduled('sf_process_monthly_billing')) {
            wp_schedule_event(time(), 'monthly', 'sf_process_monthly_billing');
        }
        
        // Schedule rent reminders
        if (!wp_next_scheduled('sf_send_rent_reminders')) {
            wp_schedule_event(time(), 'daily', 'sf_send_rent_reminders');
        }
        
        // Schedule late fee processing
        if (!wp_next_scheduled('sf_process_late_fees')) {
            wp_schedule_event(time(), 'daily', 'sf_process_late_fees');
        }
        
        // Schedule facility maintenance alerts
        if (!wp_next_scheduled('sf_maintenance_alerts')) {
            wp_schedule_event(time(), 'daily', 'sf_maintenance_alerts');
        }
        
        // Schedule access code updates
        if (!wp_next_scheduled('sf_update_access_codes')) {
            wp_schedule_event(time(), 'hourly', 'sf_update_access_codes');
        }
        
        // Schedule move-out processing
        if (!wp_next_scheduled('sf_process_moveouts')) {
            wp_schedule_event(time(), 'daily', 'sf_process_moveouts');
        }
        
        if (class_exists('SF_Billing_Engine')) {
            add_action('sf_process_monthly_billing', array('SF_Billing_Engine', 'process_monthly_billing'));
            add_action('sf_process_late_fees', array('SF_Billing_Engine', 'process_late_fees'));
        } // TODO: Create SF_Billing_Engine class
        
        if (class_exists('SF_Notification_System')) {
            add_action('sf_send_rent_reminders', array('SF_Notification_System', 'send_rent_reminders'));
        } // TODO: Create SF_Notification_System class
        
        if (class_exists('SF_Facility_Operations')) {
            add_action('sf_maintenance_alerts', array('SF_Facility_Operations', 'send_maintenance_alerts'));
        } // TODO: Create SF_Facility_Operations class
        
        if (class_exists('SF_Access_Control')) {
            add_action('sf_update_access_codes', array('SF_Access_Control', 'update_access_codes'));
        } // TODO: Create SF_Access_Control class
        
        if (class_exists('SF_Tenant_Manager')) {
            add_action('sf_process_moveouts', array('SF_Tenant_Manager', 'process_scheduled_moveouts'));
        } // TODO: Create SF_Tenant_Manager class
    }
    
    private function init_storage_operations() {
        // Initialize facility operations monitoring
        if (class_exists('SF_Facility_Operations')) {
            SF_Facility_Operations::init();
        } // TODO: Create SF_Facility_Operations class
        
        // Initialize access control system
        if (class_exists('SF_Access_Control')) {
            SF_Access_Control::init();
        } // TODO: Create SF_Access_Control class
        
        // Initialize billing engine
        if (class_exists('SF_Billing_Engine')) {
            SF_Billing_Engine::init();
        } // TODO: Create SF_Billing_Engine class
        
        // Initialize unit management system
        if (class_exists('SF_Unit_Manager')) {
            SF_Unit_Manager::init();
        } // TODO: Create SF_Unit_Manager class
    }
    
    public function run() {
        // Plugin ready
    }
}

/**
 * Plugin activation
 */
function sf_activate() {
    if (file_exists(SF_CORE_DIR . 'includes/class-sf-activator.php')) {
        require_once SF_CORE_DIR . 'includes/class-sf-activator.php';
        if (class_exists('SF_Activator')) {
            SF_Activator::activate();
        }
    } // TODO: Create SF_Activator class
}
register_activation_hook(__FILE__, 'sf_activate');

/**
 * Plugin deactivation
 */
function sf_deactivate() {
    if (file_exists(SF_CORE_DIR . 'includes/class-sf-deactivator.php')) {
        require_once SF_CORE_DIR . 'includes/class-sf-deactivator.php';
        if (class_exists('SF_Deactivator')) {
            SF_Deactivator::deactivate();
        }
    } // TODO: Create SF_Deactivator class
}
register_deactivation_hook(__FILE__, 'sf_deactivate');

/**
 * Initialize plugin
 */
function run_storageflow() {
    $plugin = StorageFlow::get_instance();
    $plugin->run();
}
run_storageflow();