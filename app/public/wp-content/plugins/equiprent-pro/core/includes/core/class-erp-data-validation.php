<?php
/**
 * Data validation class for EquipRent Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Data validation utilities
 */
class ERP_Data_Validation {

    /**
     * Validation errors
     */
    private static $errors = array();

    /**
     * Validate equipment data
     */
    public static function validate_equipment($data) {
        self::$errors = array();
        
        // Required fields
        if (empty($data['name'])) {
            self::$errors['name'] = __('Equipment name is required.', 'equiprent-pro');
        }
        
        if (empty($data['category'])) {
            self::$errors['category'] = __('Equipment category is required.', 'equiprent-pro');
        }
        
        // Validate rates
        if (isset($data['daily_rate']) && !empty($data['daily_rate'])) {
            if (!is_numeric($data['daily_rate']) || floatval($data['daily_rate']) < 0) {
                self::$errors['daily_rate'] = __('Daily rate must be a valid positive number.', 'equiprent-pro');
            }
        }
        
        if (isset($data['weekly_rate']) && !empty($data['weekly_rate'])) {
            if (!is_numeric($data['weekly_rate']) || floatval($data['weekly_rate']) < 0) {
                self::$errors['weekly_rate'] = __('Weekly rate must be a valid positive number.', 'equiprent-pro');
            }
        }
        
        if (isset($data['monthly_rate']) && !empty($data['monthly_rate'])) {
            if (!is_numeric($data['monthly_rate']) || floatval($data['monthly_rate']) < 0) {
                self::$errors['monthly_rate'] = __('Monthly rate must be a valid positive number.', 'equiprent-pro');
            }
        }
        
        // Validate deposit amount
        if (isset($data['deposit_amount']) && !empty($data['deposit_amount'])) {
            if (!is_numeric($data['deposit_amount']) || floatval($data['deposit_amount']) < 0) {
                self::$errors['deposit_amount'] = __('Deposit amount must be a valid positive number.', 'equiprent-pro');
            }
        }
        
        // Validate serial number uniqueness
        if (!empty($data['serial_number'])) {
            if (self::is_serial_number_taken($data['serial_number'], isset($data['id']) ? $data['id'] : 0)) {
                self::$errors['serial_number'] = __('This serial number is already in use.', 'equiprent-pro');
            }
        }
        
        // Validate QR code uniqueness
        if (!empty($data['qr_code'])) {
            if (self::is_qr_code_taken($data['qr_code'], isset($data['id']) ? $data['id'] : 0)) {
                self::$errors['qr_code'] = __('This QR code is already in use.', 'equiprent-pro');
            }
        }
        
        // Validate status
        if (!empty($data['status'])) {
            $valid_statuses = array('available', 'rented', 'maintenance', 'retired', 'lost', 'damaged');
            if (!in_array($data['status'], $valid_statuses)) {
                self::$errors['status'] = __('Invalid equipment status.', 'equiprent-pro');
            }
        }
        
        // Validate condition
        if (!empty($data['condition_status'])) {
            $valid_conditions = array('excellent', 'good', 'fair', 'poor', 'needs_repair');
            if (!in_array($data['condition_status'], $valid_conditions)) {
                self::$errors['condition_status'] = __('Invalid condition status.', 'equiprent-pro');
            }
        }
        
        return empty(self::$errors);
    }

    /**
     * Validate customer data
     */
    public static function validate_customer($data) {
        self::$errors = array();
        
        // Required fields
        if (empty($data['contact_first_name'])) {
            self::$errors['contact_first_name'] = __('First name is required.', 'equiprent-pro');
        }
        
        if (empty($data['contact_last_name'])) {
            self::$errors['contact_last_name'] = __('Last name is required.', 'equiprent-pro');
        }
        
        if (empty($data['email'])) {
            self::$errors['email'] = __('Email address is required.', 'equiprent-pro');
        } elseif (!is_email($data['email'])) {
            self::$errors['email'] = __('Please enter a valid email address.', 'equiprent-pro');
        } elseif (self::is_email_taken($data['email'], isset($data['id']) ? $data['id'] : 0)) {
            self::$errors['email'] = __('This email address is already in use.', 'equiprent-pro');
        }
        
        // Validate phone numbers
        if (!empty($data['phone']) && !self::is_valid_phone($data['phone'])) {
            self::$errors['phone'] = __('Please enter a valid phone number.', 'equiprent-pro');
        }
        
        if (!empty($data['mobile']) && !self::is_valid_phone($data['mobile'])) {
            self::$errors['mobile'] = __('Please enter a valid mobile number.', 'equiprent-pro');
        }
        
        // Validate customer type
        if (!empty($data['customer_type'])) {
            $valid_types = array('individual', 'business');
            if (!in_array($data['customer_type'], $valid_types)) {
                self::$errors['customer_type'] = __('Invalid customer type.', 'equiprent-pro');
            }
        }
        
        // Validate credit limit
        if (isset($data['credit_limit']) && !empty($data['credit_limit'])) {
            if (!is_numeric($data['credit_limit']) || floatval($data['credit_limit']) < 0) {
                self::$errors['credit_limit'] = __('Credit limit must be a valid positive number.', 'equiprent-pro');
            }
        }
        
        // Validate status
        if (!empty($data['status'])) {
            $valid_statuses = array('active', 'inactive', 'suspended');
            if (!in_array($data['status'], $valid_statuses)) {
                self::$errors['status'] = __('Invalid customer status.', 'equiprent-pro');
            }
        }
        
        return empty(self::$errors);
    }

