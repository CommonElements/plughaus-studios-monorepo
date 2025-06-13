<?php
/**
 * Frontend settings admin for EquipRent Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Frontend settings admin class
 */
class ERP_Frontend_Settings_Admin {

    /**
     * Initialize frontend settings
     */
    public static function init() {
        add_action('admin_init', array(__CLASS__, 'setup_frontend_settings'));
    }

    /**
     * Setup frontend settings
     */
    public static function setup_frontend_settings() {
        // Add settings sections and fields
        if (function_exists('add_settings_section')) {
            add_settings_section(
                'erp_frontend_settings',
                __('Frontend Settings', 'equiprent-pro'),
                array(__CLASS__, 'frontend_settings_callback'),
                'equiprent-settings'
            );
        }

        if (function_exists('add_settings_field')) {
            add_settings_field(
                'erp_booking_page',
                __('Booking Page', 'equiprent-pro'),
                array(__CLASS__, 'booking_page_callback'),
                'equiprent-settings',
                'erp_frontend_settings'
            );

            add_settings_field(
                'erp_equipment_page',
                __('Equipment Page', 'equiprent-pro'),
                array(__CLASS__, 'equipment_page_callback'),
                'equiprent-settings',
                'erp_frontend_settings'
            );
        }
    }

    /**
     * Frontend settings section callback
     */
    public static function frontend_settings_callback() {
        echo '<p>' . __('Configure frontend display settings for EquipRent Pro.', 'equiprent-pro') . '</p>';
    }

    /**
     * Booking page callback
     */
    public static function booking_page_callback() {
        $page_id = self::get_setting('booking_page', '');
        
        if (function_exists('wp_dropdown_pages')) {
            wp_dropdown_pages(array(
                'name' => 'erp_settings[booking_page]',
                'selected' => $page_id,
                'show_option_none' => __('Select a page...', 'equiprent-pro')
            ));
        } else {
            echo '<input type="text" name="erp_settings[booking_page]" value="' . esc_attr($page_id) . '" class="regular-text" />';
        }
        
        echo '<p class="description">' . __('Select the page where the booking form will be displayed.', 'equiprent-pro') . '</p>';
    }

    /**
     * Equipment page callback
     */
    public static function equipment_page_callback() {
        $page_id = self::get_setting('equipment_page', '');
        
        if (function_exists('wp_dropdown_pages')) {
            wp_dropdown_pages(array(
                'name' => 'erp_settings[equipment_page]',
                'selected' => $page_id,
                'show_option_none' => __('Select a page...', 'equiprent-pro')
            ));
        } else {
            echo '<input type="text" name="erp_settings[equipment_page]" value="' . esc_attr($page_id) . '" class="regular-text" />';
        }
        
        echo '<p class="description">' . __('Select the page where equipment will be displayed.', 'equiprent-pro') . '</p>';
    }

    /**
     * Get setting value
     */
    private static function get_setting($key, $default = '') {
        if (class_exists('ERP_Admin_Settings')) {
            return ERP_Admin_Settings::get_setting($key, $default);
        }
        
        if (function_exists('get_option')) {
            $settings = get_option('erp_general_settings', array());
            return isset($settings[$key]) ? $settings[$key] : $default;
        }
        
        return $default;
    }
}