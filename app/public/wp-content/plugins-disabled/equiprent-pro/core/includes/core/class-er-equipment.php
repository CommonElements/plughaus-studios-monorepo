<?php
/**
 * EquipRent Pro Equipment Management
 *
 * @package EquipRent_Pro
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Core equipment management class
 */
class ER_Equipment {

    /**
     * Equipment post type
     */
    const POST_TYPE = 'equipment';

    /**
     * Initialize the class
     */
    public static function init() {
        add_action('save_post_equipment', array(__CLASS__, 'save_equipment_meta'), 10, 2);
        add_action('wp_ajax_get_equipment_availability', array(__CLASS__, 'ajax_get_availability'));
        add_action('wp_ajax_nopriv_get_equipment_availability', array(__CLASS__, 'ajax_get_availability'));
        add_filter('manage_equipment_posts_columns', array(__CLASS__, 'admin_columns'));
        add_action('manage_equipment_posts_custom_column', array(__CLASS__, 'admin_column_content'), 10, 2);
    }

    /**
     * Create new equipment item
     */
    public static function create($data = array()) {
        // Validate required fields
        if (empty($data['title'])) {
            return new WP_Error('missing_title', __('Equipment title is required.', 'equiprent-pro'));
        }

        // Sanitize data
        $sanitized_data = self::sanitize_equipment_data($data);

        // Create post
        $post_data = array(
            'post_type' => self::POST_TYPE,
            'post_title' => $sanitized_data['title'],
            'post_content' => $sanitized_data['description'] ?? '',
            'post_status' => 'publish',
        );

        $equipment_id = wp_insert_post($post_data);

        if (is_wp_error($equipment_id)) {
            return $equipment_id;
        }

        // Save meta data
        self::save_equipment_meta($equipment_id, $sanitized_data);

        // Log activity
        ER_Utilities::log('equipment_created', 'equipment', $equipment_id, $sanitized_data);

        return $equipment_id;
    }

    /**
     * Update equipment item
     */
    public static function update($equipment_id, $data = array()) {
        if (!self::exists($equipment_id)) {
            return new WP_Error('equipment_not_found', __('Equipment item not found.', 'equiprent-pro'));
        }

        // Sanitize data
        $sanitized_data = self::sanitize_equipment_data($data);

        // Update post if title or description changed
        $post_data = array('ID' => $equipment_id);
        
        if (isset($sanitized_data['title'])) {
            $post_data['post_title'] = $sanitized_data['title'];
        }
        
        if (isset($sanitized_data['description'])) {
            $post_data['post_content'] = $sanitized_data['description'];
        }

        if (count($post_data) > 1) {
            $result = wp_update_post($post_data);
            if (is_wp_error($result)) {
                return $result;
            }
        }

        // Update meta data
        self::save_equipment_meta($equipment_id, $sanitized_data);

        // Log activity
        ER_Utilities::log('equipment_updated', 'equipment', $equipment_id, $sanitized_data);

        return $equipment_id;
    }

    /**
     * Delete equipment item
     */
    public static function delete($equipment_id, $force = false) {
        if (!self::exists($equipment_id)) {
            return new WP_Error('equipment_not_found', __('Equipment item not found.', 'equiprent-pro'));
        }

        // Check if equipment has active bookings
        if (self::has_active_bookings($equipment_id)) {
            return new WP_Error('equipment_has_bookings', __('Cannot delete equipment with active bookings.', 'equiprent-pro'));
        }

        // Delete the post
        $result = wp_delete_post($equipment_id, $force);

        if ($result) {
            // Log activity
            ER_Utilities::log('equipment_deleted', 'equipment', $equipment_id);
        }

        return $result;
    }

