<?php
/**
 * GymFlow Utilities Class
 *
 * Provides common utility functions used throughout the plugin
 *
 * @package GymFlow
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * GF_Utilities Class
 *
 * Central utility functions for GymFlow plugin
 */
class GF_Utilities {

    /**
     * Format currency value
     *
     * @param float $amount The amount to format
     * @param string $currency Currency code (default: USD)
     * @return string Formatted currency string
     */
    public static function format_currency($amount, $currency = 'USD') {
        $amount = floatval($amount);
        
        switch ($currency) {
            case 'USD':
                return '$' . number_format($amount, 2);
            case 'EUR':
                return '€' . number_format($amount, 2);
            case 'GBP':
                return '£' . number_format($amount, 2);
            default:
                return $currency . ' ' . number_format($amount, 2);
        }
    }

    /**
     * Format date for display
     *
     * @param string $date Date string
     * @param string $format Date format (default: WordPress setting)
     * @return string Formatted date
     */
    public static function format_date($date, $format = null) {
        if (empty($date)) {
            return '';
        }

        if ($format === null) {
            $format = get_option('date_format');
        }

        $timestamp = is_numeric($date) ? $date : strtotime($date);
        return date_i18n($format, $timestamp);
    }

    /**
     * Format time for display
     *
     * @param string $time Time string
     * @param string $format Time format (default: WordPress setting)
     * @return string Formatted time
     */
    public static function format_time($time, $format = null) {
        if (empty($time)) {
            return '';
        }

        if ($format === null) {
            $format = get_option('time_format');
        }

        $timestamp = is_numeric($time) ? $time : strtotime($time);
        return date_i18n($format, $timestamp);
    }

    /**
     * Format datetime for display
     *
     * @param string $datetime DateTime string
     * @return string Formatted datetime
     */
    public static function format_datetime($datetime) {
        if (empty($datetime)) {
            return '';
        }

        $date_format = get_option('date_format');
        $time_format = get_option('time_format');
        
        $timestamp = is_numeric($datetime) ? $datetime : strtotime($datetime);
        return date_i18n($date_format . ' ' . $time_format, $timestamp);
    }

    /**
     * Sanitize input data
     *
     * @param mixed $data Data to sanitize
     * @param string $type Type of sanitization
     * @return mixed Sanitized data
     */
    public static function sanitize_input($data, $type = 'text') {
        switch ($type) {
            case 'email':
                return sanitize_email($data);
            case 'url':
                return esc_url_raw($data);
            case 'int':
                return intval($data);
            case 'float':
                return floatval($data);
            case 'textarea':
                return sanitize_textarea_field($data);
            case 'key':
                return sanitize_key($data);
            case 'filename':
                return sanitize_file_name($data);
            case 'html':
                return wp_kses_post($data);
            case 'text':
            default:
                return sanitize_text_field($data);
        }
    }

    /**
     * Validate input data
     *
     * @param mixed $data Data to validate
     * @param string $type Type of validation
     * @return bool|WP_Error True if valid, WP_Error if invalid
     */
    public static function validate_input($data, $type = 'text') {
        switch ($type) {
            case 'email':
                if (!is_email($data)) {
                    return new WP_Error('invalid_email', __('Invalid email address.', 'gymflow'));
                }
                break;
                
            case 'url':
                if (!filter_var($data, FILTER_VALIDATE_URL)) {
                    return new WP_Error('invalid_url', __('Invalid URL.', 'gymflow'));
                }
                break;
                
            case 'phone':
                $phone = preg_replace('/[^0-9+\-\(\)\s]/', '', $data);
                if (strlen($phone) < 10) {
                    return new WP_Error('invalid_phone', __('Invalid phone number.', 'gymflow'));
                }
                break;
                
            case 'required':
                if (empty($data)) {
                    return new WP_Error('required_field', __('This field is required.', 'gymflow'));
                }
                break;
                
            case 'min_length':
                if (strlen($data) < 3) {
                    return new WP_Error('min_length', __('Minimum 3 characters required.', 'gymflow'));
                }
                break;
        }

        return true;
    }

