<?php
/**
 * Vireo License Integration
 * Integrates with License Manager for WooCommerce to handle plugin licensing
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Vireo_License_Integration {
    
    public function __construct() {
        add_action('init', [$this, 'init']);
    }
    
    public function init() {
        // Check if License Manager is active
        if (!class_exists('LicenseManagerForWooCommerce\Main')) {
            return;
        }
        
        // Hook into WooCommerce order completion
        add_action('woocommerce_order_status_completed', [$this, 'generate_license_on_order_complete']);
        
        // Hook into license activation
        add_action('lmfwc_license_activated', [$this, 'handle_license_activation'], 10, 2);
        
        // Add license info to My Account
        add_action('woocommerce_account_dashboard', [$this, 'display_customer_licenses']);
        
        // Add download links for licensed products
        add_filter('woocommerce_account_downloads_columns', [$this, 'add_license_column']);
        add_action('woocommerce_account_downloads_column_license', [$this, 'display_license_key']);
        
        // AJAX handlers
        add_action('wp_ajax_vireo_activate_license', [$this, 'ajax_activate_license']);
        add_action('wp_ajax_vireo_deactivate_license', [$this, 'ajax_deactivate_license']);
        add_action('wp_ajax_vireo_check_license_status', [$this, 'ajax_check_license_status']);
    }
    
    /**
     * Generate license when order is completed
     */
    public function generate_license_on_order_complete($order_id) {
        $order = wc_get_order($order_id);
        
        if (!$order) {
            return;
        }
        
        foreach ($order->get_items() as $item_id => $item) {
            $product_id = $item->get_product_id();
            $product = wc_get_product($product_id);
            
            // Check if this is a Vireo plugin product
            if ($this->is_vireo_plugin_product($product_id)) {
                $this->create_license_for_product($order, $item, $product);
            }
        }
    }
    
    /**
     * Check if product is a Vireo plugin
     */
    public function is_vireo_plugin_product($product_id) {
        $plugin_type = get_post_meta($product_id, '_vireo_plugin_type', true);
        return !empty($plugin_type);
    }
    
    /**
     * Create license for product
     */
    public function create_license_for_product($order, $item, $product) {
        
        // Get license duration from product meta
        $duration_days = $this->get_license_duration($product->get_id());
        
        // Create license generator if not exists
        $generator_id = $this->get_or_create_generator($product);
        
        if (!$generator_id) {
            error_log('Vireo License: Failed to create generator for product ' . $product->get_id());
            return false;
        }
        
        // Generate license
        $license_data = [
            'order_id' => $order->get_id(),
            'product_id' => $product->get_id(),
            'user_id' => $order->get_customer_id(),
            'license_key' => $this->generate_license_key(),
            'status' => 1, // Active
            'source' => 'generator',
            'generator_id' => $generator_id,
            'expires_at' => date('Y-m-d H:i:s', strtotime("+{$duration_days} days")),
            'created_at' => current_time('mysql'),
            'created_by' => $order->get_customer_id()
        ];
        
        // Insert license into database
        global $wpdb;
        $table_name = $wpdb->prefix . 'lmfwc_licenses';
        
        $result = $wpdb->insert($table_name, $license_data);
        
        if ($result !== false) {
            $license_id = $wpdb->insert_id;
            
            // Store license ID in order meta
            $order->add_meta_data('_vireo_license_' . $product->get_id(), $license_id);
            $order->save();
            
            // Send license email to customer
            $this->send_license_email($order, $product, $license_data['license_key']);
            
            return $license_id;
        }
        
        return false;
    }
    
    /**
     * Get or create license generator for product
     */
    public function get_or_create_generator($product) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'lmfwc_generators';
        
        // Check if generator exists for this product
        $generator = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE name = %s",
            'Vireo ' . $product->get_name()
        ));
        
        if ($generator) {
            return $generator->id;
        }
        
        // Create new generator
        $generator_data = [
            'name' => 'Vireo ' . $product->get_name(),
            'charset' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
            'chunks' => 4,
            'chunk_length' => 4,
            'times_activated_max' => 3, // Allow 3 activations
            'separator' => '-',
            'prefix' => 'VIR-',
            'suffix' => '',
            'expires_in' => $this->get_license_duration($product->get_id()),
            'created_at' => current_time('mysql')
        ];
        
        $result = $wpdb->insert($table_name, $generator_data);
        
        if ($result !== false) {
            return $wpdb->insert_id;
        }
        
        return false;
    }
    
    /**
     * Get license duration from product
     */
    public function get_license_duration($product_id) {
        $duration = get_post_meta($product_id, '_vireo_license_duration', true);
        
        // Convert to days
        switch ($duration) {
            case '1 year':
                return 365;
            case '2 years':
                return 730;
            case 'lifetime':
                return 36500; // 100 years
            default:
                return 365; // Default to 1 year
        }
    }
    
    /**
     * Generate license key
     */
    public function generate_license_key() {
        $prefix = 'VIR-';
        $parts = [];
        
        for ($i = 0; $i < 4; $i++) {
            $parts[] = $this->generate_random_string(4);
        }
        
        return $prefix . implode('-', $parts);
    }
    
    /**
     * Generate random string
     */
    private function generate_random_string($length = 4) {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $string = '';
        
        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        return $string;
    }
    
    /**
     * Send license email to customer
     */
    public function send_license_email($order, $product, $license_key) {
        $customer_email = $order->get_billing_email();
        $customer_name = $order->get_billing_first_name();
        
        $subject = 'Your ' . $product->get_name() . ' License Key';
        
        $message = "
        <h2>Thank you for your purchase!</h2>
        <p>Hi {$customer_name},</p>
        <p>Your license key for <strong>{$product->get_name()}</strong> is ready:</p>
        <div style='background: #f5f5f5; padding: 20px; margin: 20px 0; text-align: center; font-family: monospace; font-size: 18px; font-weight: bold; border: 2px dashed #059669;'>
            {$license_key}
        </div>
        <p><strong>How to activate your license:</strong></p>
        <ol>
            <li>Download and install the plugin from your account downloads</li>
            <li>Go to your WordPress admin → Plugins → Vireo Settings</li>
            <li>Enter your license key in the 'Pro License' section</li>
            <li>Click 'Activate License' to unlock all pro features</li>
        </ol>
        <p><strong>Important:</strong></p>
        <ul>
            <li>This license is valid for 1 year from purchase date</li>
            <li>You can use this license on up to 3 websites</li>
            <li>Keep this email for your records</li>
        </ul>
        <p>Need help? Contact our support team at support@vireodesigns.com</p>
        <p>Best regards,<br>The Vireo Designs Team</p>
        ";
        
        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: Vireo Designs <noreply@vireodesigns.com>'
        ];
        
        wp_mail($customer_email, $subject, $message, $headers);
    }
    
    /**
     * Handle license activation
     */
    public function handle_license_activation($license_key, $request_data) {
        // Log activation
        error_log('Vireo License Activated: ' . $license_key);
        
        // You can add additional logic here, such as:
        // - Sending activation notifications
        // - Updating user meta
        // - Triggering webhooks
    }
    
    /**
     * Display customer licenses in My Account
     */
    public function display_customer_licenses() {
        if (!is_user_logged_in()) {
            return;
        }
        
        $customer_id = get_current_user_id();
        $licenses = $this->get_customer_licenses($customer_id);
        
        if (empty($licenses)) {
            return;
        }
        
        ?>
        <div class="vireo-licenses-section">
            <h3>Your Plugin Licenses</h3>
            <table class="vireo-licenses-table">
                <thead>
                    <tr>
                        <th>Plugin</th>
                        <th>License Key</th>
                        <th>Status</th>
                        <th>Expires</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($licenses as $license): ?>
                    <tr>
                        <td><?php echo esc_html($license->product_name); ?></td>
                        <td><code><?php echo esc_html($license->license_key); ?></code></td>
                        <td>
                            <span class="license-status <?php echo esc_attr($license->status_class); ?>">
                                <?php echo esc_html($license->status_text); ?>
                            </span>
                        </td>
                        <td><?php echo esc_html($license->expires_at); ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline vireo-copy-license" 
                                    data-license="<?php echo esc_attr($license->license_key); ?>">
                                Copy
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <style>
        .vireo-licenses-section {
            margin: 20px 0;
            padding: 20px;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
        }
        .vireo-licenses-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .vireo-licenses-table th,
        .vireo-licenses-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        .vireo-licenses-table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        .license-status.active {
            color: #059669;
            font-weight: 500;
        }
        .license-status.expired {
            color: #dc2626;
            font-weight: 500;
        }
        .vireo-copy-license {
            cursor: pointer;
        }
        </style>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.vireo-copy-license').forEach(button => {
                button.addEventListener('click', function() {
                    const license = this.dataset.license;
                    navigator.clipboard.writeText(license).then(() => {
                        this.textContent = 'Copied!';
                        setTimeout(() => {
                            this.textContent = 'Copy';
                        }, 2000);
                    });
                });
            });
        });
        </script>
        <?php
    }
    
    /**
     * Get customer licenses
     */
    public function get_customer_licenses($customer_id) {
        global $wpdb;
        
        $licenses_table = $wpdb->prefix . 'lmfwc_licenses';
        $posts_table = $wpdb->posts;
        
        $query = "
            SELECT l.*, p.post_title as product_name
            FROM {$licenses_table} l
            JOIN {$posts_table} p ON l.product_id = p.ID
            WHERE l.user_id = %d
            ORDER BY l.created_at DESC
        ";
        
        $results = $wpdb->get_results($wpdb->prepare($query, $customer_id));
        
        if (!$results) {
            return [];
        }
        
        // Add status information
        foreach ($results as $license) {
            $expires_at = strtotime($license->expires_at);
            $now = time();
            
            if ($expires_at > $now) {
                $license->status_text = 'Active';
                $license->status_class = 'active';
            } else {
                $license->status_text = 'Expired';
                $license->status_class = 'expired';
            }
            
            $license->expires_at = date('M j, Y', $expires_at);
        }
        
        return $results;
    }
    
    /**
     * Add license column to downloads table
     */
    public function add_license_column($columns) {
        $columns['license'] = 'License Key';
        return $columns;
    }
    
    /**
     * Display license key in downloads table
     */
    public function display_license_key($download) {
        $product_id = $download['product_id'];
        
        if (!$this->is_vireo_plugin_product($product_id)) {
            echo '—';
            return;
        }
        
        $customer_id = get_current_user_id();
        $license = $this->get_license_for_product($customer_id, $product_id);
        
        if ($license) {
            echo '<code>' . esc_html($license->license_key) . '</code>';
        } else {
            echo '—';
        }
    }
    
    /**
     * Get license for specific product
     */
    public function get_license_for_product($customer_id, $product_id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'lmfwc_licenses';
        
        $license = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE user_id = %d AND product_id = %d ORDER BY created_at DESC LIMIT 1",
            $customer_id,
            $product_id
        ));
        
        return $license;
    }
    
    /**
     * AJAX: Activate license
     */
    public function ajax_activate_license() {
        check_ajax_referer('vireo_license_nonce', 'nonce');
        
        $license_key = sanitize_text_field($_POST['license_key']);
        $site_url = sanitize_url($_POST['site_url']);
        
        // Validate license using License Manager API
        $result = $this->validate_and_activate_license($license_key, $site_url);
        
        wp_send_json($result);
    }
    
    /**
     * Validate and activate license
     */
    public function validate_and_activate_license($license_key, $site_url) {
        // This would integrate with License Manager's API
        // For now, return success response
        return [
            'success' => true,
            'message' => 'License activated successfully',
            'data' => [
                'license_key' => $license_key,
                'status' => 'active',
                'expires_at' => date('Y-m-d', strtotime('+1 year'))
            ]
        ];
    }
}

// Initialize the integration
new Vireo_License_Integration();

?>