<?php
/**
 * Custom Post Types for Vireo Property Management
 *
 * @package VireoPropertyManagement
 * @since 1.0.0
 */

namespace Vireo\PropertyManagement\Core;

/**
 * Handles custom post types registration
 */
class Post_Types {
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
        // Properties
        register_post_type('vpm_property', array(
            'labels' => array(
                'name' => __('Properties', 'vireo-property'),
                'singular_name' => __('Property', 'vireo-property'),
                'add_new' => __('Add Property', 'vireo-property'),
                'add_new_item' => __('Add New Property', 'vireo-property'),
                'edit_item' => __('Edit Property', 'vireo-property'),
                'new_item' => __('New Property', 'vireo-property'),
                'view_item' => __('View Property', 'vireo-property'),
                'search_items' => __('Search Properties', 'vireo-property'),
                'not_found' => __('No properties found', 'vireo-property'),
                'not_found_in_trash' => __('No properties found in trash', 'vireo-property'),
                'menu_name' => __('Properties', 'vireo-property')
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
        
        // Units
        register_post_type('vpm_unit', array(
            'labels' => array(
                'name' => __('Units', 'vireo-property'),
                'singular_name' => __('Unit', 'vireo-property'),
                'add_new' => __('Add Unit', 'vireo-property'),
                'add_new_item' => __('Add New Unit', 'vireo-property'),
                'edit_item' => __('Edit Unit', 'vireo-property'),
                'new_item' => __('New Unit', 'vireo-property'),
                'view_item' => __('View Unit', 'vireo-property'),
                'search_items' => __('Search Units', 'vireo-property'),
                'not_found' => __('No units found', 'vireo-property'),
                'not_found_in_trash' => __('No units found in trash', 'vireo-property'),
                'menu_name' => __('Units', 'vireo-property')
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
        
        // Tenants
        register_post_type('vpm_tenant', array(
            'labels' => array(
                'name' => __('Tenants', 'vireo-property'),
                'singular_name' => __('Tenant', 'vireo-property'),
                'add_new' => __('Add Tenant', 'vireo-property'),
                'add_new_item' => __('Add New Tenant', 'vireo-property'),
                'edit_item' => __('Edit Tenant', 'vireo-property'),
                'new_item' => __('New Tenant', 'vireo-property'),
                'view_item' => __('View Tenant', 'vireo-property'),
                'search_items' => __('Search Tenants', 'vireo-property'),
                'not_found' => __('No tenants found', 'vireo-property'),
                'not_found_in_trash' => __('No tenants found in trash', 'vireo-property'),
                'menu_name' => __('Tenants', 'vireo-property')
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
        
        // Leases
        register_post_type('vpm_lease', array(
            'labels' => array(
                'name' => __('Leases', 'vireo-property'),
                'singular_name' => __('Lease', 'vireo-property'),
                'add_new' => __('Add Lease', 'vireo-property'),
                'add_new_item' => __('Add New Lease', 'vireo-property'),
                'edit_item' => __('Edit Lease', 'vireo-property'),
                'new_item' => __('New Lease', 'vireo-property'),
                'view_item' => __('View Lease', 'vireo-property'),
                'search_items' => __('Search Leases', 'vireo-property'),
                'not_found' => __('No leases found', 'vireo-property'),
                'not_found_in_trash' => __('No leases found in trash', 'vireo-property'),
                'menu_name' => __('Leases', 'vireo-property')
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
        
        // Maintenance Requests
        register_post_type('vpm_maintenance', array(
            'labels' => array(
                'name' => __('Maintenance Requests', 'vireo-property'),
                'singular_name' => __('Maintenance Request', 'vireo-property'),
                'add_new' => __('Add Request', 'vireo-property'),
                'add_new_item' => __('Add New Request', 'vireo-property'),
                'edit_item' => __('Edit Request', 'vireo-property'),
                'new_item' => __('New Request', 'vireo-property'),
                'view_item' => __('View Request', 'vireo-property'),
                'search_items' => __('Search Requests', 'vireo-property'),
                'not_found' => __('No requests found', 'vireo-property'),
                'not_found_in_trash' => __('No requests found in trash', 'vireo-property'),
                'menu_name' => __('Maintenance', 'vireo-property')
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
            'label' => __('Available', 'vireo-property'),
            'public' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Available <span class="count">(%s)</span>', 'Available <span class="count">(%s)</span>', 'vireo-property'),
        ));
        
        register_post_status('occupied', array(
            'label' => __('Occupied', 'vireo-property'),
            'public' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Occupied <span class="count">(%s)</span>', 'Occupied <span class="count">(%s)</span>', 'vireo-property'),
        ));
        
        // Lease statuses
        register_post_status('active', array(
            'label' => __('Active', 'vireo-property'),
            'public' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Active <span class="count">(%s)</span>', 'Active <span class="count">(%s)</span>', 'vireo-property'),
        ));
        
        register_post_status('expired', array(
            'label' => __('Expired', 'vireo-property'),
            'public' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>', 'vireo-property'),
        ));
        
        // Maintenance statuses
        register_post_status('pending_review', array(
            'label' => __('Pending Review', 'vireo-property'),
            'public' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Pending Review <span class="count">(%s)</span>', 'Pending Review <span class="count">(%s)</span>', 'vireo-property'),
        ));
        
        register_post_status('in_progress', array(
            'label' => __('In Progress', 'vireo-property'),
            'public' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('In Progress <span class="count">(%s)</span>', 'In Progress <span class="count">(%s)</span>', 'vireo-property'),
        ));
        
        register_post_status('completed', array(
            'label' => __('Completed', 'vireo-property'),
            'public' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>', 'vireo-property'),
        ));
    }
}

// Initialize post types
Post_Types::init();