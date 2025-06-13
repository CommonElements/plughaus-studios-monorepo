<?php
/**
 * Register all custom post types for EquipRent Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * The post types registration class
 */
class ERP_Post_Types {

    /**
     * Initialize the post types
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'register_post_types'));
        add_action('init', array(__CLASS__, 'register_taxonomies'));
    }

    /**
     * Register custom post types
     */
    public static function register_post_types() {
        
        // Equipment Post Type
        $equipment_labels = array(
            'name'                  => _x('Equipment', 'Post Type General Name', 'equiprent-pro'),
            'singular_name'         => _x('Equipment', 'Post Type Singular Name', 'equiprent-pro'),
            'menu_name'             => __('Equipment', 'equiprent-pro'),
            'name_admin_bar'        => __('Equipment', 'equiprent-pro'),
            'archives'              => __('Equipment Archives', 'equiprent-pro'),
            'attributes'            => __('Equipment Attributes', 'equiprent-pro'),
            'parent_item_colon'     => __('Parent Equipment:', 'equiprent-pro'),
            'all_items'             => __('All Equipment', 'equiprent-pro'),
            'add_new_item'          => __('Add New Equipment', 'equiprent-pro'),
            'add_new'               => __('Add New', 'equiprent-pro'),
            'new_item'              => __('New Equipment', 'equiprent-pro'),
            'edit_item'             => __('Edit Equipment', 'equiprent-pro'),
            'update_item'           => __('Update Equipment', 'equiprent-pro'),
            'view_item'             => __('View Equipment', 'equiprent-pro'),
            'view_items'            => __('View Equipment', 'equiprent-pro'),
            'search_items'          => __('Search Equipment', 'equiprent-pro'),
            'not_found'             => __('Not found', 'equiprent-pro'),
            'not_found_in_trash'    => __('Not found in Trash', 'equiprent-pro'),
            'featured_image'        => __('Featured Image', 'equiprent-pro'),
            'set_featured_image'    => __('Set featured image', 'equiprent-pro'),
            'remove_featured_image' => __('Remove featured image', 'equiprent-pro'),
            'use_featured_image'    => __('Use as featured image', 'equiprent-pro'),
            'insert_into_item'      => __('Insert into equipment', 'equiprent-pro'),
            'uploaded_to_this_item' => __('Uploaded to this equipment', 'equiprent-pro'),
            'items_list'            => __('Equipment list', 'equiprent-pro'),
            'items_list_navigation' => __('Equipment list navigation', 'equiprent-pro'),
            'filter_items_list'     => __('Filter equipment list', 'equiprent-pro'),
        );

        $equipment_args = array(
            'label'                 => __('Equipment', 'equiprent-pro'),
            'description'           => __('Equipment for rental management', 'equiprent-pro'),
            'labels'                => $equipment_labels,
            'supports'              => array('title', 'editor', 'thumbnail', 'custom-fields'),
            'taxonomies'            => array('equipment_category', 'equipment_tag'),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => false, // We'll add it to our custom menu
            'menu_position'         => 20,
            'menu_icon'             => 'dashicons-hammer',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'capabilities'          => array(
                'create_posts'       => 'manage_equipment',
                'edit_post'          => 'edit_equipment',
                'edit_posts'         => 'edit_equipment',
                'edit_others_posts'  => 'edit_equipment',
                'delete_post'        => 'delete_equipment',
                'delete_posts'       => 'delete_equipment',
                'read_post'          => 'view_equipment',
                'read_private_posts' => 'view_equipment',
            ),
            'show_in_rest'          => true,
            'rewrite'               => array('slug' => 'equipment'),
        );

        register_post_type('erp_equipment', $equipment_args);

        // Booking Post Type
        $booking_labels = array(
            'name'                  => _x('Bookings', 'Post Type General Name', 'equiprent-pro'),
            'singular_name'         => _x('Booking', 'Post Type Singular Name', 'equiprent-pro'),
            'menu_name'             => __('Bookings', 'equiprent-pro'),
            'name_admin_bar'        => __('Booking', 'equiprent-pro'),
            'archives'              => __('Booking Archives', 'equiprent-pro'),
            'attributes'            => __('Booking Attributes', 'equiprent-pro'),
            'parent_item_colon'     => __('Parent Booking:', 'equiprent-pro'),
            'all_items'             => __('All Bookings', 'equiprent-pro'),
            'add_new_item'          => __('Add New Booking', 'equiprent-pro'),
            'add_new'               => __('Add New', 'equiprent-pro'),
            'new_item'              => __('New Booking', 'equiprent-pro'),
            'edit_item'             => __('Edit Booking', 'equiprent-pro'),
            'update_item'           => __('Update Booking', 'equiprent-pro'),
            'view_item'             => __('View Booking', 'equiprent-pro'),
            'view_items'            => __('View Bookings', 'equiprent-pro'),
            'search_items'          => __('Search Bookings', 'equiprent-pro'),
            'not_found'             => __('Not found', 'equiprent-pro'),
            'not_found_in_trash'    => __('Not found in Trash', 'equiprent-pro'),
            'featured_image'        => __('Featured Image', 'equiprent-pro'),
            'set_featured_image'    => __('Set featured image', 'equiprent-pro'),
            'remove_featured_image' => __('Remove featured image', 'equiprent-pro'),
            'use_featured_image'    => __('Use as featured image', 'equiprent-pro'),
            'insert_into_item'      => __('Insert into booking', 'equiprent-pro'),
            'uploaded_to_this_item' => __('Uploaded to this booking', 'equiprent-pro'),
            'items_list'            => __('Bookings list', 'equiprent-pro'),
            'items_list_navigation' => __('Bookings list navigation', 'equiprent-pro'),
            'filter_items_list'     => __('Filter bookings list', 'equiprent-pro'),
        );

        $booking_args = array(
            'label'                 => __('Booking', 'equiprent-pro'),
            'description'           => __('Equipment rental bookings', 'equiprent-pro'),
            'labels'                => $booking_labels,
            'supports'              => array('title', 'editor', 'custom-fields'),
            'hierarchical'          => false,
            'public'                => false, // Bookings are admin-only
            'show_ui'               => true,
            'show_in_menu'          => false, // We'll add it to our custom menu
            'menu_position'         => 21,
            'menu_icon'             => 'dashicons-calendar-alt',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'capability_type'       => 'post',
            'capabilities'          => array(
                'create_posts'       => 'manage_bookings',
                'edit_post'          => 'edit_bookings',
                'edit_posts'         => 'edit_bookings',
                'edit_others_posts'  => 'edit_bookings',
                'delete_post'        => 'delete_bookings',
                'delete_posts'       => 'delete_bookings',
                'read_post'          => 'view_bookings',
                'read_private_posts' => 'view_bookings',
            ),
            'show_in_rest'          => true,
        );

        register_post_type('erp_booking', $booking_args);

        // Customer Post Type (for extended customer profiles)
        $customer_labels = array(
            'name'                  => _x('Customers', 'Post Type General Name', 'equiprent-pro'),
            'singular_name'         => _x('Customer', 'Post Type Singular Name', 'equiprent-pro'),
            'menu_name'             => __('Customers', 'equiprent-pro'),
            'name_admin_bar'        => __('Customer', 'equiprent-pro'),
            'archives'              => __('Customer Archives', 'equiprent-pro'),
            'attributes'            => __('Customer Attributes', 'equiprent-pro'),
            'parent_item_colon'     => __('Parent Customer:', 'equiprent-pro'),
            'all_items'             => __('All Customers', 'equiprent-pro'),
            'add_new_item'          => __('Add New Customer', 'equiprent-pro'),
            'add_new'               => __('Add New', 'equiprent-pro'),
            'new_item'              => __('New Customer', 'equiprent-pro'),
            'edit_item'             => __('Edit Customer', 'equiprent-pro'),
            'update_item'           => __('Update Customer', 'equiprent-pro'),
            'view_item'             => __('View Customer', 'equiprent-pro'),
            'view_items'            => __('View Customers', 'equiprent-pro'),
            'search_items'          => __('Search Customers', 'equiprent-pro'),
            'not_found'             => __('Not found', 'equiprent-pro'),
            'not_found_in_trash'    => __('Not found in Trash', 'equiprent-pro'),
            'featured_image'        => __('Featured Image', 'equiprent-pro'),
            'set_featured_image'    => __('Set featured image', 'equiprent-pro'),
            'remove_featured_image' => __('Remove featured image', 'equiprent-pro'),
            'use_featured_image'    => __('Use as featured image', 'equiprent-pro'),
            'insert_into_item'      => __('Insert into customer', 'equiprent-pro'),
            'uploaded_to_this_item' => __('Uploaded to this customer', 'equiprent-pro'),
            'items_list'            => __('Customers list', 'equiprent-pro'),
            'items_list_navigation' => __('Customers list navigation', 'equiprent-pro'),
            'filter_items_list'     => __('Filter customers list', 'equiprent-pro'),
        );

        $customer_args = array(
            'label'                 => __('Customer', 'equiprent-pro'),
            'description'           => __('Equipment rental customers', 'equiprent-pro'),
            'labels'                => $customer_labels,
            'supports'              => array('title', 'editor', 'custom-fields'),
            'hierarchical'          => false,
            'public'                => false, // Customers are admin-only
            'show_ui'               => true,
            'show_in_menu'          => false, // We'll add it to our custom menu
            'menu_position'         => 22,
            'menu_icon'             => 'dashicons-groups',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'capability_type'       => 'post',
            'capabilities'          => array(
                'create_posts'       => 'manage_customers',
                'edit_post'          => 'edit_customers',
                'edit_posts'         => 'edit_customers',
                'edit_others_posts'  => 'edit_customers',
                'delete_post'        => 'delete_customers',
                'delete_posts'       => 'delete_customers',
                'read_post'          => 'view_customers',
                'read_private_posts' => 'view_customers',
            ),
            'show_in_rest'          => true,
        );

        register_post_type('erp_customer', $customer_args);
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

        register_taxonomy('equipment_category', array('erp_equipment'), $category_args);

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

        register_taxonomy('equipment_tag', array('erp_equipment'), $tag_args);
    }
}