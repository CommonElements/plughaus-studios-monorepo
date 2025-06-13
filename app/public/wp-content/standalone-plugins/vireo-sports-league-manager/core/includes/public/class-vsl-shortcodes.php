<?php
/**
 * Shortcodes for Vireo Sports League
 * 
 * @package Vireo_Sports_League
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class VSL_Shortcodes {
    
    /**
     * Initialize shortcodes
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'register_shortcodes'));
    }
    
    /**
     * Register all shortcodes
     */
    public static function register_shortcodes() {
        add_shortcode('vsl_standings', array(__CLASS__, 'standings_shortcode'));
        add_shortcode('vsl_matches', array(__CLASS__, 'matches_shortcode'));
        add_shortcode('vsl_teams', array(__CLASS__, 'teams_shortcode'));
        add_shortcode('vsl_players', array(__CLASS__, 'players_shortcode'));
    }
    
    /**
     * Standings shortcode
     */
    public static function standings_shortcode($atts) {
        $atts = shortcode_atts(array(
            'league' => '',
            'season' => '',
            'limit' => 10,
        ), $atts);
        
        return '<div class="vsl-standings">Standings will be displayed here</div>';
    }
    
    /**
     * Matches shortcode
     */
    public static function matches_shortcode($atts) {
        $atts = shortcode_atts(array(
            'league' => '',
            'team' => '',
            'limit' => 10,
        ), $atts);
        
        return '<div class="vsl-matches">Matches will be displayed here</div>';
    }
    
    /**
     * Teams shortcode
     */
    public static function teams_shortcode($atts) {
        $atts = shortcode_atts(array(
            'league' => '',
            'limit' => 10,
        ), $atts);
        
        return '<div class="vsl-teams">Teams will be displayed here</div>';
    }
    
    /**
     * Players shortcode
     */
    public static function players_shortcode($atts) {
        $atts = shortcode_atts(array(
            'team' => '',
            'position' => '',
            'limit' => 10,
        ), $atts);
        
        return '<div class="vsl-players">Players will be displayed here</div>';
    }
}