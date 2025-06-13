<?php
/**
 * Sample Data Generator for PlugHaus Property Management
 * Creates demo content to help new users understand plugin capabilities
 */

if (!defined('ABSPATH')) {
    exit;
}

class PHPM_Sample_Data {
    
    /**
     * Initialize sample data hooks
     */
    public static function init() {
        add_action('wp_ajax_phpm_install_sample_data', array(__CLASS__, 'ajax_install_sample_data'));
        add_action('wp_ajax_phpm_remove_sample_data', array(__CLASS__, 'ajax_remove_sample_data'));
        add_action('wp_ajax_phpm_check_sample_data', array(__CLASS__, 'ajax_check_sample_data'));
    }
    
    /**
     * Install sample data
     */
    public static function install_sample_data() {
        // Prevent installation if data already exists
        if (self::sample_data_exists()) {
            return new WP_Error('sample_data_exists', __('Sample data already exists. Please remove existing sample data first.', 'plughaus-property'));
        }
        
        $results = array(
            'properties' => array(),
            'units' => array(),
            'tenants' => array(),
            'leases' => array(),
            'maintenance' => array()
        );
        
        try {
            // Create properties first
            $results['properties'] = self::create_sample_properties();
            
            // Create units for each property
            $results['units'] = self::create_sample_units($results['properties']);
            
            // Create tenants
            $results['tenants'] = self::create_sample_tenants();
            
            // Create leases linking tenants to units
            $results['leases'] = self::create_sample_leases($results['units'], $results['tenants']);
            
            // Create maintenance requests
            $results['maintenance'] = self::create_sample_maintenance($results['properties'], $results['units']);
            
            // Mark sample data as installed
            update_option('phpm_sample_data_installed', true);
            update_option('phpm_sample_data_timestamp', current_time('timestamp'));
            update_option('phpm_sample_data_ids', $results);
            
            return $results;
            
        } catch (Exception $e) {
            // Clean up on error
            self::cleanup_partial_installation($results);
            return new WP_Error('sample_data_error', $e->getMessage());
        }
    }
    
    /**
     * Check if sample data exists
     */
    public static function sample_data_exists() {
        return get_option('phpm_sample_data_installed', false);
    }
    
    /**
     * Create sample properties
     */
    private static function create_sample_properties() {
        $properties_data = array(
            array(
                'title' => 'Sunset Apartments',
                'description' => 'Modern apartment complex with stunning sunset views. Perfect for young professionals and small families.',
                'address' => '123 Sunset Boulevard',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'zip' => '90028',
                'units' => 12,
                'year_built' => 2018,
                'type' => 'apartment',
                'amenities' => array('Pool', 'Gym', 'Parking', 'Laundry')
            ),
            array(
                'title' => 'Downtown Lofts',
                'description' => 'Historic building converted to luxury lofts in the heart of downtown. High ceilings and exposed brick.',
                'address' => '456 Main Street',
                'city' => 'Portland',
                'state' => 'OR',
                'zip' => '97205',
                'units' => 8,
                'year_built' => 1920,
                'type' => 'loft',
                'amenities' => array('Historic Building', 'High Ceilings', 'Exposed Brick', 'Downtown Location')
            ),
            array(
                'title' => 'Greenfield Townhomes',
                'description' => 'Family-friendly townhomes with private yards and attached garages. Great schools nearby.',
                'address' => '789 Greenfield Drive',
                'city' => 'Austin',
                'state' => 'TX',
                'zip' => '78704',
                'units' => 6,
                'year_built' => 2020,
                'type' => 'townhouse',
                'amenities' => array('Private Yard', 'Garage', 'Great Schools', 'Family Friendly')
            ),
            array(
                'title' => 'Oceanview Condos',
                'description' => 'Luxury beachfront condominiums with panoramic ocean views and resort-style amenities.',
                'address' => '321 Ocean Drive',
                'city' => 'Miami',
                'state' => 'FL',
                'zip' => '33139',
                'units' => 15,
                'year_built' => 2019,
                'type' => 'condo',
                'amenities' => array('Ocean View', 'Beach Access', 'Concierge', 'Valet Parking')
            )
        );
        
        $property_ids = array();
        
        foreach ($properties_data as $property_data) {
            $property_id = wp_insert_post(array(
                'post_type' => 'phpm_property',
                'post_title' => $property_data['title'],
                'post_content' => $property_data['description'],
                'post_status' => 'publish',
                'meta_input' => array(
                    '_phpm_property_address' => $property_data['address'],
                    '_phpm_property_city' => $property_data['city'],
                    '_phpm_property_state' => $property_data['state'],
                    '_phpm_property_zip' => $property_data['zip'],
                    '_phpm_property_units' => $property_data['units'],
                    '_phpm_property_year_built' => $property_data['year_built'],
                    '_phpm_sample_data' => true
                )
            ));
            
            if (!is_wp_error($property_id)) {
                // Set property type
                wp_set_object_terms($property_id, $property_data['type'], 'phpm_property_type');
                
                // Set amenities
                wp_set_object_terms($property_id, $property_data['amenities'], 'phpm_amenities');
                
                $property_ids[] = $property_id;
            }
        }
        
        return $property_ids;
    }
    
