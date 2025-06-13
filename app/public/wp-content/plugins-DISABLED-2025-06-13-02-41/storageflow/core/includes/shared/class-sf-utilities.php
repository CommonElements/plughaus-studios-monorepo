<?php
/**
 * StorageFlow Utilities - Shared utility functions and helpers
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class SF_Utilities {
    
    /**
     * Format currency amount
     */
    public static function format_currency($amount, $currency = 'USD') {
        $formatted = '$' . number_format((float)$amount, 2);
        return $formatted;
    }
    
    /**
     * Format date for display
     */
    public static function format_date($date, $format = 'F j, Y') {
        if (empty($date)) return '';
        
        $timestamp = is_numeric($date) ? $date : strtotime($date);
        return date($format, $timestamp);
    }
    
    /**
     * Format time for display
     */
    public static function format_time($time, $format = 'g:i A') {
        if (empty($time)) return '';
        
        $timestamp = is_numeric($time) ? $time : strtotime($time);
        return date($format, $timestamp);
    }
    
    /**
     * Generate unique rental reference
     */
    public static function generate_rental_reference($prefix = 'SF') {
        return $prefix . '-' . date('Y') . '-' . strtoupper(substr(uniqid(), -6));
    }
    
    /**
     * Sanitize phone number
     */
    public static function sanitize_phone($phone) {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (strlen($phone) === 10) {
            return sprintf('(%s) %s-%s', 
                substr($phone, 0, 3),
                substr($phone, 3, 3), 
                substr($phone, 6, 4)
            );
        }
        
        return $phone;
    }
    
    /**
     * Calculate monthly cost with prorated amount
     */
    public static function calculate_prorated_rent($monthly_rate, $move_in_date) {
        $move_in_timestamp = strtotime($move_in_date);
        $days_in_month = date('t', $move_in_timestamp);
        $day_of_month = date('j', $move_in_timestamp);
        
        // If moving in on the 1st, no proration needed
        if ($day_of_month === 1) {
            return $monthly_rate;
        }
        
        $days_remaining = $days_in_month - $day_of_month + 1;
        $daily_rate = $monthly_rate / $days_in_month;
        
        return round($daily_rate * $days_remaining, 2);
    }
    
    /**
     * Get rental status label
     */
    public static function get_status_label($status) {
        $labels = array(
            'sf_application' => __('Application', 'storageflow'),
            'sf_approved' => __('Approved', 'storageflow'),
            'sf_active' => __('Active Rental', 'storageflow'),
            'sf_overdue' => __('Overdue', 'storageflow'),
            'sf_cancelled' => __('Cancelled', 'storageflow'),
            'sf_completed' => __('Move-Out Complete', 'storageflow'),
            'sf_pending' => __('Pending', 'storageflow'),
            'sf_paid' => __('Paid', 'storageflow')
        );
        
        return isset($labels[$status]) ? $labels[$status] : ucfirst($status);
    }
    
    /**
     * Get rental status color
     */
    public static function get_status_color($status) {
        $colors = array(
            'sf_application' => '#f59e0b',
            'sf_approved' => '#3b82f6',
            'sf_active' => '#10b981',
            'sf_overdue' => '#ef4444',
            'sf_cancelled' => '#6b7280',
            'sf_completed' => '#8b5cf6',
            'sf_pending' => '#f59e0b',
            'sf_paid' => '#10b981'
        );
        
        return isset($colors[$status]) ? $colors[$status] : '#6b7280';
    }
    
    /**
     * Validate email address
     */
    public static function is_valid_email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate phone number
     */
    public static function is_valid_phone($phone) {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        return strlen($phone) >= 10;
    }
    
    /**
     * Generate secure hash for rental access
     */
    public static function generate_rental_hash($rental_id, $email) {
        return hash('sha256', $rental_id . $email . wp_salt());
    }
    
    /**
     * Get business hours
     */
    public static function get_business_hours() {
        return array(
            'monday' => get_option('sf_monday_hours', '8:00 AM - 6:00 PM'),
            'tuesday' => get_option('sf_tuesday_hours', '8:00 AM - 6:00 PM'),
            'wednesday' => get_option('sf_wednesday_hours', '8:00 AM - 6:00 PM'),
            'thursday' => get_option('sf_thursday_hours', '8:00 AM - 6:00 PM'),
            'friday' => get_option('sf_friday_hours', '8:00 AM - 6:00 PM'),
            'saturday' => get_option('sf_saturday_hours', '9:00 AM - 5:00 PM'),
            'sunday' => get_option('sf_sunday_hours', '10:00 AM - 4:00 PM')
        );
    }
    
    /**
     * Check if date is business day
     */
    public static function is_business_day($date) {
        $day_of_week = strtolower(date('l', strtotime($date)));
        $hours = self::get_business_hours();
        
        return !empty($hours[$day_of_week]) && $hours[$day_of_week] !== 'Closed';
    }
    
    /**
     * Get available unit sizes
     */
    public static function get_unit_sizes() {
        return array(
            '5x5' => array(
                'dimensions' => '5\' × 5\'',
                'square_feet' => 25,
                'description' => __('Small closet size. Perfect for seasonal items, small furniture, or boxes.', 'storageflow')
            ),
            '5x10' => array(
                'dimensions' => '5\' × 10\'',
                'square_feet' => 50,
                'description' => __('Walk-in closet size. Great for studio apartment or office contents.', 'storageflow')
            ),
            '10x10' => array(
                'dimensions' => '10\' × 10\'',
                'square_feet' => 100,
                'description' => __('Small bedroom size. Ideal for 1-2 bedroom apartment or small house.', 'storageflow')
            ),
            '10x15' => array(
                'dimensions' => '10\' × 15\'',
                'square_feet' => 150,
                'description' => __('Large bedroom size. Perfect for 2-3 bedroom house contents.', 'storageflow')
            ),
            '10x20' => array(
                'dimensions' => '10\' × 20\'',
                'square_feet' => 200,
                'description' => __('One-car garage size. Suitable for 3-4 bedroom house or vehicle storage.', 'storageflow')
            ),
            '10x30' => array(
                'dimensions' => '10\' × 30\'',
                'square_feet' => 300,
                'description' => __('Large garage size. Ideal for large homes, RVs, or commercial storage.', 'storageflow')
            )
        );
    }
    
    /**
     * Get unit features
     */
    public static function get_unit_features() {
        return array(
            'climate_controlled' => __('Climate Controlled', 'storageflow'),
            'drive_up' => __('Drive-Up Access', 'storageflow'),
            'ground_floor' => __('Ground Floor', 'storageflow'),
            'indoor_access' => __('Indoor Access', 'storageflow'),
            'elevator_access' => __('Elevator Access', 'storageflow'),
            '24_hour_access' => __('24-Hour Access', 'storageflow'),
            'security_cameras' => __('Security Cameras', 'storageflow'),
            'electronic_gate' => __('Electronic Gate', 'storageflow'),
            'power_outlet' => __('Power Outlet', 'storageflow'),
            'shelving' => __('Built-in Shelving', 'storageflow')
        );
    }
    
    /**
     * Calculate occupancy rate
     */
    public static function calculate_occupancy_rate($total_units, $rented_units) {
        if ($total_units === 0) return 0;
        return round(($rented_units / $total_units) * 100, 1);
    }
    
    /**
     * Log plugin activity
     */
    public static function log_activity($message, $level = 'info', $context = array()) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log(sprintf('[StorageFlow] %s: %s', strtoupper($level), $message));
            
            if (!empty($context)) {
                error_log('[StorageFlow] Context: ' . print_r($context, true));
            }
        }
    }
    
    /**
     * Get plugin option with default
     */
    public static function get_option($option_name, $default = '') {
        return get_option('sf_' . $option_name, $default);
    }
    
    /**
     * Update plugin option
     */
    public static function update_option($option_name, $value) {
        return update_option('sf_' . $option_name, $value);
    }
    
    /**
     * Get default late fees
     */
    public static function get_late_fee_structure() {
        return array(
            'grace_period' => intval(self::get_option('late_fee_grace_days', 5)),
            'late_fee_amount' => floatval(self::get_option('late_fee_amount', 25.00)),
            'daily_late_fee' => floatval(self::get_option('daily_late_fee', 5.00)),
            'max_late_fee' => floatval(self::get_option('max_late_fee', 100.00))
        );
    }
    
    /**
     * Calculate late fees
     */
    public static function calculate_late_fees($due_date, $current_date = null) {
        if (!$current_date) {
            $current_date = current_time('Y-m-d');
        }
        
        $due_timestamp = strtotime($due_date);
        $current_timestamp = strtotime($current_date);
        
        if ($current_timestamp <= $due_timestamp) {
            return 0; // Not late
        }
        
        $fee_structure = self::get_late_fee_structure();
        $days_late = floor(($current_timestamp - $due_timestamp) / (24 * 60 * 60));
        
        if ($days_late <= $fee_structure['grace_period']) {
            return 0; // Within grace period
        }
        
        $actual_days_late = $days_late - $fee_structure['grace_period'];
        $total_late_fee = $fee_structure['late_fee_amount'] + ($actual_days_late * $fee_structure['daily_late_fee']);
        
        return min($total_late_fee, $fee_structure['max_late_fee']);
    }
    
    /**
     * Send email notification
     */
    public static function send_email($to, $subject, $message, $headers = array()) {
        if (empty($headers)) {
            $headers = array(
                'Content-Type: text/html; charset=UTF-8',
                'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>'
            );
        }
        
        return wp_mail($to, $subject, $message, $headers);
    }
    
    /**
     * Generate admin URL
     */
    public static function admin_url($page, $params = array()) {
        $url = admin_url('admin.php?page=' . $page);
        
        if (!empty($params)) {
            $url .= '&' . http_build_query($params);
        }
        
        return $url;
    }
    
    /**
     * Get payment methods
     */
    public static function get_payment_methods() {
        return array(
            'cash' => __('Cash', 'storageflow'),
            'check' => __('Check', 'storageflow'),
            'credit_card' => __('Credit Card', 'storageflow'),
            'debit_card' => __('Debit Card', 'storageflow'),
            'bank_transfer' => __('Bank Transfer', 'storageflow'),
            'online' => __('Online Payment', 'storageflow'),
            'autopay' => __('Auto-Pay', 'storageflow')
        );
    }
    
    /**
     * Get rental terms
     */
    public static function get_rental_terms() {
        return array(
            'month_to_month' => __('Month-to-Month', 'storageflow'),
            '3_month' => __('3 Month Minimum', 'storageflow'),
            '6_month' => __('6 Month Minimum', 'storageflow'),
            '12_month' => __('12 Month Contract', 'storageflow'),
            '24_month' => __('24 Month Contract', 'storageflow')
        );
    }
    
    /**
     * Get unit use types
     */
    public static function get_unit_use_types() {
        return array(
            'personal' => __('Personal Items', 'storageflow'),
            'business' => __('Business Items', 'storageflow'),
            'vehicle' => __('Vehicle Storage', 'storageflow'),
            'furniture' => __('Furniture', 'storageflow'),
            'documents' => __('Documents/Records', 'storageflow'),
            'seasonal' => __('Seasonal Items', 'storageflow'),
            'inventory' => __('Business Inventory', 'storageflow'),
            'other' => __('Other', 'storageflow')
        );
    }
    
    /**
     * Check if pro version is active
     */
    public static function is_pro_active() {
        // This will be implemented when pro version is available
        return false;
    }
    
    /**
     * Format unit size for display
     */
    public static function format_unit_size($width, $length) {
        return sprintf('%s x %s', $width, $length);
    }
    
    /**
     * Calculate storage space needed
     */
    public static function estimate_storage_space($items = array()) {
        $space_requirements = array(
            'bedroom_set' => 50, // square feet
            'living_room_set' => 75,
            'dining_room_set' => 30,
            'appliances_large' => 25,
            'appliances_small' => 10,
            'boxes_small' => 1,
            'boxes_medium' => 2,
            'boxes_large' => 4,
            'mattress_twin' => 15,
            'mattress_queen' => 25,
            'mattress_king' => 30,
            'vehicle_car' => 150,
            'vehicle_truck' => 200,
            'vehicle_motorcycle' => 25,
            'business_records' => 2,
            'seasonal_decorations' => 10
        );
        
        $total_space = 0;
        foreach ($items as $item => $quantity) {
            if (isset($space_requirements[$item])) {
                $total_space += $space_requirements[$item] * intval($quantity);
            }
        }
        
        // Add 20% buffer for moving space
        $recommended_space = $total_space * 1.2;
        
        return array(
            'minimum_space' => $total_space,
            'recommended_space' => $recommended_space,
            'suggested_sizes' => self::suggest_unit_sizes($recommended_space)
        );
    }
    
    /**
     * Suggest unit sizes based on space needed
     */
    private static function suggest_unit_sizes($space_needed) {
        $unit_sizes = self::get_unit_sizes();
        $suggestions = array();
        
        foreach ($unit_sizes as $size => $details) {
            if ($details['square_feet'] >= $space_needed) {
                $suggestions[] = $size;
            }
        }
        
        return $suggestions;
    }
}