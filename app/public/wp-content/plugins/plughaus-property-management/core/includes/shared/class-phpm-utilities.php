<?php
/**
 * Shared utilities for PlugHaus Property Management
 * Adapted from PropPlugs utilities with enhancements
 *
 * @package PlugHausPropertyManagement
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * PlugHaus Property Management Utilities Class
 */
class PHPM_Utilities {
    
    /**
     * Initialize utilities hooks
     */
    public static function init() {
        add_action('wp_ajax_phpm_dismiss_sample_data_notice', array(__CLASS__, 'ajax_dismiss_sample_data_notice'));
        add_action('wp_ajax_phpm_dismiss_sample_removal_notice', array(__CLASS__, 'ajax_dismiss_sample_removal_notice'));
    }

    /**
     * Format currency amount
     *
     * @param float $amount
     * @param string $currency
     * @return string
     */
    public static function format_currency($amount, $currency = 'USD') {
        $symbols = array(
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'CAD' => 'C$',
            'AUD' => 'A$',
            'JPY' => '¥',
            'CHF' => 'CHF',
            'SEK' => 'kr',
            'NOK' => 'kr',
            'DKK' => 'kr',
        );

        $symbol = isset($symbols[$currency]) ? $symbols[$currency] : '$';
        
        // Handle JPY (no decimals)
        $decimals = ($currency === 'JPY') ? 0 : 2;
        
        return $symbol . number_format($amount, $decimals);
    }

    /**
     * Generate unique property code
     *
     * @param string $prefix
     * @return string
     */
    public static function generate_property_code($prefix = 'PHPM') {
        return $prefix . '-' . strtoupper(wp_generate_password(8, false));
    }

    /**
     * Generate unique unit identifier
     *
     * @param int $property_id
     * @param string $unit_number
     * @return string
     */
    public static function generate_unit_code($property_id, $unit_number = '') {
        $property_code = get_post_meta($property_id, '_phpm_property_code', true);
        if (empty($property_code)) {
            $property_code = self::generate_property_code();
            update_post_meta($property_id, '_phpm_property_code', $property_code);
        }
        
        $unit_suffix = !empty($unit_number) ? '-' . strtoupper($unit_number) : '-' . wp_generate_password(4, false);
        return $property_code . $unit_suffix;
    }

    /**
     * Validate email address
     *
     * @param string $email
     * @return bool
     */
    public static function validate_email($email) {
        return is_email($email) !== false;
    }

    /**
     * Validate phone number (flexible international format)
     *
     * @param string $phone
     * @return bool
     */
    public static function validate_phone($phone) {
        // Remove all non-numeric characters except +
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Must be at least 10 digits (US minimum) or start with + for international
        if (strpos($phone, '+') === 0) {
            return strlen(str_replace('+', '', $phone)) >= 10;
        }
        
        return strlen($phone) >= 10;
    }

    /**
     * Format phone number for display
     *
     * @param string $phone
     * @param string $format
     * @return string
     */
    public static function format_phone($phone, $format = 'us') {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if ($format === 'us' && strlen($phone) === 10) {
            return sprintf('(%s) %s-%s', 
                substr($phone, 0, 3),
                substr($phone, 3, 3),
                substr($phone, 6, 4)
            );
        }
        
        return $phone;
    }

    /**
     * Format address for display
     *
     * @param array $address
     * @param string $format (oneline|multiline)
     * @return string
     */
    public static function format_address($address, $format = 'multiline') {
        $parts = array();
        
        // Street address
        if (!empty($address['street'])) {
            $parts[] = $address['street'];
        }
        
        // Unit/Apartment
        if (!empty($address['unit'])) {
            $parts[] = 'Unit ' . $address['unit'];
        }
        
        // City, State ZIP
        $city_state_zip = array();
        if (!empty($address['city'])) {
            $city_state_zip[] = $address['city'];
        }
        if (!empty($address['state'])) {
            $city_state_zip[] = $address['state'];
        }
        if (!empty($address['zip'])) {
            $city_state_zip[] = $address['zip'];
        }
        
        if (!empty($city_state_zip)) {
            $parts[] = implode(', ', $city_state_zip);
        }
        
        $separator = ($format === 'oneline') ? ', ' : '<br>';
        return implode($separator, $parts);
    }

