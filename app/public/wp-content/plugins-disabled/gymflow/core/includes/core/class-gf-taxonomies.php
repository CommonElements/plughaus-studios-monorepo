<?php
/**
 * GymFlow Taxonomies Class
 *
 * Registers custom taxonomies for the fitness studio management system
 *
 * @package GymFlow
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * GF_Taxonomies Class
 *
 * Handles registration of custom taxonomies
 */
class GF_Taxonomies {

    /**
     * Initialize taxonomies
     */
    public function init() {
        add_action('init', array($this, 'register_taxonomies'));
    }

    /**
     * Register all custom taxonomies
     */
    public function register_taxonomies() {
        $this->register_member_type_taxonomy();
        $this->register_class_category_taxonomy();
        $this->register_difficulty_level_taxonomy();
        $this->register_trainer_specialty_taxonomy();
        $this->register_equipment_category_taxonomy();
        $this->register_booking_status_taxonomy();
    }

    /**
     * Register Member Type taxonomy
     */
    private function register_member_type_taxonomy() {
        $labels = array(
            'name'                       => __('Member Types', 'gymflow'),
            'singular_name'              => __('Member Type', 'gymflow'),
            'menu_name'                  => __('Member Types', 'gymflow'),
            'all_items'                  => __('All Member Types', 'gymflow'),
            'parent_item'                => __('Parent Member Type', 'gymflow'),
            'parent_item_colon'          => __('Parent Member Type:', 'gymflow'),
            'new_item_name'              => __('New Member Type Name', 'gymflow'),
            'add_new_item'               => __('Add New Member Type', 'gymflow'),
            'edit_item'                  => __('Edit Member Type', 'gymflow'),
            'update_item'                => __('Update Member Type', 'gymflow'),
            'view_item'                  => __('View Member Type', 'gymflow'),
            'separate_items_with_commas' => __('Separate member types with commas', 'gymflow'),
            'add_or_remove_items'        => __('Add or remove member types', 'gymflow'),
            'choose_from_most_used'      => __('Choose from the most used', 'gymflow'),
            'popular_items'              => __('Popular Member Types', 'gymflow'),
            'search_items'               => __('Search Member Types', 'gymflow'),
            'not_found'                  => __('Not Found', 'gymflow'),
            'no_terms'                   => __('No member types', 'gymflow'),
            'items_list'                 => __('Member types list', 'gymflow'),
            'items_list_navigation'      => __('Member types list navigation', 'gymflow'),
        );

        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => false,
            'public'                     => false,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => false,
            'show_tagcloud'              => false,
            'show_in_rest'               => true,
            'rest_base'                  => 'member-types',
            'capabilities'               => array(
                'manage_terms' => 'manage_gymflow',
                'edit_terms'   => 'manage_gymflow',
                'delete_terms' => 'manage_gymflow',
                'assign_terms' => 'edit_gymflow'
            )
        );

