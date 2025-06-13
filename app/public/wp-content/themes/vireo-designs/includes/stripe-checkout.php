<?php
/**
 * Stripe Checkout Integration
 * Handles checkout creation and processing for Vireo Designs products
 */

if (!defined('ABSPATH')) {
    exit;
}

class Vireo_Stripe_Checkout {
    
    public function __construct() {
        add_action('wp_ajax_create_checkout_session', array($this, 'create_checkout_session'));
        add_action('wp_ajax_nopriv_create_checkout_session', array($this, 'create_checkout_session'));
        add_action('init', array($this, 'handle_checkout_success'));
    }
    
    /**
     * Create Stripe Checkout Session
     */
    public function create_checkout_session() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'vireo_nonce')) {
            wp_die('Security check failed');
        }
        
        $price_id = sanitize_text_field($_POST['price_id']);
        $product_name = sanitize_text_field($_POST['product_name']);
        
        if (!$price_id) {
            wp_send_json_error('Invalid price ID');
            return;
        }
        
        try {
            // Initialize Stripe with our secret key
            require_once get_template_directory() . '/vendor/stripe-php/init.php';
            \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
            
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => $price_id,
                    'quantity' => 1,
                ]],
                'mode' => strpos($price_id, 'recurring') ? 'subscription' : 'payment',
                'success_url' => home_url('/checkout-success?session_id={CHECKOUT_SESSION_ID}'),
                'cancel_url' => home_url('/shop'),
                'metadata' => [
                    'product_name' => $product_name,
                    'user_id' => get_current_user_id(),
                    'site_url' => home_url(),
                ],
                'customer_email' => is_user_logged_in() ? wp_get_current_user()->user_email : null,
            ]);
            
            wp_send_json_success(['checkout_url' => $session->url]);
            
        } catch (Exception $e) {
            wp_send_json_error('Checkout creation failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Handle successful checkout
     */
    public function handle_checkout_success() {
        if (!isset($_GET['session_id']) || !is_page('checkout-success')) {
            return;
        }
        
        $session_id = sanitize_text_field($_GET['session_id']);
        
        try {
            require_once get_template_directory() . '/vendor/stripe-php/init.php';
            \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
            
            $session = \Stripe\Checkout\Session::retrieve($session_id);
            
            if ($session->payment_status === 'paid') {
                // Process successful payment
                $this->process_successful_payment($session);
            }
            
        } catch (Exception $e) {
            error_log('Stripe session retrieval failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Process successful payment
     */
    private function process_successful_payment($session) {
        $user_id = $session->metadata->user_id ?? null;
        $product_name = $session->metadata->product_name ?? 'Unknown Product';
        
        // Store purchase record
        $purchase_data = array(
            'user_id' => $user_id,
            'session_id' => $session->id,
            'customer_id' => $session->customer,
            'product_name' => $product_name,
            'amount' => $session->amount_total,
            'currency' => $session->currency,
            'payment_status' => $session->payment_status,
            'created_at' => current_time('mysql'),
        );
        
        // Add to WordPress options for now (in production, use proper database table)
        $purchases = get_option('vireo_purchases', array());
        $purchases[] = $purchase_data;
        update_option('vireo_purchases', $purchases);
        
        // Send confirmation email
        $this->send_confirmation_email($session, $purchase_data);
        
        // Generate license key for Property Management Pro
        if (strpos($product_name, 'Property Management Pro') !== false) {
            $this->generate_license_key($user_id, $session);
        }
    }
    
    /**
     * Generate license key
     */
    private function generate_license_key($user_id, $session) {
        $license_key = 'PHPM-' . strtoupper(wp_generate_password(8, false)) . '-' . strtoupper(wp_generate_password(8, false));
        
        $license_data = array(
            'license_key' => $license_key,
            'user_id' => $user_id,
            'session_id' => $session->id,
            'product' => 'property-management-pro',
            'status' => 'active',
            'created_at' => current_time('mysql'),
            'expires_at' => date('Y-m-d H:i:s', strtotime('+1 year')),
        );
        
        $licenses = get_option('vireo_licenses', array());
        $licenses[$license_key] = $license_data;
        update_option('vireo_licenses', $licenses);
        
        return $license_key;
    }
    
    /**
     * Send confirmation email
     */
    private function send_confirmation_email($session, $purchase_data) {
        if (!$session->customer_details->email) {
            return;
        }
        
        $to = $session->customer_details->email;
        $subject = 'Thank you for your purchase from Vireo Designs';
        
        $message = "
        <h2>Purchase Confirmation</h2>
        <p>Thank you for purchasing {$purchase_data['product_name']} from Vireo Designs!</p>
        
        <h3>Order Details:</h3>
        <ul>
            <li><strong>Product:</strong> {$purchase_data['product_name']}</li>
            <li><strong>Amount:</strong> $" . number_format($purchase_data['amount'] / 100, 2) . " {$purchase_data['currency']}</li>
            <li><strong>Order ID:</strong> {$session->id}</li>
        </ul>
        
        <p>You can download your plugin and access your license key from your account dashboard.</p>
        
        <p>If you have any questions, please contact our support team.</p>
        
        <p>Best regards,<br>The Vireo Designs Team</p>
        ";
        
        $headers = array('Content-Type: text/html; charset=UTF-8');
        wp_mail($to, $subject, $message, $headers);
    }
}

// Initialize the checkout handler
new Vireo_Stripe_Checkout();
?>