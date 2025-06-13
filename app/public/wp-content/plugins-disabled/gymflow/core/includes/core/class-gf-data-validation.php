<?php
/**
 * GymFlow Data Validation Class
 *
 * Provides comprehensive data validation methods for the fitness studio management system
 *
 * @package GymFlow
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * GF_Data_Validation Class
 *
 * Handles validation of all data types used in GymFlow
 */
class GF_Data_Validation {

    /**
     * Validation error messages
     * @var array
     */
    private static $error_messages = array();

    /**
     * Initialize validation
     */
    public function init() {
        self::set_default_error_messages();
    }

    /**
     * Set default error messages
     */
    private static function set_default_error_messages() {
        self::$error_messages = array(
            'required' => __('This field is required.', 'gymflow'),
            'email' => __('Please enter a valid email address.', 'gymflow'),
            'phone' => __('Please enter a valid phone number.', 'gymflow'),
            'date' => __('Please enter a valid date.', 'gymflow'),
            'time' => __('Please enter a valid time.', 'gymflow'),
            'number' => __('Please enter a valid number.', 'gymflow'),
            'url' => __('Please enter a valid URL.', 'gymflow'),
            'min_length' => __('This field must be at least %d characters long.', 'gymflow'),
            'max_length' => __('This field cannot exceed %d characters.', 'gymflow'),
            'min_value' => __('Value must be at least %s.', 'gymflow'),
            'max_value' => __('Value cannot exceed %s.', 'gymflow'),
            'future_date' => __('Date must be in the future.', 'gymflow'),
            'past_date' => __('Date must be in the past.', 'gymflow'),
            'member_number' => __('Member number already exists.', 'gymflow'),
            'trainer_number' => __('Trainer number already exists.', 'gymflow'),
            'booking_reference' => __('Booking reference already exists.', 'gymflow'),
            'time_conflict' => __('Time conflict detected with existing booking.', 'gymflow'),
            'capacity_exceeded' => __('Class capacity has been exceeded.', 'gymflow'),
            'membership_expired' => __('Membership has expired.', 'gymflow'),
            'invalid_duration' => __('Invalid duration specified.', 'gymflow'),
            'invalid_price' => __('Invalid price format.', 'gymflow')
        );
    }

