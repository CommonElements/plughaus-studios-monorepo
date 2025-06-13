<?php
/**
 * REST API endpoints for PlugHaus Property Management
 */

if (!defined('ABSPATH')) {
    exit;
}

class PHPM_REST_API {
    
    /**
     * API namespace
     */
    const NAMESPACE = 'vmp/v1';
    
    /**
     * Register REST API routes
     */
    public static function register_routes() {
        // Check if API is enabled
        $options = get_option('vmp_settings');
        if (isset($options['enable_api']) && !$options['enable_api']) {
            return;
        }
        
        // Properties endpoints
        register_rest_route(self::NAMESPACE, '/properties', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array(__CLASS__, 'get_properties'),
                'permission_callback' => array(__CLASS__, 'get_items_permissions_check'),
                'args' => self::get_collection_params(),
            ),
            array(
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => array(__CLASS__, 'create_property'),
                'permission_callback' => array(__CLASS__, 'create_item_permissions_check'),
                'args' => self::get_property_params(),
            ),
        ));
        
        register_rest_route(self::NAMESPACE, '/properties/(?P<id>[\d]+)', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array(__CLASS__, 'get_property'),
                'permission_callback' => array(__CLASS__, 'get_item_permissions_check'),
                'args' => array(
                    'id' => array(
                        'validate_callback' => function($param, $request, $key) {
                            return is_numeric($param);
                        }
                    ),
                ),
            ),
            array(
                'methods' => WP_REST_Server::EDITABLE,
                'callback' => array(__CLASS__, 'update_property'),
                'permission_callback' => array(__CLASS__, 'update_item_permissions_check'),
                'args' => self::get_property_params(),
            ),
            array(
                'methods' => WP_REST_Server::DELETABLE,
                'callback' => array(__CLASS__, 'delete_property'),
                'permission_callback' => array(__CLASS__, 'delete_item_permissions_check'),
            ),
        ));
        
        // Units endpoints
        register_rest_route(self::NAMESPACE, '/units', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array(__CLASS__, 'get_units'),
                'permission_callback' => array(__CLASS__, 'get_items_permissions_check'),
                'args' => self::get_collection_params(),
            ),
            array(
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => array(__CLASS__, 'create_unit'),
                'permission_callback' => array(__CLASS__, 'create_item_permissions_check'),
                'args' => self::get_unit_params(),
            ),
        ));
        
        register_rest_route(self::NAMESPACE, '/units/(?P<id>[\d]+)', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array(__CLASS__, 'get_unit'),
                'permission_callback' => array(__CLASS__, 'get_item_permissions_check'),
            ),
            array(
                'methods' => WP_REST_Server::EDITABLE,
                'callback' => array(__CLASS__, 'update_unit'),
                'permission_callback' => array(__CLASS__, 'update_item_permissions_check'),
                'args' => self::get_unit_params(),
            ),
            array(
                'methods' => WP_REST_Server::DELETABLE,
                'callback' => array(__CLASS__, 'delete_unit'),
                'permission_callback' => array(__CLASS__, 'delete_item_permissions_check'),
            ),
        ));
        
        // Tenants endpoints
        register_rest_route(self::NAMESPACE, '/tenants', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array(__CLASS__, 'get_tenants'),
                'permission_callback' => array(__CLASS__, 'get_items_permissions_check'),
                'args' => self::get_collection_params(),
            ),
            array(
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => array(__CLASS__, 'create_tenant'),
                'permission_callback' => array(__CLASS__, 'create_item_permissions_check'),
                'args' => self::get_tenant_params(),
            ),
        ));
        
        register_rest_route(self::NAMESPACE, '/tenants/(?P<id>[\d]+)', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array(__CLASS__, 'get_tenant'),
                'permission_callback' => array(__CLASS__, 'get_item_permissions_check'),
            ),
            array(
                'methods' => WP_REST_Server::EDITABLE,
                'callback' => array(__CLASS__, 'update_tenant'),
                'permission_callback' => array(__CLASS__, 'update_item_permissions_check'),
                'args' => self::get_tenant_params(),
            ),
            array(
                'methods' => WP_REST_Server::DELETABLE,
                'callback' => array(__CLASS__, 'delete_tenant'),
                'permission_callback' => array(__CLASS__, 'delete_item_permissions_check'),
            ),
        ));
        
        // Leases endpoints
        register_rest_route(self::NAMESPACE, '/leases', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array(__CLASS__, 'get_leases'),
                'permission_callback' => array(__CLASS__, 'get_items_permissions_check'),
                'args' => self::get_collection_params(),
            ),
            array(
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => array(__CLASS__, 'create_lease'),
                'permission_callback' => array(__CLASS__, 'create_item_permissions_check'),
                'args' => self::get_lease_params(),
            ),
        ));
        
        // Maintenance endpoints
        register_rest_route(self::NAMESPACE, '/maintenance', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array(__CLASS__, 'get_maintenance_requests'),
                'permission_callback' => array(__CLASS__, 'get_items_permissions_check'),
                'args' => self::get_collection_params(),
            ),
            array(
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => array(__CLASS__, 'create_maintenance_request'),
                'permission_callback' => array(__CLASS__, 'create_maintenance_permissions_check'),
                'args' => self::get_maintenance_params(),
            ),
        ));
        
        // Reports endpoints
        register_rest_route(self::NAMESPACE, '/reports/occupancy', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array(__CLASS__, 'get_occupancy_report'),
                'permission_callback' => array(__CLASS__, 'reports_permissions_check'),
            ),
        ));
        
        register_rest_route(self::NAMESPACE, '/reports/financial', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array(__CLASS__, 'get_financial_report'),
                'permission_callback' => array(__CLASS__, 'reports_permissions_check'),
                'args' => array(
                    'start_date' => array(
                        'required' => false,
                        'type' => 'string',
                        'format' => 'date',
                    ),
                    'end_date' => array(
                        'required' => false,
                        'type' => 'string',
                        'format' => 'date',
                    ),
                ),
            ),
        ));
    }
    
    /**
     * Get properties
     */
    public static function get_properties($request) {
        $args = array(
            'post_type' => 'vmp_property',
            'posts_per_page' => $request->get_param('per_page') ?: 10,
            'paged' => $request->get_param('page') ?: 1,
            'orderby' => $request->get_param('orderby') ?: 'date',
            'order' => $request->get_param('order') ?: 'DESC',
        );
        
        // Add search
        if ($search = $request->get_param('search')) {
            $args['s'] = $search;
        }
        
        // Add filters
        if ($type = $request->get_param('type')) {
            $args['tax_query'][] = array(
                'taxonomy' => 'vmp_property_type',
                'field' => 'slug',
                'terms' => $type,
            );
        }
        
        if ($location = $request->get_param('location')) {
            $args['tax_query'][] = array(
                'taxonomy' => 'vmp_location',
                'field' => 'slug',
                'terms' => $location,
            );
        }
        
        $query = new WP_Query($args);
        
        $properties = array();
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $properties[] = self::prepare_property_response(get_the_ID());
            }
        }
        
        wp_reset_postdata();
        
        $response = rest_ensure_response($properties);
        
        // Add pagination headers
        $response->header('X-WP-Total', $query->found_posts);
        $response->header('X-WP-TotalPages', $query->max_num_pages);
        
        return $response;
    }
    
    /**
     * Get single property
     */
    public static function get_property($request) {
        $property_id = $request->get_param('id');
        $property = get_post($property_id);
        
        if (!$property || 'vmp_property' !== $property->post_type) {
            return new WP_Error('rest_property_invalid_id', __('Invalid property ID.', 'plughaus-property'), array('status' => 404));
        }
        
        return rest_ensure_response(self::prepare_property_response($property_id));
    }
    
    /**
     * Create property
     */
    public static function create_property($request) {
        $property_data = array(
            'post_type' => 'vmp_property',
            'post_title' => sanitize_text_field($request->get_param('title')),
            'post_content' => wp_kses_post($request->get_param('description')),
            'post_status' => 'publish',
        );
        
        $property_id = wp_insert_post($property_data, true);
        
        if (is_wp_error($property_id)) {
            return $property_id;
        }
        
        // Update meta fields
        if ($address = $request->get_param('address')) {
            update_post_meta($property_id, '_vmp_property_address', sanitize_text_field($address));
        }
        
        if ($city = $request->get_param('city')) {
            update_post_meta($property_id, '_vmp_property_city', sanitize_text_field($city));
        }
        
        if ($state = $request->get_param('state')) {
            update_post_meta($property_id, '_vmp_property_state', sanitize_text_field($state));
        }
        
        if ($zip = $request->get_param('zip')) {
            update_post_meta($property_id, '_vmp_property_zip', sanitize_text_field($zip));
        }
        
        if ($units = $request->get_param('units')) {
            update_post_meta($property_id, '_vmp_property_units', intval($units));
        }
        
        // Set taxonomies
        if ($types = $request->get_param('property_types')) {
            wp_set_object_terms($property_id, $types, 'vmp_property_type');
        }
        
        if ($amenities = $request->get_param('amenities')) {
            wp_set_object_terms($property_id, $amenities, 'vmp_amenities');
        }
        
        return rest_ensure_response(self::prepare_property_response($property_id));
    }
    
    /**
     * Update property
     */
    public static function update_property($request) {
        $property_id = $request->get_param('id');
        $property = get_post($property_id);
        
        if (!$property || 'vmp_property' !== $property->post_type) {
            return new WP_Error('rest_property_invalid_id', __('Invalid property ID.', 'plughaus-property'), array('status' => 404));
        }
        
        $property_data = array(
            'ID' => $property_id,
        );
        
        if ($title = $request->get_param('title')) {
            $property_data['post_title'] = sanitize_text_field($title);
        }
        
        if ($description = $request->get_param('description')) {
            $property_data['post_content'] = wp_kses_post($description);
        }
        
        $updated = wp_update_post($property_data, true);
        
        if (is_wp_error($updated)) {
            return $updated;
        }
        
        // Update meta fields
        $meta_fields = array('address', 'city', 'state', 'zip', 'units');
        
        foreach ($meta_fields as $field) {
            if ($value = $request->get_param($field)) {
                update_post_meta($property_id, '_vmp_property_' . $field, sanitize_text_field($value));
            }
        }
        
        // Update taxonomies
        if ($types = $request->get_param('property_types')) {
            wp_set_object_terms($property_id, $types, 'vmp_property_type');
        }
        
        if ($amenities = $request->get_param('amenities')) {
            wp_set_object_terms($property_id, $amenities, 'vmp_amenities');
        }
        
        return rest_ensure_response(self::prepare_property_response($property_id));
    }
    
    /**
     * Delete property
     */
    public static function delete_property($request) {
        $property_id = $request->get_param('id');
        $property = get_post($property_id);
        
        if (!$property || 'vmp_property' !== $property->post_type) {
            return new WP_Error('rest_property_invalid_id', __('Invalid property ID.', 'plughaus-property'), array('status' => 404));
        }
        
        $deleted = wp_delete_post($property_id, true);
        
        if (!$deleted) {
            return new WP_Error('rest_cannot_delete', __('The property cannot be deleted.', 'plughaus-property'), array('status' => 500));
        }
        
        return rest_ensure_response(array('deleted' => true));
    }
    
    /**
     * Prepare property response
     */
    private static function prepare_property_response($property_id) {
        $property = get_post($property_id);
        
        $response = array(
            'id' => $property->ID,
            'title' => $property->post_title,
            'description' => $property->post_content,
            'slug' => $property->post_name,
            'status' => $property->post_status,
            'link' => get_permalink($property->ID),
            'date_created' => $property->post_date,
            'date_modified' => $property->post_modified,
            'featured_image' => get_the_post_thumbnail_url($property->ID, 'full'),
            'address' => get_post_meta($property->ID, '_vmp_property_address', true),
            'city' => get_post_meta($property->ID, '_vmp_property_city', true),
            'state' => get_post_meta($property->ID, '_vmp_property_state', true),
            'zip' => get_post_meta($property->ID, '_vmp_property_zip', true),
            'units' => get_post_meta($property->ID, '_vmp_property_units', true),
            'property_types' => wp_get_post_terms($property->ID, 'vmp_property_type', array('fields' => 'names')),
            'amenities' => wp_get_post_terms($property->ID, 'vmp_amenities', array('fields' => 'names')),
            'location' => wp_get_post_terms($property->ID, 'vmp_location', array('fields' => 'names')),
        );
        
        return $response;
    }
    
    /**
     * Get units
     */
    public static function get_units($request) {
        $args = array(
            'post_type' => 'vmp_unit',
            'posts_per_page' => $request->get_param('per_page') ?: 10,
            'paged' => $request->get_param('page') ?: 1,
        );
        
        // Filter by property
        if ($property_id = $request->get_param('property')) {
            $args['meta_query'][] = array(
                'key' => '_vmp_unit_property',
                'value' => $property_id,
                'compare' => '=',
            );
        }
        
        // Filter by status
        if ($status = $request->get_param('status')) {
            $args['post_status'] = $status;
        }
        
        $query = new WP_Query($args);
        
        $units = array();
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $units[] = self::prepare_unit_response(get_the_ID());
            }
        }
        
        wp_reset_postdata();
        
        $response = rest_ensure_response($units);
        
        // Add pagination headers
        $response->header('X-WP-Total', $query->found_posts);
        $response->header('X-WP-TotalPages', $query->max_num_pages);
        
        return $response;
    }
    
    /**
     * Prepare unit response
     */
    private static function prepare_unit_response($unit_id) {
        $unit = get_post($unit_id);
        
        $response = array(
            'id' => $unit->ID,
            'title' => $unit->post_title,
            'description' => $unit->post_content,
            'status' => $unit->post_status,
            'property_id' => get_post_meta($unit->ID, '_vmp_unit_property', true),
            'rent' => get_post_meta($unit->ID, '_vmp_unit_rent', true),
            'bedrooms' => get_post_meta($unit->ID, '_vmp_unit_bedrooms', true),
            'bathrooms' => get_post_meta($unit->ID, '_vmp_unit_bathrooms', true),
            'sqft' => get_post_meta($unit->ID, '_vmp_unit_sqft', true),
            'amenities' => wp_get_post_terms($unit->ID, 'vmp_amenities', array('fields' => 'names')),
        );
        
        return $response;
    }
    
    // Additional endpoint methods would follow similar patterns...
    
    /**
     * Permissions check for getting items
     */
    public static function get_items_permissions_check($request) {
        return true; // Public access for reading
    }
    
    /**
     * Permissions check for getting single item
     */
    public static function get_item_permissions_check($request) {
        return true; // Public access for reading
    }
    
    /**
     * Permissions check for creating items
     */
    public static function create_item_permissions_check($request) {
        return current_user_can('publish_posts');
    }
    
    /**
     * Permissions check for updating items
     */
    public static function update_item_permissions_check($request) {
        return current_user_can('edit_posts');
    }
    
    /**
     * Permissions check for deleting items
     */
    public static function delete_item_permissions_check($request) {
        return current_user_can('delete_posts');
    }
    
    /**
     * Permissions check for creating maintenance requests
     */
    public static function create_maintenance_permissions_check($request) {
        return is_user_logged_in();
    }
    
    /**
     * Permissions check for reports
     */
    public static function reports_permissions_check($request) {
        return current_user_can('manage_options');
    }
    
    /**
     * Get collection parameters
     */
    private static function get_collection_params() {
        return array(
            'page' => array(
                'description' => __('Current page of the collection.', 'plughaus-property'),
                'type' => 'integer',
                'default' => 1,
                'sanitize_callback' => 'absint',
            ),
            'per_page' => array(
                'description' => __('Maximum number of items to be returned in result set.', 'plughaus-property'),
                'type' => 'integer',
                'default' => 10,
                'minimum' => 1,
                'maximum' => 100,
                'sanitize_callback' => 'absint',
            ),
            'search' => array(
                'description' => __('Limit results to those matching a string.', 'plughaus-property'),
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'orderby' => array(
                'description' => __('Sort collection by object attribute.', 'plughaus-property'),
                'type' => 'string',
                'default' => 'date',
                'enum' => array('date', 'title', 'menu_order'),
            ),
            'order' => array(
                'description' => __('Order sort attribute ascending or descending.', 'plughaus-property'),
                'type' => 'string',
                'default' => 'desc',
                'enum' => array('asc', 'desc'),
            ),
        );
    }
    
    /**
     * Get property parameters
     */
    private static function get_property_params() {
        return array(
            'title' => array(
                'description' => __('Property title.', 'plughaus-property'),
                'type' => 'string',
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'description' => array(
                'description' => __('Property description.', 'plughaus-property'),
                'type' => 'string',
                'sanitize_callback' => 'wp_kses_post',
            ),
            'address' => array(
                'description' => __('Property address.', 'plughaus-property'),
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'city' => array(
                'description' => __('Property city.', 'plughaus-property'),
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'state' => array(
                'description' => __('Property state.', 'plughaus-property'),
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'zip' => array(
                'description' => __('Property ZIP code.', 'plughaus-property'),
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'units' => array(
                'description' => __('Number of units.', 'plughaus-property'),
                'type' => 'integer',
                'sanitize_callback' => 'absint',
            ),
            'property_types' => array(
                'description' => __('Property types.', 'plughaus-property'),
                'type' => 'array',
                'items' => array(
                    'type' => 'string',
                ),
            ),
            'amenities' => array(
                'description' => __('Property amenities.', 'plughaus-property'),
                'type' => 'array',
                'items' => array(
                    'type' => 'string',
                ),
            ),
        );
    }
    
    /**
     * Get unit parameters
     */
    private static function get_unit_params() {
        return array(
            'title' => array(
                'description' => __('Unit title.', 'plughaus-property'),
                'type' => 'string',
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'property_id' => array(
                'description' => __('Property ID.', 'plughaus-property'),
                'type' => 'integer',
                'required' => true,
                'sanitize_callback' => 'absint',
            ),
            'rent' => array(
                'description' => __('Monthly rent amount.', 'plughaus-property'),
                'type' => 'number',
                'sanitize_callback' => 'floatval',
            ),
            'bedrooms' => array(
                'description' => __('Number of bedrooms.', 'plughaus-property'),
                'type' => 'integer',
                'sanitize_callback' => 'absint',
            ),
            'bathrooms' => array(
                'description' => __('Number of bathrooms.', 'plughaus-property'),
                'type' => 'number',
                'sanitize_callback' => 'floatval',
            ),
            'sqft' => array(
                'description' => __('Square footage.', 'plughaus-property'),
                'type' => 'integer',
                'sanitize_callback' => 'absint',
            ),
        );
    }
    
    /**
     * Get tenant parameters
     */
    private static function get_tenant_params() {
        return array(
            'first_name' => array(
                'description' => __('Tenant first name.', 'plughaus-property'),
                'type' => 'string',
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'last_name' => array(
                'description' => __('Tenant last name.', 'plughaus-property'),
                'type' => 'string',
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'email' => array(
                'description' => __('Tenant email address.', 'plughaus-property'),
                'type' => 'string',
                'format' => 'email',
                'required' => true,
                'sanitize_callback' => 'sanitize_email',
            ),
            'phone' => array(
                'description' => __('Tenant phone number.', 'plughaus-property'),
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ),
        );
    }
    
    /**
     * Get lease parameters
     */
    private static function get_lease_params() {
        return array(
            'unit_id' => array(
                'description' => __('Unit ID.', 'plughaus-property'),
                'type' => 'integer',
                'required' => true,
                'sanitize_callback' => 'absint',
            ),
            'tenant_id' => array(
                'description' => __('Tenant ID.', 'plughaus-property'),
                'type' => 'integer',
                'required' => true,
                'sanitize_callback' => 'absint',
            ),
            'start_date' => array(
                'description' => __('Lease start date.', 'plughaus-property'),
                'type' => 'string',
                'format' => 'date',
                'required' => true,
            ),
            'end_date' => array(
                'description' => __('Lease end date.', 'plughaus-property'),
                'type' => 'string',
                'format' => 'date',
                'required' => true,
            ),
            'rent_amount' => array(
                'description' => __('Monthly rent amount.', 'plughaus-property'),
                'type' => 'number',
                'required' => true,
                'sanitize_callback' => 'floatval',
            ),
            'security_deposit' => array(
                'description' => __('Security deposit amount.', 'plughaus-property'),
                'type' => 'number',
                'sanitize_callback' => 'floatval',
            ),
        );
    }
    
    /**
     * Get maintenance parameters
     */
    private static function get_maintenance_params() {
        return array(
            'title' => array(
                'description' => __('Request title.', 'plughaus-property'),
                'type' => 'string',
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'description' => array(
                'description' => __('Request description.', 'plughaus-property'),
                'type' => 'string',
                'required' => true,
                'sanitize_callback' => 'wp_kses_post',
            ),
            'unit_id' => array(
                'description' => __('Unit ID.', 'plughaus-property'),
                'type' => 'integer',
                'sanitize_callback' => 'absint',
            ),
            'priority' => array(
                'description' => __('Request priority.', 'plughaus-property'),
                'type' => 'string',
                'enum' => array('low', 'medium', 'high', 'emergency'),
                'default' => 'medium',
            ),
            'category' => array(
                'description' => __('Maintenance category.', 'plughaus-property'),
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ),
        );
    }
    
    // Stub methods for remaining endpoints
    public static function get_unit($request) {
        $unit_id = $request->get_param('id');
        $unit = get_post($unit_id);
        
        if (!$unit || 'vmp_unit' !== $unit->post_type) {
            return new WP_Error('rest_unit_invalid_id', __('Invalid unit ID.', 'plughaus-property'), array('status' => 404));
        }
        
        return rest_ensure_response(self::prepare_unit_response($unit_id));
    }
    
    public static function create_unit($request) {
        $unit_data = array(
            'post_type' => 'vmp_unit',
            'post_title' => sanitize_text_field($request->get_param('title')),
            'post_status' => 'publish',
        );
        
        $unit_id = wp_insert_post($unit_data, true);
        
        if (is_wp_error($unit_id)) {
            return $unit_id;
        }
        
        // Update meta fields
        if ($property_id = $request->get_param('property_id')) {
            update_post_meta($unit_id, '_vmp_unit_property', intval($property_id));
        }
        
        if ($rent = $request->get_param('rent')) {
            update_post_meta($unit_id, '_vmp_unit_rent', floatval($rent));
        }
        
        if ($bedrooms = $request->get_param('bedrooms')) {
            update_post_meta($unit_id, '_vmp_unit_bedrooms', intval($bedrooms));
        }
        
        if ($bathrooms = $request->get_param('bathrooms')) {
            update_post_meta($unit_id, '_vmp_unit_bathrooms', floatval($bathrooms));
        }
        
        if ($sqft = $request->get_param('sqft')) {
            update_post_meta($unit_id, '_vmp_unit_sqft', intval($sqft));
        }
        
        return rest_ensure_response(self::prepare_unit_response($unit_id));
    }
    
    public static function update_unit($request) {
        $unit_id = $request->get_param('id');
        $unit = get_post($unit_id);
        
        if (!$unit || 'vmp_unit' !== $unit->post_type) {
            return new WP_Error('rest_unit_invalid_id', __('Invalid unit ID.', 'plughaus-property'), array('status' => 404));
        }
        
        $unit_data = array('ID' => $unit_id);
        
        if ($title = $request->get_param('title')) {
            $unit_data['post_title'] = sanitize_text_field($title);
        }
        
        $updated = wp_update_post($unit_data, true);
        
        if (is_wp_error($updated)) {
            return $updated;
        }
        
        // Update meta fields
        $meta_fields = array('property_id', 'rent', 'bedrooms', 'bathrooms', 'sqft');
        
        foreach ($meta_fields as $field) {
            if ($value = $request->get_param($field)) {
                $meta_key = '_vmp_unit_' . ($field === 'property_id' ? 'property' : $field);
                update_post_meta($unit_id, $meta_key, $field === 'property_id' ? intval($value) : $value);
            }
        }
        
        return rest_ensure_response(self::prepare_unit_response($unit_id));
    }
    
    public static function delete_unit($request) {
        $unit_id = $request->get_param('id');
        $unit = get_post($unit_id);
        
        if (!$unit || 'vmp_unit' !== $unit->post_type) {
            return new WP_Error('rest_unit_invalid_id', __('Invalid unit ID.', 'plughaus-property'), array('status' => 404));
        }
        
        $deleted = wp_delete_post($unit_id, true);
        
        if (!$deleted) {
            return new WP_Error('rest_cannot_delete', __('The unit cannot be deleted.', 'plughaus-property'), array('status' => 500));
        }
        
        return rest_ensure_response(array('deleted' => true));
    }
    
    public static function get_tenants($request) {
        $args = array(
            'post_type' => 'vmp_tenant',
            'posts_per_page' => $request->get_param('per_page') ?: 10,
            'paged' => $request->get_param('page') ?: 1,
            'orderby' => $request->get_param('orderby') ?: 'date',
            'order' => $request->get_param('order') ?: 'DESC',
        );
        
        // Add search
        if ($search = $request->get_param('search')) {
            $args['s'] = $search;
        }
        
        $query = new WP_Query($args);
        
        $tenants = array();
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $tenants[] = self::prepare_tenant_response(get_the_ID());
            }
        }
        
        wp_reset_postdata();
        
        $response = rest_ensure_response($tenants);
        
        // Add pagination headers
        $response->header('X-WP-Total', $query->found_posts);
        $response->header('X-WP-TotalPages', $query->max_num_pages);
        
        return $response;
    }
    
    public static function get_tenant($request) {
        $tenant_id = $request->get_param('id');
        $tenant = get_post($tenant_id);
        
        if (!$tenant || 'vmp_tenant' !== $tenant->post_type) {
            return new WP_Error('rest_tenant_invalid_id', __('Invalid tenant ID.', 'plughaus-property'), array('status' => 404));
        }
        
        return rest_ensure_response(self::prepare_tenant_response($tenant_id));
    }
    
    public static function create_tenant($request) {
        $tenant_data = array(
            'post_type' => 'vmp_tenant',
            'post_title' => sanitize_text_field($request->get_param('first_name') . ' ' . $request->get_param('last_name')),
            'post_status' => 'publish',
        );
        
        $tenant_id = wp_insert_post($tenant_data, true);
        
        if (is_wp_error($tenant_id)) {
            return $tenant_id;
        }
        
        // Update meta fields
        if ($first_name = $request->get_param('first_name')) {
            update_post_meta($tenant_id, '_vmp_tenant_first_name', sanitize_text_field($first_name));
        }
        
        if ($last_name = $request->get_param('last_name')) {
            update_post_meta($tenant_id, '_vmp_tenant_last_name', sanitize_text_field($last_name));
        }
        
        if ($email = $request->get_param('email')) {
            update_post_meta($tenant_id, '_vmp_tenant_email', sanitize_email($email));
        }
        
        if ($phone = $request->get_param('phone')) {
            update_post_meta($tenant_id, '_vmp_tenant_phone', sanitize_text_field($phone));
        }
        
        return rest_ensure_response(self::prepare_tenant_response($tenant_id));
    }
    
    public static function update_tenant($request) {
        $tenant_id = $request->get_param('id');
        $tenant = get_post($tenant_id);
        
        if (!$tenant || 'vmp_tenant' !== $tenant->post_type) {
            return new WP_Error('rest_tenant_invalid_id', __('Invalid tenant ID.', 'plughaus-property'), array('status' => 404));
        }
        
        $tenant_data = array('ID' => $tenant_id);
        
        $first_name = $request->get_param('first_name');
        $last_name = $request->get_param('last_name');
        
        if ($first_name || $last_name) {
            $current_first = get_post_meta($tenant_id, '_vmp_tenant_first_name', true);
            $current_last = get_post_meta($tenant_id, '_vmp_tenant_last_name', true);
            $new_first = $first_name ?: $current_first;
            $new_last = $last_name ?: $current_last;
            $tenant_data['post_title'] = sanitize_text_field($new_first . ' ' . $new_last);
        }
        
        $updated = wp_update_post($tenant_data, true);
        
        if (is_wp_error($updated)) {
            return $updated;
        }
        
        // Update meta fields
        $meta_fields = array('first_name', 'last_name', 'email', 'phone');
        
        foreach ($meta_fields as $field) {
            if ($value = $request->get_param($field)) {
                $sanitized_value = $field === 'email' ? sanitize_email($value) : sanitize_text_field($value);
                update_post_meta($tenant_id, '_vmp_tenant_' . $field, $sanitized_value);
            }
        }
        
        return rest_ensure_response(self::prepare_tenant_response($tenant_id));
    }
    
    public static function delete_tenant($request) {
        $tenant_id = $request->get_param('id');
        $tenant = get_post($tenant_id);
        
        if (!$tenant || 'vmp_tenant' !== $tenant->post_type) {
            return new WP_Error('rest_tenant_invalid_id', __('Invalid tenant ID.', 'plughaus-property'), array('status' => 404));
        }
        
        $deleted = wp_delete_post($tenant_id, true);
        
        if (!$deleted) {
            return new WP_Error('rest_cannot_delete', __('The tenant cannot be deleted.', 'plughaus-property'), array('status' => 500));
        }
        
        return rest_ensure_response(array('deleted' => true));
    }
    
    public static function get_leases($request) {
        $args = array(
            'post_type' => 'vmp_lease',
            'posts_per_page' => $request->get_param('per_page') ?: 10,
            'paged' => $request->get_param('page') ?: 1,
            'orderby' => $request->get_param('orderby') ?: 'date',
            'order' => $request->get_param('order') ?: 'DESC',
        );
        
        // Filter by property
        if ($property_id = $request->get_param('property')) {
            $args['meta_query'][] = array(
                'key' => '_vmp_lease_property',
                'value' => $property_id,
                'compare' => '=',
            );
        }
        
        // Filter by tenant
        if ($tenant_id = $request->get_param('tenant')) {
            $args['meta_query'][] = array(
                'key' => '_vmp_lease_tenant',
                'value' => $tenant_id,
                'compare' => '=',
            );
        }
        
        $query = new WP_Query($args);
        
        $leases = array();
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $leases[] = self::prepare_lease_response(get_the_ID());
            }
        }
        
        wp_reset_postdata();
        
        $response = rest_ensure_response($leases);
        
        // Add pagination headers
        $response->header('X-WP-Total', $query->found_posts);
        $response->header('X-WP-TotalPages', $query->max_num_pages);
        
        return $response;
    }
    
    public static function create_lease($request) {
        $lease_data = array(
            'post_type' => 'vmp_lease',
            'post_title' => sprintf(__('Lease for %s - %s', 'plughaus-property'), 
                $request->get_param('start_date'),
                $request->get_param('end_date')
            ),
            'post_status' => 'publish',
        );
        
        $lease_id = wp_insert_post($lease_data, true);
        
        if (is_wp_error($lease_id)) {
            return $lease_id;
        }
        
        // Update meta fields
        $meta_fields = array('unit_id', 'tenant_id', 'start_date', 'end_date', 'rent_amount', 'security_deposit');
        
        foreach ($meta_fields as $field) {
            if ($value = $request->get_param($field)) {
                $meta_key = '_vmp_lease_' . $field;
                $sanitized_value = in_array($field, array('unit_id', 'tenant_id')) ? intval($value) : 
                                  (in_array($field, array('rent_amount', 'security_deposit')) ? floatval($value) : 
                                  sanitize_text_field($value));
                update_post_meta($lease_id, $meta_key, $sanitized_value);
            }
        }
        
        return rest_ensure_response(self::prepare_lease_response($lease_id));
    }
    
    public static function get_maintenance_requests($request) {
        $args = array(
            'post_type' => 'vmp_maintenance',
            'posts_per_page' => $request->get_param('per_page') ?: 10,
            'paged' => $request->get_param('page') ?: 1,
            'orderby' => $request->get_param('orderby') ?: 'date',
            'order' => $request->get_param('order') ?: 'DESC',
        );
        
        // Filter by property
        if ($property_id = $request->get_param('property')) {
            $args['meta_query'][] = array(
                'key' => '_vmp_maintenance_property',
                'value' => $property_id,
                'compare' => '=',
            );
        }
        
        // Filter by status
        if ($status = $request->get_param('status')) {
            $args['meta_query'][] = array(
                'key' => '_vmp_maintenance_status',
                'value' => $status,
                'compare' => '=',
            );
        }
        
        $query = new WP_Query($args);
        
        $requests = array();
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $requests[] = self::prepare_maintenance_response(get_the_ID());
            }
        }
        
        wp_reset_postdata();
        
        $response = rest_ensure_response($requests);
        
        // Add pagination headers
        $response->header('X-WP-Total', $query->found_posts);
        $response->header('X-WP-TotalPages', $query->max_num_pages);
        
        return $response;
    }
    
    public static function create_maintenance_request($request) {
        $maintenance_data = array(
            'post_type' => 'vmp_maintenance',
            'post_title' => sanitize_text_field($request->get_param('title')),
            'post_content' => wp_kses_post($request->get_param('description')),
            'post_status' => 'publish',
        );
        
        $maintenance_id = wp_insert_post($maintenance_data, true);
        
        if (is_wp_error($maintenance_id)) {
            return $maintenance_id;
        }
        
        // Update meta fields
        if ($unit_id = $request->get_param('unit_id')) {
            update_post_meta($maintenance_id, '_vmp_maintenance_unit', intval($unit_id));
        }
        
        if ($priority = $request->get_param('priority')) {
            update_post_meta($maintenance_id, '_vmp_maintenance_priority', sanitize_text_field($priority));
        }
        
        if ($category = $request->get_param('category')) {
            update_post_meta($maintenance_id, '_vmp_maintenance_category', sanitize_text_field($category));
        }
        
        // Set default status
        update_post_meta($maintenance_id, '_vmp_maintenance_status', 'open');
        
        return rest_ensure_response(self::prepare_maintenance_response($maintenance_id));
    }
    
    public static function get_occupancy_report($request) {
        // Get all properties
        $properties = get_posts(array(
            'post_type' => 'vmp_property',
            'post_status' => 'publish',
            'posts_per_page' => -1
        ));
        
        $report = array(
            'total_properties' => count($properties),
            'total_units' => 0,
            'occupied_units' => 0,
            'available_units' => 0,
            'occupancy_rate' => 0,
            'properties' => array()
        );
        
        foreach ($properties as $property) {
            $units = get_posts(array(
                'post_type' => 'vmp_unit',
                'post_status' => 'publish',
                'meta_query' => array(
                    array(
                        'key' => '_vmp_unit_property',
                        'value' => $property->ID,
                        'compare' => '='
                    )
                ),
                'posts_per_page' => -1
            ));
            
            $property_data = array(
                'id' => $property->ID,
                'name' => $property->post_title,
                'total_units' => count($units),
                'occupied_units' => 0,
                'available_units' => 0
            );
            
            foreach ($units as $unit) {
                $status = get_post_meta($unit->ID, '_vmp_unit_status', true);
                if ($status === 'occupied') {
                    $property_data['occupied_units']++;
                } else {
                    $property_data['available_units']++;
                }
            }
            
            $property_data['occupancy_rate'] = $property_data['total_units'] > 0 ? 
                round(($property_data['occupied_units'] / $property_data['total_units']) * 100, 2) : 0;
            
            $report['total_units'] += $property_data['total_units'];
            $report['occupied_units'] += $property_data['occupied_units'];
            $report['available_units'] += $property_data['available_units'];
            $report['properties'][] = $property_data;
        }
        
        $report['occupancy_rate'] = $report['total_units'] > 0 ? 
            round(($report['occupied_units'] / $report['total_units']) * 100, 2) : 0;
        
        return rest_ensure_response($report);
    }
    
    public static function get_financial_report($request) {
        $start_date = $request->get_param('start_date') ?: date('Y-m-01');
        $end_date = $request->get_param('end_date') ?: date('Y-m-t');
        
        // Get active leases
        $leases = get_posts(array(
            'post_type' => 'vmp_lease',
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => '_vmp_lease_status',
                    'value' => 'active',
                    'compare' => '='
                )
            ),
            'posts_per_page' => -1
        ));
        
        $report = array(
            'period' => array(
                'start_date' => $start_date,
                'end_date' => $end_date
            ),
            'total_rental_income' => 0,
            'total_expenses' => 0,
            'net_income' => 0,
            'active_leases' => count($leases),
            'properties' => array()
        );
        
        // Calculate rental income from active leases
        foreach ($leases as $lease) {
            $rent_amount = floatval(get_post_meta($lease->ID, '_vmp_lease_rent_amount', true));
            $property_id = get_post_meta($lease->ID, '_vmp_lease_property', true);
            
            $report['total_rental_income'] += $rent_amount;
            
            if (!isset($report['properties'][$property_id])) {
                $property = get_post($property_id);
                $report['properties'][$property_id] = array(
                    'id' => $property_id,
                    'name' => $property ? $property->post_title : 'Unknown Property',
                    'rental_income' => 0,
                    'expenses' => 0,
                    'active_leases' => 0
                );
            }
            
            $report['properties'][$property_id]['rental_income'] += $rent_amount;
            $report['properties'][$property_id]['active_leases']++;
        }
        
        // Note: In a real implementation, you would also calculate expenses from maintenance, utilities, etc.
        // For now, we'll set expenses to 0
        $report['total_expenses'] = 0;
        $report['net_income'] = $report['total_rental_income'] - $report['total_expenses'];
        
        // Convert properties array to indexed array
        $report['properties'] = array_values($report['properties']);
        
        return rest_ensure_response($report);
    }
    
    /**
     * Prepare tenant response
     */
    private static function prepare_tenant_response($tenant_id) {
        $tenant = get_post($tenant_id);
        
        $response = array(
            'id' => $tenant->ID,
            'title' => $tenant->post_title,
            'status' => $tenant->post_status,
            'first_name' => get_post_meta($tenant->ID, '_vmp_tenant_first_name', true),
            'last_name' => get_post_meta($tenant->ID, '_vmp_tenant_last_name', true),
            'email' => get_post_meta($tenant->ID, '_vmp_tenant_email', true),
            'phone' => get_post_meta($tenant->ID, '_vmp_tenant_phone', true),
            'move_in_date' => get_post_meta($tenant->ID, '_vmp_tenant_move_in_date', true),
            'emergency_contact' => array(
                'name' => get_post_meta($tenant->ID, '_vmp_tenant_emergency_name', true),
                'phone' => get_post_meta($tenant->ID, '_vmp_tenant_emergency_phone', true),
                'relationship' => get_post_meta($tenant->ID, '_vmp_tenant_emergency_relationship', true),
            ),
            'date_created' => $tenant->post_date,
            'date_modified' => $tenant->post_modified,
        );
        
        return $response;
    }
    
    /**
     * Prepare lease response
     */
    private static function prepare_lease_response($lease_id) {
        $lease = get_post($lease_id);
        
        $response = array(
            'id' => $lease->ID,
            'title' => $lease->post_title,
            'status' => $lease->post_status,
            'unit_id' => get_post_meta($lease->ID, '_vmp_lease_unit_id', true),
            'tenant_id' => get_post_meta($lease->ID, '_vmp_lease_tenant_id', true),
            'property_id' => get_post_meta($lease->ID, '_vmp_lease_property_id', true),
            'start_date' => get_post_meta($lease->ID, '_vmp_lease_start_date', true),
            'end_date' => get_post_meta($lease->ID, '_vmp_lease_end_date', true),
            'rent_amount' => get_post_meta($lease->ID, '_vmp_lease_rent_amount', true),
            'security_deposit' => get_post_meta($lease->ID, '_vmp_lease_security_deposit', true),
            'lease_status' => get_post_meta($lease->ID, '_vmp_lease_status', true),
            'date_created' => $lease->post_date,
            'date_modified' => $lease->post_modified,
        );
        
        return $response;
    }
    
    /**
     * Prepare maintenance response
     */
    private static function prepare_maintenance_response($maintenance_id) {
        $maintenance = get_post($maintenance_id);
        
        $response = array(
            'id' => $maintenance->ID,
            'title' => $maintenance->post_title,
            'description' => $maintenance->post_content,
            'status' => $maintenance->post_status,
            'unit_id' => get_post_meta($maintenance->ID, '_vmp_maintenance_unit', true),
            'property_id' => get_post_meta($maintenance->ID, '_vmp_maintenance_property', true),
            'priority' => get_post_meta($maintenance->ID, '_vmp_maintenance_priority', true),
            'category' => get_post_meta($maintenance->ID, '_vmp_maintenance_category', true),
            'maintenance_status' => get_post_meta($maintenance->ID, '_vmp_maintenance_status', true),
            'estimated_cost' => get_post_meta($maintenance->ID, '_vmp_maintenance_estimated_cost', true),
            'actual_cost' => get_post_meta($maintenance->ID, '_vmp_maintenance_actual_cost', true),
            'scheduled_date' => get_post_meta($maintenance->ID, '_vmp_maintenance_scheduled_date', true),
            'completed_date' => get_post_meta($maintenance->ID, '_vmp_maintenance_completed_date', true),
            'date_created' => $maintenance->post_date,
            'date_modified' => $maintenance->post_modified,
        );
        
        return $response;
    }
}