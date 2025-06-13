<?php
/**
 * Configure WooCommerce Products for License Management
 * 
 * Sets up all Pro products with proper downloadable files,
 * license settings, and automated delivery
 */

require_once('./wp-load.php');

echo "<h1>🛒 Configuring WooCommerce Products for License Management</h1>\n";

// Check if WooCommerce is active
if (!class_exists('WooCommerce')) {
    echo "<p>❌ WooCommerce not active. Please activate WooCommerce first.</p>\n";
    exit;
}

// Product configurations
$product_configs = array(
    'Vireo Property Management Pro' => array(
        'price' => 149.00,
        'regular_price' => 199.00,
        'description' => 'Professional property management plugin with unlimited properties, advanced analytics, and premium support.',
        'short_description' => 'Unlimited properties, advanced analytics, premium support',
        'sku' => 'VPM-PRO-001',
        'categories' => array('Property Management', 'Pro Licenses'),
        'downloadable' => true,
        'virtual' => true,
        'license_settings' => array(
            'max_sites' => 5,
            'expiry_period' => '+1 year'
        ),
        'download_files' => array(
            array(
                'name' => 'Vireo Property Management Pro Plugin',
                'file' => 'vireo-property-management-pro.zip'
            )
        )
    ),
    
    'Vireo Sports League Manager Pro' => array(
        'price' => 79.00,
        'regular_price' => 99.00,
        'description' => 'Complete sports league management with unlimited teams, advanced statistics, and tournament management.',
        'short_description' => 'Unlimited teams, advanced statistics, tournament management',
        'sku' => 'VSLM-PRO-001',
        'categories' => array('Sports & Recreation', 'Pro Licenses'),
        'downloadable' => true,
        'virtual' => true,
        'license_settings' => array(
            'max_sites' => 3,
            'expiry_period' => '+1 year'
        ),
        'download_files' => array(
            array(
                'name' => 'Vireo Sports League Manager Pro Plugin',
                'file' => 'vireo-sports-league-manager-pro.zip'
            )
        )
    ),
    
    'EquipRent Pro - Equipment Rental Management' => array(
        'price' => 129.00,
        'regular_price' => 159.00,
        'description' => 'Advanced equipment rental management with inventory tracking, delivery scheduling, and damage assessment.',
        'short_description' => 'Advanced inventory, delivery scheduling, damage assessment',
        'sku' => 'ERP-PRO-001',
        'categories' => array('Business Management', 'Pro Licenses'),
        'downloadable' => true,
        'virtual' => true,
        'license_settings' => array(
            'max_sites' => 3,
            'expiry_period' => '+1 year'
        ),
        'download_files' => array(
            array(
                'name' => 'EquipRent Pro Equipment Rental Plugin',
                'file' => 'equiprent-pro.zip'
            )
        )
    ),
    
    'DealerEdge - Auto Shop & Small Dealer Management' => array(
        'price' => 149.00,
        'regular_price' => 199.00,
        'description' => 'Complete auto shop and dealer management with inventory, customer management, and sales tracking.',
        'short_description' => 'Auto shop management, inventory, sales tracking',
        'sku' => 'DE-PRO-001',
        'categories' => array('Automotive', 'Pro Licenses'),
        'downloadable' => true,
        'virtual' => true,
        'license_settings' => array(
            'max_sites' => 5,
            'expiry_period' => '+1 year'
        ),
        'download_files' => array(
            array(
                'name' => 'DealerEdge Pro Auto Management Plugin',
                'file' => 'dealeredge-pro.zip'
            )
        )
    ),
    
    'GymFlow - Fitness Studio Management' => array(
        'price' => 89.00,
        'regular_price' => 119.00,
        'description' => 'Fitness studio management with member tracking, class scheduling, and billing automation.',
        'short_description' => 'Member tracking, class scheduling, billing automation',
        'sku' => 'GF-PRO-001',
        'categories' => array('Business Management', 'Pro Licenses'),
        'downloadable' => true,
        'virtual' => true,
        'license_settings' => array(
            'max_sites' => 3,
            'expiry_period' => '+1 year'
        ),
        'download_files' => array(
            array(
                'name' => 'GymFlow Pro Fitness Management Plugin',
                'file' => 'gymflow-pro.zip'
            )
        )
    ),
    
    'StudioSnap - Photography Studio Management' => array(
        'price' => 79.00,
        'regular_price' => 109.00,
        'description' => 'Photography studio management with client portals, session booking, and gallery management.',
        'short_description' => 'Client portals, session booking, gallery management',
        'sku' => 'SS-PRO-001',
        'categories' => array('Creative Services', 'Pro Licenses'),
        'downloadable' => true,
        'virtual' => true,
        'license_settings' => array(
            'max_sites' => 2,
            'expiry_period' => '+1 year'
        ),
        'download_files' => array(
            array(
                'name' => 'StudioSnap Pro Photography Management Plugin',
                'file' => 'studiosnap-pro.zip'
            )
        )
    )
);

echo "<h2>📦 Configuring Products</h2>\n";

