<?php
/**
 * Shared utility functions for EquipRent Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Utility functions class
 */
class ERP_Utilities {

    /**
     * Format currency amount
     */
    public static function format_currency($amount, $currency = 'USD') {
        $formatted = number_format(floatval($amount), 2);
        
        switch ($currency) {
            case 'USD':
                return '$' . $formatted;
            case 'EUR':
                return '€' . $formatted;
            case 'GBP':
                return '£' . $formatted;
            default:
                return $currency . ' ' . $formatted;
        }
    }

    /**
     * Format date
     */
    public static function format_date($date, $format = 'M j, Y') {
        if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
            return '-';
        }
        
        return date($format, strtotime($date));
    }

    /**
     * Format datetime
     */
    public static function format_datetime($datetime, $format = 'M j, Y g:i A') {
        if (empty($datetime) || $datetime === '0000-00-00 00:00:00') {
            return '-';
        }
        
        return date($format, strtotime($datetime));
    }

    /**
     * Calculate days between dates
     */
    public static function calculate_days($start_date, $end_date) {
        $start = new DateTime($start_date);
        $end = new DateTime($end_date);
        return $start->diff($end)->days + 1;
    }

    /**
     * Get booking status label
     */
    public static function get_booking_status_label($status) {
        $statuses = array(
            'pending' => __('Pending', 'equiprent-pro'),
            'confirmed' => __('Confirmed', 'equiprent-pro'),
            'in_progress' => __('In Progress', 'equiprent-pro'),
            'completed' => __('Completed', 'equiprent-pro'),
            'cancelled' => __('Cancelled', 'equiprent-pro'),
            'overdue' => __('Overdue', 'equiprent-pro'),
        );
        
        return isset($statuses[$status]) ? $statuses[$status] : ucfirst($status);
    }

    /**
     * Get equipment status label
     */
    public static function get_equipment_status_label($status) {
        $statuses = array(
            'available' => __('Available', 'equiprent-pro'),
            'rented' => __('Rented', 'equiprent-pro'),
            'maintenance' => __('Maintenance', 'equiprent-pro'),
            'retired' => __('Retired', 'equiprent-pro'),
            'lost' => __('Lost', 'equiprent-pro'),
            'damaged' => __('Damaged', 'equiprent-pro'),
        );
        
        return isset($statuses[$status]) ? $statuses[$status] : ucfirst($status);
    }

    /**
     * Get payment status label
     */
    public static function get_payment_status_label($status) {
        $statuses = array(
            'pending' => __('Pending', 'equiprent-pro'),
            'paid' => __('Paid', 'equiprent-pro'),
            'partial' => __('Partial', 'equiprent-pro'),
            'overdue' => __('Overdue', 'equiprent-pro'),
            'refunded' => __('Refunded', 'equiprent-pro'),
        );
        
        return isset($statuses[$status]) ? $statuses[$status] : ucfirst($status);
    }

    /**
     * Get status badge HTML
     */
    public static function get_status_badge($status, $type = 'booking') {
        $colors = array(
            'pending' => 'warning',
            'confirmed' => 'info',
            'in_progress' => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger',
            'overdue' => 'danger',
            'available' => 'success',
            'rented' => 'warning',
            'maintenance' => 'info',
            'retired' => 'secondary',
            'lost' => 'danger',
            'damaged' => 'warning',
            'paid' => 'success',
            'partial' => 'warning',
            'refunded' => 'info',
        );
        
        $color = isset($colors[$status]) ? $colors[$status] : 'secondary';
        
        switch ($type) {
            case 'equipment':
                $label = self::get_equipment_status_label($status);
                break;
            case 'payment':
                $label = self::get_payment_status_label($status);
                break;
            default:
                $label = self::get_booking_status_label($status);
        }
        
        return sprintf(
            '<span class="badge badge-%s">%s</span>',
            esc_attr($color),
            esc_html($label)
        );
    }

    /**
     * Generate unique booking number
     */
    public static function generate_booking_number() {
        $prefix = 'ERP';
        $year = date('Y');
        $month = date('m');
        
        global $wpdb;
        $table = $wpdb->prefix . 'erp_bookings';
        
        // Get the last booking number for this month
        $last_number = $wpdb->get_var($wpdb->prepare("
            SELECT booking_number FROM $table 
            WHERE booking_number LIKE %s 
            ORDER BY id DESC 
            LIMIT 1
        ", $prefix . $year . $month . '%'));
        
        if ($last_number) {
            $last_sequence = intval(substr($last_number, -4));
            $new_sequence = $last_sequence + 1;
        } else {
            $new_sequence = 1;
        }
        
        return $prefix . $year . $month . str_pad($new_sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate QR code for equipment
     */
    public static function generate_equipment_qr_code($equipment_id) {
        return 'ERP-' . str_pad($equipment_id, 6, '0', STR_PAD_LEFT) . '-' . wp_generate_password(4, false);
    }

    /**
     * Validate email address
     */
    public static function is_valid_email($email) {
        return is_email($email);
    }

    /**
     * Validate phone number
     */
    public static function is_valid_phone($phone) {
        // Basic phone validation - can be enhanced
        $phone = preg_replace('/[^0-9+\-\(\)\s]/', '', $phone);
        return strlen($phone) >= 10;
    }

    /**
     * Sanitize input data
     */
    public static function sanitize_input($data) {
        if (is_array($data)) {
            return array_map(array(__CLASS__, 'sanitize_input'), $data);
        }
        
        return sanitize_text_field($data);
    }

    /**
     * Get equipment categories
     */
    public static function get_equipment_categories() {
        global $wpdb;
        
        $table = $wpdb->prefix . 'erp_equipment';
        $categories = $wpdb->get_col("SELECT DISTINCT category FROM $table WHERE category IS NOT NULL AND category != ''");
        
        return $categories ? $categories : array();
    }

    /**
     * Get equipment locations
     */
    public static function get_equipment_locations() {
        global $wpdb;
        
        $table = $wpdb->prefix . 'erp_equipment';
        $locations = $wpdb->get_col("SELECT DISTINCT location FROM $table WHERE location IS NOT NULL AND location != ''");
        
        return $locations ? $locations : array();
    }

    /**
     * Check equipment availability
     */
    public static function check_equipment_availability($equipment_id, $start_date, $end_date, $exclude_booking_id = 0) {
        global $wpdb;
        
        $bookings_table = $wpdb->prefix . 'erp_bookings';
        $booking_items_table = $wpdb->prefix . 'erp_booking_items';
        
        $exclude_clause = $exclude_booking_id > 0 ? $wpdb->prepare("AND b.id != %d", $exclude_booking_id) : "";
        
        $conflicts = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*)
            FROM $bookings_table b
            INNER JOIN $booking_items_table bi ON b.id = bi.booking_id
            WHERE bi.equipment_id = %d
            AND b.status NOT IN ('cancelled', 'completed')
            $exclude_clause
            AND (
                (b.start_date <= %s AND b.end_date >= %s)
                OR (b.start_date <= %s AND b.end_date >= %s)
                OR (b.start_date >= %s AND b.end_date <= %s)
            )
        ", $equipment_id, $start_date, $start_date, $end_date, $end_date, $start_date, $end_date));

        return $conflicts == 0;
    }

    /**
     * Calculate rental cost
     */
    public static function calculate_rental_cost($daily_rate, $start_date, $end_date, $quantity = 1) {
        $days = self::calculate_days($start_date, $end_date);
        return floatval($daily_rate) * $quantity * $days;
    }

    /**
     * Log activity
     */
    public static function log_activity($message, $type = 'info', $booking_id = 0, $equipment_id = 0) {
        // This can be enhanced to store in a dedicated log table
        error_log(sprintf(
            '[EquipRent Pro] %s: %s (Booking: %d, Equipment: %d)',
            strtoupper($type),
            $message,
            $booking_id,
            $equipment_id
        ));
    }

    /**
     * Send notification email
     */
    public static function send_notification_email($to, $subject, $message, $booking_id = 0) {
        $headers = array('Content-Type: text/html; charset=UTF-8');
        
        // Add booking details to email if provided
        if ($booking_id > 0) {
            $booking = new ERP_Booking($booking_id);
            $booking_number = $booking->get('booking_number');
            $subject = "[{$booking_number}] {$subject}";
        }
        
        return wp_mail($to, $subject, $message, $headers);
    }

    /**
     * Format file size
     */
    public static function format_file_size($bytes) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get user display name
     */
    public static function get_user_display_name($user_id) {
        $user = get_user_by('id', $user_id);
        
        if (!$user) {
            return __('Unknown User', 'equiprent-pro');
        }
        
        return $user->display_name ?: $user->user_login;
    }

    /**
     * Check if user can access feature
     */
    public static function user_can_access($capability) {
        return current_user_can($capability);
    }

    /**
     * Get plugin settings
     */
    public static function get_setting($key, $default = '') {
        $settings = get_option('erp_general_settings', array());
        return isset($settings[$key]) ? $settings[$key] : $default;
    }

    /**
     * Update plugin setting
     */
    public static function update_setting($key, $value) {
        $settings = get_option('erp_general_settings', array());
        $settings[$key] = $value;
        return update_option('erp_general_settings', $settings);
    }
}