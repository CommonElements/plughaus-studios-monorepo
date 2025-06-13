<?php
require_once('./wp-config.php');
require_once('./wp-load.php');

echo "Database Tables Check:\n";
echo "=====================\n";

global $wpdb;

// Check for Vireo plugin tables
$prefixes = ['phpm_', 'vsl_', 'erp_', 'ss_', 'de_'];
$total_tables = 0;

foreach ($prefixes as $prefix) {
    $tables = $wpdb->get_results("SHOW TABLES LIKE '{$wpdb->prefix}{$prefix}%'");
    if (!empty($tables)) {
        echo "\n" . strtoupper(str_replace('_', ' ', rtrim($prefix, '_'))) . " TABLES:\n";
        foreach ($tables as $table) {
            $table_name = array_values((array)$table)[0];
            echo "• $table_name\n";
            $total_tables++;
        }
    }
}

echo "\nTotal Plugin Tables: $total_tables\n";

// Check active plugins
$active_plugins = get_option('active_plugins');
echo "\nActive Plugins:\n";
foreach ($active_plugins as $plugin) {
    if (strpos($plugin, 'vireo') !== false || strpos($plugin, 'equiprent') !== false || strpos($plugin, 'studio') !== false) {
        echo "• $plugin\n";
    }
}

// Test a simple database query
echo "\nFunctionality Test:\n";
$property_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}phpm_properties");
if ($property_count !== null) {
    echo "✅ Property Management: $property_count properties in database\n";
} else {
    echo "❌ Property Management: No properties table or error\n";
}

$team_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}vsl_teams");
if ($team_count !== null) {
    echo "✅ Sports League: $team_count teams in database\n";
} else {
    echo "❌ Sports League: No teams table or error\n";
}

$equipment_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}erp_equipment");
if ($equipment_count !== null) {
    echo "✅ EquipRent Pro: $equipment_count equipment items in database\n";
} else {
    echo "❌ EquipRent Pro: No equipment table or error\n";
}
?>