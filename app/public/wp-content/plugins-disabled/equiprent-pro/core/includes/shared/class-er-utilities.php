<?php
/**
 * EquipRent Pro Utilities
 *
 * @package EquipRent_Pro
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Utility functions for EquipRent Pro
 */
class ER_Utilities {

    /**
     * Format currency amount
     */
    public static function format_currency($amount, $currency = null) {
        if (is_null($currency)) {
            $currency = get_option('equiprent_currency', 'USD');
        }
        
        $symbol = get_option('equiprent_currency_symbol', '$');
        $position = get_option('equiprent_currency_position', 'before');
        $decimals = 2;
        
        $formatted = number_format((float)$amount, $decimals, '.', ',');
        
        if ($position === 'before') {
            return $symbol . $formatted;
        } else {
            return $formatted . $symbol;
        }
    }

    /**
     * Format date
     */
    public static function format_date($date, $format = null) {
        if (is_null($format)) {
            $format = get_option('equiprent_date_format', 'Y-m-d');
        }
        
        if (is_string($date)) {
            $date = strtotime($date);
        }
        
        return date($format, $date);
    }

    /**
     * Format time
     */
    public static function format_time($time, $format = null) {
        if (is_null($format)) {
            $format = get_option('equiprent_time_format', 'H:i');
        }
        
        if (is_string($time)) {
            $time = strtotime($time);
        }
        
        return date($format, $time);
    }

    /**
     * Format datetime
     */
    public static function format_datetime($datetime, $date_format = null, $time_format = null) {
        $date_part = self::format_date($datetime, $date_format);
        $time_part = self::format_time($datetime, $time_format);
        
        return $date_part . ' ' . $time_part;
    }

