<?php
/**
 * Custom Post Types for PlugHaus Property Management
 */

if (!defined('ABSPATH')) {
    exit;
}

class PHPM_Post_Types {
    
    /**
     * Initialize post types
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'register_post_types'));
        add_action('init', array(__CLASS__, 'register_post_statuses'));
    }
    
    /**
     * Register custom post types
     */
    public static function register_post_types() {
        // Properties Post Type
        register_post_type('phpm_property', array(
            'labels' => array(
                'name' => __('Properties', 'plughaus-property'),
                'singular_name' => __('Property', 'plughaus-property'),
                'add_new' => __('Add Property', 'plughaus-property'),
                'add_new_item' => __('Add New Property', 'plughaus-property'),
                'edit_item' => __('Edit Property', 'plughaus-property'),
                'new_item' => __('New Property', 'plughaus-property'),
                'view_item' => __('View Property', 'plughaus-property'),
                'search_items' => __('Search Properties', 'plughaus-property'),
                'not_found' => __('No properties found', 'plughaus-property'),
                'not_found_in_trash' => __('No properties found in trash', 'plughaus-property'),
                'menu_name' => __('Properties', 'plughaus-property'),
            ),
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => 'phpm-dashboard',
            'show_in_rest' => true,
            'capability_type' => 'phpm_property',
            'map_meta_cap' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'properties'),
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
            'menu_icon' => 'dashicons-building',
        ));
        
        // Units Post Type
        register_post_type('phpm_unit', array(
            'labels' => array(
                'name' => __('Units', 'plughaus-property'),
                'singular_name' => __('Unit', 'plughaus-property'),
                'add_new' => __('Add Unit', 'plughaus-property'),
                'add_new_item' => __('Add New Unit', 'plughaus-property'),
                'edit_item' => __('Edit Unit', 'plughaus-property'),
                'new_item' => __('New Unit', 'plughaus-property'),
                'view_item' => __('View Unit', 'plughaus-property'),
                'search_items' => __('Search Units', 'plughaus-property'),
                'not_found' => __('No units found', 'plughaus-property'),
                'not_found_in_trash' => __('No units found in trash', 'plughaus-property'),
                'menu_name' => __('Units', 'plughaus-property'),
            ),
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => 'phpm-dashboard',
            'show_in_rest' => true,
            'capability_type' => 'phpm_unit',
            'map_meta_cap' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'units'),
            'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
            'menu_icon' => 'dashicons-admin-home',
        ));
        
        // Tenants Post Type
        register_post_type('phpm_tenant', array(
            'labels' => array(
                'name' => __('Tenants', 'plughaus-property'),
                'singular_name' => __('Tenant', 'plughaus-property'),
                'add_new' => __('Add Tenant', 'plughaus-property'),
                'add_new_item' => __('Add New Tenant', 'plughaus-property'),
                'edit_item' => __('Edit Tenant', 'plughaus-property'),
                'new_item' => __('New Tenant', 'plughaus-property'),
                'view_item' => __('View Tenant', 'plughaus-property'),
                'search_items' => __('Search Tenants', 'plughaus-property'),
                'not_found' => __('No tenants found', 'plughaus-property'),
                'not_found_in_trash' => __('No tenants found in trash', 'plughaus-property'),
                'menu_name' => __('Tenants', 'plughaus-property'),
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => 'phpm-dashboard',
            'show_in_rest' => true,
            'capability_type' => 'phpm_tenant',
            'map_meta_cap' => true,
            'has_archive' => false,
            'rewrite' => false,
            'supports' => array('title', 'custom-fields'),
            'menu_icon' => 'dashicons-groups',
        ));
        
        // Leases Post Type
        register_post_type('phpm_lease', array(
            'labels' => array(
                'name' => __('Leases', 'plughaus-property'),
                'singular_name' => __('Lease', 'plughaus-property'),
                'add_new' => __('Add Lease', 'plughaus-property'),
                'add_new_item' => __('Add New Lease', 'plughaus-property'),
                'edit_item' => __('Edit Lease', 'plughaus-property'),
                'new_item' => __('New Lease', 'plughaus-property'),
                'view_item' => __('View Lease', 'plughaus-property'),
                'search_items' => __('Search Leases', 'plughaus-property'),
                'not_found' => __('No leases found', 'plughaus-property'),
                'not_found_in_trash' => __('No leases found in trash', 'plughaus-property'),
                'menu_name' => __('Leases', 'plughaus-property'),
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => 'phpm-dashboard',
            'show_in_rest' => true,
            'capability_type' => 'phpm_lease',
            'map_meta_cap' => true,
            'has_archive' => false,
            'rewrite' => false,
            'supports' => array('title', 'custom-fields'),
            'menu_icon' => 'dashicons-media-text',
        ));
        
        // Maintenance Requests Post Type
        register_post_type('phpm_maintenance', array(
            'labels' => array(
                'name' => __('Maintenance Requests', 'plughaus-property'),
                'singular_name' => __('Maintenance Request', 'plughaus-property'),
                'add_new' => __('Add Request', 'plughaus-property'),
                'add_new_item' => __('Add New Request', 'plughaus-property'),
                'edit_item' => __('Edit Request', 'plughaus-property'),
                'new_item' => __('New Request', 'plughaus-property'),
                'view_item' => __('View Request', 'plughaus-property'),
                'search_items' => __('Search Requests', 'plughaus-property'),
                'not_found' => __('No requests found', 'plughaus-property'),
                'not_found_in_trash' => __('No requests found in trash', 'plughaus-property'),
                'menu_name' => __('Maintenance', 'plughaus-property'),
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => 'phpm-dashboard',
            'show_in_rest' => true,
            'capability_type' => 'phpm_maintenance',
            'map_meta_cap' => true,
            'has_archive' => false,
            'rewrite' => false,
            'supports' => array('title', 'editor', 'custom-fields', 'comments'),
            'menu_icon' => 'dashicons-hammer',
        ));
    }
    
    /**
     * Register custom post statuses
     */
    public static function register_post_statuses() {
        // Property statuses
        register_post_status('available', array(
            'label' => __('Available', 'plughaus-property'),
            'public' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Available <span class="count">(%s)</span>', 'Available <span class="count">(%s)</span>', 'plughaus-property'),
        ));
        
        register_post_status('occupied', array(
            'label' => __('Occupied', 'plughaus-property'),
            'public' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Occupied <span class="count">(%s)</span>', 'Occupied <span class="count">(%s)</span>', 'plughaus-property'),
        ));
        
        // Lease statuses
        register_post_status('active', array(
            'label' => __('Active', 'plughaus-property'),
            'public' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Active <span class="count">(%s)</span>', 'Active <span class="count">(%s)</span>', 'plughaus-property'),
        ));
        
        register_post_status('expired', array(
            'label' => __('Expired', 'plughaus-property'),
            'public' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>', 'plughaus-property'),
        ));
        
        // Maintenance statuses
        register_post_status('pending_review', array(
            'label' => __('Pending Review', 'plughaus-property'),
            'public' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Pending Review <span class="count">(%s)</span>', 'Pending Review <span class="count">(%s)</span>', 'plughaus-property'),
        ));
        
        register_post_status('in_progress', array(
            'label' => __('In Progress', 'plughaus-property'),
            'public' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('In Progress <span class="count">(%s)</span>', 'In Progress <span class="count">(%s)</span>', 'plughaus-property'),
        ));
        
        register_post_status('completed', array(
            'label' => __('Completed', 'plughaus-property'),
            'public' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>', 'plughaus-property'),
        ));
    }
}

// Initialize post types
PHPM_Post_Types::init();