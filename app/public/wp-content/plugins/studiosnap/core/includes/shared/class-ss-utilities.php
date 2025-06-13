<?php
/**
 * StudioSnap Utilities - Shared utility functions and helpers
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class SS_Utilities {
    
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
     * Generate unique booking reference
     */
    public static function generate_booking_reference($prefix = 'SS') {
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
     * Calculate session duration in hours
     */
    public static function calculate_session_duration($start_time, $end_time) {
        $start = strtotime($start_time);
        $end = strtotime($end_time);
        
        return round(($end - $start) / 3600, 2);
    }
    
    /**
     * Get session status label
     */
    public static function get_status_label($status) {
        $labels = array(
            'ss_inquiry' => __('Inquiry', 'studiosnap'),
            'ss_confirmed' => __('Confirmed', 'studiosnap'),
            'ss_in_progress' => __('In Progress', 'studiosnap'),
            'ss_completed' => __('Completed', 'studiosnap'),
            'ss_cancelled' => __('Cancelled', 'studiosnap'),
            'ss_rescheduled' => __('Rescheduled', 'studiosnap')
        );
        
        return isset($labels[$status]) ? $labels[$status] : ucfirst($status);
    }
    
    /**
     * Get session status color
     */
    public static function get_status_color($status) {
        $colors = array(
            'ss_inquiry' => '#f59e0b',
            'ss_confirmed' => '#3b82f6',
            'ss_in_progress' => '#8b5cf6',
            'ss_completed' => '#10b981',
            'ss_cancelled' => '#ef4444',
            'ss_rescheduled' => '#f97316'
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
     * Get time slots for booking
     */
    public static function get_time_slots($start_hour = 9, $end_hour = 17, $interval = 60) {
        $slots = array();
        
        for ($hour = $start_hour; $hour < $end_hour; $hour++) {
            for ($minute = 0; $minute < 60; $minute += $interval) {
                $time = sprintf('%02d:%02d:00', $hour, $minute);
                $display = date('g:i A', strtotime($time));
                $slots[] = array(
                    'value' => $time,
                    'display' => $display
                );
            }
        }
        
        return $slots;
    }
    
    /**
     * Generate secure hash for booking access
     */
    public static function generate_booking_hash($booking_id, $email) {
        return hash('sha256', $booking_id . $email . wp_salt());
    }
    
    /**
     * Get business hours
     */
    public static function get_business_hours() {
        return array(
            'monday' => get_option('ss_monday_hours', '9:00 AM - 5:00 PM'),
            'tuesday' => get_option('ss_tuesday_hours', '9:00 AM - 5:00 PM'),
            'wednesday' => get_option('ss_wednesday_hours', '9:00 AM - 5:00 PM'),
            'thursday' => get_option('ss_thursday_hours', '9:00 AM - 5:00 PM'),
            'friday' => get_option('ss_friday_hours', '9:00 AM - 5:00 PM'),
            'saturday' => get_option('ss_saturday_hours', '10:00 AM - 4:00 PM'),
            'sunday' => get_option('ss_sunday_hours', 'Closed')
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
     * Get available time slots for date
     */
    public static function get_available_slots($date, $session_type = 'portrait') {
        if (!self::is_business_day($date)) {
            return array();
        }
        
        $day_of_week = strtolower(date('l', strtotime($date)));
        $business_hours = self::get_business_hours();
        $hours_string = $business_hours[$day_of_week];
        
        if ($hours_string === 'Closed') {
            return array();
        }
        
        // Parse business hours (e.g., "9:00 AM - 5:00 PM")
        if (preg_match('/(\d+:\d+\s*[AP]M)\s*-\s*(\d+:\d+\s*[AP]M)/', $hours_string, $matches)) {
            $start_time = date('H:i:s', strtotime($matches[1]));
            $end_time = date('H:i:s', strtotime($matches[2]));
            
            $start_hour = (int)date('H', strtotime($start_time));
            $end_hour = (int)date('H', strtotime($end_time));
            
            return self::get_time_slots($start_hour, $end_hour, 60);
        }
        
        return array();
    }
    
    /**
     * Log plugin activity
     */
    public static function log_activity($message, $level = 'info', $context = array()) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log(sprintf('[StudioSnap] %s: %s', strtoupper($level), $message));
            
            if (!empty($context)) {
                error_log('[StudioSnap] Context: ' . print_r($context, true));
            }
        }
    }
    
    /**
     * Get plugin option with default
     */
    public static function get_option($option_name, $default = '') {
        return get_option('ss_' . $option_name, $default);
    }
    
    /**
     * Update plugin option
     */
    public static function update_option($option_name, $value) {
        return update_option('ss_' . $option_name, $value);
    }
    
    /**
     * Get session packages
     */
    public static function get_session_packages() {
        return array(
            'portrait' => array(
                'name' => __('Portrait Session', 'studiosnap'),
                'price' => 200,
                'duration' => 2,
                'description' => __('Professional portrait photography session', 'studiosnap')
            ),
            'family' => array(
                'name' => __('Family Session', 'studiosnap'),
                'price' => 300,
                'duration' => 3,
                'description' => __('Family photography session with multiple poses', 'studiosnap')
            ),
            'headshot' => array(
                'name' => __('Professional Headshots', 'studiosnap'),
                'price' => 150,
                'duration' => 1,
                'description' => __('Professional headshots for business use', 'studiosnap')
            ),
            'event' => array(
                'name' => __('Event Photography', 'studiosnap'),
                'price' => 500,
                'duration' => 6,
                'description' => __('Event coverage and photography', 'studiosnap')
            ),
            'product' => array(
                'name' => __('Product Photography', 'studiosnap'),
                'price' => 250,
                'duration' => 4,
                'description' => __('Professional product photography', 'studiosnap')
            )
        );
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
     * Check if pro version is active
     */
    public static function is_pro_active() {
        // This will be implemented when pro version is available
        return false;
    }
}