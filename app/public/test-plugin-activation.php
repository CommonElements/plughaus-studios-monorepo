<?php
/**
 * Test script for Vireo Property Management Plugin Activation
 * Tests plugin activation and database table creation
 */

// Load WordPress
require_once __DIR__ . '/wp-config.php';
require_once ABSPATH . 'wp-settings.php';

echo "🚀 Testing Vireo Property Management Plugin Activation\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Check if plugin file exists
$plugin_file = __DIR__ . '/wp-content/plugins/vireo-property-management/vireo-property-management.php';
if (!file_exists($plugin_file)) {
    echo "❌ ERROR: Plugin file not found at: $plugin_file\n";
    exit(1);
}

echo "✅ Plugin file found\n";

// Get plugin data
$plugin_data = get_plugin_data($plugin_file);
echo "📋 Plugin: {$plugin_data['Name']} v{$plugin_data['Version']}\n";
echo "👤 Author: {$plugin_data['Author']}\n\n";

// Check if plugin is already active
$plugin_path = 'vireo-property-management/vireo-property-management.php';
if (is_plugin_active($plugin_path)) {
    echo "⚠️  Plugin is already active. Deactivating first...\n";
    deactivate_plugins($plugin_path);
}

echo "🔧 Activating plugin...\n";

// Activate the plugin
$result = activate_plugin($plugin_path);

if (is_wp_error($result)) {
    echo "❌ ERROR: Plugin activation failed!\n";
    echo "Error: " . $result->get_error_message() . "\n";
    exit(1);
}

echo "✅ Plugin activated successfully!\n\n";

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

echo "\n📊 Tables created: $tables_created/" . count($tables_to_check) . "\n\n";

// Check post types
echo "📝 Checking post types...\n";
$post_types_to_check = [
    'vmp_property',
    'vmp_unit', 
    'vmp_tenant',
    'vmp_lease',
    'vmp_maintenance'
];

$post_types_registered = 0;
foreach ($post_types_to_check as $post_type) {
    if (post_type_exists($post_type)) {
        echo "  ✅ Post type registered: $post_type\n";
        $post_types_registered++;
    } else {
        echo "  ❌ Post type missing: $post_type\n";
    }
}

echo "\n📊 Post types registered: $post_types_registered/" . count($post_types_to_check) . "\n\n";

// Check options
echo "⚙️  Checking plugin options...\n";
$options_to_check = [
    'vmp_activation_date',
    'vmp_version'
];

$options_set = 0;
foreach ($options_to_check as $option) {
    $value = get_option($option);
    if ($value) {
        echo "  ✅ Option set: $option = $value\n";
        $options_set++;
    } else {
        echo "  ❌ Option missing: $option\n";
    }
}

echo "\n📊 Options set: $options_set/" . count($options_to_check) . "\n\n";

// Check admin menu
echo "🔧 Checking admin functionality...\n";

// Test if classes exist
$classes_to_check = [
    'Vireo_Property_Management',
    'PHPM_Admin',
    'PHPM_Post_Types',
    'PHPM_Utilities'
];

$classes_loaded = 0;
foreach ($classes_to_check as $class) {
    if (class_exists($class)) {
        echo "  ✅ Class loaded: $class\n";
        $classes_loaded++;
    } else {
        echo "  ❌ Class missing: $class\n";
    }
}

echo "\n📊 Classes loaded: $classes_loaded/" . count($classes_to_check) . "\n\n";

// Summary
echo "📋 ACTIVATION TEST SUMMARY\n";
echo "=" . str_repeat("=", 30) . "\n";

$total_checks = count($tables_to_check) + count($post_types_to_check) + count($options_to_check) + count($classes_to_check);
$total_passed = $tables_created + $post_types_registered + $options_set + $classes_loaded;

echo "✅ Total checks passed: $total_passed/$total_checks\n";

if ($total_passed === $total_checks) {
    echo "🎉 ALL TESTS PASSED! Plugin is working correctly.\n";
} else {
    echo "⚠️  Some tests failed. Check the output above for details.\n";
}

echo "\n🔗 Access your WordPress admin to see the plugin in action:\n";
echo "   Admin URL: " . admin_url() . "\n";
echo "   Plugin Page: " . admin_url('admin.php?page=vmp-dashboard') . "\n";

// Clean up - remove this test file for security
echo "\n🧹 Cleaning up test file...\n";
unlink(__FILE__);
echo "✅ Test file removed for security.\n";

?>