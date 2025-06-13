<?php
/**
 * Property Management Plugin Activation Test
 * Run this file to test plugin activation without browser interface
 */

// WordPress Bootstrap
require_once __DIR__ . '/wp-config.php';
require_once ABSPATH . 'wp-settings.php';

echo "🏠 PROPERTY MANAGEMENT PLUGIN ACTIVATION TEST\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Check if plugin exists
$plugin_file = 'vireo-property-management/vireo-property-management.php';
$plugin_path = WP_PLUGIN_DIR . '/' . $plugin_file;

echo "📁 Plugin Path: " . $plugin_path . "\n";
echo "📁 Plugin Exists: " . (file_exists($plugin_path) ? "✅ YES" : "❌ NO") . "\n\n";

if (!file_exists($plugin_path)) {
    echo "❌ Plugin file not found. Exiting.\n";
    exit(1);
}

// Check if plugin is already active
$active_plugins = get_option('active_plugins', array());
$is_active = in_array($plugin_file, $active_plugins);

echo "🔌 Plugin Status: " . ($is_active ? "✅ ACTIVE" : "⚠️ INACTIVE") . "\n\n";

// Test plugin activation
if (!$is_active) {
    echo "🚀 ACTIVATING PLUGIN...\n";
    
    // Activate the plugin
    $result = activate_plugin($plugin_file);
    
    if (is_wp_error($result)) {
        echo "❌ ACTIVATION FAILED: " . $result->get_error_message() . "\n";
        exit(1);
    } else {
        echo "✅ PLUGIN ACTIVATED SUCCESSFULLY!\n";
    }
} else {
    echo "ℹ️ Plugin already active, checking functionality...\n";
}

echo "\n🔍 CHECKING PLUGIN FUNCTIONALITY...\n";
echo "-" . str_repeat("-", 40) . "\n";

// Check if main class exists
echo "🏗️ Main Class: ";
if (class_exists('Vireo_Property_Management')) {
    echo "✅ EXISTS\n";
} else {
    echo "❌ MISSING\n";
}

// Check post types
echo "📝 Post Types: ";
$post_types = array('phpm_property', 'phpm_unit', 'phpm_tenant', 'phpm_lease', 'phpm_maintenance');
$missing_types = array();

foreach ($post_types as $post_type) {
    if (!post_type_exists($post_type)) {
        $missing_types[] = $post_type;
    }
}

if (empty($missing_types)) {
    echo "✅ ALL REGISTERED (" . count($post_types) . ")\n";
} else {
    echo "⚠️ MISSING: " . implode(', ', $missing_types) . "\n";
}

// Check database tables
echo "🗄️ Database Tables: ";
global $wpdb;

$tables = array(
    'vmp_property_views',
    'vmp_maintenance_log', 
    'vmp_payments',
    'vmp_lease_history',
    'vmp_documents'
);

$missing_tables = array();
foreach ($tables as $table) {
    $table_name = $wpdb->prefix . $table;
    $exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
    if (!$exists) {
        $missing_tables[] = $table;
    }
}

if (empty($missing_tables)) {
    echo "✅ ALL CREATED (" . count($tables) . ")\n";
} else {
    echo "⚠️ MISSING: " . implode(', ', $missing_tables) . "\n";
}

// Check admin menu
echo "📋 Admin Menu: ";
global $menu, $submenu;
$menu_exists = false;
if (is_array($menu)) {
    foreach ($menu as $menu_item) {
        if (isset($menu_item[2]) && strpos($menu_item[2], 'vmp-dashboard') !== false) {
            $menu_exists = true;
            break;
        }
    }
}
echo $menu_exists ? "✅ REGISTERED\n" : "⚠️ NOT FOUND\n";

// Check capabilities
echo "🔐 Capabilities: ";
$capabilities = array(
    'manage_vmp_properties',
    'edit_vmp_properties',
    'delete_vmp_properties'
);

$admin_role = get_role('administrator');
$missing_caps = array();

if ($admin_role) {
    foreach ($capabilities as $cap) {
        if (!$admin_role->has_cap($cap)) {
            $missing_caps[] = $cap;
        }
    }
}

if (empty($missing_caps)) {
    echo "✅ ALL ASSIGNED\n";
} else {
    echo "⚠️ MISSING: " . implode(', ', $missing_caps) . "\n";
}

// Check plugin settings
echo "⚙️ Plugin Settings: ";
$settings = get_option('phpm_settings');
if ($settings && is_array($settings)) {
    echo "✅ CONFIGURED (" . count($settings) . " options)\n";
} else {
    echo "⚠️ NOT SET\n";
}

echo "\n🎯 FUNCTIONALITY TESTS\n";
echo "-" . str_repeat("-", 30) . "\n";

// Test creating a property
echo "🏠 Create Test Property: ";
$property_id = wp_insert_post(array(
    'post_type' => 'phpm_property',
    'post_title' => 'Test Property - ' . date('Y-m-d H:i:s'),
    'post_status' => 'publish',
    'meta_input' => array(
        '_phpm_property_address' => '123 Test Street',
        '_phpm_property_city' => 'Test City',
        '_phpm_property_type' => 'apartment'
    )
));

if (!is_wp_error($property_id)) {
    echo "✅ CREATED (ID: $property_id)\n";
    
    // Test meta data
    $address = get_post_meta($property_id, '_phpm_property_address', true);
    echo "📍 Property Meta: " . ($address === '123 Test Street' ? "✅ WORKING" : "❌ FAILED") . "\n";
    
    // Clean up
    wp_delete_post($property_id, true);
    echo "🗑️ Cleanup: ✅ TEST DATA REMOVED\n";
} else {
    echo "❌ FAILED: " . $property_id->get_error_message() . "\n";
}

echo "\n📊 FINAL ASSESSMENT\n";
echo "=" . str_repeat("=", 30) . "\n";

$total_checks = 7; // Number of main checks above
$passed_checks = 0;

// Count successful checks based on output analysis
if (class_exists('Vireo_Property_Management')) $passed_checks++;
if (empty($missing_types)) $passed_checks++;
if (empty($missing_tables)) $passed_checks++;
if ($menu_exists) $passed_checks++;
if (empty($missing_caps)) $passed_checks++;
if ($settings) $passed_checks++;
if (!is_wp_error($property_id)) $passed_checks++;

$percentage = round(($passed_checks / $total_checks) * 100);

echo "✅ Passed: $passed_checks/$total_checks ($percentage%)\n";

if ($percentage >= 85) {
    echo "🎉 PLUGIN IS READY FOR WORDPRESS.ORG SUBMISSION!\n";
} elseif ($percentage >= 70) {
    echo "⚠️ Plugin needs minor fixes before submission.\n";
} else {
    echo "❌ Plugin requires significant work before submission.\n";
}

echo "\n🏁 Test completed at " . date('Y-m-d H:i:s') . "\n";

// Don't delete this file - it's useful for testing
echo "\n💡 TIP: Run this test after making changes to verify functionality.\n";
?>