    /**
     * Create sample units
     */
    private static function create_sample_units($property_ids) {
        $unit_templates = array(
            array('bedrooms' => 1, 'bathrooms' => 1, 'sqft' => 650, 'rent' => 1500),
            array('bedrooms' => 1, 'bathrooms' => 1, 'sqft' => 700, 'rent' => 1600),
            array('bedrooms' => 2, 'bathrooms' => 1, 'sqft' => 900, 'rent' => 2000),
            array('bedrooms' => 2, 'bathrooms' => 2, 'sqft' => 1100, 'rent' => 2400),
            array('bedrooms' => 3, 'bathrooms' => 2, 'sqft' => 1300, 'rent' => 2800),
            array('bedrooms' => 3, 'bathrooms' => 2.5, 'sqft' => 1450, 'rent' => 3200)
        );
        
        $unit_ids = array();
        
        foreach ($property_ids as $property_id) {
            $property = get_post($property_id);
            $units_count = get_post_meta($property_id, '_phpm_property_units', true);
            
            for ($i = 1; $i <= $units_count; $i++) {
                $template = $unit_templates[array_rand($unit_templates)];
                
                // Adjust rent based on property location
                $rent_multiplier = self::get_rent_multiplier($property_id);
                $adjusted_rent = round($template['rent'] * $rent_multiplier);
                
                $unit_number = self::format_unit_number($i, $units_count);
                
                $unit_id = wp_insert_post(array(
                    'post_type' => 'phpm_unit',
                    'post_title' => $property->post_title . ' - Unit ' . $unit_number,
                    'post_content' => sprintf(__('%d bedroom, %s bathroom unit with %d sq ft.', 'plughaus-property'), 
                        $template['bedrooms'], 
                        $template['bathrooms'], 
                        $template['sqft']
                    ),
                    'post_status' => 'publish',
                    'meta_input' => array(
                        '_phpm_unit_property_id' => $property_id,
                        '_phpm_unit_number' => $unit_number,
                        '_phpm_unit_bedrooms' => $template['bedrooms'],
                        '_phpm_unit_bathrooms' => $template['bathrooms'],
                        '_phpm_unit_square_feet' => $template['sqft'],
                        '_phpm_unit_rent' => $adjusted_rent,
                        '_phpm_unit_status' => rand(0, 3) ? 'available' : 'occupied', // 75% available
                        '_phpm_sample_data' => true
                    )
                ));
                
                if (!is_wp_error($unit_id)) {
                    $unit_ids[] = $unit_id;
                }
            }
        }
        
        return $unit_ids;
    }
    