    /**
     * Calculate lease term in months
     *
     * @param string $start_date
     * @param string $end_date
     * @return int
     */
    public static function calculate_lease_term($start_date, $end_date) {
        try {
            $start = new DateTime($start_date);
            $end = new DateTime($end_date);
            $interval = $start->diff($end);
            
            return ($interval->y * 12) + $interval->m;
        } catch (Exception $e) {
            self::log('Error calculating lease term: ' . $e->getMessage(), 'error');
            return 0;
        }
    }

    /**
     * Calculate days until lease expiration
     *
     * @param string $end_date
     * @return int (negative if expired)
     */
    public static function days_until_lease_expiration($end_date) {
        try {
            $end = new DateTime($end_date);
            $now = new DateTime();
            $interval = $now->diff($end);
            
            return $interval->invert ? -$interval->days : $interval->days;
        } catch (Exception $e) {
            self::log('Error calculating lease expiration: ' . $e->getMessage(), 'error');
            return 0;
        }
    }

    /**
     * Check if user has PHPM capability
     *
     * @param string $capability
     * @return bool
     */
    public static function user_can($capability = 'manage_properties') {
        return current_user_can($capability) || current_user_can('manage_options');
    }

    /**
     * Get property types
     *
     * @return array
     */
    public static function get_property_types() {
        $types = array(
            'apartment' => __('Apartment Building', 'plughaus-property'),
            'house' => __('Single Family House', 'plughaus-property'),
            'condo' => __('Condominium', 'plughaus-property'),
            'townhouse' => __('Townhouse', 'plughaus-property'),
            'duplex' => __('Duplex', 'plughaus-property'),
            'commercial' => __('Commercial', 'plughaus-property'),
            'industrial' => __('Industrial', 'plughaus-property'),
            'land' => __('Land/Vacant Lot', 'plughaus-property'),
            'mixed_use' => __('Mixed Use', 'plughaus-property'),
            'other' => __('Other', 'plughaus-property'),
        );

        return apply_filters('phpm_property_types', $types);
    }

    /**
     * Get lease statuses
     *
     * @return array
     */
    public static function get_lease_statuses() {
        $statuses = array(
            'active' => __('Active', 'plughaus-property'),
            'pending' => __('Pending', 'plughaus-property'),
            'expired' => __('Expired', 'plughaus-property'),
            'terminated' => __('Terminated', 'plughaus-property'),
            'renewal' => __('Renewal', 'plughaus-property'),
            'hold' => __('On Hold', 'plughaus-property'),
        );

        return apply_filters('phpm_lease_statuses', $statuses);
    }

    /**
     * Get maintenance request priorities
     *
     * @return array
     */
    public static function get_maintenance_priorities() {
        $priorities = array(
            'low' => __('Low', 'plughaus-property'),
            'normal' => __('Normal', 'plughaus-property'),
            'high' => __('High', 'plughaus-property'),
            'urgent' => __('Urgent', 'plughaus-property'),
            'emergency' => __('Emergency', 'plughaus-property'),
        );

        return apply_filters('phpm_maintenance_priorities', $priorities);
    }

    /**
     * Get maintenance request statuses
     *
     * @return array
     */
    public static function get_maintenance_statuses() {
        $statuses = array(
            'open' => __('Open', 'plughaus-property'),
            'in_progress' => __('In Progress', 'plughaus-property'),
            'pending_parts' => __('Pending Parts', 'plughaus-property'),
            'completed' => __('Completed', 'plughaus-property'),
            'closed' => __('Closed', 'plughaus-property'),
            'cancelled' => __('Cancelled', 'plughaus-property'),
        );

        return apply_filters('phpm_maintenance_statuses', $statuses);
    }

