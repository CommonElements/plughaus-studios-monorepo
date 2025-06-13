<?php
/**
 * Admin menu management for EquipRent Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin menus class
 */
class ERP_Admin_Menus {

    /**
     * Initialize admin menus
     */
    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_admin_menus'));
    }

    /**
     * Add admin menus
     */
    public static function add_admin_menus() {
        // Main menu
        add_menu_page(
            __('EquipRent Pro', 'equiprent-pro'),
            __('EquipRent Pro', 'equiprent-pro'),
            'view_equipment',
            'equiprent-pro',
            array(__CLASS__, 'dashboard_page'),
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
            array(__CLASS__, 'dashboard_page')
        );

        // Equipment submenu
        add_submenu_page(
            'equiprent-pro',
            __('Equipment', 'equiprent-pro'),
            __('Equipment', 'equiprent-pro'),
            'view_equipment',
            'edit.php?post_type=erp_equipment'
        );

        // Bookings submenu
        add_submenu_page(
            'equiprent-pro',
            __('Bookings', 'equiprent-pro'),
            __('Bookings', 'equiprent-pro'),
            'view_bookings',
            'equiprent-bookings',
            array(__CLASS__, 'bookings_page')
        );

        // Customers submenu
        add_submenu_page(
            'equiprent-pro',
            __('Customers', 'equiprent-pro'),
            __('Customers', 'equiprent-pro'),
            'view_customers',
            'equiprent-customers',
            array(__CLASS__, 'customers_page')
        );

        // Settings submenu
        add_submenu_page(
            'equiprent-pro',
            __('Settings', 'equiprent-pro'),
            __('Settings', 'equiprent-pro'),
            'manage_erp_settings',
            'equiprent-settings',
            array(__CLASS__, 'settings_page')
        );
    }

    /**
     * Dashboard page callback
     */
    public static function dashboard_page() {
        if (file_exists(ERP_CORE_DIR . 'admin/templates/dashboard.php')) {
            include ERP_CORE_DIR . 'admin/templates/dashboard.php';
        } else {
            echo '<div class="wrap"><h1>EquipRent Pro Dashboard</h1><p>Dashboard template not found.</p></div>';
        }
    }

    /**
     * Bookings page callback
     */
    public static function bookings_page() {
        echo '<div class="wrap"><h1>Bookings</h1><p>Bookings management coming soon.</p></div>';
    }

    /**
     * Customers page callback
     */
    public static function customers_page() {
        echo '<div class="wrap"><h1>Customers</h1><p>Customer management coming soon.</p></div>';
    }

    /**
     * Settings page callback
     */
    public static function settings_page() {
        echo '<div class="wrap"><h1>Settings</h1><p>Settings management coming soon.</p></div>';
    }
}