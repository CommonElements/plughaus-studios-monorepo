<?php
/**
 * EquipRent Pro Taxonomies
 *
 * @package EquipRent_Pro
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handles taxonomies for equipment rental
 */
class ER_Taxonomies {

    /**
     * Initialize the class
     */
    public static function init() {
        // Taxonomies are already registered in ER_Post_Types
        // This class can be extended for additional taxonomy operations
        add_action('init', array(__CLASS__, 'create_default_terms'));
    }

    /**
     * Create default taxonomy terms
     */
    public static function create_default_terms() {
        // Only run once after plugin activation
        if (get_option('equiprent_default_terms_created')) {
            return;
        }

        // Create default equipment categories
        self::create_default_categories();

        // Create default equipment conditions
        self::create_default_conditions();

        // Create default locations (if needed)
        self::create_default_locations();

        // Mark as completed
        update_option('equiprent_default_terms_created', true);
    }

    /**
     * Create default equipment categories
     */
    private static function create_default_categories() {
        $categories = array(
            'construction' => __('Construction Equipment', 'equiprent-pro'),
            'power-tools' => __('Power Tools', 'equiprent-pro'),
            'hand-tools' => __('Hand Tools', 'equiprent-pro'),
            'party-events' => __('Party & Events', 'equiprent-pro'),
            'audio-visual' => __('Audio Visual', 'equiprent-pro'),
            'automotive' => __('Automotive', 'equiprent-pro'),
            'landscaping' => __('Landscaping', 'equiprent-pro'),
            'cleaning' => __('Cleaning Equipment', 'equiprent-pro'),
            'generators' => __('Generators & Power', 'equiprent-pro'),
            'lifting' => __('Lifting Equipment', 'equiprent-pro'),
        );

        foreach ($categories as $slug => $name) {
            if (!term_exists($slug, 'equipment_category')) {
                wp_insert_term($name, 'equipment_category', array(
                    'slug' => $slug,
                ));
            }
        }
    }

    /**
     * Create default equipment conditions
     */
    private static function create_default_conditions() {
        $conditions = array(
            'excellent' => __('Excellent', 'equiprent-pro'),
            'good' => __('Good', 'equiprent-pro'),
            'fair' => __('Fair', 'equiprent-pro'),
            'poor' => __('Poor', 'equiprent-pro'),
            'needs-repair' => __('Needs Repair', 'equiprent-pro'),
        );

        foreach ($conditions as $slug => $name) {
            if (!term_exists($slug, 'equipment_condition')) {
                wp_insert_term($name, 'equipment_condition', array(
                    'slug' => $slug,
                ));
            }
        }
    }

    /**
     * Create default locations
     */
    private static function create_default_locations() {
        $locations = array(
            'warehouse-a' => __('Warehouse A', 'equiprent-pro'),
            'warehouse-b' => __('Warehouse B', 'equiprent-pro'),
            'showroom' => __('Showroom', 'equiprent-pro'),
            'on-rental' => __('On Rental', 'equiprent-pro'),
            'maintenance' => __('In Maintenance', 'equiprent-pro'),
        );

        foreach ($locations as $slug => $name) {
            if (!term_exists($slug, 'equipment_location')) {
                wp_insert_term($name, 'equipment_location', array(
                    'slug' => $slug,
                ));
            }
        }
    }

    /**
     * Get taxonomy terms for select options
     */
    public static function get_terms_for_select($taxonomy, $placeholder = '') {
        $terms = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
        ));

        $options = array();
        
        if ($placeholder) {
            $options[''] = $placeholder;
        }

        if (!is_wp_error($terms)) {
            foreach ($terms as $term) {
                $options[$term->term_id] = $term->name;
            }
        }

        return $options;
    }

    /**
     * Get equipment categories for select
     */
    public static function get_categories_for_select($placeholder = '') {
        if (!$placeholder) {
            $placeholder = __('Select Category', 'equiprent-pro');
        }
        return self::get_terms_for_select('equipment_category', $placeholder);
    }

    /**
     * Get equipment brands for select
     */
    public static function get_brands_for_select($placeholder = '') {
        if (!$placeholder) {
            $placeholder = __('Select Brand', 'equiprent-pro');
        }
        return self::get_terms_for_select('equipment_brand', $placeholder);
    }

    /**
     * Get equipment conditions for select
     */
    public static function get_conditions_for_select($placeholder = '') {
        if (!$placeholder) {
            $placeholder = __('Select Condition', 'equiprent-pro');
        }
        return self::get_terms_for_select('equipment_condition', $placeholder);
    }

    /**
     * Get equipment locations for select
     */
    public static function get_locations_for_select($placeholder = '') {
        if (!$placeholder) {
            $placeholder = __('Select Location', 'equiprent-pro');
        }
        return self::get_terms_for_select('equipment_location', $placeholder);
    }
}