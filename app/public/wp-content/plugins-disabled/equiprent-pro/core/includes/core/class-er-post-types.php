<?php
/**
 * EquipRent Pro Post Types
 *
 * @package EquipRent_Pro
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handles custom post types for equipment rental
 */
class ER_Post_Types {

    /**
     * Initialize the class
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'register_post_types'));
        add_action('init', array(__CLASS__, 'register_taxonomies'));
    }

    /**
     * Register custom post types
     */
    public static function register_post_types() {
        // Equipment post type
        register_post_type('equipment', array(
            'labels' => array(
                'name' => __('Equipment', 'equiprent-pro'),
                'singular_name' => __('Equipment Item', 'equiprent-pro'),
                'menu_name' => __('Equipment', 'equiprent-pro'),
                'all_items' => __('All Equipment', 'equiprent-pro'),
                'add_new' => __('Add New', 'equiprent-pro'),
                'add_new_item' => __('Add New Equipment', 'equiprent-pro'),
                'edit_item' => __('Edit Equipment', 'equiprent-pro'),
                'new_item' => __('New Equipment', 'equiprent-pro'),
                'view_item' => __('View Equipment', 'equiprent-pro'),
                'view_items' => __('View Equipment', 'equiprent-pro'),
                'search_items' => __('Search Equipment', 'equiprent-pro'),
                'not_found' => __('No equipment found', 'equiprent-pro'),
                'not_found_in_trash' => __('No equipment found in trash', 'equiprent-pro'),
            ),
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => false, // We'll add it to our custom menu
            'show_in_rest' => true,
            'capability_type' => 'equipment',
            'map_meta_cap' => true,
            'hierarchical' => false,
            'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
            'has_archive' => true,
            'rewrite' => array('slug' => 'equipment'),
            'query_var' => true,
        ));

        // Booking post type
        register_post_type('er_booking', array(
            'labels' => array(
                'name' => __('Bookings', 'equiprent-pro'),
                'singular_name' => __('Booking', 'equiprent-pro'),
                'menu_name' => __('Bookings', 'equiprent-pro'),
                'all_items' => __('All Bookings', 'equiprent-pro'),
                'add_new' => __('Add New', 'equiprent-pro'),
                'add_new_item' => __('Add New Booking', 'equiprent-pro'),
                'edit_item' => __('Edit Booking', 'equiprent-pro'),
                'new_item' => __('New Booking', 'equiprent-pro'),
                'view_item' => __('View Booking', 'equiprent-pro'),
                'view_items' => __('View Bookings', 'equiprent-pro'),
                'search_items' => __('Search Bookings', 'equiprent-pro'),
                'not_found' => __('No bookings found', 'equiprent-pro'),
                'not_found_in_trash' => __('No bookings found in trash', 'equiprent-pro'),
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false, // We'll add it to our custom menu
            'show_in_rest' => false,
            'capability_type' => 'booking',
            'map_meta_cap' => true,
            'hierarchical' => false,
            'supports' => array('title', 'custom-fields'),
            'has_archive' => false,
            'rewrite' => false,
            'query_var' => false,
        ));

        // Customer post type (alternative to extending users table)
        register_post_type('er_customer', array(
            'labels' => array(
                'name' => __('Customers', 'equiprent-pro'),
                'singular_name' => __('Customer', 'equiprent-pro'),
                'menu_name' => __('Customers', 'equiprent-pro'),
                'all_items' => __('All Customers', 'equiprent-pro'),
                'add_new' => __('Add New', 'equiprent-pro'),
                'add_new_item' => __('Add New Customer', 'equiprent-pro'),
                'edit_item' => __('Edit Customer', 'equiprent-pro'),
                'new_item' => __('New Customer', 'equiprent-pro'),
                'view_item' => __('View Customer', 'equiprent-pro'),
                'view_items' => __('View Customers', 'equiprent-pro'),
                'search_items' => __('Search Customers', 'equiprent-pro'),
                'not_found' => __('No customers found', 'equiprent-pro'),
                'not_found_in_trash' => __('No customers found in trash', 'equiprent-pro'),
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false, // We'll add it to our custom menu
            'show_in_rest' => false,
            'capability_type' => 'rental_customer',
            'map_meta_cap' => true,
            'hierarchical' => false,
            'supports' => array('title', 'custom-fields'),
            'has_archive' => false,
            'rewrite' => false,
            'query_var' => false,
        ));

        // Maintenance post type
        register_post_type('er_maintenance', array(
            'labels' => array(
                'name' => __('Maintenance', 'equiprent-pro'),
                'singular_name' => __('Maintenance Record', 'equiprent-pro'),
                'menu_name' => __('Maintenance', 'equiprent-pro'),
                'all_items' => __('All Maintenance', 'equiprent-pro'),
                'add_new' => __('Add New', 'equiprent-pro'),
                'add_new_item' => __('Add Maintenance Record', 'equiprent-pro'),
                'edit_item' => __('Edit Maintenance', 'equiprent-pro'),
                'new_item' => __('New Maintenance', 'equiprent-pro'),
                'view_item' => __('View Maintenance', 'equiprent-pro'),
                'view_items' => __('View Maintenance', 'equiprent-pro'),
                'search_items' => __('Search Maintenance', 'equiprent-pro'),
                'not_found' => __('No maintenance records found', 'equiprent-pro'),
                'not_found_in_trash' => __('No maintenance records found in trash', 'equiprent-pro'),
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false, // We'll add it to our custom menu
            'show_in_rest' => false,
            'capability_type' => 'equipment', // Same permissions as equipment
            'map_meta_cap' => true,
            'hierarchical' => false,
            'supports' => array('title', 'editor', 'custom-fields'),
            'has_archive' => false,
            'rewrite' => false,
            'query_var' => false,
        ));
    }

    /**
     * Register taxonomies
     */
    public static function register_taxonomies() {
        // Equipment Category taxonomy
        register_taxonomy('equipment_category', 'equipment', array(
            'labels' => array(
                'name' => __('Equipment Categories', 'equiprent-pro'),
                'singular_name' => __('Equipment Category', 'equiprent-pro'),
                'search_items' => __('Search Categories', 'equiprent-pro'),
                'all_items' => __('All Categories', 'equiprent-pro'),
                'parent_item' => __('Parent Category', 'equiprent-pro'),
                'parent_item_colon' => __('Parent Category:', 'equiprent-pro'),
                'edit_item' => __('Edit Category', 'equiprent-pro'),
                'update_item' => __('Update Category', 'equiprent-pro'),
                'add_new_item' => __('Add New Category', 'equiprent-pro'),
                'new_item_name' => __('New Category Name', 'equiprent-pro'),
                'menu_name' => __('Categories', 'equiprent-pro'),
            ),
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_rest' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'equipment-category'),
        ));

        // Equipment Brand taxonomy
        register_taxonomy('equipment_brand', 'equipment', array(
            'labels' => array(
                'name' => __('Equipment Brands', 'equiprent-pro'),
                'singular_name' => __('Equipment Brand', 'equiprent-pro'),
                'search_items' => __('Search Brands', 'equiprent-pro'),
                'all_items' => __('All Brands', 'equiprent-pro'),
                'edit_item' => __('Edit Brand', 'equiprent-pro'),
                'update_item' => __('Update Brand', 'equiprent-pro'),
                'add_new_item' => __('Add New Brand', 'equiprent-pro'),
                'new_item_name' => __('New Brand Name', 'equiprent-pro'),
                'menu_name' => __('Brands', 'equiprent-pro'),
            ),
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_rest' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'equipment-brand'),
        ));

        // Equipment Condition taxonomy
        register_taxonomy('equipment_condition', 'equipment', array(
            'labels' => array(
                'name' => __('Equipment Condition', 'equiprent-pro'),
                'singular_name' => __('Condition', 'equiprent-pro'),
                'search_items' => __('Search Conditions', 'equiprent-pro'),
                'all_items' => __('All Conditions', 'equiprent-pro'),
                'edit_item' => __('Edit Condition', 'equiprent-pro'),
                'update_item' => __('Update Condition', 'equiprent-pro'),
                'add_new_item' => __('Add New Condition', 'equiprent-pro'),
                'new_item_name' => __('New Condition Name', 'equiprent-pro'),
                'menu_name' => __('Conditions', 'equiprent-pro'),
            ),
            'hierarchical' => false,
            'public' => false,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_rest' => false,
            'query_var' => false,
            'rewrite' => false,
        ));

        // Location taxonomy
        register_taxonomy('equipment_location', 'equipment', array(
            'labels' => array(
                'name' => __('Locations', 'equiprent-pro'),
                'singular_name' => __('Location', 'equiprent-pro'),
                'search_items' => __('Search Locations', 'equiprent-pro'),
                'all_items' => __('All Locations', 'equiprent-pro'),
                'edit_item' => __('Edit Location', 'equiprent-pro'),
                'update_item' => __('Update Location', 'equiprent-pro'),
                'add_new_item' => __('Add New Location', 'equiprent-pro'),
                'new_item_name' => __('New Location Name', 'equiprent-pro'),
                'menu_name' => __('Locations', 'equiprent-pro'),
            ),
            'hierarchical' => true,
            'public' => false,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_rest' => false,
            'query_var' => false,
            'rewrite' => false,
        ));

        // Booking Status taxonomy
        register_taxonomy('booking_status', 'er_booking', array(
            'labels' => array(
                'name' => __('Booking Status', 'equiprent-pro'),
                'singular_name' => __('Status', 'equiprent-pro'),
                'menu_name' => __('Status', 'equiprent-pro'),
            ),
            'hierarchical' => false,
            'public' => false,
            'show_ui' => false,
            'show_admin_column' => false,
            'show_in_rest' => false,
            'query_var' => false,
            'rewrite' => false,
        ));
    }

    /**
     * Get equipment statuses
     */
    public static function get_equipment_statuses() {
        return array(
            'available' => __('Available', 'equiprent-pro'),
            'rented' => __('Rented', 'equiprent-pro'),
            'maintenance' => __('In Maintenance', 'equiprent-pro'),
            'damaged' => __('Damaged', 'equiprent-pro'),
            'retired' => __('Retired', 'equiprent-pro'),
        );
    }

    /**
     * Get booking statuses
     */
    public static function get_booking_statuses() {
        return array(
            'pending' => __('Pending', 'equiprent-pro'),
            'confirmed' => __('Confirmed', 'equiprent-pro'),
            'active' => __('Active', 'equiprent-pro'),
            'completed' => __('Completed', 'equiprent-pro'),
            'cancelled' => __('Cancelled', 'equiprent-pro'),
            'overdue' => __('Overdue', 'equiprent-pro'),
        );
    }

    /**
     * Get payment statuses
     */
    public static function get_payment_statuses() {
        return array(
            'pending' => __('Pending', 'equiprent-pro'),
            'partial' => __('Partial', 'equiprent-pro'),
            'paid' => __('Paid', 'equiprent-pro'),
            'refunded' => __('Refunded', 'equiprent-pro'),
            'failed' => __('Failed', 'equiprent-pro'),
        );
    }
}