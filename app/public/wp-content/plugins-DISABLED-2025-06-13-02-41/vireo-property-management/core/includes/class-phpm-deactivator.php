<?php
/**
 * Plugin Deactivator for PlugHaus Property Management
 * Handles plugin deactivation tasks
 */

if (!defined('ABSPATH')) {
    exit;
}

class PHPM_Deactivator {
    
    /**
     * Deactivate the plugin
     */
    public static function deactivate() {
        // Clear scheduled events
        self::clear_scheduled_events();
        
        // Clear transients
        self::clear_transients();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Log deactivation
        if (class_exists('PHPM_Utilities')) {
            PHPM_Utilities::log('Plugin deactivated', 'info');
        }
    }
    
    /**
     * Clear all scheduled events
     */
    private static function clear_scheduled_events() {
        $events = array(
            'phpm_daily_lease_check',
            'phpm_weekly_cleanup',
            'phpm_monthly_reports',
            'phpm_weekly_reports'
        );
        
        foreach ($events as $event) {
            $timestamp = wp_next_scheduled($event);
            if ($timestamp) {
                wp_unschedule_event($timestamp, $event);
            }
        }
    }
    
    /**
     * Clear plugin transients
     */
    private static function clear_transients() {
        global $wpdb;
        
        // Remove all transients with our prefix
        $wpdb->query(
            "DELETE FROM $wpdb->options 
            WHERE option_name LIKE '_transient_phpm_%' 
            OR option_name LIKE '_transient_timeout_phpm_%'"
        );
    }
}