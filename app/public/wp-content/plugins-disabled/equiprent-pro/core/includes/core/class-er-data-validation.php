<?php
/**
 * EquipRent Pro Data Validation
 *
 * @package EquipRent_Pro
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handles data validation for equipment rental
 */
class ER_Data_Validation {

    /**
     * Validate equipment data
     */
    public static function validate_equipment($data) {
        $errors = array();
        
        // Required fields
        if (empty($data['title'])) {
            $errors['title'] = __('Equipment name is required.', 'equiprent-pro');
        }
        
        // Validate rates
        if (isset($data['daily_rate']) && !empty($data['daily_rate'])) {
            if (!is_numeric($data['daily_rate']) || $data['daily_rate'] < 0) {
                $errors['daily_rate'] = __('Daily rate must be a valid positive number.', 'equiprent-pro');
            }
        }
        
        if (isset($data['weekly_rate']) && !empty($data['weekly_rate'])) {
            if (!is_numeric($data['weekly_rate']) || $data['weekly_rate'] < 0) {
                $errors['weekly_rate'] = __('Weekly rate must be a valid positive number.', 'equiprent-pro');
            }
        }
        
        if (isset($data['monthly_rate']) && !empty($data['monthly_rate'])) {
            if (!is_numeric($data['monthly_rate']) || $data['monthly_rate'] < 0) {
                $errors['monthly_rate'] = __('Monthly rate must be a valid positive number.', 'equiprent-pro');
            }
        }
        
        // Validate quantity
        if (isset($data['quantity'])) {
            if (!is_numeric($data['quantity']) || $data['quantity'] < 1) {
                $errors['quantity'] = __('Quantity must be at least 1.', 'equiprent-pro');
            }
        }
        
        return $errors;
    }

    /**
     * Validate booking data
     */
    public static function validate_booking($data) {
        $errors = array();
        
        // Required fields
        if (empty($data['customer_id'])) {
            $errors['customer_id'] = __('Customer is required.', 'equiprent-pro');
        }
        
        if (empty($data['start_date'])) {
            $errors['start_date'] = __('Start date is required.', 'equiprent-pro');
        }
        
        if (empty($data['end_date'])) {
            $errors['end_date'] = __('End date is required.', 'equiprent-pro');
        }
        
        // Validate dates
        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            $start = strtotime($data['start_date']);
            $end = strtotime($data['end_date']);
            
            if ($start === false) {
                $errors['start_date'] = __('Invalid start date format.', 'equiprent-pro');
            }
            
            if ($end === false) {
                $errors['end_date'] = __('Invalid end date format.', 'equiprent-pro');
            }
            
            if ($start && $end && $end <= $start) {
                $errors['end_date'] = __('End date must be after start date.', 'equiprent-pro');
            }
            
            // Check if start date is in the past
            if ($start && $start < strtotime('today')) {
                $errors['start_date'] = __('Start date cannot be in the past.', 'equiprent-pro');
            }
        }
        
        // Validate equipment items
        if (empty($data['equipment_items']) || !is_array($data['equipment_items'])) {
            $errors['equipment_items'] = __('At least one equipment item is required.', 'equiprent-pro');
        }
        
        return $errors;
    }

    /**
     * Validate customer data
     */
    public static function validate_customer($data) {
        $errors = array();
        
        // Required fields
        if (empty($data['name'])) {
            $errors['name'] = __('Customer name is required.', 'equiprent-pro');
        }
        
        // Validate email
        if (!empty($data['email'])) {
            if (!is_email($data['email'])) {
                $errors['email'] = __('Invalid email address.', 'equiprent-pro');
            }
        }
        
        // Validate phone
        if (!empty($data['phone'])) {
            if (!self::validate_phone($data['phone'])) {
                $errors['phone'] = __('Invalid phone number format.', 'equiprent-pro');
            }
        }
        
        return $errors;
    }

    /**
     * Validate phone number
     */
    public static function validate_phone($phone) {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Check if it's a valid length (10-15 digits)
        return strlen($phone) >= 10 && strlen($phone) <= 15;
    }

    /**
     * Sanitize equipment data
     */
    public static function sanitize_equipment($data) {
        $sanitized = array();
        
        if (isset($data['title'])) {
            $sanitized['title'] = sanitize_text_field($data['title']);
        }
        
        if (isset($data['description'])) {
            $sanitized['description'] = wp_kses_post($data['description']);
        }
        
        if (isset($data['sku'])) {
            $sanitized['sku'] = sanitize_text_field($data['sku']);
        }
        
        if (isset($data['daily_rate'])) {
            $sanitized['daily_rate'] = floatval($data['daily_rate']);
        }
        
        if (isset($data['weekly_rate'])) {
            $sanitized['weekly_rate'] = floatval($data['weekly_rate']);
        }
        
        if (isset($data['monthly_rate'])) {
            $sanitized['monthly_rate'] = floatval($data['monthly_rate']);
        }
        
        if (isset($data['quantity'])) {
            $sanitized['quantity'] = intval($data['quantity']);
        }
        
        if (isset($data['status'])) {
            $valid_statuses = array_keys(ER_Post_Types::get_equipment_statuses());
            $sanitized['status'] = in_array($data['status'], $valid_statuses) ? $data['status'] : 'available';
        }
        
        return $sanitized;
    }

    /**
     * Sanitize booking data
     */
    public static function sanitize_booking($data) {
        $sanitized = array();
        
        if (isset($data['customer_id'])) {
            $sanitized['customer_id'] = intval($data['customer_id']);
        }
        
        if (isset($data['start_date'])) {
            $sanitized['start_date'] = sanitize_text_field($data['start_date']);
        }
        
        if (isset($data['end_date'])) {
            $sanitized['end_date'] = sanitize_text_field($data['end_date']);
        }
        
        if (isset($data['notes'])) {
            $sanitized['notes'] = sanitize_textarea_field($data['notes']);
        }
        
        if (isset($data['status'])) {
            $valid_statuses = array_keys(ER_Post_Types::get_booking_statuses());
            $sanitized['status'] = in_array($data['status'], $valid_statuses) ? $data['status'] : 'pending';
        }
        
        if (isset($data['equipment_items']) && is_array($data['equipment_items'])) {
            $sanitized['equipment_items'] = array();
            foreach ($data['equipment_items'] as $item) {
                $sanitized['equipment_items'][] = array(
                    'equipment_id' => intval($item['equipment_id']),
                    'quantity' => intval($item['quantity']),
                    'daily_rate' => floatval($item['daily_rate'])
                );
            }
        }
        
        return $sanitized;
    }

    /**
     * Sanitize customer data
     */
    public static function sanitize_customer($data) {
        $sanitized = array();
        
        if (isset($data['name'])) {
            $sanitized['name'] = sanitize_text_field($data['name']);
        }
        
        if (isset($data['email'])) {
            $sanitized['email'] = sanitize_email($data['email']);
        }
        
        if (isset($data['phone'])) {
            $sanitized['phone'] = sanitize_text_field($data['phone']);
        }
        
        if (isset($data['address'])) {
            $sanitized['address'] = sanitize_textarea_field($data['address']);
        }
        
        if (isset($data['customer_type'])) {
            $sanitized['customer_type'] = in_array($data['customer_type'], array('individual', 'business')) ? $data['customer_type'] : 'individual';
        }
        
        if (isset($data['company_name'])) {
            $sanitized['company_name'] = sanitize_text_field($data['company_name']);
        }
        
        return $sanitized;
    }
}