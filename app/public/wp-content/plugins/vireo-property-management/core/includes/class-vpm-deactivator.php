<?php
/**
 * Plugin Deactivator for Vireo Property Management
 * Handles plugin deactivation tasks
 */

if (!defined('ABSPATH')) {
    exit;
}

class VPM_Deactivator {
    
    /**
     * Deactivate the plugin
     */
    public static function deactivate() {
        // Clear scheduled events
        self::clear_scheduled_events();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Clear any scheduled events
     */
    private static function clear_scheduled_events() {
        // Clear maintenance reminder cron
        wp_clear_scheduled_hook('vpm_daily_maintenance_reminders');
        
        // Clear lease expiry notifications
        wp_clear_scheduled_hook('vpm_check_lease_expiry');
        
        // Clear any other scheduled tasks
        wp_clear_scheduled_hook('vpm_generate_monthly_reports');
    }
}