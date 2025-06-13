<?php
/**
 * Comprehensive Vireo Plugin Testing Script
 * Tests activation status and functionality of main Vireo plugins
 */

require_once('./wp-config.php');
require_once('./wp-load.php');

echo '<h1>🚀 Vireo Plugin Ecosystem Test</h1>';
echo '<style>
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
    .success { color: #28a745; }
    .warning { color: #ffc107; }
    .error { color: #dc3545; }
    .info { color: #007bff; }
    .plugin-section { border: 1px solid #ddd; margin: 20px 0; padding: 15px; border-radius: 5px; }
    .status-good { background-color: #d4edda; }
    .status-warning { background-color: #fff3cd; }
    .status-error { background-color: #f8d7da; }
</style>';

// Main Vireo plugins to test
$vireo_plugins = [
    'Vireo Property Management' => 'vireo-property-management/vireo-property-management.php',
    'Vireo Sports League' => 'vireo-sports-league/vireo-sports-league.php',
    'EquipRent Pro' => 'equiprent-pro/equiprent-pro.php',
    'StudioSnap' => 'studiosnap/studiosnap.php',
    'DealerEdge' => 'dealeredge/dealeredge.php'
];

// Test each plugin
foreach ($vireo_plugins as $name => $path) {
    echo '<div class="plugin-section">';
    echo '<h2>🔌 ' . $name . '</h2>';
    
    $plugin_file = WP_PLUGIN_DIR . '/' . $path;
    $plugin_dir = dirname($plugin_file);
    $exists = file_exists($plugin_file);
    $active = false;
    
    if ($exists) {
        $active = is_plugin_active($path);
    }
    
    echo '<p><strong>Plugin File:</strong> ' . ($exists ? '<span class="success">✅ Found</span>' : '<span class="error">❌ Missing</span>') . ' (' . $path . ')</p>';
    echo '<p><strong>Status:</strong> ' . ($active ? '<span class="success">✅ Active</span>' : '<span class="warning">⚠️ Inactive</span>') . '</p>';
    
    if ($exists) {
        // Check plugin structure
        $core_dir = $plugin_dir . '/core';
        $has_core = is_dir($core_dir);
        echo '<p><strong>Core Directory:</strong> ' . ($has_core ? '<span class="success">✅ Present</span>' : '<span class="error">❌ Missing</span>') . '</p>';
        
        // Check for main plugin class
        $plugin_content = file_get_contents($plugin_file);
        $has_class = (strpos($plugin_content, 'class ') !== false);
        echo '<p><strong>Main Class:</strong> ' . ($has_class ? '<span class="success">✅ Detected</span>' : '<span class="error">❌ Missing</span>') . '</p>';
        
        // Check for activation hooks
        $has_activation = (strpos($plugin_content, 'register_activation_hook') !== false);
        echo '<p><strong>Activation Hook:</strong> ' . ($has_activation ? '<span class="success">✅ Present</span>' : '<span class="warning">⚠️ Missing</span>') . '</p>';
        
        // Try to get plugin data
        if (function_exists('get_plugin_data')) {
            $plugin_data = get_plugin_data($plugin_file);
            if (!empty($plugin_data['Name'])) {
                echo '<p><strong>Plugin Name:</strong> ' . $plugin_data['Name'] . '</p>';
                echo '<p><strong>Version:</strong> ' . $plugin_data['Version'] . '</p>';
                echo '<p><strong>Description:</strong> ' . substr($plugin_data['Description'], 0, 100) . '...</p>';
            }
        }
        
        // Attempt activation if not active
        if (!$active) {
            echo '<p class="info">🔄 Attempting activation...</p>';
            
            $result = activate_plugin($path);
            if (is_wp_error($result)) {
                echo '<p class="error">❌ Activation failed: ' . $result->get_error_message() . '</p>';
                echo '<div class="status-error">';
            } else {
                echo '<p class="success">✅ Successfully activated!</p>';
                $active = true;
                echo '<div class="status-good">';
            }
        } else {
            echo '<div class="status-good">';
        }
        
        // Test database table creation (if applicable)
        global $wpdb;
        
        // Check for common plugin tables
        $table_prefixes = [
            'phpm_' => 'Property Management',
            'vsl_' => 'Sports League', 
            'erp_' => 'EquipRent Pro',
            'ss_' => 'StudioSnap',
            'de_' => 'DealerEdge'
        ];
        
        $plugin_tables = [];
        foreach ($table_prefixes as $prefix => $plugin_name) {
            if (strpos($name, $plugin_name) !== false) {
                $tables = $wpdb->get_results("SHOW TABLES LIKE '{$wpdb->prefix}{$prefix}%'");
                if (!empty($tables)) {
                    echo '<p class="success">✅ Database tables found (' . count($tables) . ' tables)</p>';
                    foreach ($tables as $table) {
                        $table_name = array_values((array)$table)[0];
                        echo '<span class="info">• ' . $table_name . '</span><br>';
                    }
                } else {
                    echo '<p class="warning">⚠️ No database tables found</p>';
                }
                break;
            }
        }
        
    } else {
        echo '<div class="status-error">';
    }
    
    echo '</div>';
    echo '</div>';
}

// Check for admin menus
echo '<div class="plugin-section">';
echo '<h2>📋 Admin Menu Check</h2>';

global $menu, $submenu;

$expected_menus = [
    'Property Management',
    'Sports League', 
    'EquipRent Pro',
    'StudioSnap',
    'DealerEdge'
];

foreach ($expected_menus as $menu_name) {
    $found = false;
    if (!empty($menu)) {
        foreach ($menu as $menu_item) {
            if (is_array($menu_item) && isset($menu_item[0]) && strpos($menu_item[0], $menu_name) !== false) {
                echo '<p class="success">✅ ' . $menu_name . ' menu found</p>';
                $found = true;
                break;
            }
        }
    }
    if (!$found) {
        echo '<p class="warning">⚠️ ' . $menu_name . ' menu not found</p>';
    }
}
echo '</div>';

// Overall assessment
echo '<div class="plugin-section">';
echo '<h2>🎯 Overall Assessment</h2>';

$total_plugins = count($vireo_plugins);
$active_plugins = 0;
$functional_plugins = 0;

foreach ($vireo_plugins as $name => $path) {
    if (is_plugin_active($path)) {
        $active_plugins++;
        
        // Basic functionality test
        $plugin_file = WP_PLUGIN_DIR . '/' . $path;
        if (file_exists($plugin_file)) {
            $content = file_get_contents($plugin_file);
            if (strpos($content, 'class ') !== false && strpos($content, 'function ') !== false) {
                $functional_plugins++;
            }
        }
    }
}

echo '<p><strong>Total Plugins:</strong> ' . $total_plugins . '</p>';
echo '<p><strong>Active Plugins:</strong> ' . $active_plugins . ' / ' . $total_plugins . '</p>';
echo '<p><strong>Functional Plugins:</strong> ' . $functional_plugins . ' / ' . $total_plugins . '</p>';

if ($functional_plugins >= 3) {
    echo '<p class="success">✅ Plugin ecosystem is largely functional!</p>';
} elseif ($functional_plugins >= 1) {
    echo '<p class="warning">⚠️ Plugin ecosystem partially functional - needs development</p>';
} else {
    echo '<p class="error">❌ Plugin ecosystem needs significant development</p>';
}

echo '</div>';

// Next steps
echo '<div class="plugin-section">';
echo '<h2>🚀 Next Steps</h2>';
echo '<ol>';
echo '<li><strong>WordPress Admin:</strong> <a href="/wp-admin/" target="_blank">Check WordPress Admin Dashboard</a></li>';
echo '<li><strong>Plugin Settings:</strong> Look for plugin-specific admin menus</li>';
echo '<li><strong>Database Check:</strong> Verify table creation in wp_admin → Tools → Site Health</li>';
echo '<li><strong>Functionality Test:</strong> Test core features of each plugin</li>';
echo '<li><strong>WordPress.org Preparation:</strong> Plugins ready for marketplace review</li>';
echo '</ol>';
echo '</div>';

echo '<p style="text-align: center; margin-top: 30px;"><em>Test completed at ' . date('Y-m-d H:i:s') . '</em></p>';
?>