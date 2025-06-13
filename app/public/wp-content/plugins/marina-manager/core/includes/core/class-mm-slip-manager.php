<?php
/**
 * Marina Manager Slip Manager - Core slip management and availability system
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class MM_Slip_Manager {
    
    public static function init() {
        add_action('wp_ajax_mm_check_slip_availability', array(__CLASS__, 'ajax_check_availability'));
        add_action('wp_ajax_nopriv_mm_check_slip_availability', array(__CLASS__, 'ajax_check_availability'));
        add_action('wp_ajax_mm_assign_boat', array(__CLASS__, 'ajax_assign_boat'));
        add_action('wp_ajax_mm_update_slip_status', array(__CLASS__, 'ajax_update_slip_status'));
    }
    
    /**
     * Check slip availability for specific dates and boat size
     */
    public static function check_availability($start_date, $end_date, $boat_length = 0, $slip_type = 'any') {
        global $wpdb;
        
        $marina_slips = get_posts(array(
            'post_type' => 'mm_slip',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => '_mm_slip_status',
                    'value' => array('available', 'seasonal'),
                    'compare' => 'IN'
                )
            )
        ));
        
        $available_slips = array();
        
        foreach ($marina_slips as $slip) {
            $slip_id = $slip->ID;
            $slip_length = (float)get_post_meta($slip_id, '_mm_slip_length', true);
            $slip_width = (float)get_post_meta($slip_id, '_mm_slip_width', true);
            $slip_type_meta = get_post_meta($slip_id, '_mm_slip_type', true);
            $daily_rate = (float)get_post_meta($slip_id, '_mm_daily_rate', true);
            $monthly_rate = (float)get_post_meta($slip_id, '_mm_monthly_rate', true);
            
            // Check boat size compatibility
            if ($boat_length > 0 && $slip_length < $boat_length) {
                continue;
            }
            
            // Check slip type compatibility
            if ($slip_type !== 'any' && $slip_type_meta !== $slip_type) {
                continue;
            }
            
            // Check for conflicting reservations
            $conflicts = self::check_slip_conflicts($slip_id, $start_date, $end_date);
            
            if (!$conflicts) {
                $available_slips[] = array(
                    'slip_id' => $slip_id,
                    'slip_number' => get_post_meta($slip_id, '_mm_slip_number', true),
                    'dock_section' => get_post_meta($slip_id, '_mm_dock_section', true),
                    'length' => $slip_length,
                    'width' => $slip_width,
                    'type' => $slip_type_meta,
                    'daily_rate' => $daily_rate,
                    'monthly_rate' => $monthly_rate,
                    'amenities' => get_post_meta($slip_id, '_mm_slip_amenities', true),
                    'utilities' => get_post_meta($slip_id, '_mm_utilities_included', true)
                );
            }
        }
        
        return $available_slips;
    }
    
    /**
     * Check for slip conflicts
     */
    private static function check_slip_conflicts($slip_id, $start_date, $end_date) {
        global $wpdb;
        
        $start_datetime = date('Y-m-d 00:00:00', strtotime($start_date));
        $end_datetime = date('Y-m-d 23:59:59', strtotime($end_date));
        
        $conflicts = $wpdb->get_results($wpdb->prepare("
            SELECT COUNT(*) as conflict_count
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_mm_slip_id'
            INNER JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_mm_reservation_start'
            INNER JOIN {$wpdb->postmeta} pm3 ON p.ID = pm3.post_id AND pm3.meta_key = '_mm_reservation_end'
            WHERE p.post_type = 'mm_reservation'
            AND p.post_status IN ('mm_confirmed', 'mm_checked_in')
            AND pm1.meta_value = %s
            AND (
                (pm2.meta_value <= %s AND pm3.meta_value >= %s) OR
                (pm2.meta_value <= %s AND pm3.meta_value >= %s) OR
                (pm2.meta_value >= %s AND pm3.meta_value <= %s)
            )
        ", $slip_id, $start_datetime, $start_datetime, $end_datetime, $end_datetime, $start_datetime, $end_datetime));
        
        return $conflicts[0]->conflict_count > 0;
    }
    
    /**
     * Create slip reservation
     */
    public static function create_reservation($slip_id, $boat_id, $start_date, $end_date, $reservation_type = 'transient') {
        $slip = get_post($slip_id);
        $boat = get_post($boat_id);
        
        if (!$slip || !$boat) {
            return new WP_Error('invalid_data', __('Invalid slip or boat ID', 'marina-manager'));
        }
        
        // Check availability one more time
        if (self::check_slip_conflicts($slip_id, $start_date, $end_date)) {
            return new WP_Error('slip_unavailable', __('Slip is no longer available for the selected dates', 'marina-manager'));
        }
        
        $boat_name = get_post_meta($boat_id, '_mm_boat_name', true);
        $boat_owner = get_post_meta($boat_id, '_mm_boat_owner', true);
        
        $reservation_data = array(
            'post_title' => sprintf('%s - Slip %s - %s to %s',
                $boat_name,
                get_post_meta($slip_id, '_mm_slip_number', true),
                date('M j', strtotime($start_date)),
                date('M j, Y', strtotime($end_date))
            ),
            'post_type' => 'mm_reservation',
            'post_status' => 'mm_pending',
            'meta_input' => array(
                '_mm_slip_id' => $slip_id,
                '_mm_boat_id' => $boat_id,
                '_mm_reservation_start' => date('Y-m-d H:i:s', strtotime($start_date)),
                '_mm_reservation_end' => date('Y-m-d H:i:s', strtotime($end_date)),
                '_mm_reservation_type' => $reservation_type,
                '_mm_boat_owner' => $boat_owner,
                '_mm_total_amount' => self::calculate_reservation_cost($slip_id, $start_date, $end_date),
                '_mm_payment_status' => 'pending',
                '_mm_created_by' => get_current_user_id()
            )
        );
        
        $reservation_id = wp_insert_post($reservation_data);
        
        if ($reservation_id) {
            // Update slip status if long-term reservation
            if ($reservation_type === 'seasonal' || $reservation_type === 'annual') {
                update_post_meta($slip_id, '_mm_slip_status', 'occupied');
                update_post_meta($slip_id, '_mm_current_boat_id', $boat_id);
            }
            
            // Generate reservation hash for public access
            $reservation_hash = wp_generate_password(32, false);
            update_post_meta($reservation_id, '_mm_reservation_hash', $reservation_hash);
            
            return $reservation_id;
        }
        
        return new WP_Error('reservation_failed', __('Failed to create reservation', 'marina-manager'));
    }
    
    /**
     * Calculate reservation cost
     */
    public static function calculate_reservation_cost($slip_id, $start_date, $end_date) {
        $daily_rate = (float)get_post_meta($slip_id, '_mm_daily_rate', true);
        $monthly_rate = (float)get_post_meta($slip_id, '_mm_monthly_rate', true);
        
        $start_timestamp = strtotime($start_date);
        $end_timestamp = strtotime($end_date);
        $days = ceil(($end_timestamp - $start_timestamp) / (60 * 60 * 24));
        
        if ($days >= 30 && $monthly_rate > 0) {
            $months = ceil($days / 30);
            $total_cost = $months * $monthly_rate;
        } else {
            $total_cost = $days * $daily_rate;
        }
        
        // Add marina fees
        $utility_fee = (float)get_option('mm_utility_fee', 5.00); // Per day
        $cleaning_fee = (float)get_option('mm_cleaning_fee', 25.00); // One time
        $security_deposit = (float)get_option('mm_security_deposit', 100.00); // Refundable
        
        $total_cost += ($utility_fee * $days) + $cleaning_fee + $security_deposit;
        
        return $total_cost;
    }
    
    /**
     * Get slip occupancy status
     */
    public static function get_slip_occupancy_overview() {
        $slips = get_posts(array(
            'post_type' => 'mm_slip',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_mm_slip_status',
                    'compare' => 'EXISTS'
                )
            )
        ));
        
        $overview = array(
            'total' => count($slips),
            'available' => 0,
            'occupied' => 0,
            'maintenance' => 0,
            'reserved' => 0,
            'revenue_this_month' => 0,
            'occupancy_rate' => 0
        );
        
        foreach ($slips as $slip) {
            $status = get_post_meta($slip->ID, '_mm_slip_status', true);
            
            switch ($status) {
                case 'available':
                    $overview['available']++;
                    break;
                case 'occupied':
                    $overview['occupied']++;
                    break;
                case 'maintenance':
                    $overview['maintenance']++;
                    break;
                case 'reserved':
                    $overview['reserved']++;
                    break;
            }
        }
        
        // Calculate occupancy rate
        $occupied_count = $overview['occupied'] + $overview['reserved'];
        $overview['occupancy_rate'] = $overview['total'] > 0 ? 
            round(($occupied_count / $overview['total']) * 100, 1) : 0;
        
        // Calculate monthly revenue
        $overview['revenue_this_month'] = self::calculate_monthly_revenue();
        
        return $overview;
    }
    
    /**
     * Calculate monthly revenue
     */
    private static function calculate_monthly_revenue() {
        global $wpdb;
        
        $current_month = date('Y-m');
        
        $revenue = $wpdb->get_var($wpdb->prepare("
            SELECT SUM(CAST(pm.meta_value AS DECIMAL(10,2)))
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
            WHERE p.post_type = 'mm_reservation'
            AND pm.meta_key = '_mm_total_amount'
            AND p.post_date LIKE %s
            AND p.post_status IN ('mm_confirmed', 'mm_completed')
        ", $current_month . '%'));
        
        return (float)$revenue;
    }
    
    /**
     * Get slip details with current status
     */
    public static function get_slip_details($slip_id) {
        $slip = get_post($slip_id);
        if (!$slip) return false;
        
        $slip_data = array(
            'id' => $slip_id,
            'number' => get_post_meta($slip_id, '_mm_slip_number', true),
            'dock_section' => get_post_meta($slip_id, '_mm_dock_section', true),
            'length' => get_post_meta($slip_id, '_mm_slip_length', true),
            'width' => get_post_meta($slip_id, '_mm_slip_width', true),
            'type' => get_post_meta($slip_id, '_mm_slip_type', true),
            'status' => get_post_meta($slip_id, '_mm_slip_status', true),
            'daily_rate' => get_post_meta($slip_id, '_mm_daily_rate', true),
            'monthly_rate' => get_post_meta($slip_id, '_mm_monthly_rate', true),
            'utilities' => get_post_meta($slip_id, '_mm_utilities_included', true),
            'amenities' => get_post_meta($slip_id, '_mm_slip_amenities', true),
            'current_boat' => null,
            'current_reservation' => null,
            'maintenance_notes' => get_post_meta($slip_id, '_mm_maintenance_notes', true)
        );
        
        // Get current boat if occupied
        $current_boat_id = get_post_meta($slip_id, '_mm_current_boat_id', true);
        if ($current_boat_id) {
            $boat = get_post($current_boat_id);
            if ($boat) {
                $slip_data['current_boat'] = array(
                    'id' => $current_boat_id,
                    'name' => get_post_meta($current_boat_id, '_mm_boat_name', true),
                    'type' => get_post_meta($current_boat_id, '_mm_boat_type', true),
                    'length' => get_post_meta($current_boat_id, '_mm_boat_length', true),
                    'owner' => get_post_meta($current_boat_id, '_mm_boat_owner', true),
                    'owner_contact' => get_post_meta($current_boat_id, '_mm_owner_contact', true)
                );
            }
        }
        
        // Get current active reservation
        $current_reservation = self::get_current_reservation($slip_id);
        if ($current_reservation) {
            $slip_data['current_reservation'] = $current_reservation;
        }
        
        return $slip_data;
    }
    
    /**
     * Get current active reservation for slip
     */
    private static function get_current_reservation($slip_id) {
        $current_date = current_time('Y-m-d H:i:s');
        
        $reservations = get_posts(array(
            'post_type' => 'mm_reservation',
            'post_status' => array('mm_confirmed', 'mm_checked_in'),
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => '_mm_slip_id',
                    'value' => $slip_id,
                    'compare' => '='
                ),
                array(
                    'key' => '_mm_reservation_start',
                    'value' => $current_date,
                    'compare' => '<='
                ),
                array(
                    'key' => '_mm_reservation_end',
                    'value' => $current_date,
                    'compare' => '>='
                )
            ),
            'posts_per_page' => 1
        ));
        
        if ($reservations) {
            $reservation = $reservations[0];
            return array(
                'id' => $reservation->ID,
                'start_date' => get_post_meta($reservation->ID, '_mm_reservation_start', true),
                'end_date' => get_post_meta($reservation->ID, '_mm_reservation_end', true),
                'type' => get_post_meta($reservation->ID, '_mm_reservation_type', true),
                'total_amount' => get_post_meta($reservation->ID, '_mm_total_amount', true),
                'payment_status' => get_post_meta($reservation->ID, '_mm_payment_status', true)
            );
        }
        
        return null;
    }
    
    /**
     * Update slip status
     */
    public static function update_slip_status($slip_id, $new_status, $notes = '') {
        $valid_statuses = array('available', 'occupied', 'maintenance', 'reserved', 'out_of_service');
        
        if (!in_array($new_status, $valid_statuses)) {
            return new WP_Error('invalid_status', __('Invalid slip status', 'marina-manager'));
        }
        
        $updated = update_post_meta($slip_id, '_mm_slip_status', $new_status);
        
        if ($notes) {
            update_post_meta($slip_id, '_mm_status_notes', $notes);
        }
        
        // Clear current boat if changing to available or maintenance
        if (in_array($new_status, array('available', 'maintenance', 'out_of_service'))) {
            delete_post_meta($slip_id, '_mm_current_boat_id');
        }
        
        // Log status change
        self::log_slip_activity($slip_id, 'status_change', array(
            'new_status' => $new_status,
            'notes' => $notes,
            'changed_by' => get_current_user_id()
        ));
        
        return $updated;
    }
    
    /**
     * Log slip activity
     */
    private static function log_slip_activity($slip_id, $activity_type, $data = array()) {
        $log_entry = array(
            'post_title' => sprintf('Slip %s - %s - %s',
                get_post_meta($slip_id, '_mm_slip_number', true),
                ucfirst(str_replace('_', ' ', $activity_type)),
                current_time('mysql')
            ),
            'post_type' => 'mm_activity_log',
            'post_status' => 'publish',
            'meta_input' => array_merge(array(
                '_mm_slip_id' => $slip_id,
                '_mm_activity_type' => $activity_type,
                '_mm_timestamp' => current_time('mysql')
            ), $data)
        );
        
        wp_insert_post($log_entry);
    }
    
    /**
     * Get slip availability calendar data
     */
    public static function get_availability_calendar($month = null, $year = null) {
        if (!$month) $month = date('n');
        if (!$year) $year = date('Y');
        
        $start_date = sprintf('%04d-%02d-01', $year, $month);
        $end_date = date('Y-m-t', strtotime($start_date));
        
        $slips = get_posts(array(
            'post_type' => 'mm_slip',
            'posts_per_page' => -1,
            'orderby' => 'meta_value_num',
            'meta_key' => '_mm_slip_number',
            'order' => 'ASC'
        ));
        
        $calendar_data = array();
        
        foreach ($slips as $slip) {
            $slip_id = $slip->ID;
            $slip_number = get_post_meta($slip_id, '_mm_slip_number', true);
            
            $calendar_data[$slip_number] = array(
                'slip_id' => $slip_id,
                'status' => get_post_meta($slip_id, '_mm_slip_status', true),
                'reservations' => self::get_slip_reservations_for_period($slip_id, $start_date, $end_date)
            );
        }
        
        return $calendar_data;
    }
    
    /**
     * Get slip reservations for specific period
     */
    private static function get_slip_reservations_for_period($slip_id, $start_date, $end_date) {
        global $wpdb;
        
        $reservations = $wpdb->get_results($wpdb->prepare("
            SELECT p.ID, p.post_title,
                   pm1.meta_value as start_date,
                   pm2.meta_value as end_date,
                   pm3.meta_value as boat_id,
                   pm4.meta_value as reservation_type
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm_slip ON p.ID = pm_slip.post_id AND pm_slip.meta_key = '_mm_slip_id'
            INNER JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_mm_reservation_start'
            INNER JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_mm_reservation_end'
            LEFT JOIN {$wpdb->postmeta} pm3 ON p.ID = pm3.post_id AND pm3.meta_key = '_mm_boat_id'
            LEFT JOIN {$wpdb->postmeta} pm4 ON p.ID = pm4.post_id AND pm4.meta_key = '_mm_reservation_type'
            WHERE p.post_type = 'mm_reservation'
            AND p.post_status IN ('mm_confirmed', 'mm_checked_in', 'mm_pending')
            AND pm_slip.meta_value = %s
            AND (
                (pm1.meta_value <= %s AND pm2.meta_value >= %s) OR
                (pm1.meta_value <= %s AND pm2.meta_value >= %s) OR
                (pm1.meta_value >= %s AND pm1.meta_value <= %s)
            )
            ORDER BY pm1.meta_value ASC
        ", $slip_id, $end_date, $start_date, $start_date, $end_date, $start_date, $end_date));
        
        $formatted_reservations = array();
        
        foreach ($reservations as $reservation) {
            $boat_name = '';
            if ($reservation->boat_id) {
                $boat_name = get_post_meta($reservation->boat_id, '_mm_boat_name', true);
            }
            
            $formatted_reservations[] = array(
                'id' => $reservation->ID,
                'start_date' => $reservation->start_date,
                'end_date' => $reservation->end_date,
                'boat_name' => $boat_name,
                'type' => $reservation->reservation_type ?: 'transient'
            );
        }
        
        return $formatted_reservations;
    }
    
    /**
     * AJAX: Check slip availability
     */
    public static function ajax_check_availability() {
        check_ajax_referer('mm_public_nonce', 'nonce');
        
        $start_date = sanitize_text_field($_POST['start_date']);
        $end_date = sanitize_text_field($_POST['end_date']);
        $boat_length = floatval($_POST['boat_length']);
        $slip_type = sanitize_text_field($_POST['slip_type']);
        
        if (!$start_date || !$end_date) {
            wp_send_json_error(__('Please provide both start and end dates', 'marina-manager'));
        }
        
        $available_slips = self::check_availability($start_date, $end_date, $boat_length, $slip_type);
        
        wp_send_json_success($available_slips);
    }
    
    /**
     * AJAX: Assign boat to slip
     */
    public static function ajax_assign_boat() {
        check_ajax_referer('mm_admin_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(__('Insufficient permissions', 'marina-manager'));
        }
        
        $slip_id = intval($_POST['slip_id']);
        $boat_id = intval($_POST['boat_id']);
        $start_date = sanitize_text_field($_POST['start_date']);
        $end_date = sanitize_text_field($_POST['end_date']);
        $reservation_type = sanitize_text_field($_POST['reservation_type']);
        
        $reservation_id = self::create_reservation($slip_id, $boat_id, $start_date, $end_date, $reservation_type);
        
        if (is_wp_error($reservation_id)) {
            wp_send_json_error($reservation_id->get_error_message());
        }
        
        wp_send_json_success(array(
            'reservation_id' => $reservation_id,
            'message' => __('Boat assigned to slip successfully', 'marina-manager')
        ));
    }
    
    /**
     * AJAX: Update slip status
     */
    public static function ajax_update_slip_status() {
        check_ajax_referer('mm_admin_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(__('Insufficient permissions', 'marina-manager'));
        }
        
        $slip_id = intval($_POST['slip_id']);
        $new_status = sanitize_text_field($_POST['status']);
        $notes = sanitize_textarea_field($_POST['notes']);
        
        $result = self::update_slip_status($slip_id, $new_status, $notes);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success(array(
            'message' => __('Slip status updated successfully', 'marina-manager'),
            'new_status' => $new_status
        ));
    }
}