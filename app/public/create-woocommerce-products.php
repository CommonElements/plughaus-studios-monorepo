<?php
/**
 * Vireo Designs - WooCommerce Product Creation Script
 * 
 * Creates downloadable products for all Vireo plugins with proper
 * licensing integration and automated file delivery.
 */

// Load WordPress
require_once __DIR__ . '/wp-load.php';

// Check if WooCommerce is active
if (!class_exists('WooCommerce')) {
    die("❌ WooCommerce not found. Please activate WooCommerce first.\n");
}

echo "🚀 Vireo Designs - WooCommerce Product Creation\n";
echo "=============================================\n\n";

// Plugin definitions with actual build status
$plugins = [
    'vireo-property-management' => [
        'name' => 'Vireo Property Management Pro',
        'slug' => 'vireo-property-management',
        'price' => 149.00,
        'description' => 'Complete property management solution for small to medium property managers. Alternative to expensive SaaS platforms like Yardi and AppFolio.',
        'features' => [
            'Property & Unit Management',
            'Tenant & Lease Tracking', 
            'Maintenance Request System',
            'Payment Tracking & Automation',
            'Advanced Analytics & Reporting',
            'Email Automation',
            'Document Management'
        ],
        'category' => 'Real Estate',
        'status' => 'ready',
        'free_file' => 'vireo-property-management/dist/free/vireo-property-management-free-v1.0.0.zip',
        'pro_file' => 'vireo-property-management/vireo-property-management-pro.zip'
    ],
    
    'vireo-sports-league' => [
        'name' => 'Vireo Sports League Manager Pro',
        'slug' => 'vireo-sports-league',
        'price' => 79.00,
        'description' => 'Professional sports league management for youth sports, amateur leagues, and tournament organizers.',
        'features' => [
            'Team & Player Management',
            'Game Scheduling & Results',
            'League Standings & Statistics',
            'Tournament Brackets',
            'Player Registration',
            'Communication Tools'
        ],
        'category' => 'Sports & Recreation',
        'status' => 'ready',
        'free_file' => 'vireo-sports-league/dist/free/vireo-sports-league-free-v1.0.0.zip',
        'pro_file' => 'vireo-sports-league/vireo-sports-league-pro.zip'
    ],
    
    'equiprent-pro' => [
        'name' => 'EquipRent Pro - Equipment Rental Management',
        'slug' => 'equiprent-pro',
        'price' => 129.00,
        'description' => 'Complete equipment rental management system for tool rental, party supplies, construction equipment, and more.',
        'features' => [
            'Inventory Management',
            'Rental Booking System',
            'Availability Calendar',
            'Customer Management',
            'Damage Assessment',
            'Delivery Scheduling',
            'QR Code Asset Tracking'
        ],
        'category' => 'Business Management',
        'status' => 'ready',
        'free_file' => 'equiprent-pro/dist/free/equiprent-pro-free-v1.0.0.zip',
        'pro_file' => 'equiprent-pro/equiprent-pro.zip'
    ],
    
    'studiosnap' => [
        'name' => 'StudioSnap - Photography Studio Management',
        'slug' => 'studiosnap',
        'price' => 79.00,
        'description' => 'Professional photography studio management for wedding photographers, portrait studios, and event photography.',
        'features' => [
            'Client Booking System',
            'Session Scheduling',
            'Contract Management',
            'Photo Gallery Sharing',
            'Payment Processing',
            'Automated Workflows',
            'Model Release Management'
        ],
        'category' => 'Creative Services',
        'status' => 'ready',
        'free_file' => 'studiosnap/dist/free/studiosnap-free.zip',
        'pro_file' => 'studiosnap/studiosnap-pro.zip'
    ],
    
    'dealeredge' => [
        'name' => 'DealerEdge - Auto Shop & Small Dealer Management',
        'slug' => 'dealeredge',
        'price' => 149.00,
        'description' => 'Comprehensive management system for auto repair shops and small car dealers.',
        'features' => [
            'Work Order Management',
            'Vehicle Inventory',
            'Customer Vehicle History',
            'Parts Inventory Tracking',
            'Service Scheduling',
            'Invoice Generation',
            'DMV Paperwork Integration'
        ],
        'category' => 'Automotive',
        'status' => 'development',
        'free_file' => 'dealeredge/dist/free/dealeredge-free-v1.0.0.zip',
        'pro_file' => 'dealeredge/dealeredge-pro.zip'
    ],
    
    'storageflow' => [
        'name' => 'StorageFlow - Storage & RV Park Management',
        'slug' => 'storageflow',
        'price' => 99.00,
        'description' => 'Management system for self-storage facilities and RV parks.',
        'features' => [
            'Unit Management',
            'Tenant Portal',
            'Online Payments',
            'Access Control Integration',
            'Occupancy Tracking',
            'Automated Billing',
            'Late Fee Management'
        ],
        'category' => 'Storage & Recreation',
        'status' => 'development',
        'free_file' => 'storageflow/dist/free/storageflow-free.zip',
        'pro_file' => 'storageflow/storageflow-pro.zip'
    ]
];

