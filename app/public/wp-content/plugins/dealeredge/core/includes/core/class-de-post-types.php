<?php
/**
 * Register custom post types for DealerEdge
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class DE_Post_Types {
    
    public static function init() {
        add_action('init', array(__CLASS__, 'register_post_types'));
        add_action('init', array(__CLASS__, 'register_taxonomies'));
    }
    
    public static function register_post_types() {
        
        // Vehicles (for both inventory and service records)
        register_post_type('de_vehicle', array(
            'labels' => array(
                'name' => __('Vehicles', 'dealeredge'),
                'singular_name' => __('Vehicle', 'dealeredge'),
                'menu_name' => __('Vehicles', 'dealeredge'),
                'add_new' => __('Add New Vehicle', 'dealeredge'),
                'add_new_item' => __('Add New Vehicle', 'dealeredge'),
                'edit_item' => __('Edit Vehicle', 'dealeredge'),
                'new_item' => __('New Vehicle', 'dealeredge'),
                'view_item' => __('View Vehicle', 'dealeredge'),
                'search_items' => __('Search Vehicles', 'dealeredge'),
                'not_found' => __('No vehicles found', 'dealeredge'),
                'not_found_in_trash' => __('No vehicles found in trash', 'dealeredge'),
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'show_in_admin_bar' => true,
            'capability_type' => 'post',
            'has_archive' => false,
            'hierarchical' => false,
            'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
            'rewrite' => false,
            'query_var' => false,
        ));
        
        // Customers
        register_post_type('de_customer', array(
            'labels' => array(
                'name' => __('Customers', 'dealeredge'),
                'singular_name' => __('Customer', 'dealeredge'),
                'menu_name' => __('Customers', 'dealeredge'),
                'add_new' => __('Add New Customer', 'dealeredge'),
                'add_new_item' => __('Add New Customer', 'dealeredge'),
                'edit_item' => __('Edit Customer', 'dealeredge'),
                'new_item' => __('New Customer', 'dealeredge'),
                'view_item' => __('View Customer', 'dealeredge'),
                'search_items' => __('Search Customers', 'dealeredge'),
                'not_found' => __('No customers found', 'dealeredge'),
                'not_found_in_trash' => __('No customers found in trash', 'dealeredge'),
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'show_in_admin_bar' => true,
            'capability_type' => 'post',
            'has_archive' => false,
            'hierarchical' => false,
            'supports' => array('title', 'editor', 'custom-fields'),
            'rewrite' => false,
            'query_var' => false,
        ));
        
        // Work Orders (for auto shop functionality)
        register_post_type('de_work_order', array(
            'labels' => array(
                'name' => __('Work Orders', 'dealeredge'),
                'singular_name' => __('Work Order', 'dealeredge'),
                'menu_name' => __('Work Orders', 'dealeredge'),
                'add_new' => __('Add New Work Order', 'dealeredge'),
                'add_new_item' => __('Add New Work Order', 'dealeredge'),
                'edit_item' => __('Edit Work Order', 'dealeredge'),
                'new_item' => __('New Work Order', 'dealeredge'),
                'view_item' => __('View Work Order', 'dealeredge'),
                'search_items' => __('Search Work Orders', 'dealeredge'),
                'not_found' => __('No work orders found', 'dealeredge'),
                'not_found_in_trash' => __('No work orders found in trash', 'dealeredge'),
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'show_in_admin_bar' => true,
            'capability_type' => 'post',
            'has_archive' => false,
            'hierarchical' => false,
            'supports' => array('title', 'editor', 'custom-fields'),
            'rewrite' => false,
            'query_var' => false,
        ));
        
        // Sales (for dealership functionality)
        register_post_type('de_sale', array(
            'labels' => array(
                'name' => __('Sales', 'dealeredge'),
                'singular_name' => __('Sale', 'dealeredge'),
                'menu_name' => __('Sales', 'dealeredge'),
                'add_new' => __('Add New Sale', 'dealeredge'),
                'add_new_item' => __('Add New Sale', 'dealeredge'),
                'edit_item' => __('Edit Sale', 'dealeredge'),
                'new_item' => __('New Sale', 'dealeredge'),
                'view_item' => __('View Sale', 'dealeredge'),
                'search_items' => __('Search Sales', 'dealeredge'),
                'not_found' => __('No sales found', 'dealeredge'),
                'not_found_in_trash' => __('No sales found in trash', 'dealeredge'),
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'show_in_admin_bar' => true,
            'capability_type' => 'post',
            'has_archive' => false,
            'hierarchical' => false,
            'supports' => array('title', 'editor', 'custom-fields'),
            'rewrite' => false,
            'query_var' => false,
        ));
        
        // Parts Inventory
        register_post_type('de_part', array(
            'labels' => array(
                'name' => __('Parts', 'dealeredge'),
                'singular_name' => __('Part', 'dealeredge'),
                'menu_name' => __('Parts', 'dealeredge'),
                'add_new' => __('Add New Part', 'dealeredge'),
                'add_new_item' => __('Add New Part', 'dealeredge'),
                'edit_item' => __('Edit Part', 'dealeredge'),
                'new_item' => __('New Part', 'dealeredge'),
                'view_item' => __('View Part', 'dealeredge'),
                'search_items' => __('Search Parts', 'dealeredge'),
                'not_found' => __('No parts found', 'dealeredge'),
                'not_found_in_trash' => __('No parts found in trash', 'dealeredge'),
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'show_in_admin_bar' => true,
            'capability_type' => 'post',
            'has_archive' => false,
            'hierarchical' => false,
            'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
            'rewrite' => false,
            'query_var' => false,
        ));
    }
    
    public static function register_taxonomies() {
        
        // Vehicle Makes
        register_taxonomy('de_vehicle_make', 'de_vehicle', array(
            'labels' => array(
                'name' => __('Vehicle Makes', 'dealeredge'),
                'singular_name' => __('Vehicle Make', 'dealeredge'),
                'menu_name' => __('Makes', 'dealeredge'),
                'all_items' => __('All Makes', 'dealeredge'),
                'edit_item' => __('Edit Make', 'dealeredge'),
                'view_item' => __('View Make', 'dealeredge'),
                'update_item' => __('Update Make', 'dealeredge'),
                'add_new_item' => __('Add New Make', 'dealeredge'),
                'new_item_name' => __('New Make Name', 'dealeredge'),
                'search_items' => __('Search Makes', 'dealeredge'),
                'popular_items' => __('Popular Makes', 'dealeredge'),
                'not_found' => __('No makes found', 'dealeredge'),
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'show_admin_column' => true,
            'hierarchical' => false,
            'rewrite' => false,
            'query_var' => false,
        ));
        
        // Vehicle Models
        register_taxonomy('de_vehicle_model', 'de_vehicle', array(
            'labels' => array(
                'name' => __('Vehicle Models', 'dealeredge'),
                'singular_name' => __('Vehicle Model', 'dealeredge'),
                'menu_name' => __('Models', 'dealeredge'),
                'all_items' => __('All Models', 'dealeredge'),
                'edit_item' => __('Edit Model', 'dealeredge'),
                'view_item' => __('View Model', 'dealeredge'),
                'update_item' => __('Update Model', 'dealeredge'),
                'add_new_item' => __('Add New Model', 'dealeredge'),
                'new_item_name' => __('New Model Name', 'dealeredge'),
                'search_items' => __('Search Models', 'dealeredge'),
                'popular_items' => __('Popular Models', 'dealeredge'),
                'not_found' => __('No models found', 'dealeredge'),
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'show_admin_column' => true,
            'hierarchical' => false,
            'rewrite' => false,
            'query_var' => false,
        ));
        
        // Service Categories
        register_taxonomy('de_service_category', 'de_work_order', array(
            'labels' => array(
                'name' => __('Service Categories', 'dealeredge'),
                'singular_name' => __('Service Category', 'dealeredge'),
                'menu_name' => __('Service Categories', 'dealeredge'),
                'all_items' => __('All Categories', 'dealeredge'),
                'edit_item' => __('Edit Category', 'dealeredge'),
                'view_item' => __('View Category', 'dealeredge'),
                'update_item' => __('Update Category', 'dealeredge'),
                'add_new_item' => __('Add New Category', 'dealeredge'),
                'new_item_name' => __('New Category Name', 'dealeredge'),
                'search_items' => __('Search Categories', 'dealeredge'),
                'popular_items' => __('Popular Categories', 'dealeredge'),
                'not_found' => __('No categories found', 'dealeredge'),
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'show_admin_column' => true,
            'hierarchical' => true,
            'rewrite' => false,
            'query_var' => false,
        ));
        
        // Part Categories
        register_taxonomy('de_part_category', 'de_part', array(
            'labels' => array(
                'name' => __('Part Categories', 'dealeredge'),
                'singular_name' => __('Part Category', 'dealeredge'),
                'menu_name' => __('Part Categories', 'dealeredge'),
                'all_items' => __('All Categories', 'dealeredge'),
                'edit_item' => __('Edit Category', 'dealeredge'),
                'view_item' => __('View Category', 'dealeredge'),
                'update_item' => __('Update Category', 'dealeredge'),
                'add_new_item' => __('Add New Category', 'dealeredge'),
                'new_item_name' => __('New Category Name', 'dealeredge'),
                'search_items' => __('Search Categories', 'dealeredge'),
                'popular_items' => __('Popular Categories', 'dealeredge'),
                'not_found' => __('No categories found', 'dealeredge'),
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'show_admin_column' => true,
            'hierarchical' => true,
            'rewrite' => false,
            'query_var' => false,
        ));
    }
}