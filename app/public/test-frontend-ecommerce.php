<?php
/**
 * Quick frontend e-commerce test
 */

// Load WordPress
require_once __DIR__ . '/wp-load.php';

echo "🛒 Testing Frontend E-commerce\n";
echo "==============================\n\n";

// Test shop page content
echo "🏪 Testing shop page content:\n";

// Get the shop page and test its rendering
$shop_page_id = wc_get_page_id('shop');
$shop_page = get_post($shop_page_id);

if ($shop_page) {
    echo "   ✅ Shop page found: {$shop_page->post_title}\n";
    echo "   📄 Content: " . (empty($shop_page->post_content) ? 'Using default template' : 'Has custom content') . "\n";
    
    // Test if shortcode is working
    if (strpos($shop_page->post_content, '[woocommerce_products') !== false) {
        echo "   ✅ Products shortcode found in content\n";
    }
    
    // Simulate rendering the shop page
    global $wp_query, $post;
    $original_query = $wp_query;
    $original_post = $post;
    
    // Set up shop query
    $wp_query = new WP_Query([
        'post_type' => 'product',
        'posts_per_page' => 12,
        'post_status' => 'publish'
    ]);
    
    if ($wp_query->have_posts()) {
        echo "   ✅ Products query returned " . $wp_query->found_posts . " products\n";
        
        echo "   📦 First 3 products:\n";
        $count = 0;
        while ($wp_query->have_posts() && $count < 3) {
            $wp_query->the_post();
            $product = wc_get_product();
            echo "     - " . get_the_title() . " (\$" . $product->get_price() . ")\n";
            $count++;
        }
    } else {
        echo "   ❌ No products found in query\n";
    }
    
    // Restore original query
    $wp_query = $original_query;
    $post = $original_post;
    wp_reset_postdata();
}

// Test cart functionality
echo "\n🛒 Testing cart functionality:\n";

// Clear cart first
WC()->cart->empty_cart();
echo "   🧹 Cart cleared\n";

// Get a test product
$test_products = get_posts([
    'post_type' => 'product',
    'numberposts' => 1,
    'post_status' => 'publish'
]);

if (!empty($test_products)) {
    $test_product = wc_get_product($test_products[0]->ID);
    echo "   🧪 Testing with: {$test_product->get_name()}\n";
    
    // Add to cart
    $cart_item_key = WC()->cart->add_to_cart($test_product->get_id(), 1);
    
    if ($cart_item_key) {
        echo "   ✅ Product added to cart successfully\n";
        echo "   📊 Cart contents: " . WC()->cart->get_cart_contents_count() . " items\n";
        echo "   💰 Cart total: \$" . WC()->cart->get_total('edit') . "\n";
        
        // Test cart URL
        $cart_url = wc_get_cart_url();
        echo "   🔗 Cart URL: {$cart_url}\n";
        
        // Test checkout URL
        $checkout_url = wc_get_checkout_url();
        echo "   🔗 Checkout URL: {$checkout_url}\n";
        
    } else {
        echo "   ❌ Failed to add product to cart\n";
    }
    
    // Clear cart
    WC()->cart->empty_cart();
} else {
    echo "   ❌ No test products found\n";
}

// Test key URLs
echo "\n🔗 Testing key e-commerce URLs:\n";

$urls_to_test = [
    'Shop' => home_url('/shop/'),
    'Cart' => wc_get_cart_url(),
    'Checkout' => wc_get_checkout_url(),
    'My Account' => wc_get_page_permalink('myaccount')
];

foreach ($urls_to_test as $name => $url) {
    echo "   {$name}: {$url}\n";
    
    // Test if URL returns 200
    $response = wp_remote_get($url, ['timeout' => 5]);
    if (!is_wp_error($response)) {
        $status_code = wp_remote_retrieve_response_code($response);
        echo "     Status: {$status_code} " . ($status_code == 200 ? '✅' : '❌') . "\n";
    } else {
        echo "     Status: Error ❌\n";
    }
}

// Test individual product pages
echo "\n📦 Testing individual product pages:\n";

$sample_products = get_posts([
    'post_type' => 'product',
    'numberposts' => 2,
    'post_status' => 'publish'
]);

foreach ($sample_products as $product_post) {
    $product = wc_get_product($product_post->ID);
    $product_url = get_permalink($product_post->ID);
    
    echo "   {$product->get_name()}:\n";
    echo "     URL: {$product_url}\n";
    echo "     Price: \$" . $product->get_price() . "\n";
    echo "     Type: " . $product->get_type() . "\n";
    echo "     Downloadable: " . ($product->is_downloadable() ? 'Yes' : 'No') . "\n";
    
    // Test if product page loads
    $response = wp_remote_get($product_url, ['timeout' => 5]);
    if (!is_wp_error($response)) {
        $status_code = wp_remote_retrieve_response_code($response);
        echo "     Page loads: " . ($status_code == 200 ? 'Yes ✅' : 'No ❌') . "\n";
        
        // Check if response contains add to cart elements
        $body = wp_remote_retrieve_body($response);
        if (strpos($body, 'add-to-cart') !== false || strpos($body, 'wc-add-to-cart') !== false) {
            echo "     Add to cart: Found ✅\n";
        } else {
            echo "     Add to cart: Not found ❌\n";
        }
    }
    echo "\n";
}

echo "🎯 QUICK FRONTEND TEST:\n";
echo "Try these URLs in your browser:\n";
echo "1. Shop: http://vireo.local/shop/\n";
echo "2. Single product: " . get_permalink($sample_products[0]->ID ?? '') . "\n";
echo "3. Cart: http://vireo.local/cart/\n";

echo "\n✨ Frontend e-commerce test complete!\n";
?>