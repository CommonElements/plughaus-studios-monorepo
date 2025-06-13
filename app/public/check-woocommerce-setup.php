<?php
/**
 * Check WooCommerce setup and products
 */

require_once('./wp-load.php');

echo "<h1>🛒 WooCommerce Setup Analysis</h1>\n";

// Check if WooCommerce is active
if (class_exists('WooCommerce')) {
    echo "<p>✅ WooCommerce is active</p>\n";
    
    // Get WooCommerce version
    $wc_version = WC()->version;
    echo "<p>📦 WooCommerce Version: $wc_version</p>\n";
    
    // Check products
    $products = wc_get_products(array(
        'limit' => -1,
        'status' => 'publish'
    ));
    
    echo "<h2>🛍️ Current Products (" . count($products) . ")</h2>\n";
    
    if (!empty($products)) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>\n";
        echo "<tr><th>ID</th><th>Name</th><th>Type</th><th>Price</th><th>Status</th><th>Stock</th></tr>\n";
        
        foreach ($products as $product) {
            echo "<tr>\n";
            echo "<td>" . $product->get_id() . "</td>\n";
            echo "<td>" . $product->get_name() . "</td>\n";
            echo "<td>" . $product->get_type() . "</td>\n";
            echo "<td>" . $product->get_price_html() . "</td>\n";
            echo "<td>" . $product->get_status() . "</td>\n";
            echo "<td>" . ($product->is_in_stock() ? 'In Stock' : 'Out of Stock') . "</td>\n";
            echo "</tr>\n";
        }
        echo "</table>\n";
    } else {
        echo "<p>❌ No products found</p>\n";
    }
    
    // Check categories
    $categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => false
    ));
    
    echo "<h2>📂 Product Categories (" . count($categories) . ")</h2>\n";
    if (!empty($categories)) {
        echo "<ul>\n";
        foreach ($categories as $category) {
            echo "<li>{$category->name} ({$category->count} products)</li>\n";
        }
        echo "</ul>\n";
    }
    
    // Check payment gateways
    $payment_gateways = WC()->payment_gateways->get_available_payment_gateways();
    echo "<h2>💳 Payment Gateways (" . count($payment_gateways) . ")</h2>\n";
    echo "<ul>\n";
    foreach ($payment_gateways as $gateway) {
        $status = $gateway->enabled === 'yes' ? '✅ Enabled' : '❌ Disabled';
        echo "<li>{$gateway->get_title()} - $status</li>\n";
    }
    echo "</ul>\n";
    
    // Check license manager
    if (class_exists('WC_License_Manager')) {
        echo "<p>✅ WooCommerce License Manager is active</p>\n";
    } else {
        echo "<p>❌ WooCommerce License Manager not found</p>\n";
    }
    
    // Check Stripe
    if (class_exists('WC_Stripe')) {
        echo "<p>✅ WooCommerce Stripe is active</p>\n";
    } else {
        echo "<p>❌ WooCommerce Stripe not found</p>\n";
    }
    
    // Shop page
    $shop_page_id = wc_get_page_id('shop');
    if ($shop_page_id > 0) {
        $shop_page = get_post($shop_page_id);
        echo "<p>✅ Shop page configured: <a href='" . get_permalink($shop_page_id) . "'>" . $shop_page->post_title . "</a></p>\n";
    } else {
        echo "<p>❌ Shop page not configured</p>\n";
    }
    
} else {
    echo "<p>❌ WooCommerce is not active</p>\n";
}

// Check for license manager plugins
echo "<h2>🔑 License Management Plugins</h2>\n";
$license_plugins = array(
    'license-manager-for-woocommerce',
    'woocommerce-software-license-manager',
    'easy-digital-downloads',
    'wp-license-manager'
);

foreach ($license_plugins as $plugin_slug) {
    $plugin_file = WP_PLUGIN_DIR . '/' . $plugin_slug . '/' . $plugin_slug . '.php';
    if (file_exists($plugin_file)) {
        echo "<p>✅ Found: $plugin_slug</p>\n";
    } else {
        echo "<p>❌ Not found: $plugin_slug</p>\n";
    }
}

echo "<h2>🔧 Next Steps for WooCommerce Setup</h2>\n";
echo "<ol>\n";
echo "<li>Create Pro version products for each plugin</li>\n";
echo "<li>Configure license delivery system</li>\n";
echo "<li>Set up Stripe payment processing</li>\n";
echo "<li>Configure downloadable product settings</li>\n";
echo "<li>Test purchase and license delivery flow</li>\n";
echo "</ol>\n";

?>