    /**
     * Generate unique ID
     *
     * @param string $prefix Optional prefix
     * @return string Unique ID
     */
    public static function generate_unique_id($prefix = 'gf') {
        return uniqid($prefix . '_', true);
    }

    /**
     * Generate random string
     *
     * @param int $length String length
     * @param string $chars Character set
     * @return string Random string
     */
    public static function generate_random_string($length = 10, $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789') {
        $str = '';
        $chars_length = strlen($chars);
        
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[random_int(0, $chars_length - 1)];
        }
        
        return $str;
    }

    /**
     * Calculate age from birthdate
     *
     * @param string $birthdate Birthdate string
     * @return int Age in years
     */
    public static function calculate_age($birthdate) {
        if (empty($birthdate)) {
            return 0;
        }

        $birth = new DateTime($birthdate);
        $today = new DateTime();
        
        return $today->diff($birth)->y;
    }

    /**
     * Get time slots for scheduling
     *
     * @param string $start_time Start time (H:i format)
     * @param string $end_time End time (H:i format)
     * @param int $interval Interval in minutes
     * @return array Array of time slots
     */
    public static function get_time_slots($start_time = '06:00', $end_time = '22:00', $interval = 60) {
        $slots = array();
        $start = strtotime($start_time);
        $end = strtotime($end_time);
        
        for ($time = $start; $time <= $end; $time += ($interval * 60)) {
            $slots[date('H:i', $time)] = date('g:i A', $time);
        }
        
        return $slots;
    }

    /**
     * Check if time slots overlap
     *
     * @param string $start1 First slot start time
     * @param string $end1 First slot end time
     * @param string $start2 Second slot start time
     * @param string $end2 Second slot end time
     * @return bool True if slots overlap
     */
    public static function time_slots_overlap($start1, $end1, $start2, $end2) {
        $start1_ts = strtotime($start1);
        $end1_ts = strtotime($end1);
        $start2_ts = strtotime($start2);
        $end2_ts = strtotime($end2);
        
        return ($start1_ts < $end2_ts) && ($end1_ts > $start2_ts);
    }

    /**
     * Get member initials for avatar
     *
     * @param string $first_name First name
     * @param string $last_name Last name
     * @return string Initials
     */
    public static function get_initials($first_name, $last_name = '') {
        $initials = strtoupper(substr($first_name, 0, 1));
        
        if (!empty($last_name)) {
            $initials .= strtoupper(substr($last_name, 0, 1));
        }
        
        return $initials;
    }

    /**
     * Get membership status badge HTML
     *
     * @param string $status Membership status
     * @return string HTML badge
     */
    public static function get_status_badge($status) {
        $statuses = array(
            'active' => array(
                'label' => __('Active', 'gymflow'),
                'class' => 'status-active'
            ),
            'expired' => array(
                'label' => __('Expired', 'gymflow'),
                'class' => 'status-expired'
            ),
            'pending' => array(
                'label' => __('Pending', 'gymflow'),
                'class' => 'status-pending'
            ),
            'cancelled' => array(
                'label' => __('Cancelled', 'gymflow'),
                'class' => 'status-cancelled'
            ),
            'on_hold' => array(
                'label' => __('On Hold', 'gymflow'),
                'class' => 'status-on-hold'
            )
        );

        $status_info = isset($statuses[$status]) ? $statuses[$status] : $statuses['pending'];
        
        return sprintf(
            '<span class="gymflow-status-badge %s">%s</span>',
            esc_attr($status_info['class']),
            esc_html($status_info['label'])
        );
    }

    /**
     * Get equipment status badge HTML
     *
     * @param string $status Equipment status
     * @return string HTML badge
     */
    public static function get_equipment_status_badge($status) {
        $statuses = array(
            'available' => array(
                'label' => __('Available', 'gymflow'),
                'class' => 'status-available'
            ),
            'booked' => array(
                'label' => __('Booked', 'gymflow'),
                'class' => 'status-booked'
            ),
            'maintenance' => array(
                'label' => __('Maintenance', 'gymflow'),
                'class' => 'status-maintenance'
            ),
            'out_of_order' => array(
                'label' => __('Out of Order', 'gymflow'),
                'class' => 'status-out-of-order'
            )
        );

        $status_info = isset($statuses[$status]) ? $statuses[$status] : $statuses['available'];
        
        return sprintf(
            '<span class="gymflow-equipment-status %s">%s</span>',
            esc_attr($status_info['class']),
            esc_html($status_info['label'])
        );
    }

    /**
     * Log debug message
     *
     * @param mixed $message Message to log
     * @param string $level Log level (debug, info, warning, error)
     */
    public static function log($message, $level = 'debug') {
        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            return;
        }

        if (is_array($message) || is_object($message)) {
            $message = print_r($message, true);
        }

        $log_message = sprintf(
            '[%s] GymFlow %s: %s',
            current_time('Y-m-d H:i:s'),
            strtoupper($level),
            $message
        );

        error_log($log_message);
    }

    /**
     * Check if current user can manage GymFlow
     *
     * @return bool True if user has management capabilities
     */
    public static function current_user_can_manage() {
        return current_user_can('manage_gymflow') || current_user_can('manage_options');
    }

    /**
     * Check if current user can edit GymFlow content
     *
     * @return bool True if user has edit capabilities
     */
    public static function current_user_can_edit() {
        return current_user_can('edit_gymflow') || current_user_can('manage_gymflow') || current_user_can('manage_options');
    }

    /**
     * Get plugin option with default value
     *
     * @param string $option_name Option name
     * @param mixed $default Default value
     * @return mixed Option value
     */
    public static function get_option($option_name, $default = false) {
        return get_option('gymflow_' . $option_name, $default);
    }

    /**
     * Update plugin option
     *
     * @param string $option_name Option name
     * @param mixed $value Option value
     * @return bool True if updated successfully
     */
    public static function update_option($option_name, $value) {
        return update_option('gymflow_' . $option_name, $value);
    }

    /**
     * Delete plugin option
     *
     * @param string $option_name Option name
     * @return bool True if deleted successfully
     */
    public static function delete_option($option_name) {
        return delete_option('gymflow_' . $option_name);
    }

    /**
     * Get upload directory for GymFlow files
     *
     * @return array Upload directory info
     */
    public static function get_upload_dir() {
        $upload_dir = wp_upload_dir();
        $gymflow_dir = $upload_dir['basedir'] . '/gymflow';
        $gymflow_url = $upload_dir['baseurl'] . '/gymflow';

        // Create directory if it doesn't exist
        if (!file_exists($gymflow_dir)) {
            wp_mkdir_p($gymflow_dir);
            
            // Create index.php to prevent directory browsing
            $index_file = $gymflow_dir . '/index.php';
            if (!file_exists($index_file)) {
                file_put_contents($index_file, '<?php // Silence is golden');
            }
        }

        return array(
            'path' => $gymflow_dir,
            'url' => $gymflow_url,
            'relative' => '/gymflow'
        );
    }

    /**
     * Clean up text for use in URLs or IDs
     *
     * @param string $text Text to clean
     * @return string Cleaned text
     */
    public static function clean_text_for_id($text) {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\-_]/', '', $text);
        $text = preg_replace('/[\-_]+/', '-', $text);
        return trim($text, '-_');
    }

    /**
     * Check if string contains only digits
     *
     * @param string $string String to check
     * @return bool True if numeric
     */
    public static function is_numeric_string($string) {
        return ctype_digit($string);
    }

    /**
     * Convert bytes to human readable format
     *
     * @param int $bytes Bytes
     * @param int $precision Decimal places
     * @return string Human readable size
     */
    public static function format_bytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}