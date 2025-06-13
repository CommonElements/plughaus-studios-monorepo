<?php
/**
 * Cart Sync Fix - Force frontend/backend cart synchronization
 */

// Load WordPress
require_once __DIR__ . '/wp-load.php';

echo "🔄 Cart Sync Fix\n";
echo "================\n\n";

// Step 1: Replace cart page with direct PHP template
echo "1. Creating Direct Cart Template:\n";

$cart_page_id = wc_get_page_id('cart');

// Create a simple cart page that bypasses shortcode issues
$direct_cart_content = '<!-- wp:html -->
<div id="direct-cart-container">
    <script>
    // Show loading while PHP loads
    document.getElementById("direct-cart-container").innerHTML = "<h2>Loading cart...</h2>";
    </script>
</div>

<?php
// Direct PHP cart implementation
if (!defined("ABSPATH")) exit;

// Ensure WooCommerce is loaded
if (!class_exists("WooCommerce")) {
    echo "<p>WooCommerce not available</p>";
    return;
}

// Initialize WooCommerce cart
WC()->frontend_includes();
if (!WC()->session) {
    WC()->session = new WC_Session_Handler();
    WC()->session->init();
}
if (!WC()->cart) {
    WC()->cart = new WC_Cart();
}
if (!WC()->customer) {
    WC()->customer = new WC_Customer();
}

echo "<style>
#direct-cart-container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
}
.cart-header {
    text-align: center;
    margin-bottom: 40px;
    padding: 40px 0;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
}
.cart-title {
    font-size: 32px;
    color: #333;
    margin-bottom: 10px;
}
.cart-empty {
    text-align: center;
    padding: 60px 20px;
    background: #f8f9fa;
    border-radius: 12px;
    margin: 40px 0;
}
.cart-items {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}
.cart-item {
    display: flex;
    align-items: center;
    padding: 20px 0;
    border-bottom: 1px solid #eee;
}
.cart-item:last-child {
    border-bottom: none;
}
.item-image {
    width: 80px;
    height: 80px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-right: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #2c5aa0;
}
.item-details {
    flex: 1;
}
.item-name {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}
.item-features {
    font-size: 14px;
    color: #6c757d;
}
.item-price {
    font-size: 20px;
    font-weight: 700;
    color: #2c5aa0;
    margin-right: 20px;
}
.item-remove {
    background: #dc3545;
    color: white;
    padding: 8px 12px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 14px;
}
.cart-total-section {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    text-align: center;
}
.cart-total {
    font-size: 28px;
    font-weight: 700;
    color: #2c5aa0;
    margin-bottom: 20px;
}
.checkout-btn {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    padding: 15px 40px;
    font-size: 18px;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s ease;
}
.checkout-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.3);
}
.continue-shopping {
    margin-top: 20px;
}
.continue-shopping a {
    color: #2c5aa0;
    text-decoration: none;
    font-weight: 600;
}
</style>";

echo "<div class=\"cart-header\">";
echo "<h1 class=\"cart-title\"><i class=\"fas fa-shopping-cart\"></i> Your Cart</h1>";
echo "<p>Review your selected plugins before checkout</p>";
echo "</div>";

$cart_items = WC()->cart->get_cart();
$cart_count = WC()->cart->get_cart_contents_count();
$cart_total = WC()->cart->get_total("edit");

if (empty($cart_items)) {
    echo "<div class=\"cart-empty\">";
    echo "<div style=\"font-size: 60px; color: #dee2e6; margin-bottom: 20px;\"><i class=\"fas fa-shopping-cart\"></i></div>";
    echo "<h3>Your cart is empty</h3>";
    echo "<p>Looks like you haven't added any plugins to your cart yet.</p>";
    echo "<a href=\"" . wc_get_page_permalink("shop") . "\" class=\"checkout-btn\">Browse Plugins</a>";
    echo "</div>";
} else {
    echo "<div class=\"cart-items\">";
    echo "<h3>Cart Items ({$cart_count})</h3>";
    
    foreach ($cart_items as $cart_item_key => $cart_item) {
        $product = $cart_item["data"];
        $product_id = $cart_item["product_id"];
        $quantity = $cart_item["quantity"];
        
        echo "<div class=\"cart-item\">";
        echo "<div class=\"item-image\"><i class=\"fas fa-puzzle-piece\"></i></div>";
        echo "<div class=\"item-details\">";
        echo "<div class=\"item-name\">" . $product->get_name() . "</div>";
        echo "<div class=\"item-features\">";
        echo "<i class=\"fas fa-download\"></i> Instant Download ";
        echo "<i class=\"fas fa-key\"></i> License Key ";
        echo "<i class=\"fas fa-support\"></i> 1 Year Support";
        echo "</div>";
        echo "</div>";
        echo "<div class=\"item-price\">$" . $product->get_price() . "</div>";
        echo "<a href=\"" . wc_get_cart_remove_url($cart_item_key) . "\" class=\"item-remove\"><i class=\"fas fa-times\"></i></a>";
        echo "</div>";
    }
    
    echo "</div>";
    
    echo "<div class=\"cart-total-section\">";
    echo "<div class=\"cart-total\">Total: $" . $cart_total . "</div>";
    echo "<a href=\"" . wc_get_checkout_url() . "\" class=\"checkout-btn\">";
    echo "<i class=\"fas fa-credit-card\"></i> Proceed to Checkout";
    echo "</a>";
    echo "<div class=\"continue-shopping\">";
    echo "<a href=\"" . wc_get_page_permalink("shop") . "\"><i class=\"fas fa-arrow-left\"></i> Continue Shopping</a>";
    echo "</div>";
    echo "</div>";
}
?>

