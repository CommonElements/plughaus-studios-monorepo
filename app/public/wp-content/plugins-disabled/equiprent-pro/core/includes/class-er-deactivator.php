<?php
/**
 * EquipRent Pro Deactivator
 *
 * @package EquipRent_Pro
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Fired during plugin deactivation
 */
class ER_Deactivator {

    /**
     * Plugin deactivation tasks
     */
    public static function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Clear scheduled events
        self::clear_scheduled_events();
        
        // Set deactivation flag
        add_option('equiprent_deactivated', true);
        add_option('equiprent_deactivation_date', current_time('mysql'));
    }

    /**
     * Clear scheduled events
     */
    private static function clear_scheduled_events() {
        // Clear any scheduled cron events
        wp_clear_scheduled_hook('equiprent_daily_maintenance_check');
        wp_clear_scheduled_hook('equiprent_hourly_booking_notifications');
        wp_clear_scheduled_hook('equiprent_weekly_reports');
    }
}