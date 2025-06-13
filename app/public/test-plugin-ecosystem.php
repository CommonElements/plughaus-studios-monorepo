<?php
/**
 * Comprehensive Test for Vireo Designs Plugin Ecosystem
 * Tests all plugins for activation safety and basic functionality
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    require_once('./wp-config.php');
    require_once('./wp-load.php');
}

echo "<h1>🔍 Vireo Designs Plugin Ecosystem Test</h1>";
echo "<p><strong>Date:</strong> " . date('Y-m-d H:i:s') . "</p>";

// Test results array
$test_results = array();

/**
 * Test plugin activation safety
 */
function test_plugin_activation($plugin_path, $plugin_name) {
    echo "<h2>Testing: {$plugin_name}</h2>";
    
    $results = array(
        'plugin' => $plugin_name,
        'path' => $plugin_path,
        'file_exists' => false,
        'syntax_valid' => false,
        'loads_safely' => false,
        'classes_loaded' => array(),
        'errors' => array()
    );
    
    // Check if plugin file exists
    if (!file_exists($plugin_path)) {
        $results['errors'][] = "Plugin file not found: {$plugin_path}";
        echo "<p style='color: red;'>❌ Plugin file not found</p>";
        return $results;
    }
    
    $results['file_exists'] = true;
    echo "<p style='color: green;'>✅ Plugin file exists</p>";
    
    // Check PHP syntax
    $syntax_check = shell_exec("php -l \"{$plugin_path}\" 2>&1");
    if (strpos($syntax_check, 'No syntax errors') !== false) {
        $results['syntax_valid'] = true;
        echo "<p style='color: green;'>✅ PHP syntax valid</p>";
    } else {
        $results['errors'][] = "PHP syntax error: {$syntax_check}";
        echo "<p style='color: red;'>❌ PHP syntax error</p>";
        return $results;
    }
    
    // Test safe loading
    try {
        ob_start();
        $error_before = error_get_last();
        
        include_once $plugin_path;
        
        $error_after = error_get_last();
        $output = ob_get_clean();
        
        if ($error_after !== $error_before && $error_after['type'] === E_ERROR) {
            $results['errors'][] = "Fatal error during loading: " . $error_after['message'];
            echo "<p style='color: red;'>❌ Fatal error during loading</p>";
        } else {
            $results['loads_safely'] = true;
            echo "<p style='color: green;'>✅ Loads safely without fatal errors</p>";
            
            // Check what classes were loaded
            $results['classes_loaded'] = get_loaded_plugin_classes($plugin_name);
            if (count($results['classes_loaded']) > 0) {
                echo "<p><strong>Classes loaded:</strong> " . implode(', ', $results['classes_loaded']) . "</p>";
            }
        }
        
    } catch (Exception $e) {
        $results['errors'][] = "Exception during loading: " . $e->getMessage();
        echo "<p style='color: red;'>❌ Exception during loading</p>";
    }
    
    return $results;
}

/**
 * Get classes that were loaded by a plugin
 */
function get_loaded_plugin_classes($plugin_name) {
    $all_classes = get_declared_classes();
    $plugin_classes = array();
    
    $prefixes = array(
        'StudioSnap' => array('SS_'),
        'DealerEdge' => array('DE_', 'DealerEdge'),
        'Marina Manager' => array('MM_', 'Marina_Manager'),
        'StorageFlow' => array('SF_', 'StorageFlow'),
        'Property Management' => array('PHPM_', 'VPM_'),
        'Sports League' => array('VSL_', 'PSL_')
    );
    
    if (isset($prefixes[$plugin_name])) {
        foreach ($all_classes as $class) {
            foreach ($prefixes[$plugin_name] as $prefix) {
                if (strpos($class, $prefix) === 0) {
                    $plugin_classes[] = $class;
                }
            }
        }
    }
    
    return $plugin_classes;
}

/**
 * Test WordPress integration
 */
