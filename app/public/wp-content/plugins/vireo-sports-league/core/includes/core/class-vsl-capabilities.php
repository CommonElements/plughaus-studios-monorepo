<?php
/**
 * User capabilities and roles for PlugHaus Sports League
 * 
 * @package PlugHaus_Sports_League
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class PSL_Capabilities {
    
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
            'manage_psl_leagues',
            'edit_psl_leagues',
            'delete_psl_leagues',
            'view_psl_leagues',
            
            // Team management
            'manage_psl_teams',
            'edit_psl_teams',
            'delete_psl_teams',
            'view_psl_teams',
            
            // Player management
            'manage_psl_players',
            'edit_psl_players',
            'delete_psl_players',
            'view_psl_players',
            
            // Match management
            'manage_psl_matches',
            'edit_psl_matches',
            'delete_psl_matches',
            'view_psl_matches',
            'edit_match_results',
            
            // Season management
            'manage_psl_seasons',
            'edit_psl_seasons',
            'delete_psl_seasons',
            'view_psl_seasons',
            
            // Statistics management
            'manage_psl_statistics',
            'edit_psl_statistics',
            'view_psl_statistics',
            
            // Settings
            'manage_psl_settings',
            'view_psl_reports',
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
            'manage_psl_leagues' => true,
            'edit_psl_leagues' => true,
            'delete_psl_leagues' => true,
            'view_psl_leagues' => true,
            
            // Team management
            'manage_psl_teams' => true,
            'edit_psl_teams' => true,
            'delete_psl_teams' => true,
            'view_psl_teams' => true,
            
            // Player management
            'manage_psl_players' => true,
            'edit_psl_players' => true,
            'delete_psl_players' => true,
            'view_psl_players' => true,
            
            // Match management
            'manage_psl_matches' => true,
            'edit_psl_matches' => true,
            'delete_psl_matches' => true,
            'view_psl_matches' => true,
            'edit_match_results' => true,
            
            // Season management
            'manage_psl_seasons' => true,
            'edit_psl_seasons' => true,
            'delete_psl_seasons' => true,
            'view_psl_seasons' => true,
            
            // Statistics
            'manage_psl_statistics' => true,
            'edit_psl_statistics' => true,
            'view_psl_statistics' => true,
            
            // Reports
            'view_psl_reports' => true,
        );
        
        add_role('psl_league_admin', __('League Administrator', 'plughaus-league'), $capabilities);
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
            'view_psl_leagues' => true,
            'view_psl_teams' => true,
            'view_psl_players' => true,
            'view_psl_matches' => true,
            'view_psl_seasons' => true,
            'view_psl_statistics' => true,
            
            // Edit own team only
            'edit_psl_teams' => true,
            'edit_psl_players' => true,
            'edit_psl_statistics' => true,
            
            // Limited match editing
            'edit_match_results' => true,
        );
        
        add_role('psl_coach', __('Team Coach', 'plughaus-league'), $capabilities);
    }
    
    /**
     * Add Stat Editor role
     */
    private static function add_stat_editor_role() {
        $capabilities = array(
            // WordPress core
            'read' => true,
            
            // View capabilities
            'view_psl_leagues' => true,
            'view_psl_teams' => true,
            'view_psl_players' => true,
            'view_psl_matches' => true,
            'view_psl_seasons' => true,
            'view_psl_statistics' => true,
            
            // Statistics editing
            'edit_psl_statistics' => true,
            'edit_match_results' => true,
        );
        
        add_role('psl_stat_editor', __('Statistics Editor', 'plughaus-league'), $capabilities);
    }
    
    /**
     * Remove plugin roles and capabilities (for uninstall)
     */
    public static function remove_custom_roles_and_capabilities() {
        // Remove custom roles
        remove_role('psl_league_admin');
        remove_role('psl_coach');
        remove_role('psl_stat_editor');
        
        // Remove capabilities from administrator
        $admin = get_role('administrator');
        if ($admin) {
            $capabilities = array(
                'manage_psl_leagues',
                'edit_psl_leagues',
                'delete_psl_leagues',
                'view_psl_leagues',
                'manage_psl_teams',
                'edit_psl_teams',
                'delete_psl_teams',
                'view_psl_teams',
                'manage_psl_players',
                'edit_psl_players',
                'delete_psl_players',
                'view_psl_players',
                'manage_psl_matches',
                'edit_psl_matches',
                'delete_psl_matches',
                'view_psl_matches',
                'edit_match_results',
                'manage_psl_seasons',
                'edit_psl_seasons',
                'delete_psl_seasons',
                'view_psl_seasons',
                'manage_psl_statistics',
                'edit_psl_statistics',
                'view_psl_statistics',
                'manage_psl_settings',
                'view_psl_reports',
            );
            
            foreach ($capabilities as $cap) {
                $admin->remove_cap($cap);
            }
        }
    }
}