        register_taxonomy('gf_member_type', array('gf_member'), $args);
    }

    /**
     * Register Class Category taxonomy
     */
    private function register_class_category_taxonomy() {
        $labels = array(
            'name'                       => __('Class Categories', 'gymflow'),
            'singular_name'              => __('Class Category', 'gymflow'),
            'menu_name'                  => __('Categories', 'gymflow'),
            'all_items'                  => __('All Categories', 'gymflow'),
            'parent_item'                => __('Parent Category', 'gymflow'),
            'parent_item_colon'          => __('Parent Category:', 'gymflow'),
            'new_item_name'              => __('New Category Name', 'gymflow'),
            'add_new_item'               => __('Add New Category', 'gymflow'),
            'edit_item'                  => __('Edit Category', 'gymflow'),
            'update_item'                => __('Update Category', 'gymflow'),
            'view_item'                  => __('View Category', 'gymflow'),
            'separate_items_with_commas' => __('Separate categories with commas', 'gymflow'),
            'add_or_remove_items'        => __('Add or remove categories', 'gymflow'),
            'choose_from_most_used'      => __('Choose from the most used', 'gymflow'),
            'popular_items'              => __('Popular Categories', 'gymflow'),
            'search_items'               => __('Search Categories', 'gymflow'),
            'not_found'                  => __('Not Found', 'gymflow'),
            'no_terms'                   => __('No categories', 'gymflow'),
            'items_list'                 => __('Categories list', 'gymflow'),
            'items_list_navigation'      => __('Categories list navigation', 'gymflow'),
        );

        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'show_in_rest'               => true,
            'rest_base'                  => 'class-categories',
            'capabilities'               => array(
                'manage_terms' => 'manage_gymflow',
                'edit_terms'   => 'manage_gymflow',
                'delete_terms' => 'manage_gymflow',
                'assign_terms' => 'edit_gymflow'
            )
        );

        register_taxonomy('gf_class_category', array('gf_class'), $args);
    }

    /**
     * Register Difficulty Level taxonomy
     */
    private function register_difficulty_level_taxonomy() {
        $labels = array(
            'name'                       => __('Difficulty Levels', 'gymflow'),
            'singular_name'              => __('Difficulty Level', 'gymflow'),
            'menu_name'                  => __('Difficulty Levels', 'gymflow'),
            'all_items'                  => __('All Levels', 'gymflow'),
            'parent_item'                => __('Parent Level', 'gymflow'),
            'parent_item_colon'          => __('Parent Level:', 'gymflow'),
            'new_item_name'              => __('New Level Name', 'gymflow'),
            'add_new_item'               => __('Add New Level', 'gymflow'),
            'edit_item'                  => __('Edit Level', 'gymflow'),
            'update_item'                => __('Update Level', 'gymflow'),
            'view_item'                  => __('View Level', 'gymflow'),
            'separate_items_with_commas' => __('Separate levels with commas', 'gymflow'),
            'add_or_remove_items'        => __('Add or remove levels', 'gymflow'),
            'choose_from_most_used'      => __('Choose from the most used', 'gymflow'),
            'popular_items'              => __('Popular Levels', 'gymflow'),
            'search_items'               => __('Search Levels', 'gymflow'),
            'not_found'                  => __('Not Found', 'gymflow'),
            'no_terms'                   => __('No levels', 'gymflow'),
            'items_list'                 => __('Levels list', 'gymflow'),
            'items_list_navigation'      => __('Levels list navigation', 'gymflow'),
        );

        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => false,
            'show_in_rest'               => true,
            'rest_base'                  => 'difficulty-levels',
            'capabilities'               => array(
                'manage_terms' => 'manage_gymflow',
                'edit_terms'   => 'manage_gymflow',
                'delete_terms' => 'manage_gymflow',
                'assign_terms' => 'edit_gymflow'
            )
        );

        register_taxonomy('gf_difficulty_level', array('gf_class'), $args);
    }

    /**
     * Register Trainer Specialty taxonomy
     */
    private function register_trainer_specialty_taxonomy() {
        $labels = array(
            'name'                       => __('Trainer Specialties', 'gymflow'),
            'singular_name'              => __('Trainer Specialty', 'gymflow'),
            'menu_name'                  => __('Specialties', 'gymflow'),
            'all_items'                  => __('All Specialties', 'gymflow'),
            'parent_item'                => __('Parent Specialty', 'gymflow'),
            'parent_item_colon'          => __('Parent Specialty:', 'gymflow'),
            'new_item_name'              => __('New Specialty Name', 'gymflow'),
            'add_new_item'               => __('Add New Specialty', 'gymflow'),
            'edit_item'                  => __('Edit Specialty', 'gymflow'),
            'update_item'                => __('Update Specialty', 'gymflow'),
            'view_item'                  => __('View Specialty', 'gymflow'),
            'separate_items_with_commas' => __('Separate specialties with commas', 'gymflow'),
            'add_or_remove_items'        => __('Add or remove specialties', 'gymflow'),
            'choose_from_most_used'      => __('Choose from the most used', 'gymflow'),
            'popular_items'              => __('Popular Specialties', 'gymflow'),
            'search_items'               => __('Search Specialties', 'gymflow'),
            'not_found'                  => __('Not Found', 'gymflow'),
            'no_terms'                   => __('No specialties', 'gymflow'),
            'items_list'                 => __('Specialties list', 'gymflow'),
            'items_list_navigation'      => __('Specialties list navigation', 'gymflow'),
        );

        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'show_in_rest'               => true,
            'rest_base'                  => 'trainer-specialties',
            'capabilities'               => array(
                'manage_terms' => 'manage_gymflow',
                'edit_terms'   => 'manage_gymflow',
                'delete_terms' => 'manage_gymflow',
                'assign_terms' => 'edit_gymflow'
            )
        );

        register_taxonomy('gf_trainer_specialty', array('gf_trainer'), $args);
    }

    /**
     * Register Equipment Category taxonomy
     */
    private function register_equipment_category_taxonomy() {
        $labels = array(
            'name'                       => __('Equipment Categories', 'gymflow'),
            'singular_name'              => __('Equipment Category', 'gymflow'),
            'menu_name'                  => __('Categories', 'gymflow'),
            'all_items'                  => __('All Categories', 'gymflow'),
            'parent_item'                => __('Parent Category', 'gymflow'),
            'parent_item_colon'          => __('Parent Category:', 'gymflow'),
            'new_item_name'              => __('New Category Name', 'gymflow'),
            'add_new_item'               => __('Add New Category', 'gymflow'),
            'edit_item'                  => __('Edit Category', 'gymflow'),
            'update_item'                => __('Update Category', 'gymflow'),
            'view_item'                  => __('View Category', 'gymflow'),
            'separate_items_with_commas' => __('Separate categories with commas', 'gymflow'),
            'add_or_remove_items'        => __('Add or remove categories', 'gymflow'),
            'choose_from_most_used'      => __('Choose from the most used', 'gymflow'),
            'popular_items'              => __('Popular Categories', 'gymflow'),
            'search_items'               => __('Search Categories', 'gymflow'),
            'not_found'                  => __('Not Found', 'gymflow'),
            'no_terms'                   => __('No categories', 'gymflow'),
            'items_list'                 => __('Categories list', 'gymflow'),
            'items_list_navigation'      => __('Categories list navigation', 'gymflow'),
        );

        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => false,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => false,
            'show_tagcloud'              => false,
            'show_in_rest'               => true,
            'rest_base'                  => 'equipment-categories',
            'capabilities'               => array(
                'manage_terms' => 'manage_gymflow',
                'edit_terms'   => 'manage_gymflow',
                'delete_terms' => 'manage_gymflow',
                'assign_terms' => 'edit_gymflow'
            )
        );

        register_taxonomy('gf_equipment_category', array('gf_equipment'), $args);
    }

    /**
     * Register Booking Status taxonomy
     */
    private function register_booking_status_taxonomy() {
        $labels = array(
            'name'                       => __('Booking Statuses', 'gymflow'),
            'singular_name'              => __('Booking Status', 'gymflow'),
            'menu_name'                  => __('Statuses', 'gymflow'),
            'all_items'                  => __('All Statuses', 'gymflow'),
            'parent_item'                => __('Parent Status', 'gymflow'),
            'parent_item_colon'          => __('Parent Status:', 'gymflow'),
            'new_item_name'              => __('New Status Name', 'gymflow'),
            'add_new_item'               => __('Add New Status', 'gymflow'),
            'edit_item'                  => __('Edit Status', 'gymflow'),
            'update_item'                => __('Update Status', 'gymflow'),
            'view_item'                  => __('View Status', 'gymflow'),
            'separate_items_with_commas' => __('Separate statuses with commas', 'gymflow'),
            'add_or_remove_items'        => __('Add or remove statuses', 'gymflow'),
            'choose_from_most_used'      => __('Choose from the most used', 'gymflow'),
            'popular_items'              => __('Popular Statuses', 'gymflow'),
            'search_items'               => __('Search Statuses', 'gymflow'),
            'not_found'                  => __('Not Found', 'gymflow'),
            'no_terms'                   => __('No statuses', 'gymflow'),
            'items_list'                 => __('Statuses list', 'gymflow'),
            'items_list_navigation'      => __('Statuses list navigation', 'gymflow'),
        );

        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => false,
            'public'                     => false,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => false,
            'show_tagcloud'              => false,
            'show_in_rest'               => true,
            'rest_base'                  => 'booking-statuses',
            'capabilities'               => array(
                'manage_terms' => 'manage_gymflow',
                'edit_terms'   => 'manage_gymflow',
                'delete_terms' => 'manage_gymflow',
                'assign_terms' => 'edit_gymflow'
            )
        );

        register_taxonomy('gf_booking_status', array('gf_booking'), $args);
    }

    /**
     * Create default taxonomy terms
     */
    public static function create_default_terms() {
        self::create_member_type_terms();
        self::create_class_category_terms();
        self::create_difficulty_level_terms();
        self::create_trainer_specialty_terms();
        self::create_equipment_category_terms();
        self::create_booking_status_terms();
    }

    /**
     * Create default member type terms
     */
    private static function create_member_type_terms() {
        $terms = array(
            'individual' => __('Individual', 'gymflow'),
            'family' => __('Family', 'gymflow'),
            'student' => __('Student', 'gymflow'),
            'senior' => __('Senior', 'gymflow'),
            'corporate' => __('Corporate', 'gymflow'),
            'day_pass' => __('Day Pass', 'gymflow'),
            'trial' => __('Trial', 'gymflow')
        );

        foreach ($terms as $slug => $name) {
            if (!term_exists($slug, 'gf_member_type')) {
                wp_insert_term($name, 'gf_member_type', array('slug' => $slug));
            }
        }
    }

    /**
     * Create default class category terms
     */
    private static function create_class_category_terms() {
        $terms = array(
            'strength-training' => __('Strength Training', 'gymflow'),
            'cardio' => __('Cardio', 'gymflow'),
            'yoga' => __('Yoga', 'gymflow'),
            'pilates' => __('Pilates', 'gymflow'),
            'martial-arts' => __('Martial Arts', 'gymflow'),
            'dance' => __('Dance', 'gymflow'),
            'cycling' => __('Cycling', 'gymflow'),
            'aqua-fitness' => __('Aqua Fitness', 'gymflow'),
            'group-fitness' => __('Group Fitness', 'gymflow'),
            'functional-training' => __('Functional Training', 'gymflow'),
            'hiit' => __('HIIT', 'gymflow'),
            'stretching' => __('Stretching', 'gymflow'),
            'mind-body' => __('Mind & Body', 'gymflow')
        );

        foreach ($terms as $slug => $name) {
            if (!term_exists($slug, 'gf_class_category')) {
                wp_insert_term($name, 'gf_class_category', array('slug' => $slug));
            }
        }
    }

    /**
     * Create default difficulty level terms
     */
    private static function create_difficulty_level_terms() {
        $terms = array(
            'beginner' => __('Beginner', 'gymflow'),
            'intermediate' => __('Intermediate', 'gymflow'),
            'advanced' => __('Advanced', 'gymflow'),
            'all-levels' => __('All Levels', 'gymflow')
        );

        foreach ($terms as $slug => $name) {
            if (!term_exists($slug, 'gf_difficulty_level')) {
                wp_insert_term($name, 'gf_difficulty_level', array('slug' => $slug));
            }
        }
    }

    /**
     * Create default trainer specialty terms
     */
    private static function create_trainer_specialty_terms() {
        $terms = array(
            'personal-training' => __('Personal Training', 'gymflow'),
            'group-fitness' => __('Group Fitness', 'gymflow'),
            'weight-training' => __('Weight Training', 'gymflow'),
            'cardiovascular' => __('Cardiovascular', 'gymflow'),
            'yoga-instructor' => __('Yoga Instructor', 'gymflow'),
            'pilates-instructor' => __('Pilates Instructor', 'gymflow'),
            'martial-arts' => __('Martial Arts', 'gymflow'),
            'swimming' => __('Swimming', 'gymflow'),
            'rehabilitation' => __('Rehabilitation', 'gymflow'),
            'nutrition' => __('Nutrition', 'gymflow'),
            'sports-specific' => __('Sports Specific', 'gymflow'),
            'senior-fitness' => __('Senior Fitness', 'gymflow'),
            'youth-fitness' => __('Youth Fitness', 'gymflow')
        );

        foreach ($terms as $slug => $name) {
            if (!term_exists($slug, 'gf_trainer_specialty')) {
                wp_insert_term($name, 'gf_trainer_specialty', array('slug' => $slug));
            }
        }
    }

    /**
     * Create default equipment category terms
     */
    private static function create_equipment_category_terms() {
        $terms = array(
            'cardio-machines' => __('Cardio Machines', 'gymflow'),
            'strength-machines' => __('Strength Machines', 'gymflow'),
            'free-weights' => __('Free Weights', 'gymflow'),
            'functional-training' => __('Functional Training', 'gymflow'),
            'flexibility' => __('Flexibility & Stretching', 'gymflow'),
            'group-fitness' => __('Group Fitness', 'gymflow'),
            'specialty' => __('Specialty Equipment', 'gymflow'),
            'aquatic' => __('Aquatic Equipment', 'gymflow'),
            'recovery' => __('Recovery Equipment', 'gymflow'),
            'assessment' => __('Assessment Tools', 'gymflow')
        );

        foreach ($terms as $slug => $name) {
            if (!term_exists($slug, 'gf_equipment_category')) {
                wp_insert_term($name, 'gf_equipment_category', array('slug' => $slug));
            }
        }
    }

    /**
     * Create default booking status terms
     */
    private static function create_booking_status_terms() {
        $terms = array(
            'confirmed' => __('Confirmed', 'gymflow'),
            'pending' => __('Pending', 'gymflow'),
            'cancelled' => __('Cancelled', 'gymflow'),
            'completed' => __('Completed', 'gymflow'),
            'no-show' => __('No Show', 'gymflow'),
            'waitlist' => __('Waitlist', 'gymflow')
        );

        foreach ($terms as $slug => $name) {
            if (!term_exists($slug, 'gf_booking_status')) {
                wp_insert_term($name, 'gf_booking_status', array('slug' => $slug));
            }
        }
    }
}