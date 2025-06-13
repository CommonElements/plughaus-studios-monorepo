<?php
/**
 * The admin-specific functionality of the plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 */
class ERP_Admin {

    /**
     * Initialize the class and set its properties.
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menus'));
        add_action('admin_init', array($this, 'admin_init'));
    }

    /**
     * Register the stylesheets for the admin area.
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            'equiprent-pro-admin',
            ERP_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            ERP_VERSION,
            'all'
        );

        // Enqueue Chart.js for analytics (Pro feature)
        if (current_user_can('view_analytics')) {
            wp_enqueue_style(
                'equiprent-pro-charts',
                'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css',
                array(),
                '3.9.1',
                'all'
            );
        }
    }

    /**
     * Register the JavaScript for the admin area.
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            'equiprent-pro-admin',
            ERP_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            ERP_VERSION,
            false
        );

        // Enqueue Chart.js for analytics (Pro feature)
        if (current_user_can('view_analytics')) {
            wp_enqueue_script(
                'chartjs',
                'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js',
                array(),
                '3.9.1',
                false
            );
        }

        // Localize script for AJAX
        wp_localize_script('equiprent-pro-admin', 'erp_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('erp_admin_nonce'),
        ));
    }

    /**
     * Add admin menus
     */
    public function add_admin_menus() {
        // Main menu
        add_menu_page(
            __('EquipRent Pro', 'equiprent-pro'),
            __('EquipRent Pro', 'equiprent-pro'),
            'view_equipment',
            'equiprent-pro',
            array($this, 'dashboard_page'),
            'dashicons-hammer',
            25
        );

        // Dashboard submenu
        add_submenu_page(
            'equiprent-pro',
            __('Dashboard', 'equiprent-pro'),
            __('Dashboard', 'equiprent-pro'),
            'view_equipment',
            'equiprent-pro',
            array($this, 'dashboard_page')
        );

        // Equipment submenu
        add_submenu_page(
            'equiprent-pro',
            __('Equipment', 'equiprent-pro'),
            __('Equipment', 'equiprent-pro'),
            'view_equipment',
            'edit.php?post_type=erp_equipment'
        );

        // Add new equipment
        add_submenu_page(
            'equiprent-pro',
            __('Add Equipment', 'equiprent-pro'),
            __('Add Equipment', 'equiprent-pro'),
            'manage_equipment',
            'post-new.php?post_type=erp_equipment'
        );

        // Bookings submenu
        add_submenu_page(
            'equiprent-pro',
            __('Bookings', 'equiprent-pro'),
            __('Bookings', 'equiprent-pro'),
            'view_bookings',
            'equiprent-bookings',
            array($this, 'bookings_page')
        );

        // Add new booking
        add_submenu_page(
            'equiprent-pro',
            __('Add Booking', 'equiprent-pro'),
            __('Add Booking', 'equiprent-pro'),
            'manage_bookings',
            'equiprent-add-booking',
            array($this, 'add_booking_page')
        );

        // Customers submenu
        add_submenu_page(
            'equiprent-pro',
            __('Customers', 'equiprent-pro'),
            __('Customers', 'equiprent-pro'),
            'view_customers',
            'equiprent-customers',
            array($this, 'customers_page')
        );

        // Maintenance submenu
        add_submenu_page(
            'equiprent-pro',
            __('Maintenance', 'equiprent-pro'),
            __('Maintenance', 'equiprent-pro'),
            'view_maintenance',
            'equiprent-maintenance',
            array($this, 'maintenance_page')
        );

        // Reports submenu
        add_submenu_page(
            'equiprent-pro',
            __('Reports', 'equiprent-pro'),
            __('Reports', 'equiprent-pro'),
            'view_reports',
            'equiprent-reports',
            array($this, 'reports_page')
        );

        // Settings submenu
        add_submenu_page(
            'equiprent-pro',
            __('Settings', 'equiprent-pro'),
            __('Settings', 'equiprent-pro'),
            'manage_erp_settings',
            'equiprent-settings',
            array($this, 'settings_page')
        );

        // Pro features (only if licensed)
        if (EquipRent_Pro::get_instance()->is_pro()) {
            // Analytics submenu
            add_submenu_page(
                'equiprent-pro',
                __('Analytics', 'equiprent-pro'),
                __('Analytics', 'equiprent-pro'),
                'view_analytics',
                'equiprent-analytics',
                array($this, 'analytics_page')
            );

            // Route Management submenu
            add_submenu_page(
                'equiprent-pro',
                __('Route Management', 'equiprent-pro'),
                __('Routes', 'equiprent-pro'),
                'manage_routes',
                'equiprent-routes',
                array($this, 'routes_page')
            );
        }
    }

    /**
     * Admin initialization
     */
    public function admin_init() {
        // Register settings
        register_setting('erp_settings', 'erp_general_settings');
        register_setting('erp_settings', 'erp_booking_settings');
        register_setting('erp_settings', 'erp_pricing_settings');
        register_setting('erp_settings', 'erp_email_settings');
    }

    /**
     * Dashboard page
     */
    public function dashboard_page() {
        include ERP_CORE_DIR . 'admin/templates/dashboard.php';
    }

    /**
     * Bookings page
     */
    public function bookings_page() {
        include ERP_CORE_DIR . 'admin/templates/bookings.php';
    }

    /**
     * Add booking page
     */
    public function add_booking_page() {
        include ERP_CORE_DIR . 'admin/templates/add-booking.php';
    }

    /**
     * Customers page
     */
    public function customers_page() {
        include ERP_CORE_DIR . 'admin/templates/customers.php';
    }

    /**
     * Maintenance page
     */
    public function maintenance_page() {
        include ERP_CORE_DIR . 'admin/templates/maintenance.php';
    }

    /**
     * Reports page
     */
    public function reports_page() {
        include ERP_CORE_DIR . 'admin/templates/reports.php';
    }

    /**
     * Settings page
     */
    public function settings_page() {
        include ERP_CORE_DIR . 'admin/templates/settings.php';
    }

    /**
     * Analytics page (Pro)
     */
    public function analytics_page() {
        if (EquipRent_Pro::get_instance()->is_pro()) {
            include ERP_PRO_DIR . 'admin/templates/analytics.php';
        } else {
            wp_die(__('This feature requires a Pro license.', 'equiprent-pro'));
        }
    }

    /**
     * Routes page (Pro)
     */
    public function routes_page() {
        if (EquipRent_Pro::get_instance()->is_pro()) {
            include ERP_PRO_DIR . 'admin/templates/routes.php';
        } else {
            wp_die(__('This feature requires a Pro license.', 'equiprent-pro'));
        }
    }
}