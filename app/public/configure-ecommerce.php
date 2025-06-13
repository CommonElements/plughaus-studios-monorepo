<?php
/**
 * Complete E-commerce Configuration for PlugHaus Studios
 * Configures WooCommerce + Stripe + License Manager integration
 * Run this by visiting: http://plughaus-studios-the-beginning-is-finished.local/configure-ecommerce.php
 */

// Include WordPress
define('WP_USE_THEMES', false);
require_once dirname(__FILE__) . '/wp-load.php';

// Check if user is admin
if (!is_user_logged_in() || !current_user_can('manage_options')) {
    wp_redirect(wp_login_url($_SERVER['REQUEST_URI']));
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>PlugHaus Studios E-commerce Configuration</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 0 auto; padding: 20px; }
        h1, h2 { color: #2563eb; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: #0066cc; font-style: italic; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 4px; color: #856404; margin: 20px 0; }
        .btn { background: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin: 10px 5px 10px 0; }
        .section { background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 8px; }
        .code { background: #f1f3f4; padding: 15px; border-radius: 4px; font-family: monospace; margin: 10px 0; overflow-x: auto; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .card { background: white; border: 1px solid #dee2e6; padding: 20px; border-radius: 8px; }
    </style>
</head>
<body>
    <h1>PlugHaus Studios E-commerce Configuration</h1>
    
    <?php
    // 1. Verify plugin installations
    echo '<div class="section">';
    echo '<h2>1. Plugin Status Verification</h2>';
    
    $plugins_status = array(
        'WooCommerce' => class_exists('WooCommerce'),
        'License Manager' => class_exists('LicenseManagerForWooCommerce\Setup'),
        'Stripe Gateway' => class_exists('WC_Stripe')
    );
    
    foreach ($plugins_status as $plugin => $status) {
        if ($status) {
            echo '<p class="success">✓ ' . $plugin . ' is active</p>';
        } else {
            echo '<p class="error">✗ ' . $plugin . ' not found or inactive</p>';
        }
    }
    echo '</div>';
    
    // 2. Create product categories
    echo '<div class="section">';
    echo '<h2>2. Setting Up Product Categories</h2>';
    
    if (class_exists('WooCommerce')) {
        $categories = array(
            'WordPress Plugins' => array(
                'description' => 'Premium WordPress plugins for business automation',
                'slug' => 'wordpress-plugins'
            ),
            'Property Management' => array(
                'description' => 'Complete property and real estate management solutions',
                'slug' => 'property-management'
            ),
            'Payment Gateways' => array(
                'description' => 'Payment processing and e-commerce solutions',
                'slug' => 'payment-gateways'
            ),
            'Pro Licenses' => array(
                'description' => 'Premium plugin licenses with advanced features',
                'slug' => 'pro-licenses'
            ),
            'Plugin Bundles' => array(
                'description' => 'Multiple plugins at discounted prices',
                'slug' => 'plugin-bundles'
            )
        );
        
        foreach ($categories as $name => $data) {
            $existing = get_term_by('name', $name, 'product_cat');
            if (!$existing) {
                $result = wp_insert_term($name, 'product_cat', array(
                    'description' => $data['description'],
                    'slug' => $data['slug']
                ));
                
                if (!is_wp_error($result)) {
                    echo '<p class="success">✓ Created category: ' . $name . '</p>';
                } else {
                    echo '<p class="error">✗ Error creating: ' . $name . '</p>';
                }
            } else {
                echo '<p class="info">→ Category exists: ' . $name . '</p>';
            }
        }
    }
    echo '</div>';
    
    // 3. Create WooCommerce products for plugins
    echo '<div class="section">';
    echo '<h2>3. Creating WooCommerce Products</h2>';
    
    if (class_exists('WooCommerce')) {
        $products = array(
            array(
                'name' => 'PlugHaus Property Management Pro',
                'slug' => 'property-management-pro',
                'description' => 'Advanced property management solution with analytics, automation, and premium features. Includes unlimited properties, advanced reporting, payment automation, and priority support.',
                'short_description' => 'Professional property management with advanced features and priority support.',
                'price' => 99,
                'sale_price' => 79, // Launch discount
                'category' => 'Property Management',
                'sku' => 'PHPM-PRO-001',
                'license_type' => 'yearly'
            ),
            array(
                'name' => 'Payment Gateway Pro License',
                'slug' => 'payment-gateway-pro',
                'description' => 'Complete payment processing solution with multiple gateways, recurring billing, fraud protection, and advanced analytics. Supports Stripe, PayPal, Square, and more.',
                'short_description' => 'Professional payment processing with multiple gateway support.',
                'price' => 149,
                'sale_price' => 119,
                'category' => 'Payment Gateways',
                'sku' => 'PHPG-PRO-001',
                'license_type' => 'yearly'
            ),
            array(
                'name' => 'Document Automator Pro',
                'slug' => 'document-automator-pro',
                'description' => 'Automate document generation with PDF creation, templates, digital signatures, and cloud storage integration. Perfect for contracts, invoices, and reports.',
                'short_description' => 'Advanced document automation with PDF generation and templates.',
                'price' => 199,
                'sale_price' => 159,
                'category' => 'WordPress Plugins',
                'sku' => 'PHDA-PRO-001',
                'license_type' => 'yearly'
            ),
            array(
                'name' => 'SEO Analytics Suite Pro',
                'slug' => 'seo-analytics-pro',
                'description' => 'Comprehensive SEO toolkit with rank tracking, competitor analysis, content optimization, and automated reporting. Boost your search engine visibility.',
                'short_description' => 'Complete SEO analytics with rank tracking and optimization tools.',
                'price' => 129,
                'sale_price' => 99,
                'category' => 'WordPress Plugins',
                'sku' => 'PHSEO-PRO-001',
                'license_type' => 'yearly'
            ),
            array(
                'name' => 'All Plugins Bundle - Lifetime',
                'slug' => 'all-plugins-bundle',
                'description' => 'Get all current and future PlugHaus Studios plugins with lifetime updates, priority support, and exclusive features. Best value for agencies and power users.',
                'short_description' => 'Lifetime access to all plugins with updates and priority support.',
                'price' => 499,
                'sale_price' => 299, // Limited time offer
                'category' => 'Plugin Bundles',
                'sku' => 'PHBUNDLE-LIFE-001',
                'license_type' => 'lifetime'
            )
        );
        
        foreach ($products as $product_data) {
            // Check if product exists
            $existing = get_page_by_path($product_data['slug'], OBJECT, 'product');
            
            if (!$existing) {
                $product = new WC_Product_Simple();
                
                $product->set_name($product_data['name']);
                $product->set_slug($product_data['slug']);
                $product->set_description($product_data['description']);
                $product->set_short_description($product_data['short_description']);
                $product->set_regular_price($product_data['price']);
                $product->set_sale_price($product_data['sale_price']);
                $product->set_sku($product_data['sku']);
                $product->set_downloadable(true);
                $product->set_virtual(true);
                $product->set_status('publish');
                $product->set_catalog_visibility('visible');
                
                // Save product
                $product_id = $product->save();
                
                if ($product_id) {
                    // Set category
                    $category = get_term_by('name', $product_data['category'], 'product_cat');
                    if ($category) {
                        wp_set_object_terms($product_id, $category->term_id, 'product_cat');
                    }
                    
                    // Add license metadata
                    update_post_meta($product_id, '_license_type', $product_data['license_type']);
                    update_post_meta($product_id, '_license_duration', $product_data['license_type'] === 'yearly' ? '365' : 'lifetime');
                    
                    // Add custom fields for license management
                    update_post_meta($product_id, '_enable_license_management', 'yes');
                    update_post_meta($product_id, '_license_activations_limit', $product_data['license_type'] === 'lifetime' ? '10' : '3');
                    
                    echo '<p class="success">✓ Created product: ' . $product_data['name'] . ' (ID: ' . $product_id . ')</p>';
                } else {
                    echo '<p class="error">✗ Failed to create: ' . $product_data['name'] . '</p>';
                }
            } else {
                echo '<p class="info">→ Product exists: ' . $product_data['name'] . '</p>';
            }
        }
    }
    echo '</div>';
    
    // 4. Configure WooCommerce settings
    echo '<div class="section">';
    echo '<h2>4. WooCommerce Configuration</h2>';
    
    // Update WooCommerce settings
    update_option('woocommerce_store_address', '123 Plugin Street');
    update_option('woocommerce_store_city', 'WordPress City');
    update_option('woocommerce_default_country', 'US:CA');
    update_option('woocommerce_store_postcode', '90210');
    update_option('woocommerce_currency', 'USD');
    update_option('woocommerce_currency_pos', 'left');
    update_option('woocommerce_price_thousand_sep', ',');
    update_option('woocommerce_price_decimal_sep', '.');
    update_option('woocommerce_price_num_decimals', 2);
    
    // Digital product settings
    update_option('woocommerce_downloads_require_login', 'yes');
    update_option('woocommerce_downloads_grant_access_after_payment', 'yes');
    
    echo '<p class="success">✓ Updated WooCommerce base settings</p>';
    echo '<p class="success">✓ Configured digital product settings</p>';
    echo '</div>';
    
    // 5. Create essential pages
    echo '<div class="section">';
    echo '<h2>5. Creating Essential E-commerce Pages</h2>';
    
    $ecommerce_pages = array(
        'shop' => array(
            'title' => 'Plugin Store',
            'content' => '<h2>Professional WordPress Plugins</h2>
<p>Discover our collection of premium WordPress plugins designed for modern businesses. All plugins include free versions with optional Pro upgrades for advanced features.</p>

[products limit="12" columns="3" orderby="popularity"]',
            'template' => 'page-shop.php'
        ),
        'pricing' => array(
            'title' => 'Pricing',
            'content' => '<h2>Choose Your Plan</h2>
<p>Flexible pricing options for individuals, businesses, and agencies. All plans include updates and support.</p>

<div class="pricing-grid">
    <div class="pricing-card">
        <h3>Free</h3>
        <div class="price">$0</div>
        <ul>
            <li>Basic plugin features</li>
            <li>Community support</li>
            <li>WordPress.org updates</li>
        </ul>
        <a href="/plugins/" class="btn">Download Free</a>
    </div>
    
    <div class="pricing-card featured">
        <h3>Pro</h3>
        <div class="price">$99-199/year</div>
        <ul>
            <li>All advanced features</li>
            <li>Priority support</li>
            <li>Automatic updates</li>
            <li>Premium documentation</li>
        </ul>
        <a href="/shop/" class="btn btn-primary">Get Pro</a>
    </div>
    
    <div class="pricing-card">
        <h3>Bundle</h3>
        <div class="price">$299 lifetime</div>
        <ul>
            <li>All current & future plugins</li>
            <li>Lifetime updates</li>
            <li>VIP support</li>
            <li>Agency license</li>
        </ul>
        <a href="/product/all-plugins-bundle/" class="btn">Get Bundle</a>
    </div>
</div>'
        ),
        'my-account' => array(
            'title' => 'My Account',
            'content' => '<p>Access your licenses, downloads, and account information.</p>[woocommerce_my_account]'
        ),
        'checkout' => array(
            'title' => 'Checkout',
            'content' => '[woocommerce_checkout]'
        ),
        'cart' => array(
            'title' => 'Cart',
            'content' => '[woocommerce_cart]'
        )
    );
    
    foreach ($ecommerce_pages as $slug => $page_data) {
        $existing_page = get_page_by_path($slug);
        
        if (!$existing_page) {
            $page_id = wp_insert_post(array(
                'post_title' => $page_data['title'],
                'post_content' => $page_data['content'],
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_name' => $slug
            ));
            
            if ($page_id && !is_wp_error($page_id)) {
                echo '<p class="success">✓ Created page: ' . $page_data['title'] . '</p>';
                
                if (isset($page_data['template'])) {
                    update_post_meta($page_id, '_wp_page_template', $page_data['template']);
                }
                
                // Set WooCommerce page options
                if ($slug === 'shop') {
                    update_option('woocommerce_shop_page_id', $page_id);
                } elseif ($slug === 'my-account') {
                    update_option('woocommerce_myaccount_page_id', $page_id);
                } elseif ($slug === 'checkout') {
                    update_option('woocommerce_checkout_page_id', $page_id);
                } elseif ($slug === 'cart') {
                    update_option('woocommerce_cart_page_id', $page_id);
                }
            }
        } else {
            echo '<p class="info">→ Page exists: ' . $page_data['title'] . '</p>';
        }
    }
    echo '</div>';
    
    // 6. Connect plugins to products
    echo '<div class="section">';
    echo '<h2>6. Linking Plugins to Products</h2>';
    
    // Get all plugins and products
    $plugins = get_posts(array(
        'post_type' => 'phstudios_plugin',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    ));
    
    $products = get_posts(array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    ));
    
    // Create connections based on name matching
    $connections = array(
        'PlugHaus Property Management' => 'PlugHaus Property Management Pro',
        'Payment Gateway Pro' => 'Payment Gateway Pro License',
        'Document Automator' => 'Document Automator Pro',
        'SEO Analytics Suite' => 'SEO Analytics Suite Pro'
    );
    
    foreach ($plugins as $plugin) {
        if (isset($connections[$plugin->post_title])) {
            $product_name = $connections[$plugin->post_title];
            $product = get_page_by_title($product_name, OBJECT, 'product');
            
            if ($product) {
                update_post_meta($plugin->ID, '_linked_product', $product->ID);
                update_post_meta($product->ID, '_linked_plugin', $plugin->ID);
                echo '<p class="success">✓ Linked: ' . $plugin->post_title . ' → ' . $product_name . '</p>';
            }
        }
    }
    echo '</div>';
    
    // 7. Add enhanced functions to theme
    echo '<div class="section">';
    echo '<h2>7. Adding E-commerce Functions to Theme</h2>';
    
    $functions_addition = "
// WooCommerce integration functions
function plughaus_add_woocommerce_support() {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'plughaus_add_woocommerce_support');

// Add Buy Pro button to plugin cards
function plughaus_get_plugin_pro_button(\$plugin_id) {
    \$product_id = get_post_meta(\$plugin_id, '_linked_product', true);
    if (\$product_id) {
        \$product = wc_get_product(\$product_id);
        if (\$product) {
            \$price = \$product->get_price_html();
            \$url = \$product->get_permalink();
            return '<a href=\"' . \$url . '\" class=\"btn btn-pro\">Get Pro ' . \$price . '</a>';
        }
    }
    return '';
}

// Display license info in My Account
function plughaus_display_customer_licenses() {
    if (function_exists('lmfwc_get_customer_licenses')) {
        \$customer_id = get_current_user_id();
        \$licenses = lmfwc_get_customer_licenses(\$customer_id);
        
        if (\$licenses) {
            echo '<h3>Your Plugin Licenses</h3>';
            echo '<table class=\"shop_table\">';
            echo '<thead><tr><th>Plugin</th><th>License Key</th><th>Status</th><th>Expires</th></tr></thead>';
            echo '<tbody>';
            
            foreach (\$licenses as \$license) {
                echo '<tr>';
                echo '<td>' . \$license->product_name . '</td>';
                echo '<td><code>' . \$license->license_key . '</code></td>';
                echo '<td>' . \$license->status . '</td>';
                echo '<td>' . (\$license->expires_at ? \$license->expires_at : 'Never') . '</td>';
                echo '</tr>';
            }
            
            echo '</tbody></table>';
        }
    }
}
add_action('woocommerce_account_dashboard', 'plughaus_display_customer_licenses');

// Custom product fields for license management
function plughaus_add_license_fields() {
    global \$post;
    if (\$post->post_type !== 'product') return;
    
    echo '<div class=\"options_group\">';
    
    woocommerce_wp_checkbox(array(
        'id' => '_enable_license_management',
        'label' => 'Enable License Management',
        'description' => 'Generate license keys for this product'
    ));
    
    woocommerce_wp_text_input(array(
        'id' => '_license_activations_limit',
        'label' => 'Activation Limit',
        'description' => 'Maximum number of sites for license',
        'type' => 'number',
        'custom_attributes' => array('min' => '1', 'max' => '100')
    ));
    
    echo '</div>';
}
add_action('woocommerce_product_options_general_product_data', 'plughaus_add_license_fields');

function plughaus_save_license_fields(\$post_id) {
    \$enable_license = isset(\$_POST['_enable_license_management']) ? 'yes' : 'no';
    update_post_meta(\$post_id, '_enable_license_management', \$enable_license);
    
    if (isset(\$_POST['_license_activations_limit'])) {
        update_post_meta(\$post_id, '_license_activations_limit', sanitize_text_field(\$_POST['_license_activations_limit']));
    }
}
add_action('woocommerce_process_product_meta', 'plughaus_save_license_fields');";
    
    $functions_file = get_template_directory() . '/functions.php';
    $current_functions = file_get_contents($functions_file);
    
    if (strpos($current_functions, 'plughaus_add_woocommerce_support') === false) {
        file_put_contents($functions_file, $current_functions . $functions_addition);
        echo '<p class="success">✓ Added WooCommerce integration functions to theme</p>';
    } else {
        echo '<p class="info">→ WooCommerce functions already added to theme</p>';
    }
    
    echo '</div>';
    
    // Summary and next steps
    echo '<div class="section">';
    echo '<h2>🎉 E-commerce Setup Complete!</h2>';
    
    echo '<div class="grid">';
    
    echo '<div class="card">';
    echo '<h3>✅ What\'s Been Set Up:</h3>';
    echo '<ul>';
    echo '<li>5 Professional plugin products</li>';
    echo '<li>Product categories and organization</li>';
    echo '<li>Essential e-commerce pages</li>';
    echo '<li>Plugin-to-product connections</li>';
    echo '<li>License management integration</li>';
    echo '<li>WooCommerce theme support</li>';
    echo '</ul>';
    echo '</div>';
    
    echo '<div class="card">';
    echo '<h3>🔧 Next Steps Required:</h3>';
    echo '<ol>';
    echo '<li>Configure Stripe payment settings</li>';
    echo '<li>Set up license manager rules</li>';
    echo '<li>Upload plugin ZIP files</li>';
    echo '<li>Test purchase flow</li>';
    echo '<li>Configure email templates</li>';
    echo '</ol>';
    echo '</div>';
    
    echo '</div>';
    
    echo '<div class="warning">';
    echo '<strong>Important Configuration Links:</strong><br>';
    echo '• <a href="' . admin_url('admin.php?page=wc-settings&tab=checkout&section=stripe') . '">Stripe Settings</a> - Configure payment processing<br>';
    echo '• <a href="' . admin_url('edit.php?post_type=product') . '">Manage Products</a> - Edit product details<br>';
    echo '• <a href="' . admin_url('admin.php?page=lmfwc_settings') . '">License Manager</a> - Configure license generation<br>';
    echo '• <a href="' . admin_url('admin.php?page=wc-settings&tab=products&section=downloadable') . '">Download Settings</a> - Configure file delivery';
    echo '</div>';
    
    echo '<a href="' . home_url('/shop/') . '" class="btn" style="background: #28a745;">View Plugin Store</a>';
    echo '<a href="' . home_url('/pricing/') . '" class="btn" style="background: #17a2b8;">View Pricing</a>';
    echo '<a href="' . admin_url('admin.php?page=wc-settings') . '" class="btn">WooCommerce Settings</a>';
    echo '</div>';
    ?>
    
    <style>
    .pricing-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 2rem;
        margin: 2rem 0;
    }
    
    .pricing-card {
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 1rem;
        padding: 2rem;
        text-align: center;
        position: relative;
    }
    
    .pricing-card.featured {
        border-color: #2563eb;
        transform: scale(1.05);
    }
    
    .pricing-card h3 {
        margin-top: 0;
        color: #1e293b;
    }
    
    .price {
        font-size: 2rem;
        font-weight: bold;
        color: #2563eb;
        margin: 1rem 0;
    }
    
    .pricing-card ul {
        list-style: none;
        padding: 0;
        margin: 1.5rem 0;
    }
    
    .pricing-card li {
        padding: 0.5rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    
    .pricing-card .btn {
        width: 100%;
        margin-top: 1rem;
    }
    </style>
    
</body>
</html>