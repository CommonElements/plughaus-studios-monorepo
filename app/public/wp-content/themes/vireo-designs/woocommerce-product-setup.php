<?php
/**
 * WooCommerce Product Setup for Vireo Plugins
 * This file creates WooCommerce products for all Vireo plugins
 * Run once to set up the store products
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Create WooCommerce products for Vireo plugins
 */
function vireo_create_plugin_products() {
    
    // Check if WooCommerce is active
    if (!class_exists('WooCommerce')) {
        return new WP_Error('woocommerce_missing', 'WooCommerce must be installed and activated');
    }
    
    $products_created = [];
    
    // 1. Vireo Property Management Pro
    $property_pro = vireo_create_product([
        'name' => 'Vireo Property Management Pro',
        'slug' => 'vireo-property-management-pro',
        'type' => 'simple',
        'status' => 'draft', // Draft until plugin is ready
        'price' => '99.00',
        'regular_price' => '129.00',
        'sale_price' => '99.00', // Early bird pricing
        'description' => 'Complete property management solution for WordPress with advanced analytics, payment automation, and priority support.',
        'short_description' => 'Professional property management plugin with all pro features included.',
        'downloadable' => true,
        'virtual' => true,
        'category' => 'Property Management',
        'tags' => ['property', 'management', 'real-estate', 'wordpress-plugin'],
        'images' => [], // Will add product images later
        'attributes' => [
            'license-type' => 'Annual License',
            'plugin-version' => '1.0.0',
            'wordpress-compatibility' => '5.8+',
            'php-compatibility' => '7.4+'
        ],
        'meta_data' => [
            '_vireo_plugin_type' => 'pro',
            '_vireo_free_version' => 'vireo-property-management',
            '_vireo_license_duration' => '1 year',
            '_vireo_support_level' => 'priority'
        ]
    ]);
    
    if (!is_wp_error($property_pro)) {
        $products_created['property_pro'] = $property_pro;
    }
    
    // 2. Vireo Sports League Manager Pro (Pre-order)
    $sports_pro = vireo_create_product([
        'name' => 'Vireo Sports League Manager Pro',
        'slug' => 'vireo-sports-league-pro',
        'type' => 'simple',
        'status' => 'draft',
        'price' => '79.00',
        'regular_price' => '99.00',
        'sale_price' => '79.00', // Pre-order pricing
        'description' => 'Complete sports league management with teams, schedules, statistics, tournaments, and advanced analytics.',
        'short_description' => 'Professional sports league management plugin - Pre-order now for early access.',
        'downloadable' => true,
        'virtual' => true,
        'category' => 'Sports Management',
        'tags' => ['sports', 'league', 'management', 'wordpress-plugin'],
        'attributes' => [
            'license-type' => 'Annual License',
            'plugin-status' => 'Pre-order',
            'expected-release' => 'Q2 2025',
            'wordpress-compatibility' => '5.8+',
            'php-compatibility' => '7.4+'
        ],
        'meta_data' => [
            '_vireo_plugin_type' => 'pro',
            '_vireo_plugin_status' => 'preorder',
            '_vireo_expected_release' => '2025-06-01',
            '_vireo_license_duration' => '1 year',
            '_vireo_support_level' => 'priority'
        ]
    ]);
    
    if (!is_wp_error($sports_pro)) {
        $products_created['sports_pro'] = $sports_pro;
    }
    
    // 3. Vireo Equipment Rental Pro (Coming Soon)
    $equipment_pro = vireo_create_product([
        'name' => 'Vireo Equipment Rental Pro',
        'slug' => 'vireo-equipment-rental-pro',
        'type' => 'simple',
        'status' => 'draft',
        'price' => '129.00',
        'regular_price' => '159.00',
        'sale_price' => '129.00',
        'description' => 'Professional equipment rental management with inventory tracking, booking system, and customer management.',
        'short_description' => 'Complete equipment rental solution - Join the waitlist for early access.',
        'downloadable' => true,
        'virtual' => true,
        'category' => 'Equipment Management',
        'tags' => ['equipment', 'rental', 'inventory', 'wordpress-plugin'],
        'attributes' => [
            'license-type' => 'Annual License',
            'plugin-status' => 'Coming Soon',
            'expected-release' => 'Q3 2025',
            'wordpress-compatibility' => '5.8+',
            'php-compatibility' => '7.4+'
        ],
        'meta_data' => [
            '_vireo_plugin_type' => 'pro',
            '_vireo_plugin_status' => 'coming_soon',
            '_vireo_expected_release' => '2025-09-01',
            '_vireo_license_duration' => '1 year',
            '_vireo_support_level' => 'priority'
        ]
    ]);
    
    if (!is_wp_error($equipment_pro)) {
        $products_created['equipment_pro'] = $equipment_pro;
    }
    
    // 4. Vireo Auto Dealer Pro (Planned)
    $dealer_pro = vireo_create_product([
        'name' => 'Vireo Auto Dealer Pro',
        'slug' => 'vireo-auto-dealer-pro',
        'type' => 'simple',
        'status' => 'draft',
        'price' => '149.00',
        'regular_price' => '199.00',
        'sale_price' => '149.00',
        'description' => 'Small car dealer management system with inventory, sales tracking, customer management, and financial reporting.',
        'short_description' => 'Complete auto dealer solution for small dealerships - Notify when available.',
        'downloadable' => true,
        'virtual' => true,
        'category' => 'Auto Dealer',
        'tags' => ['auto', 'dealer', 'inventory', 'sales', 'wordpress-plugin'],
        'attributes' => [
            'license-type' => 'Annual License',
            'plugin-status' => 'Planned',
            'expected-release' => 'Q4 2025',
            'wordpress-compatibility' => '5.8+',
            'php-compatibility' => '7.4+'
        ],
        'meta_data' => [
            '_vireo_plugin_type' => 'pro',
            '_vireo_plugin_status' => 'planned',
            '_vireo_expected_release' => '2025-12-01',
            '_vireo_license_duration' => '1 year',
            '_vireo_support_level' => 'priority'
        ]
    ]);
    
    if (!is_wp_error($dealer_pro)) {
        $products_created['dealer_pro'] = $dealer_pro;
    }
    
    // 5. Create product categories
    vireo_create_product_categories();
    
    return $products_created;
}