<script>
// Update header cart count
document.addEventListener("DOMContentLoaded", function() {
    const cartCount = <?php echo $cart_count; ?>;
    const cartCountElements = document.querySelectorAll(".cart-count");
    cartCountElements.forEach(function(element) {
        element.textContent = cartCount;
        if (cartCount > 0) {
            element.style.display = "inline-block";
        } else {
            element.style.display = "none";
        }
    });
});
</script>
<!-- /wp:html -->';

wp_update_post(array(
    'ID' => $cart_page_id,
    'post_content' => $direct_cart_content
));

echo "   ✅ Direct cart template created\n";

// Step 2: Create AJAX cart sync endpoint
echo "\n2. Creating AJAX Cart Sync:\n";

$ajax_sync_code = '
// AJAX cart sync functions
function vireo_get_cart_count() {
    if (!class_exists("WooCommerce")) {
        wp_send_json_error("WooCommerce not available");
    }
    
    // Initialize cart if needed
    if (!WC()->cart) {
        WC()->cart = new WC_Cart();
    }
    
    $count = WC()->cart->get_cart_contents_count();
    $total = WC()->cart->get_total("edit");
    
    wp_send_json_success(array(
        "count" => $count,
        "total" => $total,
        "cart_url" => wc_get_cart_url()
    ));
}
add_action("wp_ajax_get_cart_count", "vireo_get_cart_count");
add_action("wp_ajax_nopriv_get_cart_count", "vireo_get_cart_count");

function vireo_sync_cart_header() {
    if (!class_exists("WooCommerce")) {
        return;
    }
    
    $count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
    
    echo "<script>
    document.addEventListener(\"DOMContentLoaded\", function() {
        const cartElements = document.querySelectorAll(\".cart-count\");
        cartElements.forEach(function(el) {
            el.textContent = \"{$count}\";
            if ({$count} > 0) {
                el.style.display = \"inline-block\";
            } else {
                el.style.display = \"none\";
            }
        });
    });
    </script>";
}
add_action("wp_footer", "vireo_sync_cart_header");';

// Add to functions.php
$functions_file = get_template_directory() . '/functions.php';
$functions_content = file_get_contents($functions_file);

if (strpos($functions_content, 'vireo_get_cart_count') === false) {
    file_put_contents($functions_file, $functions_content . "\n" . $ajax_sync_code);
    echo "   ✅ AJAX cart sync added\n";
} else {
    echo "   ⚠️ AJAX cart sync already exists\n";
}

// Step 3: Test the current cart state
echo "\n3. Current Cart State:\n";

if (WC()->cart) {
    $count = WC()->cart->get_cart_contents_count();
    $total = WC()->cart->get_total('edit');
    echo "   Cart items: {$count}\n";
    echo "   Cart total: \${$total}\n";
    
    if ($count > 0) {
        echo "   Cart contents:\n";
        foreach (WC()->cart->get_cart() as $cart_item) {
            echo "     - " . $cart_item['data']->get_name() . "\n";
        }
    }
}

echo "\n✅ Cart Sync Fix Complete!\n";
echo "========================\n\n";

echo "The cart page now uses direct PHP rendering instead of shortcodes.\n";
echo "This should resolve the sync issues between backend and frontend.\n\n";

echo "🎯 TEST STEPS:\n";
echo "1. Visit: http://vireo.local/cart/\n";
echo "2. Should show current cart contents\n";
echo "3. Add items from shop page\n";
echo "4. Cart should update immediately\n\n";

echo "If cart is still empty, try:\n";
echo "- Clear browser cache/cookies\n";
echo "- Add items from shop page again\n";
echo "- Check browser console for errors\n";
?>