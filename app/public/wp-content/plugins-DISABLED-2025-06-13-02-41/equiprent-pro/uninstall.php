<?php
/**
 * Fired when the plugin is uninstalled.
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

/**
 * Uninstall EquipRent Pro
 * 
 * This will remove all plugin data if the user chooses to delete all data
 * during uninstallation.
 */

// Check if user wants to delete all data
$delete_data = get_option('erp_delete_data_on_uninstall', false);

if ($delete_data) {
    global $wpdb;

    // Remove all custom post types
    $post_types = array('erp_equipment', 'erp_booking', 'erp_customer');
    
    foreach ($post_types as $post_type) {
        $posts = get_posts(array(
            'post_type' => $post_type,
            'post_status' => 'any',
            'numberposts' => -1,
            'fields' => 'ids'
        ));

        foreach ($posts as $post_id) {
            wp_delete_post($post_id, true);
        }
    }

    // Remove custom taxonomies
    $taxonomies = array('equipment_category', 'equipment_tag');
    
    foreach ($taxonomies as $taxonomy) {
        $terms = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
            'fields' => 'ids'
        ));

        foreach ($terms as $term_id) {
            wp_delete_term($term_id, $taxonomy);
        }
    }

    // Remove custom database tables
    $tables = array(
        $wpdb->prefix . 'erp_equipment',
        $wpdb->prefix . 'erp_customers',
        $wpdb->prefix . 'erp_bookings',
        $wpdb->prefix . 'erp_booking_items',
        $wpdb->prefix . 'erp_maintenance',
        $wpdb->prefix . 'erp_delivery_routes',
        $wpdb->prefix . 'erp_route_stops'
    );

    foreach ($tables as $table) {
        $wpdb->query("DROP TABLE IF EXISTS $table");
    }

    // Remove plugin options
    $options = array(
        'erp_general_settings',
        'erp_booking_settings',
        'erp_pricing_settings',
        'erp_email_settings',
        'erp_capabilities_added',
        'erp_version',
        'erp_db_version',
        'erp_delete_data_on_uninstall'
    );

    foreach ($options as $option) {
        delete_option($option);
    }

    // Remove user meta
    $wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE 'erp_%'");

    // Remove custom capabilities
    global $wp_roles;
    if (class_exists('WP_Roles') && !isset($wp_roles)) {
        $wp_roles = new WP_Roles();
    }

    $capabilities = array(
        'manage_equipment',
        'edit_equipment',
        'delete_equipment',
        'view_equipment',
        'manage_bookings',
        'edit_bookings',
        'delete_bookings',
        'view_bookings',
        'manage_customers',
        'edit_customers',
        'delete_customers',
        'view_customers',
        'manage_maintenance',
        'edit_maintenance',
        'delete_maintenance',
        'view_maintenance',
        'view_reports',
        'export_reports',
        'view_analytics',
        'manage_analytics',
        'manage_routes',
        'edit_routes',
        'view_routes',
        'manage_erp_settings',
        'view_erp_settings'
    );

    foreach ($wp_roles->roles as $role_name => $role_info) {
        $role = get_role($role_name);
        if ($role) {
            foreach ($capabilities as $cap) {
                $role->remove_cap($cap);
            }
        }
    }

    // Remove custom roles
    remove_role('equipment_manager');
    remove_role('rental_agent');

    // Clear scheduled events
    wp_clear_scheduled_hook('erp_daily_maintenance_check');
    wp_clear_scheduled_hook('erp_weekly_availability_update');

    // Clear any cached data
    wp_cache_flush();
}