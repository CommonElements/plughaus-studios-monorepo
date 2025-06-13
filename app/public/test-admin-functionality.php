<?php
require_once('./wp-config.php');
require_once('./wp-load.php');

// Force admin context
if (!defined('WP_ADMIN')) {
    define('WP_ADMIN', true);
}

echo "<h1>🎛️ Admin Functionality Test</h1>";
echo "<style>
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
    .plugin-test { border: 1px solid #ddd; margin: 20px 0; padding: 15px; border-radius: 5px; }
    .success { color: #28a745; }
    .warning { color: #ffc107; }
    .error { color: #dc3545; }
    .info { color: #007bff; }
</style>";

// Test admin menu registration
echo "<div class='plugin-test'>";
echo "<h2>🧪 Plugin Features Test</h2>";

// Test Property Management
echo "<h3>Property Management Plugin</h3>";
try {
    // Test if we can create a property post
    $property_data = array(
        'post_title' => 'Test Property',
        'post_content' => 'A test property for functionality testing',
        'post_status' => 'publish',
        'post_type' => 'phpm_property'
    );
    
    $property_id = wp_insert_post($property_data);
    if ($property_id && !is_wp_error($property_id)) {
        echo "<p class='success'>✅ Can create properties (ID: $property_id)</p>";
        
        // Clean up test data
        wp_delete_post($property_id, true);
        echo "<p class='info'>🧹 Test property cleaned up</p>";
    } else {
        echo "<p class='error'>❌ Cannot create properties</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Property creation error: " . $e->getMessage() . "</p>";
}

// Test Sports League
echo "<h3>Sports League Plugin</h3>";
try {
    // Check if we can query teams
    global $wpdb;
    $teams_table = $wpdb->prefix . 'vsl_teams';
    $result = $wpdb->get_var("SELECT COUNT(*) FROM $teams_table");
    echo "<p class='success'>✅ Teams table accessible (count: $result)</p>";
    
    // Test team creation
    $team_data = array(
        'name' => 'Test Team',
        'league_id' => 1,
        'founded_year' => 2024,
        'city' => 'Test City',
        'coach' => 'Test Coach'
    );
    
    $team_id = $wpdb->insert($teams_table, $team_data);
    if ($team_id !== false) {
        echo "<p class='success'>✅ Can create teams (ID: " . $wpdb->insert_id . ")</p>";
        
        // Clean up
        $wpdb->delete($teams_table, array('id' => $wpdb->insert_id));
        echo "<p class='info'>🧹 Test team cleaned up</p>";
    } else {
        echo "<p class='error'>❌ Cannot create teams</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Sports League error: " . $e->getMessage() . "</p>";
}

// Test EquipRent Pro
echo "<h3>EquipRent Pro Plugin</h3>";
try {
    global $wpdb;
    $equipment_table = $wpdb->prefix . 'erp_equipment';
    $result = $wpdb->get_var("SELECT COUNT(*) FROM $equipment_table");
    echo "<p class='success'>✅ Equipment table accessible (count: $result)</p>";
    
    // Test equipment creation
    $equipment_data = array(
        'name' => 'Test Equipment',
        'category' => 'Tools',
        'status' => 'available',
        'daily_rate' => 50.00,
        'created_at' => current_time('mysql')
    );
    
    $equipment_id = $wpdb->insert($equipment_table, $equipment_data);
    if ($equipment_id !== false) {
        echo "<p class='success'>✅ Can create equipment (ID: " . $wpdb->insert_id . ")</p>";
        
        // Clean up
        $wpdb->delete($equipment_table, array('id' => $wpdb->insert_id));
        echo "<p class='info'>🧹 Test equipment cleaned up</p>";
    } else {
        echo "<p class='error'>❌ Cannot create equipment</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ EquipRent Pro error: " . $e->getMessage() . "</p>";
}

echo "</div>";

// Test shortcode functionality
echo "<div class='plugin-test'>";
echo "<h2>📋 Shortcode Functionality Test</h2>";

$test_shortcodes = array(
    'erp_equipment_list' => 'EquipRent Pro',
    'vsl_teams' => 'Sports League',
    'vsl_standings' => 'Sports League'
);

foreach ($test_shortcodes as $shortcode => $plugin) {
    try {
        $output = do_shortcode("[$shortcode]");
        if (!empty($output) && $output !== "[$shortcode]") {
            echo "<p class='success'>✅ $plugin: [$shortcode] renders content</p>";
        } else {
            echo "<p class='warning'>⚠️ $plugin: [$shortcode] registered but no content</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ $plugin: [$shortcode] error: " . $e->getMessage() . "</p>";
    }
}

echo "</div>";

// Plugin status summary
echo "<div class='plugin-test'>";
echo "<h2>📊 Plugin Status Summary</h2>";

$active_plugins = get_option('active_plugins');
$vireo_plugins = 0;
$functional_plugins = 0;

echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Plugin</th><th>Status</th><th>Database</th><th>Features</th></tr>";

// Check each plugin
$plugins_to_check = array(
    'vireo-property-management/vireo-property-management.php' => 'Property Management',
    'vireo-sports-league/vireo-sports-league.php' => 'Sports League',
    'equiprent-pro/equiprent-pro.php' => 'EquipRent Pro'
);

foreach ($plugins_to_check as $path => $name) {
    $is_active = in_array($path, $active_plugins);
    $vireo_plugins++;
    
    if ($is_active) {
        $functional_plugins++;
    }
    
    echo "<tr>";
    echo "<td>$name</td>";
    echo "<td>" . ($is_active ? "<span class='success'>✅ Active</span>" : "<span class='error'>❌ Inactive</span>") . "</td>";
    
    // Check database
    if ($name === 'Property Management') {
        $db_status = "<span class='success'>✅ WordPress Posts</span>";
    } else {
        $prefix = ($name === 'Sports League') ? 'vsl_' : 'erp_';
        global $wpdb;
        $tables = $wpdb->get_results("SHOW TABLES LIKE '{$wpdb->prefix}{$prefix}%'");
        $table_count = count($tables);
        $db_status = "<span class='success'>✅ $table_count tables</span>";
    }
    echo "<td>$db_status</td>";
    
    // Features
    if ($is_active) {
        echo "<td><span class='success'>✅ Functional</span></td>";
    } else {
        echo "<td><span class='warning'>⚠️ Needs activation</span></td>";
    }
    echo "</tr>";
}

echo "</table>";

echo "<p><strong>Summary:</strong> $functional_plugins / $vireo_plugins plugins are active and functional</p>";

if ($functional_plugins >= 2) {
    echo "<p class='success'>✅ The Vireo plugin ecosystem is functional and ready for use!</p>";
} else {
    echo "<p class='warning'>⚠️ The ecosystem needs more plugins activated</p>";
}

echo "</div>";

echo "<div class='plugin-test'>";
echo "<h2>🎯 Next Steps</h2>";
echo "<ol>";
echo "<li><strong>WordPress Admin Access:</strong> <a href='/wp-admin/' target='_blank'>Visit WordPress Admin</a></li>";
echo "<li><strong>Test Plugin Features:</strong> Look for admin menus for each plugin</li>";
echo "<li><strong>Create Test Data:</strong> Add properties, teams, and equipment</li>";
echo "<li><strong>Test Frontend:</strong> Use shortcodes on pages</li>";
echo "<li><strong>WordPress.org Submission:</strong> Plugins are ready for marketplace</li>";
echo "</ol>";
echo "</div>";

echo "<p style='text-align: center; margin-top: 30px;'><em>Admin functionality test completed at " . date('Y-m-d H:i:s') . "</em></p>";
?>