    /**
     * Create sample tenants
     */
    private static function create_sample_tenants() {
        $tenants_data = array(
            array('first' => 'John', 'last' => 'Smith', 'email' => 'john.smith@email.com', 'phone' => '(555) 123-4567'),
            array('first' => 'Sarah', 'last' => 'Johnson', 'email' => 'sarah.johnson@email.com', 'phone' => '(555) 234-5678'),
            array('first' => 'Michael', 'last' => 'Brown', 'email' => 'michael.brown@email.com', 'phone' => '(555) 345-6789'),
            array('first' => 'Emily', 'last' => 'Davis', 'email' => 'emily.davis@email.com', 'phone' => '(555) 456-7890'),
            array('first' => 'David', 'last' => 'Wilson', 'email' => 'david.wilson@email.com', 'phone' => '(555) 567-8901'),
            array('first' => 'Lisa', 'last' => 'Garcia', 'email' => 'lisa.garcia@email.com', 'phone' => '(555) 678-9012'),
            array('first' => 'James', 'last' => 'Martinez', 'email' => 'james.martinez@email.com', 'phone' => '(555) 789-0123'),
            array('first' => 'Jennifer', 'last' => 'Anderson', 'email' => 'jennifer.anderson@email.com', 'phone' => '(555) 890-1234'),
            array('first' => 'Robert', 'last' => 'Taylor', 'email' => 'robert.taylor@email.com', 'phone' => '(555) 901-2345'),
            array('first' => 'Amanda', 'last' => 'Thomas', 'email' => 'amanda.thomas@email.com', 'phone' => '(555) 012-3456')
        );
        
        $tenant_ids = array();
        
        foreach ($tenants_data as $tenant_data) {
            $move_in_date = date('Y-m-d', strtotime('-' . rand(30, 365) . ' days'));
            
            $tenant_id = wp_insert_post(array(
                'post_type' => 'phpm_tenant',
                'post_title' => $tenant_data['first'] . ' ' . $tenant_data['last'],
                'post_content' => sprintf(__('Tenant record for %s %s.', 'plughaus-property'), 
                    $tenant_data['first'], 
                    $tenant_data['last']
                ),
                'post_status' => 'publish',
                'meta_input' => array(
                    '_phpm_tenant_first_name' => $tenant_data['first'],
                    '_phpm_tenant_last_name' => $tenant_data['last'],
                    '_phpm_tenant_email' => $tenant_data['email'],
                    '_phpm_tenant_phone' => $tenant_data['phone'],
                    '_phpm_tenant_move_in_date' => $move_in_date,
                    '_phpm_tenant_status' => 'current',
                    '_phpm_tenant_emergency_contact' => 'Emergency Contact',
                    '_phpm_tenant_emergency_phone' => '(555) 999-8888',
                    '_phpm_sample_data' => true
                )
            ));
            
            if (!is_wp_error($tenant_id)) {
                $tenant_ids[] = $tenant_id;
            }
        }
        
        return $tenant_ids;
    }
    
    /**
     * Create sample leases
     */
    private static function create_sample_leases($unit_ids, $tenant_ids) {
        $lease_ids = array();
        $used_tenants = array();
        
        // Create leases for occupied units
        foreach ($unit_ids as $unit_id) {
            $unit_status = get_post_meta($unit_id, '_phpm_unit_status', true);
            
            if ($unit_status === 'occupied' && !empty($tenant_ids)) {
                // Find an unused tenant
                $available_tenants = array_diff($tenant_ids, $used_tenants);
                if (empty($available_tenants)) {
                    break; // No more tenants available
                }
                
                $tenant_id = array_shift($available_tenants);
                $used_tenants[] = $tenant_id;
                
                $property_id = get_post_meta($unit_id, '_phpm_unit_property_id', true);
                $rent_amount = get_post_meta($unit_id, '_phpm_unit_rent', true);
                
                // Generate lease dates
                $start_date = date('Y-m-d', strtotime('-' . rand(30, 300) . ' days'));
                $end_date = date('Y-m-d', strtotime($start_date . ' +1 year'));
                
                $unit = get_post($unit_id);
                $tenant = get_post($tenant_id);
                
                $lease_id = wp_insert_post(array(
                    'post_type' => 'phpm_lease',
                    'post_title' => sprintf(__('Lease: %s - %s', 'plughaus-property'), 
                        $unit->post_title, 
                        $tenant->post_title
                    ),
                    'post_content' => sprintf(__('Lease agreement between %s and property management.', 'plughaus-property'), 
                        $tenant->post_title
                    ),
                    'post_status' => 'publish',
                    'meta_input' => array(
                        '_phpm_lease_property_id' => $property_id,
                        '_phpm_lease_unit_id' => $unit_id,
                        '_phpm_lease_tenant_id' => $tenant_id,
                        '_phpm_lease_start_date' => $start_date,
                        '_phpm_lease_end_date' => $end_date,
                        '_phpm_lease_rent_amount' => $rent_amount,
                        '_phpm_lease_security_deposit' => $rent_amount * 1.5, // 1.5x rent
                        '_phpm_lease_status' => 'active',
                        '_phpm_lease_term_months' => 12,
                        '_phpm_sample_data' => true
                    )
                ));
                
                if (!is_wp_error($lease_id)) {
                    $lease_ids[] = $lease_id;
                }
            }
        }
        
        return $lease_ids;
    }
    
