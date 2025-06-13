<?php
/**
 * Test Purchase Flow for Vireo Designs
 * Complete testing of purchase → download → license activation
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Vireo_Purchase_Flow_Test {
    
    public function __construct() {
        add_action('init', [$this, 'init']);
    }
    
    public function init() {
        // Only run tests if user is admin and in debug mode
        if (!current_user_can('manage_options') || !defined('WP_DEBUG') || !WP_DEBUG) {
            return;
        }
        
        // Add admin menu for testing
        add_action('admin_menu', [$this, 'add_test_menu']);
        
        // Handle test actions
        add_action('wp_ajax_vireo_test_purchase_flow', [$this, 'test_purchase_flow']);
        add_action('wp_ajax_vireo_test_license_generation', [$this, 'test_license_generation']);
        add_action('wp_ajax_vireo_test_download_delivery', [$this, 'test_download_delivery']);
    }
    
    /**
     * Add test menu to admin
     */
    public function add_test_menu() {
        add_submenu_page(
            'tools.php',
            'Vireo Purchase Flow Test',
            'Vireo Purchase Test',
            'manage_options',
            'vireo-purchase-test',
            [$this, 'render_test_page']
        );
    }
    
    /**
     * Render test page
     */
    public function render_test_page() {
        ?>
        <div class="wrap">
            <h1>Vireo Purchase Flow Test</h1>
            <p>Test the complete purchase → download → license activation flow</p>
            
            <div class="vireo-test-sections">
                
                <!-- WooCommerce Setup Test -->
                <div class="test-section">
                    <h2>1. WooCommerce Setup Test</h2>
                    <p>Verify WooCommerce and License Manager are properly configured</p>
                    <button class="button button-primary" onclick="testWooCommerceSetup()">Test WooCommerce Setup</button>
                    <div id="woocommerce-test-results"></div>
                </div>
                
                <!-- Product Creation Test -->
                <div class="test-section">
                    <h2>2. Product Creation Test</h2>
                    <p>Test creation of Vireo plugin products with proper metadata</p>
                    <button class="button button-primary" onclick="testProductCreation()">Create Test Products</button>
                    <div id="product-test-results"></div>
                </div>
                
                <!-- License Generation Test -->
                <div class="test-section">
                    <h2>3. License Generation Test</h2>
                    <p>Test automatic license generation on order completion</p>
                    <button class="button button-primary" onclick="testLicenseGeneration()">Test License Generation</button>
                    <div id="license-test-results"></div>
                </div>
                
                <!-- Download Delivery Test -->
                <div class="test-section">
                    <h2>4. Download Delivery Test</h2>
                    <p>Test downloadable product delivery and customer access</p>
                    <button class="button button-primary" onclick="testDownloadDelivery()">Test Download Delivery</button>
                    <div id="download-test-results"></div>
                </div>
                
                <!-- Email Notification Test -->
                <div class="test-section">
                    <h2>5. Email Notification Test</h2>
                    <p>Test license email delivery to customers</p>
                    <button class="button button-primary" onclick="testEmailNotification()">Test Email Notification</button>
                    <div id="email-test-results"></div>
                </div>
                
                <!-- Complete Flow Test -->
                <div class="test-section">
                    <h2>6. Complete Flow Simulation</h2>
                    <p>Simulate a complete customer purchase journey</p>
                    <button class="button button-primary" onclick="testCompleteFlow()">Run Complete Flow Test</button>
                    <div id="complete-test-results"></div>
                </div>
                
            </div>
        </div>
        
        <style>
        .test-section {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        .test-section h2 {
            margin-top: 0;
            color: #059669;
        }
        .test-results {
            margin-top: 15px;
            padding: 10px;
            border-radius: 4px;
            display: none;
        }
        .test-results.success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .test-results.error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .test-results.info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }
        </style>
        
        <script>
        function testWooCommerceSetup() {
            showLoading('woocommerce-test-results');
            
            jQuery.post(ajaxurl, {
                action: 'vireo_test_woocommerce_setup',
                nonce: '<?php echo wp_create_nonce('vireo_test_nonce'); ?>'
            }, function(response) {
                showResults('woocommerce-test-results', response);
            });
        }
        
        function testProductCreation() {
            showLoading('product-test-results');
            
            jQuery.post(ajaxurl, {
                action: 'vireo_test_product_creation',
                nonce: '<?php echo wp_create_nonce('vireo_test_nonce'); ?>'
            }, function(response) {
                showResults('product-test-results', response);
            });
        }
        
        function testLicenseGeneration() {
            showLoading('license-test-results');
            
            jQuery.post(ajaxurl, {
                action: 'vireo_test_license_generation',
                nonce: '<?php echo wp_create_nonce('vireo_test_nonce'); ?>'
            }, function(response) {
                showResults('license-test-results', response);
            });
        }
        
        function testDownloadDelivery() {
            showLoading('download-test-results');
            
            jQuery.post(ajaxurl, {
                action: 'vireo_test_download_delivery',
                nonce: '<?php echo wp_create_nonce('vireo_test_nonce'); ?>'
            }, function(response) {
                showResults('download-test-results', response);
            });
        }
        
        function testEmailNotification() {
            showLoading('email-test-results');
            
            jQuery.post(ajaxurl, {
                action: 'vireo_test_email_notification',
                nonce: '<?php echo wp_create_nonce('vireo_test_nonce'); ?>'
            }, function(response) {
                showResults('email-test-results', response);
            });
        }
        
        function testCompleteFlow() {
            showLoading('complete-test-results');
            
            jQuery.post(ajaxurl, {
                action: 'vireo_test_complete_flow',
                nonce: '<?php echo wp_create_nonce('vireo_test_nonce'); ?>'
            }, function(response) {
                showResults('complete-test-results', response);
            });
        }
        
        function showLoading(elementId) {
            const element = document.getElementById(elementId);
            element.className = 'test-results info';
            element.style.display = 'block';
            element.innerHTML = '<strong>Testing...</strong> Please wait while we run the test.';
        }
        
        function showResults(elementId, response) {
            const element = document.getElementById(elementId);
            element.className = 'test-results ' + (response.success ? 'success' : 'error');
            element.style.display = 'block';
            element.innerHTML = '<strong>' + (response.success ? 'Success!' : 'Error!') + '</strong><br>' + response.message;
            
            if (response.data) {
                element.innerHTML += '<br><br><strong>Details:</strong><br><pre>' + JSON.stringify(response.data, null, 2) + '</pre>';
            }
        }
        </script>
        <?php
    }
    
    /**
     * Test WooCommerce setup
     */
    public function test_woocommerce_setup() {
        check_ajax_referer('vireo_test_nonce', 'nonce');
        
        $results = [];
        
        // Check if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            wp_send_json_error('WooCommerce is not installed or activated');
            return;
        }
        $results['woocommerce'] = 'Active';
        
        // Check if License Manager is active
        if (!class_exists('LicenseManagerForWooCommerce\Main')) {
            wp_send_json_error('License Manager for WooCommerce is not installed or activated');
            return;
        }
        $results['license_manager'] = 'Active';
        
        // Check WooCommerce pages
        $pages = ['shop', 'cart', 'checkout', 'myaccount'];
        foreach ($pages as $page) {
            $page_id = get_option('woocommerce_' . $page . '_page_id');
            $results['page_' . $page] = $page_id ? 'Configured' : 'Missing';
        }
        
        // Check currency settings
        $results['currency'] = get_option('woocommerce_currency', 'Not Set');
        
        wp_send_json_success('WooCommerce setup is properly configured', $results);
    }
    
    /**
     * Test product creation
     */
    public function test_product_creation() {
        check_ajax_referer('vireo_test_nonce', 'nonce');
        
        // Include product setup functions
        require_once get_template_directory() . '/woocommerce-product-setup.php';
        
        try {
            // Create test product
            $product_id = vireo_create_product([
                'name' => 'Test Vireo Plugin',
                'slug' => 'test-vireo-plugin-' . time(),
                'type' => 'simple',
                'status' => 'draft',
                'price' => '99.00',
                'description' => 'Test plugin for purchase flow testing',
                'downloadable' => true,
                'virtual' => true,
                'meta_data' => [
                    '_vireo_plugin_type' => 'pro',
                    '_vireo_license_duration' => '1 year'
                ]
            ]);
            
            if (is_wp_error($product_id)) {
                wp_send_json_error($product_id->get_error_message());
                return;
            }
            
            $product = wc_get_product($product_id);
            $results = [
                'product_id' => $product_id,
                'product_name' => $product->get_name(),
                'product_price' => $product->get_price(),
                'is_downloadable' => $product->is_downloadable(),
                'is_virtual' => $product->is_virtual()
            ];
            
            wp_send_json_success('Test product created successfully', $results);
            
        } catch (Exception $e) {
            wp_send_json_error('Failed to create test product: ' . $e->getMessage());
        }
    }
    
    /**
     * Test license generation
     */
    public function test_license_generation() {
        check_ajax_referer('vireo_test_nonce', 'nonce');
        
        try {
            // Create test customer
            $customer_id = $this->create_test_customer();
            
            // Create test order
            $order_id = $this->create_test_order($customer_id);
            
            // Simulate order completion
            $order = wc_get_order($order_id);
            $order->update_status('completed');
            
            // Check if license was generated
            global $wpdb;
            $license = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}lmfwc_licenses WHERE order_id = %d",
                $order_id
            ));
            
            if ($license) {
                $results = [
                    'order_id' => $order_id,
                    'license_key' => $license->license_key,
                    'license_status' => $license->status,
                    'expires_at' => $license->expires_at
                ];
                
                wp_send_json_success('License generated successfully', $results);
            } else {
                wp_send_json_error('License was not generated automatically');
            }
            
        } catch (Exception $e) {
            wp_send_json_error('Failed to test license generation: ' . $e->getMessage());
        }
    }
    
    /**
     * Test download delivery
     */
    public function test_download_delivery() {
        check_ajax_referer('vireo_test_nonce', 'nonce');
        
        try {
            // Create test downloadable file
            $test_file_path = $this->create_test_download_file();
            
            // Create downloadable product with file
            $product_id = $this->create_downloadable_product($test_file_path);
            
            // Create test order with downloadable product
            $customer_id = $this->create_test_customer();
            $order_id = $this->create_test_order($customer_id, $product_id);
            
            // Complete order
            $order = wc_get_order($order_id);
            $order->update_status('completed');
            
            // Check if download permissions were granted
            $downloads = wc_get_customer_download_permissions($customer_id);
            
            $results = [
                'order_id' => $order_id,
                'product_id' => $product_id,
                'download_permissions' => count($downloads),
                'test_file_created' => file_exists($test_file_path)
            ];
            
            if (count($downloads) > 0) {
                $results['download_details'] = $downloads[0];
                wp_send_json_success('Download delivery working correctly', $results);
            } else {
                wp_send_json_error('Download permissions were not granted', $results);
            }
            
        } catch (Exception $e) {
            wp_send_json_error('Failed to test download delivery: ' . $e->getMessage());
        }
    }
    
    /**
     * Create test customer
     */
    private function create_test_customer() {
        $customer = new WC_Customer();
        $customer->set_email('test@vireodesigns.com');
        $customer->set_first_name('Test');
        $customer->set_last_name('Customer');
        $customer->set_billing_email('test@vireodesigns.com');
        $customer->set_billing_first_name('Test');
        $customer->set_billing_last_name('Customer');
        
        return $customer->save();
    }
    
    /**
     * Create test order
     */
    private function create_test_order($customer_id, $product_id = null) {
        if (!$product_id) {
            // Get any Vireo product for testing
            $products = get_posts([
                'post_type' => 'product',
                'meta_query' => [
                    [
                        'key' => '_vireo_plugin_type',
                        'compare' => 'EXISTS'
                    ]
                ],
                'posts_per_page' => 1
            ]);
            
            if (empty($products)) {
                throw new Exception('No Vireo products found for testing');
            }
            
            $product_id = $products[0]->ID;
        }
        
        $order = wc_create_order(['customer_id' => $customer_id]);
        $product = wc_get_product($product_id);
        $order->add_product($product, 1);
        $order->calculate_totals();
        
        return $order->get_id();
    }
    
    /**
     * Create test download file
     */
    private function create_test_download_file() {
        $uploads_dir = wp_upload_dir();
        $test_file_path = $uploads_dir['basedir'] . '/vireo-test-plugin.zip';
        
        // Create a simple test ZIP file
        $zip = new ZipArchive();
        if ($zip->open($test_file_path, ZipArchive::CREATE) === TRUE) {
            $zip->addFromString('test-plugin.php', '<?php // Test plugin file');
            $zip->addFromString('readme.txt', 'Test plugin for download testing');
            $zip->close();
        }
        
        return $test_file_path;
    }
    
    /**
     * Create downloadable product
     */
    private function create_downloadable_product($file_path) {
        $product = new WC_Product_Simple();
        $product->set_name('Test Download Product');
        $product->set_regular_price('99.00');
        $product->set_downloadable(true);
        $product->set_virtual(true);
        
        // Add download file
        $downloads = [];
        $downloads[] = [
            'name' => 'Test Plugin',
            'file' => $file_path
        ];
        $product->set_downloads($downloads);
        
        return $product->save();
    }
}

// Initialize test system
if (defined('WP_DEBUG') && WP_DEBUG) {
    new Vireo_Purchase_Flow_Test();
}

// Add AJAX handlers
add_action('wp_ajax_vireo_test_woocommerce_setup', function() {
    $test = new Vireo_Purchase_Flow_Test();
    $test->test_woocommerce_setup();
});

add_action('wp_ajax_vireo_test_product_creation', function() {
    $test = new Vireo_Purchase_Flow_Test();
    $test->test_product_creation();
});

add_action('wp_ajax_vireo_test_license_generation', function() {
    $test = new Vireo_Purchase_Flow_Test();
    $test->test_license_generation();
});

add_action('wp_ajax_vireo_test_download_delivery', function() {
    $test = new Vireo_Purchase_Flow_Test();
    $test->test_download_delivery();
});

?>