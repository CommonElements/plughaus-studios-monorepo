<?php
/**
 * Stripe Webhook Handler for Vireo Designs
 * Handles Stripe payment confirmations and order processing
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Vireo_Stripe_Webhook_Handler {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('woocommerce_api_vireo_stripe_webhook', array($this, 'handle_webhook'));
    }
    
    public function init() {
        // Register webhook endpoint
        add_rewrite_rule('^vireo-stripe-webhook/?$', 'index.php?wc-api=vireo_stripe_webhook', 'top');
        
        // Flush rewrite rules if needed
        if (get_option('vireo_webhook_rules_flushed') !== 'yes') {
            flush_rewrite_rules();
            update_option('vireo_webhook_rules_flushed', 'yes');
        }
    }
    
    public function handle_webhook() {
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        
        // Verify webhook signature (when webhook secret is configured)
        $webhook_secret = get_option('vireo_stripe_webhook_secret', '');
        
        if (!empty($webhook_secret)) {
            try {
                $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $webhook_secret);
            } catch (\Exception $e) {
                error_log('Vireo Stripe Webhook Error: ' . $e->getMessage());
                http_response_code(400);
                exit();
            }
        } else {
            $event = json_decode($payload, true);
        }
        
        // Handle the event
        switch ($event['type']) {
            case 'payment_intent.succeeded':
                $this->handle_payment_success($event['data']['object']);
                break;
                
            case 'payment_intent.payment_failed':
                $this->handle_payment_failure($event['data']['object']);
                break;
                
            case 'charge.dispute.created':
                $this->handle_dispute_created($event['data']['object']);
                break;
                
            default:
                error_log('Vireo Stripe: Unhandled webhook event type: ' . $event['type']);
        }
        
        http_response_code(200);
        echo json_encode(array('status' => 'success'));
        exit();
    }
    
    private function handle_payment_success($payment_intent) {
        // Find the order by payment intent
        $order_id = $this->get_order_id_from_payment_intent($payment_intent['id']);
        
        if (!$order_id) {
            error_log('Vireo Stripe: Order not found for payment intent: ' . $payment_intent['id']);
            return;
        }
        
        $order = wc_get_order($order_id);
        if (!$order) {
            return;
        }
        
        // Add payment confirmation note
        $order->add_order_note(sprintf(
            'Stripe payment confirmed. Payment Intent: %s, Amount: %s %s',
            $payment_intent['id'],
            number_format($payment_intent['amount'] / 100, 2),
            strtoupper($payment_intent['currency'])
        ));
        
        // Mark as processing if not already completed
        if ($order->get_status() === 'pending') {
            $order->update_status('processing', 'Payment confirmed via Stripe webhook');
        }
        
        // Store Stripe metadata
        $order->add_meta_data('_stripe_payment_intent_id', $payment_intent['id']);
        $order->add_meta_data('_stripe_charge_id', $payment_intent['charges']['data'][0]['id'] ?? '');
        $order->save();
    }
    
    private function handle_payment_failure($payment_intent) {
        $order_id = $this->get_order_id_from_payment_intent($payment_intent['id']);
        
        if (!$order_id) {
            return;
        }
        
        $order = wc_get_order($order_id);
        if (!$order) {
            return;
        }
        
        $failure_reason = $payment_intent['last_payment_error']['message'] ?? 'Unknown error';
        
        $order->add_order_note(sprintf(
            'Stripe payment failed. Reason: %s. Payment Intent: %s',
            $failure_reason,
            $payment_intent['id']
        ));
        
        $order->update_status('failed', 'Payment failed via Stripe webhook');
    }
    
    private function handle_dispute_created($dispute) {
        $charge_id = $dispute['charge'];
        
        // Find order by charge ID
        $orders = wc_get_orders(array(
            'meta_key' => '_stripe_charge_id',
            'meta_value' => $charge_id,
            'limit' => 1
        ));
        
        if (empty($orders)) {
            return;
        }
        
        $order = $orders[0];
        $order->add_order_note(sprintf(
            'Stripe dispute created. Dispute ID: %s, Reason: %s, Amount: %s %s',
            $dispute['id'],
            $dispute['reason'],
            number_format($dispute['amount'] / 100, 2),
            strtoupper($dispute['currency'])
        ));
        
        // Notify admin
        $admin_email = get_option('admin_email');
        wp_mail(
            $admin_email,
            'Stripe Dispute Created - Order #' . $order->get_id(),
            sprintf(
                "A dispute has been created for order #%d.\n\nDispute ID: %s\nReason: %s\nAmount: %s %s\n\nPlease review in your Stripe dashboard.",
                $order->get_id(),
                $dispute['id'],
                $dispute['reason'],
                number_format($dispute['amount'] / 100, 2),
                strtoupper($dispute['currency'])
            )
        );
    }
    
    private function get_order_id_from_payment_intent($payment_intent_id) {
        // Search for order with this payment intent
        global $wpdb;
        
        $order_id = $wpdb->get_var($wpdb->prepare(
            "SELECT post_id FROM {$wpdb->postmeta} 
             WHERE meta_key = '_stripe_payment_intent_id' 
             AND meta_value = %s",
            $payment_intent_id
        ));
        
        return $order_id;
    }
}

// Initialize webhook handler
new Vireo_Stripe_Webhook_Handler();
?>