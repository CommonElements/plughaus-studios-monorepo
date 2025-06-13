<?php
/**
 * Debug Plugin Status - Check which property management plugins are available
 */

// WordPress bootstrap
require_once('wp-config.php');
require_once('wp-load.php');

echo "<h1>Plugin Status Debug</h1>\n";

// Check plugin directory
$plugin_dir = WP_CONTENT_DIR . '/plugins/';
$plugins = array();

if (is_dir($plugin_dir)) {
    $dirs = scandir($plugin_dir);
    foreach ($dirs as $dir) {
        if (strpos($dir, 'plughaus-property') !== false || strpos($dir, 'property-management') !== false) {
            $plugins[] = $dir;
        }
    }
}

echo "<h2>Property Management Plugin Directories Found:</h2>\n";
echo "<ul>\n";
foreach ($plugins as $plugin) {
    $path = $plugin_dir . $plugin;
    echo "<li><strong>$plugin</strong> - " . (is_dir($path) ? 'Directory' : 'File') . "</li>\n";
    
    // Check for main plugin file
    $main_files = array(
        $path . '/plughaus-property-management.php',
        $path . '/plughaus-property-management-pro.php',
        $path . '/property-management.php'
    );
    
    foreach ($main_files as $file) {
        if (file_exists($file)) {
            echo "<li style='margin-left: 20px;'>→ Main file: " . basename($file) . "</li>\n";
            
            // Check if it has our Frontend Settings class
            $content = file_get_contents($file);
            if (strpos($content, 'PHPM_Frontend_Settings_Admin') !== false) {
                echo "<li style='margin-left: 40px; color: green;'>✓ Contains Frontend Settings Admin</li>\n";
            }
        }
    }
}
echo "</ul>\n";

// Check active plugins
echo "<h2>Active Plugins:</h2>\n";
$active_plugins = get_option('active_plugins');
echo "<ul>\n";
foreach ($active_plugins as $plugin_path) {
    if (strpos($plugin_path, 'property') !== false || strpos($plugin_path, 'plughaus') !== false) {
        echo "<li><strong>$plugin_path</strong> - ACTIVE</li>\n";
    }
}
echo "</ul>\n";

// Check if our classes exist
echo "<h2>Class Availability:</h2>\n";
$classes_to_check = array(
    'PlugHaus_Property_Management',
    'PHPM_Frontend_Settings_Admin',
    'PHPM_Admin',
    'PHPM_Post_Types'
);

echo "<ul>\n";
foreach ($classes_to_check as $class) {
    $exists = class_exists($class);
    $color = $exists ? 'green' : 'red';
    $status = $exists ? 'EXISTS' : 'NOT FOUND';
    echo "<li style='color: $color;'>$class - $status</li>\n";
}
echo "</ul>\n";

// Check admin menu items
echo "<h2>Admin Menu Structure:</h2>\n";
if (is_admin() || current_user_can('manage_options')) {
    global $menu, $submenu;
    
    echo "<h3>Main Menu Items (containing 'property'):</h3>\n";
    echo "<ul>\n";
    if (isset($menu) && is_array($menu)) {
        foreach ($menu as $item) {
            if (isset($item[0]) && (stripos($item[0], 'property') !== false || stripos($item[2], 'phpm') !== false)) {
                echo "<li>" . esc_html($item[0]) . " - " . esc_html($item[2]) . "</li>\n";
            }
        }
    }
    echo "</ul>\n";
    
    echo "<h3>Submenus for Property Management:</h3>\n";
    echo "<ul>\n";
    if (isset($submenu) && is_array($submenu)) {
        foreach ($submenu as $parent => $items) {
            if (stripos($parent, 'phpm') !== false || stripos($parent, 'property') !== false) {
                echo "<li><strong>Parent: $parent</strong></li>\n";
                foreach ($items as $subitem) {
                    echo "<li style='margin-left: 20px;'>→ " . esc_html($subitem[0]) . " - " . esc_html($subitem[2]) . "</li>\n";
                }
            }
        }
    }
    echo "</ul>\n";
}

echo "<h2>File System Check:</h2>\n";
$frontend_settings_file = $plugin_dir . 'plughaus-property-management/core/includes/admin/class-phpm-frontend-settings-admin.php';
echo "<p>Frontend Settings Admin file exists: " . (file_exists($frontend_settings_file) ? 'YES' : 'NO') . "</p>\n";
echo "<p>File path: $frontend_settings_file</p>\n";

if (file_exists($frontend_settings_file)) {
    $content = file_get_contents($frontend_settings_file);
    $has_init = strpos($content, 'public static function init()') !== false;
    echo "<p>File contains init() method: " . ($has_init ? 'YES' : 'NO') . "</p>\n";
    
    $has_menu = strpos($content, 'add_submenu_page') !== false;
    echo "<p>File contains submenu registration: " . ($has_menu ? 'YES' : 'NO') . "</p>\n";
}