    /**
     * Validate booking data
     */
    public static function validate_booking($data) {
        self::$errors = array();
        
        // Required fields
        if (empty($data['customer_id']) || !is_numeric($data['customer_id'])) {
            self::$errors['customer_id'] = __('Please select a valid customer.', 'equiprent-pro');
        }
        
        if (empty($data['start_date'])) {
            self::$errors['start_date'] = __('Start date is required.', 'equiprent-pro');
        }
        
        if (empty($data['end_date'])) {
            self::$errors['end_date'] = __('End date is required.', 'equiprent-pro');
        }
        
        // Validate dates
        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            $start_date = strtotime($data['start_date']);
            $end_date = strtotime($data['end_date']);
            
            if ($start_date === false) {
                self::$errors['start_date'] = __('Please enter a valid start date.', 'equiprent-pro');
            }
            
            if ($end_date === false) {
                self::$errors['end_date'] = __('Please enter a valid end date.', 'equiprent-pro');
            }
            
            if ($start_date !== false && $end_date !== false) {
                if ($end_date < $start_date) {
                    self::$errors['end_date'] = __('End date must be after start date.', 'equiprent-pro');
                }
                
                if ($start_date < strtotime(date('Y-m-d'))) {
                    self::$errors['start_date'] = __('Start date cannot be in the past.', 'equiprent-pro');
                }
            }
        }
        
        // Validate status
        if (!empty($data['status'])) {
            $valid_statuses = array('pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'overdue');
            if (!in_array($data['status'], $valid_statuses)) {
                self::$errors['status'] = __('Invalid booking status.', 'equiprent-pro');
            }
        }
        
        // Validate booking type
        if (!empty($data['booking_type'])) {
            $valid_types = array('rental', 'service', 'demo');
            if (!in_array($data['booking_type'], $valid_types)) {
                self::$errors['booking_type'] = __('Invalid booking type.', 'equiprent-pro');
            }
        }
        
        // Validate pickup/delivery methods
        if (!empty($data['pickup_method'])) {
            $valid_methods = array('customer_pickup', 'delivery');
            if (!in_array($data['pickup_method'], $valid_methods)) {
                self::$errors['pickup_method'] = __('Invalid pickup method.', 'equiprent-pro');
            }
        }
        
        if (!empty($data['return_method'])) {
            $valid_methods = array('customer_return', 'pickup');
            if (!in_array($data['return_method'], $valid_methods)) {
                self::$errors['return_method'] = __('Invalid return method.', 'equiprent-pro');
            }
        }
        
        // Validate payment status
        if (!empty($data['payment_status'])) {
            $valid_statuses = array('pending', 'paid', 'partial', 'overdue', 'refunded');
            if (!in_array($data['payment_status'], $valid_statuses)) {
                self::$errors['payment_status'] = __('Invalid payment status.', 'equiprent-pro');
            }
        }
        
        // Validate amounts
        $amount_fields = array('subtotal', 'tax_amount', 'discount_amount', 'deposit_amount', 'total_amount', 'paid_amount');
        foreach ($amount_fields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                if (!is_numeric($data[$field]) || floatval($data[$field]) < 0) {
                    self::$errors[$field] = sprintf(__('%s must be a valid positive number.', 'equiprent-pro'), ucwords(str_replace('_', ' ', $field)));
                }
            }
        }
        
