<?php
/**
 * Stripe Payment Gateway for Knot4
 * 
 * @package Knot4
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Knot4_Stripe_Gateway {
    
    private $secret_key;
    private $publishable_key;
    private $webhook_secret;
    private $test_mode;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->init_settings();
        $this->init_hooks();
    }
    
    /**
     * Initialize payment settings
     */
    private function init_settings() {
        $payment_settings = Knot4_Utilities::get_payment_settings();
        
        $this->test_mode = !empty($payment_settings['test_mode']);
        
        if ($this->test_mode) {
            $this->secret_key = $payment_settings['stripe_test_secret_key'] ?? '';
            $this->publishable_key = $payment_settings['stripe_test_publishable_key'] ?? '';
            $this->webhook_secret = $payment_settings['stripe_test_webhook_secret'] ?? '';
        } else {
            $this->secret_key = $payment_settings['stripe_live_secret_key'] ?? '';
            $this->publishable_key = $payment_settings['stripe_live_publishable_key'] ?? '';
            $this->webhook_secret = $payment_settings['stripe_live_webhook_secret'] ?? '';
        }
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('wp_ajax_knot4_create_payment_intent', array($this, 'create_payment_intent'));
        add_action('wp_ajax_nopriv_knot4_create_payment_intent', array($this, 'create_payment_intent'));
        
        add_action('wp_ajax_knot4_confirm_payment', array($this, 'confirm_payment'));
        add_action('wp_ajax_nopriv_knot4_confirm_payment', array($this, 'confirm_payment'));
        
        add_action('wp_ajax_knot4_stripe_webhook', array($this, 'handle_webhook'));
        add_action('wp_ajax_nopriv_knot4_stripe_webhook', array($this, 'handle_webhook'));
        
        // Enqueue Stripe.js on donation pages
        add_action('wp_enqueue_scripts', array($this, 'enqueue_stripe_scripts'));
    }
    
    /**
     * Enqueue Stripe.js scripts
     */
    public function enqueue_stripe_scripts() {
        if ($this->is_donation_page()) {
            wp_enqueue_script('stripe-js', 'https://js.stripe.com/v3/', array(), null, true);
            
            wp_enqueue_script(
                'knot4-stripe',
                KNOT4_PLUGIN_URL . 'core/assets/js/stripe-handler.js',
                array('jquery', 'stripe-js', 'knot4-public'),
                KNOT4_VERSION,
                true
            );
            
            wp_localize_script('knot4-stripe', 'knot4_stripe', array(
                'publishable_key' => $this->publishable_key,
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('knot4_stripe_nonce'),
                'currency' => 'usd',
                'test_mode' => $this->test_mode,
                'strings' => array(
                    'processing' => __('Processing payment...', 'knot4'),
                    'payment_failed' => __('Payment failed. Please try again.', 'knot4'),
                    'card_error' => __('Your card was declined. Please check your card details.', 'knot4'),
                    'network_error' => __('Network error. Please check your connection and try again.', 'knot4'),
                )
            ));
        }
    }
    
    /**
     * Create payment intent
     */
    public function create_payment_intent() {
        check_ajax_referer('knot4_stripe_nonce', 'nonce');
        
        $amount = floatval($_POST['amount']);
        $currency = sanitize_text_field($_POST['currency']) ?: 'usd';
        $donation_id = intval($_POST['donation_id']);
        
        if ($amount <= 0) {
            wp_send_json_error(array('message' => __('Invalid amount.', 'knot4')));
        }
        
        try {
            $this->include_stripe_sdk();
            \Stripe\Stripe::setApiKey($this->secret_key);
            
            // Convert amount to cents
            $amount_cents = intval($amount * 100);
            
            $intent_data = array(
                'amount' => $amount_cents,
                'currency' => strtolower($currency),
                'automatic_payment_methods' => array(
                    'enabled' => true,
                ),
                'metadata' => array(
                    'donation_id' => $donation_id,
                    'plugin' => 'knot4',
                    'site_url' => home_url(),
                ),
                'receipt_email' => sanitize_email($_POST['donor_email']),
                'description' => sprintf(__('Donation to %s', 'knot4'), get_bloginfo('name')),
            );
            
            // Add customer information if available
            $customer_data = $this->prepare_customer_data($_POST);
            if ($customer_data) {
                $customer = $this->create_or_get_customer($customer_data);
                if ($customer) {
                    $intent_data['customer'] = $customer->id;
                }
            }
            
            $intent = \Stripe\PaymentIntent::create($intent_data);
            
            // Update donation record with payment intent ID
            global $wpdb;
            $wpdb->update(
                $wpdb->prefix . 'knot4_donations',
                array('gateway_transaction_id' => $intent->id),
                array('id' => $donation_id),
                array('%s'),
                array('%d')
            );
            
            wp_send_json_success(array(
                'client_secret' => $intent->client_secret,
                'payment_intent_id' => $intent->id,
            ));
            
        } catch (\Stripe\Exception\CardException $e) {
            wp_send_json_error(array('message' => $e->getError()->message));
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            wp_send_json_error(array('message' => __('Invalid request. Please try again.', 'knot4')));
        } catch (\Stripe\Exception\AuthenticationException $e) {
            wp_send_json_error(array('message' => __('Payment gateway authentication failed.', 'knot4')));
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            wp_send_json_error(array('message' => __('Network error. Please try again.', 'knot4')));
        } catch (\Stripe\Exception\ApiErrorException $e) {
            wp_send_json_error(array('message' => __('Payment processing error. Please try again.', 'knot4')));
        } catch (Exception $e) {
            error_log('Knot4 Stripe Error: ' . $e->getMessage());
            wp_send_json_error(array('message' => __('An unexpected error occurred.', 'knot4')));
        }
    }
    
    /**
     * Confirm payment
     */
    public function confirm_payment() {
        check_ajax_referer('knot4_stripe_nonce', 'nonce');
        
        $payment_intent_id = sanitize_text_field($_POST['payment_intent_id']);
        $donation_id = intval($_POST['donation_id']);
        
        try {
            $this->include_stripe_sdk();
            \Stripe\Stripe::setApiKey($this->secret_key);
            
            $intent = \Stripe\PaymentIntent::retrieve($payment_intent_id);
            
            if ($intent->status === 'succeeded') {
                // Update donation status
                Knot4_Donations::update_donation_status($donation_id, 'completed', $payment_intent_id);
                
                // Log activity
                Knot4_Utilities::log_activity(
                    'payment_completed',
                    sprintf(__('Payment completed for donation ID: %d', 'knot4'), $donation_id),
                    $donation_id
                );
                
                wp_send_json_success(array(
                    'message' => __('Payment completed successfully!', 'knot4'),
                    'redirect_url' => home_url('/donation-thank-you/?donation_id=' . $donation_id)
                ));
            } else {
                wp_send_json_error(array('message' => __('Payment not completed.', 'knot4')));
            }
            
        } catch (Exception $e) {
            error_log('Knot4 Stripe Confirm Error: ' . $e->getMessage());
            wp_send_json_error(array('message' => __('Payment confirmation failed.', 'knot4')));
        }
    }
    
    /**
     * Handle Stripe webhooks
     */
    public function handle_webhook() {
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        
        try {
            $this->include_stripe_sdk();
            \Stripe\Stripe::setApiKey($this->secret_key);
            
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $this->webhook_secret
            );
            
            // Handle the event
            switch ($event['type']) {
                case 'payment_intent.succeeded':
                    $this->handle_payment_succeeded($event['data']['object']);
                    break;
                    
                case 'payment_intent.payment_failed':
                    $this->handle_payment_failed($event['data']['object']);
                    break;
                    
                case 'invoice.payment_succeeded':
                    $this->handle_subscription_payment($event['data']['object']);
                    break;
                    
                case 'customer.subscription.deleted':
                    $this->handle_subscription_cancelled($event['data']['object']);
                    break;
                    
                default:
                    error_log('Unhandled Stripe webhook event type: ' . $event['type']);
            }
            
            http_response_code(200);
            echo json_encode(array('status' => 'success'));
            
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            error_log('Invalid Stripe webhook payload: ' . $e->getMessage());
            http_response_code(400);
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            error_log('Invalid Stripe webhook signature: ' . $e->getMessage());
            http_response_code(400);
            exit();
        } catch (Exception $e) {
            error_log('Stripe webhook error: ' . $e->getMessage());
            http_response_code(500);
            exit();
        }
        
        exit();
    }
    
    /**
     * Handle successful payment
     */
    private function handle_payment_succeeded($payment_intent) {
        $donation_id = $payment_intent->metadata->donation_id ?? null;
        
        if ($donation_id) {
            Knot4_Donations::update_donation_status($donation_id, 'completed', $payment_intent->id);
            
            Knot4_Utilities::log_activity(
                'webhook_payment_succeeded',
                sprintf(__('Webhook: Payment succeeded for donation ID: %d', 'knot4'), $donation_id),
                $donation_id
            );
        }
    }
    
    /**
     * Handle failed payment
     */
    private function handle_payment_failed($payment_intent) {
        $donation_id = $payment_intent->metadata->donation_id ?? null;
        
        if ($donation_id) {
            Knot4_Donations::update_donation_status($donation_id, 'failed', $payment_intent->id);
            
            Knot4_Utilities::log_activity(
                'webhook_payment_failed',
                sprintf(__('Webhook: Payment failed for donation ID: %d', 'knot4'), $donation_id),
                $donation_id
            );
        }
    }
    
    /**
     * Handle subscription payment
     */
    private function handle_subscription_payment($invoice) {
        // Handle recurring donation payments
        $subscription_id = $invoice->subscription;
        
        if ($subscription_id) {
            global $wpdb;
            
            // Find recurring donation record
            $recurring = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}knot4_recurring_donations WHERE gateway_subscription_id = %s",
                $subscription_id
            ));
            
            if ($recurring) {
                // Create new donation record for this payment
                $donation_data = array(
                    'donor_id' => $recurring->donor_id,
                    'amount' => $recurring->amount,
                    'currency' => $recurring->currency,
                    'frequency' => $recurring->frequency,
                    'status' => 'completed',
                    'payment_gateway' => 'stripe',
                    'gateway_transaction_id' => $invoice->payment_intent,
                    'is_recurring' => true,
                );
                
                $donation_id = Knot4_Donations::create_donation($donation_data);
                
                // Update recurring donation record
                $wpdb->update(
                    $wpdb->prefix . 'knot4_recurring_donations',
                    array(
                        'last_payment_date' => current_time('mysql'),
                        'next_payment_date' => $this->calculate_next_payment_date($recurring->frequency),
                        'failure_count' => 0,
                    ),
                    array('id' => $recurring->id),
                    array('%s', '%s', '%d'),
                    array('%d')
                );
                
                Knot4_Utilities::log_activity(
                    'recurring_payment_processed',
                    sprintf(__('Recurring payment processed for subscription: %s', 'knot4'), $subscription_id),
                    $donation_id
                );
            }
        }
    }
    
    /**
     * Handle subscription cancellation
     */
    private function handle_subscription_cancelled($subscription) {
        global $wpdb;
        
        $wpdb->update(
            $wpdb->prefix . 'knot4_recurring_donations',
            array('status' => 'cancelled'),
            array('gateway_subscription_id' => $subscription->id),
            array('%s'),
            array('%s')
        );
        
        Knot4_Utilities::log_activity(
            'subscription_cancelled',
            sprintf(__('Subscription cancelled: %s', 'knot4'), $subscription->id),
            0
        );
    }
    
    /**
     * Prepare customer data
     */
    private function prepare_customer_data($post_data) {
        $customer_data = array();
        
        if (!empty($post_data['donor_email'])) {
            $customer_data['email'] = sanitize_email($post_data['donor_email']);
        }
        
        if (!empty($post_data['donor_first_name']) || !empty($post_data['donor_last_name'])) {
            $customer_data['name'] = trim(
                sanitize_text_field($post_data['donor_first_name']) . ' ' . 
                sanitize_text_field($post_data['donor_last_name'])
            );
        }
        
        if (!empty($post_data['donor_phone'])) {
            $customer_data['phone'] = sanitize_text_field($post_data['donor_phone']);
        }
        
        return !empty($customer_data) ? $customer_data : null;
    }
    
    /**
     * Create or get Stripe customer
     */
    private function create_or_get_customer($customer_data) {
        try {
            // Check if customer already exists
            $customers = \Stripe\Customer::all(array(
                'email' => $customer_data['email'],
                'limit' => 1,
            ));
            
            if (!empty($customers->data)) {
                return $customers->data[0];
            }
            
            // Create new customer
            return \Stripe\Customer::create($customer_data);
            
        } catch (Exception $e) {
            error_log('Stripe customer creation error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Calculate next payment date
     */
    private function calculate_next_payment_date($frequency) {
        switch ($frequency) {
            case 'monthly':
                return date('Y-m-d H:i:s', strtotime('+1 month'));
            case 'quarterly':
                return date('Y-m-d H:i:s', strtotime('+3 months'));
            case 'annually':
                return date('Y-m-d H:i:s', strtotime('+1 year'));
            default:
                return date('Y-m-d H:i:s', strtotime('+1 month'));
        }
    }
    
    /**
     * Include Stripe SDK
     */
    private function include_stripe_sdk() {
        if (!class_exists('\Stripe\Stripe')) {
            require_once KNOT4_PLUGIN_DIR . 'vendor/stripe/stripe-php/init.php';
        }
    }
    
    /**
     * Check if current page has donation forms
     */
    private function is_donation_page() {
        global $post;
        
        if (!$post) {
            return false;
        }
        
        return has_shortcode($post->post_content, 'knot4_donation_form') ||
               has_shortcode($post->post_content, 'knot4_campaign_form');
    }
    
    /**
     * Get gateway status
     */
    public function is_configured() {
        return !empty($this->secret_key) && !empty($this->publishable_key);
    }
    
    /**
     * Get publishable key for frontend
     */
    public function get_publishable_key() {
        return $this->publishable_key;
    }
    
    /**
     * Create subscription for recurring donations
     */
    public function create_subscription($customer_id, $price_id, $donation_data) {
        try {
            $this->include_stripe_sdk();
            \Stripe\Stripe::setApiKey($this->secret_key);
            
            $subscription = \Stripe\Subscription::create(array(
                'customer' => $customer_id,
                'items' => array(
                    array('price' => $price_id),
                ),
                'metadata' => array(
                    'donation_id' => $donation_data['id'],
                    'donor_id' => $donation_data['donor_id'],
                    'plugin' => 'knot4',
                ),
            ));
            
            return $subscription;
            
        } catch (Exception $e) {
            error_log('Stripe subscription creation error: ' . $e->getMessage());
            return new WP_Error('subscription_failed', $e->getMessage());
        }
    }
    
    /**
     * Create price for recurring donations
     */
    public function create_recurring_price($amount, $currency, $interval) {
        try {
            $this->include_stripe_sdk();
            \Stripe\Stripe::setApiKey($this->secret_key);
            
            // Create product first
            $product = \Stripe\Product::create(array(
                'name' => sprintf(__('Recurring Donation - %s', 'knot4'), get_bloginfo('name')),
                'type' => 'service',
            ));
            
            // Create price
            $price = \Stripe\Price::create(array(
                'unit_amount' => intval($amount * 100), // Convert to cents
                'currency' => strtolower($currency),
                'recurring' => array('interval' => $interval),
                'product' => $product->id,
            ));
            
            return $price;
            
        } catch (Exception $e) {
            error_log('Stripe price creation error: ' . $e->getMessage());
            return new WP_Error('price_creation_failed', $e->getMessage());
        }
    }
}