<?php
/**
 * EquipRent Pro Customer Management
 *
 * @package EquipRent_Pro
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Core customer management class
 */
class ER_Customer {

    /**
     * Customer post type
     */
    const POST_TYPE = 'er_customer';

    /**
     * Initialize the class
     */
    public static function init() {
        add_action('save_post_er_customer', array(__CLASS__, 'save_customer_meta'), 10, 2);
        add_action('wp_ajax_search_customers', array(__CLASS__, 'ajax_search_customers'));
        add_action('wp_ajax_create_customer', array(__CLASS__, 'ajax_create_customer'));
        add_filter('manage_er_customer_posts_columns', array(__CLASS__, 'admin_columns'));
        add_action('manage_er_customer_posts_custom_column', array(__CLASS__, 'admin_column_content'), 10, 2);
    }

    /**
     * Create new customer
     */
    public static function create($data = array()) {
        // Validate required fields
        $required_fields = array('first_name', 'last_name', 'email');
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                return new WP_Error('missing_' . $field, sprintf(__('%s is required.', 'equiprent-pro'), str_replace('_', ' ', $field)));
            }
        }

        // Validate email format
        if (!is_email($data['email'])) {
            return new WP_Error('invalid_email', __('Please enter a valid email address.', 'equiprent-pro'));
        }

        // Check for duplicate email
        if (self::email_exists($data['email'])) {
            return new WP_Error('duplicate_email', __('A customer with this email address already exists.', 'equiprent-pro'));
        }

        // Sanitize data
        $sanitized_data = self::sanitize_customer_data($data);

        // Create full name for post title
        $full_name = trim($sanitized_data['first_name'] . ' ' . $sanitized_data['last_name']);

        // Create post
        $post_data = array(
            'post_type' => self::POST_TYPE,
            'post_title' => $full_name,
            'post_status' => 'publish',
        );

        $customer_id = wp_insert_post($post_data);

        if (is_wp_error($customer_id)) {
            return $customer_id;
        }

        // Save meta data
        self::save_customer_meta($customer_id, $sanitized_data);

        // Insert into database table
        self::insert_customer_record($customer_id, $sanitized_data);

        // Send welcome email if enabled
        if (ER_Utilities::get_setting('customer_welcome_email', false)) {
            self::send_welcome_email($customer_id);
        }

        // Log activity
        ER_Utilities::log('customer_created', 'customer', $customer_id, $sanitized_data);

        return $customer_id;
    }

    /**
     * Update customer
     */
    public static function update($customer_id, $data = array()) {
        if (!self::exists($customer_id)) {
            return new WP_Error('customer_not_found', __('Customer not found.', 'equiprent-pro'));
        }

        // Sanitize data
        $sanitized_data = self::sanitize_customer_data($data);

        // Check for duplicate email if email is being changed
        if (isset($sanitized_data['email'])) {
            $current_email = get_post_meta($customer_id, '_customer_email', true);
            if ($sanitized_data['email'] !== $current_email && self::email_exists($sanitized_data['email'], $customer_id)) {
                return new WP_Error('duplicate_email', __('A customer with this email address already exists.', 'equiprent-pro'));
            }
        }

        // Update post title if name changed
        if (isset($sanitized_data['first_name']) || isset($sanitized_data['last_name'])) {
            $current_first_name = get_post_meta($customer_id, '_customer_first_name', true);
            $current_last_name = get_post_meta($customer_id, '_customer_last_name', true);
            
            $first_name = $sanitized_data['first_name'] ?? $current_first_name;
            $last_name = $sanitized_data['last_name'] ?? $current_last_name;
            $full_name = trim($first_name . ' ' . $last_name);

            wp_update_post(array(
                'ID' => $customer_id,
                'post_title' => $full_name,
            ));
        }

        // Save meta data
        self::save_customer_meta($customer_id, $sanitized_data);

        // Update database record
        self::update_customer_record($customer_id, $sanitized_data);

        // Log activity
        ER_Utilities::log('customer_updated', 'customer', $customer_id, $sanitized_data);

        return $customer_id;
    }

    /**
     * Delete customer
     */
    public static function delete($customer_id, $force = false) {
        if (!self::exists($customer_id)) {
            return new WP_Error('customer_not_found', __('Customer not found.', 'equiprent-pro'));
        }

        // Check if customer has bookings
        if (self::has_bookings($customer_id) && !$force) {
            return new WP_Error('customer_has_bookings', __('Cannot delete customer with existing bookings. Use force delete if necessary.', 'equiprent-pro'));
        }

        // Delete database record
        self::delete_customer_record($customer_id);

        // Delete the post
        $result = wp_delete_post($customer_id, $force);

        if ($result) {
            // Log activity
            ER_Utilities::log('customer_deleted', 'customer', $customer_id);
        }

        return $result;
    }

    /**
     * Get customer data
     */
    public static function get($customer_id) {
        if (!self::exists($customer_id)) {
            return null;
        }

        $post = get_post($customer_id);
        $meta = get_post_meta($customer_id);

        // Build customer data
        $customer = array(
            'id' => $customer_id,
            'name' => $post->post_title,
            'status' => $post->post_status,
            'created_at' => $post->post_date,
            'updated_at' => $post->post_modified,
        );

        // Add meta fields
        $meta_fields = array(
            'first_name', 'last_name', 'email', 'phone', 'mobile', 'company',
            'customer_type', 'address_line_1', 'address_line_2', 'city', 
            'state', 'postal_code', 'country', 'drivers_license', 'tax_number',
            'credit_limit', 'payment_terms', 'preferred_contact', 'notes'
        );

        foreach ($meta_fields as $field) {
            $meta_key = '_customer_' . $field;
            $customer[$field] = isset($meta[$meta_key][0]) ? maybe_unserialize($meta[$meta_key][0]) : '';
        }

        // Get booking statistics
        $customer['booking_stats'] = self::get_booking_statistics($customer_id);

        // Get payment statistics
        $customer['payment_stats'] = self::get_payment_statistics($customer_id);

        // Get recent activity
        $customer['recent_activity'] = self::get_recent_activity($customer_id);

        return $customer;
    }

    /**
     * Get customer list with filters
     */
    public static function get_list($args = array()) {
        $defaults = array(
            'posts_per_page' => 20,
            'post_type' => self::POST_TYPE,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC',
            'meta_query' => array(),
        );

        $args = wp_parse_args($args, $defaults);

        // Handle search
        if (!empty($args['search'])) {
            $args['meta_query'][] = array(
                'relation' => 'OR',
                array(
                    'key' => '_customer_first_name',
                    'value' => $args['search'],
                    'compare' => 'LIKE',
                ),
                array(
                    'key' => '_customer_last_name',
                    'value' => $args['search'],
                    'compare' => 'LIKE',
                ),
                array(
                    'key' => '_customer_email',
                    'value' => $args['search'],
                    'compare' => 'LIKE',
                ),
                array(
                    'key' => '_customer_company',
                    'value' => $args['search'],
                    'compare' => 'LIKE',
                ),
            );
            unset($args['search']);
        }

        // Handle customer type filter
        if (!empty($args['customer_type'])) {
            $args['meta_query'][] = array(
                'key' => '_customer_type',
                'value' => $args['customer_type'],
                'compare' => '=',
            );
            unset($args['customer_type']);
        }

        $query = new WP_Query($args);
        $customer_list = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $customer_list[] = self::get(get_the_ID());
            }
            wp_reset_postdata();
        }

        return array(
            'items' => $customer_list,
            'total' => $query->found_posts,
            'pages' => $query->max_num_pages,
        );
    }

    /**
     * Search customers for autocomplete
     */
    public static function search($query, $limit = 10) {
        $args = array(
            'post_type' => self::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => $limit,
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => '_customer_first_name',
                    'value' => $query,
                    'compare' => 'LIKE',
                ),
                array(
                    'key' => '_customer_last_name',
                    'value' => $query,
                    'compare' => 'LIKE',
                ),
                array(
                    'key' => '_customer_email',
                    'value' => $query,
                    'compare' => 'LIKE',
                ),
                array(
                    'key' => '_customer_company',
                    'value' => $query,
                    'compare' => 'LIKE',
                ),
            ),
        );

        $customers = get_posts($args);
        $results = array();

        foreach ($customers as $customer) {
            $customer_data = self::get($customer->ID);
            $results[] = array(
                'id' => $customer->ID,
                'name' => $customer_data['name'],
                'email' => $customer_data['email'],
                'phone' => $customer_data['phone'],
                'company' => $customer_data['company'],
            );
        }

        return $results;
    }

    /**
     * Check if customer exists
     */
    public static function exists($customer_id) {
        $post = get_post($customer_id);
        return $post && $post->post_type === self::POST_TYPE;
    }

    /**
     * Check if email exists
     */
    public static function email_exists($email, $exclude_customer_id = null) {
        global $wpdb;
        
        $query = "
            SELECT post_id 
            FROM {$wpdb->postmeta} pm
            INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
            WHERE pm.meta_key = '_customer_email'
            AND pm.meta_value = %s
            AND p.post_type = %s
            AND p.post_status = 'publish'
        ";
        
        $params = array($email, self::POST_TYPE);
        
        if ($exclude_customer_id) {
            $query .= " AND post_id != %d";
            $params[] = $exclude_customer_id;
        }
        
        $result = $wpdb->get_var($wpdb->prepare($query, $params));
        
        return !empty($result);
    }

    /**
     * Check if customer has bookings
     */
    public static function has_bookings($customer_id) {
        global $wpdb;
        
        $bookings_table = $wpdb->prefix . 'er_bookings';
        
        $count = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*) FROM {$bookings_table} WHERE customer_id = %d
        ", $customer_id));
        
        return (int) $count > 0;
    }

    /**
     * Get customer booking statistics
     */
    public static function get_booking_statistics($customer_id) {
        global $wpdb;
        
        $bookings_table = $wpdb->prefix . 'er_bookings';
        
        $stats = $wpdb->get_row($wpdb->prepare("
            SELECT 
                COUNT(*) as total_bookings,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_bookings,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_bookings,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_bookings,
                SUM(total_amount) as total_spent,
                AVG(total_amount) as average_booking_value
            FROM {$bookings_table} 
            WHERE customer_id = %d
        ", $customer_id), ARRAY_A);
        
        return array(
            'total_bookings' => (int) ($stats['total_bookings'] ?? 0),
            'completed_bookings' => (int) ($stats['completed_bookings'] ?? 0),
            'active_bookings' => (int) ($stats['active_bookings'] ?? 0),
            'cancelled_bookings' => (int) ($stats['cancelled_bookings'] ?? 0),
            'total_spent' => (float) ($stats['total_spent'] ?? 0),
            'average_booking_value' => (float) ($stats['average_booking_value'] ?? 0),
        );
    }

    /**
     * Get customer payment statistics
     */
    public static function get_payment_statistics($customer_id) {
        global $wpdb;
        
        $payments_table = $wpdb->prefix . 'er_payments';
        $bookings_table = $wpdb->prefix . 'er_bookings';
        
        $stats = $wpdb->get_row($wpdb->prepare("
            SELECT 
                SUM(p.amount) as total_paid,
                SUM(CASE WHEN p.status = 'completed' THEN p.amount ELSE 0 END) as completed_payments,
                SUM(CASE WHEN p.status = 'pending' THEN p.amount ELSE 0 END) as pending_payments,
                SUM(b.total_amount - COALESCE(p.amount, 0)) as outstanding_amount
            FROM {$bookings_table} b
            LEFT JOIN {$payments_table} p ON b.id = p.booking_id
            WHERE b.customer_id = %d
        ", $customer_id), ARRAY_A);
        
        return array(
            'total_paid' => (float) ($stats['total_paid'] ?? 0),
            'completed_payments' => (float) ($stats['completed_payments'] ?? 0),
            'pending_payments' => (float) ($stats['pending_payments'] ?? 0),
            'outstanding_amount' => (float) ($stats['outstanding_amount'] ?? 0),
        );
    }

    /**
     * Get customer recent activity
     */
    public static function get_recent_activity($customer_id, $limit = 5) {
        global $wpdb;
        
        $activity_table = $wpdb->prefix . 'er_activity_log';
        
        $activities = $wpdb->get_results($wpdb->prepare("
            SELECT * FROM {$activity_table}
            WHERE object_type = 'customer' AND object_id = %d
            ORDER BY created_at DESC
            LIMIT %d
        ", $customer_id, $limit), ARRAY_A);
        
        return $activities ?: array();
    }

    /**
     * Send welcome email to new customer
     */
    private static function send_welcome_email($customer_id) {
        $customer = self::get($customer_id);
        if (!$customer || !$customer['email']) {
            return;
        }

        $subject = sprintf(__('Welcome to %s', 'equiprent-pro'), ER_Utilities::get_setting('business_name', get_bloginfo('name')));
        
        $message = sprintf(
            __('Dear %s,\n\nWelcome to our equipment rental service! Your customer account has been created successfully.\n\nIf you have any questions, please don\'t hesitate to contact us.\n\nBest regards,\n%s', 'equiprent-pro'),
            $customer['first_name'],
            ER_Utilities::get_setting('business_name', get_bloginfo('name'))
        );

        ER_Utilities::send_notification($customer['email'], $subject, $message);
    }

    /**
     * Insert customer record into database
     */
    private static function insert_customer_record($customer_id, $data) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'er_customers';
        
        $wpdb->insert(
            $table,
            array(
                'post_id' => $customer_id,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? '',
                'company' => $data['company'] ?? '',
                'customer_type' => $data['customer_type'] ?? 'individual',
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql'),
            ),
            array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
        );
    }

    /**
     * Update customer record in database
     */
    private static function update_customer_record($customer_id, $data) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'er_customers';
        
        $update_data = array('updated_at' => current_time('mysql'));
        $format = array('%s');

        $fields_map = array(
            'first_name' => '%s',
            'last_name' => '%s',
            'email' => '%s',
            'phone' => '%s',
            'company' => '%s',
            'customer_type' => '%s',
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
            array('post_id' => $customer_id),
            $format,
            array('%d')
        );
    }

    /**
     * Delete customer record from database
     */
    private static function delete_customer_record($customer_id) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'er_customers';
        
        $wpdb->delete(
            $table,
            array('post_id' => $customer_id),
            array('%d')
        );
    }

    /**
     * Save customer meta data
     */
    public static function save_customer_meta($customer_id, $data = null) {
        // If called from save_post hook, get data from POST
        if (is_null($data) && isset($_POST['customer_meta_nonce'])) {
            if (!wp_verify_nonce($_POST['customer_meta_nonce'], 'save_customer_meta')) {
                return;
            }

            $data = $_POST;
        }

        if (!$data || !is_array($data)) {
            return;
        }

        $meta_fields = array(
            'first_name' => 'sanitize_text_field',
            'last_name' => 'sanitize_text_field',
            'email' => 'sanitize_email',
            'phone' => 'sanitize_text_field',
            'mobile' => 'sanitize_text_field',
            'company' => 'sanitize_text_field',
            'customer_type' => 'sanitize_text_field',
            'address_line_1' => 'sanitize_text_field',
            'address_line_2' => 'sanitize_text_field',
            'city' => 'sanitize_text_field',
            'state' => 'sanitize_text_field',
            'postal_code' => 'sanitize_text_field',
            'country' => 'sanitize_text_field',
            'drivers_license' => 'sanitize_text_field',
            'tax_number' => 'sanitize_text_field',
            'credit_limit' => 'floatval',
            'payment_terms' => 'sanitize_text_field',
            'preferred_contact' => 'sanitize_text_field',
            'notes' => 'sanitize_textarea_field',
        );

        foreach ($meta_fields as $field => $sanitize_callback) {
            if (isset($data[$field])) {
                $value = call_user_func($sanitize_callback, $data[$field]);
                update_post_meta($customer_id, '_customer_' . $field, $value);
            }
        }
    }

    /**
     * Sanitize customer data
     */
    private static function sanitize_customer_data($data) {
        $sanitized = array();

        $text_fields = array(
            'first_name', 'last_name', 'phone', 'mobile', 'company', 
            'customer_type', 'address_line_1', 'address_line_2', 'city', 
            'state', 'postal_code', 'country', 'drivers_license', 
            'tax_number', 'payment_terms', 'preferred_contact'
        );
        
        foreach ($text_fields as $field) {
            if (isset($data[$field])) {
                $sanitized[$field] = sanitize_text_field($data[$field]);
            }
        }

        if (isset($data['email'])) {
            $sanitized['email'] = sanitize_email($data['email']);
        }

        if (isset($data['credit_limit'])) {
            $sanitized['credit_limit'] = floatval($data['credit_limit']);
        }

        if (isset($data['notes'])) {
            $sanitized['notes'] = sanitize_textarea_field($data['notes']);
        }

        return $sanitized;
    }

    /**
     * AJAX handler for searching customers
     */
    public static function ajax_search_customers() {
        check_ajax_referer('equiprent_admin_nonce', 'nonce');

        $query = sanitize_text_field($_POST['query'] ?? '');
        $limit = intval($_POST['limit'] ?? 10);

        if (empty($query)) {
            wp_send_json_error(__('Search query is required.', 'equiprent-pro'));
        }

        $customers = self::search($query, $limit);
        wp_send_json_success($customers);
    }

    /**
     * AJAX handler for creating customer
     */
    public static function ajax_create_customer() {
        check_ajax_referer('equiprent_admin_nonce', 'nonce');

        if (!current_user_can('edit_rental_customers')) {
            wp_send_json_error(__('Insufficient permissions.', 'equiprent-pro'));
        }

        $data = array(
            'first_name' => sanitize_text_field($_POST['first_name'] ?? ''),
            'last_name' => sanitize_text_field($_POST['last_name'] ?? ''),
            'email' => sanitize_email($_POST['email'] ?? ''),
            'phone' => sanitize_text_field($_POST['phone'] ?? ''),
            'company' => sanitize_text_field($_POST['company'] ?? ''),
            'customer_type' => sanitize_text_field($_POST['customer_type'] ?? 'individual'),
        );

        $customer_id = self::create($data);

        if (is_wp_error($customer_id)) {
            wp_send_json_error($customer_id->get_error_message());
        }

        $customer = self::get($customer_id);
        wp_send_json_success(array(
            'customer_id' => $customer_id,
            'customer' => $customer,
            'message' => __('Customer created successfully.', 'equiprent-pro')
        ));
    }

    /**
     * Admin columns for customer list
     */
    public static function admin_columns($columns) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['title'] = __('Name', 'equiprent-pro');
        $new_columns['customer_type'] = __('Type', 'equiprent-pro');
        $new_columns['email'] = __('Email', 'equiprent-pro');
        $new_columns['phone'] = __('Phone', 'equiprent-pro');
        $new_columns['bookings'] = __('Bookings', 'equiprent-pro');
        $new_columns['total_spent'] = __('Total Spent', 'equiprent-pro');
        $new_columns['date'] = $columns['date'];

        return $new_columns;
    }

    /**
     * Admin column content for customer list
     */
    public static function admin_column_content($column, $post_id) {
        $customer = self::get($post_id);
        
        switch ($column) {
            case 'customer_type':
                $type = $customer['customer_type'] ?: 'individual';
                echo $type === 'business' ? __('Business', 'equiprent-pro') : __('Individual', 'equiprent-pro');
                break;
                
            case 'email':
                if ($customer['email']) {
                    echo '<a href="mailto:' . esc_attr($customer['email']) . '">' . esc_html($customer['email']) . '</a>';
                } else {
                    echo '—';
                }
                break;
                
            case 'phone':
                if ($customer['phone']) {
                    echo '<a href="tel:' . esc_attr($customer['phone']) . '">' . esc_html($customer['phone']) . '</a>';
                } else {
                    echo '—';
                }
                break;
                
            case 'bookings':
                $stats = $customer['booking_stats'];
                echo (int) $stats['total_bookings'];
                break;
                
            case 'total_spent':
                $stats = $customer['booking_stats'];
                echo $stats['total_spent'] ? ER_Utilities::format_currency($stats['total_spent']) : '—';
                break;
        }
    }
}