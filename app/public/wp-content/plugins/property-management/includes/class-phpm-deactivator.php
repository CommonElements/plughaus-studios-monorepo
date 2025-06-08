<?php
/**
 * Fired during plugin deactivation
 */

if (!defined('ABSPATH')) {
    exit;
}

class PHPM_Deactivator {
    
    /**
     * Plugin deactivation
     */
    public static function deactivate() {
        // Unschedule cron events
        self::unschedule_events();
        
        // Remove capabilities
        PHPM_Capabilities::remove_capabilities();
        
        // Clear any transients
        self::clear_transients();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Unschedule cron events
     */
    private static function unschedule_events() {
        // Unschedule daily lease check
        $timestamp = wp_next_scheduled('phpm_daily_lease_check');
        if ($timestamp) {
            wp_unschedule_event($timestamp, 'phpm_daily_lease_check');
        }
        
        // Unschedule weekly reports
        $timestamp = wp_next_scheduled('phpm_weekly_reports');
        if ($timestamp) {
            wp_unschedule_event($timestamp, 'phpm_weekly_reports');
        }
    }
    
    /**
     * Clear plugin transients
     */
    private static function clear_transients() {
        global $wpdb;
        
        // Delete all transients with our prefix
        $wpdb->query(
            "DELETE FROM {$wpdb->options} 
            WHERE option_name LIKE '_transient_phpm_%' 
            OR option_name LIKE '_transient_timeout_phpm_%'"
        );
    }
}