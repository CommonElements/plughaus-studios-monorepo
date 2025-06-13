<?php
/**
 * DealerEdge Utility Functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class DE_Utilities {
    
    /**
     * Format currency for display
     */
    public static function format_currency($amount, $symbol = true) {
        $currency = get_option('dealeredge_currency', 'USD');
        $formatted = number_format((float)$amount, 2);
        
        if ($symbol) {
            $symbols = array(
                'USD' => '$',
                'EUR' => '€',
                'GBP' => '£',
                'CAD' => 'C$',
                'AUD' => 'A$',
            );
            
            $symbol = isset($symbols[$currency]) ? $symbols[$currency] : '$';
            return $symbol . $formatted;
        }
        
        return $formatted;
    }
    
    /**
     * Get next work order number
     */
    public static function get_next_work_order_number() {
        if (!get_option('dealeredge_auto_assign_wo_numbers', '1')) {
            return '';
        }
        
        $prefix = get_option('dealeredge_work_order_prefix', 'WO-');
        $start_number = get_option('dealeredge_work_order_start_number', '1000');
        
        // Get the highest existing work order number
        global $wpdb;
        $last_number = $wpdb->get_var("
            SELECT MAX(CAST(SUBSTRING(meta_value, LENGTH('$prefix') + 1) AS UNSIGNED))
            FROM {$wpdb->postmeta} 
            WHERE meta_key = '_de_work_order_number' 
            AND meta_value LIKE '$prefix%'
        ");
        
        $next_number = max($last_number + 1, $start_number);
        
        return $prefix . $next_number;
    }
    
    /**
     * Get next sale number
     */
    public static function get_next_sale_number() {
        if (!get_option('dealeredge_auto_assign_sale_numbers', '1')) {
            return '';
        }
        
        $prefix = get_option('dealeredge_sale_prefix', 'SALE-');
        $start_number = get_option('dealeredge_sale_start_number', '1000');
        
        // Get the highest existing sale number
        global $wpdb;
        $last_number = $wpdb->get_var("
            SELECT MAX(CAST(SUBSTRING(meta_value, LENGTH('$prefix') + 1) AS UNSIGNED))
            FROM {$wpdb->postmeta} 
            WHERE meta_key = '_de_sale_number' 
            AND meta_value LIKE '$prefix%'
        ");
        
        $next_number = max($last_number + 1, $start_number);
        
        return $prefix . $next_number;
    }
    
    /**
     * Get vehicle display name
     */
    public static function get_vehicle_display_name($vehicle_id) {
        $year = get_post_meta($vehicle_id, '_de_vehicle_year', true);
        $make_terms = get_the_terms($vehicle_id, 'de_vehicle_make');
        $model_terms = get_the_terms($vehicle_id, 'de_vehicle_model');
        
        $make = $make_terms && !is_wp_error($make_terms) ? $make_terms[0]->name : '';
        $model = $model_terms && !is_wp_error($model_terms) ? $model_terms[0]->name : '';
        
        $parts = array_filter(array($year, $make, $model));
        
        return !empty($parts) ? implode(' ', $parts) : get_the_title($vehicle_id);
    }
    
    /**
     * Get customer display name
     */
    public static function get_customer_display_name($customer_id) {
        $first_name = get_post_meta($customer_id, '_de_customer_first_name', true);
        $last_name = get_post_meta($customer_id, '_de_customer_last_name', true);
        
        if ($first_name || $last_name) {
            return trim($first_name . ' ' . $last_name);
        }
        
        return get_the_title($customer_id);
    }
    
    /**
     * Get work order status options
     */
    public static function get_work_order_statuses() {
        return array(
            'pending' => __('Pending', 'dealeredge'),
            'in_progress' => __('In Progress', 'dealeredge'),
            'waiting_parts' => __('Waiting for Parts', 'dealeredge'),
            'waiting_approval' => __('Waiting for Approval', 'dealeredge'),
            'completed' => __('Completed', 'dealeredge'),
            'delivered' => __('Delivered', 'dealeredge'),
            'cancelled' => __('Cancelled', 'dealeredge'),
        );
    }
    
    /**
     * Get sale status options
     */
    public static function get_sale_statuses() {
        return array(
            'pending' => __('Pending', 'dealeredge'),
            'negotiating' => __('Negotiating', 'dealeredge'),
            'financing' => __('Pending Financing', 'dealeredge'),
            'paperwork' => __('Paperwork', 'dealeredge'),
            'completed' => __('Completed', 'dealeredge'),
            'delivered' => __('Delivered', 'dealeredge'),
            'cancelled' => __('Cancelled', 'dealeredge'),
        );
    }
    
    /**
     * Get vehicle condition options
     */
    public static function get_vehicle_conditions() {
        return array(
            'excellent' => __('Excellent', 'dealeredge'),
            'very_good' => __('Very Good', 'dealeredge'),
            'good' => __('Good', 'dealeredge'),
            'fair' => __('Fair', 'dealeredge'),
            'poor' => __('Poor', 'dealeredge'),
            'parts_only' => __('Parts Only', 'dealeredge'),
        );
    }
    
    /**
     * Get vehicle types
     */
    public static function get_vehicle_types() {
        return array(
            'sedan' => __('Sedan', 'dealeredge'),
            'coupe' => __('Coupe', 'dealeredge'),
            'hatchback' => __('Hatchback', 'dealeredge'),
            'wagon' => __('Wagon', 'dealeredge'),
            'suv' => __('SUV', 'dealeredge'),
            'crossover' => __('Crossover', 'dealeredge'),
            'pickup' => __('Pickup Truck', 'dealeredge'),
            'van' => __('Van', 'dealeredge'),
            'convertible' => __('Convertible', 'dealeredge'),
            'motorcycle' => __('Motorcycle', 'dealeredge'),
            'truck' => __('Commercial Truck', 'dealeredge'),
            'other' => __('Other', 'dealeredge'),
        );
    }
    
    /**
     * Calculate tax amount
     */
    public static function calculate_tax($amount) {
        $tax_rate = (float)get_option('dealeredge_tax_rate', '8.25');
        return $amount * ($tax_rate / 100);
    }
    
    /**
     * Sanitize VIN number
     */
    public static function sanitize_vin($vin) {
        // Remove any non-alphanumeric characters and convert to uppercase
        $vin = preg_replace('/[^A-Z0-9]/', '', strtoupper($vin));
        
        // VIN should be exactly 17 characters
        if (strlen($vin) !== 17) {
            return false;
        }
        
        // VINs cannot contain I, O, or Q
        if (preg_match('/[IOQ]/', $vin)) {
            return false;
        }
        
        return $vin;
    }
    
    /**
     * Format phone number
     */
    public static function format_phone($phone) {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Format as (XXX) XXX-XXXX if 10 digits
        if (strlen($phone) === 10) {
            return sprintf('(%s) %s-%s', 
                substr($phone, 0, 3),
                substr($phone, 3, 3),
                substr($phone, 6, 4)
            );
        }
        
        // Format as +X (XXX) XXX-XXXX if 11 digits
        if (strlen($phone) === 11) {
            return sprintf('+%s (%s) %s-%s',
                substr($phone, 0, 1),
                substr($phone, 1, 3),
                substr($phone, 4, 3),
                substr($phone, 7, 4)
            );
        }
        
        return $phone;
    }
    
    /**
     * Get business type setting
     */
    public static function get_business_type() {
        return get_option('dealeredge_business_type', 'auto_shop');
    }
    
    /**
     * Check if feature is enabled for current business type
     */
    public static function is_feature_enabled($feature) {
        $business_type = self::get_business_type();
        
        $feature_map = array(
            'work_orders' => array('auto_shop', 'both'),
            'sales' => array('dealership', 'both'),
            'inventory' => array('dealership', 'both', 'auto_shop'),
            'parts' => array('auto_shop', 'both'),
        );
        
        if (!isset($feature_map[$feature])) {
            return true; // Feature not restricted
        }
        
        return in_array($business_type, $feature_map[$feature]);
    }
    
    /**
     * Log activity
     */
    public static function log_activity($message, $type = 'info') {
        if (WP_DEBUG) {
            error_log("[DealerEdge] [$type] $message");
        }
    }
}