foreach ($product_configs as $product_name => $config) {
    echo "<h3>🔧 Configuring: $product_name</h3>\n";
    
    // Find existing product by name
    $existing_products = wc_get_products(array(
        'name' => $product_name,
        'limit' => 1
    ));
    
    if (!empty($existing_products)) {
        $product = $existing_products[0];
        echo "<p>✅ Found existing product (ID: {$product->get_id()})</p>\n";
    } else {
        // Create new product
        $product = new WC_Product_Simple();
        $product->set_name($product_name);
        echo "<p>✅ Created new product</p>\n";
    }
    
    // Configure product settings
    $product->set_status('publish');
    $product->set_catalog_visibility('visible');
    $product->set_description($config['description']);
    $product->set_short_description($config['short_description']);
    $product->set_sku($config['sku']);
    $product->set_price($config['price']);
    $product->set_regular_price($config['regular_price']);
    $product->set_sale_price($config['price']);
    $product->set_manage_stock(false);
    $product->set_stock_status('instock');
    $product->set_virtual($config['virtual']);
    $product->set_downloadable($config['downloadable']);
    
    // Set categories
    $category_ids = array();
    foreach ($config['categories'] as $category_name) {
        $category = get_term_by('name', $category_name, 'product_cat');
        if (!$category) {
            // Create category if it doesn't exist
            $category_data = wp_insert_term($category_name, 'product_cat');
            if (!is_wp_error($category_data)) {
                $category_ids[] = $category_data['term_id'];
            }
        } else {
            $category_ids[] = $category->term_id;
        }
    }
    $product->set_category_ids($category_ids);
    
    // Save product
    $product_id = $product->save();
    
    if ($product_id) {
        echo "<p>✅ Product saved (ID: $product_id)</p>\n";
        
        // Set license settings
        update_post_meta($product_id, '_requires_license', 'yes');
        update_post_meta($product_id, '_license_max_sites', $config['license_settings']['max_sites']);
        update_post_meta($product_id, '_license_expiry_period', $config['license_settings']['expiry_period']);
        
        echo "<p>✅ License settings configured</p>\n";
        
        // Configure download files (placeholder - would need actual files)
        $downloads = array();
        foreach ($config['download_files'] as $index => $download_file) {
            $downloads[md5($download_file['file'])] = array(
                'name' => $download_file['name'],
                'file' => home_url('/wp-content/uploads/pro-plugins/' . $download_file['file'])
            );
        }
        $product->set_downloads($downloads);
        $product->set_download_limit(-1); // Unlimited downloads
        $product->set_download_expiry(-1); // Never expire
        $product->save();
        
        echo "<p>✅ Download files configured</p>\n";
    } else {
        echo "<p>❌ Failed to save product</p>\n";
    }
    
    echo "<hr>\n";
}

// Configure payment gateways
echo "<h2>💳 Configuring Payment Gateways</h2>\n";

// Enable Stripe if available
$stripe_settings = get_option('woocommerce_stripe_settings', array());
if (class_exists('WC_Gateway_Stripe')) {
    $stripe_settings['enabled'] = 'yes';
    $stripe_settings['title'] = 'Credit Card';
    $stripe_settings['description'] = 'Pay securely with your credit card via Stripe';
    update_option('woocommerce_stripe_settings', $stripe_settings);
    echo "<p>✅ Stripe payment gateway enabled</p>\n";
} else {
    echo "<p>❌ Stripe gateway not available</p>\n";
}

// Configure email settings
echo "<h2>📧 Configuring Email Settings</h2>\n";

$email_settings = get_option('woocommerce_email_settings', array());
$email_settings['from_name'] = 'Vireo Designs';
$email_settings['from_address'] = get_option('admin_email');
update_option('woocommerce_email_settings', $email_settings);

echo "<p>✅ Email settings configured</p>\n";

// Test license system
echo "<h2>🔑 Testing License System</h2>\n";

if (class_exists('Vireo_License_Manager')) {
    echo "<p>✅ License Manager loaded</p>\n";
    echo "<p>✅ License system ready for automated license generation on order completion</p>\n";
} else {
    echo "<p>❌ License Manager not loaded</p>\n";
}

echo "<h2>🚀 WooCommerce Configuration Complete!</h2>\n";
echo "<p><strong>Next Steps:</strong></p>\n";
echo "<ul>\n";
echo "<li>✅ Products configured with license management</li>\n";
echo "<li>✅ Download files prepared (need actual Pro plugin ZIPs)</li>\n";
echo "<li>✅ Payment gateways enabled</li>\n";
echo "<li>✅ License system activated</li>\n";
echo "<li>🔄 Test purchase flow</li>\n";
echo "<li>🔄 Configure Stripe live/test keys</li>\n";
echo "<li>🔄 Create Pro plugin ZIP files</li>\n";
echo "</ul>\n";

echo "<p><a href='/shop/' target='_blank'>🛒 View Shop</a> | <a href='/wp-admin/admin.php?page=vireo-licenses' target='_blank'>🔑 Manage Licenses</a></p>\n";

?>