/**
 * Helper function to create a WooCommerce product
 */
function vireo_create_product($args) {
    
    $defaults = [
        'name' => '',
        'slug' => '',
        'type' => 'simple',
        'status' => 'publish',
        'price' => '',
        'regular_price' => '',
        'sale_price' => '',
        'description' => '',
        'short_description' => '',
        'downloadable' => false,
        'virtual' => false,
        'category' => '',
        'tags' => [],
        'images' => [],
        'attributes' => [],
        'meta_data' => []
    ];
    
    $args = wp_parse_args($args, $defaults);
    
    // Check if product already exists
    $existing_product = get_page_by_path($args['slug'], OBJECT, 'product');
    if ($existing_product) {
        return new WP_Error('product_exists', 'Product already exists: ' . $args['name']);
    }
    
    // Create the product
    $product = new WC_Product_Simple();
    
    $product->set_name($args['name']);
    $product->set_slug($args['slug']);
    $product->set_status($args['status']);
    $product->set_description($args['description']);
    $product->set_short_description($args['short_description']);
    $product->set_downloadable($args['downloadable']);
    $product->set_virtual($args['virtual']);
    
    // Set pricing
    if (!empty($args['regular_price'])) {
        $product->set_regular_price($args['regular_price']);
    }
    if (!empty($args['sale_price'])) {
        $product->set_sale_price($args['sale_price']);
    }
    if (!empty($args['price'])) {
        $product->set_price($args['price']);
    }
    
    // Set category
    if (!empty($args['category'])) {
        $category_id = vireo_get_or_create_category($args['category']);
        if ($category_id) {
            $product->set_category_ids([$category_id]);
        }
    }
    
    // Set tags
    if (!empty($args['tags'])) {
        $tag_ids = [];
        foreach ($args['tags'] as $tag_name) {
            $tag = get_term_by('name', $tag_name, 'product_tag');
            if (!$tag) {
                $tag_result = wp_insert_term($tag_name, 'product_tag');
                if (!is_wp_error($tag_result)) {
                    $tag_ids[] = $tag_result['term_id'];
                }
            } else {
                $tag_ids[] = $tag->term_id;
            }
        }
        $product->set_tag_ids($tag_ids);
    }
    
    // Set attributes
    if (!empty($args['attributes'])) {
        $attributes = [];
        foreach ($args['attributes'] as $key => $value) {
            $attribute = new WC_Product_Attribute();
            $attribute->set_name($key);
            $attribute->set_options([$value]);
            $attribute->set_visible(true);
            $attributes[] = $attribute;
        }
        $product->set_attributes($attributes);
    }
    
    // Save the product
    $product_id = $product->save();
    
    if ($product_id) {
        // Add meta data
        foreach ($args['meta_data'] as $key => $value) {
            update_post_meta($product_id, $key, $value);
        }
        
        return $product_id;
    }
    
    return new WP_Error('product_creation_failed', 'Failed to create product: ' . $args['name']);
}