// Create or update products
foreach ($plugins as $plugin_id => $plugin_data) {
    echo "📦 Processing: {$plugin_data['name']}\n";
    
    // Check if product already exists
    $existing_product = get_posts([
        'post_type' => 'product',
        'meta_key' => '_vireo_plugin_slug',
        'meta_value' => $plugin_data['slug'],
        'post_status' => 'any',
        'numberposts' => 1
    ]);
    
    if ($existing_product) {
        $product_id = $existing_product[0]->ID;
        echo "   ↻ Updating existing product (ID: {$product_id})\n";
        
        // Update existing product
        wp_update_post([
            'ID' => $product_id,
            'post_title' => $plugin_data['name'],
            'post_content' => generate_product_description($plugin_data),
            'post_status' => 'publish'
        ]);
        
    } else {
        echo "   ✚ Creating new product\n";
        
        // Create new product
        $product_id = wp_insert_post([
            'post_title' => $plugin_data['name'],
            'post_content' => generate_product_description($plugin_data),
            'post_status' => 'publish',
            'post_type' => 'product'
        ]);
    }
    
    if (is_wp_error($product_id)) {
        echo "   ❌ Error creating product: " . $product_id->get_error_message() . "\n";
        continue;
    }
    
    // Set product meta
    update_post_meta($product_id, '_price', $plugin_data['price']);
    update_post_meta($product_id, '_regular_price', $plugin_data['price']);
    update_post_meta($product_id, '_virtual', 'yes');
    update_post_meta($product_id, '_downloadable', 'yes');
    update_post_meta($product_id, '_download_limit', '5');
    update_post_meta($product_id, '_download_expiry', '365');
    
    // Custom meta for Vireo plugins
    update_post_meta($product_id, '_vireo_plugin_slug', $plugin_data['slug']);
    update_post_meta($product_id, '_vireo_plugin_status', $plugin_data['status']);
    update_post_meta($product_id, '_vireo_plugin_category', $plugin_data['category']);
    
    // Set up downloadable files
    $downloads = [];
    
    // Add free version if file exists
    $free_path = WP_CONTENT_DIR . '/plugins/' . $plugin_data['free_file'];
    if (file_exists($free_path)) {
        $downloads['free'] = [
            'name' => $plugin_data['name'] . ' - Free Version',
            'file' => $plugin_data['free_file']
        ];
        echo "   ✓ Added free version download\n";
    } else {
        echo "   ⚠ Free version not found: {$free_path}\n";
    }
    
    // Add pro version (will be generated on purchase)
    $downloads['pro'] = [
        'name' => $plugin_data['name'] . ' - Pro Version', 
        'file' => $plugin_data['pro_file']
    ];
    echo "   ✓ Added pro version download\n";
    
    update_post_meta($product_id, '_downloadable_files', $downloads);
    
    // Set product categories
    $category_term = get_term_by('name', $plugin_data['category'], 'product_cat');
    if (!$category_term) {
        $category_term = wp_insert_term($plugin_data['category'], 'product_cat');
        $category_term = get_term($category_term['term_id'], 'product_cat');
    }
    
    wp_set_object_terms($product_id, [$category_term->term_id], 'product_cat');
    
    // Set status-based tags
    $status_tag = $plugin_data['status'] === 'ready' ? 'Ready to Ship' : 'In Development';
    $tag_term = get_term_by('name', $status_tag, 'product_tag');
    if (!$tag_term) {
        $tag_term = wp_insert_term($status_tag, 'product_tag');
        $tag_term = get_term($tag_term['term_id'], 'product_tag');
    }
    wp_set_object_terms($product_id, [$tag_term->term_id], 'product_tag');
    
    echo "   ✅ Product created/updated successfully!\n";
    echo "   💰 Price: $" . number_format($plugin_data['price'], 2) . "\n";
    echo "   🏷️ Category: {$plugin_data['category']}\n";
    echo "   📊 Status: {$plugin_data['status']}\n\n";
}

echo "🎉 All products processed successfully!\n\n";

// Display summary
echo "📊 SUMMARY\n";
echo "==========\n";
$ready_count = count(array_filter($plugins, function($p) { return $p['status'] === 'ready'; }));
$dev_count = count(array_filter($plugins, function($p) { return $p['status'] === 'development'; }));
$total_value = array_sum(array_column($plugins, 'price'));

echo "✅ Ready for sale: {$ready_count} plugins\n";
echo "🔧 In development: {$dev_count} plugins\n"; 
echo "💰 Total portfolio value: $" . number_format($total_value, 2) . "\n";
echo "🎯 Average price: $" . number_format($total_value / count($plugins), 2) . "\n\n";

echo "🔗 Next steps:\n";
echo "1. Visit WooCommerce → Products to review\n";
echo "2. Add product images and detailed descriptions\n";
echo "3. Test purchase and download flow\n";
echo "4. Configure License Manager integration\n\n";

function generate_product_description($plugin_data) {
    $desc = $plugin_data['description'] . "\n\n";
    $desc .= "<h3>Key Features:</h3>\n<ul>\n";
    
    foreach ($plugin_data['features'] as $feature) {
        $desc .= "<li>{$feature}</li>\n";
    }
    
    $desc .= "</ul>\n\n";
    
    if ($plugin_data['status'] === 'ready') {
        $desc .= "<p><strong>✅ Ready to Ship:</strong> This plugin is production-ready and available for immediate download after purchase.</p>\n\n";
    } else {
        $desc .= "<p><strong>🔧 In Development:</strong> This plugin is currently in active development. Pre-order now and receive the full version when complete.</p>\n\n";
    }
    
    $desc .= "<p><strong>What's Included:</strong></p>\n";
    $desc .= "<ul>\n";
    $desc .= "<li>Complete plugin with all pro features</li>\n";
    $desc .= "<li>1 year of free updates</li>\n";
    $desc .= "<li>Priority support</li>\n";
    $desc .= "<li>30-day money-back guarantee</li>\n";
    $desc .= "<li>Commercial license for unlimited sites</li>\n";
    $desc .= "</ul>\n\n";
    
    $desc .= "<p><em>Powered by Vireo Designs - Professional WordPress Plugin Solutions</em></p>";
    
    return $desc;
}

echo "✨ WooCommerce integration complete!\n";
?>