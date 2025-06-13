<?php
/**
 * Data validation and relationship management for PlugHaus Property Management
 */

if (!defined('ABSPATH')) {
    exit;
}

class PHPM_Data_Validation {
    
    /**
     * Initialize data validation hooks
     */
    public static function init() {
        // Pre-save validation hooks
        add_action('save_post_phpm_property', array(__CLASS__, 'validate_property_data'), 10, 3);
        add_action('save_post_phpm_unit', array(__CLASS__, 'validate_unit_data'), 10, 3);
        add_action('save_post_phpm_tenant', array(__CLASS__, 'validate_tenant_data'), 10, 3);
        add_action('save_post_phpm_lease', array(__CLASS__, 'validate_lease_data'), 10, 3);
        add_action('save_post_phpm_maintenance', array(__CLASS__, 'validate_maintenance_data'), 10, 3);
        
        // Relationship management hooks
        add_action('save_post_phpm_lease', array(__CLASS__, 'update_unit_occupancy'), 20, 3);
        add_action('save_post_phpm_lease', array(__CLASS__, 'update_tenant_status'), 20, 3);
        add_action('before_delete_post', array(__CLASS__, 'handle_post_deletion'));
        
        // Admin notices for validation errors
        add_action('admin_notices', array(__CLASS__, 'display_validation_notices'));
        
        // AJAX validation endpoints
        add_action('wp_ajax_phpm_validate_email', array(__CLASS__, 'ajax_validate_email'));
        add_action('wp_ajax_phpm_validate_lease_dates', array(__CLASS__, 'ajax_validate_lease_dates'));
        add_action('wp_ajax_phpm_check_unit_availability', array(__CLASS__, 'ajax_check_unit_availability'));
        
        // REST API validation
        add_filter('rest_pre_insert_phpm_property', array(__CLASS__, 'validate_property_rest_data'), 10, 2);
        add_filter('rest_pre_insert_phpm_unit', array(__CLASS__, 'validate_unit_rest_data'), 10, 2);
        add_filter('rest_pre_insert_phpm_tenant', array(__CLASS__, 'validate_tenant_rest_data'), 10, 2);
        add_filter('rest_pre_insert_phpm_lease', array(__CLASS__, 'validate_lease_rest_data'), 10, 2);
    }
    
    /**
     * Validate property data
     */
    public static function validate_property_data($post_id, $post, $update) {
        // Skip validation for autosaves and revisions
        if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
            return;
        }
        
        $errors = array();
        
        // Validate address information
        $address = get_post_meta($post_id, '_phpm_property_address', true);
        $city = get_post_meta($post_id, '_phpm_property_city', true);
        $state = get_post_meta($post_id, '_phpm_property_state', true);
        $zip = get_post_meta($post_id, '_phpm_property_zip', true);
        
        if (empty($address)) {
            $errors[] = __('Property address is required.', 'plughaus-property');
        }
        
        if (empty($city)) {
            $errors[] = __('City is required.', 'plughaus-property');
        }
        
        if (empty($state)) {
            $errors[] = __('State is required.', 'plughaus-property');
        }
        
        if (!empty($zip) && !self::validate_zip_code($zip)) {
            $errors[] = __('Invalid ZIP code format.', 'plughaus-property');
        }
        
        // Validate units count
        $units_count = get_post_meta($post_id, '_phpm_property_units', true);
        if (!empty($units_count) && (!is_numeric($units_count) || $units_count < 1)) {
            $errors[] = __('Units count must be a positive number.', 'plughaus-property');
        }
        
        // Validate year built
        $year_built = get_post_meta($post_id, '_phpm_property_year_built', true);
        if (!empty($year_built)) {
            $current_year = date('Y');
            if (!is_numeric($year_built) || $year_built < 1800 || $year_built > $current_year) {
                $errors[] = sprintf(__('Year built must be between 1800 and %d.', 'plughaus-property'), $current_year);
            }
        }
        