/**
 * Get or create product category
 */
function vireo_get_or_create_category($category_name) {
    $category = get_term_by('name', $category_name, 'product_cat');
    
    if (!$category) {
        $result = wp_insert_term($category_name, 'product_cat');
        if (!is_wp_error($result)) {
            return $result['term_id'];
        }
        return false;
    }
    
    return $category->term_id;
}

/**
 * Create product categories for Vireo plugins
 */
function vireo_create_product_categories() {
    $categories = [
        'Property Management' => 'WordPress plugins for property and real estate management',
        'Sports Management' => 'WordPress plugins for sports leagues and team management',
        'Equipment Management' => 'WordPress plugins for equipment rental and inventory management',
        'Auto Dealer' => 'WordPress plugins for automotive dealers and inventory management',
        'Business Management' => 'WordPress plugins for general business management'
    ];
    
    foreach ($categories as $name => $description) {
        $existing = get_term_by('name', $name, 'product_cat');
        if (!$existing) {
            wp_insert_term($name, 'product_cat', [
                'description' => $description,
                'slug' => sanitize_title($name)
            ]);
        }
    }
}

/**
 * Setup WooCommerce for digital downloads
 */
function vireo_configure_woocommerce_settings() {
    
    // Enable downloads
    update_option('woocommerce_enable_downloads', 'yes');
    
    // Set download permissions
    update_option('woocommerce_downloads_require_login', 'yes');
    update_option('woocommerce_downloads_grant_access_after_payment', 'yes');
    
    // Set up pages
    $pages = [
        'shop' => 'Store',
        'cart' => 'Cart',
        'checkout' => 'Checkout',
        'myaccount' => 'My Account',
        'terms' => 'Terms & Conditions'
    ];
    
    foreach ($pages as $option => $title) {
        $page_id = get_option('woocommerce_' . $option . '_page_id');
        if (!$page_id || !get_post($page_id)) {
            $page_id = wp_insert_post([
                'post_title' => $title,
                'post_content' => '[woocommerce_' . $option . ']',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_name' => $option
            ]);
            update_option('woocommerce_' . $option . '_page_id', $page_id);
        }
    }
    
    // Currency settings
    update_option('woocommerce_currency', 'USD');
    update_option('woocommerce_currency_pos', 'left');
    
    // Enable guest checkout
    update_option('woocommerce_enable_guest_checkout', 'yes');
    update_option('woocommerce_enable_checkout_login_reminder', 'yes');
    
    return true;
}

/**
 * Run the setup (call this function to execute)
 */
function vireo_run_product_setup() {
    
    // Configure WooCommerce
    $wc_setup = vireo_configure_woocommerce_settings();
    
    // Create products
    $products = vireo_create_plugin_products();
    
    $results = [
        'woocommerce_configured' => $wc_setup,
        'products_created' => $products
    ];
    
    return $results;
}

// Uncomment to run setup (run only once)
// add_action('init', 'vireo_run_product_setup');

?>