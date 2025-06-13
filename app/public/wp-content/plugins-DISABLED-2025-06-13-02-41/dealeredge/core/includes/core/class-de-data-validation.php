<?php
/**
 * DealerEdge Data Validation
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class DE_Data_Validation {
    
    public static function validate_vin($vin) {
        return DE_Utilities::sanitize_vin($vin);
    }
    
    public static function validate_phone($phone) {
        return DE_Utilities::format_phone($phone);
    }
    
    public static function validate_email($email) {
        return is_email($email) ? $email : false;
    }
}