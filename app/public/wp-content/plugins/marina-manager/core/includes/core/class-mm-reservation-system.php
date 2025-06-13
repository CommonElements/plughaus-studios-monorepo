<?php
/**
 * Marina Manager Reservation System - Complete booking and reservation management
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class MM_Reservation_System {
    
    public static function init() {
        add_action('wp_ajax_mm_submit_reservation', array(__CLASS__, 'ajax_submit_reservation'));
        add_action('wp_ajax_nopriv_mm_submit_reservation', array(__CLASS__, 'ajax_submit_reservation'));
        add_action('wp_ajax_mm_confirm_reservation', array(__CLASS__, 'ajax_confirm_reservation'));
        add_action('wp_ajax_mm_cancel_reservation', array(__CLASS__, 'ajax_cancel_reservation'));
    }
    
    /**
     * Process reservation request
     */
    public static function process_reservation_request($data) {
        // Validate required fields
        $required_fields = array('boat_name', 'boat_type', 'boat_length', 'owner_name', 'owner_email', 'start_date', 'end_date');
        
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                return new WP_Error('missing_field', sprintf(__('Field %s is required', 'marina-manager'), $field));
            }
        }
        
        // Validate dates
        $start_date = strtotime($data['start_date']);
        $end_date = strtotime($data['end_date']);
        
        if (!$start_date || !$end_date || $end_date <= $start_date) {
            return new WP_Error('invalid_dates', __('Invalid reservation dates', 'marina-manager'));
        }
        
        if ($start_date < strtotime('tomorrow')) {
            return new WP_Error('past_date', __('Reservation must be for a future date', 'marina-manager'));
        }
        
        // Check for available slips
        $boat_length = floatval($data['boat_length']);
        $slip_type = isset($data['slip_type']) ? $data['slip_type'] : 'any';
        
        $available_slips = MM_Slip_Manager::check_availability(
            $data['start_date'], 
            $data['end_date'], 
            $boat_length, 
            $slip_type
        );
        
        if (empty($available_slips)) {
            return new WP_Error('no_availability', __('No suitable slips available for the requested dates', 'marina-manager'));
        }
        
        // Find best matching slip
        $selected_slip = self::find_best_slip_match($available_slips, $boat_length, $data);
        
        // Create or get boat record
        $boat_id = self::create_or_update_boat_record($data);
        if (is_wp_error($boat_id)) {
            return $boat_id;
        }
        
        // Create reservation
        $reservation_id = self::create_reservation_record($selected_slip['slip_id'], $boat_id, $data);
        if (is_wp_error($reservation_id)) {
            return $reservation_id;
        }
        
        // Send notifications
        self::send_reservation_notifications($reservation_id);
        
        return array(
            'reservation_id' => $reservation_id,
            'slip_id' => $selected_slip['slip_id'],
            'slip_number' => $selected_slip['slip_number'],
            'estimated_cost' => MM_Slip_Manager::calculate_reservation_cost(
                $selected_slip['slip_id'], 
                $data['start_date'], 
                $data['end_date']
            )
        );
    }
    
    /**
     * Find the best slip match for boat and preferences
     */
    private static function find_best_slip_match($available_slips, $boat_length, $data) {
        // Sort by preference - exact fit first, then smallest that fits
        usort($available_slips, function($a, $b) use ($boat_length) {
            $a_fit = $a['length'] - $boat_length;
            $b_fit = $b['length'] - $boat_length;
            
            // Prefer exact fit or closest fit
            if (abs($a_fit) !== abs($b_fit)) {
                return abs($a_fit) - abs($b_fit);
            }
            
            // If same fit difference, prefer by rate
            return $a['daily_rate'] - $b['daily_rate'];
        });
        
        // Consider user preferences
        $preferred_amenities = isset($data['preferred_amenities']) ? $data['preferred_amenities'] : array();
        $max_budget = isset($data['max_daily_rate']) ? floatval($data['max_daily_rate']) : 0;
        
        foreach ($available_slips as $slip) {
            // Check budget constraint
            if ($max_budget > 0 && $slip['daily_rate'] > $max_budget) {
                continue;
            }
            
            // Check amenity preferences
            if (!empty($preferred_amenities)) {
                $slip_amenities = $slip['amenities'] ? explode(',', $slip['amenities']) : array();
                $has_preferred = array_intersect($preferred_amenities, $slip_amenities);
                
                if (empty($has_preferred)) {
                    continue;
                }
            }
            
            return $slip; // Return first match
        }
        
        // Fallback to first available if no preferences match
        return $available_slips[0];
    }
    
    /**
     * Create or update boat record
     */
    private static function create_or_update_boat_record($data) {
        // Check if boat already exists by name and owner
        $existing_boat = get_posts(array(
            'post_type' => 'mm_boat',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => '_mm_boat_name',
                    'value' => $data['boat_name'],
                    'compare' => '='
                ),
                array(
                    'key' => '_mm_owner_email',
                    'value' => $data['owner_email'],
                    'compare' => '='
                )
            ),
            'posts_per_page' => 1
        ));
        
        $boat_data = array(
            'post_title' => sprintf('%s (%s)', $data['boat_name'], $data['owner_name']),
            'post_type' => 'mm_boat',
            'post_status' => 'publish',
            'meta_input' => array(
                '_mm_boat_name' => sanitize_text_field($data['boat_name']),
                '_mm_boat_type' => sanitize_text_field($data['boat_type']),
                '_mm_boat_length' => floatval($data['boat_length']),
                '_mm_boat_beam' => isset($data['boat_beam']) ? floatval($data['boat_beam']) : 0,
                '_mm_boat_draft' => isset($data['boat_draft']) ? floatval($data['boat_draft']) : 0,
                '_mm_boat_year' => isset($data['boat_year']) ? intval($data['boat_year']) : '',
                '_mm_boat_make' => isset($data['boat_make']) ? sanitize_text_field($data['boat_make']) : '',
                '_mm_boat_model' => isset($data['boat_model']) ? sanitize_text_field($data['boat_model']) : '',
                '_mm_registration_number' => isset($data['registration_number']) ? sanitize_text_field($data['registration_number']) : '',
                '_mm_boat_owner' => sanitize_text_field($data['owner_name']),
                '_mm_owner_email' => sanitize_email($data['owner_email']),
                '_mm_owner_phone' => isset($data['owner_phone']) ? sanitize_text_field($data['owner_phone']) : '',
                '_mm_owner_address' => isset($data['owner_address']) ? sanitize_textarea_field($data['owner_address']) : '',
                '_mm_emergency_contact' => isset($data['emergency_contact']) ? sanitize_text_field($data['emergency_contact']) : '',
                '_mm_emergency_phone' => isset($data['emergency_phone']) ? sanitize_text_field($data['emergency_phone']) : '',
                '_mm_insurance_company' => isset($data['insurance_company']) ? sanitize_text_field($data['insurance_company']) : '',
                '_mm_insurance_policy' => isset($data['insurance_policy']) ? sanitize_text_field($data['insurance_policy']) : '',
                '_mm_special_requirements' => isset($data['special_requirements']) ? sanitize_textarea_field($data['special_requirements']) : ''
            )
        );
        
        if ($existing_boat) {
            $boat_id = $existing_boat[0]->ID;
            $boat_data['ID'] = $boat_id;
            wp_update_post($boat_data);
        } else {
            $boat_id = wp_insert_post($boat_data);
        }
        
        if (!$boat_id) {
            return new WP_Error('boat_creation_failed', __('Failed to create boat record', 'marina-manager'));
        }
        
        return $boat_id;
    }
    
    /**
     * Create reservation record
     */
    private static function create_reservation_record($slip_id, $boat_id, $data) {
        $boat_name = $data['boat_name'];
        $slip_number = get_post_meta($slip_id, '_mm_slip_number', true);
        
        $reservation_type = isset($data['reservation_type']) ? $data['reservation_type'] : 'transient';
        
        $total_cost = MM_Slip_Manager::calculate_reservation_cost(
            $slip_id, 
            $data['start_date'], 
            $data['end_date']
        );
        
        $reservation_data = array(
            'post_title' => sprintf('%s - Slip %s - %s to %s',
                $boat_name,
                $slip_number,
                date('M j', strtotime($data['start_date'])),
                date('M j, Y', strtotime($data['end_date']))
            ),
            'post_type' => 'mm_reservation',
            'post_status' => 'mm_pending',
            'post_content' => isset($data['special_requests']) ? sanitize_textarea_field($data['special_requests']) : '',
            'meta_input' => array(
                '_mm_slip_id' => $slip_id,
                '_mm_boat_id' => $boat_id,
                '_mm_reservation_start' => date('Y-m-d H:i:s', strtotime($data['start_date'] . ' 14:00:00')), // Default 2 PM check-in
                '_mm_reservation_end' => date('Y-m-d H:i:s', strtotime($data['end_date'] . ' 11:00:00')), // Default 11 AM check-out
                '_mm_reservation_type' => $reservation_type,
                '_mm_boat_owner' => sanitize_text_field($data['owner_name']),
                '_mm_owner_email' => sanitize_email($data['owner_email']),
                '_mm_owner_phone' => isset($data['owner_phone']) ? sanitize_text_field($data['owner_phone']) : '',
                '_mm_total_amount' => $total_cost,
                '_mm_payment_status' => 'pending',
                '_mm_deposit_required' => $total_cost * 0.3, // 30% deposit
                '_mm_balance_due' => $total_cost * 0.7, // 70% balance
                '_mm_cancellation_policy' => self::get_cancellation_policy($reservation_type),
                '_mm_guest_count' => isset($data['guest_count']) ? intval($data['guest_count']) : 1,
                '_mm_pets' => isset($data['pets']) ? sanitize_text_field($data['pets']) : 'no',
                '_mm_power_requirements' => isset($data['power_requirements']) ? sanitize_text_field($data['power_requirements']) : '30amp',
                '_mm_requested_amenities' => isset($data['preferred_amenities']) ? implode(',', array_map('sanitize_text_field', $data['preferred_amenities'])) : '',
                '_mm_created_date' => current_time('mysql'),
                '_mm_created_by_ip' => $_SERVER['REMOTE_ADDR'] ?? ''
            )
        );
        
        $reservation_id = wp_insert_post($reservation_data);
        
        if ($reservation_id) {
            // Generate secure access hash
            $reservation_hash = wp_generate_password(32, false);
            update_post_meta($reservation_id, '_mm_reservation_hash', $reservation_hash);
            
            // Generate confirmation number
            $confirmation_number = 'MM-' . date('Y') . '-' . str_pad($reservation_id, 4, '0', STR_PAD_LEFT);
            update_post_meta($reservation_id, '_mm_confirmation_number', $confirmation_number);
            
            return $reservation_id;
        }
        
        return new WP_Error('reservation_creation_failed', __('Failed to create reservation', 'marina-manager'));
    }
    
    /**
     * Get cancellation policy for reservation type
     */
    private static function get_cancellation_policy($reservation_type) {
        $policies = array(
            'transient' => __('Cancellations more than 48 hours before arrival: Full refund. Within 48 hours: 50% refund. Same day: No refund.', 'marina-manager'),
            'seasonal' => __('Cancellations more than 30 days before start: 90% refund. 30-14 days: 50% refund. Less than 14 days: No refund.', 'marina-manager'),
            'annual' => __('Cancellations more than 60 days before start: 80% refund. 60-30 days: 50% refund. Less than 30 days: No refund.', 'marina-manager')
        );
        
        return isset($policies[$reservation_type]) ? $policies[$reservation_type] : $policies['transient'];
    }
    
    /**
     * Send reservation notifications
     */
    private static function send_reservation_notifications($reservation_id) {
        // Send to customer
        MM_Notification_System::send_reservation_confirmation($reservation_id);
        
        // Send to marina staff
        MM_Notification_System::send_admin_reservation_notification($reservation_id);
    }
    
    /**
     * Confirm reservation
     */
    public static function confirm_reservation($reservation_id, $notes = '') {
        $reservation = get_post($reservation_id);
        if (!$reservation || $reservation->post_type !== 'mm_reservation') {
            return new WP_Error('invalid_reservation', __('Invalid reservation ID', 'marina-manager'));
        }
        
        // Update reservation status
        wp_update_post(array(
            'ID' => $reservation_id,
            'post_status' => 'mm_confirmed'
        ));
        
        // Update slip status if long-term
        $slip_id = get_post_meta($reservation_id, '_mm_slip_id', true);
        $reservation_type = get_post_meta($reservation_id, '_mm_reservation_type', true);
        
        if (in_array($reservation_type, array('seasonal', 'annual'))) {
            update_post_meta($slip_id, '_mm_slip_status', 'reserved');
        }
        
        // Add confirmation notes
        if ($notes) {
            update_post_meta($reservation_id, '_mm_confirmation_notes', $notes);
        }
        
        update_post_meta($reservation_id, '_mm_confirmed_date', current_time('mysql'));
        update_post_meta($reservation_id, '_mm_confirmed_by', get_current_user_id());
        
        // Send confirmation email
        MM_Notification_System::send_reservation_approved($reservation_id);
        
        return true;
    }
    
    /**
     * Cancel reservation
     */
    public static function cancel_reservation($reservation_id, $reason = '', $refund_amount = 0) {
        $reservation = get_post($reservation_id);
        if (!$reservation || $reservation->post_type !== 'mm_reservation') {
            return new WP_Error('invalid_reservation', __('Invalid reservation ID', 'marina-manager'));
        }
        
        // Update reservation status
        wp_update_post(array(
            'ID' => $reservation_id,
            'post_status' => 'mm_cancelled'
        ));
        
        // Free up the slip
        $slip_id = get_post_meta($reservation_id, '_mm_slip_id', true);
        update_post_meta($slip_id, '_mm_slip_status', 'available');
        delete_post_meta($slip_id, '_mm_current_boat_id');
        
        // Record cancellation details
        update_post_meta($reservation_id, '_mm_cancellation_reason', $reason);
        update_post_meta($reservation_id, '_mm_cancellation_date', current_time('mysql'));
        update_post_meta($reservation_id, '_mm_cancelled_by', get_current_user_id());
        update_post_meta($reservation_id, '_mm_refund_amount', $refund_amount);
        
        // Process refund if applicable
        if ($refund_amount > 0) {
            self::process_refund($reservation_id, $refund_amount);
        }
        
        // Send cancellation notification
        MM_Notification_System::send_reservation_cancelled($reservation_id);
        
        return true;
    }
    
    /**
     * Check-in boat
     */
    public static function check_in_boat($reservation_id, $actual_arrival = null, $notes = '') {
        $reservation = get_post($reservation_id);
        if (!$reservation) {
            return new WP_Error('invalid_reservation', __('Invalid reservation ID', 'marina-manager'));
        }
        
        $slip_id = get_post_meta($reservation_id, '_mm_slip_id', true);
        $boat_id = get_post_meta($reservation_id, '_mm_boat_id', true);
        
        // Update reservation status
        wp_update_post(array(
            'ID' => $reservation_id,
            'post_status' => 'mm_checked_in'
        ));
        
        // Update slip status
        update_post_meta($slip_id, '_mm_slip_status', 'occupied');
        update_post_meta($slip_id, '_mm_current_boat_id', $boat_id);
        
        // Record check-in details
        $checkin_time = $actual_arrival ? $actual_arrival : current_time('mysql');
        update_post_meta($reservation_id, '_mm_actual_arrival', $checkin_time);
        update_post_meta($reservation_id, '_mm_checkin_notes', $notes);
        update_post_meta($reservation_id, '_mm_checked_in_by', get_current_user_id());
        
        // Create activity log
        MM_Harbor_Operations::log_harbor_activity($slip_id, 'boat_arrival', array(
            'reservation_id' => $reservation_id,
            'boat_id' => $boat_id,
            'arrival_time' => $checkin_time,
            'notes' => $notes
        ));
        
        return true;
    }
    
    /**
     * Check-out boat
     */
    public static function check_out_boat($reservation_id, $actual_departure = null, $damage_report = '', $additional_charges = 0) {
        $reservation = get_post($reservation_id);
        if (!$reservation) {
            return new WP_Error('invalid_reservation', __('Invalid reservation ID', 'marina-manager'));
        }
        
        $slip_id = get_post_meta($reservation_id, '_mm_slip_id', true);
        
        // Update reservation status
        wp_update_post(array(
            'ID' => $reservation_id,
            'post_status' => 'mm_completed'
        ));
        
        // Update slip status
        update_post_meta($slip_id, '_mm_slip_status', 'available');
        delete_post_meta($slip_id, '_mm_current_boat_id');
        
        // Record check-out details
        $checkout_time = $actual_departure ? $actual_departure : current_time('mysql');
        update_post_meta($reservation_id, '_mm_actual_departure', $checkout_time);
        update_post_meta($reservation_id, '_mm_damage_report', $damage_report);
        update_post_meta($reservation_id, '_mm_additional_charges', $additional_charges);
        update_post_meta($reservation_id, '_mm_checked_out_by', get_current_user_id());
        
        // Update total amount if additional charges
        if ($additional_charges > 0) {
            $current_total = (float)get_post_meta($reservation_id, '_mm_total_amount', true);
            update_post_meta($reservation_id, '_mm_total_amount', $current_total + $additional_charges);
        }
        
        // Generate final invoice
        $invoice_id = MM_Billing_Engine::generate_final_invoice($reservation_id);
        
        // Send checkout notification
        MM_Notification_System::send_checkout_summary($reservation_id);
        
        return $invoice_id;
    }
    
    /**
     * Get reservation details
     */
    public static function get_reservation_details($reservation_id) {
        $reservation = get_post($reservation_id);
        if (!$reservation) return false;
        
        $slip_id = get_post_meta($reservation_id, '_mm_slip_id', true);
        $boat_id = get_post_meta($reservation_id, '_mm_boat_id', true);
        
        return array(
            'id' => $reservation_id,
            'confirmation_number' => get_post_meta($reservation_id, '_mm_confirmation_number', true),
            'status' => $reservation->post_status,
            'start_date' => get_post_meta($reservation_id, '_mm_reservation_start', true),
            'end_date' => get_post_meta($reservation_id, '_mm_reservation_end', true),
            'type' => get_post_meta($reservation_id, '_mm_reservation_type', true),
            'total_amount' => get_post_meta($reservation_id, '_mm_total_amount', true),
            'deposit_required' => get_post_meta($reservation_id, '_mm_deposit_required', true),
            'payment_status' => get_post_meta($reservation_id, '_mm_payment_status', true),
            'slip' => MM_Slip_Manager::get_slip_details($slip_id),
            'boat' => self::get_boat_details($boat_id),
            'owner_email' => get_post_meta($reservation_id, '_mm_owner_email', true),
            'owner_phone' => get_post_meta($reservation_id, '_mm_owner_phone', true),
            'special_requests' => $reservation->post_content,
            'guest_count' => get_post_meta($reservation_id, '_mm_guest_count', true),
            'power_requirements' => get_post_meta($reservation_id, '_mm_power_requirements', true),
            'created_date' => get_post_meta($reservation_id, '_mm_created_date', true)
        );
    }
    
    /**
     * Get boat details
     */
    private static function get_boat_details($boat_id) {
        $boat = get_post($boat_id);
        if (!$boat) return null;
        
        return array(
            'id' => $boat_id,
            'name' => get_post_meta($boat_id, '_mm_boat_name', true),
            'type' => get_post_meta($boat_id, '_mm_boat_type', true),
            'length' => get_post_meta($boat_id, '_mm_boat_length', true),
            'beam' => get_post_meta($boat_id, '_mm_boat_beam', true),
            'draft' => get_post_meta($boat_id, '_mm_boat_draft', true),
            'make' => get_post_meta($boat_id, '_mm_boat_make', true),
            'model' => get_post_meta($boat_id, '_mm_boat_model', true),
            'year' => get_post_meta($boat_id, '_mm_boat_year', true),
            'registration' => get_post_meta($boat_id, '_mm_registration_number', true),
            'owner' => get_post_meta($boat_id, '_mm_boat_owner', true)
        );
    }
    
    /**
     * Process refund
     */
    private static function process_refund($reservation_id, $amount) {
        // Integration point for payment processors
        // This would integrate with Stripe, PayPal, etc.
        
        // For now, just log the refund request
        $refund_log = array(
            'post_title' => sprintf('Refund Request - Reservation %s - $%.2f', 
                get_post_meta($reservation_id, '_mm_confirmation_number', true),
                $amount
            ),
            'post_type' => 'mm_refund_log',
            'post_status' => 'publish',
            'meta_input' => array(
                '_mm_reservation_id' => $reservation_id,
                '_mm_refund_amount' => $amount,
                '_mm_refund_status' => 'pending',
                '_mm_refund_method' => 'manual', // Will be automated with payment gateway
                '_mm_requested_date' => current_time('mysql')
            )
        );
        
        wp_insert_post($refund_log);
    }
    
    /**
     * AJAX: Submit reservation
     */
    public static function ajax_submit_reservation() {
        check_ajax_referer('mm_public_nonce', 'nonce');
        
        $data = array(
            'boat_name' => sanitize_text_field($_POST['boat_name']),
            'boat_type' => sanitize_text_field($_POST['boat_type']),
            'boat_length' => floatval($_POST['boat_length']),
            'boat_beam' => isset($_POST['boat_beam']) ? floatval($_POST['boat_beam']) : 0,
            'boat_draft' => isset($_POST['boat_draft']) ? floatval($_POST['boat_draft']) : 0,
            'owner_name' => sanitize_text_field($_POST['owner_name']),
            'owner_email' => sanitize_email($_POST['owner_email']),
            'owner_phone' => sanitize_text_field($_POST['owner_phone']),
            'start_date' => sanitize_text_field($_POST['start_date']),
            'end_date' => sanitize_text_field($_POST['end_date']),
            'reservation_type' => sanitize_text_field($_POST['reservation_type']),
            'guest_count' => intval($_POST['guest_count']),
            'power_requirements' => sanitize_text_field($_POST['power_requirements']),
            'special_requests' => sanitize_textarea_field($_POST['special_requests'])
        );
        
        $result = self::process_reservation_request($data);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success(array(
            'message' => __('Reservation request submitted successfully! You will receive a confirmation email within 24 hours.', 'marina-manager'),
            'reservation_id' => $result['reservation_id'],
            'confirmation_number' => get_post_meta($result['reservation_id'], '_mm_confirmation_number', true)
        ));
    }
    
    /**
     * AJAX: Confirm reservation (admin)
     */
    public static function ajax_confirm_reservation() {
        check_ajax_referer('mm_admin_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(__('Insufficient permissions', 'marina-manager'));
        }
        
        $reservation_id = intval($_POST['reservation_id']);
        $notes = sanitize_textarea_field($_POST['notes']);
        
        $result = self::confirm_reservation($reservation_id, $notes);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success(__('Reservation confirmed successfully', 'marina-manager'));
    }
    
    /**
     * AJAX: Cancel reservation
     */
    public static function ajax_cancel_reservation() {
        check_ajax_referer('mm_admin_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(__('Insufficient permissions', 'marina-manager'));
        }
        
        $reservation_id = intval($_POST['reservation_id']);
        $reason = sanitize_textarea_field($_POST['reason']);
        $refund_amount = floatval($_POST['refund_amount']);
        
        $result = self::cancel_reservation($reservation_id, $reason, $refund_amount);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success(__('Reservation cancelled successfully', 'marina-manager'));
    }
}