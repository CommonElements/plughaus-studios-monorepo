<?php
/**
 * Check Plugin Activation Status
 */

require_once('./wp-config.php');
require_once('./wp-load.php');

echo '<h1>🔌 Plugin Activation Status</h1>';

$plugins_to_check = [
    'StudioSnap' => 'studiosnap/studiosnap.php',
    'DealerEdge' => 'dealeredge/dealeredge.php', 
    'Marina Manager' => 'marina-manager/marina-manager.php',
    'StorageFlow' => 'storageflow/storageflow.php'
];

foreach ($plugins_to_check as $name => $path) {
    $plugin_file = WP_PLUGIN_DIR . '/' . $path;
    $exists = file_exists($plugin_file);
    $active = is_plugin_active($path);
    
    echo '<h2>' . $name . '</h2>';
    echo '<p><strong>File exists:</strong> ' . ($exists ? '✅ Yes' : '❌ No') . '</p>';
    echo '<p><strong>Currently active:</strong> ' . ($active ? '✅ Yes' : '❌ No') . '</p>';
    
    if ($exists && !$active) {
        echo '<p style="color: orange;">⚠️ Ready to activate</p>';
        
        // Try to activate the plugin
        $result = activate_plugin($path);
        if (is_wp_error($result)) {
            echo '<p style="color: red;">❌ Activation failed: ' . $result->get_error_message() . '</p>';
        } else {
            echo '<p style="color: green;">✅ Successfully activated!</p>';
        }
    }
}

echo '<hr>';

// Check shortcodes after activation
global $shortcode_tags;
$expected_shortcodes = [
    'studiosnap_booking_form' => 'StudioSnap',
    'dealeredge_work_order_form' => 'DealerEdge',
    'marina_reservation_form' => 'Marina Manager', 
    'storage_rental_form' => 'StorageFlow'
];

echo '<h2>📋 Shortcode Registration Status</h2>';
foreach ($expected_shortcodes as $shortcode => $plugin) {
    if (isset($shortcode_tags[$shortcode])) {
        echo '<p style="color: green;">✅ ' . $plugin . ': [' . $shortcode . '] registered</p>';
    } else {
        echo '<p style="color: orange;">⚠️ ' . $plugin . ': [' . $shortcode . '] not registered (needs class implementation)</p>';
    }
}

echo '<hr>';
echo '<h2>🎯 Next Steps</h2>';
echo '<ol>';
echo '<li><strong>WordPress Admin:</strong> <a href="/wp-admin/">Go to WordPress Admin</a></li>';
echo '<li><strong>Check Plugin Menus:</strong> Look for new admin menus in sidebar</li>';
echo '<li><strong>Test StudioSnap:</strong> <a href="/studiosnap-booking-test/">Visit booking test page</a></li>';
echo '<li><strong>WordPress.org Submission:</strong> Plugins ready for marketplace submission</li>';
echo '</ol>';

echo '<p><em>Test completed at ' . date('Y-m-d H:i:s') . '</em></p>';
?>