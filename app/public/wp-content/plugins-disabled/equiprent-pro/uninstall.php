<?php
/**
 * EquipRent Pro Uninstall
 *
 * @package EquipRent_Pro
 */

// Exit if uninstall not called from WordPress
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

/**
 * Clean up plugin data on uninstall
 * Only runs when plugin is deleted, not deactivated
 */

global $wpdb;

// Get all plugin options
$plugin_options = array(
    'equiprent_currency',
    'equiprent_currency_symbol', 
    'equiprent_currency_position',
    'equiprent_date_format',
    'equiprent_time_format',
    'equiprent_timezone',
    'equiprent_business_name',
    'equiprent_business_email',
    'equiprent_booking_prefix',
    'equiprent_default_rental_period',
    'equiprent_require_deposit',
    'equiprent_default_deposit_percentage',
    'equiprent_late_fee_enabled',
    'equiprent_late_fee_amount',
    'equiprent_late_fee_type',
    'equiprent_tax_enabled',
    'equiprent_tax_rate',
    'equiprent_inventory_tracking',
    'equiprent_low_stock_threshold',
    'equiprent_email_notifications',
    'equiprent_sms_notifications',
    'equiprent_activated',
    'equiprent_activation_date',
    'equiprent_deactivated',
    'equiprent_deactivation_date',
    'equiprent_setup_completed',
    'equiprent_last_booking_number',
    'equiprent_page_equipment_catalog',
    'equiprent_page_booking_form',
    'equiprent_page_my_bookings'
);

// Only delete data if user confirms
$delete_data = get_option('equiprent_delete_data_on_uninstall', false);

if ($delete_data) {
    // Delete plugin options
    foreach ($plugin_options as $option) {
        delete_option($option);
    }

    // Delete custom post types and their meta
    $post_types = array('equipment', 'er_booking', 'er_customer', 'er_maintenance');
    
    foreach ($post_types as $post_type) {
        $posts = get_posts(array(
            'post_type' => $post_type,
            'numberposts' => -1,
            'post_status' => 'any'
        ));
        
        foreach ($posts as $post) {
            wp_delete_post($post->ID, true);
        }
    }

    // Delete custom taxonomies
    $taxonomies = array(
        'equipment_category',
        'equipment_brand', 
        'equipment_condition',
        'equipment_location',
        'booking_status'
    );
    
    foreach ($taxonomies as $taxonomy) {
        $terms = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => false
        ));
        
        if (!is_wp_error($terms)) {
            foreach ($terms as $term) {
                wp_delete_term($term->term_id, $taxonomy);
            }
        }
    }

    // Delete custom database tables
    $table_names = array(
        $wpdb->prefix . 'er_equipment',
        $wpdb->prefix . 'er_bookings',
        $wpdb->prefix . 'er_booking_items',
        $wpdb->prefix . 'er_customers',
        $wpdb->prefix . 'er_maintenance'
    );
    
    foreach ($table_names as $table_name) {
        $wpdb->query("DROP TABLE IF EXISTS {$table_name}");
    }

    // Remove user capabilities
    $roles = array('administrator', 'rental_manager', 'rental_staff');
    $capabilities = array(
        'manage_equipment',
        'edit_equipment', 
        'delete_equipment',
        'create_equipment',
        'manage_bookings',
        'edit_bookings',
        'delete_bookings', 
        'create_bookings',
        'manage_rental_customers',
        'edit_rental_customers',
        'delete_rental_customers',
        'manage_equiprent_settings',
        'view_equiprent_reports'
    );
    
    foreach ($roles as $role_name) {
        $role = get_role($role_name);
        if ($role) {
            foreach ($capabilities as $cap) {
                $role->remove_cap($cap);
            }
        }
    }

    // Remove custom roles
    remove_role('rental_manager');
    remove_role('rental_staff');

    // Clear any scheduled events
    wp_clear_scheduled_hook('equiprent_daily_maintenance_check');
    wp_clear_scheduled_hook('equiprent_hourly_booking_notifications');
    wp_clear_scheduled_hook('equiprent_weekly_reports');

    // Delete uploaded files (be careful with this)
    $upload_dir = wp_upload_dir();
    $equiprent_uploads = $upload_dir['basedir'] . '/equiprent-uploads/';
    
    if (is_dir($equiprent_uploads)) {
        // Only delete if directory exists and is specifically for our plugin
        $files = glob($equiprent_uploads . '*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        rmdir($equiprent_uploads);
    }
    
    // Flush rewrite rules
    flush_rewrite_rules();
}

// Always clean up transients (these are temporary anyway)
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_equiprent_%'");
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_equiprent_%'");