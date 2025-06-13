<?php
/**
 * Admin settings management for EquipRent Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin settings class
 */
class ERP_Admin_Settings {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_init', array($this, 'init_settings'));
    }

    /**
     * Initialize settings
     */
    public function init_settings() {
        // Register settings
        if (function_exists('register_setting')) {
            register_setting('erp_settings', 'erp_general_settings');
            register_setting('erp_settings', 'erp_booking_settings');
            register_setting('erp_settings', 'erp_pricing_settings');
            register_setting('erp_settings', 'erp_email_settings');
        }
    }

    /**
     * Get setting value
     */
    public static function get_setting($key, $default = '') {
        if (function_exists('get_option')) {
            $settings = get_option('erp_general_settings', array());
            return isset($settings[$key]) ? $settings[$key] : $default;
        }
        return $default;
    }

    /**
     * Update setting value
     */
    public static function update_setting($key, $value) {
        if (function_exists('get_option') && function_exists('update_option')) {
            $settings = get_option('erp_general_settings', array());
            $settings[$key] = $value;
            return update_option('erp_general_settings', $settings);
        }
        return false;
    }
}