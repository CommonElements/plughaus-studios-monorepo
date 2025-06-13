<?php
/**
 * DealerEdge Plugin Deactivator
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class DE_Deactivator {
    
    public static function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Clear any scheduled events
        wp_clear_scheduled_hook('dealeredge_daily_maintenance');
        wp_clear_scheduled_hook('dealeredge_weekly_reports');
        
        // Clear transients
        delete_transient('dealeredge_activation_notice');
        delete_transient('dealeredge_dashboard_stats');
        
        // Note: We don't delete user data or options on deactivation
        // That should only happen on uninstall
    }
}