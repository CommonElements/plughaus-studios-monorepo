<?php
/**
 * Quick Plugin Activation Test
 * 
 * Simple script to test basic plugin activation
 */

// Load WordPress
$wp_root = dirname(dirname(dirname(__FILE__))) . '/app/public';
require_once($wp_root . '/wp-config.php');
require_once($wp_root . '/wp-load.php');

echo "=== QUICK PLUGIN ACTIVATION TEST ===\n";
echo "WordPress Version: " . get_bloginfo('version') . "\n";
echo "PHP Version: " . PHP_VERSION . "\n\n";

/**
 * Test Property Management Plugin
 */
echo "Testing Property Management Plugin:\n";

$property_plugin = 'vireo-property-management/vireo-property-management.php';

// Check if plugin file exists
$plugin_file = WP_PLUGIN_DIR . '/' . $property_plugin;
if (!file_exists($plugin_file)) {
    echo "❌ Plugin file not found: $plugin_file\n";
} else {
    echo "✅ Plugin file found\n";
    
    // Try to get plugin data
    if (!function_exists('get_plugin_data')) {
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }
    
    $plugin_data = get_plugin_data($plugin_file);
    echo "   Plugin Name: " . $plugin_data['Name'] . "\n";
    echo "   Version: " . $plugin_data['Version'] . "\n";
    
    // Check if active
    if (is_plugin_active($property_plugin)) {
        echo "✅ Plugin is currently active\n";
        
        // Test main class
        if (class_exists('Vireo_Property_Management')) {
            echo "✅ Main plugin class exists\n";
        } else {
            echo "❌ Main plugin class not found\n";
        }
        
    } else {
        echo "⚠️  Plugin is not active\n";
        
        // Try to activate
        echo "   Attempting activation...\n";
        $result = activate_plugin($property_plugin);
        
        if (is_wp_error($result)) {
            echo "❌ Activation failed: " . $result->get_error_message() . "\n";
        } else {
            echo "✅ Plugin activated successfully\n";
        }
    }
}

echo "\n";

/**
 * Test Sports League Plugin (if it exists)
 */
echo "Testing Sports League Plugin:\n";

$sports_plugin = 'vireo-sports-league/vireo-sports-league.php';
$sports_file = WP_PLUGIN_DIR . '/' . $sports_plugin;

if (!file_exists($sports_file)) {
    echo "⚠️  Sports League plugin not found (this is expected if not yet created)\n";
} else {
    echo "✅ Plugin file found\n";
    
    $plugin_data = get_plugin_data($sports_file);
    echo "   Plugin Name: " . $plugin_data['Name'] . "\n";
    echo "   Version: " . $plugin_data['Version'] . "\n";
    
    if (is_plugin_active($sports_plugin)) {
        echo "✅ Plugin is currently active\n";
    } else {
        echo "⚠️  Plugin is not active\n";
    }
}

echo "\n";

/**
 * Check Database Tables
 */
echo "Checking Database Tables:\n";

global $wpdb;

// Property Management tables
$pm_tables = [
    'vpm_properties',
    'vpm_units', 
    'vpm_tenants',
    'vpm_leases',
    'vpm_maintenance_requests'
];

echo "Property Management Tables:\n";
foreach ($pm_tables as $table) {
    $table_name = $wpdb->prefix . $table;
    $exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
    echo "   " . ($exists ? "✅" : "❌") . " $table_name\n";
}

echo "\n";

/**
 * Check for Errors
 */
echo "Checking for Recent Errors:\n";

$error_log = WP_CONTENT_DIR . '/debug.log';
if (file_exists($error_log) && WP_DEBUG_LOG) {
    $errors = file_get_contents($error_log);
    $recent_errors = array_slice(file($error_log), -20);
    
    $plugin_errors = array_filter($recent_errors, function($line) {
        return (strpos($line, 'plughaus') !== false || strpos($line, 'vireo') !== false || strpos($line, 'vpm_') !== false || strpos($line, 'vsl_') !== false);
    });
    
    if (empty($plugin_errors)) {
        echo "✅ No recent plugin-related errors found\n";
    } else {
        echo "⚠️  Found " . count($plugin_errors) . " plugin-related errors:\n";
        foreach ($plugin_errors as $error) {
            echo "   " . trim($error) . "\n";
        }
    }
} else {
    echo "⚠️  Debug logging not enabled or log file not found\n";
}

echo "\n";

/**
 * Performance Check
 */
echo "Basic Performance Check:\n";

$start_time = microtime(true);

// Run some basic queries
$wpdb->get_results("SELECT COUNT(*) FROM {$wpdb->posts}");
$wpdb->get_results("SELECT COUNT(*) FROM {$wpdb->users}");

$end_time = microtime(true);
$query_time = round(($end_time - $start_time) * 1000, 2);

echo "✅ Database query time: {$query_time}ms\n";

if ($query_time > 1000) {
    echo "⚠️  Database queries are slow (>{$query_time}ms)\n";
}

echo "\n=== TEST COMPLETE ===\n";

// Memory usage
echo "Memory Usage: " . round(memory_get_usage() / 1024 / 1024, 2) . "MB\n";
echo "Peak Memory: " . round(memory_get_peak_usage() / 1024 / 1024, 2) . "MB\n";
echo "Execution Time: " . round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 2) . "s\n";
?>