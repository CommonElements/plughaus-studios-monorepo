<?php
/**
 * Taxonomy management for EquipRent Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Taxonomies class
 */
class ERP_Taxonomies {

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
        if (!function_exists('register_taxonomy')) {
            return;
        }

        // Equipment Category taxonomy
        $category_labels = array(
            'name'              => _x('Equipment Categories', 'taxonomy general name', 'equiprent-pro'),
            'singular_name'     => _x('Equipment Category', 'taxonomy singular name', 'equiprent-pro'),
            'search_items'      => __('Search Categories', 'equiprent-pro'),
            'all_items'         => __('All Categories', 'equiprent-pro'),
            'parent_item'       => __('Parent Category', 'equiprent-pro'),
            'parent_item_colon' => __('Parent Category:', 'equiprent-pro'),
            'edit_item'         => __('Edit Category', 'equiprent-pro'),
            'update_item'       => __('Update Category', 'equiprent-pro'),
            'add_new_item'      => __('Add New Category', 'equiprent-pro'),
            'new_item_name'     => __('New Category Name', 'equiprent-pro'),
            'menu_name'         => __('Categories', 'equiprent-pro'),
        );

        $category_args = array(
            'hierarchical'      => true,
            'labels'            => $category_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'equipment-category'),
            'show_in_rest'      => true,
            'capabilities'      => array(
                'manage_terms' => 'manage_equipment_categories',
                'edit_terms'   => 'edit_equipment_categories',
                'delete_terms' => 'delete_equipment_categories',
                'assign_terms' => 'assign_equipment_categories',
            ),
        );

        register_taxonomy('equipment_category', array('erp_equipment'), $category_args);

        // Equipment Tag taxonomy
        $tag_labels = array(
            'name'                       => _x('Equipment Tags', 'taxonomy general name', 'equiprent-pro'),
            'singular_name'              => _x('Equipment Tag', 'taxonomy singular name', 'equiprent-pro'),
            'search_items'               => __('Search Tags', 'equiprent-pro'),
            'popular_items'              => __('Popular Tags', 'equiprent-pro'),
            'all_items'                  => __('All Tags', 'equiprent-pro'),
            'edit_item'                  => __('Edit Tag', 'equiprent-pro'),
            'update_item'                => __('Update Tag', 'equiprent-pro'),
            'add_new_item'               => __('Add New Tag', 'equiprent-pro'),
            'new_item_name'              => __('New Tag Name', 'equiprent-pro'),
            'separate_items_with_commas' => __('Separate tags with commas', 'equiprent-pro'),
            'add_or_remove_items'        => __('Add or remove tags', 'equiprent-pro'),
            'choose_from_most_used'      => __('Choose from the most used tags', 'equiprent-pro'),
            'not_found'                  => __('No tags found.', 'equiprent-pro'),
            'menu_name'                  => __('Tags', 'equiprent-pro'),
        );

        $tag_args = array(
            'hierarchical'          => false,
            'labels'                => $tag_labels,
            'show_ui'               => true,
            'show_admin_column'     => false,
            'update_count_callback' => '_update_post_term_count',
            'query_var'             => true,
            'rewrite'               => array('slug' => 'equipment-tag'),
            'show_in_rest'          => true,
            'capabilities'          => array(
                'manage_terms' => 'manage_equipment_tags',
                'edit_terms'   => 'edit_equipment_tags',
                'delete_terms' => 'delete_equipment_tags',
                'assign_terms' => 'assign_equipment_tags',
            ),
        );

        register_taxonomy('equipment_tag', array('erp_equipment'), $tag_args);

        // Equipment Location taxonomy (Pro feature)
        if (class_exists('ERP_Utilities') && ERP_Utilities::is_pro_active()) {
            $location_labels = array(
                'name'              => _x('Equipment Locations', 'taxonomy general name', 'equiprent-pro'),
                'singular_name'     => _x('Equipment Location', 'taxonomy singular name', 'equiprent-pro'),
                'search_items'      => __('Search Locations', 'equiprent-pro'),
                'all_items'         => __('All Locations', 'equiprent-pro'),
                'parent_item'       => __('Parent Location', 'equiprent-pro'),
                'parent_item_colon' => __('Parent Location:', 'equiprent-pro'),
                'edit_item'         => __('Edit Location', 'equiprent-pro'),
                'update_item'       => __('Update Location', 'equiprent-pro'),
                'add_new_item'      => __('Add New Location', 'equiprent-pro'),
                'new_item_name'     => __('New Location Name', 'equiprent-pro'),
                'menu_name'         => __('Locations', 'equiprent-pro'),
            );

            $location_args = array(
                'hierarchical'      => true,
                'labels'            => $location_labels,
                'show_ui'           => true,
                'show_admin_column' => true,
                'query_var'         => true,
                'rewrite'           => array('slug' => 'equipment-location'),
                'show_in_rest'      => true,
                'capabilities'      => array(
                    'manage_terms' => 'manage_equipment_locations',
                    'edit_terms'   => 'edit_equipment_locations',
                    'delete_terms' => 'delete_equipment_locations',
                    'assign_terms' => 'assign_equipment_locations',
                ),
            );

            register_taxonomy('equipment_location', array('erp_equipment'), $location_args);
        }
    }

    /**
     * Create default taxonomy terms
     */
    public static function create_default_terms() {
        if (!function_exists('wp_insert_term')) {
            return;
        }

        // Default equipment categories
        $default_categories = array(
            'Construction Equipment' => array(
                'Excavators',
                'Bulldozers', 
                'Cranes',
                'Concrete Equipment',
                'Compactors'
            ),
            'Tools & Hardware' => array(
                'Power Tools',
                'Hand Tools',
                'Safety Equipment',
                'Measuring Tools'
            ),
            'Party & Event Equipment' => array(
                'Tables & Chairs',
                'Tents & Canopies',
                'Audio/Visual',
                'Lighting',
                'Catering Equipment'
            ),
            'Outdoor & Recreation' => array(
                'Camping Gear',
                'Sports Equipment',
                'Water Sports',
                'Winter Sports'
            )
        );

        foreach ($default_categories as $parent => $children) {
            // Create parent category
            $parent_term = wp_insert_term($parent, 'equipment_category');
            
            if (!is_wp_error($parent_term)) {
                $parent_id = $parent_term['term_id'];
                
                // Create child categories
                foreach ($children as $child) {
                    wp_insert_term($child, 'equipment_category', array(
                        'parent' => $parent_id
                    ));
                }
            }
        }

        // Default equipment tags
        $default_tags = array(
            'popular',
            'new-arrival',
            'featured',
            'heavy-duty',
            'portable',
            'electric',
            'gas-powered',
            'professional',
            'commercial',
            'residential'
        );

        foreach ($default_tags as $tag) {
            wp_insert_term($tag, 'equipment_tag');
        }

        // Default locations (Pro feature)
        if (class_exists('ERP_Utilities') && ERP_Utilities::is_pro_active()) {
            $default_locations = array(
                'Main Warehouse',
                'North Location',
                'South Location',
                'Mobile Unit 1',
                'Mobile Unit 2'
            );

            foreach ($default_locations as $location) {
                wp_insert_term($location, 'equipment_location');
            }
        }
    }

    /**
     * Get equipment categories for dropdown
     */
    public static function get_categories_dropdown($selected = '') {
        if (!function_exists('get_terms')) {
            return '<option value="">No categories available</option>';
        }

        $categories = get_terms(array(
            'taxonomy' => 'equipment_category',
            'hide_empty' => false,
            'hierarchical' => true
        ));

        if (is_wp_error($categories) || empty($categories)) {
            return '<option value="">No categories available</option>';
        }

        $output = '<option value="">Select Category</option>';
        foreach ($categories as $category) {
            $selected_attr = selected($selected, $category->term_id, false);
            $output .= sprintf(
                '<option value="%d" %s>%s</option>',
                $category->term_id,
                $selected_attr,
                esc_html($category->name)
            );
        }

        return $output;
    }

    /**
     * Get equipment by category
     */
    public static function get_equipment_by_category($category_id, $limit = 10) {
        if (!function_exists('get_posts')) {
            return array();
        }

        return get_posts(array(
            'post_type' => 'erp_equipment',
            'numberposts' => $limit,
            'post_status' => 'publish',
            'tax_query' => array(
                array(
                    'taxonomy' => 'equipment_category',
                    'field'    => 'term_id',
                    'terms'    => $category_id,
                ),
            ),
        ));
    }
}