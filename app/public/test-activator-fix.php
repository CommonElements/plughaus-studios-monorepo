<?php
/**
 * Simple test to see if we can activate plugin
 */

// Load WordPress
require_once __DIR__ . '/wp-config.php';
require_once ABSPATH . 'wp-settings.php';

echo "🧪 Testing minimal plugin activation...\n";

// Deactivate first if active
$plugin_path = 'vireo-property-management/vireo-property-management.php';
if (is_plugin_active($plugin_path)) {
    deactivate_plugins($plugin_path);
    echo "✅ Deactivated plugin\n";
}

// Try to activate
$result = activate_plugin($plugin_path);

if (is_wp_error($result)) {
    echo "❌ Activation failed: " . $result->get_error_message() . "\n";
} else {
    echo "✅ Plugin activated successfully!\n";
    
    // Check if main class exists
    if (class_exists('Vireo_Property_Management')) {
        echo "✅ Main class loaded\n";
    } else {
        echo "❌ Main class not loaded\n";
    }
}

// Clean up
unlink(__FILE__);
?>