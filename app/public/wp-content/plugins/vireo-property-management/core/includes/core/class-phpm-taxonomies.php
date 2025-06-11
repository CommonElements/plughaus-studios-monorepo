<?php
/**
 * Custom Taxonomies for PlugHaus Property Management
 */

if (!defined('ABSPATH')) {
    exit;
}

class PHPM_Taxonomies {
    
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
        // Property Type Taxonomy
        register_taxonomy('phpm_property_type', array('phpm_property'), array(
            'labels' => array(
                'name' => __('Property Types', 'plughaus-property'),
                'singular_name' => __('Property Type', 'plughaus-property'),
                'search_items' => __('Search Property Types', 'plughaus-property'),
                'all_items' => __('All Property Types', 'plughaus-property'),
                'parent_item' => __('Parent Property Type', 'plughaus-property'),
                'parent_item_colon' => __('Parent Property Type:', 'plughaus-property'),
                'edit_item' => __('Edit Property Type', 'plughaus-property'),
                'update_item' => __('Update Property Type', 'plughaus-property'),
                'add_new_item' => __('Add New Property Type', 'plughaus-property'),
                'new_item_name' => __('New Property Type Name', 'plughaus-property'),
                'menu_name' => __('Property Types', 'plughaus-property'),
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_in_rest' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'property-type'),
        ));
        
        // Property Amenities Taxonomy
        register_taxonomy('phpm_amenities', array('phpm_property', 'phpm_unit'), array(
            'labels' => array(
                'name' => __('Amenities', 'plughaus-property'),
                'singular_name' => __('Amenity', 'plughaus-property'),
                'search_items' => __('Search Amenities', 'plughaus-property'),
                'all_items' => __('All Amenities', 'plughaus-property'),
                'edit_item' => __('Edit Amenity', 'plughaus-property'),
                'update_item' => __('Update Amenity', 'plughaus-property'),
                'add_new_item' => __('Add New Amenity', 'plughaus-property'),
                'new_item_name' => __('New Amenity Name', 'plughaus-property'),
                'menu_name' => __('Amenities', 'plughaus-property'),
            ),
            'hierarchical' => false,
            'show_ui' => true,
            'show_in_rest' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'amenities'),
        ));
        
        // Property Location Taxonomy
        register_taxonomy('phpm_location', array('phpm_property'), array(
            'labels' => array(
                'name' => __('Locations', 'plughaus-property'),
                'singular_name' => __('Location', 'plughaus-property'),
                'search_items' => __('Search Locations', 'plughaus-property'),
                'all_items' => __('All Locations', 'plughaus-property'),
                'parent_item' => __('Parent Location', 'plughaus-property'),
                'parent_item_colon' => __('Parent Location:', 'plughaus-property'),
                'edit_item' => __('Edit Location', 'plughaus-property'),
                'update_item' => __('Update Location', 'plughaus-property'),
                'add_new_item' => __('Add New Location', 'plughaus-property'),
                'new_item_name' => __('New Location Name', 'plughaus-property'),
                'menu_name' => __('Locations', 'plughaus-property'),
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_in_rest' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'location'),
        ));
        
        // Maintenance Category Taxonomy
        register_taxonomy('phpm_maintenance_category', array('phpm_maintenance'), array(
            'labels' => array(
                'name' => __('Maintenance Categories', 'plughaus-property'),
                'singular_name' => __('Maintenance Category', 'plughaus-property'),
                'search_items' => __('Search Categories', 'plughaus-property'),
                'all_items' => __('All Categories', 'plughaus-property'),
                'parent_item' => __('Parent Category', 'plughaus-property'),
                'parent_item_colon' => __('Parent Category:', 'plughaus-property'),
                'edit_item' => __('Edit Category', 'plughaus-property'),
                'update_item' => __('Update Category', 'plughaus-property'),
                'add_new_item' => __('Add New Category', 'plughaus-property'),
                'new_item_name' => __('New Category Name', 'plughaus-property'),
                'menu_name' => __('Categories', 'plughaus-property'),
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_in_rest' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'maintenance-category'),
        ));
        
        // Add default terms
        self::add_default_terms();
    }
    
    /**
     * Add default taxonomy terms
     */
    private static function add_default_terms() {
        // Default property types
        $property_types = array(
            'Single Family Home',
            'Multi-Family',
            'Apartment Complex',
            'Condominium',
            'Townhouse',
            'Commercial',
            'Mixed Use',
        );
        
        foreach ($property_types as $type) {
            if (!term_exists($type, 'phpm_property_type')) {
                wp_insert_term($type, 'phpm_property_type');
            }
        }
        
        // Default amenities
        $amenities = array(
            'Parking',
            'Laundry',
            'Pool',
            'Gym',
            'Pet Friendly',
            'Storage',
            'Balcony',
            'Garden',
        );
        
        foreach ($amenities as $amenity) {
            if (!term_exists($amenity, 'phpm_amenities')) {
                wp_insert_term($amenity, 'phpm_amenities');
            }
        }
        
        // Default maintenance categories
        $maintenance_categories = array(
            'Plumbing',
            'Electrical',
            'HVAC',
            'Appliances',
            'Structural',
            'Landscaping',
            'Cleaning',
            'Other',
        );
        
        foreach ($maintenance_categories as $category) {
            if (!term_exists($category, 'phpm_maintenance_category')) {
                wp_insert_term($category, 'phpm_maintenance_category');
            }
        }
    }
}

// Initialize taxonomies
PHPM_Taxonomies::init();