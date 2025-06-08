<?php
/**
 * PHPM Database Check - DELETE AFTER USE
 * Quick diagnostic script to check plugin database tables
 */

// Load WordPress
require_once('wp-config.php');
require_once('wp-load.php');

// Security check
if (!current_user_can('manage_options')) {
    die('Access denied');
}

echo "<h2>PlugHaus Property Management - Database Check</h2>";

global $wpdb;

// Check for custom tables created by the plugin
$custom_tables = [
    'phpm_property_views',
    'phpm_maintenance_log', 
    'phpm_payments'
];

echo "<h3>Custom Database Tables:</h3>";
foreach ($custom_tables as $table) {
    $table_name = $wpdb->prefix . $table;
    $result = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
    
    if ($result) {
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        echo "<p>✅ <strong>$table_name</strong> - EXISTS (Records: $count)</p>";
    } else {
        echo "<p>❌ <strong>$table_name</strong> - NOT FOUND</p>";
    }
}

// Check WordPress custom post types
echo "<h3>Custom Post Types:</h3>";
$post_types = ['phpm_property', 'phpm_unit', 'phpm_tenant', 'phpm_lease', 'phpm_maintenance'];

foreach ($post_types as $post_type) {
    $count = wp_count_posts($post_type);
    $total = $count->publish + $count->draft + $count->private;
    echo "<p>📄 <strong>$post_type</strong> - Total posts: $total (Published: {$count->publish}, Draft: {$count->draft})</p>";
}

// Check plugin options
echo "<h3>Plugin Settings:</h3>";
$settings = get_option('phpm_settings', []);
if (!empty($settings)) {
    echo "<p>✅ Plugin settings exist</p>";
    echo "<pre>" . print_r($settings, true) . "</pre>";
} else {
    echo "<p>⚠️ No plugin settings found</p>";
}

// Check plugin version
$version = get_option('phpm_version');
echo "<p><strong>Plugin Version:</strong> " . ($version ?: 'Not set') . "</p>";

// Check WordPress tables exist
$wp_tables_result = $wpdb->get_results("SHOW TABLES LIKE '{$wpdb->prefix}%'");
echo "<p><strong>Total WordPress tables:</strong> " . count($wp_tables_result) . "</p>";

echo "<hr>";
echo "<p><strong>Database Host:</strong> " . DB_HOST . "</p>";
echo "<p><strong>Database Name:</strong> " . DB_NAME . "</p>";
echo "<p><strong>Table Prefix:</strong> " . $wpdb->prefix . "</p>";

echo "<hr>";
echo "<p style='color: red;'><strong>DELETE THIS FILE (phpm-db-check.php) AFTER REVIEWING!</strong></p>";
?>