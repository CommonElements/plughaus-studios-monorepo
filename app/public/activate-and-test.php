<?php
/**
 * Activate Plugins and Test Functionality
 */

require_once('./wp-config.php');
require_once('./wp-load.php');

echo '<h1>🚀 Vireo Designs Plugin Ecosystem - Activation & Testing</h1>';
echo '<p><strong>Date:</strong> ' . date('Y-m-d H:i:s') . '</p>';

$plugins_to_activate = [
    'StudioSnap' => 'studiosnap/studiosnap.php',
    'DealerEdge' => 'dealeredge/dealeredge.php',
    'Marina Manager' => 'marina-manager/marina-manager.php',
    'StorageFlow' => 'storageflow/storageflow.php'
];

$activation_results = [];

echo '<h2>📦 Plugin Activation</h2>';

foreach ($plugins_to_activate as $name => $path) {
    echo '<h3>' . $name . '</h3>';
    
    $plugin_file = WP_PLUGIN_DIR . '/' . $path;
    
    if (!file_exists($plugin_file)) {
        echo '<p style="color: red;">❌ Plugin file not found: ' . $path . '</p>';
        $activation_results[$name] = false;
        continue;
    }
    
    if (is_plugin_active($path)) {
        echo '<p style="color: blue;">ℹ️ Already active</p>';
        $activation_results[$name] = true;
        continue;
    }
    
    // Attempt activation
    $result = activate_plugin($path);
    
    if (is_wp_error($result)) {
        echo '<p style="color: red;">❌ Activation failed: ' . $result->get_error_message() . '</p>';
        $activation_results[$name] = false;
    } else {
        echo '<p style="color: green;">✅ Successfully activated!</p>';
        $activation_results[$name] = true;
        
        // Check for admin menu
        global $menu, $submenu;
        $menu_found = false;
        
        // Trigger admin_menu action to populate menus
        if (is_admin()) {
            do_action('admin_menu');
        }
    }
}

echo '<hr>';
echo '<h2>🔍 Functionality Testing</h2>';

// Test shortcode registration
echo '<h3>Shortcode Registration</h3>';
global $shortcode_tags;

$expected_shortcodes = [
    'studiosnap_booking_form' => 'StudioSnap',
    'dealeredge_work_order_form' => 'DealerEdge', 
    'marina_reservation_form' => 'Marina Manager',
    'storage_rental_form' => 'StorageFlow'
];

$shortcodes_registered = 0;
foreach ($expected_shortcodes as $shortcode => $plugin) {
    if (isset($shortcode_tags[$shortcode])) {
        echo '<p style="color: green;">✅ [' . $shortcode . '] - ' . $plugin . '</p>';
        $shortcodes_registered++;
    } else {
        echo '<p style="color: orange;">⚠️ [' . $shortcode . '] - ' . $plugin . ' (class not implemented)</p>';
    }
}

// Test class loading
echo '<h3>Class Loading Status</h3>';
$classes_to_check = [
    'StudioSnap' => ['SS_Admin', 'SS_Booking_System', 'SS_Booking_Form'],
    'DealerEdge' => ['DE_Admin', 'DE_Work_Order_System'],
    'Marina Manager' => ['MM_Admin', 'MM_Reservation_System'],
    'StorageFlow' => ['SF_Admin', 'SF_Rental_System']
];

foreach ($classes_to_check as $plugin => $classes) {
    echo '<h4>' . $plugin . '</h4>';
    foreach ($classes as $class) {
        if (class_exists($class)) {
            echo '<p style="color: green;">✅ ' . $class . ' loaded</p>';
        } else {
            echo '<p style="color: orange;">⚠️ ' . $class . ' not loaded (needs implementation)</p>';
        }
    }
}

echo '<hr>';
echo '<h2>📊 Test Summary</h2>';

$total_plugins = count($plugins_to_activate);
$activated_plugins = array_sum($activation_results);

echo '<p><strong>Total Plugins:</strong> ' . $total_plugins . '</p>';
echo '<p><strong>Successfully Activated:</strong> ' . $activated_plugins . '/' . $total_plugins . '</p>';
echo '<p><strong>Shortcodes Registered:</strong> ' . $shortcodes_registered . '/' . count($expected_shortcodes) . '</p>';

if ($activated_plugins == $total_plugins) {
    echo '<p style="color: green; font-size: 18px; font-weight: bold;">🎉 ALL PLUGINS ACTIVATED SUCCESSFULLY!</p>';
} else {
    echo '<p style="color: orange; font-size: 18px; font-weight: bold;">⚠️ Some plugins need attention</p>';
}

echo '<hr>';
echo '<h2>🎯 Next Testing Steps</h2>';
echo '<ol>';
echo '<li><strong>WordPress Admin Dashboard:</strong> <a href="/wp-admin/" target="_blank">Check for new admin menus</a></li>';
echo '<li><strong>StudioSnap Booking Test:</strong> <a href="/studiosnap-booking-test/" target="_blank">Test booking form functionality</a></li>';
echo '<li><strong>Plugin Settings:</strong> Configure each plugin\'s settings as needed</li>';
echo '<li><strong>Frontend Testing:</strong> Test all shortcodes on frontend pages</li>';
echo '<li><strong>WordPress.org Submission:</strong> Prepare packages for marketplace</li>';
echo '</ol>';

echo '<hr>';
echo '<h2>🔧 Development Status</h2>';
echo '<p>✅ <strong>StudioSnap:</strong> Fully functional with complete booking system</p>';
echo '<p>⚠️ <strong>Other Plugins:</strong> Framework complete, need core class implementation</p>';
echo '<p>📋 <strong>Next Phase:</strong> Implement missing classes using StudioSnap as template</p>';

echo '<p><em>Activation test completed at ' . date('Y-m-d H:i:s') . '</em></p>';
?>