    /**
     * Validate member data
     *
     * @param array $data Member data to validate
     * @return true|WP_Error True if valid, WP_Error if invalid
     */
    public static function validate_member_data($data) {
        $errors = new WP_Error();

        // Required fields
        $required_fields = array('first_name', 'last_name', 'email');
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                $errors->add('required_' . $field, sprintf(__('%s is required.', 'gymflow'), ucwords(str_replace('_', ' ', $field))));
            }
        }

        // Validate email
        if (!empty($data['email']) && !is_email($data['email'])) {
            $errors->add('invalid_email', self::$error_messages['email']);
        }

        // Check for duplicate email
        if (!empty($data['email']) && self::email_exists($data['email'], isset($data['id']) ? $data['id'] : 0)) {
            $errors->add('duplicate_email', __('This email address is already registered.', 'gymflow'));
        }

        // Validate phone
        if (!empty($data['phone']) && !self::validate_phone($data['phone'])) {
            $errors->add('invalid_phone', self::$error_messages['phone']);
        }

        // Validate member number
        if (!empty($data['member_number']) && self::member_number_exists($data['member_number'], isset($data['id']) ? $data['id'] : 0)) {
            $errors->add('duplicate_member_number', self::$error_messages['member_number']);
        }

        // Validate date of birth
        if (!empty($data['date_of_birth']) && !self::validate_date($data['date_of_birth'])) {
            $errors->add('invalid_dob', __('Please enter a valid date of birth.', 'gymflow'));
        }

        // Validate membership dates
        if (!empty($data['membership_start_date']) && !self::validate_date($data['membership_start_date'])) {
            $errors->add('invalid_start_date', __('Please enter a valid membership start date.', 'gymflow'));
        }

        if (!empty($data['membership_end_date']) && !self::validate_date($data['membership_end_date'])) {
            $errors->add('invalid_end_date', __('Please enter a valid membership end date.', 'gymflow'));
        }

        // Check membership date logic
        if (!empty($data['membership_start_date']) && !empty($data['membership_end_date'])) {
            if (strtotime($data['membership_start_date']) >= strtotime($data['membership_end_date'])) {
                $errors->add('invalid_date_range', __('Membership end date must be after start date.', 'gymflow'));
            }
        }

        return $errors->has_errors() ? $errors : true;
    }

    /**
     * Validate class data
     *
     * @param array $data Class data to validate
     * @return true|WP_Error True if valid, WP_Error if invalid
     */
    public static function validate_class_data($data) {
        $errors = new WP_Error();

        // Required fields
        if (empty($data['name'])) {
            $errors->add('required_name', __('Class name is required.', 'gymflow'));
        }

        // Validate duration
        if (!empty($data['duration']) && (!is_numeric($data['duration']) || $data['duration'] <= 0)) {
            $errors->add('invalid_duration', self::$error_messages['invalid_duration']);
        }

        // Validate capacity
        if (!empty($data['capacity']) && (!is_numeric($data['capacity']) || $data['capacity'] <= 0)) {
            $errors->add('invalid_capacity', __('Please enter a valid capacity.', 'gymflow'));
        }

        // Validate prices
        if (!empty($data['price']) && !self::validate_price($data['price'])) {
            $errors->add('invalid_price', self::$error_messages['invalid_price']);
        }

        if (!empty($data['drop_in_price']) && !self::validate_price($data['drop_in_price'])) {
            $errors->add('invalid_drop_in_price', __('Please enter a valid drop-in price.', 'gymflow'));
        }

        return $errors->has_errors() ? $errors : true;
    }

    /**
     * Validate trainer data
     *
     * @param array $data Trainer data to validate
     * @return true|WP_Error True if valid, WP_Error if invalid
     */
    public static function validate_trainer_data($data) {
        $errors = new WP_Error();

        // Required fields
        $required_fields = array('first_name', 'last_name', 'email');
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                $errors->add('required_' . $field, sprintf(__('%s is required.', 'gymflow'), ucwords(str_replace('_', ' ', $field))));
            }
        }

        // Validate email
        if (!empty($data['email']) && !is_email($data['email'])) {
            $errors->add('invalid_email', self::$error_messages['email']);
        }

        // Check for duplicate email
        if (!empty($data['email']) && self::email_exists($data['email'], isset($data['id']) ? $data['id'] : 0, 'trainer')) {
            $errors->add('duplicate_email', __('This email address is already registered.', 'gymflow'));
        }

        // Validate trainer number
        if (!empty($data['trainer_number']) && self::trainer_number_exists($data['trainer_number'], isset($data['id']) ? $data['id'] : 0)) {
            $errors->add('duplicate_trainer_number', self::$error_messages['trainer_number']);
        }

        // Validate hire date
        if (!empty($data['hire_date']) && !self::validate_date($data['hire_date'])) {
            $errors->add('invalid_hire_date', __('Please enter a valid hire date.', 'gymflow'));
        }

        // Validate rates
        if (!empty($data['hourly_rate']) && !self::validate_price($data['hourly_rate'])) {
            $errors->add('invalid_hourly_rate', __('Please enter a valid hourly rate.', 'gymflow'));
        }

        if (!empty($data['commission_rate']) && (!is_numeric($data['commission_rate']) || $data['commission_rate'] < 0 || $data['commission_rate'] > 100)) {
            $errors->add('invalid_commission_rate', __('Commission rate must be between 0 and 100.', 'gymflow'));
        }

        return $errors->has_errors() ? $errors : true;
    }

    /**
     * Validate equipment data
     *
     * @param array $data Equipment data to validate
     * @return true|WP_Error True if valid, WP_Error if invalid
     */
    public static function validate_equipment_data($data) {
        $errors = new WP_Error();

        // Required fields
        if (empty($data['name'])) {
            $errors->add('required_name', __('Equipment name is required.', 'gymflow'));
        }

        // Validate purchase date
        if (!empty($data['purchase_date']) && !self::validate_date($data['purchase_date'])) {
            $errors->add('invalid_purchase_date', __('Please enter a valid purchase date.', 'gymflow'));
        }

        // Validate prices
        if (!empty($data['purchase_price']) && !self::validate_price($data['purchase_price'])) {
            $errors->add('invalid_purchase_price', __('Please enter a valid purchase price.', 'gymflow'));
        }

        if (!empty($data['current_value']) && !self::validate_price($data['current_value'])) {
            $errors->add('invalid_current_value', __('Please enter a valid current value.', 'gymflow'));
        }

        // Validate maintenance dates
        if (!empty($data['last_maintenance_date']) && !self::validate_date($data['last_maintenance_date'])) {
            $errors->add('invalid_last_maintenance', __('Please enter a valid last maintenance date.', 'gymflow'));
        }

        if (!empty($data['next_maintenance_date']) && !self::validate_date($data['next_maintenance_date'])) {
            $errors->add('invalid_next_maintenance', __('Please enter a valid next maintenance date.', 'gymflow'));
        }

        // Check maintenance date logic
        if (!empty($data['last_maintenance_date']) && !empty($data['next_maintenance_date'])) {
            if (strtotime($data['last_maintenance_date']) >= strtotime($data['next_maintenance_date'])) {
                $errors->add('invalid_maintenance_dates', __('Next maintenance date must be after last maintenance date.', 'gymflow'));
            }
        }

        // Validate booking settings
        if (!empty($data['booking_duration']) && (!is_numeric($data['booking_duration']) || $data['booking_duration'] <= 0)) {
            $errors->add('invalid_booking_duration', __('Please enter a valid booking duration.', 'gymflow'));
        }

        if (!empty($data['advance_booking_days']) && (!is_numeric($data['advance_booking_days']) || $data['advance_booking_days'] < 0)) {
            $errors->add('invalid_advance_booking', __('Please enter a valid advance booking period.', 'gymflow'));
        }

        return $errors->has_errors() ? $errors : true;
    }

    /**
     * Validate booking data
     *
     * @param array $data Booking data to validate
     * @return true|WP_Error True if valid, WP_Error if invalid
     */
    public static function validate_booking_data($data) {
        $errors = new WP_Error();

        // Required fields
        $required_fields = array('member_id', 'booking_date', 'start_time', 'end_time');
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                $errors->add('required_' . $field, sprintf(__('%s is required.', 'gymflow'), ucwords(str_replace('_', ' ', $field))));
            }
        }

        // Validate booking type
        if (!empty($data['booking_type']) && !in_array($data['booking_type'], array('class', 'equipment', 'personal_training'))) {
            $errors->add('invalid_booking_type', __('Invalid booking type.', 'gymflow'));
        }

        // Validate date
        if (!empty($data['booking_date']) && !self::validate_date($data['booking_date'])) {
            $errors->add('invalid_date', self::$error_messages['date']);
        }

        // Validate times
        if (!empty($data['start_time']) && !self::validate_time($data['start_time'])) {
            $errors->add('invalid_start_time', __('Please enter a valid start time.', 'gymflow'));
        }

        if (!empty($data['end_time']) && !self::validate_time($data['end_time'])) {
            $errors->add('invalid_end_time', __('Please enter a valid end time.', 'gymflow'));
        }

        // Check time logic
        if (!empty($data['start_time']) && !empty($data['end_time'])) {
            if (strtotime($data['start_time']) >= strtotime($data['end_time'])) {
                $errors->add('invalid_time_range', __('End time must be after start time.', 'gymflow'));
            }
        }

        // Validate booking reference uniqueness
        if (!empty($data['booking_reference']) && self::booking_reference_exists($data['booking_reference'], isset($data['id']) ? $data['id'] : 0)) {
            $errors->add('duplicate_booking_reference', self::$error_messages['booking_reference']);
        }

        // Check for time conflicts
        if (!empty($data['booking_date']) && !empty($data['start_time']) && !empty($data['end_time'])) {
            $conflicts = self::check_booking_conflicts($data);
            if ($conflicts) {
                $errors->add('time_conflict', self::$error_messages['time_conflict']);
            }
        }

        // Validate amount
        if (!empty($data['amount']) && !self::validate_price($data['amount'])) {
            $errors->add('invalid_amount', __('Please enter a valid amount.', 'gymflow'));
        }

        return $errors->has_errors() ? $errors : true;
    }

    /**
     * Validate payment data
     *
     * @param array $data Payment data to validate
     * @return true|WP_Error True if valid, WP_Error if invalid
     */
    public static function validate_payment_data($data) {
        $errors = new WP_Error();

        // Required fields
        $required_fields = array('member_id', 'amount', 'payment_method');
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                $errors->add('required_' . $field, sprintf(__('%s is required.', 'gymflow'), ucwords(str_replace('_', ' ', $field))));
            }
        }

        // Validate amount
        if (!empty($data['amount']) && !self::validate_price($data['amount'])) {
            $errors->add('invalid_amount', self::$error_messages['invalid_price']);
        }

        // Validate payment date
        if (!empty($data['payment_date']) && !self::validate_datetime($data['payment_date'])) {
            $errors->add('invalid_payment_date', __('Please enter a valid payment date.', 'gymflow'));
        }

        // Validate due date
        if (!empty($data['due_date']) && !self::validate_date($data['due_date'])) {
            $errors->add('invalid_due_date', __('Please enter a valid due date.', 'gymflow'));
        }

        return $errors->has_errors() ? $errors : true;
    }

    /**
     * Validate email address
     *
     * @param string $email Email to validate
     * @return bool True if valid
     */
    public static function validate_email($email) {
        return is_email($email);
    }

    /**
     * Validate phone number
     *
     * @param string $phone Phone number to validate
     * @return bool True if valid
     */
    public static function validate_phone($phone) {
        // Remove all non-numeric characters except + - ( ) and spaces
        $clean_phone = preg_replace('/[^0-9+\-\(\)\s]/', '', $phone);
        
        // Check if it has at least 10 digits
        $digits_only = preg_replace('/[^0-9]/', '', $clean_phone);
        
        return strlen($digits_only) >= 10;
    }

    /**
     * Validate date
     *
     * @param string $date Date to validate
     * @return bool True if valid
     */
    public static function validate_date($date) {
        $formats = array('Y-m-d', 'm/d/Y', 'd/m/Y', 'Y-m-d H:i:s');
        
        foreach ($formats as $format) {
            $d = DateTime::createFromFormat($format, $date);
            if ($d && $d->format($format) === $date) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Validate time
     *
     * @param string $time Time to validate
     * @return bool True if valid
     */
    public static function validate_time($time) {
        $formats = array('H:i', 'H:i:s', 'g:i A', 'g:i a');
        
        foreach ($formats as $format) {
            $t = DateTime::createFromFormat($format, $time);
            if ($t) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Validate datetime
     *
     * @param string $datetime DateTime to validate
     * @return bool True if valid
     */
    public static function validate_datetime($datetime) {
        $formats = array('Y-m-d H:i:s', 'Y-m-d H:i', 'm/d/Y H:i:s', 'm/d/Y H:i');
        
        foreach ($formats as $format) {
            $dt = DateTime::createFromFormat($format, $datetime);
            if ($dt && $dt->format($format) === $datetime) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Validate price/currency amount
     *
     * @param mixed $price Price to validate
     * @return bool True if valid
     */
    public static function validate_price($price) {
        // Remove currency symbols and spaces
        $clean_price = preg_replace('/[^0-9\.]/', '', $price);
        
        // Check if it's a valid decimal number
        return is_numeric($clean_price) && $clean_price >= 0;
    }

    /**
     * Validate URL
     *
     * @param string $url URL to validate
     * @return bool True if valid
     */
    public static function validate_url($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Check if email exists in database
     *
     * @param string $email Email to check
     * @param int $exclude_id ID to exclude from check
     * @param string $type Type of record (member, trainer)
     * @return bool True if exists
     */
    private static function email_exists($email, $exclude_id = 0, $type = 'member') {
        global $wpdb;
        
        $table = $wpdb->prefix . 'gf_' . $type . 's';
        
        $query = $wpdb->prepare(
            "SELECT id FROM {$table} WHERE email = %s AND id != %d",
            $email,
            $exclude_id
        );
        
        return $wpdb->get_var($query) !== null;
    }

    /**
     * Check if member number exists
     *
     * @param string $member_number Member number to check
     * @param int $exclude_id ID to exclude from check
     * @return bool True if exists
     */
    private static function member_number_exists($member_number, $exclude_id = 0) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'gf_members';
        
        $query = $wpdb->prepare(
            "SELECT id FROM {$table} WHERE member_number = %s AND id != %d",
            $member_number,
            $exclude_id
        );
        
        return $wpdb->get_var($query) !== null;
    }

    /**
     * Check if trainer number exists
     *
     * @param string $trainer_number Trainer number to check
     * @param int $exclude_id ID to exclude from check
     * @return bool True if exists
     */
    private static function trainer_number_exists($trainer_number, $exclude_id = 0) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'gf_trainers';
        
        $query = $wpdb->prepare(
            "SELECT id FROM {$table} WHERE trainer_number = %s AND id != %d",
            $trainer_number,
            $exclude_id
        );
        
        return $wpdb->get_var($query) !== null;
    }

    /**
     * Check if booking reference exists
     *
     * @param string $booking_reference Booking reference to check
     * @param int $exclude_id ID to exclude from check
     * @return bool True if exists
     */
    private static function booking_reference_exists($booking_reference, $exclude_id = 0) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'gf_bookings';
        
        $query = $wpdb->prepare(
            "SELECT id FROM {$table} WHERE booking_reference = %s AND id != %d",
            $booking_reference,
            $exclude_id
        );
        
        return $wpdb->get_var($query) !== null;
    }

    /**
     * Check for booking conflicts
     *
     * @param array $booking_data Booking data to check
     * @return bool True if conflicts exist
     */
    private static function check_booking_conflicts($booking_data) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'gf_bookings';
        $exclude_id = isset($booking_data['id']) ? $booking_data['id'] : 0;
        
        $query = $wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} 
             WHERE booking_date = %s 
             AND status NOT IN ('cancelled', 'no_show')
             AND id != %d
             AND (
                 (start_time <= %s AND end_time > %s) OR
                 (start_time < %s AND end_time >= %s) OR
                 (start_time >= %s AND end_time <= %s)
             )",
            $booking_data['booking_date'],
            $exclude_id,
            $booking_data['start_time'], $booking_data['start_time'],
            $booking_data['end_time'], $booking_data['end_time'],
            $booking_data['start_time'], $booking_data['end_time']
        );
        
        // Additional checks for specific booking types
        if (!empty($booking_data['equipment_id'])) {
            $query .= $wpdb->prepare(" AND equipment_id = %d", $booking_data['equipment_id']);
        }
        
        if (!empty($booking_data['trainer_id'])) {
            $query .= $wpdb->prepare(" AND trainer_id = %d", $booking_data['trainer_id']);
        }
        
        return $wpdb->get_var($query) > 0;
    }

    /**
     * Get validation error message
     *
     * @param string $error_type Error type
     * @param mixed $param Optional parameter for message
     * @return string Error message
     */
    public static function get_error_message($error_type, $param = null) {
        $message = isset(self::$error_messages[$error_type]) ? self::$error_messages[$error_type] : __('Validation error.', 'gymflow');
        
        if ($param !== null) {
            return sprintf($message, $param);
        }
        
        return $message;
    }

    /**
     * Sanitize and validate form data
     *
     * @param array $data Raw form data
     * @param array $rules Validation rules
     * @return array|WP_Error Sanitized data or error
     */
    public static function sanitize_and_validate($data, $rules) {
        $sanitized_data = array();
        $errors = new WP_Error();
        
        foreach ($rules as $field => $rule) {
            $value = isset($data[$field]) ? $data[$field] : '';
            
            // Sanitize based on type
            if (isset($rule['type'])) {
                $value = GF_Utilities::sanitize_input($value, $rule['type']);
            }
            
            // Check required
            if (isset($rule['required']) && $rule['required'] && empty($value)) {
                $errors->add('required_' . $field, sprintf(__('%s is required.', 'gymflow'), $rule['label']));
                continue;
            }
            
            // Skip further validation if empty and not required
            if (empty($value) && (!isset($rule['required']) || !$rule['required'])) {
                $sanitized_data[$field] = $value;
                continue;
            }
            
            // Apply validation rules
            if (isset($rule['validation'])) {
                foreach ($rule['validation'] as $validation_rule) {
                    $is_valid = true;
                    
                    switch ($validation_rule['type']) {
                        case 'email':
                            $is_valid = self::validate_email($value);
                            break;
                        case 'phone':
                            $is_valid = self::validate_phone($value);
                            break;
                        case 'date':
                            $is_valid = self::validate_date($value);
                            break;
                        case 'time':
                            $is_valid = self::validate_time($value);
                            break;
                        case 'url':
                            $is_valid = self::validate_url($value);
                            break;
                        case 'price':
                            $is_valid = self::validate_price($value);
                            break;
                        case 'min_length':
                            $is_valid = strlen($value) >= $validation_rule['value'];
                            break;
                        case 'max_length':
                            $is_valid = strlen($value) <= $validation_rule['value'];
                            break;
                    }
                    
                    if (!$is_valid) {
                        $message = isset($validation_rule['message']) ? $validation_rule['message'] : self::get_error_message($validation_rule['type']);
                        $errors->add('validation_' . $field, $message);
                        break;
                    }
                }
            }
            
            $sanitized_data[$field] = $value;
        }
        
        return $errors->has_errors() ? $errors : $sanitized_data;
    }
}