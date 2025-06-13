<?php
/**
 * GymFlow Post Types Class
 *
 * Registers custom post types for the fitness studio management system
 *
 * @package GymFlow
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * GF_Post_Types Class
 *
 * Handles registration of custom post types
 */
class GF_Post_Types {

    /**
     * Initialize post types
     */
    public function init() {
        add_action('init', array($this, 'register_post_types'));
    }

    /**
     * Register all custom post types
     */
    public function register_post_types() {
        $this->register_member_post_type();
        $this->register_class_post_type();
        $this->register_trainer_post_type();
        $this->register_equipment_post_type();
        $this->register_booking_post_type();
    }

    /**
     * Register Member post type
     */
    private function register_member_post_type() {
        $labels = array(
            'name'                  => __('Members', 'gymflow'),
            'singular_name'         => __('Member', 'gymflow'),
            'menu_name'             => __('Members', 'gymflow'),
            'name_admin_bar'        => __('Member', 'gymflow'),
            'archives'              => __('Member Archives', 'gymflow'),
            'attributes'            => __('Member Attributes', 'gymflow'),
            'parent_item_colon'     => __('Parent Member:', 'gymflow'),
            'all_items'             => __('All Members', 'gymflow'),
            'add_new_item'          => __('Add New Member', 'gymflow'),
            'add_new'               => __('Add New', 'gymflow'),
            'new_item'              => __('New Member', 'gymflow'),
            'edit_item'             => __('Edit Member', 'gymflow'),
            'update_item'           => __('Update Member', 'gymflow'),
            'view_item'             => __('View Member', 'gymflow'),
            'view_items'            => __('View Members', 'gymflow'),
            'search_items'          => __('Search Members', 'gymflow'),
            'not_found'             => __('Not found', 'gymflow'),
            'not_found_in_trash'    => __('Not found in Trash', 'gymflow'),
            'featured_image'        => __('Profile Photo', 'gymflow'),
            'set_featured_image'    => __('Set profile photo', 'gymflow'),
            'remove_featured_image' => __('Remove profile photo', 'gymflow'),
            'use_featured_image'    => __('Use as profile photo', 'gymflow'),
            'insert_into_item'      => __('Insert into member', 'gymflow'),
            'uploaded_to_this_item' => __('Uploaded to this member', 'gymflow'),
            'items_list'            => __('Members list', 'gymflow'),
            'items_list_navigation' => __('Members list navigation', 'gymflow'),
            'filter_items_list'     => __('Filter members list', 'gymflow'),
        );

        $args = array(
            'label'                 => __('Member', 'gymflow'),
            'description'           => __('Gym and fitness studio members', 'gymflow'),
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'thumbnail', 'custom-fields'),
            'taxonomies'            => array('gf_member_type'),
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => false, // We'll add to custom menu
            'menu_position'         => 25,
            'menu_icon'             => 'dashicons-groups',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'capability_type'       => array('gf_member', 'gf_members'),
            'map_meta_cap'          => true,
            'show_in_rest'          => true,
            'rest_base'             => 'gf-members',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        );

