<?php
/**
 * Vireo Designs - Shop Frontend Fix
 * 
 * Diagnoses and fixes WooCommerce shop frontend issues
 */

// Load WordPress
require_once __DIR__ . '/wp-load.php';

echo "🔧 Vireo Designs - Shop Frontend Fix\n";
echo "===================================\n\n";

// Check WooCommerce status
if (!class_exists('WooCommerce')) {
    die("❌ WooCommerce not found. Please activate WooCommerce first.\n");
}

echo "✅ WooCommerce is active\n";

// Check if shop page exists and is set up
$shop_page_id = wc_get_page_id('shop');
$shop_page = get_post($shop_page_id);

echo "🏪 Shop page status:\n";
echo "   Page ID: {$shop_page_id}\n";
echo "   Page exists: " . ($shop_page ? 'Yes' : 'No') . "\n";

if ($shop_page) {
    echo "   Page title: {$shop_page->post_title}\n";
    echo "   Page status: {$shop_page->post_status}\n";
    echo "   Page slug: {$shop_page->post_name}\n";
}

// Check products
$products = get_posts([
    'post_type' => 'product',
    'post_status' => 'publish',
    'numberposts' => -1
]);

echo "\n📦 Product status:\n";
echo "   Total products: " . count($products) . "\n";

foreach ($products as $product_post) {
    $product = wc_get_product($product_post->ID);
    echo "   - {$product->get_name()}: ";
    echo "Status={$product_post->post_status}, ";
    echo "Visibility=" . $product->get_catalog_visibility() . ", ";
    echo "Stock=" . ($product->is_in_stock() ? 'In Stock' : 'Out of Stock');
    echo "\n";
}

// Check theme support
echo "\n🎨 Theme support:\n";
echo "   Current theme: " . get_template() . "\n";
echo "   WooCommerce support: " . (current_theme_supports('woocommerce') ? 'Yes' : 'No') . "\n";

// Add WooCommerce support if missing
if (!current_theme_supports('woocommerce')) {
    echo "   🔧 Adding WooCommerce theme support...\n";
    add_theme_support('woocommerce');
    echo "   ✅ WooCommerce theme support added\n";
}

// Check if shop page has content or uses default template
echo "\n📄 Shop page content:\n";
if ($shop_page && !empty($shop_page->post_content)) {
    echo "   Custom content: Yes (" . strlen($shop_page->post_content) . " characters)\n";
} else {
    echo "   Custom content: No (using default WooCommerce template)\n";
}

// Check WooCommerce pages setup
$pages_to_check = [
    'shop' => 'Shop',
    'cart' => 'Cart',
    'checkout' => 'Checkout',
    'myaccount' => 'My Account'
];

echo "\n📋 WooCommerce pages setup:\n";
foreach ($pages_to_check as $page_type => $page_name) {
    $page_id = wc_get_page_id($page_type);
    $page = get_post($page_id);
    
    if ($page && $page->post_status === 'publish') {
        echo "   ✅ {$page_name}: Configured (ID: {$page_id})\n";
    } else {
        echo "   ❌ {$page_name}: Missing or not published\n";
        
        // Try to create missing page
        $page_content = '';
        switch ($page_type) {
            case 'shop':
                $page_content = '[woocommerce_products limit="12" columns="3"]';
                break;
            case 'cart':
                $page_content = '[woocommerce_cart]';
                break;
            case 'checkout':
                $page_content = '[woocommerce_checkout]';
                break;
            case 'myaccount':
                $page_content = '[woocommerce_my_account]';
                break;
        }
        
        $new_page_id = wp_insert_post([
            'post_title' => $page_name,
            'post_content' => $page_content,
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_name' => $page_type
        ]);
        
        if ($new_page_id) {
            update_option('woocommerce_' . $page_type . '_page_id', $new_page_id);
            echo "     🔧 Created {$page_name} page (ID: {$new_page_id})\n";
        }
    }
}

// Check if products are visible on frontend
echo "\n🔍 Frontend visibility test:\n";

// Add shortcode to shop page if it's empty
if ($shop_page && empty($shop_page->post_content)) {
    echo "   📝 Adding products shortcode to shop page...\n";
    wp_update_post([
        'ID' => $shop_page->ID,
        'post_content' => '[woocommerce_products limit="12" columns="3" orderby="date" order="DESC"]'
    ]);
    echo "   ✅ Shop page updated with products shortcode\n";
}

// Test actual frontend by simulating a request
echo "\n🌐 Testing frontend output:\n";

ob_start();
query_posts([
    'post_type' => 'product',
    'posts_per_page' => 3,
    'post_status' => 'publish'
]);

$frontend_products = [];
while (have_posts()) {
    the_post();
    global $post, $product;
    $product = wc_get_product($post->ID);
    $frontend_products[] = [
        'title' => get_the_title(),
        'price' => $product->get_price(),
        'url' => get_permalink()
    ];
}
wp_reset_query();
ob_end_clean();

if (!empty($frontend_products)) {
    echo "   ✅ Products found on frontend:\n";
    foreach ($frontend_products as $fp) {
        echo "     - {$fp['title']} (\${$fp['price']}) - {$fp['url']}\n";
    }
} else {
    echo "   ❌ No products found on frontend\n";
}

// Check cart functionality
echo "\n🛒 Cart functionality:\n";
if (function_exists('WC')) {
    echo "   ✅ WooCommerce cart functions available\n";
    
    // Test adding item to cart
    WC()->cart->empty_cart();
    if (!empty($products)) {
        $test_product = wc_get_product($products[0]->ID);
        $cart_item_key = WC()->cart->add_to_cart($test_product->get_id());
        
        if ($cart_item_key) {
            echo "   ✅ Cart add functionality working\n";
            echo "   📦 Test item added: {$test_product->get_name()}\n";
            WC()->cart->empty_cart();
        } else {
            echo "   ❌ Cart add functionality failed\n";
        }
    }
} else {
    echo "   ❌ WooCommerce cart functions not available\n";
}

// Flush rewrite rules to ensure permalinks work
echo "\n🔄 Flushing rewrite rules...\n";
flush_rewrite_rules();
echo "   ✅ Rewrite rules flushed\n";

echo "\n🎯 RECOMMENDATIONS:\n";
echo "1. Visit http://vireo.local/shop/ to test the shop page\n";
echo "2. Check that products are visible and clickable\n";
echo "3. Test adding items to cart\n";
echo "4. Verify checkout process works\n";

if (count($products) > 0) {
    echo "\n📋 Direct product links to test:\n";
    foreach (array_slice($products, 0, 3) as $product_post) {
        $product_url = get_permalink($product_post->ID);
        echo "   - {$product_post->post_title}: {$product_url}\n";
    }
}

echo "\n✨ Shop frontend fix complete!\n";
?>