<?php
/**
 * Simple EquipRent Pro Plugin Test
 */

echo "🔧 Testing EquipRent Pro Plugin Loading\n";
echo "=======================================\n\n";

// Set up basic WordPress constants
define('ABSPATH', __DIR__ . '/');
define('WP_CONTENT_DIR', __DIR__ . '/wp-content');
define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');

// Check if plugin file exists
$plugin_path = WP_PLUGIN_DIR . '/equiprent-pro/equiprent-pro.php';

if (!file_exists($plugin_path)) {
    echo "❌ Plugin file not found: $plugin_path\n";
    exit(1);
}

echo "✅ Plugin file found: $plugin_path\n";

// Mock WordPress functions that the plugin needs
if (!function_exists('plugin_dir_path')) {
    function plugin_dir_path($file) {
        return dirname($file) . '/';
    }
}

if (!function_exists('plugin_dir_url')) {
    function plugin_dir_url($file) {
        return 'http://localhost/wp-content/plugins/' . basename(dirname($file)) . '/';
    }
}

if (!function_exists('plugin_basename')) {
    function plugin_basename($file) {
        return str_replace(WP_PLUGIN_DIR . '/', '', $file);
    }
}

if (!function_exists('add_action')) {
    function add_action($hook, $callback, $priority = 10, $accepted_args = 1) {
        return true;
    }
}

if (!function_exists('register_activation_hook')) {
    function register_activation_hook($file, $callback) {
        return true;
    }
}

if (!function_exists('register_deactivation_hook')) {
    function register_deactivation_hook($file, $callback) {
        return true;
    }
}

if (!function_exists('load_plugin_textdomain')) {
    function load_plugin_textdomain($domain, $deprecated, $plugin_rel_path) {
        return true;
    }
}

if (!function_exists('current_time')) {
    function current_time($type) {
        return date('Y-m-d H:i:s');
    }
}

if (!function_exists('wp_generate_password')) {
    function wp_generate_password($length = 12, $special_chars = true) {
        return 'TEST' . rand(1000, 9999);
    }
}

echo "🔄 Attempting to load plugin...\n";

// Suppress any output during plugin loading
ob_start();
$error = null;

try {
    // Include the main plugin file
    include_once $plugin_path;
    
    echo "✅ Plugin loaded without fatal errors!\n\n";
    
    // Check if main class exists
    if (class_exists('EquipRent_Pro')) {
        echo "✅ Main EquipRent_Pro class found\n";
    } else {
        echo "❌ Main EquipRent_Pro class not found\n";
    }
    
    // Check if constants are defined
    $constants_to_check = ['ERP_VERSION', 'ERP_PLUGIN_DIR', 'ERP_PLUGIN_URL', 'ERP_CORE_DIR'];
    
    echo "\n🔍 Checking plugin constants:\n";
    foreach ($constants_to_check as $constant) {
        if (defined($constant)) {
            echo "  ✅ $constant = " . constant($constant) . "\n";
        } else {
            echo "  ❌ $constant not defined\n";
        }
    }
    
    // Check for other important classes
    $classes_to_check = [
        'ERP_Equipment',
        'ERP_Customer', 
        'ERP_Booking',
        'ERP_Utilities',
        'ERP_Admin'
    ];
    
    echo "\n🏗️  Checking if core classes are defined:\n";
    foreach ($classes_to_check as $class) {
        if (class_exists($class)) {
            echo "  ✅ $class\n";
        } else {
            echo "  ❌ $class\n";
        }
    }
    
} catch (Error $e) {
    echo "❌ Fatal Error: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    $error = $e;
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    $error = $e;
}

$output = ob_get_clean();
if (!empty($output)) {
    echo "\n⚠️  Plugin Output:\n";
    echo $output;
}

if ($error) {
    echo "\n❌ Plugin loading failed with errors.\n";
    exit(1);
} else {
    echo "\n🎯 Plugin loading test completed successfully!\n";
    echo "Plugin appears to be structurally sound and ready for activation.\n";
}
?>