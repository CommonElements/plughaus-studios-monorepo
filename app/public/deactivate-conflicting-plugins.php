<?php
/**
 * Temporary script to deactivate conflicting property management plugins
 * DELETE THIS FILE AFTER USE!
 */

// Include WordPress
require_once('wp-config.php');

echo "<h2>Plugin Conflict Resolution</h2>";

// Get all active plugins
$active_plugins = get_option('active_plugins', array());

echo "<h3>Currently Active Plugins:</h3>";
echo "<ul>";
foreach ($active_plugins as $plugin) {
    echo "<li>" . esc_html($plugin) . "</li>";
}
echo "</ul>";

// Plugins to deactivate
$plugins_to_deactivate = array(
    'plughaus-property-management-free/plughaus-property-management.php',
    'plughaus-property-management-pro/plughaus-property-management-pro.php'
);

echo "<h3>Deactivating Conflicting Plugins:</h3>";

$updated_plugins = array();
foreach ($active_plugins as $plugin) {
    if (!in_array($plugin, $plugins_to_deactivate)) {
        $updated_plugins[] = $plugin;
        echo "<li>✅ Keeping: " . esc_html($plugin) . "</li>";
    } else {
        echo "<li>❌ Deactivating: " . esc_html($plugin) . "</li>";
    }
}

// Update the active plugins list
update_option('active_plugins', $updated_plugins);

echo "<h3>✅ Done! Conflicting plugins have been deactivated.</h3>";
echo "<p><strong>Only keeping:</strong> plughaus-property-management/plughaus-property-management.php</p>";
echo "<p><strong style='color: red;'>IMPORTANT: Delete this file immediately after use!</strong></p>";

// Clear any plugin caches
wp_cache_flush();
?>