<?php
/**
 * Plugin Activator for Knot4
 * 
 * @package Knot4
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Knot4_Activator {
    
    /**
     * Activate the plugin
     */
    public static function activate() {
        // Create database tables
        self::create_database_tables();
        
        // Set default options
        self::set_default_options();
        
        // Create capabilities
        self::create_capabilities();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Set activation timestamp
        update_option('knot4_activated_at', current_time('mysql'));
        
        // Log activation
        error_log('Knot4 Plugin Activated Successfully');
    }
    
    /**
     * Create database tables
     */
    private static function create_database_tables() {
        if (class_exists('Knot4_Database')) {
            Knot4_Database::create_tables();
        }
    }
    
    /**
     * Set default plugin options
     */
    private static function set_default_options() {
        $defaults = array(
            'knot4_version' => KNOT4_VERSION,
            'knot4_organization_settings' => array(
                'organization_name' => get_bloginfo('name'),
                'organization_email' => get_option('admin_email'),
                'currency' => 'USD',
                'date_format' => get_option('date_format'),
                'time_format' => get_option('time_format'),
            ),
            'knot4_payment_settings' => array(
                'enabled_gateways' => array('stripe'),
                'test_mode' => true,
                'stripe_test_publishable_key' => '',
                'stripe_test_secret_key' => '',
                'stripe_live_publishable_key' => '',
                'stripe_live_secret_key' => '',
            ),
            'knot4_email_settings' => array(
                'from_name' => get_bloginfo('name'),
                'from_email' => get_option('admin_email'),
                'reply_to' => get_option('admin_email'),
            ),
        );
        
        foreach ($defaults as $option_name => $option_value) {
            if (!get_option($option_name)) {
                add_option($option_name, $option_value);
            }
        }
    }
    
    /**
     * Create user capabilities
     */
    private static function create_capabilities() {
        if (class_exists('Knot4_Capabilities')) {
            Knot4_Capabilities::add_capabilities();
        }
    }
}