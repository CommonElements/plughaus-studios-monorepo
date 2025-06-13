<?php
/**
 * Donations Module for Knot4
 * 
 * @package Knot4
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Knot4_Donations {
    
    /**
     * Initialize donations module
     */
    public static function init() {
        add_action('wp_ajax_knot4_process_donation', array(__CLASS__, 'process_donation'));
        add_action('wp_ajax_nopriv_knot4_process_donation', array(__CLASS__, 'process_donation'));
        add_action('wp_ajax_knot4_create_donation', array(__CLASS__, 'ajax_create_donation'));
        add_action('wp_ajax_nopriv_knot4_create_donation', array(__CLASS__, 'ajax_create_donation'));
        add_action('wp_ajax_knot4_validate_donation_form', array(__CLASS__, 'validate_donation_form'));
        add_action('wp_ajax_nopriv_knot4_validate_donation_form', array(__CLASS__, 'validate_donation_form'));
        
        // Webhook handlers for payment gateways
        add_action('wp_ajax_knot4_stripe_webhook', array(__CLASS__, 'handle_stripe_webhook'));
        add_action('wp_ajax_nopriv_knot4_stripe_webhook', array(__CLASS__, 'handle_stripe_webhook'));
        
        // Recurring donation processing
        add_action('knot4_process_recurring_donations', array(__CLASS__, 'process_recurring_donations'));
        
        // Schedule recurring donation processing if not already scheduled
        if (!wp_next_scheduled('knot4_process_recurring_donations')) {
            wp_schedule_event(time(), 'daily', 'knot4_process_recurring_donations');
        }
    }
    
    /**
     * Create a new donation record
     */
    public static function create_donation($data) {
        global $wpdb;
        
        $defaults = array(
            'donor_id' => null,
            'form_id' => null,
            'campaign_id' => null,
            'reference_id' => Knot4_Utilities::generate_donation_reference(),
            'amount' => 0.00,
            'currency' => 'USD',
            'frequency' => 'once',
            'status' => 'pending',
            'payment_method' => null,
            'payment_gateway' => null,
            'gateway_transaction_id' => null,
            'donor_email' => '',
            'donor_first_name' => '',
            'donor_last_name' => '',
            'donor_address' => null,
            'donor_phone' => null,
            'is_anonymous' => false,
            'is_recurring' => false,
            'fund_designation' => null,
            'dedication_type' => null,
            'dedication_name' => null,
            'dedication_message' => null,
            'notes' => null,
        );
        
        $donation_data = wp_parse_args($data, $defaults);
        
        // Validate required fields
        if (empty($donation_data['donor_email']) || !is_email($donation_data['donor_email'])) {
            return new WP_Error('invalid_email', __('Valid email address is required.', 'knot4'));
        }
        
        if ($donation_data['amount'] <= 0) {
            return new WP_Error('invalid_amount', __('Donation amount must be greater than 0.', 'knot4'));
        }
        
        // Insert donation record
        $result = $wpdb->insert(
            $wpdb->prefix . 'knot4_donations',
            array(
                'donor_id' => $donation_data['donor_id'],
                'form_id' => $donation_data['form_id'],
                'campaign_id' => $donation_data['campaign_id'],
                'reference_id' => $donation_data['reference_id'],
                'amount' => $donation_data['amount'],
                'currency' => $donation_data['currency'],
                'frequency' => $donation_data['frequency'],
                'status' => $donation_data['status'],
                'payment_method' => $donation_data['payment_method'],
                'payment_gateway' => $donation_data['payment_gateway'],
                'gateway_transaction_id' => $donation_data['gateway_transaction_id'],
                'donor_email' => $donation_data['donor_email'],
                'donor_first_name' => $donation_data['donor_first_name'],
                'donor_last_name' => $donation_data['donor_last_name'],
                'donor_address' => $donation_data['donor_address'],
                'donor_phone' => $donation_data['donor_phone'],
                'is_anonymous' => $donation_data['is_anonymous'] ? 1 : 0,
                'is_recurring' => $donation_data['is_recurring'] ? 1 : 0,
                'fund_designation' => $donation_data['fund_designation'],
                'dedication_type' => $donation_data['dedication_type'],
                'dedication_name' => $donation_data['dedication_name'],
                'dedication_message' => $donation_data['dedication_message'],
                'notes' => $donation_data['notes'],
                'created_at' => current_time('mysql'),
            ),
            array(
                '%d', '%d', '%d', '%s', '%f', '%s', '%s', '%s', '%s', '%s', '%s',
                '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s'
            )
        );
        
        if ($result === false) {
            return new WP_Error('db_error', __('Failed to create donation record.', 'knot4'));
        }
        
        $donation_id = $wpdb->insert_id;
        
        // Create or update donor record
        $donor_id = self::create_or_update_donor($donation_data);
        if (!is_wp_error($donor_id) && $donor_id) {
            $wpdb->update(
                $wpdb->prefix . 'knot4_donations',
                array('donor_id' => $donor_id),
                array('id' => $donation_id),
                array('%d'),
                array('%d')
            );
        }
        
        // Log activity
        Knot4_Utilities::log_activity(
            'donation_created',
            sprintf(__('New donation created: %s', 'knot4'), Knot4_Utilities::format_currency($donation_data['amount'])),
            $donation_id
        );
        
        do_action('knot4_donation_created', $donation_id, $donation_data);
        
        return $donation_id;
    }
    
    /**
     * Create or update donor record
     */
    public static function create_or_update_donor($data) {
        // Check if donor exists by email
        $existing_donor = get_posts(array(
            'post_type' => 'knot4_donor',
            'meta_query' => array(
                array(
                    'key' => '_knot4_donor_email',
                    'value' => $data['donor_email'],
                    'compare' => '='
                )
            ),
            'posts_per_page' => 1,
        ));
        
        $donor_name = trim($data['donor_first_name'] . ' ' . $data['donor_last_name']);
        if (empty($donor_name)) {
            $donor_name = $data['donor_email'];
        }
        
        if (!empty($existing_donor)) {
            // Update existing donor
            $donor_id = $existing_donor[0]->ID;
            
            // Update meta fields
            update_post_meta($donor_id, '_knot4_donor_first_name', sanitize_text_field($data['donor_first_name']));
            update_post_meta($donor_id, '_knot4_donor_last_name', sanitize_text_field($data['donor_last_name']));
            update_post_meta($donor_id, '_knot4_donor_phone', sanitize_text_field($data['donor_phone']));
            update_post_meta($donor_id, '_knot4_donor_address', sanitize_textarea_field($data['donor_address']));
            
            // Update total donated amount
            $current_total = (float) get_post_meta($donor_id, '_knot4_total_donated', true);
            $new_total = $current_total + (float) $data['amount'];
            update_post_meta($donor_id, '_knot4_total_donated', $new_total);
            
            // Update donation count
            $current_count = (int) get_post_meta($donor_id, '_knot4_donation_count', true);
            update_post_meta($donor_id, '_knot4_donation_count', $current_count + 1);
            
            // Update last donation date
            update_post_meta($donor_id, '_knot4_last_donation_date', current_time('mysql'));
            
        } else {
            // Create new donor
            $donor_id = wp_insert_post(array(
                'post_type' => 'knot4_donor',
                'post_title' => $donor_name,
                'post_status' => 'publish',
                'meta_input' => array(
                    '_knot4_donor_email' => sanitize_email($data['donor_email']),
                    '_knot4_donor_first_name' => sanitize_text_field($data['donor_first_name']),
                    '_knot4_donor_last_name' => sanitize_text_field($data['donor_last_name']),
                    '_knot4_donor_phone' => sanitize_text_field($data['donor_phone']),
                    '_knot4_donor_address' => sanitize_textarea_field($data['donor_address']),
                    '_knot4_donor_type' => 'individual',
                    '_knot4_total_donated' => (float) $data['amount'],
                    '_knot4_donation_count' => 1,
                    '_knot4_first_donation_date' => current_time('mysql'),
                    '_knot4_last_donation_date' => current_time('mysql'),
                ),
            ));
            
            if (is_wp_error($donor_id)) {
                return $donor_id;
            }
        }
        
        return $donor_id;
    }
    
    /**
     * Get donation by ID
     */
    public static function get_donation($donation_id) {
        global $wpdb;
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}knot4_donations WHERE id = %d",
            $donation_id
        ));
    }
    
    /**
     * Update donation status
     */
    public static function update_donation_status($donation_id, $status, $gateway_transaction_id = null) {
        global $wpdb;
        
        $update_data = array(
            'status' => $status,
            'updated_at' => current_time('mysql'),
        );
        
        if ($gateway_transaction_id) {
            $update_data['gateway_transaction_id'] = $gateway_transaction_id;
        }
        
        $result = $wpdb->update(
            $wpdb->prefix . 'knot4_donations',
            $update_data,
            array('id' => $donation_id),
            array('%s', '%s', '%s'),
            array('%d')
        );
        
        if ($result !== false) {
            // Send thank you email if completed
            if ($status === 'completed') {
                self::send_donation_confirmation($donation_id);
            }
            
            // Log activity
            Knot4_Utilities::log_activity(
                'donation_status_updated',
                sprintf(__('Donation status updated to: %s', 'knot4'), $status),
                $donation_id
            );
            
            do_action('knot4_donation_status_updated', $donation_id, $status);
        }
        
        return $result;
    }
    
    /**
     * Send donation confirmation email
     */
    public static function send_donation_confirmation($donation_id) {
        $donation = self::get_donation($donation_id);
        if (!$donation) {
            return false;
        }
        
        // Get email template
        global $wpdb;
        $template = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}knot4_email_templates WHERE type = %s AND is_active = 1",
            'donation_thank_you'
        ));
        
        if (!$template) {
            return false;
        }
        
        // Replace template variables
        $org_settings = Knot4_Utilities::get_organization_settings();
        $variables = array(
            '{{donor_first_name}}' => $donation->donor_first_name,
            '{{donor_last_name}}' => $donation->donor_last_name,
            '{{amount}}' => Knot4_Utilities::format_currency($donation->amount, $donation->currency),
            '{{donation_date}}' => date_i18n(get_option('date_format'), strtotime($donation->created_at)),
            '{{reference_id}}' => $donation->reference_id,
            '{{organization_name}}' => $org_settings['organization_name'],
            '{{organization_email}}' => $org_settings['organization_email'],
            '{{organization_phone}}' => $org_settings['organization_phone'],
            '{{organization_address}}' => $org_settings['organization_address'],
        );
        
        $subject = str_replace(array_keys($variables), array_values($variables), $template->subject);
        $message = str_replace(array_keys($variables), array_values($variables), $template->content);
        
        $sent = Knot4_Utilities::send_notification($donation->donor_email, $subject, $message);
        
        if ($sent) {
            // Update donation record to mark receipt as sent
            global $wpdb;
            $wpdb->update(
                $wpdb->prefix . 'knot4_donations',
                array('receipt_sent_at' => current_time('mysql')),
                array('id' => $donation_id),
                array('%s'),
                array('%d')
            );
        }
        
        return $sent;
    }
    
    /**
     * AJAX handler for creating donation records (Stripe integration)
     */
    public static function ajax_create_donation() {
        check_ajax_referer('knot4_stripe_nonce', 'nonce');
        
        $form_data = array(
            'amount' => floatval($_POST['amount']),
            'currency' => sanitize_text_field($_POST['currency']) ?: 'USD',
            'frequency' => sanitize_text_field($_POST['frequency']) ?: 'once',
            'donor_email' => sanitize_email($_POST['donor_email']),
            'donor_first_name' => sanitize_text_field($_POST['donor_first_name']),
            'donor_last_name' => sanitize_text_field($_POST['donor_last_name']),
            'donor_phone' => sanitize_text_field($_POST['donor_phone']),
            'donor_address' => sanitize_textarea_field($_POST['donor_address']),
            'payment_gateway' => 'stripe',
            'payment_method' => 'card',
            'status' => 'pending',
            'form_id' => intval($_POST['form_id']) ?: null,
            'fund_designation' => sanitize_text_field($_POST['fund_designation']),
            'is_anonymous' => !empty($_POST['is_anonymous']),
            'is_recurring' => ($_POST['frequency'] !== 'once'),
            'notes' => sanitize_textarea_field($_POST['notes']),
        );
        
        // Create donation record
        $donation_id = self::create_donation($form_data);
        
        if (is_wp_error($donation_id)) {
            wp_send_json_error(array(
                'message' => $donation_id->get_error_message()
            ));
        }
        
        wp_send_json_success(array(
            'donation_id' => $donation_id,
            'message' => __('Donation record created successfully.', 'knot4')
        ));
    }
    
    /**
     * AJAX handler for donation processing
     */
    public static function process_donation() {
        check_ajax_referer('knot4_donation_nonce', 'nonce');
        
        $form_data = array(
            'amount' => floatval($_POST['amount']),
            'frequency' => sanitize_text_field($_POST['frequency']),
            'donor_email' => sanitize_email($_POST['donor_email']),
            'donor_first_name' => sanitize_text_field($_POST['donor_first_name']),
            'donor_last_name' => sanitize_text_field($_POST['donor_last_name']),
            'donor_phone' => sanitize_text_field($_POST['donor_phone']),
            'payment_method' => sanitize_text_field($_POST['payment_method']),
            'form_id' => intval($_POST['form_id']),
        );
        
        // Create donation record
        $donation_id = self::create_donation($form_data);
        
        if (is_wp_error($donation_id)) {
            wp_send_json_error(array(
                'message' => $donation_id->get_error_message()
            ));
        }
        
        // Process payment based on gateway
        $payment_result = self::process_payment($donation_id, $form_data);
        
        if (is_wp_error($payment_result)) {
            wp_send_json_error(array(
                'message' => $payment_result->get_error_message()
            ));
        }
        
        wp_send_json_success(array(
            'donation_id' => $donation_id,
            'message' => __('Thank you for your donation!', 'knot4'),
            'redirect_url' => $payment_result['redirect_url']
        ));
    }
    
    /**
     * Process payment through gateway
     */
    public static function process_payment($donation_id, $form_data) {
        $payment_settings = Knot4_Utilities::get_payment_settings();
        
        switch ($form_data['payment_method']) {
            case 'stripe':
                return self::process_stripe_payment($donation_id, $form_data, $payment_settings);
            case 'paypal':
                return self::process_paypal_payment($donation_id, $form_data, $payment_settings);
            default:
                return new WP_Error('invalid_payment_method', __('Invalid payment method.', 'knot4'));
        }
    }
    
    /**
     * Process Stripe payment (basic implementation)
     */
    private static function process_stripe_payment($donation_id, $form_data, $payment_settings) {
        // This would integrate with Stripe API
        // For now, return success for demo purposes
        return array(
            'redirect_url' => home_url('/donation-thank-you/'),
            'transaction_id' => 'stripe_' . time(),
        );
    }
    
    /**
     * Process PayPal payment (basic implementation)
     */
    private static function process_paypal_payment($donation_id, $form_data, $payment_settings) {
        // This would integrate with PayPal API
        // For now, return success for demo purposes
        return array(
            'redirect_url' => home_url('/donation-thank-you/'),
            'transaction_id' => 'paypal_' . time(),
        );
    }
    
    /**
     * Handle Stripe webhook
     */
    public static function handle_stripe_webhook() {
        // Stripe webhook handling would go here
        wp_die('Stripe webhook handled', '', array('response' => 200));
    }
    
    /**
     * Process recurring donations
     */
    public static function process_recurring_donations() {
        // Process scheduled recurring donations
        global $wpdb;
        
        $recurring_donations = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}knot4_recurring_donations 
             WHERE status = %s AND next_payment_date <= %s",
            'active',
            current_time('mysql')
        ));
        
        foreach ($recurring_donations as $recurring) {
            // Process each recurring donation
            self::process_single_recurring_donation($recurring);
        }
    }
    
    /**
     * Process a single recurring donation
     */
    private static function process_single_recurring_donation($recurring) {
        // Implementation would process the recurring payment
        // and create new donation records
        
        Knot4_Utilities::log_activity(
            'recurring_donation_processed',
            sprintf(__('Recurring donation processed for donor ID: %d', 'knot4'), $recurring->donor_id),
            $recurring->id
        );
    }
}