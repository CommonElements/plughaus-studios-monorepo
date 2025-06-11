<?php
/**
 * WooCommerce Checkout Flow Enhancements for Vireo Designs
 * Optimizes the checkout process for plugin purchases
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Vireo_Checkout_Enhancements {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
    }
    
    public function init() {
        // Checkout process enhancements
        add_filter('woocommerce_checkout_fields', array($this, 'customize_checkout_fields'));
        add_action('woocommerce_checkout_process', array($this, 'validate_plugin_checkout'));
        add_filter('woocommerce_order_button_text', array($this, 'custom_order_button_text'));
        
        // Order confirmation enhancements
        add_action('woocommerce_thankyou', array($this, 'custom_thankyou_actions'));
        add_filter('woocommerce_order_details_after_order_table', array($this, 'add_license_info_to_order'));
        
        // Email enhancements
        add_action('woocommerce_email_before_order_table', array($this, 'add_license_to_email'), 10, 4);
        
        // Cart enhancements for plugins
        add_filter('woocommerce_add_to_cart_message_html', array($this, 'custom_add_to_cart_message'));
        add_action('woocommerce_before_cart', array($this, 'cart_plugin_notice'));
        
        // Checkout page customizations
        add_action('woocommerce_before_checkout_form', array($this, 'checkout_security_notice'));
        add_filter('woocommerce_gateway_description', array($this, 'enhance_payment_description'), 10, 2);
        
        // Auto-complete virtual orders
        add_action('woocommerce_payment_complete', array($this, 'auto_complete_plugin_orders'));
    }
    
    public function customize_checkout_fields($fields) {
        // Simplify billing fields for digital products
        if ($this->cart_contains_only_plugins()) {
            // Remove unnecessary fields for digital downloads
            unset($fields['billing']['billing_address_1']);
            unset($fields['billing']['billing_address_2']);
            unset($fields['billing']['billing_city']);
            unset($fields['billing']['billing_postcode']);
            unset($fields['billing']['billing_state']);
            unset($fields['billing']['billing_country']);
            unset($fields['billing']['billing_phone']);
            
            // Customize remaining fields
            $fields['billing']['billing_first_name']['placeholder'] = 'John';
            $fields['billing']['billing_last_name']['placeholder'] = 'Doe';
            $fields['billing']['billing_email']['placeholder'] = 'john@yourcompany.com';
            
            // Make company name required for business licenses
            if ($this->cart_contains_business_license()) {
                $fields['billing']['billing_company']['required'] = true;
                $fields['billing']['billing_company']['placeholder'] = 'Your Company Name';
                $fields['billing']['billing_company']['description'] = 'Required for business license validation';
            }
        }
        
        return $fields;
    }
    
    public function validate_plugin_checkout() {
        if (!$this->cart_contains_only_plugins()) {
            return;
        }
        
        // Validate email domain for business licenses
        if ($this->cart_contains_business_license()) {
            $email = $_POST['billing_email'] ?? '';
            $company = $_POST['billing_company'] ?? '';
            
            if (empty($company)) {
                wc_add_notice(__('Company name is required for business licenses.', 'vireo-designs'), 'error');
            }
            
            // Basic email domain validation
            if (!empty($email) && strpos($email, '@gmail.com') !== false && !empty($company)) {
                wc_add_notice(__('Please use your business email address for business licenses.', 'vireo-designs'), 'notice');
            }
        }
        
        // Validate against multiple purchases of same plugin
        $this->validate_duplicate_plugins();
    }
    
    public function custom_order_button_text($button_text) {
        if ($this->cart_contains_only_plugins()) {
            return __('Complete Purchase & Download', 'vireo-designs');
        }
        return $button_text;
    }
    
    public function custom_thankyou_actions($order_id) {
        if (!$order_id) return;
        
        $order = wc_get_order($order_id);
        if (!$order) return;
        
        // Track plugin purchase analytics
        $this->track_plugin_purchase($order);
        
        // Schedule follow-up emails
        $this->schedule_followup_emails($order);
    }
    
    public function add_license_info_to_order($order) {
        if (!$order) return;
        
        $licenses = array();
        foreach ($order->get_items() as $item) {
            $product = $item->get_product();
            if ($product && has_term('plugins', 'product_cat', $product->get_id())) {
                $license_key = $order->get_meta('_vireo_license_' . $product->get_id());
                if ($license_key) {
                    $licenses[] = array(
                        'product' => $product->get_name(),
                        'license' => $license_key,
                        'limit' => $order->get_meta('_vireo_license_limit_' . $product->get_id())
                    );
                }
            }
        }
        
        if (!empty($licenses)) {
            echo '<div class="vireo-license-section">';
            echo '<h3>Your License Keys</h3>';
            foreach ($licenses as $license) {
                echo '<div class="license-item">';
                echo '<strong>' . esc_html($license['product']) . '</strong><br>';
                echo '<code>' . esc_html($license['license']) . '</code>';
                echo '<span class="license-limit"> (Limit: ' . intval($license['limit']) . ' sites)</span>';
                echo '</div>';
            }
            echo '</div>';
        }
    }
    
    public function add_license_to_email($order, $sent_to_admin, $plain_text, $email) {
        if ($plain_text || !$order) return;
        
        // Only add to customer emails, not admin
        if ($sent_to_admin) return;
        
        $this->add_license_info_to_order($order);
    }
    
    public function custom_add_to_cart_message($message) {
        if ($this->cart_contains_only_plugins()) {
            return sprintf(
                '<div class="woocommerce-message plugin-cart-message">%s <a href="%s" class="button">%s</a></div>',
                __('Plugin added to cart! Ready for instant download.', 'vireo-designs'),
                wc_get_checkout_url(),
                __('Proceed to Checkout', 'vireo-designs')
            );
        }
        return $message;
    }
    
    public function cart_plugin_notice() {
        if (!$this->cart_contains_only_plugins()) {
            return;
        }
        
        wc_print_notice(
            __('🚀 You\'re purchasing digital plugins! After payment, you\'ll receive instant download links and license keys via email.', 'vireo-designs'),
            'notice'
        );
    }
    
    public function checkout_security_notice() {
        if (!$this->cart_contains_only_plugins()) {
            return;
        }
        
        echo '<div class="vireo-checkout-notice">';
        echo '<div class="container">';
        echo '<div class="notice-content">';
        echo '<i class="fas fa-shield-alt"></i>';
        echo '<div class="notice-text">';
        echo '<strong>Secure Checkout</strong>';
        echo '<span>Your payment is processed securely via Stripe. License keys will be delivered instantly.</span>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        
        // Add CSS for the notice
        echo '<style>
        .vireo-checkout-notice {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
        .notice-content {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .notice-content i {
            font-size: 1.5rem;
            color: #34d399;
        }
        .notice-text {
            display: flex;
            flex-direction: column;
        }
        .notice-text strong {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        .notice-text span {
            opacity: 0.9;
            font-size: 0.9rem;
        }
        </style>';
    }
    
    public function enhance_payment_description($description, $payment_id) {
        if ($payment_id === 'stripe' && $this->cart_contains_only_plugins()) {
            return $description . '<br><small style="color: #666;">Your license keys will be generated automatically after payment confirmation.</small>';
        }
        return $description;
    }
    
    public function auto_complete_plugin_orders($order_id) {
        $order = wc_get_order($order_id);
        if (!$order) return;
        
        // Auto-complete if order contains only virtual/downloadable products
        $virtual_downloadable_only = true;
        foreach ($order->get_items() as $item) {
            $product = $item->get_product();
            if ($product && (!$product->is_virtual() || !$product->is_downloadable())) {
                $virtual_downloadable_only = false;
                break;
            }
        }
        
        if ($virtual_downloadable_only) {
            $order->update_status('completed', 'Auto-completed - digital products only');
        }
    }
    
    private function cart_contains_only_plugins() {
        if (!WC()->cart) return false;
        
        foreach (WC()->cart->get_cart() as $cart_item) {
            $product = $cart_item['data'];
            if (!$product || !has_term('plugins', 'product_cat', $product->get_id())) {
                return false;
            }
        }
        
        return !WC()->cart->is_empty();
    }
    
    private function cart_contains_business_license() {
        if (!WC()->cart) return false;
        
        foreach (WC()->cart->get_cart() as $cart_item) {
            $product = $cart_item['data'];
            if ($product && stripos($product->get_name(), 'business') !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    private function validate_duplicate_plugins() {
        if (!WC()->cart) return;
        
        $plugin_types = array();
        foreach (WC()->cart->get_cart() as $cart_item) {
            $product = $cart_item['data'];
            if ($product && has_term('plugins', 'product_cat', $product->get_id())) {
                // Extract base plugin name (remove "Pro", "Business", etc.)
                $base_name = preg_replace('/\s+(Pro|Business|Developer|Premium).*$/i', '', $product->get_name());
                
                if (in_array($base_name, $plugin_types)) {
                    wc_add_notice(
                        sprintf(__('You already have "%s" in your cart. You only need one license per plugin.', 'vireo-designs'), $base_name),
                        'error'
                    );
                }
                
                $plugin_types[] = $base_name;
            }
        }
    }
    
    private function track_plugin_purchase($order) {
        // Track purchase analytics (implement your preferred analytics)
        foreach ($order->get_items() as $item) {
            $product = $item->get_product();
            if ($product && has_term('plugins', 'product_cat', $product->get_id())) {
                // Log purchase event
                error_log(sprintf(
                    'Vireo Plugin Purchase: %s by %s (%s)',
                    $product->get_name(),
                    $order->get_billing_email(),
                    $order->get_formatted_order_total()
                ));
                
                // You can integrate with Google Analytics, Mixpanel, etc. here
            }
        }
    }
    
    private function schedule_followup_emails($order) {
        // Schedule follow-up emails for customer engagement
        $customer_email = $order->get_billing_email();
        
        // Schedule review request email (7 days)
        wp_schedule_single_event(
            time() + (7 * DAY_IN_SECONDS),
            'vireo_send_review_request',
            array($order->get_id())
        );
        
        // Schedule satisfaction survey (14 days)
        wp_schedule_single_event(
            time() + (14 * DAY_IN_SECONDS),
            'vireo_send_satisfaction_survey',
            array($order->get_id())
        );
    }
}

// Initialize checkout enhancements
new Vireo_Checkout_Enhancements();

// Handle scheduled email actions
add_action('vireo_send_review_request', function($order_id) {
    $order = wc_get_order($order_id);
    if (!$order) return;
    
    $customer_email = $order->get_billing_email();
    $subject = 'How are you enjoying your Vireo plugins?';
    $message = sprintf(
        "Hi %s,\n\nIt's been a week since you purchased our plugins. We'd love to hear how they're working for you!\n\nIf you're happy with your purchase, would you consider leaving a review? It really helps other users discover our plugins.\n\nThanks!\nThe Vireo Team",
        $order->get_billing_first_name()
    );
    
    wp_mail($customer_email, $subject, $message);
});

add_action('vireo_send_satisfaction_survey', function($order_id) {
    $order = wc_get_order($order_id);
    if (!$order) return;
    
    $customer_email = $order->get_billing_email();
    $subject = 'Quick feedback on your Vireo experience';
    $message = sprintf(
        "Hi %s,\n\nWe hope you're getting great value from your Vireo plugins!\n\nWe're always looking to improve. Would you mind sharing any feedback or suggestions?\n\nSimply reply to this email with your thoughts.\n\nBest regards,\nThe Vireo Team",
        $order->get_billing_first_name()
    );
    
    wp_mail($customer_email, $subject, $message);
});
?>