    /**
     * Log PHPM activity
     *
     * @param string $message
     * @param string $level (info|warning|error|debug)
     * @param array $context
     */
    public static function log($message, $level = 'info', $context = array()) {
        if (defined('PHPM_DEBUG') && PHPM_DEBUG) {
            $log_message = '[PHPM ' . strtoupper($level) . '] ' . $message;
            if (!empty($context)) {
                $log_message .= ' Context: ' . wp_json_encode($context);
            }
            error_log($log_message);
        }

        // Also store in WordPress option for admin viewing (if debug mode)
        if (current_user_can('manage_options') && $level === 'error') {
            $error_logs = get_option('phpm_error_logs', array());
            $error_logs[] = array(
                'message' => $message,
                'level' => $level,
                'context' => $context,
                'timestamp' => current_time('mysql'),
                'user_id' => get_current_user_id()
            );
            
            // Keep only last 50 error logs
            if (count($error_logs) > 50) {
                $error_logs = array_slice($error_logs, -50);
            }
            
            update_option('phpm_error_logs', $error_logs);
        }
    }

    /**
     * Get Pro version status
     *
     * @return bool
     */
    public static function is_pro() {
        // Check if this is the pro version with license validation
        if (class_exists('PHPM_License_Manager')) {
            return PHPM_License_Manager::is_valid();
        }
        
        return false;
    }

    /**
     * Get Pro feature availability
     *
     * @param string $feature
     * @return bool
     */
    public static function is_pro_feature_available($feature) {
        if (!self::is_pro()) {
            return false;
        }

        $pro_features = array(
            'analytics_dashboard',
            'automation',
            'document_vault',
            'white_label',
            'advanced_reporting',
            'integrations',
            'bulk_operations',
            'custom_fields',
            'email_templates',
            'sms_notifications'
        );

        return in_array($feature, $pro_features);
    }

    /**
     * Generate nonce for PHPM actions
     *
     * @param string $action
     * @return string
     */
    public static function create_nonce($action) {
        return wp_create_nonce('phpm_' . $action);
    }

    /**
     * Verify nonce for PHPM actions
     *
     * @param string $nonce
     * @param string $action
     * @return bool
     */
    public static function verify_nonce($nonce, $action) {
        return wp_verify_nonce($nonce, 'phpm_' . $action);
    }

    /**
     * Get plugin version
     *
     * @return string
     */
    public static function get_version() {
        return defined('PHPM_VERSION') ? PHPM_VERSION : '1.0.0';
    }

    /**
     * Check if plugin is properly configured
     *
     * @return array
     */
    public static function get_health_status() {
        $status = array(
            'healthy' => true,
            'issues' => array(),
            'score' => 100
        );

        // Check database tables
        global $wpdb;
        $required_tables = array(
            $wpdb->prefix . 'phpm_property_views',
            $wpdb->prefix . 'phpm_maintenance_log',
            $wpdb->prefix . 'phpm_payments'
        );

        foreach ($required_tables as $table) {
            $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table'");
            if (!$table_exists) {
                $status['healthy'] = false;
                $status['issues'][] = sprintf(__('Database table %s is missing', 'plughaus-property'), $table);
                $status['score'] -= 20;
            }
        }

        // Check settings
        $settings = get_option('phpm_settings', array());
        if (empty($settings)) {
            $status['issues'][] = __('Plugin settings not configured', 'plughaus-property');
            $status['score'] -= 10;
        }

        // Check capabilities
        if (!current_user_can('edit_phpm_property')) {
            $status['issues'][] = __('User capabilities not properly set', 'plughaus-property');
            $status['score'] -= 15;
        }

        return $status;
    }

