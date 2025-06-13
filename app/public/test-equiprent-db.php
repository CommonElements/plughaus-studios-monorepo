<?php
/**
 * Test script for EquipRent Pro database creation
 * 
 * This script tests the database tables creation functionality
 * Run this by visiting: http://your-site.local/test-equiprent-db.php
 * DELETE THIS FILE after testing!
 */

// Load WordPress
require_once('wp-config.php');
require_once('wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    wp_die('Access denied. Admin privileges required.');
}

echo '<h1>EquipRent Pro Database Test</h1>';

// Load the activator class
require_once('wp-content/plugins/equiprent-pro/core/includes/class-er-activator.php');

// Get table names that should be created
global $wpdb;
$tables = array(
    'er_equipment',
    'er_bookings', 
    'er_booking_items',
    'er_customers',
    'er_maintenance',
    'er_damage_reports',
    'er_payments',
    'er_activity_log',
    'er_availability',
    'er_documents'
);

echo '<h2>Current Database State</h2>';
echo '<table border="1" style="border-collapse: collapse; margin: 20px 0;">';
echo '<tr><th style="padding: 10px;">Table Name</th><th style="padding: 10px;">Exists</th><th style="padding: 10px;">Row Count</th></tr>';

foreach ($tables as $table) {
    $full_table_name = $wpdb->prefix . $table;
    $exists = $wpdb->get_var("SHOW TABLES LIKE '{$full_table_name}'") === $full_table_name;
    $row_count = $exists ? $wpdb->get_var("SELECT COUNT(*) FROM {$full_table_name}") : 'N/A';
    
    echo '<tr>';
    echo '<td style="padding: 10px;">' . $full_table_name . '</td>';
    echo '<td style="padding: 10px; color: ' . ($exists ? 'green' : 'red') . ';">' . ($exists ? 'YES' : 'NO') . '</td>';
    echo '<td style="padding: 10px;">' . $row_count . '</td>';
    echo '</tr>';
}

echo '</table>';

// Check plugin options
echo '<h2>Plugin Options</h2>';
$options = array(
    'equiprent_activated',
    'equiprent_activation_date', 
    'equiprent_db_version',
    'equiprent_version',
    'equiprent_settings'
);

echo '<table border="1" style="border-collapse: collapse; margin: 20px 0;">';
echo '<tr><th style="padding: 10px;">Option Name</th><th style="padding: 10px;">Value</th></tr>';

foreach ($options as $option) {
    $value = get_option($option, 'NOT SET');
    if (is_array($value) || is_object($value)) {
        $value = '<pre>' . print_r($value, true) . '</pre>';
    }
    
    echo '<tr>';
    echo '<td style="padding: 10px;">' . $option . '</td>';
    echo '<td style="padding: 10px;">' . $value . '</td>';
    echo '</tr>';
}

echo '</table>';

// Check if EquipRent Pro is active
echo '<h2>Plugin Status</h2>';
$active_plugins = get_option('active_plugins', array());
$equiprent_active = in_array('equiprent-pro/equiprent-pro.php', $active_plugins);

echo '<p><strong>EquipRent Pro Active:</strong> ' . ($equiprent_active ? '<span style="color: green;">YES</span>' : '<span style="color: red;">NO</span>') . '</p>';

if (!$equiprent_active) {
    echo '<p style="color: orange;"><strong>Note:</strong> Plugin is not currently active. Activate it through the WordPress admin to trigger database creation.</p>';
}

// Check for any WordPress errors
if (defined('WP_DEBUG') && WP_DEBUG) {
    echo '<h2>Debug Information</h2>';
    echo '<p><strong>WP_DEBUG:</strong> ON</p>';
    echo '<p><strong>PHP Version:</strong> ' . PHP_VERSION . '</p>';
    echo '<p><strong>WordPress Version:</strong> ' . get_bloginfo('version') . '</p>';
    echo '<p><strong>MySQL Version:</strong> ' . $wpdb->db_version() . '</p>';
}

echo '<hr>';
echo '<p style="color: red;"><strong>IMPORTANT:</strong> Delete this file (test-equiprent-db.php) after testing!</p>';
echo '<p><strong>To activate EquipRent Pro:</strong> Go to WordPress Admin → Plugins → Activate "EquipRent Pro"</p>';
?>