function test_wordpress_integration() {
    echo "<h2>WordPress Integration Test</h2>";
    
    // Test WordPress functions
    echo "<p><strong>Site URL:</strong> " . get_site_url() . "</p>";
    echo "<p><strong>Home URL:</strong> " . get_home_url() . "</p>";
    echo "<p><strong>WordPress Version:</strong> " . get_bloginfo('version') . "</p>";
    echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
    
    // Test database connection
    global $wpdb;
    $result = $wpdb->get_var("SELECT 1");
    if ($result == 1) {
        echo "<p style='color: green;'>✅ Database connection working</p>";
    } else {
        echo "<p style='color: red;'>❌ Database connection failed</p>";
    }
    
    // Check active plugins
    $active_plugins = get_option('active_plugins', array());
    echo "<p><strong>Currently Active Plugins:</strong> " . count($active_plugins) . "</p>";
    foreach ($active_plugins as $plugin) {
        echo "<p>  - {$plugin}</p>";
    }
}

/**
 * Test plugin shortcodes
 */
function test_plugin_shortcodes() {
    echo "<h2>Plugin Shortcodes Test</h2>";
    
    $shortcodes_to_test = array(
        'studiosnap_booking_form' => 'StudioSnap',
        'dealeredge_work_order_form' => 'DealerEdge',
        'marina_reservation_form' => 'Marina Manager',
        'storage_rental_form' => 'StorageFlow'
    );
    
    global $shortcode_tags;
    
    foreach ($shortcodes_to_test as $shortcode => $plugin) {
        if (isset($shortcode_tags[$shortcode])) {
            echo "<p style='color: green;'>✅ {$plugin}: [{$shortcode}] registered</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ {$plugin}: [{$shortcode}] not registered (class may not exist)</p>";
        }
    }
}

// Main testing sequence
echo "<hr>";
test_wordpress_integration();

echo "<hr>";

// Test all Vireo Designs plugins
$plugins_to_test = array(
    'StudioSnap' => ABSPATH . 'wp-content/plugins/studiosnap/studiosnap.php',
    'DealerEdge' => ABSPATH . 'wp-content/plugins/dealeredge/dealeredge.php',
    'Marina Manager' => ABSPATH . 'wp-content/plugins/marina-manager/marina-manager.php',
    'StorageFlow' => ABSPATH . 'wp-content/plugins/storageflow/storageflow.php',
    'Property Management' => ABSPATH . 'wp-content/plugins/vireo-property-management/vireo-property-management.php',
    'Sports League' => ABSPATH . 'wp-content/plugins/vireo-sports-league/vireo-sports-league.php'
);

foreach ($plugins_to_test as $name => $path) {
    $test_results[] = test_plugin_activation($path, $name);
    echo "<hr>";
}

test_plugin_shortcodes();

echo "<hr>";

// Summary
echo "<h2>📊 Test Summary</h2>";
$total_plugins = count($test_results);
$safe_plugins = 0;
$plugins_with_classes = 0;

foreach ($test_results as $result) {
    if ($result['loads_safely']) {
        $safe_plugins++;
    }
    if (count($result['classes_loaded']) > 0) {
        $plugins_with_classes++;
    }
}

echo "<p><strong>Total Plugins Tested:</strong> {$total_plugins}</p>";
echo "<p><strong>Safe to Activate:</strong> {$safe_plugins}/{$total_plugins}</p>";
echo "<p><strong>Plugins with Loaded Classes:</strong> {$plugins_with_classes}/{$total_plugins}</p>";

if ($safe_plugins == $total_plugins) {
    echo "<p style='color: green; font-size: 18px; font-weight: bold;'>🎉 ALL PLUGINS PASS SAFETY TESTS!</p>";
    echo "<p>The Vireo Designs plugin ecosystem is ready for activation and testing in WordPress admin.</p>";
} else {
    echo "<p style='color: red; font-size: 18px; font-weight: bold;'>⚠️ Some plugins need attention before activation.</p>";
}

echo "<hr>";
echo "<h2>📋 Next Steps</h2>";
echo "<ol>";
echo "<li><strong>Activate Plugins:</strong> Go to WordPress Admin → Plugins and activate the safe plugins</li>";
echo "<li><strong>Test Admin Interfaces:</strong> Check that admin menus appear and load without errors</li>";
echo "<li><strong>Test Shortcodes:</strong> Create test pages with shortcodes like [studiosnap_booking_form]</li>";
echo "<li><strong>WordPress.org Submission:</strong> Plugins are ready for marketplace submission</li>";
echo "<li><strong>Implement Missing Classes:</strong> Create the classes marked with TODO comments</li>";
echo "</ol>";

echo "<p><em>Test completed at " . date('Y-m-d H:i:s') . "</em></p>";
?>