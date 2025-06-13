<?php
/**
 * Quick Plugin Activation Test
 */

require_once('./wp-config.php');
require_once('./wp-load.php');

echo "<h1>🚀 Quick Plugin Activation Test</h1>\n";

$plugins_to_test = [
    'StudioSnap' => 'studiosnap/studiosnap.php',
    'DealerEdge' => 'dealeredge/dealeredge.php', 
    'Marina Manager' => 'marina-manager/marina-manager.php',
    'StorageFlow' => 'storageflow/storageflow.php'
];

foreach ($plugins_to_test as $name => $path) {
    echo "<h2>Testing: {$name}</h2>\n";
    
    $plugin_file = WP_PLUGIN_DIR . '/' . $path;
    
    if (!file_exists($plugin_file)) {
        echo "<p style='color: red;'>❌ Plugin file not found: {$path}</p>\n";
        continue;
    }
    
    // Check if already active
    if (is_plugin_active($path)) {
        echo "<p style='color: blue;'>ℹ️ Already active</p>\n";
        continue;
    }
    
    // Try to activate
    $result = activate_plugin($path);
    
    if (is_wp_error($result)) {
        echo "<p style='color: red;'>❌ Activation failed: " . $result->get_error_message() . "</p>\n";
    } else {
        echo "<p style='color: green;'>✅ Activated successfully!</p>\n";
        
        // Test if shortcodes were registered
        global $shortcode_tags;
        $expected_shortcodes = [
            'StudioSnap' => 'studiosnap_booking_form',
            'DealerEdge' => 'dealeredge_work_order_form',
            'Marina Manager' => 'marina_reservation_form', 
            'StorageFlow' => 'storage_rental_form'
        ];
        
        if (isset($expected_shortcodes[$name]) && isset($shortcode_tags[$expected_shortcodes[$name]])) {
            echo "<p style='color: green;'>  ✅ Shortcode [{$expected_shortcodes[$name]}] registered</p>\n";
        }
    }
}

echo "<hr>\n";
echo "<h2>📋 Next Steps</h2>\n";
echo "<ol>\n";
echo "<li><strong>Access WordPress Admin:</strong> /wp-admin/</li>\n";
echo "<li><strong>Check Plugin Menus:</strong> Look for StudioSnap, DealerEdge, etc. in admin menu</li>\n"; 
echo "<li><strong>Test StudioSnap Booking:</strong> Create a page with [studiosnap_booking_form]</li>\n";
echo "<li><strong>WordPress.org Submission:</strong> Run build scripts to create submission packages</li>\n";
echo "</ol>\n";

echo "<p><em>Test completed at " . date('Y-m-d H:i:s') . "</em></p>\n";
?>