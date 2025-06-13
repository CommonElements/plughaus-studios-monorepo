<?php
/**
 * Shared utilities for Vireo Designs plugins
 * 
 * NOTE: This file is copied into each plugin during build/extraction
 * It should NOT be referenced as a shared dependency at runtime
 *
 * @package VireoShared
 * @since 1.0.0
 */

namespace Vireo\Shared;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Shared utilities class that gets copied into each plugin
 * Each plugin will rename this class to avoid conflicts
 */
class Vireo_Shared_Utilities {

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
    public static function generate_property_code($prefix = 'PROP') {
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
        $property_code = get_post_meta($property_id, '_property_code', true);
        if (empty($property_code)) {
            $property_code = self::generate_property_code();
            update_post_meta($property_id, '_property_code', $property_code);
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
            return 0;
        }
    }

    /**
     * Get property types
     *
     * @return array
     */
    public static function get_property_types() {
        $types = array(
            'apartment' => __('Apartment Building'),
            'house' => __('Single Family House'),
            'condo' => __('Condominium'),
            'townhouse' => __('Townhouse'),
            'duplex' => __('Duplex'),
            'commercial' => __('Commercial'),
            'industrial' => __('Industrial'),
            'land' => __('Land/Vacant Lot'),
            'mixed_use' => __('Mixed Use'),
            'other' => __('Other'),
        );

        return $types;
    }

    /**
     * Get lease statuses
     *
     * @return array
     */
    public static function get_lease_statuses() {
        $statuses = array(
            'active' => __('Active'),
            'pending' => __('Pending'),
            'expired' => __('Expired'),
            'terminated' => __('Terminated'),
            'renewal' => __('Renewal'),
            'hold' => __('On Hold'),
        );

        return $statuses;
    }

    /**
     * Get maintenance request priorities
     *
     * @return array
     */
    public static function get_maintenance_priorities() {
        $priorities = array(
            'low' => __('Low'),
            'normal' => __('Normal'),
            'high' => __('High'),
            'urgent' => __('Urgent'),
            'emergency' => __('Emergency'),
        );

        return $priorities;
    }

    /**
     * Get maintenance request statuses
     *
     * @return array
     */
    public static function get_maintenance_statuses() {
        $statuses = array(
            'open' => __('Open'),
            'in_progress' => __('In Progress'),
            'pending_parts' => __('Pending Parts'),
            'completed' => __('Completed'),
            'closed' => __('Closed'),
            'cancelled' => __('Cancelled'),
        );

        return $statuses;
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

        return $sanitized;
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

        return $sanitized;
    }
}