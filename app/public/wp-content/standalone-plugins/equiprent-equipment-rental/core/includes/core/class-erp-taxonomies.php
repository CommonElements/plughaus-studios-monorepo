<?php
/**
 * Register taxonomies for EquipRent Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * The taxonomies registration class
 */
class ERP_Taxonomies {

    /**
     * Initialize the taxonomies
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'register_taxonomies'));
    }

    /**
     * Register custom taxonomies
     */
    public static function register_taxonomies() {
        
        // Equipment Category Taxonomy
        $category_labels = array(
            'name'                       => _x('Equipment Categories', 'Taxonomy General Name', 'equiprent-pro'),
            'singular_name'              => _x('Equipment Category', 'Taxonomy Singular Name', 'equiprent-pro'),
            'menu_name'                  => __('Categories', 'equiprent-pro'),
            'all_items'                  => __('All Categories', 'equiprent-pro'),
            'parent_item'                => __('Parent Category', 'equiprent-pro'),
            'parent_item_colon'          => __('Parent Category:', 'equiprent-pro'),
            'new_item_name'              => __('New Category Name', 'equiprent-pro'),
            'add_new_item'               => __('Add New Category', 'equiprent-pro'),
            'edit_item'                  => __('Edit Category', 'equiprent-pro'),
            'update_item'                => __('Update Category', 'equiprent-pro'),
            'view_item'                  => __('View Category', 'equiprent-pro'),
            'separate_items_with_commas' => __('Separate categories with commas', 'equiprent-pro'),
            'add_or_remove_items'        => __('Add or remove categories', 'equiprent-pro'),
            'choose_from_most_used'      => __('Choose from the most used', 'equiprent-pro'),
            'popular_items'              => __('Popular Categories', 'equiprent-pro'),
            'search_items'               => __('Search Categories', 'equiprent-pro'),
            'not_found'                  => __('Not Found', 'equiprent-pro'),
            'no_terms'                   => __('No categories', 'equiprent-pro'),
            'items_list'                 => __('Categories list', 'equiprent-pro'),
            'items_list_navigation'      => __('Categories list navigation', 'equiprent-pro'),
        );

        $category_args = array(
            'labels'                     => $category_labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'show_in_rest'               => true,
            'rewrite'                    => array('slug' => 'equipment-category'),
        );

        if (function_exists('register_taxonomy')) {
            register_taxonomy('equipment_category', array('erp_equipment'), $category_args);
        }

        // Equipment Tags Taxonomy
        $tag_labels = array(
            'name'                       => _x('Equipment Tags', 'Taxonomy General Name', 'equiprent-pro'),
            'singular_name'              => _x('Equipment Tag', 'Taxonomy Singular Name', 'equiprent-pro'),
            'menu_name'                  => __('Tags', 'equiprent-pro'),
            'all_items'                  => __('All Tags', 'equiprent-pro'),
            'parent_item'                => __('Parent Tag', 'equiprent-pro'),
            'parent_item_colon'          => __('Parent Tag:', 'equiprent-pro'),
            'new_item_name'              => __('New Tag Name', 'equiprent-pro'),
            'add_new_item'               => __('Add New Tag', 'equiprent-pro'),
            'edit_item'                  => __('Edit Tag', 'equiprent-pro'),
            'update_item'                => __('Update Tag', 'equiprent-pro'),
            'view_item'                  => __('View Tag', 'equiprent-pro'),
            'separate_items_with_commas' => __('Separate tags with commas', 'equiprent-pro'),
            'add_or_remove_items'        => __('Add or remove tags', 'equiprent-pro'),
            'choose_from_most_used'      => __('Choose from the most used', 'equiprent-pro'),
            'popular_items'              => __('Popular Tags', 'equiprent-pro'),
            'search_items'               => __('Search Tags', 'equiprent-pro'),
            'not_found'                  => __('Not Found', 'equiprent-pro'),
            'no_terms'                   => __('No tags', 'equiprent-pro'),
            'items_list'                 => __('Tags list', 'equiprent-pro'),
            'items_list_navigation'      => __('Tags list navigation', 'equiprent-pro'),
        );

        $tag_args = array(
            'labels'                     => $tag_labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'show_in_rest'               => true,
            'rewrite'                    => array('slug' => 'equipment-tag'),
        );

        if (function_exists('register_taxonomy')) {
            register_taxonomy('equipment_tag', array('erp_equipment'), $tag_args);
        }
    }
}