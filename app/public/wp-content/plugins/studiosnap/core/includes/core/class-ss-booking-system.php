<?php
/**
 * Booking System for StudioSnap
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class SS_Booking_System {
    
    public function __construct() {
        add_action('wp_ajax_ss_submit_booking', array($this, 'ajax_submit_booking'));
        add_action('wp_ajax_nopriv_ss_submit_booking', array($this, 'ajax_submit_booking'));
        add_action('wp_ajax_ss_check_availability', array($this, 'ajax_check_availability'));
        add_action('wp_ajax_nopriv_ss_check_availability', array($this, 'ajax_check_availability'));
    }
    
    public static function register_booking_statuses() {
        // Register custom post statuses for bookings
        register_post_status('ss_inquiry', array(
            'label' => __('Inquiry', 'studiosnap'),
            'public' => false,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Inquiry (%s)', 'Inquiries (%s)', 'studiosnap'),
        ));
        
        register_post_status('ss_confirmed', array(
            'label' => __('Confirmed', 'studiosnap'),
            'public' => false,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Confirmed (%s)', 'Confirmed (%s)', 'studiosnap'),
        ));
        
        register_post_status('ss_completed', array(
            'label' => __('Completed', 'studiosnap'),
            'public' => false,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Completed (%s)', 'Completed (%s)', 'studiosnap'),
        ));
        
        register_post_status('ss_cancelled', array(
            'label' => __('Cancelled', 'studiosnap'),
            'public' => false,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Cancelled (%s)', 'Cancelled (%s)', 'studiosnap'),
        ));
    }
    
    /**
     * Check availability for a specific date and time
     */
    public static function check_availability($date, $start_time, $duration_hours = 2) {
        global $wpdb;
        
        $start_datetime = $date . ' ' . $start_time;
        $end_datetime = date('Y-m-d H:i:s', strtotime($start_datetime . ' +' . $duration_hours . ' hours'));
        
        // Check for conflicting bookings
        $conflicts = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*) FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_ss_session_date'
            LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_ss_session_start_time'
            LEFT JOIN {$wpdb->postmeta} pm3 ON p.ID = pm3.post_id AND pm3.meta_key = '_ss_session_duration'
            WHERE p.post_type = 'ss_booking'
            AND p.post_status IN ('ss_confirmed', 'ss_inquiry')
            AND pm1.meta_value = %s
            AND (
                (pm2.meta_value <= %s AND DATE_ADD(CONCAT(pm1.meta_value, ' ', pm2.meta_value), INTERVAL pm3.meta_value HOUR) > %s)
                OR 
                (pm2.meta_value < %s AND DATE_ADD(CONCAT(pm1.meta_value, ' ', pm2.meta_value), INTERVAL pm3.meta_value HOUR) >= %s)
            )
        ", $date, $start_time, $start_datetime, $end_datetime, $end_datetime));
        
        return $conflicts == 0;
    }
    
    /**
     * Get available time slots for a specific date
     */
    public static function get_available_slots($date, $session_type = 'portrait') {
        $business_hours = self::get_business_hours();
        $day_of_week = strtolower(date('l', strtotime($date)));
        
        if (!isset($business_hours[$day_of_week]) || !$business_hours[$day_of_week]['open']) {
            return array(); // Business closed this day
        }
        
        $start_hour = $business_hours[$day_of_week]['start'];
        $end_hour = $business_hours[$day_of_week]['end'];
        $session_duration = self::get_session_duration($session_type);
        
        $available_slots = array();
        $current_time = strtotime($date . ' ' . $start_hour);
        $end_time = strtotime($date . ' ' . $end_hour);
        
        while ($current_time + ($session_duration * 3600) <= $end_time) {
            $slot_time = date('H:i', $current_time);
            
            if (self::check_availability($date, $slot_time, $session_duration)) {
                $available_slots[] = array(
                    'time' => $slot_time,
                    'display' => date('g:i A', $current_time),
                    'datetime' => $date . ' ' . $slot_time
                );
            }
            
            // Move to next hour slot
            $current_time += 3600; // 1 hour intervals
        }
        
        return $available_slots;
    }
    
    /**
     * Create a new booking
     */
    public static function create_booking($booking_data) {
        // Validate required fields
        $required_fields = array('client_name', 'client_email', 'session_date', 'session_time', 'session_type');
        foreach ($required_fields as $field) {
            if (empty($booking_data[$field])) {
                return new WP_Error('missing_field', sprintf(__('Required field missing: %s', 'studiosnap'), $field));
            }
        }
        
        // Validate email
        if (!is_email($booking_data['client_email'])) {
            return new WP_Error('invalid_email', __('Invalid email address', 'studiosnap'));
        }
        
        // Check availability again
        $duration = self::get_session_duration($booking_data['session_type']);
        if (!self::check_availability($booking_data['session_date'], $booking_data['session_time'], $duration)) {
            return new WP_Error('slot_unavailable', __('Selected time slot is no longer available', 'studiosnap'));
        }
        
        // Create client if doesn't exist
        $client_id = self::get_or_create_client($booking_data['client_name'], $booking_data['client_email'], $booking_data);
        
        // Create booking post
        $booking_post = array(
            'post_title' => sprintf('%s - %s %s', 
                $booking_data['client_name'], 
                ucfirst($booking_data['session_type']), 
                __('Session', 'studiosnap')
            ),
            'post_type' => 'ss_booking',
            'post_status' => 'ss_inquiry',
            'post_author' => get_current_user_id(),
            'meta_input' => array(
                '_ss_client_id' => $client_id,
                '_ss_client_name' => sanitize_text_field($booking_data['client_name']),
                '_ss_client_email' => sanitize_email($booking_data['client_email']),
                '_ss_client_phone' => sanitize_text_field($booking_data['client_phone'] ?? ''),
                '_ss_session_date' => sanitize_text_field($booking_data['session_date']),
                '_ss_session_start_time' => sanitize_text_field($booking_data['session_time']),
                '_ss_session_duration' => $duration,
                '_ss_session_type' => sanitize_text_field($booking_data['session_type']),
                '_ss_session_location' => sanitize_text_field($booking_data['session_location'] ?? 'studio'),
                '_ss_special_requests' => sanitize_textarea_field($booking_data['special_requests'] ?? ''),
                '_ss_booking_source' => sanitize_text_field($booking_data['booking_source'] ?? 'website'),
                '_ss_created_date' => current_time('mysql'),
                '_ss_booking_hash' => wp_generate_password(32, false)
            )
        );
        
        $booking_id = wp_insert_post($booking_post);
        
        if (is_wp_error($booking_id)) {
            return $booking_id;
        }
        
        // Calculate pricing
        $pricing = self::calculate_session_pricing($booking_data['session_type'], $booking_data);
        update_post_meta($booking_id, '_ss_base_price', $pricing['base_price']);
        update_post_meta($booking_id, '_ss_total_price', $pricing['total_price']);
        
        // Send confirmation email
        SS_Email_Handler::send_booking_inquiry_email($booking_id);
        
        // Send admin notification
        SS_Email_Handler::send_admin_booking_notification($booking_id);
        
        return $booking_id;
    }
    
    /**
     * Update booking status
     */
    public static function update_booking_status($booking_id, $new_status, $notes = '') {
        $booking = get_post($booking_id);
        if (!$booking || $booking->post_type !== 'ss_booking') {
            return false;
        }
        
        $old_status = $booking->post_status;
        
        // Update post status
        wp_update_post(array(
            'ID' => $booking_id,
            'post_status' => $new_status
        ));
        
        // Log status change
        $status_log = get_post_meta($booking_id, '_ss_status_log', true) ?: array();
        $status_log[] = array(
            'from' => $old_status,
            'to' => $new_status,
            'date' => current_time('mysql'),
            'user' => get_current_user_id(),
            'notes' => $notes
        );
        update_post_meta($booking_id, '_ss_status_log', $status_log);
        
        // Send appropriate emails based on status change
        switch ($new_status) {
            case 'ss_confirmed':
                SS_Email_Handler::send_booking_confirmation_email($booking_id);
                break;
            case 'ss_cancelled':
                SS_Email_Handler::send_booking_cancellation_email($booking_id);
                break;
            case 'ss_completed':
                SS_Email_Handler::send_session_completed_email($booking_id);
                break;
        }
        
        return true;
    }
    
    /**
     * Get or create client
     */
    private static function get_or_create_client($name, $email, $additional_data = array()) {
        // Check if client already exists
        $existing_client = get_posts(array(
            'post_type' => 'ss_client',
            'meta_query' => array(
                array(
                    'key' => '_ss_client_email',
                    'value' => $email,
                    'compare' => '='
                )
            ),
            'posts_per_page' => 1
        ));
        
        if (!empty($existing_client)) {
            return $existing_client[0]->ID;
        }
        
        // Create new client
        $client_post = array(
            'post_title' => $name,
            'post_type' => 'ss_client',
            'post_status' => 'publish',
            'meta_input' => array(
                '_ss_client_name' => $name,
                '_ss_client_email' => $email,
                '_ss_client_phone' => $additional_data['client_phone'] ?? '',
                '_ss_client_created_date' => current_time('mysql'),
                '_ss_total_sessions' => 0,
                '_ss_total_spent' => 0
            )
        );
        
        return wp_insert_post($client_post);
    }
    
    /**
     * Calculate session pricing
     */
    private static function calculate_session_pricing($session_type, $booking_data = array()) {
        $packages = self::get_session_packages();
        $base_price = isset($packages[$session_type]) ? $packages[$session_type]['price'] : 200;
        
        // Apply any modifiers
        $total_price = $base_price;
        
        // Location modifier
        if (isset($booking_data['session_location']) && $booking_data['session_location'] === 'on_location') {
            $total_price += 50; // Travel fee
        }
        
        // Rush booking (less than 48 hours)
        $booking_date = strtotime($booking_data['session_date'] . ' ' . $booking_data['session_time']);
        if ($booking_date < strtotime('+48 hours')) {
            $total_price *= 1.2; // 20% rush fee
        }
        
        return array(
            'base_price' => $base_price,
            'total_price' => $total_price,
            'modifiers' => array()
        );
    }
    
    /**
     * Get session packages/types
     */
    public static function get_session_packages() {
        return array(
            'portrait' => array(
                'name' => __('Portrait Session', 'studiosnap'),
                'duration' => 2,
                'price' => 200,
                'description' => __('Individual or couple portraits in studio or on location', 'studiosnap')
            ),
            'family' => array(
                'name' => __('Family Session', 'studiosnap'),
                'duration' => 3,
                'price' => 300,
                'description' => __('Family portraits with up to 6 people', 'studiosnap')
            ),
            'headshot' => array(
                'name' => __('Professional Headshots', 'studiosnap'),
                'duration' => 1,
                'price' => 150,
                'description' => __('Professional headshots for business use', 'studiosnap')
            ),
            'event' => array(
                'name' => __('Event Photography', 'studiosnap'),
                'duration' => 4,
                'price' => 500,
                'description' => __('Wedding, party, or corporate event coverage', 'studiosnap')
            ),
            'product' => array(
                'name' => __('Product Photography', 'studiosnap'),
                'duration' => 2,
                'price' => 250,
                'description' => __('Professional product photography for e-commerce or marketing', 'studiosnap')
            )
        );
    }
    
    /**
     * Get session duration
     */
    private static function get_session_duration($session_type) {
        $packages = self::get_session_packages();
        return isset($packages[$session_type]) ? $packages[$session_type]['duration'] : 2;
    }
    
    /**
     * Get business hours
     */
    private static function get_business_hours() {
        return get_option('ss_business_hours', array(
            'monday' => array('open' => true, 'start' => '09:00', 'end' => '17:00'),
            'tuesday' => array('open' => true, 'start' => '09:00', 'end' => '17:00'),
            'wednesday' => array('open' => true, 'start' => '09:00', 'end' => '17:00'),
            'thursday' => array('open' => true, 'start' => '09:00', 'end' => '17:00'),
            'friday' => array('open' => true, 'start' => '09:00', 'end' => '17:00'),
            'saturday' => array('open' => true, 'start' => '10:00', 'end' => '16:00'),
            'sunday' => array('open' => false, 'start' => '', 'end' => '')
        ));
    }
    
    /**
     * AJAX: Submit booking from frontend
     */
    public static function ajax_submit_booking() {
        check_ajax_referer('ss_booking_nonce', 'nonce');
        
        $booking_data = array(
            'client_name' => sanitize_text_field($_POST['client_name']),
            'client_email' => sanitize_email($_POST['client_email']),
            'client_phone' => sanitize_text_field($_POST['client_phone']),
            'session_date' => sanitize_text_field($_POST['session_date']),
            'session_time' => sanitize_text_field($_POST['session_time']),
            'session_type' => sanitize_text_field($_POST['session_type']),
            'session_location' => sanitize_text_field($_POST['session_location']),
            'special_requests' => sanitize_textarea_field($_POST['special_requests']),
            'booking_source' => 'website'
        );
        
        $result = self::create_booking($booking_data);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        } else {
            wp_send_json_success(array(
                'booking_id' => $result,
                'message' => __('Booking inquiry submitted successfully! We will contact you within 24 hours to confirm your session.', 'studiosnap')
            ));
        }
    }
    
    /**
     * AJAX: Check availability
     */
    public static function ajax_check_availability() {
        check_ajax_referer('ss_booking_nonce', 'nonce');
        
        $date = sanitize_text_field($_POST['date']);
        $session_type = sanitize_text_field($_POST['session_type']);
        
        $available_slots = self::get_available_slots($date, $session_type);
        
        wp_send_json_success($available_slots);
    }
    
    /**
     * AJAX: Update booking status from admin
     */
    public static function ajax_update_booking_status() {
        check_ajax_referer('ss_admin_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(__('Insufficient permissions', 'studiosnap'));
        }
        
        $booking_id = intval($_POST['booking_id']);
        $new_status = sanitize_text_field($_POST['new_status']);
        $notes = sanitize_textarea_field($_POST['notes']);
        
        $result = self::update_booking_status($booking_id, $new_status, $notes);
        
        if ($result) {
            wp_send_json_success(__('Booking status updated successfully', 'studiosnap'));
        } else {
            wp_send_json_error(__('Failed to update booking status', 'studiosnap'));
        }
    }
}