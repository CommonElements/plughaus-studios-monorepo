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
    const NAMESPACE = 'phpm/v1';
    
    /**
     * Register REST API routes
     */
    public static function register_routes() {
        // Check if API is enabled
        $options = get_option('phpm_settings');
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
            'post_type' => 'phpm_property',
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
                'taxonomy' => 'phpm_property_type',
                'field' => 'slug',
                'terms' => $type,
            );
        }
        
        if ($location = $request->get_param('location')) {
            $args['tax_query'][] = array(
                'taxonomy' => 'phpm_location',
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
        
        if (!$property || 'phpm_property' !== $property->post_type) {
            return new WP_Error('rest_property_invalid_id', __('Invalid property ID.', 'plughaus-property'), array('status' => 404));
        }
        
        return rest_ensure_response(self::prepare_property_response($property_id));
    }
    
    /**
     * Create property
     */
    public static function create_property($request) {
        $property_data = array(
            'post_type' => 'phpm_property',
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
            update_post_meta($property_id, '_phpm_property_address', sanitize_text_field($address));
        }
        
        if ($city = $request->get_param('city')) {
            update_post_meta($property_id, '_phpm_property_city', sanitize_text_field($city));
        }
        
        if ($state = $request->get_param('state')) {
            update_post_meta($property_id, '_phpm_property_state', sanitize_text_field($state));
        }
        
        if ($zip = $request->get_param('zip')) {
            update_post_meta($property_id, '_phpm_property_zip', sanitize_text_field($zip));
        }
        
        if ($units = $request->get_param('units')) {
            update_post_meta($property_id, '_phpm_property_units', intval($units));
        }
        
        // Set taxonomies
        if ($types = $request->get_param('property_types')) {
            wp_set_object_terms($property_id, $types, 'phpm_property_type');
        }
        
        if ($amenities = $request->get_param('amenities')) {
            wp_set_object_terms($property_id, $amenities, 'phpm_amenities');
        }
        
        return rest_ensure_response(self::prepare_property_response($property_id));
    }
    
    /**
     * Update property
     */
    public static function update_property($request) {
        $property_id = $request->get_param('id');
        $property = get_post($property_id);
        
        if (!$property || 'phpm_property' !== $property->post_type) {
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
                update_post_meta($property_id, '_phpm_property_' . $field, sanitize_text_field($value));
            }
        }
        
        // Update taxonomies
        if ($types = $request->get_param('property_types')) {
            wp_set_object_terms($property_id, $types, 'phpm_property_type');
        }
        
        if ($amenities = $request->get_param('amenities')) {
            wp_set_object_terms($property_id, $amenities, 'phpm_amenities');
        }
        
        return rest_ensure_response(self::prepare_property_response($property_id));
    }
    
    /**
     * Delete property
     */
    public static function delete_property($request) {
        $property_id = $request->get_param('id');
        $property = get_post($property_id);
        
        if (!$property || 'phpm_property' !== $property->post_type) {
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
            'address' => get_post_meta($property->ID, '_phpm_property_address', true),
            'city' => get_post_meta($property->ID, '_phpm_property_city', true),
            'state' => get_post_meta($property->ID, '_phpm_property_state', true),
            'zip' => get_post_meta($property->ID, '_phpm_property_zip', true),
            'units' => get_post_meta($property->ID, '_phpm_property_units', true),
            'property_types' => wp_get_post_terms($property->ID, 'phpm_property_type', array('fields' => 'names')),
            'amenities' => wp_get_post_terms($property->ID, 'phpm_amenities', array('fields' => 'names')),
            'location' => wp_get_post_terms($property->ID, 'phpm_location', array('fields' => 'names')),
        );
        
        return $response;
    }
    
    /**
     * Get units
     */
    public static function get_units($request) {
        $args = array(
            'post_type' => 'phpm_unit',
            'posts_per_page' => $request->get_param('per_page') ?: 10,
            'paged' => $request->get_param('page') ?: 1,
        );
        
        // Filter by property
        if ($property_id = $request->get_param('property')) {
            $args['meta_query'][] = array(
                'key' => '_phpm_unit_property',
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
            'property_id' => get_post_meta($unit->ID, '_phpm_unit_property', true),
            'rent' => get_post_meta($unit->ID, '_phpm_unit_rent', true),
            'bedrooms' => get_post_meta($unit->ID, '_phpm_unit_bedrooms', true),
            'bathrooms' => get_post_meta($unit->ID, '_phpm_unit_bathrooms', true),
            'sqft' => get_post_meta($unit->ID, '_phpm_unit_sqft', true),
            'amenities' => wp_get_post_terms($unit->ID, 'phpm_amenities', array('fields' => 'names')),
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
        // Implementation
    }
    
    public static function create_unit($request) {
        // Implementation
    }
    
    public static function update_unit($request) {
        // Implementation
    }
    
    public static function delete_unit($request) {
        // Implementation
    }
    
    public static function get_tenants($request) {
        // Implementation
    }
    
    public static function get_tenant($request) {
        // Implementation
    }
    
    public static function create_tenant($request) {
        // Implementation
    }
    
    public static function update_tenant($request) {
        // Implementation
    }
    
    public static function delete_tenant($request) {
        // Implementation
    }
    
    public static function get_leases($request) {
        // Implementation
    }
    
    public static function create_lease($request) {
        // Implementation
    }
    
    public static function get_maintenance_requests($request) {
        // Implementation
    }
    
    public static function create_maintenance_request($request) {
        // Implementation
    }
    
    public static function get_occupancy_report($request) {
        // Implementation
    }
    
    public static function get_financial_report($request) {
        // Implementation
    }
}