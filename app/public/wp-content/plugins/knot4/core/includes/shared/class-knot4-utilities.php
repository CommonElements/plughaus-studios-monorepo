<?php
/**
 * Shared utilities for Knot4
 * 
 * @package Knot4
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Knot4_Utilities {
    
    /**
     * Check if pro features are available
     */
    public static function is_pro() {
        $knot4 = Knot4::get_instance();
        return $knot4->is_pro();
    }
    
    /**
     * Format currency amount
     */
    public static function format_currency($amount, $currency = 'USD') {
        $currency_symbols = array(
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'CAD' => 'C$',
            'AUD' => 'A$',
        );
        
        $symbol = isset($currency_symbols[$currency]) ? $currency_symbols[$currency] : '$';
        return $symbol . number_format((float)$amount, 2);
    }
    
    /**
     * Format phone number
     */
    public static function format_phone($phone) {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (strlen($phone) == 10) {
            return sprintf('(%s) %s-%s',
                substr($phone, 0, 3),
                substr($phone, 3, 3),
                substr($phone, 6, 4)
            );
        }
        
        return $phone;
    }
    
    /**
     * Validate email address
     */
    public static function validate_email($email) {
        return is_email($email);
    }
    
    /**
     * Generate organization code
     */
    public static function generate_organization_code() {
        return 'ORG-' . strtoupper(wp_generate_password(8, false));
    }
    
    /**
     * Generate donation reference
     */
    public static function generate_donation_reference() {
        return 'DON-' . date('Y') . '-' . strtoupper(wp_generate_password(6, false));
    }
    
    /**
     * Generate event code
     */
    public static function generate_event_code() {
        return 'EVT-' . strtoupper(wp_generate_password(8, false));
    }
    
    /**
     * Get donation frequencies
     */
    public static function get_donation_frequencies() {
        return array(
            'once' => __('One-time', 'knot4'),
            'weekly' => __('Weekly', 'knot4'),
            'monthly' => __('Monthly', 'knot4'),
            'quarterly' => __('Quarterly', 'knot4'),
            'annually' => __('Annually', 'knot4'),
        );
    }
    
    /**
     * Get donation statuses
     */
    public static function get_donation_statuses() {
        return array(
            'pending' => __('Pending', 'knot4'),
            'completed' => __('Completed', 'knot4'),
            'failed' => __('Failed', 'knot4'),
            'cancelled' => __('Cancelled', 'knot4'),
            'refunded' => __('Refunded', 'knot4'),
        );
    }
    
    /**
     * Get event types
     */
    public static function get_event_types() {
        return array(
            'fundraiser' => __('Fundraising Event', 'knot4'),
            'meeting' => __('Meeting', 'knot4'),
            'volunteer' => __('Volunteer Event', 'knot4'),
            'social' => __('Social Event', 'knot4'),
            'educational' => __('Educational', 'knot4'),
            'other' => __('Other', 'knot4'),
        );
    }
    
    /**
     * Get donor types
     */
    public static function get_donor_types() {
        return array(
            'individual' => __('Individual', 'knot4'),
            'family' => __('Family', 'knot4'),
            'business' => __('Business', 'knot4'),
            'foundation' => __('Foundation', 'knot4'),
            'organization' => __('Organization', 'knot4'),
        );
    }
    
    /**
     * Get communication preferences
     */
    public static function get_communication_preferences() {
        return array(
            'email' => __('Email', 'knot4'),
            'mail' => __('Physical Mail', 'knot4'),
            'phone' => __('Phone', 'knot4'),
            'text' => __('Text/SMS', 'knot4'),
        );
    }
    
    /**
     * Calculate donation total for period
     */
    public static function calculate_donation_total($start_date = null, $end_date = null, $status = 'completed') {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'knot4_donations';
        
        $sql = "SELECT SUM(amount) FROM {$table_name} WHERE status = %s";
        $params = array($status);
        
        if ($start_date) {
            $sql .= " AND created_at >= %s";
            $params[] = $start_date;
        }
        
        if ($end_date) {
            $sql .= " AND created_at <= %s";
            $params[] = $end_date;
        }
        
        return (float) $wpdb->get_var($wpdb->prepare($sql, $params));
    }
    
    /**
     * Get organization settings
     */
    public static function get_organization_settings() {
        $defaults = array(
            'organization_name' => get_bloginfo('name'),
            'organization_email' => get_option('admin_email'),
            'organization_phone' => '',
            'organization_address' => '',
            'tax_id' => '',
            'currency' => 'USD',
            'timezone' => get_option('timezone_string', 'America/New_York'),
            'donation_page' => 0,
            'thank_you_page' => 0,
            'receipt_template' => 'default',
        );
        
        $settings = get_option('knot4_organization_settings', array());
        return wp_parse_args($settings, $defaults);
    }
    
    /**
     * Get page settings
     */
    public static function get_page_settings() {
        $defaults = array(
            'donation_page' => 0,
            'donor_portal_page' => 0,
            'events_page' => 0,
            'volunteer_page' => 0,
            'thank_you_page' => 0,
            'newsletter_page' => 0,
        );
        
        $settings = get_option('knot4_page_settings', array());
        return wp_parse_args($settings, $defaults);
    }
    
    /**
     * Get payment gateway settings
     */
    public static function get_payment_settings() {
        $defaults = array(
            'enabled_gateways' => array('stripe'),
            'stripe_test_publishable_key' => '',
            'stripe_test_secret_key' => '',
            'stripe_test_webhook_secret' => '',
            'stripe_live_publishable_key' => '',
            'stripe_live_secret_key' => '',
            'stripe_live_webhook_secret' => '',
            'paypal_client_id' => '',
            'paypal_client_secret' => '',
            'test_mode' => true,
        );
        
        $settings = get_option('knot4_payment_settings', array());
        return wp_parse_args($settings, $defaults);
    }
    
    /**
     * Get email settings
     */
    public static function get_email_settings() {
        $defaults = array(
            'from_name' => get_bloginfo('name'),
            'from_email' => get_option('admin_email'),
            'reply_to' => '',
            'templates' => array(),
        );
        
        $settings = get_option('knot4_email_settings', array());
        return wp_parse_args($settings, $defaults);
    }
    
    /**
     * Log activity
     */
    public static function log_activity($type, $message, $object_id = 0, $user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        global $wpdb;
        
        $wpdb->insert(
            $wpdb->prefix . 'knot4_activity_log',
            array(
                'type' => $type,
                'message' => $message,
                'object_id' => $object_id,
                'user_id' => $user_id,
                'created_at' => current_time('mysql'),
            ),
            array('%s', '%s', '%d', '%d', '%s')
        );
    }
    
    /**
     * Send notification email
     */
    public static function send_notification($to, $subject, $message, $headers = array()) {
        $org_settings = self::get_organization_settings();
        
        $default_headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $org_settings['organization_name'] . ' <' . $org_settings['organization_email'] . '>',
        );
        
        $headers = array_merge($default_headers, $headers);
        
        return wp_mail($to, $subject, $message, $headers);
    }
    
    /**
     * Generate receipt PDF (pro feature)
     */
    public static function generate_receipt_pdf($donation_id) {
        if (!self::is_pro()) {
            return false;
        }
        
        // Pro feature - PDF generation
        return apply_filters('knot4_generate_receipt_pdf', false, $donation_id);
    }
    
    /**
     * Check user capabilities for nonprofit management
     */
    public static function current_user_can_manage_nonprofit() {
        return current_user_can('manage_knot4_nonprofit') || current_user_can('manage_options');
    }
    
    /**
     * Check user capabilities for viewing nonprofit data
     */
    public static function current_user_can_view_nonprofit() {
        return current_user_can('view_knot4_nonprofit') || self::current_user_can_manage_nonprofit();
    }
}