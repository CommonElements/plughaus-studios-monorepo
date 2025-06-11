<?php
/**
 * Test plugin activation with PHPM activator
 */

// Load WordPress
require_once __DIR__ . '/wp-config.php';
require_once ABSPATH . 'wp-settings.php';

echo "🧪 Testing plugin activation with PHPM activator...\n";

// Deactivate first if active
$plugin_path = 'vireo-property-management/vireo-property-management.php';
if (is_plugin_active($plugin_path)) {
    deactivate_plugins($plugin_path);
    echo "✅ Deactivated plugin\n";
}

// Try to activate
echo "🔧 Activating plugin...\n";
$result = activate_plugin($plugin_path);

if (is_wp_error($result)) {
    echo "❌ Activation failed: " . $result->get_error_message() . "\n";
    exit(1);
} else {
    echo "✅ Plugin activated successfully!\n";
}

// Check database tables
echo "🗄️  Checking database tables...\n";
global $wpdb;

$tables_to_check = [
    'vmp_property_views',
    'vmp_maintenance_log', 
    'vmp_payments',
    'vmp_lease_history',
    'vmp_documents'
];

$tables_created = 0;
foreach ($tables_to_check as $table) {
    $full_table_name = $wpdb->prefix . $table;
    $exists = $wpdb->get_var("SHOW TABLES LIKE '$full_table_name'");
    
    if ($exists) {
        echo "  ✅ Table exists: $full_table_name\n";
        $tables_created++;
    } else {
        echo "  ❌ Table missing: $full_table_name\n";
    }
}

echo "\n📊 Tables created: $tables_created/" . count($tables_to_check) . "\n";

// Check if main class exists
if (class_exists('Vireo_Property_Management')) {
    echo "✅ Main class loaded\n";
} else {
    echo "❌ Main class not loaded\n";
}

// Test admin interface
if (class_exists('PHPM_Admin')) {
    echo "✅ Admin class loaded\n";
} else {
    echo "❌ Admin class not loaded\n";
}

echo "\n🎯 Test completed!\n";

// Clean up
unlink(__FILE__);
?>