        return empty(self::$errors);
    }

    /**
     * Check if serial number is already taken
     */
    private static function is_serial_number_taken($serial_number, $exclude_id = 0) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'erp_equipment';
        $query = $wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE serial_number = %s AND id != %d",
            $serial_number,
            $exclude_id
        );
        
        return $wpdb->get_var($query) > 0;
    }

    /**
     * Check if QR code is already taken
     */
    private static function is_qr_code_taken($qr_code, $exclude_id = 0) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'erp_equipment';
        $query = $wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE qr_code = %s AND id != %d",
            $qr_code,
            $exclude_id
        );
        
        return $wpdb->get_var($query) > 0;
    }

    /**
     * Check if email is already taken
     */
    private static function is_email_taken($email, $exclude_id = 0) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'erp_customers';
        $query = $wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE email = %s AND id != %d",
            $email,
            $exclude_id
        );
        
        return $wpdb->get_var($query) > 0;
    }

    /**
     * Validate phone number
     */
    private static function is_valid_phone($phone) {
        // Remove all non-digit characters except + and -
        $cleaned = preg_replace('/[^0-9+\-]/', '', $phone);
        
        // Check if it's at least 10 digits long
        $digits_only = preg_replace('/[^0-9]/', '', $cleaned);
        
        return strlen($digits_only) >= 10;
    }

    /**
     * Get validation errors
     */
    public static function get_errors() {
        return self::$errors;
    }

    /**
     * Get first error message
     */
    public static function get_first_error() {
        return !empty(self::$errors) ? reset(self::$errors) : '';
    }

    /**
     * Add custom error
     */
    public static function add_error($field, $message) {
        self::$errors[$field] = $message;
    }

    /**
     * Clear errors
     */
    public static function clear_errors() {
        self::$errors = array();
    }

    /**
     * Has errors
     */
    public static function has_errors() {
        return !empty(self::$errors);
    }

    /**
     * Sanitize and validate equipment data
     */
    public static function sanitize_equipment_data($data) {
        $sanitized = array();
        
        // String fields
        $string_fields = array('name', 'description', 'category', 'brand', 'model', 'serial_number', 'qr_code', 'status', 'condition_status', 'location', 'dimensions', 'power_requirements', 'accessories', 'maintenance_schedule');
        foreach ($string_fields as $field) {
            if (isset($data[$field])) {
                $sanitized[$field] = sanitize_text_field($data[$field]);
            }
        }
        
        // Numeric fields
        $numeric_fields = array('daily_rate', 'weekly_rate', 'monthly_rate', 'deposit_amount', 'weight', 'purchase_price', 'depreciation_rate', 'insurance_value');
        foreach ($numeric_fields as $field) {
            if (isset($data[$field])) {
                $sanitized[$field] = floatval($data[$field]);
            }
        }
        
        // Date fields
        $date_fields = array('last_maintenance_date', 'next_maintenance_date', 'purchase_date');
        foreach ($date_fields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                $sanitized[$field] = date('Y-m-d', strtotime($data[$field]));
            }
        }
        
        // Text area fields
        if (isset($data['image_gallery'])) {
            $sanitized['image_gallery'] = sanitize_textarea_field($data['image_gallery']);
        }
        
        return $sanitized;
    }

    /**
     * Sanitize and validate customer data
     */
    public static function sanitize_customer_data($data) {
        $sanitized = array();
        
        // String fields
        $string_fields = array('company_name', 'contact_first_name', 'contact_last_name', 'phone', 'mobile', 'address_line_1', 'address_line_2', 'city', 'state', 'postal_code', 'country', 'customer_type', 'payment_terms', 'tax_id', 'license_number', 'insurance_certificate', 'emergency_contact_name', 'emergency_contact_phone', 'status');
        foreach ($string_fields as $field) {
            if (isset($data[$field])) {
                $sanitized[$field] = sanitize_text_field($data[$field]);
            }
        }
        
        // Email field
        if (isset($data['email'])) {
            $sanitized['email'] = sanitize_email($data['email']);
        }
        
        // Numeric fields
        $numeric_fields = array('user_id', 'credit_limit', 'current_balance', 'tax_exempt');
        foreach ($numeric_fields as $field) {
            if (isset($data[$field])) {
                $sanitized[$field] = $field === 'tax_exempt' ? intval($data[$field]) : floatval($data[$field]);
            }
        }
        
        // Text area fields
        $textarea_fields = array('delivery_instructions', 'notes');
        foreach ($textarea_fields as $field) {
            if (isset($data[$field])) {
                $sanitized[$field] = sanitize_textarea_field($data[$field]);
            }
        }
        
        return $sanitized;
    }

    /**
     * Sanitize and validate booking data
     */
    public static function sanitize_booking_data($data) {
        $sanitized = array();
        
        // String fields
        $string_fields = array('booking_number', 'status', 'booking_type', 'pickup_method', 'return_method', 'delivery_time_slot', 'return_time_slot', 'payment_status', 'payment_method', 'transaction_id');
        foreach ($string_fields as $field) {
            if (isset($data[$field])) {
                $sanitized[$field] = sanitize_text_field($data[$field]);
            }
        }
        
        // Numeric fields
        $numeric_fields = array('customer_id', 'subtotal', 'tax_amount', 'discount_amount', 'deposit_amount', 'total_amount', 'paid_amount', 'damage_waiver', 'damage_waiver_fee', 'created_by');
        foreach ($numeric_fields as $field) {
            if (isset($data[$field])) {
                $sanitized[$field] = $field === 'damage_waiver' ? intval($data[$field]) : floatval($data[$field]);
            }
        }
        
        // Date fields
        $date_fields = array('start_date', 'end_date', 'delivery_date', 'return_date');
        foreach ($date_fields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                $sanitized[$field] = date('Y-m-d', strtotime($data[$field]));
            }
        }
        
        // Time fields
        $time_fields = array('start_time', 'end_time');
        foreach ($time_fields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                $sanitized[$field] = date('H:i:s', strtotime($data[$field]));
            }
        }
        
        // Text area fields
        $textarea_fields = array('delivery_address', 'delivery_instructions', 'return_address', 'special_instructions', 'notes');
        foreach ($textarea_fields as $field) {
            if (isset($data[$field])) {
                $sanitized[$field] = sanitize_textarea_field($data[$field]);
            }
        }
        
        return $sanitized;
    }
}