    /**
     * Get equipment data
     */
    public static function get($equipment_id) {
        if (!self::exists($equipment_id)) {
            return null;
        }

        $post = get_post($equipment_id);
        $meta = get_post_meta($equipment_id);

        // Build equipment data
        $equipment = array(
            'id' => $equipment_id,
            'title' => $post->post_title,
            'description' => $post->post_content,
            'status' => $post->post_status,
            'created_at' => $post->post_date,
            'updated_at' => $post->post_modified,
        );

        // Add meta fields
        $meta_fields = array(
            'sku', 'daily_rate', 'weekly_rate', 'monthly_rate', 'quantity',
            'equipment_status', 'condition', 'location', 'model', 'serial_number',
            'purchase_date', 'purchase_price', 'warranty_expiry', 'maintenance_schedule',
            'dimensions', 'weight', 'power_requirements', 'accessories', 'notes'
        );

        foreach ($meta_fields as $field) {
            $meta_key = '_equipment_' . $field;
            $equipment[$field] = isset($meta[$meta_key][0]) ? maybe_unserialize($meta[$meta_key][0]) : '';
        }

        // Get taxonomies
        $equipment['categories'] = wp_get_post_terms($equipment_id, 'equipment_category', array('fields' => 'names'));
        $equipment['brands'] = wp_get_post_terms($equipment_id, 'equipment_brand', array('fields' => 'names'));
        $equipment['conditions'] = wp_get_post_terms($equipment_id, 'equipment_condition', array('fields' => 'names'));
        $equipment['locations'] = wp_get_post_terms($equipment_id, 'equipment_location', array('fields' => 'names'));

        // Get stock information
        $equipment['stock'] = ER_Utilities::get_equipment_stock($equipment_id);

        // Get featured image
        $equipment['featured_image'] = get_the_post_thumbnail_url($equipment_id, 'medium');

        return $equipment;
    }

