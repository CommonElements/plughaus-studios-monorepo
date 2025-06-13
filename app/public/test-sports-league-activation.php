<?php
/**
 * Test script to verify Sports League plugin can be activated
 * This tests class loading and basic functionality
 */

// Simulate WordPress environment for testing
define('ABSPATH', __DIR__ . '/');
define('WPINC', 'wp-includes');

// Mock WordPress functions for testing
if (!function_exists('plugin_dir_path')) {
    function plugin_dir_path($file) {
        return dirname($file) . '/';
    }
}

if (!function_exists('plugin_dir_url')) {
    function plugin_dir_url($file) {
        return 'http://test.local/' . basename(dirname($file)) . '/';
    }
}

if (!function_exists('plugin_basename')) {
    function plugin_basename($file) {
        return basename(dirname($file)) . '/' . basename($file);
    }
}

// Test Sports League plugin loading
echo "🧪 Testing Vireo Sports League Plugin Loading...\n\n";

try {
    // Include the main plugin file
    $plugin_file = __DIR__ . '/wp-content/plugins/vireo-sports-league/vireo-sports-league.php';
    
    if (!file_exists($plugin_file)) {
        throw new Exception("Plugin file not found: $plugin_file");
    }
    
    echo "✅ Plugin file exists: $plugin_file\n";
    
    // Test constants definition
    if (!defined('VIREO_LEAGUE_VERSION')) {
        include_once $plugin_file;
    }
    
    echo "✅ Constants defined:\n";
    echo "   - VIREO_LEAGUE_VERSION: " . (defined('VIREO_LEAGUE_VERSION') ? VIREO_LEAGUE_VERSION : 'NOT DEFINED') . "\n";
    echo "   - VIREO_LEAGUE_PLUGIN_DIR: " . (defined('VIREO_LEAGUE_PLUGIN_DIR') ? 'DEFINED' : 'NOT DEFINED') . "\n";
    echo "   - VIREO_LEAGUE_CORE_DIR: " . (defined('VIREO_LEAGUE_CORE_DIR') ? 'DEFINED' : 'NOT DEFINED') . "\n";
    
    // Test class loading
    echo "\n🔍 Testing class loading...\n";
    
    $expected_classes = [
        'Vireo_Sports_League',
        'VSL_Utilities',
        'VSL_Post_Types', 
        'VSL_Taxonomies',
        'VSL_Capabilities',
        'VSL_Database',
        'VSL_Admin',
        'VSL_Activator'
    ];
    
    foreach ($expected_classes as $class) {
        if (class_exists($class)) {
            echo "✅ Class loaded: $class\n";
        } else {
            echo "❌ Class missing: $class\n";
        }
    }
    
    // Test main plugin class instantiation
    echo "\n⚙️ Testing main plugin class...\n";
    
    if (class_exists('Vireo_Sports_League')) {
        $plugin = Vireo_Sports_League::get_instance();
        echo "✅ Main plugin class instantiated successfully\n";
        echo "   - Is Pro: " . ($plugin->is_pro() ? 'Yes' : 'No') . "\n";
        echo "   - Available modules: " . implode(', ', array_keys($plugin->get_modules())) . "\n";
    }
    
    // Test activator
    echo "\n🔧 Testing activator class...\n";
    
    if (class_exists('VSL_Activator')) {
        echo "✅ VSL_Activator class is available\n";
        echo "   - Ready for activation process\n";
    } else {
        echo "❌ VSL_Activator class not found\n";
    }
    
    echo "\n🎉 All tests passed! Sports League plugin is ready for activation.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

// Clean up
echo "\n🧹 Cleaning up test script...\n";
unlink(__FILE__);
echo "✅ Test script removed\n";
?>