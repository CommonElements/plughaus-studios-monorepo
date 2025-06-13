<?php
/**
 * Test License System Setup
 */

require_once('./wp-load.php');

echo "<h1>🔧 Testing License System</h1>\n";

// Check if license manager is loaded
if (class_exists('Vireo_License_Manager')) {
    echo "<p>✅ Vireo_License_Manager class loaded</p>\n";
} else {
    echo "<p>❌ Vireo_License_Manager class not loaded</p>\n";
    echo "<p>Checking if file exists...</p>\n";
    
    $license_file = get_template_directory() . '/inc/license-manager.php';
    if (file_exists($license_file)) {
        echo "<p>✅ License manager file exists at: $license_file</p>\n";
        require_once $license_file;
        if (class_exists('Vireo_License_Manager')) {
            echo "<p>✅ License manager class loaded after manual include</p>\n";
        }
    } else {
        echo "<p>❌ License manager file not found at: $license_file</p>\n";
    }
}

// Check database table
global $wpdb;
$table_name = $wpdb->prefix . 'vireo_licenses';
$table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");

if ($table_exists) {
    echo "<p>✅ License table exists: $table_name</p>\n";
} else {
    echo "<p>❌ License table not found: $table_name</p>\n";
}

// Check WooCommerce integration
if (class_exists('WooCommerce')) {
    echo "<p>✅ WooCommerce active</p>\n";
    
    // Check for hooks
    $hooks_registered = array(
        'woocommerce_order_status_completed',
        'woocommerce_order_status_processing'
    );
    
    foreach ($hooks_registered as $hook) {
        if (has_action($hook)) {
            echo "<p>✅ Hook registered: $hook</p>\n";
        } else {
            echo "<p>❌ Hook not registered: $hook</p>\n";
        }
    }
} else {
    echo "<p>❌ WooCommerce not active</p>\n";
}

echo "<h2>🛒 WooCommerce Products Check</h2>\n";

$products = wc_get_products(array(
    'limit' => -1,
    'name' => 'Pro'
));

echo "<p>Found " . count($products) . " Pro products</p>\n";

foreach ($products as $product) {
    echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>\n";
    echo "<h3>{$product->get_name()}</h3>\n";
    echo "<p>ID: {$product->get_id()}</p>\n";
    echo "<p>Price: {$product->get_price_html()}</p>\n";
    echo "<p>Downloadable: " . ($product->is_downloadable() ? 'Yes' : 'No') . "</p>\n";
    echo "<p>Virtual: " . ($product->is_virtual() ? 'Yes' : 'No') . "</p>\n";
    
    $requires_license = get_post_meta($product->get_id(), '_requires_license', true);
    $max_sites = get_post_meta($product->get_id(), '_license_max_sites', true);
    
    echo "<p>Requires License: " . ($requires_license ? $requires_license : 'Not set') . "</p>\n";
    echo "<p>Max Sites: " . ($max_sites ? $max_sites : 'Not set') . "</p>\n";
    echo "</div>\n";
}

echo "<h2>📧 Test License Validation API</h2>\n";

// Test validation endpoint
$test_url = home_url('/wp-json/vireo/v1/validate-license');
echo "<p>API Endpoint: <a href='$test_url' target='_blank'>$test_url</a></p>\n";

echo "<h2>🎯 Next Steps</h2>\n";
echo "<ul>\n";
echo "<li>Update plugin cards to link to Pro versions in shop</li>\n";
echo "<li>Create Pro plugin ZIP files for download</li>\n";
echo "<li>Test complete purchase → license → download flow</li>\n";
echo "<li>Configure Stripe test/live keys</li>\n";
echo "</ul>\n";

?>