        // Check for duplicate properties at same address
        if (!empty($address) && !empty($city) && !empty($state)) {
            $duplicate = self::check_duplicate_property($post_id, $address, $city, $state);
            if ($duplicate) {
                $errors[] = __('A property already exists at this address.', 'plughaus-property');
            }
        }
        
        self::store_validation_errors($post_id, $errors);
    }
    
    /**
     * Validate unit data
     */
    public static function validate_unit_data($post_id, $post, $update) {
        if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
            return;
        }
        
        $errors = array();
        
        // Validate property assignment
        $property_id = get_post_meta($post_id, '_phpm_unit_property_id', true);
        if (empty($property_id)) {
            $errors[] = __('Unit must be assigned to a property.', 'plughaus-property');
        } elseif (!self::property_exists($property_id)) {
            $errors[] = __('Selected property does not exist.', 'plughaus-property');
        }
        
        // Validate unit number
        $unit_number = get_post_meta($post_id, '_phpm_unit_number', true);
        if (empty($unit_number)) {
            $errors[] = __('Unit number is required.', 'plughaus-property');
        } elseif (!empty($property_id)) {
            // Check for duplicate unit numbers within the same property
            $duplicate = self::check_duplicate_unit_number($post_id, $property_id, $unit_number);
            if ($duplicate) {
                $errors[] = __('Unit number already exists for this property.', 'plughaus-property');
            }
        }
        
        // Validate rent amount
        $rent = get_post_meta($post_id, '_phpm_unit_rent', true);
        if (!empty($rent) && (!is_numeric($rent) || $rent < 0)) {
            $errors[] = __('Rent amount must be a positive number.', 'plughaus-property');
        }
        
        // Validate bedroom count
        $bedrooms = get_post_meta($post_id, '_phpm_unit_bedrooms', true);
        if (!empty($bedrooms) && (!is_numeric($bedrooms) || $bedrooms < 0)) {
            $errors[] = __('Number of bedrooms must be a positive number.', 'plughaus-property');
        }
        
        // Validate bathroom count
        $bathrooms = get_post_meta($post_id, '_phpm_unit_bathrooms', true);
        if (!empty($bathrooms) && !is_numeric($bathrooms)) {
            $errors[] = __('Number of bathrooms must be a number.', 'plughaus-property');
        }
        
        // Validate square footage
        $sqft = get_post_meta($post_id, '_phpm_unit_square_feet', true);
        if (!empty($sqft) && (!is_numeric($sqft) || $sqft <= 0)) {
            $errors[] = __('Square footage must be a positive number.', 'plughaus-property');
        }
        
        self::store_validation_errors($post_id, $errors);
    }
    
    /**
     * Validate tenant data
     */
    public static function validate_tenant_data($post_id, $post, $update) {
        if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
            return;
        }
        
        $errors = array();
        
        // Validate required fields
        $first_name = get_post_meta($post_id, '_phpm_tenant_first_name', true);
        $last_name = get_post_meta($post_id, '_phpm_tenant_last_name', true);
        $email = get_post_meta($post_id, '_phpm_tenant_email', true);
        
        if (empty($first_name)) {
            $errors[] = __('First name is required.', 'plughaus-property');
        }
        
        if (empty($last_name)) {
            $errors[] = __('Last name is required.', 'plughaus-property');
        }
        
        if (empty($email)) {
            $errors[] = __('Email address is required.', 'plughaus-property');
        } elseif (!is_email($email)) {
            $errors[] = __('Invalid email address format.', 'plughaus-property');
        } else {
            // Check for duplicate email addresses
            $duplicate = self::check_duplicate_tenant_email($post_id, $email);
            if ($duplicate) {
                $errors[] = __('A tenant with this email address already exists.', 'plughaus-property');
            }
        }
        
        // Validate phone number
        $phone = get_post_meta($post_id, '_phpm_tenant_phone', true);
        if (!empty($phone) && !PHPM_Utilities::validate_phone($phone)) {
            $errors[] = __('Invalid phone number format.', 'plughaus-property');
        }
        
        // Validate emergency contact phone
        $emergency_phone = get_post_meta($post_id, '_phpm_tenant_emergency_phone', true);
        if (!empty($emergency_phone) && !PHPM_Utilities::validate_phone($emergency_phone)) {
            $errors[] = __('Invalid emergency contact phone number format.', 'plughaus-property');
        }
        
        // Validate move-in date
        $move_in_date = get_post_meta($post_id, '_phpm_tenant_move_in_date', true);
        if (!empty($move_in_date) && !self::validate_date($move_in_date)) {
            $errors[] = __('Invalid move-in date format.', 'plughaus-property');
        }
        
        self::store_validation_errors($post_id, $errors);
    }
    
    /**
     * Validate lease data
     */
    public static function validate_lease_data($post_id, $post, $update) {
        if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
            return;
        }
        
        $errors = array();
        
        // Validate required relationships
        $property_id = get_post_meta($post_id, '_phpm_lease_property_id', true);
        $unit_id = get_post_meta($post_id, '_phpm_lease_unit_id', true);
        $tenant_id = get_post_meta($post_id, '_phpm_lease_tenant_id', true);
        
        if (empty($property_id)) {
            $errors[] = __('Property is required for lease.', 'plughaus-property');
        } elseif (!self::property_exists($property_id)) {
            $errors[] = __('Selected property does not exist.', 'plughaus-property');
        }
        
        if (empty($tenant_id)) {
            $errors[] = __('Tenant is required for lease.', 'plughaus-property');
        } elseif (!self::tenant_exists($tenant_id)) {
            $errors[] = __('Selected tenant does not exist.', 'plughaus-property');
        }
        
        if (!empty($unit_id) && !self::unit_exists($unit_id)) {
            $errors[] = __('Selected unit does not exist.', 'plughaus-property');
        }
        
        // Validate unit belongs to property
        if (!empty($unit_id) && !empty($property_id)) {
            $unit_property_id = get_post_meta($unit_id, '_phpm_unit_property_id', true);
            if ($unit_property_id != $property_id) {
                $errors[] = __('Selected unit does not belong to the selected property.', 'plughaus-property');
            }
        }
        
        // Validate lease dates
        $start_date = get_post_meta($post_id, '_phpm_lease_start_date', true);
        $end_date = get_post_meta($post_id, '_phpm_lease_end_date', true);
        
        if (empty($start_date)) {
            $errors[] = __('Lease start date is required.', 'plughaus-property');
        } elseif (!self::validate_date($start_date)) {
            $errors[] = __('Invalid lease start date format.', 'plughaus-property');
        }
        
        if (empty($end_date)) {
            $errors[] = __('Lease end date is required.', 'plughaus-property');
        } elseif (!self::validate_date($end_date)) {
            $errors[] = __('Invalid lease end date format.', 'plughaus-property');
        }
        
        // Validate date relationship
        if (!empty($start_date) && !empty($end_date)) {
            if (strtotime($end_date) <= strtotime($start_date)) {
                $errors[] = __('Lease end date must be after start date.', 'plughaus-property');
            }
        }
        
        // Validate rent amount
        $rent_amount = get_post_meta($post_id, '_phpm_lease_rent_amount', true);
        if (empty($rent_amount)) {
            $errors[] = __('Monthly rent amount is required.', 'plughaus-property');
        } elseif (!is_numeric($rent_amount) || $rent_amount <= 0) {
            $errors[] = __('Rent amount must be a positive number.', 'plughaus-property');
        }
        
        // Validate security deposit
        $security_deposit = get_post_meta($post_id, '_phpm_lease_security_deposit', true);
        if (!empty($security_deposit) && (!is_numeric($security_deposit) || $security_deposit < 0)) {
            $errors[] = __('Security deposit must be a positive number.', 'plughaus-property');
        }
        
        // Check for overlapping leases
        if (!empty($unit_id) && !empty($start_date) && !empty($end_date)) {
            $overlapping = self::check_overlapping_leases($post_id, $unit_id, $start_date, $end_date);
            if ($overlapping) {
                $errors[] = __('Lease dates overlap with an existing lease for this unit.', 'plughaus-property');
            }
        }
        
        self::store_validation_errors($post_id, $errors);
    }
    
    /**
     * Validate maintenance data
     */
    public static function validate_maintenance_data($post_id, $post, $update) {
        if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
            return;
        }
        
        $errors = array();
        
        // Validate property assignment
        $property_id = get_post_meta($post_id, '_phpm_maintenance_property_id', true);
        if (empty($property_id)) {
            $errors[] = __('Property is required for maintenance request.', 'plughaus-property');
        } elseif (!self::property_exists($property_id)) {
            $errors[] = __('Selected property does not exist.', 'plughaus-property');
        }
        
        // Validate unit if specified
        $unit_id = get_post_meta($post_id, '_phpm_maintenance_unit_id', true);
        if (!empty($unit_id)) {
            if (!self::unit_exists($unit_id)) {
                $errors[] = __('Selected unit does not exist.', 'plughaus-property');
            } else {
                // Validate unit belongs to property
                $unit_property_id = get_post_meta($unit_id, '_phpm_unit_property_id', true);
                if ($unit_property_id != $property_id) {
                    $errors[] = __('Selected unit does not belong to the selected property.', 'plughaus-property');
                }
            }
        }
        
        // Validate priority
        $priority = get_post_meta($post_id, '_phpm_maintenance_priority', true);
        if (!empty($priority)) {
            $valid_priorities = array_keys(PHPM_Utilities::get_maintenance_priorities());
            if (!in_array($priority, $valid_priorities)) {
                $errors[] = __('Invalid maintenance priority.', 'plughaus-property');
            }
        }
        
        // Validate status
        $status = get_post_meta($post_id, '_phpm_maintenance_status', true);
        if (!empty($status)) {
            $valid_statuses = array_keys(PHPM_Utilities::get_maintenance_statuses());
            if (!in_array($status, $valid_statuses)) {
                $errors[] = __('Invalid maintenance status.', 'plughaus-property');
            }
        }
        
        self::store_validation_errors($post_id, $errors);
    }
    
    /**
     * Update unit occupancy status when lease is saved
     */
    public static function update_unit_occupancy($post_id, $post, $update) {
        $unit_id = get_post_meta($post_id, '_phpm_lease_unit_id', true);
        $lease_status = get_post_meta($post_id, '_phpm_lease_status', true);
        
        if (!empty($unit_id)) {
            if ($lease_status === 'active') {
                update_post_meta($unit_id, '_phpm_unit_status', 'occupied');
            } else {
                // Check if there are other active leases for this unit
                $active_leases = get_posts(array(
                    'post_type' => 'phpm_lease',
                    'post_status' => 'publish',
                    'meta_query' => array(
                        array(
                            'key' => '_phpm_lease_unit_id',
                            'value' => $unit_id,
                            'compare' => '='
                        ),
                        array(
                            'key' => '_phpm_lease_status',
                            'value' => 'active',
                            'compare' => '='
                        )
                    ),
                    'exclude' => array($post_id),
                    'posts_per_page' => 1,
                    'fields' => 'ids'
                ));
                
                if (empty($active_leases)) {
                    update_post_meta($unit_id, '_phpm_unit_status', 'available');
                }
            }
        }
    }
    
    /**
     * Update tenant status when lease is saved
     */
    public static function update_tenant_status($post_id, $post, $update) {
        $tenant_id = get_post_meta($post_id, '_phpm_lease_tenant_id', true);
        $lease_status = get_post_meta($post_id, '_phpm_lease_status', true);
        
        if (!empty($tenant_id)) {
            $tenant_status = ($lease_status === 'active') ? 'current' : 'former';
            update_post_meta($tenant_id, '_phpm_tenant_status', $tenant_status);
        }
    }
    
    /**
     * Handle post deletion relationships
     */
    public static function handle_post_deletion($post_id) {
        $post_type = get_post_type($post_id);
        
        switch ($post_type) {
            case 'phpm_property':
                self::handle_property_deletion($post_id);
                break;
            case 'phpm_unit':
                self::handle_unit_deletion($post_id);
                break;
            case 'phpm_tenant':
                self::handle_tenant_deletion($post_id);
                break;
            case 'phpm_lease':
                self::handle_lease_deletion($post_id);
                break;
        }
    }
    
    /**
     * Handle property deletion
     */
    private static function handle_property_deletion($property_id) {
        // Check for related units
        $units = get_posts(array(
            'post_type' => 'phpm_unit',
            'meta_query' => array(
                array(
                    'key' => '_phpm_unit_property_id',
                    'value' => $property_id,
                    'compare' => '='
                )
            ),
            'posts_per_page' => -1,
            'fields' => 'ids'
        ));
        
        if (!empty($units)) {
            wp_die(__('Cannot delete property: it has associated units. Please delete all units first.', 'plughaus-property'));
        }
        
        // Check for related leases
        $leases = get_posts(array(
            'post_type' => 'phpm_lease',
            'meta_query' => array(
                array(
                    'key' => '_phpm_lease_property_id',
                    'value' => $property_id,
                    'compare' => '='
                )
            ),
            'posts_per_page' => 1,
            'fields' => 'ids'
        ));
        
        if (!empty($leases)) {
            wp_die(__('Cannot delete property: it has active leases. Please remove all leases first.', 'plughaus-property'));
        }
    }
    
    /**
     * Handle unit deletion
     */
    private static function handle_unit_deletion($unit_id) {
        // Check for active leases
        $active_leases = get_posts(array(
            'post_type' => 'phpm_lease',
            'meta_query' => array(
                array(
                    'key' => '_phpm_lease_unit_id',
                    'value' => $unit_id,
                    'compare' => '='
                ),
                array(
                    'key' => '_phpm_lease_status',
                    'value' => 'active',
                    'compare' => '='
                )
            ),
            'posts_per_page' => 1,
            'fields' => 'ids'
        ));
        
        if (!empty($active_leases)) {
            wp_die(__('Cannot delete unit: it has an active lease. Please end the lease first.', 'plughaus-property'));
        }
    }
    
    /**
     * Handle tenant deletion
     */
    private static function handle_tenant_deletion($tenant_id) {
        // Check for active leases
        $active_leases = get_posts(array(
            'post_type' => 'phpm_lease',
            'meta_query' => array(
                array(
                    'key' => '_phpm_lease_tenant_id',
                    'value' => $tenant_id,
                    'compare' => '='
                ),
                array(
                    'key' => '_phpm_lease_status',
                    'value' => 'active',
                    'compare' => '='
                )
            ),
            'posts_per_page' => 1,
            'fields' => 'ids'
        ));
        
        if (!empty($active_leases)) {
            wp_die(__('Cannot delete tenant: they have an active lease. Please end the lease first.', 'plughaus-property'));
        }
    }
    
    /**
     * Handle lease deletion
     */
    private static function handle_lease_deletion($lease_id) {
        $unit_id = get_post_meta($lease_id, '_phpm_lease_unit_id', true);
        
        if (!empty($unit_id)) {
            // Set unit back to available if this was the only active lease
            $other_active_leases = get_posts(array(
                'post_type' => 'phpm_lease',
                'meta_query' => array(
                    array(
                        'key' => '_phpm_lease_unit_id',
                        'value' => $unit_id,
                        'compare' => '='
                    ),
                    array(
                        'key' => '_phpm_lease_status',
                        'value' => 'active',
                        'compare' => '='
                    )
                ),
                'exclude' => array($lease_id),
                'posts_per_page' => 1,
                'fields' => 'ids'
            ));
            
            if (empty($other_active_leases)) {
                update_post_meta($unit_id, '_phpm_unit_status', 'available');
            }
        }
    }
    
    // Validation helper methods
    
    /**
     * Validate ZIP code format
     */
    private static function validate_zip_code($zip) {
        // US ZIP code format (5 digits or 5+4 digits)
        return preg_match('/^\d{5}(-\d{4})?$/', $zip);
    }
    
    /**
     * Validate date format
     */
    private static function validate_date($date) {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
    
    /**
     * Check if property exists
     */
    private static function property_exists($property_id) {
        $property = get_post($property_id);
        return $property && $property->post_type === 'phpm_property';
    }
    
    /**
     * Check if unit exists
     */
    private static function unit_exists($unit_id) {
        $unit = get_post($unit_id);
        return $unit && $unit->post_type === 'phpm_unit';
    }
    
    /**
     * Check if tenant exists
     */
    private static function tenant_exists($tenant_id) {
        $tenant = get_post($tenant_id);
        return $tenant && $tenant->post_type === 'phpm_tenant';
    }
    
    /**
     * Check for duplicate property
     */
    private static function check_duplicate_property($post_id, $address, $city, $state) {
        $existing = get_posts(array(
            'post_type' => 'phpm_property',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => '_phpm_property_address',
                    'value' => $address,
                    'compare' => '='
                ),
                array(
                    'key' => '_phpm_property_city',
                    'value' => $city,
                    'compare' => '='
                ),
                array(
                    'key' => '_phpm_property_state',
                    'value' => $state,
                    'compare' => '='
                )
            ),
            'exclude' => array($post_id),
            'posts_per_page' => 1,
            'fields' => 'ids'
        ));
        
        return !empty($existing);
    }
    
    /**
     * Check for duplicate unit number
     */
    private static function check_duplicate_unit_number($post_id, $property_id, $unit_number) {
        $existing = get_posts(array(
            'post_type' => 'phpm_unit',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => '_phpm_unit_property_id',
                    'value' => $property_id,
                    'compare' => '='
                ),
                array(
                    'key' => '_phpm_unit_number',
                    'value' => $unit_number,
                    'compare' => '='
                )
            ),
            'exclude' => array($post_id),
            'posts_per_page' => 1,
            'fields' => 'ids'
        ));
        
        return !empty($existing);
    }
    
    /**
     * Check for duplicate tenant email
     */
    private static function check_duplicate_tenant_email($post_id, $email) {
        $existing = get_posts(array(
            'post_type' => 'phpm_tenant',
            'meta_query' => array(
                array(
                    'key' => '_phpm_tenant_email',
                    'value' => $email,
                    'compare' => '='
                )
            ),
            'exclude' => array($post_id),
            'posts_per_page' => 1,
            'fields' => 'ids'
        ));
        
        return !empty($existing);
    }
    
    /**
     * Check for overlapping leases
     */
    private static function check_overlapping_leases($post_id, $unit_id, $start_date, $end_date) {
        $existing = get_posts(array(
            'post_type' => 'phpm_lease',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => '_phpm_lease_unit_id',
                    'value' => $unit_id,
                    'compare' => '='
                ),
                array(
                    'relation' => 'OR',
                    array(
                        'relation' => 'AND',
                        array(
                            'key' => '_phpm_lease_start_date',
                            'value' => $start_date,
                            'compare' => '<='
                        ),
                        array(
                            'key' => '_phpm_lease_end_date',
                            'value' => $start_date,
                            'compare' => '>='
                        )
                    ),
                    array(
                        'relation' => 'AND',
                        array(
                            'key' => '_phpm_lease_start_date',
                            'value' => $end_date,
                            'compare' => '<='
                        ),
                        array(
                            'key' => '_phpm_lease_end_date',
                            'value' => $end_date,
                            'compare' => '>='
                        )
                    ),
                    array(
                        'relation' => 'AND',
                        array(
                            'key' => '_phpm_lease_start_date',
                            'value' => $start_date,
                            'compare' => '>='
                        ),
                        array(
                            'key' => '_phpm_lease_end_date',
                            'value' => $end_date,
                            'compare' => '<='
                        )
                    )
                )
            ),
            'exclude' => array($post_id),
            'posts_per_page' => 1,
            'fields' => 'ids'
        ));
        
        return !empty($existing);
    }
    
    /**
     * Store validation errors for display
     */
    private static function store_validation_errors($post_id, $errors) {
        if (!empty($errors)) {
            set_transient('phpm_validation_errors_' . $post_id, $errors, 30);
        } else {
            delete_transient('phpm_validation_errors_' . $post_id);
        }
    }
    
    /**
     * Display validation error notices
     */
    public static function display_validation_notices() {
        global $post;
        
        if (!$post || !in_array($post->post_type, array('phpm_property', 'phpm_unit', 'phpm_tenant', 'phpm_lease', 'phpm_maintenance'))) {
            return;
        }
        
        $errors = get_transient('phpm_validation_errors_' . $post->ID);
        
        if (!empty($errors)) {
            echo '<div class="notice notice-error is-dismissible">';
            echo '<p><strong>' . __('Validation Errors:', 'plughaus-property') . '</strong></p>';
            echo '<ul>';
            foreach ($errors as $error) {
                echo '<li>' . esc_html($error) . '</li>';
            }
            echo '</ul>';
            echo '</div>';
            
            delete_transient('phpm_validation_errors_' . $post->ID);
        }
    }
    
    // AJAX validation methods
    
    /**
     * AJAX email validation
     */
    public static function ajax_validate_email() {
        check_ajax_referer('phpm_admin_nonce', 'nonce');
        
        $email = sanitize_email($_POST['email']);
        $tenant_id = intval($_POST['tenant_id']);
        
        if (!is_email($email)) {
            wp_send_json_error(__('Invalid email format.', 'plughaus-property'));
        }
        
        $duplicate = self::check_duplicate_tenant_email($tenant_id, $email);
        
        if ($duplicate) {
            wp_send_json_error(__('Email address already exists.', 'plughaus-property'));
        }
        
        wp_send_json_success(__('Email is valid.', 'plughaus-property'));
    }
    
    /**
     * AJAX lease date validation
     */
    public static function ajax_validate_lease_dates() {
        check_ajax_referer('phpm_admin_nonce', 'nonce');
        
        $unit_id = intval($_POST['unit_id']);
        $start_date = sanitize_text_field($_POST['start_date']);
        $end_date = sanitize_text_field($_POST['end_date']);
        $lease_id = intval($_POST['lease_id']);
        
        if (strtotime($end_date) <= strtotime($start_date)) {
            wp_send_json_error(__('End date must be after start date.', 'plughaus-property'));
        }
        
        $overlapping = self::check_overlapping_leases($lease_id, $unit_id, $start_date, $end_date);
        
        if ($overlapping) {
            wp_send_json_error(__('Dates overlap with existing lease.', 'plughaus-property'));
        }
        
        wp_send_json_success(__('Dates are valid.', 'plughaus-property'));
    }
    
    /**
     * AJAX unit availability check
     */
    public static function ajax_check_unit_availability() {
        check_ajax_referer('phpm_admin_nonce', 'nonce');
        
        $unit_id = intval($_POST['unit_id']);
        $start_date = sanitize_text_field($_POST['start_date']);
        $end_date = sanitize_text_field($_POST['end_date']);
        
        $overlapping = self::check_overlapping_leases(0, $unit_id, $start_date, $end_date);
        
        if ($overlapping) {
            wp_send_json_error(__('Unit not available for these dates.', 'plughaus-property'));
        }
        
        wp_send_json_success(__('Unit is available.', 'plughaus-property'));
    }
    
    // REST API validation methods
    
    public static function validate_property_rest_data($prepared_post, $request) {
        // Implement REST API validation for properties
        return $prepared_post;
    }
    
    public static function validate_unit_rest_data($prepared_post, $request) {
        // Implement REST API validation for units
        return $prepared_post;
    }
    
    public static function validate_tenant_rest_data($prepared_post, $request) {
        // Implement REST API validation for tenants
        return $prepared_post;
    }
    
    public static function validate_lease_rest_data($prepared_post, $request) {
        // Implement REST API validation for leases
        return $prepared_post;
    }
}

// Initialize data validation
PHPM_Data_Validation::init();