    /**
     * Generate booking number
     */
    public static function generate_booking_number() {
        $prefix = get_option('equiprent_booking_prefix', 'ER');
        $number = self::get_next_booking_number();
        
        return $prefix . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get next booking number
     */
    private static function get_next_booking_number() {
        $last_number = get_option('equiprent_last_booking_number', 0);
        $next_number = $last_number + 1;
        
        update_option('equiprent_last_booking_number', $next_number);
        
        return $next_number;
    }

    /**
     * Calculate rental duration in days
     */
    public static function calculate_rental_days($start_date, $end_date) {
        $start = new DateTime($start_date);
        $end = new DateTime($end_date);
        $interval = $start->diff($end);
        
        return max(1, $interval->days); // Minimum 1 day
    }

    /**
     * Calculate rental cost
     */
    public static function calculate_rental_cost($daily_rate, $days, $weekly_rate = null, $monthly_rate = null) {
        // If weekly rate is available and rental is 7+ days
        if ($weekly_rate && $days >= 7) {
            $weeks = floor($days / 7);
            $remaining_days = $days % 7;
            
            return ($weeks * $weekly_rate) + ($remaining_days * $daily_rate);
        }
        
        // If monthly rate is available and rental is 30+ days
        if ($monthly_rate && $days >= 30) {
            $months = floor($days / 30);
            $remaining_days = $days % 30;
            
            return ($months * $monthly_rate) + ($remaining_days * $daily_rate);
        }
        
        // Default to daily rate
        return $days * $daily_rate;
    }

    /**
     * Check equipment availability
     */
    public static function is_equipment_available($equipment_id, $start_date, $end_date, $exclude_booking = null) {
        global $wpdb;
        
        $booking_items_table = $wpdb->prefix . 'er_booking_items';
        $bookings_table = $wpdb->prefix . 'er_bookings';
        
        $query = "
            SELECT COUNT(*) 
            FROM {$booking_items_table} bi
            INNER JOIN {$bookings_table} b ON bi.booking_id = b.id
            WHERE bi.equipment_id = %d
            AND b.status NOT IN ('cancelled', 'completed')
            AND (
                (b.start_date <= %s AND b.end_date >= %s)
                OR (b.start_date <= %s AND b.end_date >= %s)
                OR (b.start_date >= %s AND b.end_date <= %s)
            )
        ";
        
        $params = array(
            $equipment_id,
            $start_date, $start_date,
            $end_date, $end_date,
            $start_date, $end_date
        );
        
        if ($exclude_booking) {
            $query .= " AND b.id != %d";
            $params[] = $exclude_booking;
        }
        
        $conflicting_bookings = $wpdb->get_var($wpdb->prepare($query, $params));
        
        return $conflicting_bookings == 0;
    }

    /**
     * Get equipment stock level
     */
    public static function get_equipment_stock($equipment_id) {
        // Get total quantity from meta
        $total_quantity = get_post_meta($equipment_id, '_equipment_quantity', true);
        if (!$total_quantity) {
            $total_quantity = 1; // Default to 1 if not set
        }
        
        // Get currently rented quantity
        global $wpdb;
        $booking_items_table = $wpdb->prefix . 'er_booking_items';
        $bookings_table = $wpdb->prefix . 'er_bookings';
        
        $rented_quantity = $wpdb->get_var($wpdb->prepare("
            SELECT SUM(bi.quantity)
            FROM {$booking_items_table} bi
            INNER JOIN {$bookings_table} b ON bi.booking_id = b.id
            WHERE bi.equipment_id = %d
            AND b.status IN ('confirmed', 'active')
        ", $equipment_id));
        
        if (!$rented_quantity) {
            $rented_quantity = 0;
        }
        
        return array(
            'total' => (int)$total_quantity,
            'rented' => (int)$rented_quantity,
            'available' => (int)$total_quantity - (int)$rented_quantity
        );
    }

    /**
     * Send notification email
     */
    public static function send_notification($to, $subject, $message, $headers = array()) {
        if (!get_option('equiprent_email_notifications', 1)) {
            return false;
        }
        
        $default_headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_option('equiprent_business_name', get_bloginfo('name')) . ' <' . get_option('equiprent_business_email', get_option('admin_email')) . '>'
        );
        
        $headers = array_merge($default_headers, $headers);
        
        return wp_mail($to, $subject, $message, $headers);
    }

    /**
     * Log activity
     */
    public static function log_activity($message, $context = array(), $level = 'info') {
        if (function_exists('error_log')) {
            $log_message = '[EquipRent Pro] ' . $message;
            if (!empty($context)) {
                $log_message .= ' Context: ' . json_encode($context);
            }
            error_log($log_message);
        }
    }

    /**
     * Log activity to database
     */
    public static function log($action, $object_type = '', $object_id = 0, $metadata = array()) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'er_activity_log';
        
        // Check if table exists first
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            // Table doesn't exist yet, just log to error log
            self::log_activity($action, array(
                'object_type' => $object_type,
                'object_id' => $object_id,
                'metadata' => $metadata
            ));
            return;
        }
        
        $user_id = get_current_user_id();
        $ip_address = self::get_client_ip();
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : '';
        
        $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'action' => sanitize_text_field($action),
                'object_type' => sanitize_text_field($object_type),
                'object_id' => intval($object_id),
                'ip_address' => sanitize_text_field($ip_address),
                'user_agent' => $user_agent,
                'metadata' => maybe_serialize($metadata),
                'created_at' => current_time('mysql')
            ),
            array('%d', '%s', '%s', '%d', '%s', '%s', '%s', '%s')
        );
    }

    /**
     * Get client IP address
     */
    private static function get_client_ip() {
        $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');
        
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown';
    }

    /**
     * Sanitize array
     */
    public static function sanitize_array($array) {
        $sanitized = array();
        
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $sanitized[sanitize_key($key)] = self::sanitize_array($value);
            } else {
                $sanitized[sanitize_key($key)] = sanitize_text_field($value);
            }
        }
        
        return $sanitized;
    }

    /**
     * Get plugin setting
     */
    public static function get_setting($key, $default = null) {
        return get_option('equiprent_' . $key, $default);
    }

    /**
     * Update plugin setting
     */
    public static function update_setting($key, $value) {
        return update_option('equiprent_' . $key, $value);
    }

    /**
     * Get equipment categories for select dropdown
     */
    public static function get_equipment_categories_options() {
        $categories = get_terms(array(
            'taxonomy' => 'equipment_category',
            'hide_empty' => false,
        ));
        
        $options = array('' => __('Select Category', 'equiprent-pro'));
        
        if (!is_wp_error($categories)) {
            foreach ($categories as $category) {
                $options[$category->term_id] = $category->name;
            }
        }
        
        return $options;
    }

    /**
     * Get equipment brands for select dropdown
     */
    public static function get_equipment_brands_options() {
        $brands = get_terms(array(
            'taxonomy' => 'equipment_brand',
            'hide_empty' => false,
        ));
        
        $options = array('' => __('Select Brand', 'equiprent-pro'));
        
        if (!is_wp_error($brands)) {
            foreach ($brands as $brand) {
                $options[$brand->term_id] = $brand->name;
            }
        }
        
        return $options;
    }

    /**
     * Get equipment locations for select dropdown
     */
    public static function get_equipment_locations_options() {
        $locations = get_terms(array(
            'taxonomy' => 'equipment_location',
            'hide_empty' => false,
        ));
        
        $options = array('' => __('Select Location', 'equiprent-pro'));
        
        if (!is_wp_error($locations)) {
            foreach ($locations as $location) {
                $options[$location->term_id] = $location->name;
            }
        }
        
        return $options;
    }

    /**
     * Is pro version active
     */
    public static function is_pro() {
        $plugin = EquipRent_Pro::get_instance();
        return $plugin->is_pro();
    }

    /**
     * Get pro upgrade message
     */
    public static function get_pro_upgrade_message($feature_name = '') {
        $message = __('This feature is available in EquipRent Pro.', 'equiprent-pro');
        
        if ($feature_name) {
            $message = sprintf(
                __('%s is available in EquipRent Pro.', 'equiprent-pro'),
                $feature_name
            );
        }
        
        $message .= ' <a href="https://vireodesigns.com/plugins/equiprent-pro" target="_blank">' . __('Upgrade Now', 'equiprent-pro') . '</a>';
        
        return $message;
    }
}