    /**
     * Create sample maintenance requests
     */
    private static function create_sample_maintenance($property_ids, $unit_ids) {
        $maintenance_templates = array(
            array(
                'title' => 'Leaky Kitchen Faucet',
                'description' => 'The kitchen faucet has been dripping constantly. Needs repair or replacement.',
                'priority' => 'medium',
                'category' => 'Plumbing',
                'status' => 'open'
            ),
            array(
                'title' => 'Air Conditioning Not Working',
                'description' => 'AC unit stopped working yesterday. Very hot in the apartment.',
                'priority' => 'high',
                'category' => 'HVAC',
                'status' => 'in_progress'
            ),
            array(
                'title' => 'Burned Out Light Bulb',
                'description' => 'Light bulb in hallway ceiling fixture needs replacement.',
                'priority' => 'low',
                'category' => 'Electrical',
                'status' => 'completed'
            ),
            array(
                'title' => 'Squeaky Door Hinge',
                'description' => 'Bedroom door hinge squeaks loudly when opening/closing.',
                'priority' => 'low',
                'category' => 'General Maintenance',
                'status' => 'open'
            ),
            array(
                'title' => 'Clogged Bathroom Drain',
                'description' => 'Bathroom sink drains very slowly, possibly clogged.',
                'priority' => 'medium',
                'category' => 'Plumbing',
                'status' => 'in_progress'
            ),
            array(
                'title' => 'Window Won\'t Close Properly',
                'description' => 'Living room window is stuck and won\'t close all the way.',
                'priority' => 'medium',
                'category' => 'General Maintenance',
                'status' => 'open'
            )
        );
        
        $maintenance_ids = array();
        
        foreach ($maintenance_templates as $template) {
            // Randomly assign to a unit or just a property
            if (rand(0, 1) && !empty($unit_ids)) {
                $unit_id = $unit_ids[array_rand($unit_ids)];
                $property_id = get_post_meta($unit_id, '_phpm_unit_property_id', true);
            } else {
                $unit_id = null;
                $property_id = $property_ids[array_rand($property_ids)];
            }
            
            $request_date = date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days'));
            
            $maintenance_id = wp_insert_post(array(
                'post_type' => 'phpm_maintenance',
                'post_title' => $template['title'],
                'post_content' => $template['description'],
                'post_status' => 'publish',
                'post_date' => $request_date,
                'meta_input' => array(
                    '_phpm_maintenance_property_id' => $property_id,
                    '_phpm_maintenance_unit_id' => $unit_id,
                    '_phpm_maintenance_priority' => $template['priority'],
                    '_phpm_maintenance_category' => $template['category'],
                    '_phpm_maintenance_status' => $template['status'],
                    '_phpm_maintenance_requested_by' => 'tenant',
                    '_phpm_maintenance_cost' => $template['status'] === 'completed' ? rand(50, 300) : 0,
                    '_phpm_sample_data' => true
                )
            ));
            
            if (!is_wp_error($maintenance_id)) {
                $maintenance_ids[] = $maintenance_id;
            }
        }
        
        return $maintenance_ids;
    }
    
