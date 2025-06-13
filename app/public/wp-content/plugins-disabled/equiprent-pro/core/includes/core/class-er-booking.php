<?php
/**
 * EquipRent Pro Booking Management
 *
 * @package EquipRent_Pro
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Core booking management class
 */
class ER_Booking {

    /**
     * Booking post type
     */
    const POST_TYPE = 'er_booking';

    /**
     * Initialize the class
     */
    public static function init() {
        add_action('save_post_er_booking', array(__CLASS__, 'save_booking_meta'), 10, 2);
        add_action('wp_ajax_create_booking', array(__CLASS__, 'ajax_create_booking'));
        add_action('wp_ajax_update_booking_status', array(__CLASS__, 'ajax_update_status'));
        add_action('transition_post_status', array(__CLASS__, 'handle_status_change'), 10, 3);
        add_filter('manage_er_booking_posts_columns', array(__CLASS__, 'admin_columns'));
        add_action('manage_er_booking_posts_custom_column', array(__CLASS__, 'admin_column_content'), 10, 2);
    }

    /**
     * Create new booking
     */
    public static function create($data = array()) {
        // Validate required fields
        $required_fields = array('customer_id', 'start_date', 'end_date', 'equipment_items');
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                return new WP_Error('missing_' . $field, sprintf(__('%s is required.', 'equiprent-pro'), str_replace('_', ' ', $field)));
            }
        }

        // Validate customer exists
        if (!ER_Customer::exists($data['customer_id'])) {
            return new WP_Error('invalid_customer', __('Invalid customer ID.', 'equiprent-pro'));
        }

        // Validate dates
        if (strtotime($data['start_date']) >= strtotime($data['end_date'])) {
            return new WP_Error('invalid_dates', __('End date must be after start date.', 'equiprent-pro'));
        }

        // Validate equipment availability
        foreach ($data['equipment_items'] as $item) {
            $availability = ER_Equipment::get_availability($item['equipment_id'], $data['start_date'], $data['end_date']);
            if (!$availability['available'] || $availability['quantity'] < $item['quantity']) {
                $equipment = ER_Equipment::get($item['equipment_id']);
                return new WP_Error('equipment_unavailable', sprintf(
                    __('Equipment "%s" is not available for the selected dates.', 'equiprent-pro'),
                    $equipment['title']
                ));
            }
        }

        // Sanitize data
        $sanitized_data = self::sanitize_booking_data($data);

        // Generate booking number
        $booking_number = ER_Utilities::generate_booking_number();

        // Calculate totals
        $totals = self::calculate_totals($sanitized_data['equipment_items'], $sanitized_data['start_date'], $sanitized_data['end_date']);

        // Create post
        $post_data = array(
            'post_type' => self::POST_TYPE,
            'post_title' => sprintf(__('Booking %s', 'equiprent-pro'), $booking_number),
            'post_status' => 'publish',
        );

        $booking_id = wp_insert_post($post_data);

        if (is_wp_error($booking_id)) {
            return $booking_id;
        }

        // Add calculated data
        $sanitized_data['booking_number'] = $booking_number;
        $sanitized_data['subtotal'] = $totals['subtotal'];
        $sanitized_data['tax_amount'] = $totals['tax_amount'];
        $sanitized_data['total_amount'] = $totals['total_amount'];
        $sanitized_data['booking_status'] = $sanitized_data['booking_status'] ?? 'pending';

        // Save meta data
        self::save_booking_meta($booking_id, $sanitized_data);

        // Insert into database table
        self::insert_booking_record($booking_id, $sanitized_data);

        // Insert booking items
        self::insert_booking_items($booking_id, $sanitized_data['equipment_items'], $sanitized_data['start_date'], $sanitized_data['end_date']);

        // Send notification email
        self::send_booking_notification($booking_id, 'created');

        // Log activity
        ER_Utilities::log('booking_created', 'booking', $booking_id, $sanitized_data);

        return $booking_id;
    }

    /**
     * Update booking
     */
    public static function update($booking_id, $data = array()) {
        if (!self::exists($booking_id)) {
            return new WP_Error('booking_not_found', __('Booking not found.', 'equiprent-pro'));
        }

        // Get current booking data
        $current_booking = self::get($booking_id);

        // Sanitize new data
        $sanitized_data = self::sanitize_booking_data($data);

        // If dates or equipment changed, validate availability
        if (isset($sanitized_data['start_date']) || isset($sanitized_data['end_date']) || isset($sanitized_data['equipment_items'])) {
            $start_date = $sanitized_data['start_date'] ?? $current_booking['start_date'];
            $end_date = $sanitized_data['end_date'] ?? $current_booking['end_date'];
            $equipment_items = $sanitized_data['equipment_items'] ?? $current_booking['equipment_items'];

            foreach ($equipment_items as $item) {
                $availability = ER_Equipment::get_availability($item['equipment_id'], $start_date, $end_date, $booking_id);
                if (!$availability['available'] || $availability['quantity'] < $item['quantity']) {
                    $equipment = ER_Equipment::get($item['equipment_id']);
                    return new WP_Error('equipment_unavailable', sprintf(
                        __('Equipment "%s" is not available for the selected dates.', 'equiprent-pro'),
                        $equipment['title']
                    ));
                }
            }

            // Recalculate totals if needed
            if (isset($sanitized_data['equipment_items']) || isset($sanitized_data['start_date']) || isset($sanitized_data['end_date'])) {
                $totals = self::calculate_totals($equipment_items, $start_date, $end_date);
                $sanitized_data['subtotal'] = $totals['subtotal'];
                $sanitized_data['tax_amount'] = $totals['tax_amount'];
                $sanitized_data['total_amount'] = $totals['total_amount'];
            }
        }

        // Save meta data
        self::save_booking_meta($booking_id, $sanitized_data);

        // Update database record
        self::update_booking_record($booking_id, $sanitized_data);

        // Update booking items if needed
        if (isset($sanitized_data['equipment_items'])) {
            self::delete_booking_items($booking_id);
            self::insert_booking_items($booking_id, $sanitized_data['equipment_items'], 
                $sanitized_data['start_date'] ?? $current_booking['start_date'], 
                $sanitized_data['end_date'] ?? $current_booking['end_date']);
        }

        // Log activity
        ER_Utilities::log('booking_updated', 'booking', $booking_id, $sanitized_data);

        return $booking_id;
    }

    /**
     * Delete booking
     */
    public static function delete($booking_id, $force = false) {
        if (!self::exists($booking_id)) {
            return new WP_Error('booking_not_found', __('Booking not found.', 'equiprent-pro'));
        }

        $booking = self::get($booking_id);

        // Check if booking can be deleted
        if (in_array($booking['booking_status'], array('active', 'completed')) && !$force) {
            return new WP_Error('booking_cannot_delete', __('Cannot delete active or completed bookings.', 'equiprent-pro'));
        }

        // Delete booking items
        self::delete_booking_items($booking_id);

        // Delete database record
        self::delete_booking_record($booking_id);

        // Delete the post
        $result = wp_delete_post($booking_id, $force);

        if ($result) {
            // Log activity
            ER_Utilities::log('booking_deleted', 'booking', $booking_id);
        }

        return $result;
    }

    /**
     * Get booking data
     */
    public static function get($booking_id) {
        if (!self::exists($booking_id)) {
            return null;
        }

        $post = get_post($booking_id);
        $meta = get_post_meta($booking_id);

        // Build booking data
        $booking = array(
            'id' => $booking_id,
            'title' => $post->post_title,
            'status' => $post->post_status,
            'created_at' => $post->post_date,
            'updated_at' => $post->post_modified,
        );

        // Add meta fields
        $meta_fields = array(
            'booking_number', 'customer_id', 'start_date', 'end_date', 
            'booking_status', 'payment_status', 'subtotal', 'tax_amount', 
            'total_amount', 'paid_amount', 'delivery_address', 'pickup_address',
            'delivery_date', 'pickup_date', 'notes', 'internal_notes'
        );

        foreach ($meta_fields as $field) {
            $meta_key = '_' . $field;
            $booking[$field] = isset($meta[$meta_key][0]) ? maybe_unserialize($meta[$meta_key][0]) : '';
        }

        // Get customer data
        if ($booking['customer_id']) {
            $booking['customer'] = ER_Customer::get($booking['customer_id']);
        }

        // Get booking items
        $booking['equipment_items'] = self::get_booking_items($booking_id);

        return $booking;
    }

    /**
     * Get booking list with filters
     */
    public static function get_list($args = array()) {
        $defaults = array(
            'posts_per_page' => 20,
            'post_type' => self::POST_TYPE,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
            'meta_query' => array(),
        );

        $args = wp_parse_args($args, $defaults);

        // Handle search by booking number or customer
        if (!empty($args['search'])) {
            $args['meta_query'][] = array(
                'relation' => 'OR',
                array(
                    'key' => '_booking_number',
                    'value' => $args['search'],
                    'compare' => 'LIKE',
                ),
                array(
                    'key' => '_customer_id',
                    'value' => $args['search'],
                    'compare' => '=',
                ),
            );
            unset($args['search']);
        }

        // Handle status filter
        if (!empty($args['booking_status'])) {
            $args['meta_query'][] = array(
                'key' => '_booking_status',
                'value' => $args['booking_status'],
                'compare' => '=',
            );
            unset($args['booking_status']);
        }

        // Handle date range filter
        if (!empty($args['date_from']) && !empty($args['date_to'])) {
            $args['meta_query'][] = array(
                'relation' => 'AND',
                array(
                    'key' => '_start_date',
                    'value' => $args['date_from'],
                    'compare' => '>=',
                    'type' => 'DATE',
                ),
                array(
                    'key' => '_end_date',
                    'value' => $args['date_to'],
                    'compare' => '<=',
                    'type' => 'DATE',
                ),
            );
            unset($args['date_from'], $args['date_to']);
        }

        $query = new WP_Query($args);
        $booking_list = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $booking_list[] = self::get(get_the_ID());
            }
            wp_reset_postdata();
        }

        return array(
            'items' => $booking_list,
            'total' => $query->found_posts,
            'pages' => $query->max_num_pages,
        );
    }

    /**
     * Update booking status
     */
    public static function update_status($booking_id, $new_status) {
        if (!self::exists($booking_id)) {
            return new WP_Error('booking_not_found', __('Booking not found.', 'equiprent-pro'));
        }

        $valid_statuses = array_keys(ER_Post_Types::get_booking_statuses());
        if (!in_array($new_status, $valid_statuses)) {
            return new WP_Error('invalid_status', __('Invalid booking status.', 'equiprent-pro'));
        }

        $old_status = get_post_meta($booking_id, '_booking_status', true);
        
        update_post_meta($booking_id, '_booking_status', $new_status);
        self::update_booking_record($booking_id, array('status' => $new_status));

        // Send notification
        self::send_booking_notification($booking_id, 'status_changed');

        // Log activity
        ER_Utilities::log('booking_status_changed', 'booking', $booking_id, array(
            'old_status' => $old_status,
            'new_status' => $new_status
        ));

        return true;
    }

    /**
     * Check if booking exists
     */
    public static function exists($booking_id) {
        $post = get_post($booking_id);
        return $post && $post->post_type === self::POST_TYPE;
    }

    /**
     * Calculate booking totals
     */
    public static function calculate_totals($equipment_items, $start_date, $end_date) {
        $subtotal = 0;

        foreach ($equipment_items as $item) {
            $cost = ER_Equipment::calculate_cost($item['equipment_id'], $start_date, $end_date, $item['quantity']);
            $subtotal += $cost;
        }

        $tax_rate = (float) ER_Utilities::get_setting('tax_rate', 0) / 100;
        $tax_amount = $subtotal * $tax_rate;
        $total_amount = $subtotal + $tax_amount;

        return array(
            'subtotal' => $subtotal,
            'tax_amount' => $tax_amount,
            'total_amount' => $total_amount,
        );
    }

    /**
     * Get booking items
     */
    public static function get_booking_items($booking_id) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'er_booking_items';
        
        $items = $wpdb->get_results($wpdb->prepare("
            SELECT * FROM {$table} WHERE booking_id = %d ORDER BY id ASC
        ", $booking_id), ARRAY_A);

        if (!$items) {
            return array();
        }

        // Add equipment data to each item
        foreach ($items as &$item) {
            $equipment = ER_Equipment::get($item['equipment_id']);
            $item['equipment'] = $equipment;
            $item['equipment_name'] = $equipment ? $equipment['title'] : __('Deleted Equipment', 'equiprent-pro');
        }

        return $items;
    }

    /**
     * Insert booking record into database
     */
    private static function insert_booking_record($booking_id, $data) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'er_bookings';
        
        $wpdb->insert(
            $table,
            array(
                'post_id' => $booking_id,
                'booking_number' => $data['booking_number'],
                'customer_id' => $data['customer_id'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'status' => $data['booking_status'],
                'subtotal' => $data['subtotal'],
                'tax_amount' => $data['tax_amount'],
                'total_amount' => $data['total_amount'],
                'paid_amount' => $data['paid_amount'] ?? 0,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql'),
            ),
            array('%d', '%s', '%d', '%s', '%s', '%s', '%f', '%f', '%f', '%f', '%s', '%s')
        );
    }

    /**
     * Update booking record in database
     */
    private static function update_booking_record($booking_id, $data) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'er_bookings';
        
        $update_data = array('updated_at' => current_time('mysql'));
        $format = array('%s');

        $fields_map = array(
            'customer_id' => '%d',
            'start_date' => '%s',
            'end_date' => '%s',
            'booking_status' => '%s',
            'subtotal' => '%f',
            'tax_amount' => '%f',
            'total_amount' => '%f',
            'paid_amount' => '%f',
        );

        foreach ($fields_map as $field => $field_format) {
            if (isset($data[$field])) {
                $update_data[$field] = $data[$field];
                $format[] = $field_format;
            }
        }

        $wpdb->update(
            $table,
            $update_data,
            array('post_id' => $booking_id),
            $format,
            array('%d')
        );
    }

    /**
     * Delete booking record from database
     */
    private static function delete_booking_record($booking_id) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'er_bookings';
        
        $wpdb->delete(
            $table,
            array('post_id' => $booking_id),
            array('%d')
        );
    }

    /**
     * Insert booking items
     */
    private static function insert_booking_items($booking_id, $equipment_items, $start_date, $end_date) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'er_booking_items';

        foreach ($equipment_items as $item) {
            $cost = ER_Equipment::calculate_cost($item['equipment_id'], $start_date, $end_date, $item['quantity']);
            
            $wpdb->insert(
                $table,
                array(
                    'booking_id' => $booking_id,
                    'equipment_id' => $item['equipment_id'],
                    'quantity' => $item['quantity'],
                    'daily_rate' => $item['daily_rate'] ?? 0,
                    'total_cost' => $cost,
                    'created_at' => current_time('mysql'),
                ),
                array('%d', '%d', '%d', '%f', '%f', '%s')
            );
        }
    }

    /**
     * Delete booking items
     */
    private static function delete_booking_items($booking_id) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'er_booking_items';
        
        $wpdb->delete(
            $table,
            array('booking_id' => $booking_id),
            array('%d')
        );
    }

    /**
     * Send booking notification
     */
    private static function send_booking_notification($booking_id, $type = 'created') {
        if (!ER_Utilities::get_setting('email_notifications', true)) {
            return;
        }

        $booking = self::get($booking_id);
        if (!$booking || !$booking['customer']) {
            return;
        }

        $customer_email = $booking['customer']['email'];
        if (!$customer_email) {
            return;
        }

        $subject = '';
        $message = '';

        switch ($type) {
            case 'created':
                $subject = sprintf(__('Booking Confirmation - %s', 'equiprent-pro'), $booking['booking_number']);
                $message = sprintf(
                    __('Your booking %s has been created and is pending confirmation.', 'equiprent-pro'),
                    $booking['booking_number']
                );
                break;
                
            case 'status_changed':
                $subject = sprintf(__('Booking Status Update - %s', 'equiprent-pro'), $booking['booking_number']);
                $statuses = ER_Post_Types::get_booking_statuses();
                $status_label = $statuses[$booking['booking_status']] ?? $booking['booking_status'];
                $message = sprintf(
                    __('Your booking %s status has been updated to: %s', 'equiprent-pro'),
                    $booking['booking_number'],
                    $status_label
                );
                break;
        }

        if ($subject && $message) {
            ER_Utilities::send_notification($customer_email, $subject, $message);
        }
    }

    /**
     * Save booking meta data
     */
    public static function save_booking_meta($booking_id, $data = null) {
        // If called from save_post hook, get data from POST
        if (is_null($data) && isset($_POST['booking_meta_nonce'])) {
            if (!wp_verify_nonce($_POST['booking_meta_nonce'], 'save_booking_meta')) {
                return;
            }

            $data = $_POST;
        }

        if (!$data || !is_array($data)) {
            return;
        }

        $meta_fields = array(
            'booking_number' => 'sanitize_text_field',
            'customer_id' => 'intval',
            'start_date' => 'sanitize_text_field',
            'end_date' => 'sanitize_text_field',
            'booking_status' => 'sanitize_text_field',
            'payment_status' => 'sanitize_text_field',
            'subtotal' => 'floatval',
            'tax_amount' => 'floatval',
            'total_amount' => 'floatval',
            'paid_amount' => 'floatval',
            'delivery_address' => 'sanitize_textarea_field',
            'pickup_address' => 'sanitize_textarea_field',
            'delivery_date' => 'sanitize_text_field',
            'pickup_date' => 'sanitize_text_field',
            'notes' => 'sanitize_textarea_field',
            'internal_notes' => 'sanitize_textarea_field',
        );

        foreach ($meta_fields as $field => $sanitize_callback) {
            if (isset($data[$field])) {
                $value = call_user_func($sanitize_callback, $data[$field]);
                update_post_meta($booking_id, '_' . $field, $value);
            }
        }
    }

    /**
     * Sanitize booking data
     */
    private static function sanitize_booking_data($data) {
        $sanitized = array();

        $text_fields = array(
            'booking_number', 'start_date', 'end_date', 'booking_status', 
            'payment_status', 'delivery_date', 'pickup_date'
        );
        
        foreach ($text_fields as $field) {
            if (isset($data[$field])) {
                $sanitized[$field] = sanitize_text_field($data[$field]);
            }
        }

        $number_fields = array('subtotal', 'tax_amount', 'total_amount', 'paid_amount');
        foreach ($number_fields as $field) {
            if (isset($data[$field])) {
                $sanitized[$field] = floatval($data[$field]);
            }
        }

        $int_fields = array('customer_id');
        foreach ($int_fields as $field) {
            if (isset($data[$field])) {
                $sanitized[$field] = intval($data[$field]);
            }
        }

        $textarea_fields = array('delivery_address', 'pickup_address', 'notes', 'internal_notes');
        foreach ($textarea_fields as $field) {
            if (isset($data[$field])) {
                $sanitized[$field] = sanitize_textarea_field($data[$field]);
            }
        }

        // Sanitize equipment items
        if (isset($data['equipment_items']) && is_array($data['equipment_items'])) {
            $sanitized['equipment_items'] = array();
            foreach ($data['equipment_items'] as $item) {
                $sanitized['equipment_items'][] = array(
                    'equipment_id' => intval($item['equipment_id']),
                    'quantity' => intval($item['quantity']),
                    'daily_rate' => floatval($item['daily_rate'] ?? 0),
                );
            }
        }

        return $sanitized;
    }

    /**
     * AJAX handler for creating booking
     */
    public static function ajax_create_booking() {
        check_ajax_referer('equiprent_admin_nonce', 'nonce');

        if (!current_user_can('create_bookings')) {
            wp_send_json_error(__('Insufficient permissions.', 'equiprent-pro'));
        }

        $data = array(
            'customer_id' => intval($_POST['customer_id'] ?? 0),
            'start_date' => sanitize_text_field($_POST['start_date'] ?? ''),
            'end_date' => sanitize_text_field($_POST['end_date'] ?? ''),
            'equipment_items' => $_POST['equipment_items'] ?? array(),
            'notes' => sanitize_textarea_field($_POST['notes'] ?? ''),
        );

        $booking_id = self::create($data);

        if (is_wp_error($booking_id)) {
            wp_send_json_error($booking_id->get_error_message());
        }

        $booking = self::get($booking_id);
        wp_send_json_success(array(
            'booking_id' => $booking_id,
            'booking' => $booking,
            'message' => __('Booking created successfully.', 'equiprent-pro')
        ));
    }

    /**
     * AJAX handler for updating booking status
     */
    public static function ajax_update_status() {
        check_ajax_referer('equiprent_admin_nonce', 'nonce');

        if (!current_user_can('edit_bookings')) {
            wp_send_json_error(__('Insufficient permissions.', 'equiprent-pro'));
        }

        $booking_id = intval($_POST['booking_id'] ?? 0);
        $new_status = sanitize_text_field($_POST['status'] ?? '');

        $result = self::update_status($booking_id, $new_status);

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }

        wp_send_json_success(array(
            'message' => __('Booking status updated successfully.', 'equiprent-pro')
        ));
    }

    /**
     * Handle post status changes
     */
    public static function handle_status_change($new_status, $old_status, $post) {
        if ($post->post_type !== self::POST_TYPE) {
            return;
        }

        // Update booking status when post status changes
        if ($new_status === 'trash') {
            update_post_meta($post->ID, '_booking_status', 'cancelled');
            self::update_booking_record($post->ID, array('booking_status' => 'cancelled'));
        }
    }

    /**
     * Admin columns for booking list
     */
    public static function admin_columns($columns) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['booking_number'] = __('Booking #', 'equiprent-pro');
        $new_columns['customer'] = __('Customer', 'equiprent-pro');
        $new_columns['dates'] = __('Rental Period', 'equiprent-pro');
        $new_columns['total'] = __('Total', 'equiprent-pro');
        $new_columns['status'] = __('Status', 'equiprent-pro');
        $new_columns['date'] = $columns['date'];

        return $new_columns;
    }

    /**
     * Admin column content for booking list
     */
    public static function admin_column_content($column, $post_id) {
        $booking = self::get($post_id);
        
        switch ($column) {
            case 'booking_number':
                echo '<strong>' . esc_html($booking['booking_number']) . '</strong>';
                break;
                
            case 'customer':
                if ($booking['customer']) {
                    echo esc_html($booking['customer']['name']);
                } else {
                    echo '—';
                }
                break;
                
            case 'dates':
                if ($booking['start_date'] && $booking['end_date']) {
                    echo ER_Utilities::format_date($booking['start_date']) . ' - ' . ER_Utilities::format_date($booking['end_date']);
                } else {
                    echo '—';
                }
                break;
                
            case 'total':
                echo $booking['total_amount'] ? ER_Utilities::format_currency($booking['total_amount']) : '—';
                break;
                
            case 'status':
                $status = $booking['booking_status'] ?: 'pending';
                $statuses = ER_Post_Types::get_booking_statuses();
                $status_label = $statuses[$status] ?? $status;
                
                printf(
                    '<span class="booking-status status-%s">%s</span>',
                    esc_attr($status),
                    esc_html($status_label)
                );
                break;
        }
    }
}