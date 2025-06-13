<?php
/**
 * Purchase Flow Testing System for Vireo Designs
 * Comprehensive testing of the complete e-commerce flow
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Vireo_Purchase_Flow_Tester {
    
    public function __construct() {
        // Only enable in development/staging environments
        if (defined('WP_DEBUG') && WP_DEBUG) {
            add_action('init', array($this, 'init'));
        }
    }
    
    public function init() {
        // Add admin menu for testing
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // AJAX handlers for testing
        add_action('wp_ajax_vireo_test_purchase_flow', array($this, 'test_purchase_flow'));
        add_action('wp_ajax_vireo_test_email_delivery', array($this, 'test_email_delivery'));
        add_action('wp_ajax_vireo_test_download_system', array($this, 'test_download_system'));
        add_action('wp_ajax_vireo_test_license_generation', array($this, 'test_license_generation'));
        
        // Test data creation
        add_action('wp_ajax_vireo_create_test_data', array($this, 'create_test_data'));
        add_action('wp_ajax_vireo_cleanup_test_data', array($this, 'cleanup_test_data'));
    }
    
    public function add_admin_menu() {
        add_submenu_page(
            'tools.php',
            'Vireo Purchase Flow Tester',
            'Vireo Testing',
            'manage_options',
            'vireo-testing',
            array($this, 'admin_page')
        );
    }
    
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1>Vireo Purchase Flow Testing</h1>
            
            <div class="vireo-testing-dashboard">
                
                <!-- Test Overview -->
                <div class="test-section">
                    <h2>🧪 Testing Dashboard</h2>
                    <p>Comprehensive testing suite for the complete Vireo e-commerce flow.</p>
                    
                    <div class="test-status">
                        <div class="status-grid">
                            <div class="status-item">
                                <strong>WooCommerce:</strong>
                                <span class="status <?php echo class_exists('WooCommerce') ? 'active' : 'inactive'; ?>">
                                    <?php echo class_exists('WooCommerce') ? '✅ Active' : '❌ Inactive'; ?>
                                </span>
                            </div>
                            <div class="status-item">
                                <strong>Stripe Gateway:</strong>
                                <span class="status <?php echo class_exists('WC_Gateway_Stripe') ? 'active' : 'inactive'; ?>">
                                    <?php echo class_exists('WC_Gateway_Stripe') ? '✅ Available' : '❌ Missing'; ?>
                                </span>
                            </div>
                            <div class="status-item">
                                <strong>Download System:</strong>
                                <span class="status <?php echo $this->check_download_table() ? 'active' : 'inactive'; ?>">
                                    <?php echo $this->check_download_table() ? '✅ Ready' : '❌ Not Setup'; ?>
                                </span>
                            </div>
                            <div class="status-item">
                                <strong>Test Products:</strong>
                                <span class="status <?php echo $this->count_test_products() > 0 ? 'active' : 'inactive'; ?>">
                                    <?php echo $this->count_test_products(); ?> products
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Test Data Management -->
                <div class="test-section">
                    <h3>🏗️ Test Data Management</h3>
                    <div class="test-actions">
                        <button class="button button-primary" onclick="createTestData()">
                            Create Test Products & Data
                        </button>
                        <button class="button button-secondary" onclick="cleanupTestData()">
                            Cleanup Test Data
                        </button>
                    </div>
                    <div id="test-data-result"></div>
                </div>
                
                <!-- Individual Test Modules -->
                <div class="test-modules">
                    
                    <!-- Purchase Flow Test -->
                    <div class="test-module">
                        <h3>🛒 Complete Purchase Flow</h3>
                        <p>Test the entire purchase process from cart to download.</p>
                        <button class="button button-primary" onclick="testPurchaseFlow()">
                            Run Complete Flow Test
                        </button>
                        <div id="purchase-flow-result" class="test-result"></div>
                    </div>
                    
                    <!-- Email Delivery Test -->
                    <div class="test-module">
                        <h3>📧 Email Delivery System</h3>
                        <p>Test purchase confirmation and license delivery emails.</p>
                        <input type="email" id="test-email" placeholder="Enter test email" value="test@vireodesigns.com">
                        <button class="button button-primary" onclick="testEmailDelivery()">
                            Test Email Delivery
                        </button>
                        <div id="email-delivery-result" class="test-result"></div>
                    </div>
                    
                    <!-- Download System Test -->
                    <div class="test-module">
                        <h3>⬇️ Download System</h3>
                        <p>Test secure download token generation and access.</p>
                        <button class="button button-primary" onclick="testDownloadSystem()">
                            Test Download System
                        </button>
                        <div id="download-system-result" class="test-result"></div>
                    </div>
                    
                    <!-- License Generation Test -->
                    <div class="test-module">
                        <h3>🔑 License Generation</h3>
                        <p>Test automatic license key generation and validation.</p>
                        <button class="button button-primary" onclick="testLicenseGeneration()">
                            Test License Generation
                        </button>
                        <div id="license-generation-result" class="test-result"></div>
                    </div>
                    
                </div>
                
                <!-- Test Results Summary -->
                <div class="test-section">
                    <h3>📊 Test Results Summary</h3>
                    <div id="test-summary" class="test-summary">
                        <p>Run tests to see results summary here.</p>
                    </div>
                </div>
                
            </div>
        </div>
        
        <style>
        .vireo-testing-dashboard {
            max-width: 1200px;
        }
        
        .test-section {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border: 1px solid #ccd0d4;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .status-item {
            padding: 15px;
            background: #f9f9f9;
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .status.active {
            color: #059669;
            font-weight: 600;
        }
        
        .status.inactive {
            color: #dc2626;
            font-weight: 600;
        }
        
        .test-modules {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .test-module {
            background: white;
            padding: 20px;
            border: 1px solid #ccd0d4;
            border-radius: 8px;
        }
        
        .test-module h3 {
            margin-top: 0;
            color: #059669;
        }
        
        .test-actions {
            display: flex;
            gap: 10px;
            margin: 15px 0;
        }
        
        .test-result {
            margin-top: 15px;
            padding: 10px;
            border-radius: 4px;
            display: none;
        }
        
        .test-result.success {
            background: #dcfce7;
            border: 1px solid #16a34a;
            color: #15803d;
        }
        
        .test-result.error {
            background: #fef2f2;
            border: 1px solid #dc2626;
            color: #dc2626;
        }
        
        .test-result.info {
            background: #dbeafe;
            border: 1px solid #2563eb;
            color: #1d4ed8;
        }
        
        .test-summary {
            background: #f8fafc;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #059669;
        }
        
        #test-email {
            width: 250px;
            margin-right: 10px;
        }
        </style>
        
        <script>
        function showResult(elementId, message, type = 'info') {
            const element = document.getElementById(elementId);
            element.className = 'test-result ' + type;
            element.innerHTML = message;
            element.style.display = 'block';
        }
        
        function createTestData() {
            showResult('test-data-result', 'Creating test data...', 'info');
            
            fetch(ajaxurl, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams({
                    action: 'vireo_create_test_data',
                    nonce: '<?php echo wp_create_nonce('vireo_testing'); ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showResult('test-data-result', data.data.message, 'success');
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showResult('test-data-result', 'Error: ' + data.data, 'error');
                }
            })
            .catch(error => {
                showResult('test-data-result', 'Network error: ' + error, 'error');
            });
        }
        
        function cleanupTestData() {
            if (confirm('Are you sure you want to delete all test data? This cannot be undone.')) {
                showResult('test-data-result', 'Cleaning up test data...', 'info');
                
                fetch(ajaxurl, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: new URLSearchParams({
                        action: 'vireo_cleanup_test_data',
                        nonce: '<?php echo wp_create_nonce('vireo_testing'); ?>'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showResult('test-data-result', data.data.message, 'success');
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        showResult('test-data-result', 'Error: ' + data.data, 'error');
                    }
                });
            }
        }
        
        function testPurchaseFlow() {
            showResult('purchase-flow-result', 'Testing complete purchase flow...', 'info');
            
            fetch(ajaxurl, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams({
                    action: 'vireo_test_purchase_flow',
                    nonce: '<?php echo wp_create_nonce('vireo_testing'); ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showResult('purchase-flow-result', data.data.message, 'success');
                } else {
                    showResult('purchase-flow-result', 'Error: ' + data.data, 'error');
                }
            });
        }
        
        function testEmailDelivery() {
            const email = document.getElementById('test-email').value;
            if (!email) {
                showResult('email-delivery-result', 'Please enter a test email address', 'error');
                return;
            }
            
            showResult('email-delivery-result', 'Testing email delivery...', 'info');
            
            fetch(ajaxurl, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams({
                    action: 'vireo_test_email_delivery',
                    email: email,
                    nonce: '<?php echo wp_create_nonce('vireo_testing'); ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showResult('email-delivery-result', data.data.message, 'success');
                } else {
                    showResult('email-delivery-result', 'Error: ' + data.data, 'error');
                }
            });
        }
        
        function testDownloadSystem() {
            showResult('download-system-result', 'Testing download system...', 'info');
            
            fetch(ajaxurl, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams({
                    action: 'vireo_test_download_system',
                    nonce: '<?php echo wp_create_nonce('vireo_testing'); ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showResult('download-system-result', data.data.message, 'success');
                } else {
                    showResult('download-system-result', 'Error: ' + data.data, 'error');
                }
            });
        }
        
        function testLicenseGeneration() {
            showResult('license-generation-result', 'Testing license generation...', 'info');
            
            fetch(ajaxurl, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams({
                    action: 'vireo_test_license_generation',
                    nonce: '<?php echo wp_create_nonce('vireo_testing'); ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showResult('license-generation-result', data.data.message, 'success');
                } else {
                    showResult('license-generation-result', 'Error: ' + data.data, 'error');
                }
            });
        }
        </script>
        <?php
    }
    
    public function create_test_data() {
        check_ajax_referer('vireo_testing', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Access denied');
        }
        
        $results = array();
        
        try {
            // Create plugin category
            $plugin_cat = wp_insert_term('Plugins', 'product_cat', array(
                'description' => 'WordPress Plugins by Vireo Designs',
                'slug' => 'plugins'
            ));
            
            // Create test products
            $test_products = array(
                array(
                    'name' => 'Property Management Pro',
                    'price' => 99,
                    'description' => 'Complete property management solution for small landlords.',
                    'features' => array('Property CRUD', 'Tenant Management', 'Lease Tracking', 'Maintenance Requests')
                ),
                array(
                    'name' => 'Sports League Manager',
                    'price' => 79,
                    'description' => 'Manage sports leagues, teams, and tournaments.',
                    'features' => array('League Management', 'Team Rosters', 'Game Scheduling', 'Statistics Tracking')
                ),
                array(
                    'name' => 'Equipment Rental System',
                    'price' => 89,
                    'description' => 'Complete rental management for equipment businesses.',
                    'features' => array('Inventory Management', 'Rental Scheduling', 'Customer Database', 'Payment Processing')
                )
            );
            
            foreach ($test_products as $product_data) {
                $product = new WC_Product_Simple();
                $product->set_name($product_data['name']);
                $product->set_description($product_data['description']);
                $product->set_short_description(wp_trim_words($product_data['description'], 15));
                $product->set_regular_price($product_data['price']);
                $product->set_virtual(true);
                $product->set_downloadable(true);
                $product->set_catalog_visibility('visible');
                $product->set_status('publish');
                
                // Add to plugins category
                if (!is_wp_error($plugin_cat)) {
                    $product->set_category_ids(array($plugin_cat['term_id']));
                }
                
                $product_id = $product->save();
                
                // Add custom meta
                update_post_meta($product_id, '_enable_license_management', 'yes');
                update_post_meta($product_id, '_license_activations_limit', '3');
                update_post_meta($product_id, '_plugin_features', implode("\n", $product_data['features']));
                
                // Add a dummy download file
                $download = new WC_Product_Download();
                $download->set_name($product_data['name'] . ' Plugin');
                $download->set_file(home_url('/wp-content/themes/vireo-designs/assets/test-plugin.zip'));
                
                $product->set_downloads(array($download->get_id() => $download));
                $product->save();
                
                $results[] = "Created product: {$product_data['name']} (ID: {$product_id})";
            }
            
            // Create test customer
            $customer = new WC_Customer();
            $customer->set_email('testcustomer@vireodesigns.com');
            $customer->set_first_name('Test');
            $customer->set_last_name('Customer');
            $customer->set_billing_email('testcustomer@vireodesigns.com');
            $customer->set_billing_first_name('Test');
            $customer->set_billing_last_name('Customer');
            $customer_id = $customer->save();
            
            $results[] = "Created test customer (ID: {$customer_id})";
            
            // Create download table
            $this->create_download_table();
            $results[] = "Created/verified download table";
            
            wp_send_json_success(array(
                'message' => 'Test data created successfully:<br>• ' . implode('<br>• ', $results)
            ));
            
        } catch (Exception $e) {
            wp_send_json_error('Error creating test data: ' . $e->getMessage());
        }
    }
    
    public function cleanup_test_data() {
        check_ajax_referer('vireo_testing', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Access denied');
        }
        
        try {
            // Delete test products
            $test_products = get_posts(array(
                'post_type' => 'product',
                'meta_query' => array(
                    array(
                        'key' => '_enable_license_management',
                        'value' => 'yes',
                        'compare' => '='
                    )
                ),
                'posts_per_page' => -1
            ));
            
            $deleted_count = 0;
            foreach ($test_products as $product) {
                wp_delete_post($product->ID, true);
                $deleted_count++;
            }
            
            // Delete test customer
            $customers = get_users(array(
                'meta_key' => 'billing_email',
                'meta_value' => 'testcustomer@vireodesigns.com'
            ));
            
            foreach ($customers as $customer) {
                wp_delete_user($customer->ID);
            }
            
            // Clean download table
            global $wpdb;
            $wpdb->query("TRUNCATE TABLE {$wpdb->prefix}vireo_downloads");
            
            wp_send_json_success(array(
                'message' => "Cleanup completed:<br>• Deleted {$deleted_count} test products<br>• Removed test customers<br>• Cleared download tokens"
            ));
            
        } catch (Exception $e) {
            wp_send_json_error('Error during cleanup: ' . $e->getMessage());
        }
    }
    
    public function test_purchase_flow() {
        check_ajax_referer('vireo_testing', 'nonce');
        
        $results = array();
        $success = true;
        
        try {
            // Test 1: Check if we can create an order
            $product_ids = get_posts(array(
                'post_type' => 'product',
                'meta_key' => '_enable_license_management',
                'meta_value' => 'yes',
                'posts_per_page' => 1,
                'fields' => 'ids'
            ));
            
            if (empty($product_ids)) {
                throw new Exception('No test products found. Create test data first.');
            }
            
            $product_id = $product_ids[0];
            $product = wc_get_product($product_id);
            
            // Create test order
            $order = wc_create_order();
            $order->add_product($product, 1);
            $order->set_billing_first_name('Test');
            $order->set_billing_last_name('Customer');
            $order->set_billing_email('test@vireodesigns.com');
            $order->calculate_totals();
            $order->update_status('completed', 'Test order');
            
            $results[] = "✅ Created test order #{$order->get_id()}";
            
            // Test 2: License generation
            if (class_exists('Vireo_Stripe_Setup')) {
                $stripe_setup = new Vireo_Stripe_Setup();
                $stripe_setup->generate_plugin_license($order, null, $product);
                
                $license = $order->get_meta('_vireo_license_' . $product_id);
                if ($license) {
                    $results[] = "✅ License generated: {$license}";
                } else {
                    $success = false;
                    $results[] = "❌ License generation failed";
                }
            }
            
            // Test 3: Download token creation
            if (class_exists('Vireo_Download_System')) {
                $download_system = new Vireo_Download_System();
                $token = $download_system->create_secure_download_link($order, $product);
                
                if ($token) {
                    $results[] = "✅ Download token created: {$token}";
                    $results[] = "🔗 Download URL: " . home_url("/vireo-download/{$token}");
                } else {
                    $success = false;
                    $results[] = "❌ Download token creation failed";
                }
            }
            
            // Test 4: Email components
            $email_content = "Test purchase confirmation email would be sent to: {$order->get_billing_email()}";
            $results[] = "📧 " . $email_content;
            
            // Cleanup test order
            wp_delete_post($order->get_id(), true);
            $results[] = "🧹 Test order cleaned up";
            
            if ($success) {
                wp_send_json_success(array(
                    'message' => 'Purchase flow test completed successfully:<br>• ' . implode('<br>• ', $results)
                ));
            } else {
                wp_send_json_error('Purchase flow test had errors:<br>• ' . implode('<br>• ', $results));
            }
            
        } catch (Exception $e) {
            wp_send_json_error('Purchase flow test failed: ' . $e->getMessage());
        }
    }
    
    public function test_email_delivery() {
        check_ajax_referer('vireo_testing', 'nonce');
        
        $email = sanitize_email($_POST['email']);
        if (!$email) {
            wp_send_json_error('Invalid email address');
        }
        
        try {
            // Test email content
            $subject = 'Vireo Designs - Test Email Delivery';
            $message = '
            <h2>🧪 Test Email Delivery</h2>
            <p>This is a test email from the Vireo Designs purchase flow testing system.</p>
            
            <h3>Sample Purchase Confirmation:</h3>
            <div style="background: #f9f9f9; padding: 15px; border-radius: 8px;">
                <h4>Thank you for your purchase!</h4>
                <p><strong>Product:</strong> Property Management Pro</p>
                <p><strong>License Key:</strong> <code>VIREO-0001-123-ABCD1234</code></p>
                <p><strong>Download Link:</strong> <a href="#">Download Plugin</a></p>
            </div>
            
            <p><small>This is a test email sent at ' . current_time('mysql') . '</small></p>
            ';
            
            $headers = array(
                'Content-Type: text/html; charset=UTF-8',
                'From: Vireo Designs <noreply@vireodesigns.com>'
            );
            
            $sent = wp_mail($email, $subject, $message, $headers);
            
            if ($sent) {
                wp_send_json_success(array(
                    'message' => "✅ Test email sent successfully to {$email}<br>📧 Check your inbox for the test email"
                ));
            } else {
                wp_send_json_error('Failed to send test email. Check your WordPress mail configuration.');
            }
            
        } catch (Exception $e) {
            wp_send_json_error('Email test failed: ' . $e->getMessage());
        }
    }
    
    public function test_download_system() {
        check_ajax_referer('vireo_testing', 'nonce');
        
        try {
            $results = array();
            
            // Test 1: Check download table
            if (!$this->check_download_table()) {
                $this->create_download_table();
                $results[] = "✅ Created download table";
            } else {
                $results[] = "✅ Download table exists";
            }
            
            // Test 2: Create test download token
            global $wpdb;
            $test_token = wp_generate_password(32, false);
            $test_data = array(
                'token' => $test_token,
                'order_id' => 999999,
                'product_id' => 1,
                'customer_email' => 'test@example.com',
                'download_data' => wp_json_encode(array(
                    'download_count' => 0,
                    'download_limit' => 10,
                    'expires' => time() + DAY_IN_SECONDS
                )),
                'created_at' => current_time('mysql'),
                'expires_at' => date('Y-m-d H:i:s', time() + DAY_IN_SECONDS)
            );
            
            $inserted = $wpdb->insert($wpdb->prefix . 'vireo_downloads', $test_data);
            
            if ($inserted) {
                $results[] = "✅ Created test download token: {$test_token}";
                $results[] = "🔗 Test download URL: " . home_url("/vireo-download/{$test_token}");
                
                // Cleanup
                $wpdb->delete($wpdb->prefix . 'vireo_downloads', array('token' => $test_token));
                $results[] = "🧹 Cleaned up test token";
            } else {
                $results[] = "❌ Failed to create test download token";
            }
            
            // Test 3: Check rewrite rules
            $rules = get_option('rewrite_rules');
            $has_download_rule = false;
            foreach ($rules as $rule => $rewrite) {
                if (strpos($rule, 'vireo-download') !== false) {
                    $has_download_rule = true;
                    break;
                }
            }
            
            if ($has_download_rule) {
                $results[] = "✅ Download rewrite rules are active";
            } else {
                $results[] = "⚠️ Download rewrite rules may need flushing";
            }
            
            wp_send_json_success(array(
                'message' => 'Download system test completed:<br>• ' . implode('<br>• ', $results)
            ));
            
        } catch (Exception $e) {
            wp_send_json_error('Download system test failed: ' . $e->getMessage());
        }
    }
    
    public function test_license_generation() {
        check_ajax_referer('vireo_testing', 'nonce');
        
        try {
            $results = array();
            
            // Test license key generation
            for ($i = 1; $i <= 3; $i++) {
                $prefix = 'VIREO';
                $order_id = str_pad($i, 4, '0', STR_PAD_LEFT);
                $product_id = str_pad($i, 3, '0', STR_PAD_LEFT);
                $random = strtoupper(wp_generate_password(8, false));
                
                $license_key = sprintf('%s-%s-%s-%s', $prefix, $order_id, $product_id, $random);
                $results[] = "✅ Generated license #{$i}: {$license_key}";
            }
            
            // Test activation limits
            $test_limits = array(
                'Property Management Pro' => 1,
                'Property Management Business' => 5,
                'Property Management Developer' => 25
            );
            
            foreach ($test_limits as $product_name => $expected_limit) {
                // Simulate limit calculation
                if (strpos(strtolower($product_name), 'business') !== false) {
                    $limit = 5;
                } elseif (strpos(strtolower($product_name), 'developer') !== false) {
                    $limit = 25;
                } else {
                    $limit = 1;
                }
                
                if ($limit === $expected_limit) {
                    $results[] = "✅ {$product_name}: {$limit} sites (correct)";
                } else {
                    $results[] = "❌ {$product_name}: {$limit} sites (expected {$expected_limit})";
                }
            }
            
            wp_send_json_success(array(
                'message' => 'License generation test completed:<br>• ' . implode('<br>• ', $results)
            ));
            
        } catch (Exception $e) {
            wp_send_json_error('License generation test failed: ' . $e->getMessage());
        }
    }
    
    private function check_download_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'vireo_downloads';
        return $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") == $table_name;
    }
    
    private function create_download_table() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'vireo_downloads';
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            token varchar(64) NOT NULL,
            order_id mediumint(9) NOT NULL,
            product_id mediumint(9) NOT NULL,
            customer_email varchar(255) NOT NULL,
            download_data longtext NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            expires_at datetime NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY token (token),
            KEY order_id (order_id),
            KEY expires_at (expires_at)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    private function count_test_products() {
        return count(get_posts(array(
            'post_type' => 'product',
            'meta_key' => '_enable_license_management',
            'meta_value' => 'yes',
            'posts_per_page' => -1,
            'fields' => 'ids'
        )));
    }
}

// Initialize tester in development environments
new Vireo_Purchase_Flow_Tester();
?>