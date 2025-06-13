<?php
/**
 * Vireo Designs License Manager
 * 
 * Custom license management system for Pro WordPress plugins
 * Integrates with WooCommerce for automated license delivery
 */

if (!defined('ABSPATH')) {
    exit;
}

class Vireo_License_Manager {
    
    private $table_name;
    
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'vireo_licenses';
        
        add_action('init', array($this, 'init'));
        add_action('woocommerce_order_status_completed', array($this, 'generate_license_on_order_complete'));
        add_action('woocommerce_order_status_processing', array($this, 'generate_license_on_order_complete'));
        add_action('wp_ajax_validate_license', array($this, 'ajax_validate_license'));
        add_action('wp_ajax_nopriv_validate_license', array($this, 'ajax_validate_license'));
        add_action('rest_api_init', array($this, 'register_rest_routes'));
        
        // Admin hooks
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('add_meta_boxes', array($this, 'add_order_meta_box'));
        
        // Customer account hooks
        add_action('woocommerce_account_navigation', array($this, 'add_license_menu_item'));
        add_action('woocommerce_account_licenses_endpoint', array($this, 'licenses_endpoint_content'));
        add_filter('woocommerce_account_menu_items', array($this, 'add_license_account_menu'));
        add_filter('woocommerce_get_query_vars', array($this, 'add_license_query_var'));
    }
    
    /**
     * Initialize license system
     */
    public function init() {
        $this->create_license_table();
        $this->add_rewrite_endpoints();
    }
    
    /**
     * Create license table
     */
    private function create_license_table() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
            id int(11) NOT NULL AUTO_INCREMENT,
            license_key varchar(255) NOT NULL UNIQUE,
            product_id int(11) NOT NULL,
            order_id int(11) NOT NULL,
            customer_email varchar(255) NOT NULL,
            status enum('active','inactive','expired','revoked') DEFAULT 'active',
            max_sites int(11) DEFAULT 1,
            activated_sites text,
            activation_count int(11) DEFAULT 0,
            expires_at datetime DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY product_id (product_id),
            KEY order_id (order_id),
            KEY customer_email (customer_email),
            KEY license_key (license_key)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Add rewrite endpoints for customer account
     */
    private function add_rewrite_endpoints() {
        add_rewrite_endpoint('licenses', EP_ROOT | EP_PAGES);
    }
    
    /**
     * Generate license when order is completed
     */
    public function generate_license_on_order_complete($order_id) {
        $order = wc_get_order($order_id);
        
        if (!$order) {
            return;
        }
        
        foreach ($order->get_items() as $item) {
            $product = $item->get_product();
            
            if ($product && $this->is_licensable_product($product)) {
                $existing_license = $this->get_license_by_order_and_product($order_id, $product->get_id());
                
                if (!$existing_license) {
                    $this->create_license($order, $product, $item);
                }
            }
        }
    }
    
    /**
     * Check if product requires a license
     */
    private function is_licensable_product($product) {
        // Check if product has 'pro' in name or specific meta
        $product_name = strtolower($product->get_name());
        $is_pro = strpos($product_name, 'pro') !== false;
        
        // Check custom meta field
        $requires_license = get_post_meta($product->get_id(), '_requires_license', true);
        
        return $is_pro || ($requires_license === 'yes');
    }
    
    /**
     * Create license for product
     */
    private function create_license($order, $product, $item) {
        global $wpdb;
        
        $license_key = $this->generate_license_key();
        $customer_email = $order->get_billing_email();
        
        // Determine license settings based on product
        $max_sites = $this->get_product_max_sites($product);
        $expires_at = $this->get_product_expiry_date($product);
        
        $result = $wpdb->insert(
            $this->table_name,
            array(
                'license_key' => $license_key,
                'product_id' => $product->get_id(),
                'order_id' => $order->get_id(),
                'customer_email' => $customer_email,
                'status' => 'active',
                'max_sites' => $max_sites,
                'activated_sites' => '',
                'expires_at' => $expires_at
            ),
            array('%s', '%d', '%d', '%s', '%s', '%d', '%s', '%s')
        );
        
        if ($result) {
            // Add order note
            $order->add_order_note(
                sprintf('License generated: %s for %s', $license_key, $product->get_name())
            );
            
            // Send email with license
            $this->send_license_email($order, $product, $license_key);
            
            // Log license creation
            error_log("License created: $license_key for product {$product->get_id()} order {$order->get_id()}");
        }
    }
    
    /**
     * Generate unique license key
     */
    private function generate_license_key() {
        $prefix = 'VD';
        $segments = array(
            strtoupper(wp_generate_password(4, false)),
            strtoupper(wp_generate_password(4, false)),
            strtoupper(wp_generate_password(4, false)),
            strtoupper(wp_generate_password(4, false))
        );
        
        return $prefix . '-' . implode('-', $segments);
    }
    
    /**
     * Get max sites for product
     */
    private function get_product_max_sites($product) {
        $max_sites = get_post_meta($product->get_id(), '_license_max_sites', true);
        return $max_sites ? intval($max_sites) : 1;
    }
    
    /**
     * Get expiry date for product
     */
    private function get_product_expiry_date($product) {
        $expiry_period = get_post_meta($product->get_id(), '_license_expiry_period', true);
        
        if ($expiry_period) {
            return date('Y-m-d H:i:s', strtotime($expiry_period));
        }
        
        // Default to 1 year
        return date('Y-m-d H:i:s', strtotime('+1 year'));
    }
    
    /**
     * Send license email to customer
     */
    private function send_license_email($order, $product, $license_key) {
        $customer_email = $order->get_billing_email();
        $customer_name = $order->get_billing_first_name();
        
        $subject = sprintf('Your %s License Key', $product->get_name());
        
        $message = sprintf(
            "Hi %s,\n\n" .
            "Thank you for purchasing %s!\n\n" .
            "Your license key is: %s\n\n" .
            "License Details:\n" .
            "- Product: %s\n" .
            "- Max Sites: %d\n" .
            "- Valid Until: %s\n\n" .
            "To activate your license:\n" .
            "1. Install the plugin on your WordPress site\n" .
            "2. Go to the plugin settings\n" .
            "3. Enter your license key: %s\n" .
            "4. Click 'Activate License'\n\n" .
            "You can manage your licenses at: %s\n\n" .
            "Need help? Contact us at: %s\n\n" .
            "Best regards,\n" .
            "The Vireo Designs Team",
            $customer_name,
            $product->get_name(),
            $license_key,
            $product->get_name(),
            $this->get_product_max_sites($product),
            date('F j, Y', strtotime($this->get_product_expiry_date($product))),
            $license_key,
            wc_get_account_endpoint_url('licenses'),
            get_option('admin_email')
        );
        
        $headers = array('Content-Type: text/plain; charset=UTF-8');
        
        wp_mail($customer_email, $subject, $message, $headers);
    }
    
    /**
     * Validate license via AJAX
     */
    public function ajax_validate_license() {
        $license_key = sanitize_text_field($_POST['license_key'] ?? '');
        $site_url = sanitize_url($_POST['site_url'] ?? '');
        
        if (empty($license_key) || empty($site_url)) {
            wp_send_json_error('Missing license key or site URL.');
        }
        
        $validation = $this->validate_license($license_key, $site_url);
        
        if ($validation['valid']) {
            wp_send_json_success($validation);
        } else {
            wp_send_json_error($validation['message']);
        }
    }
    
    /**
     * Validate license
     */
    public function validate_license($license_key, $site_url = '') {
        global $wpdb;
        
        $license = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$this->table_name} WHERE license_key = %s",
                $license_key
            )
        );
        
        if (!$license) {
            return array(
                'valid' => false,
                'message' => 'Invalid license key.',
                'license_key' => $license_key
            );
        }
        
        // Check status
        if ($license->status !== 'active') {
            return array(
                'valid' => false,
                'message' => 'License is not active.',
                'status' => $license->status
            );
        }
        
        // Check expiry
        if ($license->expires_at && strtotime($license->expires_at) < time()) {
            return array(
                'valid' => false,
                'message' => 'License has expired.',
                'expires_at' => $license->expires_at
            );
        }
        
        // Check site activation if URL provided
        if ($site_url) {
            $activated_sites = json_decode($license->activated_sites, true) ?: array();
            $site_url = trailingslashit(strtolower($site_url));
            
            if (!in_array($site_url, $activated_sites)) {
                // Check if we can activate new site
                if (count($activated_sites) >= $license->max_sites) {
                    return array(
                        'valid' => false,
                        'message' => 'Maximum number of sites activated.',
                        'max_sites' => $license->max_sites,
                        'activated_sites' => count($activated_sites)
                    );
                }
                
                // Activate new site
                $activated_sites[] = $site_url;
                $wpdb->update(
                    $this->table_name,
                    array(
                        'activated_sites' => wp_json_encode($activated_sites),
                        'activation_count' => count($activated_sites)
                    ),
                    array('id' => $license->id),
                    array('%s', '%d'),
                    array('%d')
                );
            }
        }
        
        return array(
            'valid' => true,
            'message' => 'License is valid.',
            'license_key' => $license_key,
            'product_id' => $license->product_id,
            'max_sites' => $license->max_sites,
            'activated_sites' => $license->activation_count,
            'expires_at' => $license->expires_at
        );
    }
    
    /**
     * Register REST API routes
     */
    public function register_rest_routes() {
        register_rest_route('vireo/v1', '/validate-license', array(
            'methods' => 'POST',
            'callback' => array($this, 'rest_validate_license'),
            'permission_callback' => '__return_true'
        ));
    }
    
    /**
     * REST API license validation
     */
    public function rest_validate_license($request) {
        $license_key = sanitize_text_field($request->get_param('license_key'));
        $site_url = sanitize_url($request->get_param('site_url'));
        
        if (empty($license_key)) {
            return new WP_Error('missing_license', 'License key is required.', array('status' => 400));
        }
        
        $validation = $this->validate_license($license_key, $site_url);
        
        return rest_ensure_response($validation);
    }
    
    /**
     * Get license by order and product
     */
    private function get_license_by_order_and_product($order_id, $product_id) {
        global $wpdb;
        
        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$this->table_name} WHERE order_id = %d AND product_id = %d",
                $order_id,
                $product_id
            )
        );
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_submenu_page(
            'woocommerce',
            'License Manager',
            'Licenses',
            'manage_options',
            'vireo-licenses',
            array($this, 'admin_page')
        );
    }
    
    /**
     * Admin page
     */
    public function admin_page() {
        global $wpdb;
        
        // Handle actions
        if (isset($_POST['action']) && $_POST['action'] === 'revoke_license') {
            $license_id = intval($_POST['license_id']);
            $wpdb->update(
                $this->table_name,
                array('status' => 'revoked'),
                array('id' => $license_id),
                array('%s'),
                array('%d')
            );
            echo '<div class="notice notice-success"><p>License revoked successfully.</p></div>';
        }
        
        // Get licenses
        $licenses = $wpdb->get_results(
            "SELECT l.*, p.post_title as product_name 
             FROM {$this->table_name} l 
             LEFT JOIN {$wpdb->posts} p ON l.product_id = p.ID 
             ORDER BY l.created_at DESC 
             LIMIT 50"
        );
        
        ?>
        <div class="wrap">
            <h1>License Manager</h1>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>License Key</th>
                        <th>Product</th>
                        <th>Customer</th>
                        <th>Status</th>
                        <th>Sites</th>
                        <th>Expires</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($licenses as $license) : ?>
                        <tr>
                            <td><code><?php echo esc_html($license->license_key); ?></code></td>
                            <td><?php echo esc_html($license->product_name); ?></td>
                            <td><?php echo esc_html($license->customer_email); ?></td>
                            <td>
                                <span class="license-status status-<?php echo esc_attr($license->status); ?>">
                                    <?php echo esc_html(ucfirst($license->status)); ?>
                                </span>
                            </td>
                            <td><?php echo esc_html($license->activation_count . '/' . $license->max_sites); ?></td>
                            <td><?php echo $license->expires_at ? esc_html(date('M j, Y', strtotime($license->expires_at))) : 'Never'; ?></td>
                            <td>
                                <?php if ($license->status === 'active') : ?>
                                    <form method="post" style="display: inline;">
                                        <input type="hidden" name="action" value="revoke_license">
                                        <input type="hidden" name="license_id" value="<?php echo esc_attr($license->id); ?>">
                                        <input type="submit" class="button button-small" value="Revoke" 
                                               onclick="return confirm('Are you sure you want to revoke this license?')">
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <style>
            .license-status {
                padding: 2px 8px;
                border-radius: 3px;
                font-size: 11px;
                font-weight: bold;
                text-transform: uppercase;
            }
            .status-active { background: #4CAF50; color: white; }
            .status-inactive { background: #FFC107; color: black; }
            .status-expired { background: #FF9800; color: white; }
            .status-revoked { background: #F44336; color: white; }
            </style>
        </div>
        <?php
    }
    
    /**
     * Add order meta box
     */
    public function add_order_meta_box() {
        add_meta_box(
            'vireo-order-licenses',
            'Licenses',
            array($this, 'order_licenses_meta_box'),
            'shop_order'
        );
    }
    
    /**
     * Order licenses meta box
     */
    public function order_licenses_meta_box($post) {
        global $wpdb;
        
        $order_id = $post->ID;
        $licenses = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$this->table_name} WHERE order_id = %d",
                $order_id
            )
        );
        
        if (empty($licenses)) {
            echo '<p>No licenses generated for this order.</p>';
            return;
        }
        
        echo '<table class="widefat">';
        echo '<thead><tr><th>License Key</th><th>Product ID</th><th>Status</th><th>Sites</th></tr></thead>';
        echo '<tbody>';
        
        foreach ($licenses as $license) {
            echo '<tr>';
            echo '<td><code>' . esc_html($license->license_key) . '</code></td>';
            echo '<td>' . esc_html($license->product_id) . '</td>';
            echo '<td>' . esc_html(ucfirst($license->status)) . '</td>';
            echo '<td>' . esc_html($license->activation_count . '/' . $license->max_sites) . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody></table>';
    }
    
    /**
     * Add license menu to customer account
     */
    public function add_license_account_menu($items) {
        $items['licenses'] = 'Licenses';
        return $items;
    }
    
    /**
     * Add license query var
     */
    public function add_license_query_var($vars) {
        $vars['licenses'] = 'licenses';
        return $vars;
    }
    
    /**
     * License endpoint content
     */
    public function licenses_endpoint_content() {
        global $wpdb;
        
        $customer_email = wp_get_current_user()->user_email;
        
        $licenses = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT l.*, p.post_title as product_name 
                 FROM {$this->table_name} l 
                 LEFT JOIN {$wpdb->posts} p ON l.product_id = p.ID 
                 WHERE l.customer_email = %s 
                 ORDER BY l.created_at DESC",
                $customer_email
            )
        );
        
        ?>
        <h2>Your Licenses</h2>
        
        <?php if (empty($licenses)) : ?>
            <p>You don't have any licenses yet. <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">Browse our products</a> to get started.</p>
        <?php else : ?>
            <table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>License Key</th>
                        <th>Status</th>
                        <th>Sites Used</th>
                        <th>Expires</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($licenses as $license) : ?>
                        <tr>
                            <td><?php echo esc_html($license->product_name); ?></td>
                            <td>
                                <code style="font-size: 12px; padding: 4px; background: #f9f9f9; border-radius: 3px;">
                                    <?php echo esc_html($license->license_key); ?>
                                </code>
                                <button onclick="copyLicense('<?php echo esc_js($license->license_key); ?>')" 
                                        style="margin-left: 5px; padding: 2px 6px; font-size: 11px;">Copy</button>
                            </td>
                            <td>
                                <span class="license-status status-<?php echo esc_attr($license->status); ?>">
                                    <?php echo esc_html(ucfirst($license->status)); ?>
                                </span>
                            </td>
                            <td><?php echo esc_html($license->activation_count . ' / ' . $license->max_sites); ?></td>
                            <td>
                                <?php if ($license->expires_at) : ?>
                                    <?php echo esc_html(date('F j, Y', strtotime($license->expires_at))); ?>
                                <?php else : ?>
                                    Never
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <script>
            function copyLicense(licenseKey) {
                navigator.clipboard.writeText(licenseKey).then(function() {
                    alert('License key copied to clipboard!');
                });
            }
            </script>
            
            <style>
            .license-status {
                padding: 3px 8px;
                border-radius: 3px;
                font-size: 11px;
                font-weight: bold;
                text-transform: uppercase;
            }
            .status-active { background: #4CAF50; color: white; }
            .status-inactive { background: #FFC107; color: black; }
            .status-expired { background: #FF9800; color: white; }
            .status-revoked { background: #F44336; color: white; }
            </style>
        <?php endif; ?>
        <?php
    }
}

// Initialize license manager
new Vireo_License_Manager();

/**
 * Helper function to validate license
 */
function vireo_validate_license($license_key, $site_url = '') {
    $license_manager = new Vireo_License_Manager();
    return $license_manager->validate_license($license_key, $site_url);
}