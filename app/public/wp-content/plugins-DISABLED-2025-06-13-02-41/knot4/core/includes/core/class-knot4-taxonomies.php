<?php
/**
 * Custom Taxonomies for Knot4
 * 
 * @package Knot4
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Knot4_Taxonomies {
    
    /**
     * Initialize taxonomies
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'register_taxonomies'));
    }
    
    /**
     * Register custom taxonomies
     */
    public static function register_taxonomies() {
        // Event Categories
        register_taxonomy(
            'knot4_event_category',
            'knot4_event',
            array(
                'labels' => array(
                    'name' => __('Event Categories', 'knot4'),
                    'singular_name' => __('Event Category', 'knot4'),
                    'menu_name' => __('Categories', 'knot4'),
                    'all_items' => __('All Categories', 'knot4'),
                    'edit_item' => __('Edit Category', 'knot4'),
                    'view_item' => __('View Category', 'knot4'),
                    'update_item' => __('Update Category', 'knot4'),
                    'add_new_item' => __('Add New Category', 'knot4'),
                    'new_item_name' => __('New Category Name', 'knot4'),
                    'search_items' => __('Search Categories', 'knot4'),
                    'popular_items' => __('Popular Categories', 'knot4'),
                    'separate_items_with_commas' => __('Separate categories with commas', 'knot4'),
                    'add_or_remove_items' => __('Add or remove categories', 'knot4'),
                    'choose_from_most_used' => __('Choose from most used categories', 'knot4'),
                    'not_found' => __('No categories found', 'knot4'),
                ),
                'public' => true,
                'publicly_queryable' => true,
                'hierarchical' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_nav_menus' => true,
                'show_in_rest' => true,
                'show_tagcloud' => true,
                'show_in_quick_edit' => true,
                'show_admin_column' => true,
                'capabilities' => array(
                    'manage_terms' => 'manage_knot4_events',
                    'edit_terms' => 'manage_knot4_events',
                    'delete_terms' => 'manage_knot4_events',
                    'assign_terms' => 'edit_knot4_events',
                ),
                'rewrite' => array(
                    'slug' => 'event-category',
                    'with_front' => false,
                    'hierarchical' => true,
                ),
            )
        );
        
        // Donor Types/Categories
        register_taxonomy(
            'knot4_donor_type',
            'knot4_donor',
            array(
                'labels' => array(
                    'name' => __('Donor Types', 'knot4'),
                    'singular_name' => __('Donor Type', 'knot4'),
                    'menu_name' => __('Types', 'knot4'),
                    'all_items' => __('All Types', 'knot4'),
                    'edit_item' => __('Edit Type', 'knot4'),
                    'view_item' => __('View Type', 'knot4'),
                    'update_item' => __('Update Type', 'knot4'),
                    'add_new_item' => __('Add New Type', 'knot4'),
                    'new_item_name' => __('New Type Name', 'knot4'),
                    'search_items' => __('Search Types', 'knot4'),
                    'popular_items' => __('Popular Types', 'knot4'),
                    'separate_items_with_commas' => __('Separate types with commas', 'knot4'),
                    'add_or_remove_items' => __('Add or remove types', 'knot4'),
                    'choose_from_most_used' => __('Choose from most used types', 'knot4'),
                    'not_found' => __('No types found', 'knot4'),
                ),
                'public' => false,
                'publicly_queryable' => false,
                'hierarchical' => false,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_nav_menus' => false,
                'show_in_rest' => true,
                'show_tagcloud' => false,
                'show_in_quick_edit' => true,
                'show_admin_column' => true,
                'capabilities' => array(
                    'manage_terms' => 'manage_knot4_donors',
                    'edit_terms' => 'manage_knot4_donors',
                    'delete_terms' => 'manage_knot4_donors',
                    'assign_terms' => 'edit_knot4_donors',
                ),
            )
        );
        
        // Campaign Categories (Pro feature)
        if (Knot4_Utilities::is_pro()) {
            register_taxonomy(
                'knot4_campaign_category',
                'knot4_campaign',
                array(
                    'labels' => array(
                        'name' => __('Campaign Categories', 'knot4'),
                        'singular_name' => __('Campaign Category', 'knot4'),
                        'menu_name' => __('Categories', 'knot4'),
                        'all_items' => __('All Categories', 'knot4'),
                        'edit_item' => __('Edit Category', 'knot4'),
                        'view_item' => __('View Category', 'knot4'),
                        'update_item' => __('Update Category', 'knot4'),
                        'add_new_item' => __('Add New Category', 'knot4'),
                        'new_item_name' => __('New Category Name', 'knot4'),
                        'search_items' => __('Search Categories', 'knot4'),
                        'popular_items' => __('Popular Categories', 'knot4'),
                        'separate_items_with_commas' => __('Separate categories with commas', 'knot4'),
                        'add_or_remove_items' => __('Add or remove categories', 'knot4'),
                        'choose_from_most_used' => __('Choose from most used categories', 'knot4'),
                        'not_found' => __('No categories found', 'knot4'),
                    ),
                    'public' => true,
                    'publicly_queryable' => true,
                    'hierarchical' => true,
                    'show_ui' => true,
                    'show_in_menu' => true,
                    'show_in_nav_menus' => true,
                    'show_in_rest' => true,
                    'show_tagcloud' => true,
                    'show_in_quick_edit' => true,
                    'show_admin_column' => true,
                    'capabilities' => array(
                        'manage_terms' => 'manage_knot4_nonprofit',
                        'edit_terms' => 'manage_knot4_nonprofit',
                        'delete_terms' => 'manage_knot4_nonprofit',
                        'assign_terms' => 'edit_knot4_donations',
                    ),
                    'rewrite' => array(
                        'slug' => 'campaign-category',
                        'with_front' => false,
                        'hierarchical' => true,
                    ),
                )
            );
        }
        
        // Fund Designations
        register_taxonomy(
            'knot4_fund_designation',
            array('knot4_donation', 'knot4_campaign'),
            array(
                'labels' => array(
                    'name' => __('Fund Designations', 'knot4'),
                    'singular_name' => __('Fund Designation', 'knot4'),
                    'menu_name' => __('Fund Designations', 'knot4'),
                    'all_items' => __('All Designations', 'knot4'),
                    'edit_item' => __('Edit Designation', 'knot4'),
                    'view_item' => __('View Designation', 'knot4'),
                    'update_item' => __('Update Designation', 'knot4'),
                    'add_new_item' => __('Add New Designation', 'knot4'),
                    'new_item_name' => __('New Designation Name', 'knot4'),
                    'search_items' => __('Search Designations', 'knot4'),
                    'popular_items' => __('Popular Designations', 'knot4'),
                    'separate_items_with_commas' => __('Separate designations with commas', 'knot4'),
                    'add_or_remove_items' => __('Add or remove designations', 'knot4'),
                    'choose_from_most_used' => __('Choose from most used designations', 'knot4'),
                    'not_found' => __('No designations found', 'knot4'),
                ),
                'public' => false,
                'publicly_queryable' => false,
                'hierarchical' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_nav_menus' => false,
                'show_in_rest' => true,
                'show_tagcloud' => false,
                'show_in_quick_edit' => true,
                'show_admin_column' => true,
                'capabilities' => array(
                    'manage_terms' => 'manage_knot4_nonprofit',
                    'edit_terms' => 'manage_knot4_nonprofit',
                    'delete_terms' => 'manage_knot4_nonprofit',
                    'assign_terms' => 'edit_knot4_donations',
                ),
            )
        );
    }
    
    /**
     * Create default taxonomy terms
     */
    public static function create_default_terms() {
        // Default event categories
        $event_categories = array(
            __('Fundraising', 'knot4'),
            __('Community', 'knot4'),
            __('Educational', 'knot4'),
            __('Volunteer', 'knot4'),
            __('Social', 'knot4'),
        );
        
        foreach ($event_categories as $category) {
            if (!term_exists($category, 'knot4_event_category')) {
                wp_insert_term($category, 'knot4_event_category');
            }
        }
        
        // Default donor types
        $donor_types = array(
            __('Individual', 'knot4'),
            __('Corporate', 'knot4'),
            __('Foundation', 'knot4'),
            __('Government', 'knot4'),
            __('Religious Organization', 'knot4'),
        );
        
        foreach ($donor_types as $type) {
            if (!term_exists($type, 'knot4_donor_type')) {
                wp_insert_term($type, 'knot4_donor_type');
            }
        }
        
        // Default fund designations
        $fund_designations = array(
            __('General Fund', 'knot4'),
            __('Building Fund', 'knot4'),
            __('Education Fund', 'knot4'),
            __('Emergency Relief', 'knot4'),
            __('Scholarship Fund', 'knot4'),
        );
        
        foreach ($fund_designations as $designation) {
            if (!term_exists($designation, 'knot4_fund_designation')) {
                wp_insert_term($designation, 'knot4_fund_designation');
            }
        }
    }
}