<?php
/**
 * User capabilities and roles for Vireo Sports League
 * 
 * @package Vireo_Sports_League
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class VSL_Capabilities {
    
    /**
     * Initialize capabilities
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'add_custom_roles_and_capabilities'));
    }
    
    /**
     * Add custom roles and capabilities
     */
    public static function add_custom_roles_and_capabilities() {
        // Add capabilities to administrator
        $admin = get_role('administrator');
        if ($admin) {
            self::add_admin_capabilities($admin);
        }
        
        // Add League Admin role
        self::add_league_admin_role();
        
        // Add Coach role
        self::add_coach_role();
        
        // Add Stat Editor role
        self::add_stat_editor_role();
    }
    
    /**
     * Add admin capabilities
     */
    private static function add_admin_capabilities($role) {
        $capabilities = array(
            // League management
            'manage_vsl_leagues',
            'edit_vsl_leagues',
            'delete_vsl_leagues',
            'view_vsl_leagues',
            
            // Team management
            'manage_vsl_teams',
            'edit_vsl_teams',
            'delete_vsl_teams',
            'view_vsl_teams',
            
            // Player management
            'manage_vsl_players',
            'edit_vsl_players',
            'delete_vsl_players',
            'view_vsl_players',
            
            // Match management
            'manage_vsl_matches',
            'edit_vsl_matches',
            'delete_vsl_matches',
            'view_vsl_matches',
            'edit_match_results',
            
            // Season management
            'manage_vsl_seasons',
            'edit_vsl_seasons',
            'delete_vsl_seasons',
            'view_vsl_seasons',
            
            // Statistics management
            'manage_vsl_statistics',
            'edit_vsl_statistics',
            'view_vsl_statistics',
            
            // Settings
            'manage_vsl_settings',
            'view_vsl_reports',
        );
        
        foreach ($capabilities as $cap) {
            $role->add_cap($cap);
        }
    }
    
    /**
     * Add League Admin role
     */
    private static function add_league_admin_role() {
        $capabilities = array(
            // WordPress core
            'read' => true,
            'upload_files' => true,
            
            // League management
            'manage_vsl_leagues' => true,
            'edit_vsl_leagues' => true,
            'delete_vsl_leagues' => true,
            'view_vsl_leagues' => true,
            
            // Team management
            'manage_vsl_teams' => true,
            'edit_vsl_teams' => true,
            'delete_vsl_teams' => true,
            'view_vsl_teams' => true,
            
            // Player management
            'manage_vsl_players' => true,
            'edit_vsl_players' => true,
            'delete_vsl_players' => true,
            'view_vsl_players' => true,
            
            // Match management
            'manage_vsl_matches' => true,
            'edit_vsl_matches' => true,
            'delete_vsl_matches' => true,
            'view_vsl_matches' => true,
            'edit_match_results' => true,
            
            // Season management
            'manage_vsl_seasons' => true,
            'edit_vsl_seasons' => true,
            'delete_vsl_seasons' => true,
            'view_vsl_seasons' => true,
            
            // Statistics
            'manage_vsl_statistics' => true,
            'edit_vsl_statistics' => true,
            'view_vsl_statistics' => true,
            
            // Reports
            'view_vsl_reports' => true,
        );
        
        add_role('vsl_league_admin', __('League Administrator', 'vireo-league'), $capabilities);
    }
    
    /**
     * Add Coach role
     */
    private static function add_coach_role() {
        $capabilities = array(
            // WordPress core
            'read' => true,
            'upload_files' => true,
            
            // View capabilities
            'view_vsl_leagues' => true,
            'view_vsl_teams' => true,
            'view_vsl_players' => true,
            'view_vsl_matches' => true,
            'view_vsl_seasons' => true,
            'view_vsl_statistics' => true,
            
            // Edit own team only
            'edit_vsl_teams' => true,
            'edit_vsl_players' => true,
            'edit_vsl_statistics' => true,
            
            // Limited match editing
            'edit_match_results' => true,
        );
        
        add_role('vsl_coach', __('Team Coach', 'vireo-league'), $capabilities);
    }
    
    /**
     * Add Stat Editor role
     */
    private static function add_stat_editor_role() {
        $capabilities = array(
            // WordPress core
            'read' => true,
            
            // View capabilities
            'view_vsl_leagues' => true,
            'view_vsl_teams' => true,
            'view_vsl_players' => true,
            'view_vsl_matches' => true,
            'view_vsl_seasons' => true,
            'view_vsl_statistics' => true,
            
            // Statistics editing
            'edit_vsl_statistics' => true,
            'edit_match_results' => true,
        );
        
        add_role('vsl_stat_editor', __('Statistics Editor', 'vireo-league'), $capabilities);
    }
    
    /**
     * Remove plugin roles and capabilities (for uninstall)
     */
    public static function remove_custom_roles_and_capabilities() {
        // Remove custom roles
        remove_role('vsl_league_admin');
        remove_role('vsl_coach');
        remove_role('vsl_stat_editor');
        
        // Remove capabilities from administrator
        $admin = get_role('administrator');
        if ($admin) {
            $capabilities = array(
                'manage_vsl_leagues',
                'edit_vsl_leagues',
                'delete_vsl_leagues',
                'view_vsl_leagues',
                'manage_vsl_teams',
                'edit_vsl_teams',
                'delete_vsl_teams',
                'view_vsl_teams',
                'manage_vsl_players',
                'edit_vsl_players',
                'delete_vsl_players',
                'view_vsl_players',
                'manage_vsl_matches',
                'edit_vsl_matches',
                'delete_vsl_matches',
                'view_vsl_matches',
                'edit_match_results',
                'manage_vsl_seasons',
                'edit_vsl_seasons',
                'delete_vsl_seasons',
                'view_vsl_seasons',
                'manage_vsl_statistics',
                'edit_vsl_statistics',
                'view_vsl_statistics',
                'manage_vsl_settings',
                'view_vsl_reports',
            );
            
            foreach ($capabilities as $cap) {
                $admin->remove_cap($cap);
            }
        }
    }
}