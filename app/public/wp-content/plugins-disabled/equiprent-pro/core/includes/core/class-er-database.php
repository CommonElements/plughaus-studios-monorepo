<?php
/**
 * EquipRent Pro Database
 *
 * @package EquipRent_Pro
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handles database operations for equipment rental
 */
class ER_Database {

    /**
     * Initialize the class
     */
    public static function init() {
        // Database operations are handled in activator for now
        // This class can be extended for complex database operations
    }

    /**
     * Get table names
     */
    public static function get_table_names() {
        global $wpdb;
        
        return array(
            'equipment' => $wpdb->prefix . 'er_equipment',
            'bookings' => $wpdb->prefix . 'er_bookings',
            'booking_items' => $wpdb->prefix . 'er_booking_items',
            'customers' => $wpdb->prefix . 'er_customers',
            'maintenance' => $wpdb->prefix . 'er_maintenance',
            'damage_reports' => $wpdb->prefix . 'er_damage_reports',
            'payments' => $wpdb->prefix . 'er_payments',
            'activity_log' => $wpdb->prefix . 'er_activity_log',
            'availability' => $wpdb->prefix . 'er_availability',
            'documents' => $wpdb->prefix . 'er_documents',
        );
    }

    /**
     * Get equipment data
     */
    public static function get_equipment($equipment_id) {
        global $wpdb;
        $table = self::get_table_names()['equipment'];
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE post_id = %d",
            $equipment_id
        ));
    }

    /**
     * Get booking data
     */
    public static function get_booking($booking_id) {
        global $wpdb;
        $table = self::get_table_names()['bookings'];
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE id = %d",
            $booking_id
        ));
    }

    /**
     * Get customer data
     */
    public static function get_customer($customer_id) {
        global $wpdb;
        $table = self::get_table_names()['customers'];
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE id = %d",
            $customer_id
        ));
    }

    /**
     * Get payment data
     */
    public static function get_payment($payment_id) {
        global $wpdb;
        $table = self::get_table_names()['payments'];
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE id = %d",
            $payment_id
        ));
    }

    /**
     * Get damage report data
     */
    public static function get_damage_report($report_id) {
        global $wpdb;
        $table = self::get_table_names()['damage_reports'];
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE id = %d",
            $report_id
        ));
    }

    /**
     * Get maintenance record
     */
    public static function get_maintenance_record($maintenance_id) {
        global $wpdb;
        $table = self::get_table_names()['maintenance'];
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE id = %d",
            $maintenance_id
        ));
    }

    /**
     * Get availability data
     */
    public static function get_availability($equipment_id, $date = null) {
        global $wpdb;
        $table = self::get_table_names()['availability'];
        
        if ($date) {
            return $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$table} WHERE equipment_id = %d AND date = %s",
                $equipment_id, $date
            ));
        } else {
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$table} WHERE equipment_id = %d ORDER BY date ASC",
                $equipment_id
            ));
        }
    }

    /**
     * Log activity
     */
    public static function log_activity($entity_type, $entity_id, $action, $description = '', $metadata = array()) {
        global $wpdb;
        $table = self::get_table_names()['activity_log'];
        
        $data = array(
            'entity_type' => $entity_type,
            'entity_id' => $entity_id,
            'action' => $action,
            'description' => $description,
            'user_id' => get_current_user_id(),
            'ip_address' => self::get_client_ip(),
            'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
            'metadata' => wp_json_encode($metadata),
            'created_at' => current_time('mysql')
        );
        
        return $wpdb->insert($table, $data);
    }

    /**
     * Get client IP address
     */
    private static function get_client_ip() {
        $ip_keys = array('HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR');
        
        foreach ($ip_keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Check if tables exist
     */
    public static function tables_exist() {
        global $wpdb;
        $tables = self::get_table_names();
        
        foreach ($tables as $table) {
            if ($wpdb->get_var("SHOW TABLES LIKE '{$table}'") !== $table) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Get database schema version
     */
    public static function get_schema_version() {
        return get_option('equiprent_db_version', '0.0.0');
    }
}