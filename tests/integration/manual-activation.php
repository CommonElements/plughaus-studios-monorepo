<?php
/**
 * Manual Plugin Activation Script
 * 
 * This script manually triggers plugin activation to create database tables
 */

// Load WordPress
$wp_root = dirname(dirname(dirname(__FILE__))) . '/app/public';
require_once($wp_root . '/wp-config.php');
require_once($wp_root . '/wp-load.php');

echo "=== MANUAL PLUGIN ACTIVATION ===\n";

// Load required files
if (!function_exists('activate_plugin')) {
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

// Activate Property Management Plugin
echo "Activating Property Management Plugin...\n";

$property_plugin = 'vireo-property-management/vireo-property-management.php';

// Deactivate first to ensure clean activation
if (is_plugin_active($property_plugin)) {
    deactivate_plugins($property_plugin);
    echo "Deactivated existing plugin\n";
}

// Activate plugin
$result = activate_plugin($property_plugin);

if (is_wp_error($result)) {
    echo "❌ Activation failed: " . $result->get_error_message() . "\n";
} else {
    echo "✅ Plugin activated successfully\n";
    
    // Check if tables were created
    global $wpdb;
    
    $tables = [
        'vpm_properties',
        'vpm_units',
        'vpm_tenants',
        'vpm_leases',
        'vpm_maintenance_requests',
        'vpm_payments',
        'vpm_expenses',
        'vpm_documents',
        'vpm_communications',
        'vpm_settings',
        'vpm_activity_log'
    ];
    
    echo "\nChecking database tables:\n";
    foreach ($tables as $table) {
        $table_name = $wpdb->prefix . $table;
        $exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
        echo ($exists ? "✅" : "❌") . " $table_name\n";
    }
}

// Activate Sports League Plugin
echo "\n=== ACTIVATING SPORTS LEAGUE PLUGIN ===\n";

$sports_plugin = 'vireo-sports-league/vireo-sports-league.php';

if (file_exists(WP_PLUGIN_DIR . '/' . $sports_plugin)) {
    // Deactivate first
    if (is_plugin_active($sports_plugin)) {
        deactivate_plugins($sports_plugin);
        echo "Deactivated existing sports plugin\n";
    }
    
    // Activate plugin
    $result = activate_plugin($sports_plugin);
    
    if (is_wp_error($result)) {
        echo "❌ Activation failed: " . $result->get_error_message() . "\n";
    } else {
        echo "✅ Sports League plugin activated successfully\n";
        
        // Check sports league tables
        $sports_tables = [
            'vsl_leagues',
            'vsl_seasons',
            'vsl_teams',
            'vsl_players',
            'vsl_matches',
            'vsl_match_events',
            'vsl_standings',
            'vsl_player_stats',
            'vsl_venues',
            'vsl_officials',
            'vsl_activity_log'
        ];
        
        echo "\nChecking sports league tables:\n";
        foreach ($sports_tables as $table) {
            $table_name = $wpdb->prefix . $table;
            $exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
            echo ($exists ? "✅" : "❌") . " $table_name\n";
        }
    }
} else {
    echo "⚠️  Sports League plugin file not found\n";
}

echo "\n=== ACTIVATION COMPLETE ===\n";
?>