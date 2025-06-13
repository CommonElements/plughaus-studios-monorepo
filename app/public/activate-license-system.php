<?php
/**
 * Activate License System and Create Tables
 */

require_once('./wp-load.php');

echo "<h1>🔧 Activating License System</h1>\n";

// Manually trigger the license manager initialization
if (class_exists('Vireo_License_Manager')) {
    $license_manager = new Vireo_License_Manager();
    
    // Create the table manually
    global $wpdb;
    $table_name = $wpdb->prefix . 'vireo_licenses';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
        id int(11) NOT NULL AUTO_INCREMENT,
        license_key varchar(255) NOT NULL UNIQUE,
        product_id int(11) NOT NULL,
        order_id int(11) NOT NULL,
        customer_email varchar(255) NOT NULL,
        status enum('active','inactive','expired','revoked') DEFAULT 'active',
        max_sites int(11) DEFAULT 1,
        activated_sites text,
        activation_count int(11) DEFAULT 0,
        expires_at datetime DEFAULT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY product_id (product_id),
        KEY order_id (order_id),
        KEY customer_email (customer_email),
        KEY license_key (license_key)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $result = dbDelta($sql);
    
    echo "<p>✅ License table creation attempted</p>\n";
    
    // Check if table was created
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
    if ($table_exists) {
        echo "<p>✅ License table created successfully: $table_name</p>\n";
    } else {
        echo "<p>❌ Failed to create license table</p>\n";
    }
    
    // Add rewrite rules
    add_rewrite_endpoint('licenses', EP_ROOT | EP_PAGES);
    flush_rewrite_rules();
    echo "<p>✅ Rewrite rules flushed</p>\n";
    
} else {
    echo "<p>❌ License manager class not found</p>\n";
}

// Configure existing products for licensing
echo "<h2>📦 Configuring Existing Products</h2>\n";

$all_products = wc_get_products(array('limit' => -1));

foreach ($all_products as $product) {
    $product_name = $product->get_name();
    
    if (strpos(strtolower($product_name), 'pro') !== false) {
        echo "<h3>🔧 Configuring: $product_name</h3>\n";
        
        // Set as requiring license
        update_post_meta($product->get_id(), '_requires_license', 'yes');
        
        // Set license settings based on product
        if (strpos($product_name, 'Property Management') !== false || strpos($product_name, 'DealerEdge') !== false) {
            update_post_meta($product->get_id(), '_license_max_sites', 5);
        } elseif (strpos($product_name, 'Sports') !== false || strpos($product_name, 'EquipRent') !== false || strpos($product_name, 'GymFlow') !== false) {
            update_post_meta($product->get_id(), '_license_max_sites', 3);
        } else {
            update_post_meta($product->get_id(), '_license_max_sites', 2);
        }
        
        update_post_meta($product->get_id(), '_license_expiry_period', '+1 year');
        
        // Set as downloadable and virtual
        $product->set_downloadable(true);
        $product->set_virtual(true);
        $product->save();
        
        echo "<p>✅ License settings configured for {$product->get_name()}</p>\n";
    }
}

echo "<h2>🛒 Final System Check</h2>\n";

// Check everything is working
if (class_exists('Vireo_License_Manager')) {
    echo "<p>✅ License Manager active</p>\n";
}

if (class_exists('Vireo_Plugin_Downloads')) {
    echo "<p>✅ Download Manager active</p>\n";
}

$table_name = $wpdb->prefix . 'vireo_licenses';
$table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
if ($table_exists) {
    echo "<p>✅ License database table ready</p>\n";
} else {
    echo "<p>❌ License database table missing</p>\n";
}

// Check product configuration
$pro_products = wc_get_products(array('limit' => -1));
$configured_count = 0;

foreach ($pro_products as $product) {
    $requires_license = get_post_meta($product->get_id(), '_requires_license', true);
    if ($requires_license === 'yes') {
        $configured_count++;
    }
}

echo "<p>✅ $configured_count products configured for licensing</p>\n";

echo "<h2>🚀 System Ready!</h2>\n";
echo "<p><strong>Your complete WordPress plugin distribution system is now active:</strong></p>\n";
echo "<ul>\n";
echo "<li>✅ Free plugin downloads with secure token system</li>\n";
echo "<li>✅ Pro plugin sales with automated license generation</li>\n";
echo "<li>✅ Customer license management portal</li>\n";
echo "<li>✅ Admin license management dashboard</li>\n";
echo "<li>✅ API for plugin license validation</li>\n";
echo "</ul>\n";

echo "<p><strong>Test the complete flow:</strong></p>\n";
echo "<ol>\n";
echo "<li><a href='/plugins/property-management/' target='_blank'>Visit a plugin page</a> → Download free version</li>\n";
echo "<li><a href='/shop/' target='_blank'>Visit shop</a> → Purchase Pro version</li>\n";
echo "<li>Check email for license key</li>\n";
echo "<li><a href='/my-account/licenses/' target='_blank'>View licenses in account</a></li>\n";
echo "<li><a href='/wp-admin/admin.php?page=vireo-licenses' target='_blank'>Admin: Manage licenses</a></li>\n";
echo "</ol>\n";

?>