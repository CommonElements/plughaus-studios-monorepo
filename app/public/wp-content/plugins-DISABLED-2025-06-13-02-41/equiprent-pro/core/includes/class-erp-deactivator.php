<?php
/**
 * Fired during plugin deactivation
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 */
class ERP_Deactivator {

    /**
     * Short Description.
     *
     * Long Description.
     */
    public static function deactivate() {
        self::clear_scheduled_events();
        self::flush_rewrite_rules();
    }

    /**
     * Clear all scheduled events
     */
    private static function clear_scheduled_events() {
        // Clear scheduled maintenance checks
        wp_clear_scheduled_hook('erp_daily_maintenance_check');
        wp_clear_scheduled_hook('erp_weekly_availability_update');
    }

    /**
     * Flush rewrite rules
     */
    private static function flush_rewrite_rules() {
        flush_rewrite_rules();
    }
}