<?php
/**
 * E-commerce Setup Script for PlugHaus Studios
 * Sets up WooCommerce + Stripe + License Management
 * Run this by visiting: http://plughaus-studios-the-beginning-is-finished.local/setup-ecommerce.php
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
    <title>PlugHaus Studios E-commerce Setup</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 0 auto; padding: 20px; }
        h1, h2 { color: #2563eb; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: #0066cc; font-style: italic; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 4px; color: #856404; margin: 20px 0; }
        .btn { background: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin: 10px 5px 10px 0; }
        .section { background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 8px; }
        .code { background: #f1f3f4; padding: 10px; border-radius: 4px; font-family: monospace; margin: 10px 0; }
        .step { background: white; border: 1px solid #dee2e6; padding: 15px; margin: 10px 0; border-radius: 6px; }
    </style>
</head>
<body>
    <h1>PlugHaus Studios E-commerce Setup</h1>
    
    <div class="warning">
        <strong>Important:</strong> This script sets up the foundation for your plugin business. You'll need to install WooCommerce and configure payment methods separately.
    </div>
    
    <?php
    // Check for existing WooCommerce
    echo '<div class="section">';
    echo '<h2>1. WooCommerce Status Check</h2>';
    
    if (class_exists('WooCommerce')) {
        echo '<p class="success">✓ WooCommerce is installed and active</p>';
        echo '<p class="info">WooCommerce Version: ' . WC()->version . '</p>';
    } else {
        echo '<p class="error">✗ WooCommerce not found</p>';
        echo '<div class="step">';
        echo '<h3>Install WooCommerce:</h3>';
        echo '<p>1. Go to <a href="' . admin_url('plugin-install.php?s=woocommerce&tab=search&type=term') . '">Plugins → Add New</a></p>';
        echo '<p>2. Search for "WooCommerce"</p>';
        echo '<p>3. Install and activate WooCommerce</p>';
        echo '<p>4. Run the WooCommerce Setup Wizard</p>';
        echo '</div>';
    }
    echo '</div>';
    
    // Create product categories for plugins
    echo '<div class="section">';
    echo '<h2>2. Setting Up Product Categories</h2>';
    
    if (class_exists('WooCommerce')) {
        $plugin_categories = array(
            'WordPress Plugins' => 'Our premium WordPress plugins',
            'Property Management' => 'Property and real estate management solutions',
            'Payment Processing' => 'Payment gateway and e-commerce tools',
            'Business Automation' => 'Tools for business process automation',
            'Pro Licenses' => 'Premium plugin licenses and upgrades'
        );
        
        foreach ($plugin_categories as $cat_name => $cat_description) {
            $existing_cat = get_term_by('name', $cat_name, 'product_cat');
            
            if (!$existing_cat) {
                $cat_id = wp_insert_term($cat_name, 'product_cat', array(
                    'description' => $cat_description
                ));
                
                if (!is_wp_error($cat_id)) {
                    echo '<p class="success">✓ Created category: ' . $cat_name . '</p>';
                } else {
                    echo '<p class="error">✗ Failed to create category: ' . $cat_name . '</p>';
                }
            } else {
                echo '<p class="info">→ Category already exists: ' . $cat_name . '</p>';
            }
        }
    } else {
        echo '<p class="warning">Install WooCommerce first to create product categories.</p>';
    }
    echo '</div>';
    
    // Create sample products
    echo '<div class="section">';
    echo '<h2>3. Creating Sample Products</h2>';
    
    if (class_exists('WooCommerce')) {
        $sample_products = array(
            array(
                'name' => 'PlugHaus Property Management Pro',
                'description' => 'Complete property management solution with advanced features, analytics, and automation tools.',
                'short_description' => 'Advanced property management with pro features.',
                'price' => '99.00',
                'category' => 'Property Management',
                'type' => 'simple',
                'downloadable' => true,
                'virtual' => true
            ),
            array(
                'name' => 'Payment Gateway Pro License',
                'description' => 'Professional payment processing with multiple gateways, recurring billing, and advanced security features.',
                'short_description' => 'Pro payment processing solution.',
                'price' => '149.00',
                'category' => 'Payment Processing',
                'type' => 'simple',
                'downloadable' => true,
                'virtual' => true
            ),
            array(
                'name' => 'All Plugins Bundle',
                'description' => 'Get all PlugHaus Studios plugins with lifetime updates and priority support.',
                'short_description' => 'Complete plugin bundle with lifetime access.',
                'price' => '299.00',
                'category' => 'Pro Licenses',
                'type' => 'simple',
                'downloadable' => true,
                'virtual' => true
            )
        );
        
        foreach ($sample_products as $product_data) {
            // Check if product exists
            $existing_product = get_page_by_title($product_data['name'], OBJECT, 'product');
            
            if (!$existing_product) {
                $product = new WC_Product_Simple();
                
                $product->set_name($product_data['name']);
                $product->set_description($product_data['description']);
                $product->set_short_description($product_data['short_description']);
                $product->set_regular_price($product_data['price']);
                $product->set_downloadable($product_data['downloadable']);
                $product->set_virtual($product_data['virtual']);
                $product->set_status('publish');
                
                $product_id = $product->save();
                
                if ($product_id) {
                    // Assign category
                    $cat_term = get_term_by('name', $product_data['category'], 'product_cat');
                    if ($cat_term) {
                        wp_set_object_terms($product_id, $cat_term->term_id, 'product_cat');
                    }
                    
                    echo '<p class="success">✓ Created product: ' . $product_data['name'] . '</p>';
                } else {
                    echo '<p class="error">✗ Failed to create product: ' . $product_data['name'] . '</p>';
                }
            } else {
                echo '<p class="info">→ Product already exists: ' . $product_data['name'] . '</p>';
            }
        }
    } else {
        echo '<p class="warning">Install WooCommerce first to create products.</p>';
    }
    echo '</div>';
    
    // License management setup
    echo '<div class="section">';
    echo '<h2>4. License Management Setup</h2>';
    
    echo '<div class="step">';
    echo '<h3>Recommended License Management Solutions:</h3>';
    echo '<ol>';
    echo '<li><strong>WooCommerce Software Add-on</strong> - Official WooCommerce solution</li>';
    echo '<li><strong>License Manager for WooCommerce</strong> - Popular third-party plugin</li>';
    echo '<li><strong>Easy Digital Downloads</strong> - Alternative to WooCommerce for digital products</li>';
    echo '</ol>';
    echo '</div>';
    
    echo '<div class="code">';
    echo '<strong>Install License Manager for WooCommerce:</strong><br>';
    echo '1. Download from: <a href="https://www.licensemanager.at/" target="_blank">https://www.licensemanager.at/</a><br>';
    echo '2. Or search "License Manager" in WordPress plugins<br>';
    echo '3. Configure API keys and license generation<br>';
    echo '4. Set up automatic plugin updates';
    echo '</div>';
    echo '</div>';
    
    // Stripe integration
    echo '<div class="section">';
    echo '<h2>5. Stripe Payment Setup</h2>';
    
    echo '<div class="step">';
    echo '<h3>Setting Up Stripe Payments:</h3>';
    echo '<ol>';
    echo '<li>Install WooCommerce Stripe Gateway plugin</li>';
    echo '<li>Get Stripe API keys from <a href="https://dashboard.stripe.com/apikeys" target="_blank">Stripe Dashboard</a></li>';
    echo '<li>Configure in WooCommerce → Settings → Payments → Stripe</li>';
    echo '<li>Test with Stripe test keys first</li>';
    echo '</ol>';
    echo '</div>';
    
    echo '<div class="code">';
    echo '<strong>Stripe Test Cards:</strong><br>';
    echo 'Success: 4242 4242 4242 4242<br>';
    echo 'Decline: 4000 0000 0000 0002<br>';
    echo 'CVV: 123, Exp: Any future date';
    echo '</div>';
    echo '</div>';
    
    // Integration with existing plugins
    echo '<div class="section">';
    echo '<h2>6. Integration with Plugin Showcase</h2>';
    
    echo '<p class="info">Let\'s connect your existing plugin showcase with WooCommerce products:</p>';
    
    // Add meta box for linking plugins to products
    $meta_box_code = "
// Add this to your theme's functions.php
function plughaus_add_product_link_meta_box() {
    add_meta_box(
        'plugin_product_link',
        'WooCommerce Product Link',
        'plughaus_product_link_callback',
        'phstudios_plugin',
        'side'
    );
}
add_action('add_meta_boxes', 'plughaus_add_product_link_meta_box');

function plughaus_product_link_callback(\$post) {
    \$product_id = get_post_meta(\$post->ID, '_linked_product', true);
    \$products = get_posts(array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    ));
    
    echo '<select name=\"linked_product\" style=\"width: 100%\">';
    echo '<option value=\"\">Select a product...</option>';
    foreach (\$products as \$product) {
        \$selected = selected(\$product_id, \$product->ID, false);
        echo '<option value=\"' . \$product->ID . '\" ' . \$selected . '>' . \$product->post_title . '</option>';
    }
    echo '</select>';
}

function plughaus_save_product_link(\$post_id) {
    if (isset(\$_POST['linked_product'])) {
        update_post_meta(\$post_id, '_linked_product', sanitize_text_field(\$_POST['linked_product']));
    }
}
add_action('save_post', 'plughaus_save_product_link');";
    
    echo '<div class="code">' . esc_html($meta_box_code) . '</div>';
    echo '</div>';
    
    // Next steps
    echo '<div class="section">';
    echo '<h2>🚀 Next Steps Checklist</h2>';
    
    echo '<h3>Required Plugins to Install:</h3>';
    echo '<ol>';
    echo '<li>✅ <strong>WooCommerce</strong> - Core e-commerce platform</li>';
    echo '<li>🔄 <strong>WooCommerce Stripe Gateway</strong> - Payment processing</li>';
    echo '<li>🔄 <strong>License Manager for WooCommerce</strong> - Software licensing</li>';
    echo '<li>📧 <strong>MailChimp for WooCommerce</strong> - Email marketing (optional)</li>';
    echo '</ol>';
    
    echo '<h3>Configuration Steps:</h3>';
    echo '<ol>';
    echo '<li>Run WooCommerce setup wizard</li>';
    echo '<li>Configure Stripe API keys</li>';
    echo '<li>Set up license generation rules</li>';
    echo '<li>Create download files for each plugin</li>';
    echo '<li>Test purchase and license delivery flow</li>';
    echo '</ol>';
    
    echo '<div class="warning">';
    echo '<strong>Important:</strong> You\'ll need:<br>';
    echo '• Stripe account for payments<br>';
    echo '• SSL certificate for secure checkout<br>';
    echo '• Plugin ZIP files ready for download<br>';
    echo '• License validation endpoints in your plugins';
    echo '</div>';
    
    echo '<a href="' . admin_url('admin.php?page=wc-settings') . '" class="btn">WooCommerce Settings</a>';
    echo '<a href="' . admin_url('edit.php?post_type=product') . '" class="btn" style="background: #28a745;">Manage Products</a>';
    echo '<a href="' . home_url() . '" class="btn" style="background: #6c757d;">View Site</a>';
    echo '</div>';
    ?>
    
</body>
</html>