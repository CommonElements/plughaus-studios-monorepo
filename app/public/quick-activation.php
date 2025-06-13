<?php
/**
 * Quick Plugin Activation
 */

require_once('./wp-config.php');
require_once('./wp-load.php');

echo "🚀 Activating Vireo Designs Plugins...\n\n";

$plugins = [
    'studiosnap/studiosnap.php',
    'dealeredge/dealeredge.php',
    'marina-manager/marina-manager.php', 
    'storageflow/storageflow.php'
];

foreach ($plugins as $plugin) {
    if (!is_plugin_active($plugin)) {
        $result = activate_plugin($plugin);
        if (is_wp_error($result)) {
            echo "❌ Failed to activate {$plugin}: " . $result->get_error_message() . "\n";
        } else {
            echo "✅ Successfully activated {$plugin}\n";
        }
    } else {
        echo "ℹ️ {$plugin} already active\n";
    }
}

echo "\n🎯 Next: Visit http://Vireo/wp-admin/ to see the new plugin menus!\n";
echo "📋 Test: Visit http://Vireo/studiosnap-booking-test/ to test booking form!\n";
?>