<?php
/**
 * Custom Post Types for Knot4
 * 
 * @package Knot4
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Knot4_Post_Types {
    
    /**
     * Initialize post types
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'register_post_types'));
        add_action('init', array(__CLASS__, 'register_taxonomies'));
    }
    
    /**
     * Register custom post types
     */
    public static function register_post_types() {
        // Donors post type
        register_post_type('knot4_donor', array(
            'labels' => array(
                'name' => __('Donors', 'knot4'),
                'singular_name' => __('Donor', 'knot4'),
                'menu_name' => __('Donors', 'knot4'),
                'add_new' => __('Add New Donor', 'knot4'),
                'add_new_item' => __('Add New Donor', 'knot4'),
                'edit_item' => __('Edit Donor', 'knot4'),
                'new_item' => __('New Donor', 'knot4'),
                'view_item' => __('View Donor', 'knot4'),
                'search_items' => __('Search Donors', 'knot4'),
                'not_found' => __('No donors found', 'knot4'),
                'not_found_in_trash' => __('No donors found in trash', 'knot4'),
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => 'knot4-dashboard',
            'capability_type' => 'knot4_donor',
            'capabilities' => array(
                'edit_post' => 'edit_knot4_donor',
                'read_post' => 'read_knot4_donor',
                'delete_post' => 'delete_knot4_donor',
                'edit_posts' => 'edit_knot4_donors',
                'edit_others_posts' => 'edit_others_knot4_donors',
                'publish_posts' => 'publish_knot4_donors',
                'read_private_posts' => 'read_private_knot4_donors',
            ),
            'supports' => array('title', 'editor', 'custom-fields'),
            'menu_icon' => 'dashicons-groups',
            'has_archive' => false,
            'rewrite' => false,
            'show_in_rest' => false,
        ));
        
        // Events post type
        register_post_type('knot4_event', array(
            'labels' => array(
                'name' => __('Events', 'knot4'),
                'singular_name' => __('Event', 'knot4'),
                'menu_name' => __('Events', 'knot4'),
                'add_new' => __('Add New Event', 'knot4'),
                'add_new_item' => __('Add New Event', 'knot4'),
                'edit_item' => __('Edit Event', 'knot4'),
                'new_item' => __('New Event', 'knot4'),
                'view_item' => __('View Event', 'knot4'),
                'search_items' => __('Search Events', 'knot4'),
                'not_found' => __('No events found', 'knot4'),
                'not_found_in_trash' => __('No events found in trash', 'knot4'),
            ),
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => 'knot4-dashboard',
            'capability_type' => 'knot4_event',
            'capabilities' => array(
                'edit_post' => 'edit_knot4_event',
                'read_post' => 'read_knot4_event',
                'delete_post' => 'delete_knot4_event',
                'edit_posts' => 'edit_knot4_events',
                'edit_others_posts' => 'edit_others_knot4_events',
                'publish_posts' => 'publish_knot4_events',
                'read_private_posts' => 'read_private_knot4_events',
            ),
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
            'menu_icon' => 'dashicons-calendar-alt',
            'has_archive' => true,
            'rewrite' => array('slug' => 'events'),
            'show_in_rest' => true,
        ));
        
        // Campaigns post type (Pro feature)
        if (Knot4_Utilities::is_pro()) {
            register_post_type('knot4_campaign', array(
                'labels' => array(
                    'name' => __('Campaigns', 'knot4'),
                    'singular_name' => __('Campaign', 'knot4'),
                    'menu_name' => __('Campaigns', 'knot4'),
                    'add_new' => __('Add New Campaign', 'knot4'),
                    'add_new_item' => __('Add New Campaign', 'knot4'),
                    'edit_item' => __('Edit Campaign', 'knot4'),
                    'new_item' => __('New Campaign', 'knot4'),
                    'view_item' => __('View Campaign', 'knot4'),
                    'search_items' => __('Search Campaigns', 'knot4'),
                    'not_found' => __('No campaigns found', 'knot4'),
                    'not_found_in_trash' => __('No campaigns found in trash', 'knot4'),
                ),
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => 'knot4-dashboard',
                'capability_type' => 'knot4_campaign',
                'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
                'menu_icon' => 'dashicons-megaphone',
                'has_archive' => true,
                'rewrite' => array('slug' => 'campaigns'),
                'show_in_rest' => true,
            ));
        }
        
        // Forms post type (for donation forms)
        register_post_type('knot4_form', array(
            'labels' => array(
                'name' => __('Donation Forms', 'knot4'),
                'singular_name' => __('Donation Form', 'knot4'),
                'menu_name' => __('Forms', 'knot4'),
                'add_new' => __('Add New Form', 'knot4'),
                'add_new_item' => __('Add New Form', 'knot4'),
                'edit_item' => __('Edit Form', 'knot4'),
                'new_item' => __('New Form', 'knot4'),
                'view_item' => __('View Form', 'knot4'),
                'search_items' => __('Search Forms', 'knot4'),
                'not_found' => __('No forms found', 'knot4'),
                'not_found_in_trash' => __('No forms found in trash', 'knot4'),
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => 'knot4-dashboard',
            'capability_type' => 'knot4_form',
            'capabilities' => array(
                'edit_post' => 'edit_knot4_form',
                'read_post' => 'read_knot4_form',
                'delete_post' => 'delete_knot4_form',
                'edit_posts' => 'edit_knot4_forms',
                'edit_others_posts' => 'edit_others_knot4_forms',
                'publish_posts' => 'publish_knot4_forms',
                'read_private_posts' => 'read_private_knot4_forms',
            ),
            'supports' => array('title', 'custom-fields'),
            'menu_icon' => 'dashicons-feedback',
            'has_archive' => false,
            'rewrite' => false,
            'show_in_rest' => false,
        ));
    }
    
    /**
     * Register taxonomies
     */
    public static function register_taxonomies() {
        // Donor tags
        register_taxonomy('knot4_donor_tag', 'knot4_donor', array(
            'labels' => array(
                'name' => __('Donor Tags', 'knot4'),
                'singular_name' => __('Donor Tag', 'knot4'),
                'menu_name' => __('Tags', 'knot4'),
                'add_new_item' => __('Add New Donor Tag', 'knot4'),
                'new_item_name' => __('New Donor Tag Name', 'knot4'),
            ),
            'hierarchical' => false,
            'public' => false,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_menu' => true,
            'show_tagcloud' => false,
            'rewrite' => false,
            'capabilities' => array(
                'manage_terms' => 'manage_knot4_donor_tags',
                'edit_terms' => 'edit_knot4_donor_tags',
                'delete_terms' => 'delete_knot4_donor_tags',
                'assign_terms' => 'assign_knot4_donor_tags',
            ),
        ));
        
        // Event categories
        register_taxonomy('knot4_event_category', 'knot4_event', array(
            'labels' => array(
                'name' => __('Event Categories', 'knot4'),
                'singular_name' => __('Event Category', 'knot4'),
                'menu_name' => __('Categories', 'knot4'),
                'add_new_item' => __('Add New Event Category', 'knot4'),
                'new_item_name' => __('New Event Category Name', 'knot4'),
            ),
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_menu' => true,
            'show_tagcloud' => true,
            'rewrite' => array('slug' => 'event-category'),
            'show_in_rest' => true,
            'capabilities' => array(
                'manage_terms' => 'manage_knot4_event_categories',
                'edit_terms' => 'edit_knot4_event_categories',
                'delete_terms' => 'delete_knot4_event_categories',
                'assign_terms' => 'assign_knot4_event_categories',
            ),
        ));
        
        // Donation funds/categories
        register_taxonomy('knot4_fund', array('knot4_donor', 'knot4_campaign'), array(
            'labels' => array(
                'name' => __('Funds', 'knot4'),
                'singular_name' => __('Fund', 'knot4'),
                'menu_name' => __('Funds', 'knot4'),
                'add_new_item' => __('Add New Fund', 'knot4'),
                'new_item_name' => __('New Fund Name', 'knot4'),
            ),
            'hierarchical' => true,
            'public' => false,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_menu' => true,
            'show_tagcloud' => false,
            'rewrite' => false,
            'capabilities' => array(
                'manage_terms' => 'manage_knot4_funds',
                'edit_terms' => 'edit_knot4_funds',
                'delete_terms' => 'delete_knot4_funds',
                'assign_terms' => 'assign_knot4_funds',
            ),
        ));
    }
}