    /**
     * Remove sample data
     */
    public static function remove_sample_data() {
        if (!self::sample_data_exists()) {
            return new WP_Error('no_sample_data', __('No sample data found to remove.', 'plughaus-property'));
        }
        
        // Get all posts with sample data meta
        $post_types = array('phpm_property', 'phpm_unit', 'phpm_tenant', 'phpm_lease', 'phpm_maintenance');
        
        foreach ($post_types as $post_type) {
            $posts = get_posts(array(
                'post_type' => $post_type,
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => '_phpm_sample_data',
                        'value' => true,
                        'compare' => '='
                    )
                ),
                'fields' => 'ids'
            ));
            
            foreach ($posts as $post_id) {
                wp_delete_post($post_id, true);
            }
        }
        
        // Clean up options
        delete_option('phpm_sample_data_installed');
        delete_option('phpm_sample_data_timestamp');
        delete_option('phpm_sample_data_ids');
        
        return true;
    }
    
    /**
     * Get rent multiplier based on property location
     */
    private static function get_rent_multiplier($property_id) {
        $state = get_post_meta($property_id, '_phpm_property_state', true);
        
        $multipliers = array(
            'CA' => 1.5,  // California - expensive
            'NY' => 1.4,  // New York - expensive
            'FL' => 1.1,  // Florida - moderate
            'TX' => 0.9,  // Texas - affordable
            'OR' => 1.2   // Oregon - moderate-high
        );
        
        return isset($multipliers[$state]) ? $multipliers[$state] : 1.0;
    }
    
    /**
     * Format unit number
     */
    private static function format_unit_number($number, $total_units) {
        if ($total_units > 100) {
            return sprintf('%03d', $number);
        } elseif ($total_units > 10) {
            return sprintf('%02d', $number);
        } else {
            return $number;
        }
    }
    
    /**
     * Clean up partial installation on error
     */
    private static function cleanup_partial_installation($results) {
        foreach ($results as $type => $ids) {
            if (is_array($ids)) {
                foreach ($ids as $id) {
                    wp_delete_post($id, true);
                }
            }
        }
    }
    
    /**
     * AJAX handler for installing sample data
     */
    public static function ajax_install_sample_data() {
        check_ajax_referer('phpm_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Insufficient permissions.', 'plughaus-property'));
        }
        
        $result = self::install_sample_data();
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        $summary = self::get_installation_summary($result);
        
        wp_send_json_success(array(
            'message' => __('Sample data installed successfully!', 'plughaus-property'),
            'summary' => $summary,
            'redirect' => admin_url('edit.php?post_type=phpm_property')
        ));
    }
    
    /**
     * AJAX handler for removing sample data
     */
    public static function ajax_remove_sample_data() {
        check_ajax_referer('phpm_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Insufficient permissions.', 'plughaus-property'));
        }
        
        $result = self::remove_sample_data();
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success(array(
            'message' => __('Sample data removed successfully!', 'plughaus-property')
        ));
    }
    
    /**
     * AJAX handler for checking sample data status
     */
    public static function ajax_check_sample_data() {
        $exists = self::sample_data_exists();
        $timestamp = get_option('phpm_sample_data_timestamp', null);
        
        wp_send_json_success(array(
            'exists' => $exists,
            'installed_date' => $timestamp ? date_i18n(get_option('date_format'), $timestamp) : null
        ));
    }
    
    /**
     * Get installation summary
     */
    private static function get_installation_summary($results) {
        $summary = array();
        
        foreach ($results as $type => $ids) {
            if (is_array($ids)) {
                $count = count($ids);
                $label = ucfirst($type);
                $summary[] = sprintf(_n('%d %s', '%d %s', $count, 'plughaus-property'), $count, $label);
            }
        }
        
        return implode(', ', $summary);
    }
}

// Initialize sample data
PHPM_Sample_Data::init();