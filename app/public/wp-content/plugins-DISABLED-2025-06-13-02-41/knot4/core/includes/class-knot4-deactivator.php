<?php
/**
 * Plugin Deactivator for Knot4
 * 
 * @package Knot4
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Knot4_Deactivator {
    
    /**
     * Deactivate the plugin
     */
    public static function deactivate() {
        // Clear scheduled events
        self::clear_scheduled_events();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Log deactivation
        error_log('Knot4 Plugin Deactivated');
    }
    
    /**
     * Clear scheduled events
     */
    private static function clear_scheduled_events() {
        $scheduled_events = array(
            'knot4_process_recurring_donations',
            'knot4_send_reminder_emails',
            'knot4_cleanup_old_logs',
        );
        
        foreach ($scheduled_events as $event) {
            $timestamp = wp_next_scheduled($event);
            if ($timestamp) {
                wp_unschedule_event($timestamp, $event);
            }
        }
    }
}