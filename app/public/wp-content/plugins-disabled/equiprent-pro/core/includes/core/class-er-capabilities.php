<?php
/**
 * EquipRent Pro Capabilities
 *
 * @package EquipRent_Pro
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handles user capabilities for equipment rental
 */
class ER_Capabilities {

    /**
     * Initialize the class
     */
    public static function init() {
        add_filter('map_meta_cap', array(__CLASS__, 'map_meta_caps'), 10, 4);
    }

    /**
     * Map meta capabilities to primitive capabilities
     */
    public static function map_meta_caps($caps, $cap, $user_id, $args) {
        // Handle equipment capabilities
        if (strpos($cap, 'equipment') !== false) {
            return self::map_equipment_caps($caps, $cap, $user_id, $args);
        }

        // Handle booking capabilities
        if (strpos($cap, 'booking') !== false) {
            return self::map_booking_caps($caps, $cap, $user_id, $args);
        }

        // Handle customer capabilities
        if (strpos($cap, 'rental_customer') !== false) {
            return self::map_customer_caps($caps, $cap, $user_id, $args);
        }

        return $caps;
    }

    /**
     * Map equipment capabilities
     */
    private static function map_equipment_caps($caps, $cap, $user_id, $args) {
        switch ($cap) {
            case 'edit_equipment':
                $caps = array('edit_equipment');
                break;

            case 'read_equipment':
                $caps = array('read');
                break;

            case 'delete_equipment':
                $caps = array('delete_equipment');
                break;

            case 'edit_equipments':
                $caps = array('edit_equipment');
                break;

            case 'edit_others_equipments':
                $caps = array('edit_equipment');
                break;

            case 'publish_equipments':
                $caps = array('edit_equipment');
                break;

            case 'read_private_equipments':
                $caps = array('read');
                break;

            case 'delete_equipments':
                $caps = array('delete_equipment');
                break;

            case 'delete_private_equipments':
                $caps = array('delete_equipment');
                break;

            case 'delete_published_equipments':
                $caps = array('delete_equipment');
                break;

            case 'delete_others_equipments':
                $caps = array('delete_equipment');
                break;

            case 'edit_private_equipments':
                $caps = array('edit_equipment');
                break;

            case 'edit_published_equipments':
                $caps = array('edit_equipment');
                break;
        }

        return $caps;
    }

    /**
     * Map booking capabilities
     */
    private static function map_booking_caps($caps, $cap, $user_id, $args) {
        switch ($cap) {
            case 'edit_booking':
                $caps = array('edit_bookings');
                
                // If editing a specific booking, check ownership
                if (isset($args[0])) {
                    $post = get_post($args[0]);
                    if ($post && $post->post_author != $user_id && !user_can($user_id, 'manage_bookings')) {
                        $caps[] = 'do_not_allow';
                    }
                }
                break;

            case 'read_booking':
                $caps = array('read');
                
                // If reading a specific booking, check ownership or permissions
                if (isset($args[0])) {
                    $post = get_post($args[0]);
                    if ($post && $post->post_author != $user_id && !user_can($user_id, 'manage_bookings')) {
                        $caps[] = 'do_not_allow';
                    }
                }
                break;

            case 'delete_booking':
                $caps = array('delete_bookings');
                
                // If deleting a specific booking, check ownership
                if (isset($args[0])) {
                    $post = get_post($args[0]);
                    if ($post && $post->post_author != $user_id && !user_can($user_id, 'manage_bookings')) {
                        $caps[] = 'do_not_allow';
                    }
                }
                break;

            case 'edit_bookings':
                $caps = array('edit_bookings');
                break;

            case 'edit_others_bookings':
                $caps = array('manage_bookings');
                break;

            case 'publish_bookings':
                $caps = array('edit_bookings');
                break;

            case 'read_private_bookings':
                $caps = array('manage_bookings');
                break;

            case 'delete_bookings':
                $caps = array('delete_bookings');
                break;

            case 'delete_private_bookings':
                $caps = array('manage_bookings');
                break;

            case 'delete_published_bookings':
                $caps = array('delete_bookings');
                break;

            case 'delete_others_bookings':
                $caps = array('manage_bookings');
                break;

            case 'edit_private_bookings':
                $caps = array('edit_bookings');
                break;

            case 'edit_published_bookings':
                $caps = array('edit_bookings');
                break;
        }

        return $caps;
    }

    /**
     * Map customer capabilities
     */
    private static function map_customer_caps($caps, $cap, $user_id, $args) {
        switch ($cap) {
            case 'edit_rental_customer':
                $caps = array('edit_rental_customers');
                break;

            case 'read_rental_customer':
                $caps = array('read');
                break;

            case 'delete_rental_customer':
                $caps = array('delete_rental_customers');
                break;

            case 'edit_rental_customers':
                $caps = array('edit_rental_customers');
                break;

            case 'edit_others_rental_customers':
                $caps = array('manage_rental_customers');
                break;

            case 'publish_rental_customers':
                $caps = array('edit_rental_customers');
                break;

            case 'read_private_rental_customers':
                $caps = array('manage_rental_customers');
                break;

            case 'delete_rental_customers':
                $caps = array('delete_rental_customers');
                break;

            case 'delete_private_rental_customers':
                $caps = array('delete_rental_customers');
                break;

            case 'delete_published_rental_customers':
                $caps = array('delete_rental_customers');
                break;

            case 'delete_others_rental_customers':
                $caps = array('manage_rental_customers');
                break;

            case 'edit_private_rental_customers':
                $caps = array('edit_rental_customers');
                break;

            case 'edit_published_rental_customers':
                $caps = array('edit_rental_customers');
                break;
        }

        return $caps;
    }

    /**
     * Get all custom capabilities
     */
    public static function get_all_capabilities() {
        return array(
            // Equipment capabilities
            'manage_equipment',
            'edit_equipment',
            'delete_equipment',
            'create_equipment',
            
            // Booking capabilities
            'manage_bookings',
            'edit_bookings',
            'delete_bookings',
            'create_bookings',
            
            // Customer capabilities
            'manage_rental_customers',
            'edit_rental_customers',
            'delete_rental_customers',
            
            // Settings capabilities
            'manage_equiprent_settings',
            
            // Reporting capabilities
            'view_equiprent_reports',
        );
    }

    /**
     * Check if user can manage equipment
     */
    public static function user_can_manage_equipment($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        return user_can($user_id, 'manage_equipment');
    }

    /**
     * Check if user can manage bookings
     */
    public static function user_can_manage_bookings($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        return user_can($user_id, 'manage_bookings');
    }

    /**
     * Check if user can manage customers
     */
    public static function user_can_manage_customers($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        return user_can($user_id, 'manage_rental_customers');
    }

    /**
     * Check if user can access settings
     */
    public static function user_can_manage_settings($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        return user_can($user_id, 'manage_equiprent_settings');
    }

    /**
     * Check if user can view reports
     */
    public static function user_can_view_reports($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        return user_can($user_id, 'view_equiprent_reports');
    }
}