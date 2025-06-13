<?php
/**
 * StorageFlow Rental System - Complete rental and lease management
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class SF_Rental_System {
    
    public static function init() {
        add_action('wp_ajax_sf_submit_rental', array(__CLASS__, 'ajax_submit_rental'));
        add_action('wp_ajax_nopriv_sf_submit_rental', array(__CLASS__, 'ajax_submit_rental'));
        add_action('wp_ajax_sf_approve_rental', array(__CLASS__, 'ajax_approve_rental'));
        add_action('wp_ajax_sf_cancel_rental', array(__CLASS__, 'ajax_cancel_rental'));
    }
    
    /**
     * Process rental application
     */
    public static function process_rental_application($data) {
        // Validate required fields
        $required_fields = array('unit_id', 'tenant_name', 'tenant_email', 'tenant_phone', 'move_in_date');
        
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                return new WP_Error('missing_field', sprintf(__('Field %s is required', 'storageflow'), $field));
            }
        }
        
        // Validate move-in date
        $move_in_date = strtotime($data['move_in_date']);
        
        if (!$move_in_date || $move_in_date < strtotime('today')) {
            return new WP_Error('invalid_date', __('Move-in date must be today or in the future', 'storageflow'));
        }
        
        // Check unit availability
        $unit_id = intval($data['unit_id']);
        $unit = get_post($unit_id);
        
        if (!$unit || $unit->post_type !== 'sf_unit') {
            return new WP_Error('invalid_unit', __('Invalid unit ID', 'storageflow'));
        }
        
        $unit_status = get_post_meta($unit_id, '_sf_unit_status', true);
        if (!in_array($unit_status, array('available', 'vacant_clean'))) {
            return new WP_Error('unit_unavailable', __('Selected unit is no longer available', 'storageflow'));
        }
        
        // Create or get tenant record
        $tenant_id = self::create_or_update_tenant_record($data);
        if (is_wp_error($tenant_id)) {
            return $tenant_id;
        }
        
        // Create rental application
        $application_id = self::create_rental_application($unit_id, $tenant_id, $data);
        if (is_wp_error($application_id)) {
            return $application_id;
        }
        
        // Send notifications
        self::send_application_notifications($application_id);
        
        return array(
            'application_id' => $application_id,
            'unit_id' => $unit_id,
            'unit_number' => get_post_meta($unit_id, '_sf_unit_number', true),
            'monthly_rate' => get_post_meta($unit_id, '_sf_monthly_rate', true),
            'tenant_id' => $tenant_id
        );
    }
    
    /**
     * Create or update tenant record
     */
    private static function create_or_update_tenant_record($data) {
        // Check if tenant already exists by email
        $existing_tenant = get_posts(array(
            'post_type' => 'sf_tenant',
            'meta_query' => array(
                array(
                    'key' => '_sf_tenant_email',
                    'value' => $data['tenant_email'],
                    'compare' => '='
                )
            ),
            'posts_per_page' => 1
        ));
        
        $tenant_data = array(
            'post_title' => sprintf('%s %s', $data['tenant_name'], $data['tenant_email']),
            'post_type' => 'sf_tenant',
            'post_status' => 'publish',
            'meta_input' => array(
                '_sf_tenant_name' => sanitize_text_field($data['tenant_name']),
                '_sf_tenant_email' => sanitize_email($data['tenant_email']),
                '_sf_tenant_phone' => sanitize_text_field($data['tenant_phone']),
                '_sf_tenant_address' => isset($data['tenant_address']) ? sanitize_textarea_field($data['tenant_address']) : '',
                '_sf_tenant_city' => isset($data['tenant_city']) ? sanitize_text_field($data['tenant_city']) : '',
                '_sf_tenant_state' => isset($data['tenant_state']) ? sanitize_text_field($data['tenant_state']) : '',
                '_sf_tenant_zip' => isset($data['tenant_zip']) ? sanitize_text_field($data['tenant_zip']) : '',
                '_sf_emergency_contact_name' => isset($data['emergency_contact_name']) ? sanitize_text_field($data['emergency_contact_name']) : '',
                '_sf_emergency_contact_phone' => isset($data['emergency_contact_phone']) ? sanitize_text_field($data['emergency_contact_phone']) : '',
                '_sf_employer' => isset($data['employer']) ? sanitize_text_field($data['employer']) : '',
                '_sf_monthly_income' => isset($data['monthly_income']) ? floatval($data['monthly_income']) : 0,
                '_sf_drivers_license' => isset($data['drivers_license']) ? sanitize_text_field($data['drivers_license']) : '',
                '_sf_birth_date' => isset($data['birth_date']) ? sanitize_text_field($data['birth_date']) : '',
                '_sf_tenant_type' => isset($data['tenant_type']) ? sanitize_text_field($data['tenant_type']) : 'individual'
            )
        );
        
        if ($existing_tenant) {
            $tenant_id = $existing_tenant[0]->ID;
            $tenant_data['ID'] = $tenant_id;
            wp_update_post($tenant_data);
        } else {
            $tenant_id = wp_insert_post($tenant_data);
        }
        
        if (!$tenant_id) {
            return new WP_Error('tenant_creation_failed', __('Failed to create tenant record', 'storageflow'));
        }
        
        return $tenant_id;
    }
    
    /**
     * Create rental application
     */
    private static function create_rental_application($unit_id, $tenant_id, $data) {
        $tenant_name = $data['tenant_name'];
        $unit_number = get_post_meta($unit_id, '_sf_unit_number', true);
        $monthly_rate = (float)get_post_meta($unit_id, '_sf_monthly_rate', true);
        
        // Calculate move-in costs
        $security_deposit = isset($data['security_deposit']) ? (float)$data['security_deposit'] : $monthly_rate;
        $admin_fee = (float)get_option('sf_admin_fee', 25.00);
        $prorated_rent = self::calculate_prorated_rent($monthly_rate, $data['move_in_date']);
        $total_move_in_cost = $monthly_rate + $security_deposit + $admin_fee;
        
        $application_data = array(
            'post_title' => sprintf('%s - Unit %s Application - %s',
                $tenant_name,
                $unit_number,
                date('M j, Y', strtotime($data['move_in_date']))
            ),
            'post_type' => 'sf_rental',
            'post_status' => 'sf_application',
            'post_content' => isset($data['special_requests']) ? sanitize_textarea_field($data['special_requests']) : '',
            'meta_input' => array(
                '_sf_unit_id' => $unit_id,
                '_sf_tenant_id' => $tenant_id,
                '_sf_move_in_date' => date('Y-m-d', strtotime($data['move_in_date'])),
                '_sf_rental_term' => isset($data['rental_term']) ? sanitize_text_field($data['rental_term']) : 'month_to_month',
                '_sf_monthly_rate' => $monthly_rate,
                '_sf_security_deposit' => $security_deposit,
                '_sf_admin_fee' => $admin_fee,
                '_sf_prorated_rent' => $prorated_rent,
                '_sf_total_move_in_cost' => $total_move_in_cost,
                '_sf_payment_due_date' => isset($data['payment_due_date']) ? intval($data['payment_due_date']) : 1,
                '_sf_late_fee_amount' => (float)get_option('sf_late_fee_amount', 25.00),
                '_sf_late_fee_grace_days' => intval(get_option('sf_late_fee_grace_days', 5)),
                '_sf_unit_use' => isset($data['unit_use']) ? sanitize_text_field($data['unit_use']) : 'personal',
                '_sf_vehicle_info' => isset($data['vehicle_info']) ? sanitize_textarea_field($data['vehicle_info']) : '',
                '_sf_referral_source' => isset($data['referral_source']) ? sanitize_text_field($data['referral_source']) : '',
                '_sf_insurance_required' => get_option('sf_insurance_required', 'yes'),
                '_sf_insurance_company' => isset($data['insurance_company']) ? sanitize_text_field($data['insurance_company']) : '',
                '_sf_insurance_policy' => isset($data['insurance_policy']) ? sanitize_text_field($data['insurance_policy']) : '',
                '_sf_application_date' => current_time('mysql'),
                '_sf_application_source' => 'online',
                '_sf_payment_method' => isset($data['payment_method']) ? sanitize_text_field($data['payment_method']) : 'credit_card',
                '_sf_auto_pay' => isset($data['auto_pay']) ? sanitize_text_field($data['auto_pay']) : 'no'
            )
        );
        
        $application_id = wp_insert_post($application_data);
        
        if ($application_id) {
            // Generate application hash for public access
            $application_hash = wp_generate_password(32, false);
            update_post_meta($application_id, '_sf_application_hash', $application_hash);
            
            // Generate application number
            $application_number = 'APP-' . date('Y') . '-' . str_pad($application_id, 4, '0', STR_PAD_LEFT);
            update_post_meta($application_id, '_sf_application_number', $application_number);
            
            // Reserve the unit temporarily
            update_post_meta($unit_id, '_sf_unit_status', 'reserved');
            update_post_meta($unit_id, '_sf_reserved_until', date('Y-m-d H:i:s', strtotime('+24 hours')));
            
            return $application_id;
        }
        
        return new WP_Error('application_creation_failed', __('Failed to create rental application', 'storageflow'));
    }
    
    /**
     * Calculate prorated rent for partial month
     */
    private static function calculate_prorated_rent($monthly_rate, $move_in_date) {
        $move_in_timestamp = strtotime($move_in_date);
        $days_in_month = date('t', $move_in_timestamp);
        $day_of_month = date('j', $move_in_timestamp);
        
        // If moving in on the 1st, no proration needed
        if ($day_of_month === 1) {
            return 0;
        }
        
        $days_remaining = $days_in_month - $day_of_month + 1;
        $daily_rate = $monthly_rate / $days_in_month;
        
        return round($daily_rate * $days_remaining, 2);
    }
    
    /**
     * Approve rental application
     */
    public static function approve_rental_application($application_id, $approval_notes = '') {
        $application = get_post($application_id);
        if (!$application || $application->post_type !== 'sf_rental') {
            return new WP_Error('invalid_application', __('Invalid application ID', 'storageflow'));
        }
        
        $unit_id = get_post_meta($application_id, '_sf_unit_id', true);
        $tenant_id = get_post_meta($application_id, '_sf_tenant_id', true);
        
        // Update application status to active rental
        wp_update_post(array(
            'ID' => $application_id,
            'post_status' => 'sf_active'
        ));
        
        // Update unit status
        update_post_meta($unit_id, '_sf_unit_status', 'rented');
        update_post_meta($unit_id, '_sf_current_tenant_id', $tenant_id);
        update_post_meta($unit_id, '_sf_current_rental_id', $application_id);
        delete_post_meta($unit_id, '_sf_reserved_until');
        
        // Generate access codes
        $access_code = SF_Unit_Manager::generate_access_code();
        $gate_code = self::generate_gate_code();
        
        update_post_meta($application_id, '_sf_access_code', $access_code);
        update_post_meta($application_id, '_sf_gate_code', $gate_code);
        
        // Generate account number
        $account_number = 'SF-' . date('Y') . '-' . str_pad($application_id, 4, '0', STR_PAD_LEFT);
        update_post_meta($application_id, '_sf_account_number', $account_number);
        
        // Record approval details
        update_post_meta($application_id, '_sf_approved_date', current_time('mysql'));
        update_post_meta($application_id, '_sf_approved_by', get_current_user_id());
        update_post_meta($application_id, '_sf_approval_notes', $approval_notes);
        
        // Initialize billing
        self::initialize_rental_billing($application_id);
        
        // Send approval notification
        SF_Notification_System::send_rental_approved($application_id);
        
        return true;
    }
    
    /**
     * Initialize billing for new rental
     */
    private static function initialize_rental_billing($rental_id) {
        $move_in_date = get_post_meta($rental_id, '_sf_move_in_date', true);
        $monthly_rate = (float)get_post_meta($rental_id, '_sf_monthly_rate', true);
        $payment_due_date = intval(get_post_meta($rental_id, '_sf_payment_due_date', true));
        
        // Set first payment due date
        $first_due_date = date('Y-m-d', strtotime(date('Y-m-01', strtotime($move_in_date)) . " +1 month +{$payment_due_date} days -1 day"));
        
        // Create first invoice
        $invoice_data = array(
            'post_title' => sprintf('Invoice - %s - %s',
                get_post_meta($rental_id, '_sf_account_number', true),
                date('M Y', strtotime($first_due_date))
            ),
            'post_type' => 'sf_invoice',
            'post_status' => 'sf_pending',
            'meta_input' => array(
                '_sf_rental_id' => $rental_id,
                '_sf_tenant_id' => get_post_meta($rental_id, '_sf_tenant_id', true),
                '_sf_unit_id' => get_post_meta($rental_id, '_sf_unit_id', true),
                '_sf_invoice_date' => current_time('Y-m-d'),
                '_sf_due_date' => $first_due_date,
                '_sf_period_start' => date('Y-m-01', strtotime($first_due_date)),
                '_sf_period_end' => date('Y-m-t', strtotime($first_due_date)),
                '_sf_rent_amount' => $monthly_rate,
                '_sf_total_amount' => $monthly_rate,
                '_sf_payment_status' => 'pending'
            )
        );
        
        wp_insert_post($invoice_data);
        
        // Update rental payment status
        update_post_meta($rental_id, '_sf_payment_status', 'current');
        update_post_meta($rental_id, '_sf_next_payment_due', $first_due_date);
        update_post_meta($rental_id, '_sf_balance_due', 0);
    }
    
    /**
     * Cancel rental application
     */
    public static function cancel_rental_application($application_id, $reason = '') {
        $application = get_post($application_id);
        if (!$application || $application->post_type !== 'sf_rental') {
            return new WP_Error('invalid_application', __('Invalid application ID', 'storageflow'));
        }
        
        $unit_id = get_post_meta($application_id, '_sf_unit_id', true);
        
        // Update application status
        wp_update_post(array(
            'ID' => $application_id,
            'post_status' => 'sf_cancelled'
        ));
        
        // Free up the unit
        update_post_meta($unit_id, '_sf_unit_status', 'available');
        delete_post_meta($unit_id, '_sf_reserved_until');
        delete_post_meta($unit_id, '_sf_current_tenant_id');
        delete_post_meta($unit_id, '_sf_current_rental_id');
        
        // Record cancellation details
        update_post_meta($application_id, '_sf_cancellation_reason', $reason);
        update_post_meta($application_id, '_sf_cancellation_date', current_time('mysql'));
        update_post_meta($application_id, '_sf_cancelled_by', get_current_user_id());
        
        // Send cancellation notification
        SF_Notification_System::send_rental_cancelled($application_id);
        
        return true;
    }
    
    /**
     * Process move-out
     */
    public static function process_move_out($rental_id, $move_out_date = null, $final_charges = 0, $damages = '') {
        $rental = get_post($rental_id);
        if (!$rental) {
            return new WP_Error('invalid_rental', __('Invalid rental ID', 'storageflow'));
        }
        
        $unit_id = get_post_meta($rental_id, '_sf_unit_id', true);
        $tenant_id = get_post_meta($rental_id, '_sf_tenant_id', true);
        
        $actual_move_out = $move_out_date ? $move_out_date : current_time('Y-m-d');
        
        // Update rental status
        wp_update_post(array(
            'ID' => $rental_id,
            'post_status' => 'sf_completed'
        ));
        
        // Update unit status
        $unit_status = $damages ? 'vacant_dirty' : 'vacant_clean';
        update_post_meta($unit_id, '_sf_unit_status', $unit_status);
        delete_post_meta($unit_id, '_sf_current_tenant_id');
        delete_post_meta($unit_id, '_sf_current_rental_id');
        
        // Record move-out details
        update_post_meta($rental_id, '_sf_move_out_date', $actual_move_out);
        update_post_meta($rental_id, '_sf_final_charges', $final_charges);
        update_post_meta($rental_id, '_sf_damage_report', $damages);
        update_post_meta($rental_id, '_sf_processed_by', get_current_user_id());
        update_post_meta($rental_id, '_sf_move_out_processed', current_time('mysql'));
        
        // Calculate security deposit refund
        $security_deposit = (float)get_post_meta($rental_id, '_sf_security_deposit', true);
        $balance_due = (float)get_post_meta($rental_id, '_sf_balance_due', true);
        $refund_amount = max(0, $security_deposit - $balance_due - $final_charges);
        
        update_post_meta($rental_id, '_sf_security_deposit_refund', $refund_amount);
        
        // Generate final statement
        $final_statement_id = self::generate_final_statement($rental_id);
        
        // Send move-out notification
        SF_Notification_System::send_move_out_statement($rental_id);
        
        // Deactivate access codes
        SF_Access_Control::deactivate_tenant_access($rental_id);
        
        return $final_statement_id;
    }
    
    /**
     * Generate final statement
     */
    private static function generate_final_statement($rental_id) {
        $account_number = get_post_meta($rental_id, '_sf_account_number', true);
        $move_out_date = get_post_meta($rental_id, '_sf_move_out_date', true);
        
        $statement_data = array(
            'post_title' => sprintf('Final Statement - %s - %s',
                $account_number,
                date('M j, Y', strtotime($move_out_date))
            ),
            'post_type' => 'sf_statement',
            'post_status' => 'sf_final',
            'meta_input' => array(
                '_sf_rental_id' => $rental_id,
                '_sf_tenant_id' => get_post_meta($rental_id, '_sf_tenant_id', true),
                '_sf_unit_id' => get_post_meta($rental_id, '_sf_unit_id', true),
                '_sf_statement_date' => current_time('Y-m-d'),
                '_sf_statement_type' => 'final',
                '_sf_security_deposit' => get_post_meta($rental_id, '_sf_security_deposit', true),
                '_sf_balance_due' => get_post_meta($rental_id, '_sf_balance_due', true),
                '_sf_final_charges' => get_post_meta($rental_id, '_sf_final_charges', true),
                '_sf_refund_amount' => get_post_meta($rental_id, '_sf_security_deposit_refund', true)
            )
        );
        
        return wp_insert_post($statement_data);
    }
    
    /**
     * Get rental details
     */
    public static function get_rental_details($rental_id) {
        $rental = get_post($rental_id);
        if (!$rental) return false;
        
        $unit_id = get_post_meta($rental_id, '_sf_unit_id', true);
        $tenant_id = get_post_meta($rental_id, '_sf_tenant_id', true);
        
        return array(
            'id' => $rental_id,
            'account_number' => get_post_meta($rental_id, '_sf_account_number', true),
            'status' => $rental->post_status,
            'move_in_date' => get_post_meta($rental_id, '_sf_move_in_date', true),
            'move_out_date' => get_post_meta($rental_id, '_sf_move_out_date', true),
            'rental_term' => get_post_meta($rental_id, '_sf_rental_term', true),
            'monthly_rate' => get_post_meta($rental_id, '_sf_monthly_rate', true),
            'security_deposit' => get_post_meta($rental_id, '_sf_security_deposit', true),
            'payment_status' => get_post_meta($rental_id, '_sf_payment_status', true),
            'balance_due' => get_post_meta($rental_id, '_sf_balance_due', true),
            'next_payment_due' => get_post_meta($rental_id, '_sf_next_payment_due', true),
            'access_code' => get_post_meta($rental_id, '_sf_access_code', true),
            'gate_code' => get_post_meta($rental_id, '_sf_gate_code', true),
            'unit' => SF_Unit_Manager::get_unit_details($unit_id),
            'tenant' => self::get_tenant_details($tenant_id),
            'auto_pay' => get_post_meta($rental_id, '_sf_auto_pay', true),
            'payment_method' => get_post_meta($rental_id, '_sf_payment_method', true)
        );
    }
    
    /**
     * Get tenant details
     */
    private static function get_tenant_details($tenant_id) {
        $tenant = get_post($tenant_id);
        if (!$tenant) return null;
        
        return array(
            'id' => $tenant_id,
            'name' => get_post_meta($tenant_id, '_sf_tenant_name', true),
            'email' => get_post_meta($tenant_id, '_sf_tenant_email', true),
            'phone' => get_post_meta($tenant_id, '_sf_tenant_phone', true),
            'address' => get_post_meta($tenant_id, '_sf_tenant_address', true),
            'city' => get_post_meta($tenant_id, '_sf_tenant_city', true),
            'state' => get_post_meta($tenant_id, '_sf_tenant_state', true),
            'zip' => get_post_meta($tenant_id, '_sf_tenant_zip', true),
            'emergency_contact' => get_post_meta($tenant_id, '_sf_emergency_contact_name', true),
            'emergency_phone' => get_post_meta($tenant_id, '_sf_emergency_contact_phone', true)
        );
    }
    
    /**
     * Generate gate access code
     */
    private static function generate_gate_code() {
        $facility_code = get_option('sf_facility_gate_code', '1234');
        return $facility_code; // In pro version, this would be dynamic per tenant
    }
    
    /**
     * Send application notifications
     */
    private static function send_application_notifications($application_id) {
        // Send to applicant
        SF_Notification_System::send_application_received($application_id);
        
        // Send to facility staff
        SF_Notification_System::send_admin_application_notification($application_id);
    }
    
    /**
     * AJAX: Submit rental application
     */
    public static function ajax_submit_rental() {
        check_ajax_referer('sf_public_nonce', 'nonce');
        
        $data = array(
            'unit_id' => intval($_POST['unit_id']),
            'tenant_name' => sanitize_text_field($_POST['tenant_name']),
            'tenant_email' => sanitize_email($_POST['tenant_email']),
            'tenant_phone' => sanitize_text_field($_POST['tenant_phone']),
            'tenant_address' => sanitize_textarea_field($_POST['tenant_address']),
            'tenant_city' => sanitize_text_field($_POST['tenant_city']),
            'tenant_state' => sanitize_text_field($_POST['tenant_state']),
            'tenant_zip' => sanitize_text_field($_POST['tenant_zip']),
            'move_in_date' => sanitize_text_field($_POST['move_in_date']),
            'rental_term' => sanitize_text_field($_POST['rental_term']),
            'unit_use' => sanitize_text_field($_POST['unit_use']),
            'emergency_contact_name' => sanitize_text_field($_POST['emergency_contact_name']),
            'emergency_contact_phone' => sanitize_text_field($_POST['emergency_contact_phone']),
            'referral_source' => sanitize_text_field($_POST['referral_source']),
            'special_requests' => sanitize_textarea_field($_POST['special_requests'])
        );
        
        $result = self::process_rental_application($data);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success(array(
            'message' => __('Rental application submitted successfully! You will receive a confirmation email within 24 hours.', 'storageflow'),
            'application_id' => $result['application_id'],
            'application_number' => get_post_meta($result['application_id'], '_sf_application_number', true)
        ));
    }
    
    /**
     * AJAX: Approve rental application (admin)
     */
    public static function ajax_approve_rental() {
        check_ajax_referer('sf_admin_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(__('Insufficient permissions', 'storageflow'));
        }
        
        $application_id = intval($_POST['application_id']);
        $notes = sanitize_textarea_field($_POST['notes']);
        
        $result = self::approve_rental_application($application_id, $notes);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success(__('Rental application approved successfully', 'storageflow'));
    }
    
    /**
     * AJAX: Cancel rental application
     */
    public static function ajax_cancel_rental() {
        check_ajax_referer('sf_admin_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(__('Insufficient permissions', 'storageflow'));
        }
        
        $application_id = intval($_POST['application_id']);
        $reason = sanitize_textarea_field($_POST['reason']);
        
        $result = self::cancel_rental_application($application_id, $reason);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success(__('Rental application cancelled successfully', 'storageflow'));
    }
}