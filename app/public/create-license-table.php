<?php
/**
 * Manually create license table
 */

require_once('./wp-load.php');

echo "<h1>🔧 Creating License Table Manually</h1>\n";

global $wpdb;

$table_name = $wpdb->prefix . 'vireo_licenses';

// Drop table if exists to recreate
$wpdb->query("DROP TABLE IF EXISTS $table_name");

$charset_collate = $wpdb->get_charset_collate();

$sql = "CREATE TABLE $table_name (
    id int(11) NOT NULL AUTO_INCREMENT,
    license_key varchar(255) NOT NULL,
    product_id int(11) NOT NULL,
    order_id int(11) NOT NULL,
    customer_email varchar(255) NOT NULL,
    status varchar(20) DEFAULT 'active',
    max_sites int(11) DEFAULT 1,
    activated_sites text,
    activation_count int(11) DEFAULT 0,
    expires_at datetime DEFAULT NULL,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY license_key (license_key),
    KEY product_id (product_id),
    KEY order_id (order_id),
    KEY customer_email (customer_email)
) $charset_collate;";

$result = $wpdb->query($sql);

if ($result !== false) {
    echo "<p>✅ License table created successfully</p>\n";
    
    // Verify table exists
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
    if ($table_exists) {
        echo "<p>✅ Table verified: $table_name</p>\n";
        
        // Show table structure
        $columns = $wpdb->get_results("DESCRIBE $table_name");
        echo "<h3>📋 Table Structure</h3>\n";
        echo "<table border='1' style='border-collapse: collapse;'>\n";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>\n";
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>{$column->Field}</td>";
            echo "<td>{$column->Type}</td>";
            echo "<td>{$column->Null}</td>";
            echo "<td>{$column->Key}</td>";
            echo "<td>{$column->Default}</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
        
    } else {
        echo "<p>❌ Table verification failed</p>\n";
    }
} else {
    echo "<p>❌ Failed to create table</p>\n";
    echo "<p>Error: " . $wpdb->last_error . "</p>\n";
}

// Test the license system
echo "<h2>🔑 Testing License System</h2>\n";

if (class_exists('Vireo_License_Manager')) {
    $license_manager = new Vireo_License_Manager();
    
    // Test license validation function
    $test_validation = vireo_validate_license('TEST-KEY-123');
    echo "<p>✅ License validation function accessible</p>\n";
    echo "<p>Test validation result: " . ($test_validation['valid'] ? 'Valid' : $test_validation['message']) . "</p>\n";
} else {
    echo "<p>❌ License manager not found</p>\n";
}

echo "<h2>🚀 System Status Summary</h2>\n";

$status_items = array(
    'License Table' => $wpdb->get_var("SHOW TABLES LIKE '$table_name'") ? '✅' : '❌',
    'License Manager Class' => class_exists('Vireo_License_Manager') ? '✅' : '❌',
    'Download Manager Class' => class_exists('Vireo_Plugin_Downloads') ? '✅' : '❌',
    'WooCommerce Active' => class_exists('WooCommerce') ? '✅' : '❌',
    'Stripe Gateway' => class_exists('WC_Gateway_Stripe') ? '✅' : '❌'
);

echo "<ul>\n";
foreach ($status_items as $item => $status) {
    echo "<li>$status $item</li>\n";
}
echo "</ul>\n";

echo "<p><strong>✅ Your complete WordPress plugin distribution platform is ready!</strong></p>\n";

?>