<?php
/**
 * StorageFlow Unit Manager - Core unit management and availability system
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class SF_Unit_Manager {
    
    public static function init() {
        add_action('wp_ajax_sf_check_unit_availability', array(__CLASS__, 'ajax_check_availability'));
        add_action('wp_ajax_nopriv_sf_check_unit_availability', array(__CLASS__, 'ajax_check_availability'));
        add_action('wp_ajax_sf_assign_tenant', array(__CLASS__, 'ajax_assign_tenant'));
        add_action('wp_ajax_sf_update_unit_status', array(__CLASS__, 'ajax_update_unit_status'));
    }
    
    /**
     * Check unit availability for specific size and features
     */
    public static function check_availability($unit_size = 'any', $features = array(), $move_in_date = null) {
        global $wpdb;
        
        $storage_units = get_posts(array(
            'post_type' => 'sf_unit',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_sf_unit_status',
                    'value' => array('available', 'vacant_clean'),
                    'compare' => 'IN'
                )
            )
        ));
        
        $available_units = array();
        
        foreach ($storage_units as $unit) {
            $unit_id = $unit->ID;
            $unit_size_meta = get_post_meta($unit_id, '_sf_unit_size', true);
            $unit_width = (float)get_post_meta($unit_id, '_sf_unit_width', true);
            $unit_length = (float)get_post_meta($unit_id, '_sf_unit_length', true);
            $unit_features = get_post_meta($unit_id, '_sf_unit_features', true);
            $monthly_rate = (float)get_post_meta($unit_id, '_sf_monthly_rate', true);
            $unit_type = get_post_meta($unit_id, '_sf_unit_type', true);
            
            // Check size compatibility
            if ($unit_size !== 'any' && $unit_size_meta !== $unit_size) {
                continue;
            }
            
            // Check features compatibility
            if (!empty($features)) {
                $unit_features_array = $unit_features ? explode(',', $unit_features) : array();
                $has_required_features = array_intersect($features, $unit_features_array);
                
                if (count($has_required_features) < count($features)) {
                    continue;
                }
            }
            
            // Check for active rentals if move-in date specified
            if ($move_in_date) {
                $has_conflict = self::check_unit_rental_conflicts($unit_id, $move_in_date);
                if ($has_conflict) {
                    continue;
                }
            }
            
            $available_units[] = array(
                'unit_id' => $unit_id,
                'unit_number' => get_post_meta($unit_id, '_sf_unit_number', true),
                'building' => get_post_meta($unit_id, '_sf_building', true),
                'floor' => get_post_meta($unit_id, '_sf_floor', true),
                'size' => $unit_size_meta,
                'width' => $unit_width,
                'length' => $unit_length,
                'square_feet' => $unit_width * $unit_length,
                'type' => $unit_type,
                'monthly_rate' => $monthly_rate,
                'features' => $unit_features_array,
                'climate_controlled' => in_array('climate_controlled', $unit_features_array),
                'ground_floor' => get_post_meta($unit_id, '_sf_ground_floor', true) === 'yes',
                'drive_up_access' => in_array('drive_up', $unit_features_array)
            );
        }
        
        // Sort by price (lowest first)
        usort($available_units, function($a, $b) {
            return $a['monthly_rate'] - $b['monthly_rate'];
        });
        
        return $available_units;
    }
    
    /**
     * Check for unit rental conflicts
     */
    private static function check_unit_rental_conflicts($unit_id, $move_in_date) {
        global $wpdb;
        
        $move_in_datetime = date('Y-m-d 00:00:00', strtotime($move_in_date));
        
        $conflicts = $wpdb->get_results($wpdb->prepare("
            SELECT COUNT(*) as conflict_count
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_sf_unit_id'
            INNER JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_sf_move_in_date'
            LEFT JOIN {$wpdb->postmeta} pm3 ON p.ID = pm3.post_id AND pm3.meta_key = '_sf_move_out_date'
            WHERE p.post_type = 'sf_rental'
            AND p.post_status IN ('sf_active', 'sf_pending')
            AND pm1.meta_value = %s
            AND pm2.meta_value <= %s
            AND (pm3.meta_value IS NULL OR pm3.meta_value >= %s)
        ", $unit_id, $move_in_datetime, $move_in_datetime));
        
        return $conflicts[0]->conflict_count > 0;
    }
    
    /**
     * Create rental agreement
     */
    public static function create_rental($unit_id, $tenant_id, $rental_data) {
        $unit = get_post($unit_id);
        $tenant = get_post($tenant_id);
        
        if (!$unit || !$tenant) {
            return new WP_Error('invalid_data', __('Invalid unit or tenant ID', 'storageflow'));
        }
        
        // Check availability one more time
        if (self::check_unit_rental_conflicts($unit_id, $rental_data['move_in_date'])) {
            return new WP_Error('unit_unavailable', __('Unit is no longer available for the selected move-in date', 'storageflow'));
        }
        
        $tenant_name = get_post_meta($tenant_id, '_sf_tenant_name', true);
        $unit_number = get_post_meta($unit_id, '_sf_unit_number', true);
        
        $monthly_rate = (float)get_post_meta($unit_id, '_sf_monthly_rate', true);
        $security_deposit = isset($rental_data['security_deposit']) ? (float)$rental_data['security_deposit'] : $monthly_rate;
        $admin_fee = isset($rental_data['admin_fee']) ? (float)$rental_data['admin_fee'] : 25.00;
        
        $rental_agreement = array(
            'post_title' => sprintf('%s - Unit %s - %s',
                $tenant_name,
                $unit_number,
                date('M j, Y', strtotime($rental_data['move_in_date']))
            ),
            'post_type' => 'sf_rental',
            'post_status' => 'sf_pending',
            'meta_input' => array(
                '_sf_unit_id' => $unit_id,
                '_sf_tenant_id' => $tenant_id,
                '_sf_move_in_date' => date('Y-m-d', strtotime($rental_data['move_in_date'])),
                '_sf_rental_term' => isset($rental_data['rental_term']) ? $rental_data['rental_term'] : 'month_to_month',
                '_sf_monthly_rate' => $monthly_rate,
                '_sf_security_deposit' => $security_deposit,
                '_sf_admin_fee' => $admin_fee,
                '_sf_payment_due_date' => isset($rental_data['payment_due_date']) ? intval($rental_data['payment_due_date']) : 1,
                '_sf_late_fee_amount' => isset($rental_data['late_fee']) ? (float)$rental_data['late_fee'] : 25.00,
                '_sf_late_fee_grace_days' => isset($rental_data['grace_days']) ? intval($rental_data['grace_days']) : 5,
                '_sf_access_code' => self::generate_access_code(),
                '_sf_gate_code' => isset($rental_data['gate_code']) ? $rental_data['gate_code'] : self::generate_gate_code(),
                '_sf_emergency_contact' => isset($rental_data['emergency_contact']) ? sanitize_text_field($rental_data['emergency_contact']) : '',
                '_sf_emergency_phone' => isset($rental_data['emergency_phone']) ? sanitize_text_field($rental_data['emergency_phone']) : '',
                '_sf_insurance_required' => isset($rental_data['insurance_required']) ? $rental_data['insurance_required'] : 'yes',
                '_sf_insurance_company' => isset($rental_data['insurance_company']) ? sanitize_text_field($rental_data['insurance_company']) : '',
                '_sf_special_terms' => isset($rental_data['special_terms']) ? sanitize_textarea_field($rental_data['special_terms']) : '',
                '_sf_created_by' => get_current_user_id(),
                '_sf_total_move_in_cost' => $monthly_rate + $security_deposit + $admin_fee
            )
        );
        
        $rental_id = wp_insert_post($rental_agreement);
        
        if ($rental_id) {
            // Update unit status
            update_post_meta($unit_id, '_sf_unit_status', 'rented');
            update_post_meta($unit_id, '_sf_current_tenant_id', $tenant_id);
            update_post_meta($unit_id, '_sf_current_rental_id', $rental_id);
            
            // Generate rental agreement hash for public access
            $rental_hash = wp_generate_password(32, false);
            update_post_meta($rental_id, '_sf_rental_hash', $rental_hash);
            
            // Generate account number
            $account_number = 'SF-' . date('Y') . '-' . str_pad($rental_id, 4, '0', STR_PAD_LEFT);
            update_post_meta($rental_id, '_sf_account_number', $account_number);
            
            return $rental_id;
        }
        
        return new WP_Error('rental_failed', __('Failed to create rental agreement', 'storageflow'));
    }
    
    /**
     * Generate unique access code
     */
    private static function generate_access_code() {
        do {
            $access_code = str_pad(mt_rand(1000, 9999), 4, '0', STR_PAD_LEFT);
            
            // Check if code is already in use
            $existing = get_posts(array(
                'post_type' => 'sf_rental',
                'post_status' => array('sf_active', 'sf_pending'),
                'meta_query' => array(
                    array(
                        'key' => '_sf_access_code',
                        'value' => $access_code,
                        'compare' => '='
                    )
                ),
                'posts_per_page' => 1
            ));
            
        } while (!empty($existing));
        
        return $access_code;
    }
    
    /**
     * Generate gate access code
     */
    private static function generate_gate_code() {
        $facility_code = get_option('sf_facility_gate_code', '1234');
        return $facility_code; // In pro version, this would be dynamic per tenant
    }
    
    /**
     * Get unit occupancy overview
     */
    public static function get_facility_occupancy_overview() {
        $units = get_posts(array(
            'post_type' => 'sf_unit',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_sf_unit_status',
                    'compare' => 'EXISTS'
                )
            )
        ));
        
        $overview = array(
            'total_units' => count($units),
            'available' => 0,
            'rented' => 0,
            'maintenance' => 0,
            'vacant_dirty' => 0,
            'reserved' => 0,
            'total_revenue' => 0,
            'occupancy_rate' => 0,
            'average_rate_per_sqft' => 0
        );
        
        $total_square_feet = 0;
        $total_potential_revenue = 0;
        
        foreach ($units as $unit) {
            $status = get_post_meta($unit->ID, '_sf_unit_status', true);
            $width = (float)get_post_meta($unit->ID, '_sf_unit_width', true);
            $length = (float)get_post_meta($unit->ID, '_sf_unit_length', true);
            $monthly_rate = (float)get_post_meta($unit->ID, '_sf_monthly_rate', true);
            
            $unit_sqft = $width * $length;
            $total_square_feet += $unit_sqft;
            $total_potential_revenue += $monthly_rate;
            
            switch ($status) {
                case 'available':
                case 'vacant_clean':
                    $overview['available']++;
                    break;
                case 'rented':
                    $overview['rented']++;
                    $overview['total_revenue'] += $monthly_rate;
                    break;
                case 'maintenance':
                    $overview['maintenance']++;
                    break;
                case 'vacant_dirty':
                    $overview['vacant_dirty']++;
                    break;
                case 'reserved':
                    $overview['reserved']++;
                    $overview['total_revenue'] += $monthly_rate;
                    break;
            }
        }
        
        // Calculate occupancy rate
        $occupied_count = $overview['rented'] + $overview['reserved'];
        $overview['occupancy_rate'] = $overview['total_units'] > 0 ? 
            round(($occupied_count / $overview['total_units']) * 100, 1) : 0;
        
        // Calculate average rate per square foot
        $overview['average_rate_per_sqft'] = $total_square_feet > 0 ? 
            round($total_potential_revenue / $total_square_feet, 2) : 0;
        
        // Calculate additional metrics
        $overview['revenue_efficiency'] = $total_potential_revenue > 0 ? 
            round(($overview['total_revenue'] / $total_potential_revenue) * 100, 1) : 0;
        
        return $overview;
    }
    
    /**
     * Get unit details with current status
     */
    public static function get_unit_details($unit_id) {
        $unit = get_post($unit_id);
        if (!$unit) return false;
        
        $unit_data = array(
            'id' => $unit_id,
            'number' => get_post_meta($unit_id, '_sf_unit_number', true),
            'building' => get_post_meta($unit_id, '_sf_building', true),
            'floor' => get_post_meta($unit_id, '_sf_floor', true),
            'size' => get_post_meta($unit_id, '_sf_unit_size', true),
            'width' => get_post_meta($unit_id, '_sf_unit_width', true),
            'length' => get_post_meta($unit_id, '_sf_unit_length', true),
            'type' => get_post_meta($unit_id, '_sf_unit_type', true),
            'status' => get_post_meta($unit_id, '_sf_unit_status', true),
            'monthly_rate' => get_post_meta($unit_id, '_sf_monthly_rate', true),
            'features' => get_post_meta($unit_id, '_sf_unit_features', true),
            'climate_controlled' => get_post_meta($unit_id, '_sf_climate_controlled', true) === 'yes',
            'ground_floor' => get_post_meta($unit_id, '_sf_ground_floor', true) === 'yes',
            'current_tenant' => null,
            'current_rental' => null,
            'maintenance_notes' => get_post_meta($unit_id, '_sf_maintenance_notes', true),
            'last_cleaned' => get_post_meta($unit_id, '_sf_last_cleaned', true),
            'last_inspected' => get_post_meta($unit_id, '_sf_last_inspected', true)
        );
        
        // Get current tenant if rented
        $current_tenant_id = get_post_meta($unit_id, '_sf_current_tenant_id', true);
        if ($current_tenant_id) {
            $tenant = get_post($current_tenant_id);
            if ($tenant) {
                $unit_data['current_tenant'] = array(
                    'id' => $current_tenant_id,
                    'name' => get_post_meta($current_tenant_id, '_sf_tenant_name', true),
                    'email' => get_post_meta($current_tenant_id, '_sf_tenant_email', true),
                    'phone' => get_post_meta($current_tenant_id, '_sf_tenant_phone', true),
                    'move_in_date' => get_post_meta($current_tenant_id, '_sf_move_in_date', true)
                );
            }
        }
        
        // Get current active rental
        $current_rental = self::get_current_rental($unit_id);
        if ($current_rental) {
            $unit_data['current_rental'] = $current_rental;
        }
        
        return $unit_data;
    }
    
    /**
     * Get current active rental for unit
     */
    private static function get_current_rental($unit_id) {
        $rentals = get_posts(array(
            'post_type' => 'sf_rental',
            'post_status' => array('sf_active', 'sf_pending'),
            'meta_query' => array(
                array(
                    'key' => '_sf_unit_id',
                    'value' => $unit_id,
                    'compare' => '='
                )
            ),
            'posts_per_page' => 1,
            'orderby' => 'date',
            'order' => 'DESC'
        ));
        
        if ($rentals) {
            $rental = $rentals[0];
            return array(
                'id' => $rental->ID,
                'move_in_date' => get_post_meta($rental->ID, '_sf_move_in_date', true),
                'move_out_date' => get_post_meta($rental->ID, '_sf_move_out_date', true),
                'monthly_rate' => get_post_meta($rental->ID, '_sf_monthly_rate', true),
                'security_deposit' => get_post_meta($rental->ID, '_sf_security_deposit', true),
                'payment_status' => get_post_meta($rental->ID, '_sf_payment_status', true),
                'balance_due' => get_post_meta($rental->ID, '_sf_balance_due', true),
                'access_code' => get_post_meta($rental->ID, '_sf_access_code', true),
                'account_number' => get_post_meta($rental->ID, '_sf_account_number', true)
            );
        }
        
        return null;
    }
    
    /**
     * Update unit status
     */
    public static function update_unit_status($unit_id, $new_status, $notes = '') {
        $valid_statuses = array('available', 'rented', 'maintenance', 'vacant_dirty', 'vacant_clean', 'reserved', 'out_of_service');
        
        if (!in_array($new_status, $valid_statuses)) {
            return new WP_Error('invalid_status', __('Invalid unit status', 'storageflow'));
        }
        
        $updated = update_post_meta($unit_id, '_sf_unit_status', $new_status);
        
        if ($notes) {
            update_post_meta($unit_id, '_sf_status_notes', $notes);
        }
        
        // Clear current tenant if changing to available or maintenance
        if (in_array($new_status, array('available', 'maintenance', 'vacant_dirty', 'vacant_clean', 'out_of_service'))) {
            delete_post_meta($unit_id, '_sf_current_tenant_id');
            delete_post_meta($unit_id, '_sf_current_rental_id');
        }
        
        // Update timestamps
        if ($new_status === 'vacant_clean') {
            update_post_meta($unit_id, '_sf_last_cleaned', current_time('mysql'));
        }
        
        if (in_array($new_status, array('available', 'vacant_clean'))) {
            update_post_meta($unit_id, '_sf_last_inspected', current_time('mysql'));
        }
        
        // Log status change
        self::log_unit_activity($unit_id, 'status_change', array(
            'new_status' => $new_status,
            'notes' => $notes,
            'changed_by' => get_current_user_id()
        ));
        
        return $updated;
    }
    
    /**
     * Log unit activity
     */
    private static function log_unit_activity($unit_id, $activity_type, $data = array()) {
        $log_entry = array(
            'post_title' => sprintf('Unit %s - %s - %s',
                get_post_meta($unit_id, '_sf_unit_number', true),
                ucfirst(str_replace('_', ' ', $activity_type)),
                current_time('mysql')
            ),
            'post_type' => 'sf_activity_log',
            'post_status' => 'publish',
            'meta_input' => array_merge(array(
                '_sf_unit_id' => $unit_id,
                '_sf_activity_type' => $activity_type,
                '_sf_timestamp' => current_time('mysql')
            ), $data)
        );
        
        wp_insert_post($log_entry);
    }
    
    /**
     * Get units by size category
     */
    public static function get_units_by_size($include_rented = false) {
        $status_query = $include_rented ? 
            array('available', 'rented', 'vacant_clean') : 
            array('available', 'vacant_clean');
            
        $units = get_posts(array(
            'post_type' => 'sf_unit',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_sf_unit_status',
                    'value' => $status_query,
                    'compare' => 'IN'
                )
            )
        ));
        
        $size_categories = array();
        
        foreach ($units as $unit) {
            $size = get_post_meta($unit->ID, '_sf_unit_size', true);
            $monthly_rate = (float)get_post_meta($unit->ID, '_sf_monthly_rate', true);
            $status = get_post_meta($unit->ID, '_sf_unit_status', true);
            
            if (!isset($size_categories[$size])) {
                $size_categories[$size] = array(
                    'size' => $size,
                    'total_units' => 0,
                    'available_units' => 0,
                    'rented_units' => 0,
                    'lowest_rate' => $monthly_rate,
                    'highest_rate' => $monthly_rate,
                    'average_rate' => 0,
                    'units' => array()
                );
            }
            
            $size_categories[$size]['total_units']++;
            
            if (in_array($status, array('available', 'vacant_clean'))) {
                $size_categories[$size]['available_units']++;
            } elseif ($status === 'rented') {
                $size_categories[$size]['rented_units']++;
            }
            
            $size_categories[$size]['lowest_rate'] = min($size_categories[$size]['lowest_rate'], $monthly_rate);
            $size_categories[$size]['highest_rate'] = max($size_categories[$size]['highest_rate'], $monthly_rate);
            
            $size_categories[$size]['units'][] = array(
                'unit_id' => $unit->ID,
                'unit_number' => get_post_meta($unit->ID, '_sf_unit_number', true),
                'monthly_rate' => $monthly_rate,
                'status' => $status
            );
        }
        
        // Calculate average rates
        foreach ($size_categories as $size => &$category) {
            $total_rate = array_sum(array_column($category['units'], 'monthly_rate'));
            $category['average_rate'] = $category['total_units'] > 0 ? 
                round($total_rate / $category['total_units'], 2) : 0;
        }
        
        return $size_categories;
    }
    
    /**
     * AJAX: Check unit availability
     */
    public static function ajax_check_availability() {
        check_ajax_referer('sf_public_nonce', 'nonce');
        
        $unit_size = sanitize_text_field($_POST['unit_size']);
        $features = isset($_POST['features']) ? array_map('sanitize_text_field', $_POST['features']) : array();
        $move_in_date = sanitize_text_field($_POST['move_in_date']);
        
        $available_units = self::check_availability($unit_size, $features, $move_in_date);
        
        wp_send_json_success($available_units);
    }
    
    /**
     * AJAX: Assign tenant to unit
     */
    public static function ajax_assign_tenant() {
        check_ajax_referer('sf_admin_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(__('Insufficient permissions', 'storageflow'));
        }
        
        $unit_id = intval($_POST['unit_id']);
        $tenant_id = intval($_POST['tenant_id']);
        $rental_data = array(
            'move_in_date' => sanitize_text_field($_POST['move_in_date']),
            'rental_term' => sanitize_text_field($_POST['rental_term']),
            'security_deposit' => floatval($_POST['security_deposit']),
            'admin_fee' => floatval($_POST['admin_fee'])
        );
        
        $rental_id = self::create_rental($unit_id, $tenant_id, $rental_data);
        
        if (is_wp_error($rental_id)) {
            wp_send_json_error($rental_id->get_error_message());
        }
        
        wp_send_json_success(array(
            'rental_id' => $rental_id,
            'message' => __('Tenant assigned to unit successfully', 'storageflow')
        ));
    }
    
    /**
     * AJAX: Update unit status
     */
    public static function ajax_update_unit_status() {
        check_ajax_referer('sf_admin_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(__('Insufficient permissions', 'storageflow'));
        }
        
        $unit_id = intval($_POST['unit_id']);
        $new_status = sanitize_text_field($_POST['status']);
        $notes = sanitize_textarea_field($_POST['notes']);
        
        $result = self::update_unit_status($unit_id, $new_status, $notes);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success(array(
            'message' => __('Unit status updated successfully', 'storageflow'),
            'new_status' => $new_status
        ));
    }
}