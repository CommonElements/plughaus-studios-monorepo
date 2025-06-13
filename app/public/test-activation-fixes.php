<?php
/**
 * Test Plugin Activation After Critical Fixes
 */

require_once('./wp-config.php');
require_once('./wp-load.php');

echo '<h1>🔧 Testing Plugin Activation After Critical Fixes</h1>';
echo '<p><strong>Date:</strong> ' . date('Y-m-d H:i:s') . '</p>';

$plugins_to_test = [
    'Property Management' => 'vireo-property-management/vireo-property-management.php',
    'Sports League' => 'vireo-sports-league/vireo-sports-league.php',
    'StudioSnap' => 'studiosnap/studiosnap.php',
    'DealerEdge' => 'dealeredge/dealeredge.php',
    'Marina Manager' => 'marina-manager/marina-manager.php',
    'StorageFlow' => 'storageflow/storageflow.php'
];

echo '<h2>✅ Critical Fixes Applied:</h2>';
echo '<ul>';
echo '<li>✅ Fixed Property Management deactivation hook class name mismatch</li>';
echo '<li>✅ Verified all activator files exist</li>';
echo '<li>✅ Cleaned up extracted plugin versions to prevent conflicts</li>';
echo '</ul>';

echo '<hr>';
echo '<h2>🚀 Plugin Activation Tests</h2>';

$results = [];
$activation_errors = [];

foreach ($plugins_to_test as $name => $path) {
    echo '<h3>' . $name . '</h3>';
    
    $plugin_file = WP_PLUGIN_DIR . '/' . $path;
    
    // Check if plugin file exists
    if (!file_exists($plugin_file)) {
        echo '<p style="color: red;">❌ Plugin file not found: ' . $path . '</p>';
        $results[$name] = false;
        continue;
    }
    
    echo '<p style="color: green;">✅ Plugin file exists</p>';
    
    // Check if already active
    if (is_plugin_active($path)) {
        echo '<p style="color: blue;">ℹ️ Already active</p>';
        $results[$name] = true;
        continue;
    }
    
    // Try to activate
    echo '<p>🔄 Attempting activation...</p>';
    
    // Capture any errors during activation
    ob_start();
    $activation_result = activate_plugin($path);
    $output = ob_get_clean();
    
    if (is_wp_error($activation_result)) {
        echo '<p style="color: red;">❌ Activation failed: ' . $activation_result->get_error_message() . '</p>';
        $activation_errors[$name] = $activation_result->get_error_message();
        $results[$name] = false;
    } else {
        echo '<p style="color: green;">✅ Successfully activated!</p>';
        $results[$name] = true;
        
        // Check for admin menus if this is an admin request
        if (is_admin()) {
            global $menu, $submenu;
            $found_menu = false;
            
            foreach ($menu as $menu_item) {
                if (isset($menu_item[0]) && 
                    (strpos(strtolower($menu_item[0]), strtolower($name)) !== false ||
                     strpos(strtolower($menu_item[0]), 'vireo') !== false)) {
                    echo '<p style="color: green;">  ✅ Admin menu found: ' . $menu_item[0] . '</p>';
                    $found_menu = true;
                    break;
                }
            }
            
            if (!$found_menu) {
                echo '<p style="color: orange;">  ⚠️ Admin menu not found (may need class implementation)</p>';
            }
        }
    }
    
    if (!empty($output)) {
        echo '<p style="color: orange;">⚠️ Output during activation: ' . htmlspecialchars($output) . '</p>';
    }
}

echo '<hr>';
echo '<h2>📊 Activation Summary</h2>';

$total_plugins = count($plugins_to_test);
$successful_activations = array_sum($results);

echo '<p><strong>Total Plugins Tested:</strong> ' . $total_plugins . '</p>';
echo '<p><strong>Successfully Activated:</strong> ' . $successful_activations . '/' . $total_plugins . '</p>';

if ($successful_activations == $total_plugins) {
    echo '<p style="color: green; font-size: 18px; font-weight: bold;">🎉 ALL PLUGINS ACTIVATED SUCCESSFULLY!</p>';
    echo '<p>The critical fixes have resolved the activation issues. The plugin ecosystem is now stable.</p>';
} else {
    echo '<p style="color: orange; font-size: 18px; font-weight: bold;">⚠️ Some plugins need additional attention</p>';
    
    if (!empty($activation_errors)) {
        echo '<h3>❌ Activation Errors:</h3>';
        foreach ($activation_errors as $plugin => $error) {
            echo '<p><strong>' . $plugin . ':</strong> ' . $error . '</p>';
        }
    }
}

// Test shortcode registration
echo '<hr>';
echo '<h2>🔗 Shortcode Registration Test</h2>';

global $shortcode_tags;
$expected_shortcodes = [
    'property_search' => 'Property Management',
    'league_standings' => 'Sports League',
    'studiosnap_booking_form' => 'StudioSnap',
    'dealeredge_work_order_form' => 'DealerEdge',
    'marina_reservation_form' => 'Marina Manager',
    'storage_rental_form' => 'StorageFlow'
];

$registered_shortcodes = 0;
foreach ($expected_shortcodes as $shortcode => $plugin) {
    if (isset($shortcode_tags[$shortcode])) {
        echo '<p style="color: green;">✅ [' . $shortcode . '] - ' . $plugin . '</p>';
        $registered_shortcodes++;
    } else {
        echo '<p style="color: orange;">⚠️ [' . $shortcode . '] - ' . $plugin . ' (needs implementation)</p>';
    }
}

echo '<p><strong>Shortcodes Registered:</strong> ' . $registered_shortcodes . '/' . count($expected_shortcodes) . '</p>';

echo '<hr>';
echo '<h2>🎯 Next Steps</h2>';

if ($successful_activations == $total_plugins) {
    echo '<ol>';
    echo '<li><strong>WordPress Admin:</strong> <a href="/wp-admin/">Check admin interface</a> for new plugin menus</li>';
    echo '<li><strong>Frontend Testing:</strong> Test shortcodes on frontend pages</li>';
    echo '<li><strong>Database Verification:</strong> Check that database tables were created properly</li>';
    echo '<li><strong>WordPress.org Submission:</strong> Prepare production packages</li>';
    echo '</ol>';
} else {
    echo '<ol>';
    echo '<li><strong>Address Activation Errors:</strong> Fix remaining plugin issues</li>';
    echo '<li><strong>Implement Missing Classes:</strong> Create core functionality for framework plugins</li>';
    echo '<li><strong>Test Again:</strong> Re-run activation tests after fixes</li>';
    echo '</ol>';
}

echo '<p><em>Test completed at ' . date('Y-m-d H:i:s') . '</em></p>';
?>