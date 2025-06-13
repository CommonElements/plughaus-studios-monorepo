<?php
/**
 * Plugin deactivator for Vireo Sports League
 * 
 * @package Vireo_Sports_League
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class VSL_Deactivator {
    
    /**
     * Deactivate the plugin
     */
    public static function deactivate() {
        // Clear scheduled events
        self::clear_scheduled_events();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Log deactivation
        if (class_exists('VSL_Utilities')) {
            VSL_Utilities::log_activity('system', 'Plugin deactivated', 0, get_current_user_id());
        }
        
        // Set deactivation timestamp
        update_option('vsl_deactivation_time', time());
    }
    
    /**
     * Clear all scheduled events
     */
    private static function clear_scheduled_events() {
        // Clear standings update
        $timestamp = wp_next_scheduled('vsl_update_standings');
        if ($timestamp) {
            wp_unschedule_event($timestamp, 'vsl_update_standings');
        }
        
        // Clear daily cleanup
        $timestamp = wp_next_scheduled('vsl_daily_cleanup');
        if ($timestamp) {
            wp_unschedule_event($timestamp, 'vsl_daily_cleanup');
        }
        
        // Clear statistics calculation
        $timestamp = wp_next_scheduled('vsl_calculate_statistics');
        if ($timestamp) {
            wp_unschedule_event($timestamp, 'vsl_calculate_statistics');
        }
        
        // Clear any other plugin-specific events
        wp_clear_scheduled_hook('vsl_update_standings');
        wp_clear_scheduled_hook('vsl_daily_cleanup');
        wp_clear_scheduled_hook('vsl_calculate_statistics');
    }
}