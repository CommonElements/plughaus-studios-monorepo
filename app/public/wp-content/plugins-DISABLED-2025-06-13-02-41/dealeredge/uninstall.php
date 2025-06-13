<?php
/**
 * DealerEdge Uninstall
 * 
 * This file is called when the plugin is uninstalled
 */

// If uninstall not called from WordPress, then exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Only proceed if user has proper permissions
if (!current_user_can('activate_plugins')) {
    exit;
}

// Delete plugin options
delete_option('dealeredge_business_name');
delete_option('dealeredge_business_type');
delete_option('dealeredge_currency');
delete_option('dealeredge_tax_rate');
delete_option('dealeredge_labor_rate');
delete_option('dealeredge_work_order_prefix');
delete_option('dealeredge_work_order_start_number');
delete_option('dealeredge_auto_assign_wo_numbers');
delete_option('dealeredge_sale_prefix');
delete_option('dealeredge_sale_start_number');
delete_option('dealeredge_auto_assign_sale_numbers');
delete_option('dealeredge_low_stock_threshold');
delete_option('dealeredge_track_parts_inventory');
delete_option('dealeredge_auto_deduct_parts');
delete_option('dealeredge_email_notifications');
delete_option('dealeredge_admin_email');
delete_option('dealeredge_version');
delete_option('dealeredge_db_version');

// Remove user capabilities
$roles = array('administrator', 'editor');
$capabilities = array(
    'manage_dealeredge',
    'view_dealeredge_dashboard',
    'edit_dealeredge_settings',
    'edit_de_vehicles',
    'edit_others_de_vehicles',
    'publish_de_vehicles',
    'read_private_de_vehicles',
    'delete_de_vehicles',
    'delete_private_de_vehicles',
    'delete_published_de_vehicles',
    'delete_others_de_vehicles',
    'edit_private_de_vehicles',
    'edit_published_de_vehicles',
    'edit_de_customers',
    'edit_others_de_customers',
    'publish_de_customers',
    'read_private_de_customers',
    'delete_de_customers',
    'delete_private_de_customers',
    'delete_published_de_customers',
    'delete_others_de_customers',
    'edit_private_de_customers',
    'edit_published_de_customers',
    'edit_de_work_orders',
    'edit_others_de_work_orders',
    'publish_de_work_orders',
    'read_private_de_work_orders',
    'delete_de_work_orders',
    'delete_private_de_work_orders',
    'delete_published_de_work_orders',
    'delete_others_de_work_orders',
    'edit_private_de_work_orders',
    'edit_published_de_work_orders',
    'edit_de_sales',
    'edit_others_de_sales',
    'publish_de_sales',
    'read_private_de_sales',
    'delete_de_sales',
    'delete_private_de_sales',
    'delete_published_de_sales',
    'delete_others_de_sales',
    'edit_private_de_sales',
    'edit_published_de_sales',
    'edit_de_parts',
    'edit_others_de_parts',
    'publish_de_parts',
    'read_private_de_parts',
    'delete_de_parts',
    'delete_private_de_parts',
    'delete_published_de_parts',
    'delete_others_de_parts',
    'edit_private_de_parts',
    'edit_published_de_parts',
);

foreach ($roles as $role_name) {
    $role = get_role($role_name);
    if ($role) {
        foreach ($capabilities as $cap) {
            $role->remove_cap($cap);
        }
    }
}

// Clear any transients
delete_transient('dealeredge_activation_notice');
delete_transient('dealeredge_dashboard_stats');

// Uncomment the following lines if you want to delete all plugin data on uninstall
// WARNING: This will permanently delete ALL DealerEdge data!

/*
global $wpdb;

// Delete all posts of DealerEdge post types
$post_types = array('de_vehicle', 'de_customer', 'de_work_order', 'de_sale', 'de_part');
foreach ($post_types as $post_type) {
    $posts = get_posts(array(
        'post_type' => $post_type,
        'posts_per_page' => -1,
        'post_status' => 'any'
    ));
    
    foreach ($posts as $post) {
        wp_delete_post($post->ID, true);
    }
}

// Delete all plugin custom taxonomies and terms
$taxonomies = array('de_vehicle_make', 'de_vehicle_model', 'de_service_category', 'de_part_category');
foreach ($taxonomies as $taxonomy) {
    $terms = get_terms(array(
        'taxonomy' => $taxonomy,
        'hide_empty' => false
    ));
    
    foreach ($terms as $term) {
        wp_delete_term($term->term_id, $taxonomy);
    }
}

// Delete custom database tables
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}de_vehicle_history");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}de_parts_usage");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}de_customer_vehicles");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}de_service_templates");

// Delete all plugin meta data
$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_de_%'");
*/