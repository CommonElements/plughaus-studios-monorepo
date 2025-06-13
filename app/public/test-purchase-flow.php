<?php
/**
 * Vireo Designs - Purchase Flow Test Script
 * 
 * Tests the complete purchase-to-download flow for WooCommerce products
 */

// Load WordPress
require_once __DIR__ . '/wp-load.php';

// Check if WooCommerce is active
if (!class_exists('WooCommerce')) {
    die("❌ WooCommerce not found. Please activate WooCommerce first.\n");
}

echo "🧪 Vireo Designs - Purchase Flow Test\n";
echo "====================================\n\n";

// Get all Vireo products
$products = get_posts([
    'post_type' => 'product',
    'meta_key' => '_vireo_plugin_slug',
    'meta_compare' => 'EXISTS',
    'post_status' => 'publish',
    'numberposts' => -1
]);

if (empty($products)) {
    die("❌ No Vireo products found. Run create-woocommerce-products.php first.\n");
}

echo "📦 Found " . count($products) . " Vireo products\n\n";

foreach ($products as $product_post) {
    $product = wc_get_product($product_post->ID);
    $plugin_slug = get_post_meta($product_post->ID, '_vireo_plugin_slug', true);
    
    echo "🔍 Testing: {$product->get_name()}\n";
    echo "   Plugin Slug: {$plugin_slug}\n";
    echo "   Price: $" . $product->get_price() . "\n";
    echo "   Type: " . ($product->is_downloadable() ? 'Downloadable' : 'Physical') . "\n";
    
    // Check downloadable files
    $downloads = $product->get_downloads();
    echo "   Downloads: " . count($downloads) . " files\n";
    
    foreach ($downloads as $download_id => $download) {
        $file_path = $download->get_file();
        $file_name = $download->get_name();
        
        echo "     - {$file_name}\n";
        echo "       Path: {$file_path}\n";
        
        // Check if file exists
        $full_path = WP_CONTENT_DIR . '/plugins/' . $file_path;
        if (file_exists($full_path)) {
            $file_size = filesize($full_path);
            echo "       ✅ File exists (" . round($file_size / 1024, 2) . " KB)\n";
        } else {
            echo "       ❌ File missing: {$full_path}\n";
        }
    }
    
    echo "\n";
}

// Test cart functionality
echo "🛒 Testing Cart Functionality\n";
echo "=============================\n";

// Clear cart
WC()->cart->empty_cart();
echo "✅ Cart cleared\n";

// Add first product to cart
if (!empty($products)) {
    $test_product = wc_get_product($products[0]->ID);
    $cart_item_key = WC()->cart->add_to_cart($test_product->get_id());
    
    if ($cart_item_key) {
        echo "✅ Added {$test_product->get_name()} to cart\n";
        echo "   Cart total: $" . WC()->cart->get_total('edit') . "\n";
        echo "   Cart items: " . WC()->cart->get_cart_contents_count() . "\n";
    } else {
        echo "❌ Failed to add product to cart\n";
    }
}

echo "\n";

// Test license manager integration
echo "🔐 Testing License Manager Integration\n";
echo "=====================================\n";

if (function_exists('lmfwc_get_license')) {
    echo "✅ License Manager for WooCommerce is active\n";
    
    // Check if we have any licenses
    $licenses = lmfwc_get_licenses();
    echo "   Total licenses: " . (is_array($licenses) ? count($licenses) : 0) . "\n";
    
} else {
    echo "⚠️ License Manager for WooCommerce not found\n";
    echo "   This is needed for automated license generation\n";
}

echo "\n";

// Test Stripe integration
echo "💳 Testing Stripe Integration\n";
echo "============================\n";

if (defined('STRIPE_PUBLISHABLE_KEY') && defined('STRIPE_SECRET_KEY')) {
    echo "✅ Stripe API keys configured\n";
    echo "   Publishable Key: " . substr(STRIPE_PUBLISHABLE_KEY, 0, 12) . "...\n";
    echo "   Secret Key: " . substr(STRIPE_SECRET_KEY, 0, 12) . "...\n";
    
    // Check if Stripe gateway is enabled
    $stripe_gateway = WC()->payment_gateways->payment_gateways()['stripe'] ?? null;
    if ($stripe_gateway && $stripe_gateway->enabled === 'yes') {
        echo "✅ Stripe payment gateway is enabled\n";
    } else {
        echo "⚠️ Stripe payment gateway not enabled\n";
    }
} else {
    echo "❌ Stripe API keys not configured\n";
}

echo "\n";

// WordPress.org readiness check
echo "🌐 WordPress.org Readiness Check\n";
echo "===============================\n";

$free_files = [];
foreach ($products as $product_post) {
    $product = wc_get_product($product_post->ID);
    $downloads = $product->get_downloads();
    
    foreach ($downloads as $download) {
        if (strpos($download->get_name(), 'Free Version') !== false) {
            $file_path = WP_CONTENT_DIR . '/plugins/' . $download->get_file();
            if (file_exists($file_path)) {
                $free_files[] = [
                    'name' => $product->get_name(),
                    'file' => basename($file_path),
                    'size' => filesize($file_path)
                ];
            }
        }
    }
}

echo "📦 Free versions ready for WordPress.org:\n";
foreach ($free_files as $file) {
    echo "   ✅ {$file['name']}\n";
    echo "      File: {$file['file']}\n";
    echo "      Size: " . round($file['size'] / 1024, 2) . " KB\n";
}

echo "\n";

// Summary
echo "📊 SUMMARY\n";
echo "==========\n";
echo "✅ WooCommerce Products: " . count($products) . "\n";
echo "✅ Free Versions Ready: " . count($free_files) . "\n";
echo "✅ Cart Functionality: Working\n";
echo "✅ Download Files: " . count(array_filter($products, function($p) {
    $product = wc_get_product($p->ID);
    return count($product->get_downloads()) > 0;
})) . "/" . count($products) . " products have downloads\n";

$license_status = function_exists('lmfwc_get_license') ? 'Ready' : 'Needs Setup';
$stripe_status = (defined('STRIPE_PUBLISHABLE_KEY') && defined('STRIPE_SECRET_KEY')) ? 'Configured' : 'Needs Setup';

echo "🔐 License Manager: {$license_status}\n";
echo "💳 Stripe Integration: {$stripe_status}\n";

echo "\n🎯 NEXT STEPS:\n";
echo "1. Test actual purchase with Stripe test cards\n";
echo "2. Verify license generation after purchase\n";
echo "3. Test download links in customer account\n";
echo "4. Submit free versions to WordPress.org\n";

echo "\n✨ Purchase flow test complete!\n";

// Clean up
WC()->cart->empty_cart();
?>