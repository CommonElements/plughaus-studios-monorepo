<?php
/**
 * Custom taxonomies for PlugHaus Sports League
 * 
 * @package PlugHaus_Sports_League
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class PSL_Taxonomies {
    
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
            'name' => _x('Age Divisions', 'Taxonomy General Name', 'plughaus-league'),
            'singular_name' => _x('Age Division', 'Taxonomy Singular Name', 'plughaus-league'),
            'menu_name' => __('Age Divisions', 'plughaus-league'),
            'all_items' => __('All Age Divisions', 'plughaus-league'),
            'parent_item' => __('Parent Age Division', 'plughaus-league'),
            'parent_item_colon' => __('Parent Age Division:', 'plughaus-league'),
            'new_item_name' => __('New Age Division Name', 'plughaus-league'),
            'add_new_item' => __('Add New Age Division', 'plughaus-league'),
            'edit_item' => __('Edit Age Division', 'plughaus-league'),
            'update_item' => __('Update Age Division', 'plughaus-league'),
            'view_item' => __('View Age Division', 'plughaus-league'),
            'separate_items_with_commas' => __('Separate age divisions with commas', 'plughaus-league'),
            'add_or_remove_items' => __('Add or remove age divisions', 'plughaus-league'),
            'choose_from_most_used' => __('Choose from the most used', 'plughaus-league'),
            'popular_items' => __('Popular Age Divisions', 'plughaus-league'),
            'search_items' => __('Search Age Divisions', 'plughaus-league'),
            'not_found' => __('Not Found', 'plughaus-league'),
            'no_terms' => __('No age divisions', 'plughaus-league'),
            'items_list' => __('Age divisions list', 'plughaus-league'),
            'items_list_navigation' => __('Age divisions list navigation', 'plughaus-league'),
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
        
        register_taxonomy('psl_age_division', array('psl_league', 'psl_team', 'psl_player'), $args);
        
        // Add default terms
        self::add_default_age_divisions();
    }
    
    /**
     * Register League Type taxonomy
     */
    private static function register_league_type() {
        $labels = array(
            'name' => _x('League Types', 'Taxonomy General Name', 'plughaus-league'),
            'singular_name' => _x('League Type', 'Taxonomy Singular Name', 'plughaus-league'),
            'menu_name' => __('League Types', 'plughaus-league'),
            'all_items' => __('All League Types', 'plughaus-league'),
            'parent_item' => __('Parent League Type', 'plughaus-league'),
            'parent_item_colon' => __('Parent League Type:', 'plughaus-league'),
            'new_item_name' => __('New League Type Name', 'plughaus-league'),
            'add_new_item' => __('Add New League Type', 'plughaus-league'),
            'edit_item' => __('Edit League Type', 'plughaus-league'),
            'update_item' => __('Update League Type', 'plughaus-league'),
            'view_item' => __('View League Type', 'plughaus-league'),
            'separate_items_with_commas' => __('Separate league types with commas', 'plughaus-league'),
            'add_or_remove_items' => __('Add or remove league types', 'plughaus-league'),
            'choose_from_most_used' => __('Choose from the most used', 'plughaus-league'),
            'popular_items' => __('Popular League Types', 'plughaus-league'),
            'search_items' => __('Search League Types', 'plughaus-league'),
            'not_found' => __('Not Found', 'plughaus-league'),
            'no_terms' => __('No league types', 'plughaus-league'),
            'items_list' => __('League types list', 'plughaus-league'),
            'items_list_navigation' => __('League types list navigation', 'plughaus-league'),
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
        
        register_taxonomy('psl_league_type', array('psl_league'), $args);
        
        // Add default terms
        self::add_default_league_types();
    }
    
    /**
     * Register Match Type taxonomy
     */
    private static function register_match_type() {
        $labels = array(
            'name' => _x('Match Types', 'Taxonomy General Name', 'plughaus-league'),
            'singular_name' => _x('Match Type', 'Taxonomy Singular Name', 'plughaus-league'),
            'menu_name' => __('Match Types', 'plughaus-league'),
            'all_items' => __('All Match Types', 'plughaus-league'),
            'parent_item' => __('Parent Match Type', 'plughaus-league'),
            'parent_item_colon' => __('Parent Match Type:', 'plughaus-league'),
            'new_item_name' => __('New Match Type Name', 'plughaus-league'),
            'add_new_item' => __('Add New Match Type', 'plughaus-league'),
            'edit_item' => __('Edit Match Type', 'plughaus-league'),
            'update_item' => __('Update Match Type', 'plughaus-league'),
            'view_item' => __('View Match Type', 'plughaus-league'),
            'separate_items_with_commas' => __('Separate match types with commas', 'plughaus-league'),
            'add_or_remove_items' => __('Add or remove match types', 'plughaus-league'),
            'choose_from_most_used' => __('Choose from the most used', 'plughaus-league'),
            'popular_items' => __('Popular Match Types', 'plughaus-league'),
            'search_items' => __('Search Match Types', 'plughaus-league'),
            'not_found' => __('Not Found', 'plughaus-league'),
            'no_terms' => __('No match types', 'plughaus-league'),
            'items_list' => __('Match types list', 'plughaus-league'),
            'items_list_navigation' => __('Match types list navigation', 'plughaus-league'),
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
        
        register_taxonomy('psl_match_type', array('psl_match'), $args);
        
        // Add default terms
        self::add_default_match_types();
    }
    
    /**
     * Register Player Position taxonomy
     */
    private static function register_player_position() {
        $labels = array(
            'name' => _x('Player Positions', 'Taxonomy General Name', 'plughaus-league'),
            'singular_name' => _x('Player Position', 'Taxonomy Singular Name', 'plughaus-league'),
            'menu_name' => __('Positions', 'plughaus-league'),
            'all_items' => __('All Positions', 'plughaus-league'),
            'parent_item' => __('Parent Position', 'plughaus-league'),
            'parent_item_colon' => __('Parent Position:', 'plughaus-league'),
            'new_item_name' => __('New Position Name', 'plughaus-league'),
            'add_new_item' => __('Add New Position', 'plughaus-league'),
            'edit_item' => __('Edit Position', 'plughaus-league'),
            'update_item' => __('Update Position', 'plughaus-league'),
            'view_item' => __('View Position', 'plughaus-league'),
            'separate_items_with_commas' => __('Separate positions with commas', 'plughaus-league'),
            'add_or_remove_items' => __('Add or remove positions', 'plughaus-league'),
            'choose_from_most_used' => __('Choose from the most used', 'plughaus-league'),
            'popular_items' => __('Popular Positions', 'plughaus-league'),
            'search_items' => __('Search Positions', 'plughaus-league'),
            'not_found' => __('Not Found', 'plughaus-league'),
            'no_terms' => __('No positions', 'plughaus-league'),
            'items_list' => __('Positions list', 'plughaus-league'),
            'items_list_navigation' => __('Positions list navigation', 'plughaus-league'),
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
        
        register_taxonomy('psl_player_position', array('psl_player'), $args);
    }
    
    /**
     * Add default age divisions
     */
    private static function add_default_age_divisions() {
        $divisions = PSL_Utilities::get_age_divisions();
        
        foreach ($divisions as $slug => $name) {
            if (!term_exists($slug, 'psl_age_division')) {
                wp_insert_term($name, 'psl_age_division', array('slug' => $slug));
            }
        }
    }
    
    /**
     * Add default league types
     */
    private static function add_default_league_types() {
        $types = array(
            'recreational' => __('Recreational', 'plughaus-league'),
            'competitive' => __('Competitive', 'plughaus-league'),
            'tournament' => __('Tournament', 'plughaus-league'),
            'friendly' => __('Friendly', 'plughaus-league'),
            'cup' => __('Cup Competition', 'plughaus-league'),
            'playoff' => __('Playoff', 'plughaus-league'),
        );
        
        foreach ($types as $slug => $name) {
            if (!term_exists($slug, 'psl_league_type')) {
                wp_insert_term($name, 'psl_league_type', array('slug' => $slug));
            }
        }
    }
    
    /**
     * Add default match types
     */
    private static function add_default_match_types() {
        $types = array(
            'regular_season' => __('Regular Season', 'plughaus-league'),
            'playoff' => __('Playoff', 'plughaus-league'),
            'championship' => __('Championship', 'plughaus-league'),
            'friendly' => __('Friendly', 'plughaus-league'),
            'exhibition' => __('Exhibition', 'plughaus-league'),
            'tournament' => __('Tournament', 'plughaus-league'),
        );
        
        foreach ($types as $slug => $name) {
            if (!term_exists($slug, 'psl_match_type')) {
                wp_insert_term($name, 'psl_match_type', array('slug' => $slug));
            }
        }
    }
}