    /**
     * Sanitize property data
     *
     * @param array $data
     * @return array
     */
    public static function sanitize_property_data($data) {
        $sanitized = array();

        // Basic fields
        $sanitized['name'] = isset($data['name']) ? sanitize_text_field($data['name']) : '';
        $sanitized['description'] = isset($data['description']) ? wp_kses_post($data['description']) : '';
        $sanitized['type'] = isset($data['type']) ? sanitize_key($data['type']) : 'apartment';
        
        // Address fields
        $sanitized['address'] = isset($data['address']) ? sanitize_text_field($data['address']) : '';
        $sanitized['city'] = isset($data['city']) ? sanitize_text_field($data['city']) : '';
        $sanitized['state'] = isset($data['state']) ? sanitize_text_field($data['state']) : '';
        $sanitized['zip'] = isset($data['zip']) ? sanitize_text_field($data['zip']) : '';
        $sanitized['country'] = isset($data['country']) ? sanitize_text_field($data['country']) : 'US';
        
        // Numeric fields
        $sanitized['units'] = isset($data['units']) ? absint($data['units']) : 1;
        $sanitized['year_built'] = isset($data['year_built']) ? absint($data['year_built']) : '';
        $sanitized['square_footage'] = isset($data['square_footage']) ? absint($data['square_footage']) : '';
        
        // Financial fields
        $sanitized['purchase_price'] = isset($data['purchase_price']) ? floatval($data['purchase_price']) : 0;
        $sanitized['current_value'] = isset($data['current_value']) ? floatval($data['current_value']) : 0;

        return apply_filters('phpm_sanitize_property_data', $sanitized, $data);
    }

    /**
     * Sanitize tenant data
     *
     * @param array $data
     * @return array
     */
    public static function sanitize_tenant_data($data) {
        $sanitized = array();

        // Personal information
        $sanitized['first_name'] = isset($data['first_name']) ? sanitize_text_field($data['first_name']) : '';
        $sanitized['last_name'] = isset($data['last_name']) ? sanitize_text_field($data['last_name']) : '';
        $sanitized['email'] = isset($data['email']) ? sanitize_email($data['email']) : '';
        $sanitized['phone'] = isset($data['phone']) ? sanitize_text_field($data['phone']) : '';
        
        // Emergency contact
        $sanitized['emergency_name'] = isset($data['emergency_name']) ? sanitize_text_field($data['emergency_name']) : '';
        $sanitized['emergency_phone'] = isset($data['emergency_phone']) ? sanitize_text_field($data['emergency_phone']) : '';
        $sanitized['emergency_relationship'] = isset($data['emergency_relationship']) ? sanitize_text_field($data['emergency_relationship']) : '';
        
        // Additional info
        $sanitized['notes'] = isset($data['notes']) ? wp_kses_post($data['notes']) : '';
        $sanitized['move_in_date'] = isset($data['move_in_date']) ? sanitize_text_field($data['move_in_date']) : '';

        return apply_filters('phpm_sanitize_tenant_data', $sanitized, $data);
    }
    
    /**
     * AJAX handler for dismissing sample data notice
     */
    public static function ajax_dismiss_sample_data_notice() {
        check_ajax_referer('phpm_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions.', 'plughaus-property'));
        }
        
        set_transient('phpm_sample_data_notice_dismissed', true, WEEK_IN_SECONDS);
        
        wp_die(); // This is required to terminate immediately and return a proper response
    }
    
    /**
     * AJAX handler for dismissing sample removal notice
     */
    public static function ajax_dismiss_sample_removal_notice() {
        check_ajax_referer('phpm_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions.', 'plughaus-property'));
        }
        
        set_transient('phpm_sample_removal_notice_dismissed', true, WEEK_IN_SECONDS);
        
        wp_die(); // This is required to terminate immediately and return a proper response
    }
}

// Initialize utilities
PHPM_Utilities::init();