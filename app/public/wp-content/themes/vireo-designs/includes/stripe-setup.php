<?php
/**
 * Stripe Payment Gateway Setup for Vireo Designs
 * Configures WooCommerce Stripe integration for plugin sales
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Vireo_Stripe_Setup {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('woocommerce_init', array($this, 'configure_stripe'));
        add_filter('woocommerce_payment_gateways', array($this, 'add_stripe_gateway'));
        add_action('woocommerce_checkout_process', array($this, 'validate_checkout'));
        add_action('woocommerce_order_status_completed', array($this, 'handle_order_completion'));
    }
    
    public function init() {
        // Check if WooCommerce and Stripe gateway are active
        if (!class_exists('WooCommerce') || !class_exists('WC_Gateway_Stripe')) {
            add_action('admin_notices', array($this, 'missing_dependencies_notice'));
            return;
        }
        
        // Configure Stripe settings
        $this->setup_stripe_settings();
    }
    
    public function configure_stripe() {
        if (!class_exists('WC_Gateway_Stripe')) {
            return;
        }
        
        // Get Stripe gateway settings
        $stripe_settings = get_option('woocommerce_stripe_settings', array());
        
        // Configure default Stripe settings for Vireo
        $default_settings = array(
            'enabled' => 'yes',
            'title' => 'Credit Card',
            'description' => 'Pay securely with your credit card. Your payment information is processed securely via Stripe.',
            'testmode' => 'yes', // Set to 'no' for production
            'test_publishable_key' => 'pk_test_51234567890abcdefghijk', // Replace with your test key
            'test_secret_key' => 'sk_test_51234567890abcdefghijk', // Replace with your test key
            'publishable_key' => '', // Add your live key when ready
            'secret_key' => '', // Add your live key when ready
            'capture' => 'yes',
            'payment_request' => 'yes',
            'payment_request_button_type' => 'buy',
            'payment_request_button_theme' => 'dark',
            'saved_cards' => 'yes',
            'logging' => 'yes',
            'statement_descriptor' => 'VIREO DESIGNS',
            'payment_request_button_locations' => array('product', 'cart', 'checkout'),
            'upe_checkout_experience_enabled' => 'yes',
            'webhook_secret' => '',
            'inline_cc_form' => 'yes'
        );
        
        // Merge with existing settings
        $stripe_settings = array_merge($default_settings, $stripe_settings);
        
        // Update settings
        update_option('woocommerce_stripe_settings', $stripe_settings);
    }
    
    public function setup_stripe_settings() {
        // Stripe configuration for Vireo Designs
        add_filter('woocommerce_stripe_request_body', array($this, 'customize_stripe_request'));
        add_filter('woocommerce_stripe_payment_intent_args', array($this, 'customize_payment_intent'));
    }
    
    public function customize_stripe_request($body) {
        // Add Vireo-specific metadata to Stripe requests
        if (!isset($body['metadata'])) {
            $body['metadata'] = array();
        }
        
        $body['metadata']['company'] = 'Vireo Designs';
        $body['metadata']['website'] = home_url();
        
        return $body;
    }
    
    public function customize_payment_intent($args) {
        // Customize payment intent for plugin purchases
        $args['metadata']['product_type'] = 'wordpress_plugin';
        $args['metadata']['vendor'] = 'vireo_designs';
        
        return $args;
    }
    
    public function add_stripe_gateway($gateways) {
        // Ensure Stripe is the primary payment method
        if (class_exists('WC_Gateway_Stripe')) {
            // Move Stripe to the beginning of the array
            $stripe_key = array_search('WC_Gateway_Stripe', $gateways);
            if ($stripe_key !== false) {
                unset($gateways[$stripe_key]);
                array_unshift($gateways, 'WC_Gateway_Stripe');
            }
        }
        
        return $gateways;
    }
    
    public function validate_checkout() {
        // Additional validation for plugin purchases
        if (is_admin() && !defined('DOING_AJAX')) {
            return;
        }
        
        // Check if purchasing a plugin
        $cart_contains_plugins = false;
        foreach (WC()->cart->get_cart() as $cart_item) {
            $product = $cart_item['data'];
            if ($product && has_term('plugins', 'product_cat', $product->get_id())) {
                $cart_contains_plugins = true;
                break;
            }
        }
        
        if ($cart_contains_plugins) {
            // Additional validation for plugin purchases
            $this->validate_plugin_purchase();
        }
    }
    
    public function validate_plugin_purchase() {
        // Email validation
        $billing_email = $_POST['billing_email'] ?? '';
        if (empty($billing_email) || !is_email($billing_email)) {
            wc_add_notice(__('Please provide a valid email address for license delivery.', 'vireo-designs'), 'error');
        }
        
        // Company name for business licenses
        foreach (WC()->cart->get_cart() as $cart_item) {
            $product = $cart_item['data'];
            if ($product && strpos($product->get_name(), 'Business') !== false) {
                $billing_company = $_POST['billing_company'] ?? '';
                if (empty($billing_company)) {
                    wc_add_notice(__('Company name is required for business licenses.', 'vireo-designs'), 'error');
                }
            }
        }
    }
    
    public function handle_order_completion($order_id) {
        $order = wc_get_order($order_id);
        if (!$order) {
            return;
        }
        
        // Process plugin license generation
        foreach ($order->get_items() as $item) {
            $product = $item->get_product();
            if ($product && has_term('plugins', 'product_cat', $product->get_id())) {
                $this->generate_plugin_license($order, $item, $product);
            }
        }
        
        // Send welcome email with download links
        $this->send_purchase_confirmation($order);
    }
    
    public function generate_plugin_license($order, $item, $product) {
        // Check if license management is enabled for this product
        $enable_license = get_post_meta($product->get_id(), '_enable_license_management', true);
        if ($enable_license !== 'yes') {
            return;
        }
        
        // Check if license already exists for this order item
        $existing_license = $order->get_meta('_vireo_license_' . $product->get_id());
        if (!empty($existing_license)) {
            return; // License already generated
        }
        
        // Generate a simple license key for now (replace with License Manager when available)
        $license_key = $this->generate_license_key($order, $product);
        
        // Store license info in order meta
        $order->add_meta_data('_vireo_license_' . $product->get_id(), $license_key);
        $order->add_meta_data('_vireo_license_status_' . $product->get_id(), 'active');
        $order->add_meta_data('_vireo_license_activations_' . $product->get_id(), 0);
        $order->add_meta_data('_vireo_license_limit_' . $product->get_id(), $this->get_activation_limit($product));
        $order->save();
        
        // Log license generation
        $order->add_order_note(sprintf(
            'Vireo license generated for %s: %s (Limit: %d sites)',
            $product->get_name(),
            $license_key,
            $this->get_activation_limit($product)
        ));
        
        // Future: Integration with License Manager for WooCommerce
        /*
        if (class_exists('LicenseManagerForWooCommerce\\Main')) {
            try {
                $license_data = array(
                    'order_id' => $order->get_id(),
                    'product_id' => $product->get_id(),
                    'user_id' => $order->get_user_id(),
                    'expires_at' => null, // Lifetime license
                    'activations_limit' => $this->get_activation_limit($product),
                    'status' => 'active'
                );
                
                $license = \LicenseManagerForWooCommerce\Repositories\Generators\GeneratorRepository::instance()->create($license_data);
                $order->update_meta_data('_vireo_license_' . $product->get_id(), $license->getDecryptedLicenseKey());
                $order->save();
            } catch (Exception $e) {
                error_log('License Manager Integration Error: ' . $e->getMessage());
            }
        }
        */
    }
    
    private function generate_license_key($order, $product) {
        // Generate a unique license key
        $prefix = 'VIREO';
        $order_id = str_pad($order->get_id(), 4, '0', STR_PAD_LEFT);
        $product_id = str_pad($product->get_id(), 3, '0', STR_PAD_LEFT);
        $random = strtoupper(wp_generate_password(8, false));
        
        return sprintf('%s-%s-%s-%s', $prefix, $order_id, $product_id, $random);
    }
    
    public function get_activation_limit($product) {
        // Determine activation limits based on product
        $product_name = strtolower($product->get_name());
        
        if (strpos($product_name, 'business') !== false) {
            return 5; // Business license: 5 sites
        } elseif (strpos($product_name, 'developer') !== false) {
            return 25; // Developer license: 25 sites
        } else {
            return 1; // Single site license
        }
    }
    
    public function send_purchase_confirmation($order) {
        // Get customer email
        $customer_email = $order->get_billing_email();
        
        // Prepare email content
        $subject = 'Your Vireo Designs Plugin Purchase - Download Links & License Keys';
        
        $message = $this->get_purchase_email_template($order);
        
        // Send email
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: Vireo Designs <noreply@vireodesigns.com>'
        );
        
        wp_mail($customer_email, $subject, $message, $headers);
        
        // Also send to admin
        wp_mail(get_option('admin_email'), 'New Plugin Purchase: Order #' . $order->get_id(), $message, $headers);
    }
    
    public function get_purchase_email_template($order) {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Your Vireo Plugin Purchase</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #059669; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .plugin-item { background: white; padding: 15px; margin: 10px 0; border-radius: 8px; }
                .license-key { background: #e8f5e8; padding: 10px; font-family: monospace; word-break: break-all; }
                .footer { padding: 20px; text-align: center; font-size: 14px; color: #666; }
                .btn { background: #059669; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🎉 Thank You for Your Purchase!</h1>
                    <p>Your Vireo Designs plugins are ready for download</p>
                </div>
                
                <div class="content">
                    <h2>Order #<?php echo $order->get_id(); ?></h2>
                    <p>Hello <?php echo $order->get_billing_first_name(); ?>,</p>
                    <p>Thank you for purchasing from Vireo Designs! Your plugins are now available for download with the license keys below.</p>
                    
                    <?php foreach ($order->get_items() as $item): 
                        $product = $item->get_product();
                        if ($product && has_term('plugins', 'product_cat', $product->get_id())):
                            $license_key = $order->get_meta('_vireo_license_' . $product->get_id());
                    ?>
                    <div class="plugin-item">
                        <h3><?php echo $product->get_name(); ?></h3>
                        <p><strong>License Key:</strong></p>
                        <div class="license-key"><?php echo $license_key ?: 'Generating...'; ?></div>
                        <p>
                            <a href="<?php echo wp_get_attachment_url($product->get_download_files()[0]['id'] ?? ''); ?>" class="btn">
                                Download Plugin
                            </a>
                        </p>
                        <p><small>Activation limit: <?php echo $this->get_activation_limit($product); ?> site(s)</small></p>
                    </div>
                    <?php endif; endforeach; ?>
                    
                    <h3>What's Next?</h3>
                    <ol>
                        <li>Download your plugin files using the links above</li>
                        <li>Install the plugin on your WordPress site</li>
                        <li>Enter your license key in the plugin settings</li>
                        <li>Enjoy your new functionality!</li>
                    </ol>
                    
                    <p><strong>Need Help?</strong><br>
                    Visit our <a href="<?php echo home_url('/support/'); ?>">Support Center</a> or reply to this email.</p>
                </div>
                
                <div class="footer">
                    <p>© <?php echo date('Y'); ?> Vireo Designs. All rights reserved.</p>
                    <p>Simple software solutions for small business success.</p>
                </div>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
    
    public function missing_dependencies_notice() {
        ?>
        <div class="notice notice-error">
            <p>
                <strong>Vireo Stripe Setup:</strong> 
                WooCommerce and WooCommerce Stripe Gateway plugins are required for payment processing.
            </p>
        </div>
        <?php
    }
}

// Initialize Stripe setup
new Vireo_Stripe_Setup();
?>