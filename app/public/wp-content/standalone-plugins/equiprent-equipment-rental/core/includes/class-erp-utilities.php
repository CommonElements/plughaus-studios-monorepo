<?php
/**
 * Utility functions for EquipRent Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Utilities class
 */
class ERP_Utilities {

    /**
     * Format currency amount
     */
    public static function format_currency($amount, $currency = 'USD') {
        if (!is_numeric($amount)) {
            return '-';
        }
        
        $symbol = '$'; // Default to USD
        return $symbol . number_format((float)$amount, 2);
    }

    /**
     * Get status badge HTML
     */
    public static function get_status_badge($status, $type = 'general') {
        $statuses = array(
            'equipment' => array(
                'available' => array('label' => 'Available', 'class' => 'success'),
                'rented' => array('label' => 'Rented', 'class' => 'warning'),
                'maintenance' => array('label' => 'Maintenance', 'class' => 'danger'),
                'out_of_service' => array('label' => 'Out of Service', 'class' => 'secondary')
            ),
            'booking' => array(
                'pending' => array('label' => 'Pending', 'class' => 'warning'),
                'confirmed' => array('label' => 'Confirmed', 'class' => 'success'),
                'active' => array('label' => 'Active', 'class' => 'primary'),
                'completed' => array('label' => 'Completed', 'class' => 'secondary'),
                'cancelled' => array('label' => 'Cancelled', 'class' => 'danger')
            )
        );

        if (!isset($statuses[$type][$status])) {
            return esc_html(ucfirst($status));
        }

        $config = $statuses[$type][$status];
        return sprintf(
            '<span class="badge badge-%s">%s</span>',
            esc_attr($config['class']),
            esc_html($config['label'])
        );
    }

    /**
     * Sanitize and validate input data
     */
    public static function sanitize_input($input, $type = 'text') {
        switch ($type) {
            case 'email':
                return function_exists('sanitize_email') ? sanitize_email($input) : filter_var($input, FILTER_SANITIZE_EMAIL);
                
            case 'url':
                return function_exists('esc_url_raw') ? esc_url_raw($input) : filter_var($input, FILTER_SANITIZE_URL);
                
            case 'number':
                return is_numeric($input) ? (float)$input : 0;
                
            case 'int':
                return (int)$input;
                
            case 'textarea':
                return function_exists('sanitize_textarea_field') ? sanitize_textarea_field($input) : strip_tags($input);
                
            default:
                return function_exists('sanitize_text_field') ? sanitize_text_field($input) : strip_tags($input);
        }
    }

    /**
     * Generate equipment serial number
     */
    public static function generate_serial_number($prefix = 'ERP') {
        return $prefix . '-' . date('Y') . '-' . sprintf('%06d', wp_rand(1, 999999));
    }

    /**
     * Calculate rental price based on duration and rate type
     */
    public static function calculate_rental_price($daily_rate, $start_date, $end_date, $weekly_rate = null, $monthly_rate = null) {
        if (!$daily_rate || !$start_date || !$end_date) {
            return 0;
        }

        $start = new DateTime($start_date);
        $end = new DateTime($end_date);
        $days = $start->diff($end)->days + 1;

        // If monthly rate available and rental is 30+ days
        if ($monthly_rate && $days >= 30) {
            $months = floor($days / 30);
            $remaining_days = $days % 30;
            return ($months * $monthly_rate) + ($remaining_days * $daily_rate);
        }

        // If weekly rate available and rental is 7+ days
        if ($weekly_rate && $days >= 7) {
            $weeks = floor($days / 7);
            $remaining_days = $days % 7;
            return ($weeks * $weekly_rate) + ($remaining_days * $daily_rate);
        }

        // Default to daily rate
        return $days * $daily_rate;
    }

    /**
     * Get equipment availability status
     */
    public static function get_equipment_availability($equipment_id, $start_date, $end_date) {
        global $wpdb;
        
        if (!$equipment_id || !$start_date || !$end_date) {
            return false;
        }

        // Check for conflicting bookings
        $table_name = $wpdb->prefix . 'erp_bookings';
        
        if (!function_exists('get_results')) {
            return true; // Assume available if no database access
        }

        $query = $wpdb->prepare("
            SELECT COUNT(*) as conflicts 
            FROM {$table_name} 
            WHERE equipment_id = %d 
            AND status IN ('confirmed', 'active')
            AND (
                (start_date <= %s AND end_date >= %s)
                OR (start_date <= %s AND end_date >= %s)
                OR (start_date >= %s AND end_date <= %s)
            )
        ", $equipment_id, $start_date, $start_date, $end_date, $end_date, $start_date, $end_date);

        $result = $wpdb->get_var($query);
        return (int)$result === 0;
    }

    /**
     * Log plugin activity
     */
    public static function log($message, $level = 'info') {
        if (defined('WP_DEBUG') && WP_DEBUG && function_exists('error_log')) {
            error_log(sprintf('[EquipRent Pro] [%s] %s', strtoupper($level), $message));
        }
    }

    /**
     * Get formatted date for display
     */
    public static function format_date($date, $format = 'Y-m-d') {
        if (empty($date)) {
            return '-';
        }
        
        try {
            $datetime = new DateTime($date);
            return $datetime->format($format);
        } catch (Exception $e) {
            return $date;
        }
    }

    /**
     * Check if pro features are available
     */
    public static function is_pro_active() {
        if (class_exists('ERP_License_Manager')) {
            return ERP_License_Manager::is_valid();
        }
        return false;
    }

    /**
     * Get equipment image URL
     */
    public static function get_equipment_image($equipment_id, $size = 'medium') {
        if (!$equipment_id) {
            return '';
        }

        if (function_exists('get_the_post_thumbnail_url')) {
            $image_url = get_the_post_thumbnail_url($equipment_id, $size);
            if ($image_url) {
                return $image_url;
            }
        }

        // Return placeholder image
        return ERP_PLUGIN_URL . 'assets/images/equipment-placeholder.png';
    }

    /**
     * Generate QR code for equipment (Pro feature)
     */
    public static function generate_qr_code($equipment_id, $size = 200) {
        if (!self::is_pro_active()) {
            return false;
        }

        $equipment_url = get_permalink($equipment_id);
        if (!$equipment_url) {
            return false;
        }

        // In a real implementation, this would use a QR code library
        // For now, return a placeholder URL
        return sprintf(
            'https://api.qrserver.com/v1/create-qr-code/?size=%dx%d&data=%s',
            $size,
            $size,
            urlencode($equipment_url)
        );
    }
}