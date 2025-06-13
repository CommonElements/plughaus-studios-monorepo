<?php
/**
 * Frontend functionality for Knot4
 * 
 * @package Knot4
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Knot4_Public {
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Frontend form processing
        add_action('wp_ajax_knot4_submit_donation', array($this, 'handle_donation_submission'));
        add_action('wp_ajax_nopriv_knot4_submit_donation', array($this, 'handle_donation_submission'));
        
        add_action('wp_ajax_knot4_register_event', array($this, 'handle_event_registration'));
        add_action('wp_ajax_nopriv_knot4_register_event', array($this, 'handle_event_registration'));
        
        add_action('wp_ajax_knot4_submit_volunteer', array($this, 'handle_volunteer_submission'));
        add_action('wp_ajax_nopriv_knot4_submit_volunteer', array($this, 'handle_volunteer_submission'));
        
        // Custom page templates
        add_filter('template_include', array($this, 'load_custom_templates'));
        
        // Body classes for styling
        add_filter('body_class', array($this, 'add_body_classes'));
        
        // Login/logout redirects for donor portal
        add_filter('login_redirect', array($this, 'donor_login_redirect'), 10, 3);
    }
    
    /**
     * Enqueue frontend styles
     */
    public function enqueue_styles() {
        // Main frontend stylesheet
        wp_enqueue_style(
            'knot4-public',
            KNOT4_PLUGIN_URL . 'core/assets/css/public.css',
            array(),
            KNOT4_VERSION
        );
        
        // Donation form specific styles
        if ($this->is_donation_page()) {
            wp_enqueue_style(
                'knot4-donations',
                KNOT4_PLUGIN_URL . 'core/assets/css/donations.css',
                array('knot4-public'),
                KNOT4_VERSION
            );
        }
        
        // Event styles
        if ($this->is_event_page()) {
            wp_enqueue_style(
                'knot4-events',
                KNOT4_PLUGIN_URL . 'core/assets/css/events.css',
                array('knot4-public'),
                KNOT4_VERSION
            );
        }
        
        // Donor portal styles
        if ($this->is_donor_portal()) {
            wp_enqueue_style(
                'knot4-portal',
                KNOT4_PLUGIN_URL . 'core/assets/css/donor-portal.css',
                array('knot4-public'),
                KNOT4_VERSION
            );
        }
    }
    
    /**
     * Enqueue frontend scripts
     */
    public function enqueue_scripts() {
        // Main frontend script
        wp_enqueue_script(
            'knot4-public',
            KNOT4_PLUGIN_URL . 'core/assets/js/public.js',
            array('jquery'),
            KNOT4_VERSION,
            true
        );
        
        // Localize script with data
        wp_localize_script('knot4-public', 'knot4_public', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'api_url' => home_url('/wp-json/knot4/v1/'),
            'nonce' => wp_create_nonce('knot4_public_nonce'),
            'strings' => array(
                'processing' => __('Processing...', 'knot4'),
                'success' => __('Success!', 'knot4'),
                'error' => __('An error occurred. Please try again.', 'knot4'),
                'required_field' => __('This field is required.', 'knot4'),
                'invalid_email' => __('Please enter a valid email address.', 'knot4'),
                'invalid_amount' => __('Please enter a valid donation amount.', 'knot4'),
                'confirm_cancel' => __('Are you sure you want to cancel this recurring donation?', 'knot4'),
                'donation_success' => __('Thank you for your generous donation!', 'knot4'),
                'registration_success' => __('You have been successfully registered for this event!', 'knot4'),
            ),
            'currency' => array(
                'symbol' => '$',
                'position' => 'before',
                'decimals' => 2,
            ),
            'payment' => array(
                'test_mode' => $this->is_test_mode(),
                'available_gateways' => $this->get_available_payment_gateways(),
            )
        ));
        
        // Donation form specific scripts
        if ($this->is_donation_page()) {
            wp_enqueue_script(
                'knot4-donations',
                KNOT4_PLUGIN_URL . 'core/assets/js/donations.js',
                array('knot4-public', 'stripe-js'),
                KNOT4_VERSION,
                true
            );
            
            // Stripe.js (if Stripe is enabled)
            if ($this->is_stripe_enabled()) {
                wp_enqueue_script('stripe-js', 'https://js.stripe.com/v3/', array(), null, true);
            }
        }
        
        // Event registration scripts
        if ($this->is_event_page()) {
            wp_enqueue_script(
                'knot4-events',
                KNOT4_PLUGIN_URL . 'core/assets/js/events.js',
                array('knot4-public'),
                KNOT4_VERSION,
                true
            );
        }
        
        // Donor portal scripts
        if ($this->is_donor_portal()) {
            wp_enqueue_script(
                'knot4-portal',
                KNOT4_PLUGIN_URL . 'core/assets/js/donor-portal.js',
                array('knot4-public'),
                KNOT4_VERSION,
                true
            );
        }
    }
    
    /**
     * Handle donation form submission
     */
    public function handle_donation_submission() {
        check_ajax_referer('knot4_public_nonce', 'nonce');
        
        // Validate and sanitize form data
        $form_data = array(
            'amount' => floatval($_POST['amount']),
            'frequency' => sanitize_text_field($_POST['frequency']),
            'donor_email' => sanitize_email($_POST['donor_email']),
            'donor_first_name' => sanitize_text_field($_POST['donor_first_name']),
            'donor_last_name' => sanitize_text_field($_POST['donor_last_name']),
            'donor_phone' => sanitize_text_field($_POST['donor_phone']),
            'donor_address' => sanitize_textarea_field($_POST['donor_address']),
            'payment_method' => sanitize_text_field($_POST['payment_method']),
            'form_id' => intval($_POST['form_id']),
            'campaign_id' => intval($_POST['campaign_id']),
            'fund_designation' => sanitize_text_field($_POST['fund_designation']),
            'is_anonymous' => !empty($_POST['is_anonymous']),
            'dedication_type' => sanitize_text_field($_POST['dedication_type']),
            'dedication_name' => sanitize_text_field($_POST['dedication_name']),
            'dedication_message' => sanitize_textarea_field($_POST['dedication_message']),
            'cover_fees' => !empty($_POST['cover_fees']),
            'opt_in_newsletter' => !empty($_POST['opt_in_newsletter']),
        );
        
        // Server-side validation
        $errors = array();
        
        if (empty($form_data['donor_email']) || !is_email($form_data['donor_email'])) {
            $errors[] = __('Valid email address is required.', 'knot4');
        }
        
        if ($form_data['amount'] <= 0) {
            $errors[] = __('Donation amount must be greater than 0.', 'knot4');
        }
        
        if (empty($form_data['donor_first_name'])) {
            $errors[] = __('First name is required.', 'knot4');
        }
        
        if (empty($form_data['donor_last_name'])) {
            $errors[] = __('Last name is required.', 'knot4');
        }
        
        if (!empty($errors)) {
            wp_send_json_error(array(
                'message' => implode('<br>', $errors)
            ));
        }
        
        // Calculate fees if opted to cover
        if ($form_data['cover_fees']) {
            $form_data['amount'] = $this->calculate_amount_with_fees($form_data['amount'], $form_data['payment_method']);
        }
        
        // Create donation record
        $donation_id = Knot4_Donations::create_donation($form_data);
        
        if (is_wp_error($donation_id)) {
            wp_send_json_error(array(
                'message' => $donation_id->get_error_message()
            ));
        }
        
        // Process payment
        $payment_result = $this->process_payment($donation_id, $form_data);
        
        if (is_wp_error($payment_result)) {
            wp_send_json_error(array(
                'message' => $payment_result->get_error_message()
            ));
        }
        
        // Handle newsletter opt-in
        if ($form_data['opt_in_newsletter']) {
            $this->handle_newsletter_signup($form_data['donor_email'], $form_data['donor_first_name'], $form_data['donor_last_name']);
        }
        
        // Success response
        wp_send_json_success(array(
            'donation_id' => $donation_id,
            'message' => __('Thank you for your generous donation!', 'knot4'),
            'redirect_url' => $payment_result['redirect_url'] ?? home_url('/donation-thank-you/'),
            'payment_data' => $payment_result
        ));
    }
    
    /**
     * Handle event registration
     */
    public function handle_event_registration() {
        check_ajax_referer('knot4_public_nonce', 'nonce');
        
        $registration_data = array(
            'event_id' => intval($_POST['event_id']),
            'attendee_email' => sanitize_email($_POST['attendee_email']),
            'attendee_first_name' => sanitize_text_field($_POST['attendee_first_name']),
            'attendee_last_name' => sanitize_text_field($_POST['attendee_last_name']),
            'attendee_phone' => sanitize_text_field($_POST['attendee_phone']),
            'ticket_quantity' => intval($_POST['ticket_quantity']),
            'registration_type' => sanitize_text_field($_POST['registration_type']),
            'special_requirements' => sanitize_textarea_field($_POST['special_requirements']),
            'payment_method' => sanitize_text_field($_POST['payment_method']),
        );
        
        // Validate registration data
        $errors = array();
        
        if (empty($registration_data['event_id'])) {
            $errors[] = __('Event ID is required.', 'knot4');
        }
        
        if (empty($registration_data['attendee_email']) || !is_email($registration_data['attendee_email'])) {
            $errors[] = __('Valid email address is required.', 'knot4');
        }
        
        if (empty($registration_data['attendee_first_name'])) {
            $errors[] = __('First name is required.', 'knot4');
        }
        
        if (empty($registration_data['attendee_last_name'])) {
            $errors[] = __('Last name is required.', 'knot4');
        }
        
        if ($registration_data['ticket_quantity'] <= 0) {
            $errors[] = __('Ticket quantity must be greater than 0.', 'knot4');
        }
        
        // Check event capacity
        if (!$this->check_event_capacity($registration_data['event_id'], $registration_data['ticket_quantity'])) {
            $errors[] = __('Sorry, this event is at capacity.', 'knot4');
        }
        
        if (!empty($errors)) {
            wp_send_json_error(array(
                'message' => implode('<br>', $errors)
            ));
        }
        
        // Calculate total amount
        $event_price = $this->get_event_price($registration_data['event_id'], $registration_data['registration_type']);
        $registration_data['total_amount'] = $event_price * $registration_data['ticket_quantity'];
        
        // Create registration record
        $registration_id = $this->create_event_registration($registration_data);
        
        if (is_wp_error($registration_id)) {
            wp_send_json_error(array(
                'message' => $registration_id->get_error_message()
            ));
        }
        
        // Process payment if required
        if ($registration_data['total_amount'] > 0) {
            $payment_result = $this->process_event_payment($registration_id, $registration_data);
            
            if (is_wp_error($payment_result)) {
                wp_send_json_error(array(
                    'message' => $payment_result->get_error_message()
                ));
            }
        }
        
        // Send confirmation email
        $this->send_event_confirmation($registration_id);
        
        wp_send_json_success(array(
            'registration_id' => $registration_id,
            'message' => __('Registration successful! Check your email for confirmation.', 'knot4'),
            'redirect_url' => home_url('/event-registration-success/')
        ));
    }
    
    /**
     * Handle volunteer form submission
     */
    public function handle_volunteer_submission() {
        check_ajax_referer('knot4_public_nonce', 'nonce');
        
        $volunteer_data = array(
            'volunteer_email' => sanitize_email($_POST['volunteer_email']),
            'volunteer_first_name' => sanitize_text_field($_POST['volunteer_first_name']),
            'volunteer_last_name' => sanitize_text_field($_POST['volunteer_last_name']),
            'volunteer_phone' => sanitize_text_field($_POST['volunteer_phone']),
            'volunteer_interests' => array_map('sanitize_text_field', $_POST['volunteer_interests']),
            'volunteer_availability' => sanitize_textarea_field($_POST['volunteer_availability']),
            'volunteer_experience' => sanitize_textarea_field($_POST['volunteer_experience']),
            'volunteer_message' => sanitize_textarea_field($_POST['volunteer_message']),
        );
        
        // Create volunteer record
        $submission_id = $this->create_volunteer_submission($volunteer_data);
        
        if (is_wp_error($submission_id)) {
            wp_send_json_error(array(
                'message' => $submission_id->get_error_message()
            ));
        }
        
        // Send notification to admin
        $this->send_volunteer_notification($submission_id);
        
        wp_send_json_success(array(
            'message' => __('Thank you for your interest in volunteering! We will be in touch soon.', 'knot4')
        ));
    }
    
    /**
     * Load custom page templates
     */
    public function load_custom_templates($template) {
        global $post;
        
        if (!$post) {
            return $template;
        }
        
        // Donor portal template
        if ($this->is_donor_portal_page($post->ID)) {
            $custom_template = KNOT4_PLUGIN_DIR . 'core/templates/donor-portal.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }
        
        // Event single template
        if (is_singular('knot4_event')) {
            $custom_template = KNOT4_PLUGIN_DIR . 'core/templates/single-event.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }
        
        // Campaign single template
        if (is_singular('knot4_campaign')) {
            $custom_template = KNOT4_PLUGIN_DIR . 'core/templates/single-campaign.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }
        
        return $template;
    }
    
    /**
     * Add custom body classes
     */
    public function add_body_classes($classes) {
        if ($this->is_donation_page()) {
            $classes[] = 'knot4-donation-page';
        }
        
        if ($this->is_event_page()) {
            $classes[] = 'knot4-event-page';
        }
        
        if ($this->is_donor_portal()) {
            $classes[] = 'knot4-donor-portal';
        }
        
        if (Knot4_Utilities::is_pro()) {
            $classes[] = 'knot4-pro';
        } else {
            $classes[] = 'knot4-free';
        }
        
        return $classes;
    }
    
    /**
     * Check if current page is a donation page
     */
    private function is_donation_page() {
        global $post;
        
        if (!$post) {
            return false;
        }
        
        // Check if page contains donation shortcode
        return has_shortcode($post->post_content, 'knot4_donation_form') ||
               has_shortcode($post->post_content, 'knot4_campaign_form');
    }
    
    /**
     * Check if current page is an event page
     */
    private function is_event_page() {
        return is_singular('knot4_event') || 
               is_post_type_archive('knot4_event') ||
               has_shortcode(get_post()->post_content ?? '', 'knot4_events');
    }
    
    /**
     * Check if current page is donor portal
     */
    private function is_donor_portal() {
        global $post;
        
        if (!$post) {
            return false;
        }
        
        return has_shortcode($post->post_content, 'knot4_donor_portal') ||
               $this->is_donor_portal_page($post->ID);
    }
    
    /**
     * Check if specific page is designated as donor portal
     */
    private function is_donor_portal_page($page_id) {
        $org_settings = Knot4_Utilities::get_organization_settings();
        return !empty($org_settings['donor_portal_page']) && $org_settings['donor_portal_page'] == $page_id;
    }
    
    /**
     * Check if payment gateway is enabled
     */
    private function is_stripe_enabled() {
        $payment_settings = Knot4_Utilities::get_payment_settings();
        return in_array('stripe', $payment_settings['enabled_gateways']) && 
               !empty($payment_settings['stripe_publishable_key']);
    }
    
    /**
     * Check if in test mode
     */
    private function is_test_mode() {
        $payment_settings = Knot4_Utilities::get_payment_settings();
        return !empty($payment_settings['test_mode']);
    }
    
    /**
     * Get available payment gateways
     */
    private function get_available_payment_gateways() {
        $payment_settings = Knot4_Utilities::get_payment_settings();
        return $payment_settings['enabled_gateways'] ?? array('stripe');
    }
    
    /**
     * Calculate amount with processing fees
     */
    private function calculate_amount_with_fees($amount, $payment_method) {
        $fee_rate = 0.029; // 2.9% default
        $fee_fixed = 0.30; // $0.30 fixed
        
        // Adjust based on payment method
        switch ($payment_method) {
            case 'stripe':
                $fee_rate = 0.029;
                $fee_fixed = 0.30;
                break;
            case 'paypal':
                $fee_rate = 0.0349;
                $fee_fixed = 0.49;
                break;
        }
        
        return $amount + ($amount * $fee_rate) + $fee_fixed;
    }
    
    /**
     * Process payment for donation
     */
    private function process_payment($donation_id, $form_data) {
        // Delegate to donations module
        return Knot4_Donations::process_payment($donation_id, $form_data);
    }
    
    /**
     * Handle newsletter signup
     */
    private function handle_newsletter_signup($email, $first_name, $last_name) {
        // Integration with email marketing platforms would go here
        do_action('knot4_newsletter_signup', $email, $first_name, $last_name);
    }
    
    /**
     * Create event registration record
     */
    private function create_event_registration($data) {
        global $wpdb;
        
        $result = $wpdb->insert(
            $wpdb->prefix . 'knot4_event_registrations',
            array(
                'event_id' => $data['event_id'],
                'attendee_email' => $data['attendee_email'],
                'attendee_first_name' => $data['attendee_first_name'],
                'attendee_last_name' => $data['attendee_last_name'],
                'attendee_phone' => $data['attendee_phone'],
                'registration_type' => $data['registration_type'],
                'ticket_quantity' => $data['ticket_quantity'],
                'total_amount' => $data['total_amount'],
                'special_requirements' => $data['special_requirements'],
                'registration_source' => 'website',
                'created_at' => current_time('mysql'),
            ),
            array('%d', '%s', '%s', '%s', '%s', '%s', '%d', '%f', '%s', '%s', '%s')
        );
        
        if ($result === false) {
            return new WP_Error('db_error', __('Failed to create registration record.', 'knot4'));
        }
        
        return $wpdb->insert_id;
    }
    
    /**
     * Check event capacity
     */
    private function check_event_capacity($event_id, $requested_tickets) {
        $max_capacity = get_post_meta($event_id, '_knot4_event_max_capacity', true);
        
        if (empty($max_capacity)) {
            return true; // No capacity limit
        }
        
        global $wpdb;
        $current_registrations = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(ticket_quantity) FROM {$wpdb->prefix}knot4_event_registrations 
             WHERE event_id = %d AND payment_status != 'cancelled'",
            $event_id
        ));
        
        return ($current_registrations + $requested_tickets) <= $max_capacity;
    }
    
    /**
     * Get event price
     */
    private function get_event_price($event_id, $registration_type = 'general') {
        $base_price = get_post_meta($event_id, '_knot4_event_price', true);
        
        // Check for different pricing tiers
        $pricing_tiers = get_post_meta($event_id, '_knot4_event_pricing_tiers', true);
        
        if (!empty($pricing_tiers) && isset($pricing_tiers[$registration_type])) {
            return floatval($pricing_tiers[$registration_type]);
        }
        
        return floatval($base_price);
    }
    
    /**
     * Process event payment
     */
    private function process_event_payment($registration_id, $data) {
        // Similar to donation payment processing
        return array(
            'redirect_url' => home_url('/event-registration-success/'),
            'transaction_id' => 'event_' . time(),
        );
    }
    
    /**
     * Send event confirmation email
     */
    private function send_event_confirmation($registration_id) {
        // Implementation would send confirmation email
        return true;
    }
    
    /**
     * Create volunteer submission
     */
    private function create_volunteer_submission($data) {
        global $wpdb;
        
        $result = $wpdb->insert(
            $wpdb->prefix . 'knot4_form_submissions',
            array(
                'form_type' => 'volunteer',
                'submitter_email' => $data['volunteer_email'],
                'submitter_name' => $data['volunteer_first_name'] . ' ' . $data['volunteer_last_name'],
                'submission_data' => json_encode($data),
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'created_at' => current_time('mysql'),
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%s')
        );
        
        if ($result === false) {
            return new WP_Error('db_error', __('Failed to create volunteer submission.', 'knot4'));
        }
        
        return $wpdb->insert_id;
    }
    
    /**
     * Send volunteer notification to admin
     */
    private function send_volunteer_notification($submission_id) {
        $org_settings = Knot4_Utilities::get_organization_settings();
        
        $subject = __('New Volunteer Application', 'knot4');
        $message = sprintf(__('A new volunteer application has been submitted. View details in your Knot4 dashboard.', 'knot4'));
        
        return Knot4_Utilities::send_notification($org_settings['organization_email'], $subject, $message);
    }
    
    /**
     * Donor login redirect
     */
    public function donor_login_redirect($redirect_to, $request, $user) {
        if (is_wp_error($user)) {
            return $redirect_to;
        }
        
        // Check if user should be redirected to donor portal
        $org_settings = Knot4_Utilities::get_organization_settings();
        if (!empty($org_settings['donor_portal_page']) && 
            user_can($user, 'knot4_donor') && 
            strpos($request, 'knot4') !== false) {
            return get_permalink($org_settings['donor_portal_page']);
        }
        
        return $redirect_to;
    }
}