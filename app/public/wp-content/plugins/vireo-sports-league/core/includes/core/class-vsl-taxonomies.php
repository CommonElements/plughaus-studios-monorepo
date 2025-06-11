<?php
/**
 * Custom taxonomies for Vireo Sports League
 * 
 * @package Vireo_Sports_League
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class VSL_Taxonomies {
    
    /**
     * Initialize taxonomies
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'register_taxonomies'));
    }
    
    /**
     * Register all custom taxonomies
     */
    public static function register_taxonomies() {
        self::register_age_division();
        self::register_league_type();
        self::register_match_type();
        self::register_player_position();
    }
    
    /**
     * Register Age Division taxonomy
     */
    private static function register_age_division() {
        $labels = array(
            'name' => _x('Age Divisions', 'Taxonomy General Name', 'vireo-league'),
            'singular_name' => _x('Age Division', 'Taxonomy Singular Name', 'vireo-league'),
            'menu_name' => __('Age Divisions', 'vireo-league'),
            'all_items' => __('All Age Divisions', 'vireo-league'),
            'parent_item' => __('Parent Age Division', 'vireo-league'),
            'parent_item_colon' => __('Parent Age Division:', 'vireo-league'),
            'new_item_name' => __('New Age Division Name', 'vireo-league'),
            'add_new_item' => __('Add New Age Division', 'vireo-league'),
            'edit_item' => __('Edit Age Division', 'vireo-league'),
            'update_item' => __('Update Age Division', 'vireo-league'),
            'view_item' => __('View Age Division', 'vireo-league'),
            'separate_items_with_commas' => __('Separate age divisions with commas', 'vireo-league'),
            'add_or_remove_items' => __('Add or remove age divisions', 'vireo-league'),
            'choose_from_most_used' => __('Choose from the most used', 'vireo-league'),
            'popular_items' => __('Popular Age Divisions', 'vireo-league'),
            'search_items' => __('Search Age Divisions', 'vireo-league'),
            'not_found' => __('Not Found', 'vireo-league'),
            'no_terms' => __('No age divisions', 'vireo-league'),
            'items_list' => __('Age divisions list', 'vireo-league'),
            'items_list_navigation' => __('Age divisions list navigation', 'vireo-league'),
        );
        
        $args = array(
            'labels' => $labels,
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud' => false,
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'age-division'),
        );
        
        register_taxonomy('vsl_age_division', array('vsl_league', 'vsl_team', 'vsl_player'), $args);
        
        // Add default terms
        self::add_default_age_divisions();
    }
    
    /**
     * Register League Type taxonomy
     */
    private static function register_league_type() {
        $labels = array(
            'name' => _x('League Types', 'Taxonomy General Name', 'vireo-league'),
            'singular_name' => _x('League Type', 'Taxonomy Singular Name', 'vireo-league'),
            'menu_name' => __('League Types', 'vireo-league'),
            'all_items' => __('All League Types', 'vireo-league'),
            'parent_item' => __('Parent League Type', 'vireo-league'),
            'parent_item_colon' => __('Parent League Type:', 'vireo-league'),
            'new_item_name' => __('New League Type Name', 'vireo-league'),
            'add_new_item' => __('Add New League Type', 'vireo-league'),
            'edit_item' => __('Edit League Type', 'vireo-league'),
            'update_item' => __('Update League Type', 'vireo-league'),
            'view_item' => __('View League Type', 'vireo-league'),
            'separate_items_with_commas' => __('Separate league types with commas', 'vireo-league'),
            'add_or_remove_items' => __('Add or remove league types', 'vireo-league'),
            'choose_from_most_used' => __('Choose from the most used', 'vireo-league'),
            'popular_items' => __('Popular League Types', 'vireo-league'),
            'search_items' => __('Search League Types', 'vireo-league'),
            'not_found' => __('Not Found', 'vireo-league'),
            'no_terms' => __('No league types', 'vireo-league'),
            'items_list' => __('League types list', 'vireo-league'),
            'items_list_navigation' => __('League types list navigation', 'vireo-league'),
        );
        
        $args = array(
            'labels' => $labels,
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud' => false,
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'league-type'),
        );
        
        register_taxonomy('vsl_league_type', array('vsl_league'), $args);
        
        // Add default terms
        self::add_default_league_types();
    }
    
    /**
     * Register Match Type taxonomy
     */
    private static function register_match_type() {
        $labels = array(
            'name' => _x('Match Types', 'Taxonomy General Name', 'vireo-league'),
            'singular_name' => _x('Match Type', 'Taxonomy Singular Name', 'vireo-league'),
            'menu_name' => __('Match Types', 'vireo-league'),
            'all_items' => __('All Match Types', 'vireo-league'),
            'parent_item' => __('Parent Match Type', 'vireo-league'),
            'parent_item_colon' => __('Parent Match Type:', 'vireo-league'),
            'new_item_name' => __('New Match Type Name', 'vireo-league'),
            'add_new_item' => __('Add New Match Type', 'vireo-league'),
            'edit_item' => __('Edit Match Type', 'vireo-league'),
            'update_item' => __('Update Match Type', 'vireo-league'),
            'view_item' => __('View Match Type', 'vireo-league'),
            'separate_items_with_commas' => __('Separate match types with commas', 'vireo-league'),
            'add_or_remove_items' => __('Add or remove match types', 'vireo-league'),
            'choose_from_most_used' => __('Choose from the most used', 'vireo-league'),
            'popular_items' => __('Popular Match Types', 'vireo-league'),
            'search_items' => __('Search Match Types', 'vireo-league'),
            'not_found' => __('Not Found', 'vireo-league'),
            'no_terms' => __('No match types', 'vireo-league'),
            'items_list' => __('Match types list', 'vireo-league'),
            'items_list_navigation' => __('Match types list navigation', 'vireo-league'),
        );
        
        $args = array(
            'labels' => $labels,
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud' => false,
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'match-type'),
        );
        
        register_taxonomy('vsl_match_type', array('vsl_match'), $args);
        
        // Add default terms
        self::add_default_match_types();
    }
    
    /**
     * Register Player Position taxonomy
     */
    private static function register_player_position() {
        $labels = array(
            'name' => _x('Player Positions', 'Taxonomy General Name', 'vireo-league'),
            'singular_name' => _x('Player Position', 'Taxonomy Singular Name', 'vireo-league'),
            'menu_name' => __('Positions', 'vireo-league'),
            'all_items' => __('All Positions', 'vireo-league'),
            'parent_item' => __('Parent Position', 'vireo-league'),
            'parent_item_colon' => __('Parent Position:', 'vireo-league'),
            'new_item_name' => __('New Position Name', 'vireo-league'),
            'add_new_item' => __('Add New Position', 'vireo-league'),
            'edit_item' => __('Edit Position', 'vireo-league'),
            'update_item' => __('Update Position', 'vireo-league'),
            'view_item' => __('View Position', 'vireo-league'),
            'separate_items_with_commas' => __('Separate positions with commas', 'vireo-league'),
            'add_or_remove_items' => __('Add or remove positions', 'vireo-league'),
            'choose_from_most_used' => __('Choose from the most used', 'vireo-league'),
            'popular_items' => __('Popular Positions', 'vireo-league'),
            'search_items' => __('Search Positions', 'vireo-league'),
            'not_found' => __('Not Found', 'vireo-league'),
            'no_terms' => __('No positions', 'vireo-league'),
            'items_list' => __('Positions list', 'vireo-league'),
            'items_list_navigation' => __('Positions list navigation', 'vireo-league'),
        );
        
        $args = array(
            'labels' => $labels,
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud' => false,
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'position'),
        );
        
        register_taxonomy('vsl_player_position', array('vsl_player'), $args);
    }
    
    /**
     * Add default age divisions
     */
    private static function add_default_age_divisions() {
        $divisions = VSL_Utilities::get_age_divisions();
        
        foreach ($divisions as $slug => $name) {
            if (!term_exists($slug, 'vsl_age_division')) {
                wp_insert_term($name, 'vsl_age_division', array('slug' => $slug));
            }
        }
    }
    
    /**
     * Add default league types
     */
    private static function add_default_league_types() {
        $types = array(
            'recreational' => __('Recreational', 'vireo-league'),
            'competitive' => __('Competitive', 'vireo-league'),
            'tournament' => __('Tournament', 'vireo-league'),
            'friendly' => __('Friendly', 'vireo-league'),
            'cup' => __('Cup Competition', 'vireo-league'),
            'playoff' => __('Playoff', 'vireo-league'),
        );
        
        foreach ($types as $slug => $name) {
            if (!term_exists($slug, 'vsl_league_type')) {
                wp_insert_term($name, 'vsl_league_type', array('slug' => $slug));
            }
        }
    }
    
    /**
     * Add default match types
     */
    private static function add_default_match_types() {
        $types = array(
            'regular_season' => __('Regular Season', 'vireo-league'),
            'playoff' => __('Playoff', 'vireo-league'),
            'championship' => __('Championship', 'vireo-league'),
            'friendly' => __('Friendly', 'vireo-league'),
            'exhibition' => __('Exhibition', 'vireo-league'),
            'tournament' => __('Tournament', 'vireo-league'),
        );
        
        foreach ($types as $slug => $name) {
            if (!term_exists($slug, 'vsl_match_type')) {
                wp_insert_term($name, 'vsl_match_type', array('slug' => $slug));
            }
        }
    }
}