    /**
     * Get equipment list with filters
     */
    public static function get_list($args = array()) {
        $defaults = array(
            'posts_per_page' => 20,
            'post_type' => self::POST_TYPE,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC',
            'meta_query' => array(),
            'tax_query' => array(),
        );

        $args = wp_parse_args($args, $defaults);

        // Handle search
        if (!empty($args['search'])) {
            $args['s'] = $args['search'];
            unset($args['search']);
        }

        // Handle category filter
        if (!empty($args['category'])) {
            $args['tax_query'][] = array(
                'taxonomy' => 'equipment_category',
                'field' => 'slug',
                'terms' => $args['category'],
            );
            unset($args['category']);
        }

        // Handle status filter
        if (!empty($args['equipment_status'])) {
            $args['meta_query'][] = array(
                'key' => '_equipment_status',
                'value' => $args['equipment_status'],
                'compare' => '=',
            );
            unset($args['equipment_status']);
        }

        // Handle availability filter
        if (!empty($args['available_only'])) {
            $args['meta_query'][] = array(
                'key' => '_equipment_status',
                'value' => 'available',
                'compare' => '=',
            );
            unset($args['available_only']);
        }

        $query = new WP_Query($args);
        $equipment_list = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $equipment_list[] = self::get(get_the_ID());
            }
            wp_reset_postdata();
        }

        return array(
            'items' => $equipment_list,
            'total' => $query->found_posts,
            'pages' => $query->max_num_pages,
        );
    }

    /**
     * Check if equipment exists
     */
    public static function exists($equipment_id) {
        $post = get_post($equipment_id);
        return $post && $post->post_type === self::POST_TYPE;
    }

    /**
     * Check if equipment has active bookings
     */
    public static function has_active_bookings($equipment_id) {
        global $wpdb;
        
        $booking_items_table = $wpdb->prefix . 'er_booking_items';
        $bookings_table = $wpdb->prefix . 'er_bookings';

        $count = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*)
            FROM {$booking_items_table} bi
            INNER JOIN {$bookings_table} b ON bi.booking_id = b.id
            WHERE bi.equipment_id = %d
            AND b.status IN ('confirmed', 'active')
        ", $equipment_id));

        return (int) $count > 0;
    }

    /**
     * Get equipment availability for date range
     */
    public static function get_availability($equipment_id, $start_date, $end_date) {
        if (!self::exists($equipment_id)) {
            return false;
        }

        $stock = ER_Utilities::get_equipment_stock($equipment_id);
        
        // Check if any equipment is available
        if ($stock['available'] <= 0) {
            return array(
                'available' => false,
                'quantity' => 0,
                'message' => __('Equipment is currently out of stock.', 'equiprent-pro')
            );
        }

        // Check for conflicting bookings
        $is_available = ER_Utilities::is_equipment_available($equipment_id, $start_date, $end_date);

        if (!$is_available) {
            return array(
                'available' => false,
                'quantity' => 0,
                'message' => __('Equipment is not available for the selected dates.', 'equiprent-pro')
            );
        }

        return array(
            'available' => true,
            'quantity' => $stock['available'],
            'message' => sprintf(
                __('%d units available for the selected dates.', 'equiprent-pro'),
                $stock['available']
            )
        );
    }

    /**
     * Calculate rental cost for equipment
     */
    public static function calculate_cost($equipment_id, $start_date, $end_date, $quantity = 1) {
        $equipment = self::get($equipment_id);
        if (!$equipment) {
            return 0;
        }

        $days = ER_Utilities::calculate_rental_days($start_date, $end_date);
        $daily_rate = (float) $equipment['daily_rate'];
        $weekly_rate = (float) $equipment['weekly_rate'];
        $monthly_rate = (float) $equipment['monthly_rate'];

        $cost_per_unit = ER_Utilities::calculate_rental_cost($daily_rate, $days, $weekly_rate, $monthly_rate);

        return $cost_per_unit * $quantity;
    }

    /**
     * Save equipment meta data
     */
    public static function save_equipment_meta($equipment_id, $data = null) {
        // If called from save_post hook, get data from POST
        if (is_null($data) && isset($_POST['equipment_meta_nonce'])) {
            if (!wp_verify_nonce($_POST['equipment_meta_nonce'], 'save_equipment_meta')) {
                return;
            }

            $data = $_POST;
        }

        if (!$data || !is_array($data)) {
            return;
        }

        $meta_fields = array(
            'sku' => 'sanitize_text_field',
            'daily_rate' => 'floatval',
            'weekly_rate' => 'floatval',
            'monthly_rate' => 'floatval',
            'quantity' => 'intval',
            'equipment_status' => 'sanitize_text_field',
            'condition' => 'sanitize_text_field',
            'location' => 'sanitize_text_field',
            'model' => 'sanitize_text_field',
            'serial_number' => 'sanitize_text_field',
            'purchase_date' => 'sanitize_text_field',
            'purchase_price' => 'floatval',
            'warranty_expiry' => 'sanitize_text_field',
            'maintenance_schedule' => 'sanitize_text_field',
            'dimensions' => 'sanitize_text_field',
            'weight' => 'sanitize_text_field',
            'power_requirements' => 'sanitize_text_field',
            'accessories' => 'sanitize_textarea_field',
            'notes' => 'sanitize_textarea_field',
        );

        foreach ($meta_fields as $field => $sanitize_callback) {
            $meta_key = '_equipment_' . $field;
            
            if (isset($data[$field])) {
                $value = call_user_func($sanitize_callback, $data[$field]);
                update_post_meta($equipment_id, $meta_key, $value);
            }
        }

        // Handle taxonomy assignments
        if (isset($data['equipment_categories'])) {
            wp_set_post_terms($equipment_id, $data['equipment_categories'], 'equipment_category');
        }
        
        if (isset($data['equipment_brands'])) {
            wp_set_post_terms($equipment_id, $data['equipment_brands'], 'equipment_brand');
        }
        
        if (isset($data['equipment_conditions'])) {
            wp_set_post_terms($equipment_id, $data['equipment_conditions'], 'equipment_condition');
        }
        
        if (isset($data['equipment_locations'])) {
            wp_set_post_terms($equipment_id, $data['equipment_locations'], 'equipment_location');
        }
    }

    /**
     * Sanitize equipment data
     */
    private static function sanitize_equipment_data($data) {
        $sanitized = array();

        if (isset($data['title'])) {
            $sanitized['title'] = sanitize_text_field($data['title']);
        }

        if (isset($data['description'])) {
            $sanitized['description'] = wp_kses_post($data['description']);
        }

        $fields = array(
            'sku', 'equipment_status', 'condition', 'location', 'model', 
            'serial_number', 'purchase_date', 'warranty_expiry', 
            'maintenance_schedule', 'dimensions', 'weight', 'power_requirements'
        );

        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $sanitized[$field] = sanitize_text_field($data[$field]);
            }
        }

        $number_fields = array('daily_rate', 'weekly_rate', 'monthly_rate', 'purchase_price');
        foreach ($number_fields as $field) {
            if (isset($data[$field])) {
                $sanitized[$field] = floatval($data[$field]);
            }
        }

        $int_fields = array('quantity');
        foreach ($int_fields as $field) {
            if (isset($data[$field])) {
                $sanitized[$field] = intval($data[$field]);
            }
        }

        $text_fields = array('accessories', 'notes');
        foreach ($text_fields as $field) {
            if (isset($data[$field])) {
                $sanitized[$field] = sanitize_textarea_field($data[$field]);
            }
        }

        return $sanitized;
    }

    /**
     * AJAX handler for getting equipment availability
     */
    public static function ajax_get_availability() {
        check_ajax_referer('equiprent_admin_nonce', 'nonce');

        $equipment_id = intval($_POST['equipment_id'] ?? 0);
        $start_date = sanitize_text_field($_POST['start_date'] ?? '');
        $end_date = sanitize_text_field($_POST['end_date'] ?? '');

        if (!$equipment_id || !$start_date || !$end_date) {
            wp_send_json_error(__('Missing required parameters.', 'equiprent-pro'));
        }

        $availability = self::get_availability($equipment_id, $start_date, $end_date);
        wp_send_json_success($availability);
    }

    /**
     * Admin columns for equipment list
     */
    public static function admin_columns($columns) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['title'] = $columns['title'];
        $new_columns['equipment_image'] = __('Image', 'equiprent-pro');
        $new_columns['equipment_sku'] = __('SKU', 'equiprent-pro');
        $new_columns['equipment_category'] = __('Category', 'equiprent-pro');
        $new_columns['equipment_rate'] = __('Daily Rate', 'equiprent-pro');
        $new_columns['equipment_stock'] = __('Stock', 'equiprent-pro');
        $new_columns['equipment_status'] = __('Status', 'equiprent-pro');
        $new_columns['date'] = $columns['date'];

        return $new_columns;
    }

    /**
     * Admin column content for equipment list
     */
    public static function admin_column_content($column, $post_id) {
        $equipment = self::get($post_id);
        
        switch ($column) {
            case 'equipment_image':
                if ($equipment['featured_image']) {
                    echo '<img src="' . esc_url($equipment['featured_image']) . '" style="width: 50px; height: 50px; object-fit: cover;">';
                } else {
                    echo '<span class="dashicons dashicons-format-image"></span>';
                }
                break;
                
            case 'equipment_sku':
                echo esc_html($equipment['sku'] ?: '—');
                break;
                
            case 'equipment_category':
                echo esc_html(implode(', ', $equipment['categories']) ?: '—');
                break;
                
            case 'equipment_rate':
                echo $equipment['daily_rate'] ? ER_Utilities::format_currency($equipment['daily_rate']) : '—';
                break;
                
            case 'equipment_stock':
                $stock = $equipment['stock'];
                printf(
                    '<span class="stock-info">%d / %d</span>',
                    $stock['available'],
                    $stock['total']
                );
                break;
                
            case 'equipment_status':
                $status = $equipment['equipment_status'] ?: 'available';
                $statuses = ER_Post_Types::get_equipment_statuses();
                $status_label = $statuses[$status] ?? $status;
                
                printf(
                    '<span class="equipment-status status-%s">%s</span>',
                    esc_attr($status),
                    esc_html($status_label)
                );
                break;
        }
    }
}