        register_post_type('gf_member', $args);
    }

    /**
     * Register Class post type
     */
    private function register_class_post_type() {
        $labels = array(
            'name'                  => __('Classes', 'gymflow'),
            'singular_name'         => __('Class', 'gymflow'),
            'menu_name'             => __('Classes', 'gymflow'),
            'name_admin_bar'        => __('Class', 'gymflow'),
            'archives'              => __('Class Archives', 'gymflow'),
            'attributes'            => __('Class Attributes', 'gymflow'),
            'parent_item_colon'     => __('Parent Class:', 'gymflow'),
            'all_items'             => __('All Classes', 'gymflow'),
            'add_new_item'          => __('Add New Class', 'gymflow'),
            'add_new'               => __('Add New', 'gymflow'),
            'new_item'              => __('New Class', 'gymflow'),
            'edit_item'             => __('Edit Class', 'gymflow'),
            'update_item'           => __('Update Class', 'gymflow'),
            'view_item'             => __('View Class', 'gymflow'),
            'view_items'            => __('View Classes', 'gymflow'),
            'search_items'          => __('Search Classes', 'gymflow'),
            'not_found'             => __('Not found', 'gymflow'),
            'not_found_in_trash'    => __('Not found in Trash', 'gymflow'),
            'featured_image'        => __('Class Image', 'gymflow'),
            'set_featured_image'    => __('Set class image', 'gymflow'),
            'remove_featured_image' => __('Remove class image', 'gymflow'),
            'use_featured_image'    => __('Use as class image', 'gymflow'),
            'insert_into_item'      => __('Insert into class', 'gymflow'),
            'uploaded_to_this_item' => __('Uploaded to this class', 'gymflow'),
            'items_list'            => __('Classes list', 'gymflow'),
            'items_list_navigation' => __('Classes list navigation', 'gymflow'),
            'filter_items_list'     => __('Filter classes list', 'gymflow'),
        );

        $args = array(
            'label'                 => __('Class', 'gymflow'),
            'description'           => __('Fitness classes and programs', 'gymflow'),
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
            'taxonomies'            => array('gf_class_category', 'gf_difficulty_level'),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => false, // We'll add to custom menu
            'menu_position'         => 26,
            'menu_icon'             => 'dashicons-calendar-alt',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => array('gf_class', 'gf_classes'),
            'map_meta_cap'          => true,
            'show_in_rest'          => true,
            'rest_base'             => 'gf-classes',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        );

        register_post_type('gf_class', $args);
    }

    /**
     * Register Trainer post type
     */
    private function register_trainer_post_type() {
        $labels = array(
            'name'                  => __('Trainers', 'gymflow'),
            'singular_name'         => __('Trainer', 'gymflow'),
            'menu_name'             => __('Trainers', 'gymflow'),
            'name_admin_bar'        => __('Trainer', 'gymflow'),
            'archives'              => __('Trainer Archives', 'gymflow'),
            'attributes'            => __('Trainer Attributes', 'gymflow'),
            'parent_item_colon'     => __('Parent Trainer:', 'gymflow'),
            'all_items'             => __('All Trainers', 'gymflow'),
            'add_new_item'          => __('Add New Trainer', 'gymflow'),
            'add_new'               => __('Add New', 'gymflow'),
            'new_item'              => __('New Trainer', 'gymflow'),
            'edit_item'             => __('Edit Trainer', 'gymflow'),
            'update_item'           => __('Update Trainer', 'gymflow'),
            'view_item'             => __('View Trainer', 'gymflow'),
            'view_items'            => __('View Trainers', 'gymflow'),
            'search_items'          => __('Search Trainers', 'gymflow'),
            'not_found'             => __('Not found', 'gymflow'),
            'not_found_in_trash'    => __('Not found in Trash', 'gymflow'),
            'featured_image'        => __('Trainer Photo', 'gymflow'),
            'set_featured_image'    => __('Set trainer photo', 'gymflow'),
            'remove_featured_image' => __('Remove trainer photo', 'gymflow'),
            'use_featured_image'    => __('Use as trainer photo', 'gymflow'),
            'insert_into_item'      => __('Insert into trainer', 'gymflow'),
            'uploaded_to_this_item' => __('Uploaded to this trainer', 'gymflow'),
            'items_list'            => __('Trainers list', 'gymflow'),
            'items_list_navigation' => __('Trainers list navigation', 'gymflow'),
            'filter_items_list'     => __('Filter trainers list', 'gymflow'),
        );

        $args = array(
            'label'                 => __('Trainer', 'gymflow'),
            'description'           => __('Fitness trainers and instructors', 'gymflow'),
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
            'taxonomies'            => array('gf_trainer_specialty'),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => false, // We'll add to custom menu
            'menu_position'         => 27,
            'menu_icon'             => 'dashicons-businessman',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => array('gf_trainer', 'gf_trainers'),
            'map_meta_cap'          => true,
            'show_in_rest'          => true,
            'rest_base'             => 'gf-trainers',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        );

        register_post_type('gf_trainer', $args);
    }

    /**
     * Register Equipment post type
     */
    private function register_equipment_post_type() {
        $labels = array(
            'name'                  => __('Equipment', 'gymflow'),
            'singular_name'         => __('Equipment', 'gymflow'),
            'menu_name'             => __('Equipment', 'gymflow'),
            'name_admin_bar'        => __('Equipment', 'gymflow'),
            'archives'              => __('Equipment Archives', 'gymflow'),
            'attributes'            => __('Equipment Attributes', 'gymflow'),
            'parent_item_colon'     => __('Parent Equipment:', 'gymflow'),
            'all_items'             => __('All Equipment', 'gymflow'),
            'add_new_item'          => __('Add New Equipment', 'gymflow'),
            'add_new'               => __('Add New', 'gymflow'),
            'new_item'              => __('New Equipment', 'gymflow'),
            'edit_item'             => __('Edit Equipment', 'gymflow'),
            'update_item'           => __('Update Equipment', 'gymflow'),
            'view_item'             => __('View Equipment', 'gymflow'),
            'view_items'            => __('View Equipment', 'gymflow'),
            'search_items'          => __('Search Equipment', 'gymflow'),
            'not_found'             => __('Not found', 'gymflow'),
            'not_found_in_trash'    => __('Not found in Trash', 'gymflow'),
            'featured_image'        => __('Equipment Photo', 'gymflow'),
            'set_featured_image'    => __('Set equipment photo', 'gymflow'),
            'remove_featured_image' => __('Remove equipment photo', 'gymflow'),
            'use_featured_image'    => __('Use as equipment photo', 'gymflow'),
            'insert_into_item'      => __('Insert into equipment', 'gymflow'),
            'uploaded_to_this_item' => __('Uploaded to this equipment', 'gymflow'),
            'items_list'            => __('Equipment list', 'gymflow'),
            'items_list_navigation' => __('Equipment list navigation', 'gymflow'),
            'filter_items_list'     => __('Filter equipment list', 'gymflow'),
        );

        $args = array(
            'label'                 => __('Equipment', 'gymflow'),
            'description'           => __('Gym and fitness equipment', 'gymflow'),
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'thumbnail', 'custom-fields'),
            'taxonomies'            => array('gf_equipment_category'),
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => false, // We'll add to custom menu
            'menu_position'         => 28,
            'menu_icon'             => 'dashicons-hammer',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'capability_type'       => array('gf_equipment', 'gf_equipment'),
            'map_meta_cap'          => true,
            'show_in_rest'          => true,
            'rest_base'             => 'gf-equipment',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        );

        register_post_type('gf_equipment', $args);
    }

    /**
     * Register Booking post type
     */
    private function register_booking_post_type() {
        $labels = array(
            'name'                  => __('Bookings', 'gymflow'),
            'singular_name'         => __('Booking', 'gymflow'),
            'menu_name'             => __('Bookings', 'gymflow'),
            'name_admin_bar'        => __('Booking', 'gymflow'),
            'archives'              => __('Booking Archives', 'gymflow'),
            'attributes'            => __('Booking Attributes', 'gymflow'),
            'parent_item_colon'     => __('Parent Booking:', 'gymflow'),
            'all_items'             => __('All Bookings', 'gymflow'),
            'add_new_item'          => __('Add New Booking', 'gymflow'),
            'add_new'               => __('Add New', 'gymflow'),
            'new_item'              => __('New Booking', 'gymflow'),
            'edit_item'             => __('Edit Booking', 'gymflow'),
            'update_item'           => __('Update Booking', 'gymflow'),
            'view_item'             => __('View Booking', 'gymflow'),
            'view_items'            => __('View Bookings', 'gymflow'),
            'search_items'          => __('Search Bookings', 'gymflow'),
            'not_found'             => __('Not found', 'gymflow'),
            'not_found_in_trash'    => __('Not found in Trash', 'gymflow'),
            'featured_image'        => __('Booking Image', 'gymflow'),
            'set_featured_image'    => __('Set booking image', 'gymflow'),
            'remove_featured_image' => __('Remove booking image', 'gymflow'),
            'use_featured_image'    => __('Use as booking image', 'gymflow'),
            'insert_into_item'      => __('Insert into booking', 'gymflow'),
            'uploaded_to_this_item' => __('Uploaded to this booking', 'gymflow'),
            'items_list'            => __('Bookings list', 'gymflow'),
            'items_list_navigation' => __('Bookings list navigation', 'gymflow'),
            'filter_items_list'     => __('Filter bookings list', 'gymflow'),
        );

        $args = array(
            'label'                 => __('Booking', 'gymflow'),
            'description'           => __('Class and equipment bookings', 'gymflow'),
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'custom-fields'),
            'taxonomies'            => array('gf_booking_status'),
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => false, // We'll add to custom menu
            'menu_position'         => 29,
            'menu_icon'             => 'dashicons-calendar-alt',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'capability_type'       => array('gf_booking', 'gf_bookings'),
            'map_meta_cap'          => true,
            'show_in_rest'          => true,
            'rest_base'             => 'gf-bookings',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        );

        register_post_type('gf_booking', $args);
    }

    /**
     * Set custom capabilities for post types
     */
    public static function set_custom_capabilities() {
        $post_types = array(
            'gf_member' => array(
                'edit_post' => 'edit_gf_member',
                'read_post' => 'read_gf_member',
                'delete_post' => 'delete_gf_member',
                'edit_posts' => 'edit_gf_members',
                'edit_others_posts' => 'edit_others_gf_members',
                'publish_posts' => 'publish_gf_members',
                'read_private_posts' => 'read_private_gf_members',
                'delete_posts' => 'delete_gf_members',
                'delete_private_posts' => 'delete_private_gf_members',
                'delete_published_posts' => 'delete_published_gf_members',
                'delete_others_posts' => 'delete_others_gf_members',
                'edit_private_posts' => 'edit_private_gf_members',
                'edit_published_posts' => 'edit_published_gf_members'
            ),
            'gf_class' => array(
                'edit_post' => 'edit_gf_class',
                'read_post' => 'read_gf_class',
                'delete_post' => 'delete_gf_class',
                'edit_posts' => 'edit_gf_classes',
                'edit_others_posts' => 'edit_others_gf_classes',
                'publish_posts' => 'publish_gf_classes',
                'read_private_posts' => 'read_private_gf_classes',
                'delete_posts' => 'delete_gf_classes',
                'delete_private_posts' => 'delete_private_gf_classes',
                'delete_published_posts' => 'delete_published_gf_classes',
                'delete_others_posts' => 'delete_others_gf_classes',
                'edit_private_posts' => 'edit_private_gf_classes',
                'edit_published_posts' => 'edit_published_gf_classes'
            ),
            'gf_trainer' => array(
                'edit_post' => 'edit_gf_trainer',
                'read_post' => 'read_gf_trainer',
                'delete_post' => 'delete_gf_trainer',
                'edit_posts' => 'edit_gf_trainers',
                'edit_others_posts' => 'edit_others_gf_trainers',
                'publish_posts' => 'publish_gf_trainers',
                'read_private_posts' => 'read_private_gf_trainers',
                'delete_posts' => 'delete_gf_trainers',
                'delete_private_posts' => 'delete_private_gf_trainers',
                'delete_published_posts' => 'delete_published_gf_trainers',
                'delete_others_posts' => 'delete_others_gf_trainers',
                'edit_private_posts' => 'edit_private_gf_trainers',
                'edit_published_posts' => 'edit_published_gf_trainers'
            ),
            'gf_equipment' => array(
                'edit_post' => 'edit_gf_equipment',
                'read_post' => 'read_gf_equipment',
                'delete_post' => 'delete_gf_equipment',
                'edit_posts' => 'edit_gf_equipment',
                'edit_others_posts' => 'edit_others_gf_equipment',
                'publish_posts' => 'publish_gf_equipment',
                'read_private_posts' => 'read_private_gf_equipment',
                'delete_posts' => 'delete_gf_equipment',
                'delete_private_posts' => 'delete_private_gf_equipment',
                'delete_published_posts' => 'delete_published_gf_equipment',
                'delete_others_posts' => 'delete_others_gf_equipment',
                'edit_private_posts' => 'edit_private_gf_equipment',
                'edit_published_posts' => 'edit_published_gf_equipment'
            ),
            'gf_booking' => array(
                'edit_post' => 'edit_gf_booking',
                'read_post' => 'read_gf_booking',
                'delete_post' => 'delete_gf_booking',
                'edit_posts' => 'edit_gf_bookings',
                'edit_others_posts' => 'edit_others_gf_bookings',
                'publish_posts' => 'publish_gf_bookings',
                'read_private_posts' => 'read_private_gf_bookings',
                'delete_posts' => 'delete_gf_bookings',
                'delete_private_posts' => 'delete_private_gf_bookings',
                'delete_published_posts' => 'delete_published_gf_bookings',
                'delete_others_posts' => 'delete_others_gf_bookings',
                'edit_private_posts' => 'edit_private_gf_bookings',
                'edit_published_posts' => 'edit_published_gf_bookings'
            )
        );

        return $post_types;
    }
}