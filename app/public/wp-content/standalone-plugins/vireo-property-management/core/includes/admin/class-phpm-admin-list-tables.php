<?php
/**
 * Admin List Tables for PlugHaus Property Management
 * Customizes the admin list tables for better property management
 */

if (!defined('ABSPATH')) {
    exit;
}

class PHPM_Admin_List_Tables {
    
    /**
     * Initialize list table customizations
     */
    public static function init() {
        // Properties list table
        add_filter('manage_phpm_property_posts_columns', array(__CLASS__, 'property_columns'));
        add_action('manage_phpm_property_posts_custom_column', array(__CLASS__, 'property_column_content'), 10, 2);
        add_filter('manage_edit-phpm_property_sortable_columns', array(__CLASS__, 'property_sortable_columns'));
        
        // Units list table
        add_filter('manage_phpm_unit_posts_columns', array(__CLASS__, 'unit_columns'));
        add_action('manage_phpm_unit_posts_custom_column', array(__CLASS__, 'unit_column_content'), 10, 2);
        add_filter('manage_edit-phpm_unit_sortable_columns', array(__CLASS__, 'unit_sortable_columns'));
        
        // Tenants list table
        add_filter('manage_phpm_tenant_posts_columns', array(__CLASS__, 'tenant_columns'));
        add_action('manage_phpm_tenant_posts_custom_column', array(__CLASS__, 'tenant_column_content'), 10, 2);
        add_filter('manage_edit-phpm_tenant_sortable_columns', array(__CLASS__, 'tenant_sortable_columns'));
        
        // Leases list table
        add_filter('manage_phpm_lease_posts_columns', array(__CLASS__, 'lease_columns'));
        add_action('manage_phpm_lease_posts_custom_column', array(__CLASS__, 'lease_column_content'), 10, 2);
        add_filter('manage_edit-phpm_lease_sortable_columns', array(__CLASS__, 'lease_sortable_columns'));
        
        // Maintenance list table
        add_filter('manage_phpm_maintenance_posts_columns', array(__CLASS__, 'maintenance_columns'));
        add_action('manage_phpm_maintenance_posts_custom_column', array(__CLASS__, 'maintenance_column_content'), 10, 2);
        add_filter('manage_edit-phpm_maintenance_sortable_columns', array(__CLASS__, 'maintenance_sortable_columns'));
        
        // Add bulk actions
        add_filter('bulk_actions-edit-phpm_property', array(__CLASS__, 'property_bulk_actions'));
        add_filter('bulk_actions-edit-phpm_unit', array(__CLASS__, 'unit_bulk_actions'));
        add_filter('bulk_actions-edit-phpm_lease', array(__CLASS__, 'lease_bulk_actions'));
        add_filter('bulk_actions-edit-phpm_maintenance', array(__CLASS__, 'maintenance_bulk_actions'));
        
        // Handle bulk actions
        add_filter('handle_bulk_actions-edit-phpm_property', array(__CLASS__, 'handle_property_bulk_actions'), 10, 3);
        add_filter('handle_bulk_actions-edit-phpm_unit', array(__CLASS__, 'handle_unit_bulk_actions'), 10, 3);
        add_filter('handle_bulk_actions-edit-phpm_lease', array(__CLASS__, 'handle_lease_bulk_actions'), 10, 3);
        add_filter('handle_bulk_actions-edit-phpm_maintenance', array(__CLASS__, 'handle_maintenance_bulk_actions'), 10, 3);
        
        // Quick edit support
        add_action('quick_edit_custom_box', array(__CLASS__, 'quick_edit_fields'), 10, 2);
        add_action('save_post', array(__CLASS__, 'save_quick_edit_data'));
        
        // Add admin notices for bulk actions
        add_action('admin_notices', array(__CLASS__, 'bulk_action_notices'));
    }
    
    /**
     * Properties list table columns
     */
    public static function property_columns($columns) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['title'] = __('Property Name', 'plughaus-property');
        $new_columns['property_type'] = __('Type', 'plughaus-property');
        $new_columns['address'] = __('Address', 'plughaus-property');
        $new_columns['units'] = __('Units', 'plughaus-property');
        $new_columns['occupied_units'] = __('Occupied', 'plughaus-property');
        $new_columns['monthly_income'] = __('Monthly Income', 'plughaus-property');
        $new_columns['date'] = __('Date Added', 'plughaus-property');
        
        return $new_columns;
    }
    
    /**
     * Properties column content
     */
    public static function property_column_content($column, $post_id) {
        switch ($column) {
            case 'property_type':
                $type = get_post_meta($post_id, '_phpm_property_type', true);
                $types = PHPM_Utilities::get_property_types();
                echo isset($types[$type]) ? esc_html($types[$type]) : esc_html($type);
                break;
                
            case 'address':
                $address = get_post_meta($post_id, '_phpm_property_address', true);
                $city = get_post_meta($post_id, '_phpm_property_city', true);
                $state = get_post_meta($post_id, '_phpm_property_state', true);
                
                $full_address = array_filter(array($address, $city, $state));
                echo esc_html(implode(', ', $full_address));
                break;
                
            case 'units':
                $total_units = self::get_property_total_units($post_id);
                echo '<span class="total-units">' . esc_html($total_units) . '</span>';
                break;
                
            case 'occupied_units':
                $occupied = self::get_property_occupied_units($post_id);
                $total = self::get_property_total_units($post_id);
                $percentage = $total > 0 ? round(($occupied / $total) * 100) : 0;
                
                echo '<span class="occupied-units">' . esc_html($occupied) . '/' . esc_html($total) . '</span>';
                echo '<span class="occupancy-rate">(' . esc_html($percentage) . '%)</span>';
                break;
                
            case 'monthly_income':
                $income = self::calculate_property_monthly_income($post_id);
                echo '<span class="monthly-income">' . PHPM_Utilities::format_currency($income) . '</span>';
                break;
        }
    }
    
    /**
     * Units list table columns
     */
    public static function unit_columns($columns) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['title'] = __('Unit', 'plughaus-property');
        $new_columns['property'] = __('Property', 'plughaus-property');
        $new_columns['unit_details'] = __('Details', 'plughaus-property');
        $new_columns['rent_amount'] = __('Rent', 'plughaus-property');
        $new_columns['tenant'] = __('Current Tenant', 'plughaus-property');
        $new_columns['status'] = __('Status', 'plughaus-property');
        $new_columns['date'] = __('Date Added', 'plughaus-property');
        
        return $new_columns;
    }
    
    /**
     * Units column content
     */
    public static function unit_column_content($column, $post_id) {
        switch ($column) {
            case 'property':
                $property_id = get_post_meta($post_id, '_phpm_unit_property_id', true);
                if ($property_id) {
                    $property = get_post($property_id);
                    if ($property) {
                        echo '<a href="' . get_edit_post_link($property_id) . '">' . esc_html($property->post_title) . '</a>';
                    }
                } else {
                    echo '<span class="na">—</span>';
                }
                break;
                
            case 'unit_details':
                $bedrooms = get_post_meta($post_id, '_phpm_unit_bedrooms', true);
                $bathrooms = get_post_meta($post_id, '_phpm_unit_bathrooms', true);
                $sqft = get_post_meta($post_id, '_phpm_unit_square_feet', true);
                
                $details = array();
                if ($bedrooms) $details[] = $bedrooms . ' bed';
                if ($bathrooms) $details[] = $bathrooms . ' bath';
                if ($sqft) $details[] = number_format($sqft) . ' sqft';
                
                echo !empty($details) ? esc_html(implode(', ', $details)) : '<span class="na">—</span>';
                break;
                
            case 'rent_amount':
                $rent = get_post_meta($post_id, '_phpm_unit_rent', true);
                echo $rent ? PHPM_Utilities::format_currency($rent) : '<span class="na">—</span>';
                break;
                
            case 'tenant':
                $tenant = self::get_unit_current_tenant($post_id);
                if ($tenant) {
                    echo '<a href="' . get_edit_post_link($tenant->ID) . '">' . esc_html($tenant->post_title) . '</a>';
                } else {
                    echo '<span class="vacant">Vacant</span>';
                }
                break;
                
            case 'status':
                $status = get_post_meta($post_id, '_phpm_unit_status', true) ?: 'available';
                $status_labels = array(
                    'available' => __('Available', 'plughaus-property'),
                    'occupied' => __('Occupied', 'plughaus-property'),
                    'maintenance' => __('Maintenance', 'plughaus-property'),
                    'offline' => __('Offline', 'plughaus-property')
                );
                
                $label = isset($status_labels[$status]) ? $status_labels[$status] : $status;
                echo '<span class="unit-status status-' . esc_attr($status) . '">' . esc_html($label) . '</span>';
                break;
        }
    }
    
    /**
     * Tenants list table columns
     */
    public static function tenant_columns($columns) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['title'] = __('Tenant Name', 'plughaus-property');
        $new_columns['contact'] = __('Contact Info', 'plughaus-property');
        $new_columns['current_lease'] = __('Current Lease', 'plughaus-property');
        $new_columns['unit'] = __('Unit', 'plughaus-property');
        $new_columns['lease_status'] = __('Lease Status', 'plughaus-property');
        $new_columns['move_in_date'] = __('Move-in Date', 'plughaus-property');
        $new_columns['date'] = __('Date Added', 'plughaus-property');
        
        return $new_columns;
    }
    
    /**
     * Tenants column content
     */
    public static function tenant_column_content($column, $post_id) {
        switch ($column) {
            case 'contact':
                $email = get_post_meta($post_id, '_phpm_tenant_email', true);
                $phone = get_post_meta($post_id, '_phpm_tenant_phone', true);
                
                if ($email) {
                    echo '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a><br>';
                }
                if ($phone) {
                    echo '<span class="phone">' . esc_html(PHPM_Utilities::format_phone($phone)) . '</span>';
                }
                break;
                
            case 'current_lease':
                $lease = self::get_tenant_current_lease($post_id);
                if ($lease) {
                    echo '<a href="' . get_edit_post_link($lease->ID) . '">' . esc_html($lease->post_title) . '</a>';
                } else {
                    echo '<span class="na">No active lease</span>';
                }
                break;
                
            case 'unit':
                $lease = self::get_tenant_current_lease($post_id);
                if ($lease) {
                    $unit_id = get_post_meta($lease->ID, '_phpm_lease_unit_id', true);
                    if ($unit_id) {
                        $unit = get_post($unit_id);
                        if ($unit) {
                            echo '<a href="' . get_edit_post_link($unit_id) . '">' . esc_html($unit->post_title) . '</a>';
                        }
                    }
                } else {
                    echo '<span class="na">—</span>';
                }
                break;
                
            case 'lease_status':
                $lease = self::get_tenant_current_lease($post_id);
                if ($lease) {
                    $status = get_post_meta($lease->ID, '_phpm_lease_status', true);
                    $statuses = PHPM_Utilities::get_lease_statuses();
                    echo isset($statuses[$status]) ? esc_html($statuses[$status]) : esc_html($status);
                } else {
                    echo '<span class="na">—</span>';
                }
                break;
                
            case 'move_in_date':
                $move_in = get_post_meta($post_id, '_phpm_tenant_move_in_date', true);
                echo $move_in ? esc_html(date_i18n(get_option('date_format'), strtotime($move_in))) : '<span class="na">—</span>';
                break;
        }
    }
    
    /**
     * Leases list table columns
     */
    public static function lease_columns($columns) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['title'] = __('Lease', 'plughaus-property');
        $new_columns['tenant'] = __('Tenant', 'plughaus-property');
        $new_columns['property_unit'] = __('Property/Unit', 'plughaus-property');
        $new_columns['lease_term'] = __('Lease Term', 'plughaus-property');
        $new_columns['rent_amount'] = __('Rent', 'plughaus-property');
        $new_columns['status'] = __('Status', 'plughaus-property');
        $new_columns['expiration'] = __('Expires', 'plughaus-property');
        $new_columns['date'] = __('Date Created', 'plughaus-property');
        
        return $new_columns;
    }
    
    /**
     * Leases column content
     */
    public static function lease_column_content($column, $post_id) {
        switch ($column) {
            case 'tenant':
                $tenant_id = get_post_meta($post_id, '_phpm_lease_tenant_id', true);
                if ($tenant_id) {
                    $tenant = get_post($tenant_id);
                    if ($tenant) {
                        echo '<a href="' . get_edit_post_link($tenant_id) . '">' . esc_html($tenant->post_title) . '</a>';
                    }
                } else {
                    echo '<span class="na">—</span>';
                }
                break;
                
            case 'property_unit':
                $property_id = get_post_meta($post_id, '_phpm_lease_property_id', true);
                $unit_id = get_post_meta($post_id, '_phpm_lease_unit_id', true);
                
                if ($property_id) {
                    $property = get_post($property_id);
                    echo '<a href="' . get_edit_post_link($property_id) . '">' . esc_html($property->post_title) . '</a>';
                    
                    if ($unit_id) {
                        $unit = get_post($unit_id);
                        echo '<br><span class="unit-link">Unit: <a href="' . get_edit_post_link($unit_id) . '">' . esc_html($unit->post_title) . '</a></span>';
                    }
                } else {
                    echo '<span class="na">—</span>';
                }
                break;
                
            case 'lease_term':
                $start = get_post_meta($post_id, '_phpm_lease_start_date', true);
                $end = get_post_meta($post_id, '_phpm_lease_end_date', true);
                
                if ($start && $end) {
                    echo esc_html(date_i18n('M j, Y', strtotime($start))) . '<br>';
                    echo '<span class="to">to</span> ' . esc_html(date_i18n('M j, Y', strtotime($end)));
                    
                    $term_months = PHPM_Utilities::calculate_lease_term($start, $end);
                    if ($term_months) {
                        echo '<br><span class="term-length">(' . sprintf(_n('%d month', '%d months', $term_months, 'plughaus-property'), $term_months) . ')</span>';
                    }
                } else {
                    echo '<span class="na">—</span>';
                }
                break;
                
            case 'rent_amount':
                $rent = get_post_meta($post_id, '_phpm_lease_rent_amount', true);
                echo $rent ? PHPM_Utilities::format_currency($rent) : '<span class="na">—</span>';
                break;
                
            case 'status':
                $status = get_post_meta($post_id, '_phpm_lease_status', true);
                $statuses = PHPM_Utilities::get_lease_statuses();
                $label = isset($statuses[$status]) ? $statuses[$status] : $status;
                echo '<span class="lease-status status-' . esc_attr($status) . '">' . esc_html($label) . '</span>';
                break;
                
            case 'expiration':
                $end_date = get_post_meta($post_id, '_phpm_lease_end_date', true);
                if ($end_date) {
                    $days = PHPM_Utilities::days_until_lease_expiration($end_date);
                    echo esc_html(date_i18n('M j, Y', strtotime($end_date)));
                    
                    if ($days > 0) {
                        echo '<br><span class="days-remaining">(' . sprintf(_n('%d day left', '%d days left', $days, 'plughaus-property'), $days) . ')</span>';
                    } elseif ($days < 0) {
                        echo '<br><span class="expired">Expired ' . abs($days) . ' days ago</span>';
                    } else {
                        echo '<br><span class="expires-today">Expires today</span>';
                    }
                } else {
                    echo '<span class="na">—</span>';
                }
                break;
        }
    }
    
    /**
     * Maintenance list table columns
     */
    public static function maintenance_columns($columns) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['title'] = __('Request', 'plughaus-property');
        $new_columns['property_unit'] = __('Property/Unit', 'plughaus-property');
        $new_columns['priority'] = __('Priority', 'plughaus-property');
        $new_columns['status'] = __('Status', 'plughaus-property');
        $new_columns['submitter'] = __('Submitted By', 'plughaus-property');
        $new_columns['assigned_to'] = __('Assigned To', 'plughaus-property');
        $new_columns['date'] = __('Date Submitted', 'plughaus-property');
        
        return $new_columns;
    }
    
    /**
     * Maintenance column content
     */
    public static function maintenance_column_content($column, $post_id) {
        switch ($column) {
            case 'property_unit':
                $property_id = get_post_meta($post_id, '_phpm_maintenance_property_id', true);
                $unit_id = get_post_meta($post_id, '_phpm_maintenance_unit_id', true);
                
                if ($property_id) {
                    $property = get_post($property_id);
                    echo '<a href="' . get_edit_post_link($property_id) . '">' . esc_html($property->post_title) . '</a>';
                    
                    if ($unit_id) {
                        $unit = get_post($unit_id);
                        echo '<br>Unit: ' . esc_html($unit->post_title);
                    }
                } else {
                    echo '<span class="na">—</span>';
                }
                break;
                
            case 'priority':
                $priority = get_post_meta($post_id, '_phpm_maintenance_priority', true);
                $priorities = PHPM_Utilities::get_maintenance_priorities();
                $label = isset($priorities[$priority]) ? $priorities[$priority] : $priority;
                echo '<span class="priority priority-' . esc_attr($priority) . '">' . esc_html($label) . '</span>';
                break;
                
            case 'status':
                $status = get_post_meta($post_id, '_phpm_maintenance_status', true);
                $statuses = PHPM_Utilities::get_maintenance_statuses();
                $label = isset($statuses[$status]) ? $statuses[$status] : $status;
                echo '<span class="maintenance-status status-' . esc_attr($status) . '">' . esc_html($label) . '</span>';
                break;
                
            case 'submitter':
                $submitter_id = get_post_meta($post_id, '_phpm_maintenance_submitter_id', true);
                if ($submitter_id) {
                    $submitter = get_post($submitter_id);
                    if ($submitter) {
                        echo '<a href="' . get_edit_post_link($submitter_id) . '">' . esc_html($submitter->post_title) . '</a>';
                    }
                } else {
                    $author = get_post_field('post_author', $post_id);
                    $user = get_user_by('id', $author);
                    echo $user ? esc_html($user->display_name) : '<span class="na">—</span>';
                }
                break;
                
            case 'assigned_to':
                $assigned_to = get_post_meta($post_id, '_phpm_maintenance_assigned_to', true);
                if ($assigned_to) {
                    $user = get_user_by('id', $assigned_to);
                    echo $user ? esc_html($user->display_name) : '<span class="na">—</span>';
                } else {
                    echo '<span class="unassigned">Unassigned</span>';
                }
                break;
        }
    }
    
    // Helper methods
    
    /**
     * Get total units for a property
     */
    private static function get_property_total_units($property_id) {
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
        
        return count($units);
    }
    
    /**
     * Get occupied units for a property
     */
    private static function get_property_occupied_units($property_id) {
        $occupied_units = get_posts(array(
            'post_type' => 'phpm_unit',
            'meta_query' => array(
                array(
                    'key' => '_phpm_unit_property_id',
                    'value' => $property_id,
                    'compare' => '='
                ),
                array(
                    'key' => '_phpm_unit_status',
                    'value' => 'occupied',
                    'compare' => '='
                )
            ),
            'posts_per_page' => -1,
            'fields' => 'ids'
        ));
        
        return count($occupied_units);
    }
    
    /**
     * Calculate monthly income for a property
     */
    private static function calculate_property_monthly_income($property_id) {
        $units = get_posts(array(
            'post_type' => 'phpm_unit',
            'meta_query' => array(
                array(
                    'key' => '_phpm_unit_property_id',
                    'value' => $property_id,
                    'compare' => '='
                ),
                array(
                    'key' => '_phpm_unit_status',
                    'value' => 'occupied',
                    'compare' => '='
                )
            ),
            'posts_per_page' => -1
        ));
        
        $total_income = 0;
        foreach ($units as $unit) {
            $rent = get_post_meta($unit->ID, '_phpm_unit_rent', true);
            if ($rent) {
                $total_income += floatval($rent);
            }
        }
        
        return $total_income;
    }
    
    /**
     * Get current tenant for a unit
     */
    private static function get_unit_current_tenant($unit_id) {
        $leases = get_posts(array(
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
            'posts_per_page' => 1
        ));
        
        if (!empty($leases)) {
            $tenant_id = get_post_meta($leases[0]->ID, '_phpm_lease_tenant_id', true);
            if ($tenant_id) {
                return get_post($tenant_id);
            }
        }
        
        return null;
    }
    
    /**
     * Get current lease for a tenant
     */
    private static function get_tenant_current_lease($tenant_id) {
        $leases = get_posts(array(
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
            'posts_per_page' => 1
        ));
        
        return !empty($leases) ? $leases[0] : null;
    }
    
    /**
     * Add bulk actions for properties
     */
    public static function property_bulk_actions($actions) {
        $actions['export_properties'] = __('Export to CSV', 'plughaus-property');
        return $actions;
    }
    
    /**
     * Add bulk actions for units
     */
    public static function unit_bulk_actions($actions) {
        $actions['mark_available'] = __('Mark as Available', 'plughaus-property');
        $actions['mark_occupied'] = __('Mark as Occupied', 'plughaus-property');
        $actions['mark_maintenance'] = __('Mark as Under Maintenance', 'plughaus-property');
        return $actions;
    }
    
    /**
     * Add bulk actions for leases
     */
    public static function lease_bulk_actions($actions) {
        $actions['activate_leases'] = __('Activate Leases', 'plughaus-property');
        $actions['expire_leases'] = __('Mark as Expired', 'plughaus-property');
        return $actions;
    }
    
    /**
     * Add bulk actions for maintenance
     */
    public static function maintenance_bulk_actions($actions) {
        $actions['mark_in_progress'] = __('Mark as In Progress', 'plughaus-property');
        $actions['mark_completed'] = __('Mark as Completed', 'plughaus-property');
        return $actions;
    }
    
    /**
     * Handle property bulk actions
     */
    public static function handle_property_bulk_actions($sendback, $action, $post_ids) {
        if ($action === 'export_properties') {
            // Redirect to export functionality
            $sendback = add_query_arg('phpm_export', 'properties', $sendback);
            $sendback = add_query_arg('post_ids', implode(',', $post_ids), $sendback);
        }
        
        return $sendback;
    }
    
    /**
     * Handle unit bulk actions
     */
    public static function handle_unit_bulk_actions($sendback, $action, $post_ids) {
        $status_actions = array(
            'mark_available' => 'available',
            'mark_occupied' => 'occupied',
            'mark_maintenance' => 'maintenance'
        );
        
        if (isset($status_actions[$action])) {
            foreach ($post_ids as $post_id) {
                update_post_meta($post_id, '_phpm_unit_status', $status_actions[$action]);
            }
            
            $sendback = add_query_arg('phpm_updated', count($post_ids), $sendback);
        }
        
        return $sendback;
    }
    
    /**
     * Handle lease bulk actions
     */
    public static function handle_lease_bulk_actions($sendback, $action, $post_ids) {
        $status_actions = array(
            'activate_leases' => 'active',
            'expire_leases' => 'expired'
        );
        
        if (isset($status_actions[$action])) {
            foreach ($post_ids as $post_id) {
                update_post_meta($post_id, '_phpm_lease_status', $status_actions[$action]);
            }
            
            $sendback = add_query_arg('phpm_updated', count($post_ids), $sendback);
        }
        
        return $sendback;
    }
    
    /**
     * Handle maintenance bulk actions
     */
    public static function handle_maintenance_bulk_actions($sendback, $action, $post_ids) {
        $status_actions = array(
            'mark_in_progress' => 'in_progress',
            'mark_completed' => 'completed'
        );
        
        if (isset($status_actions[$action])) {
            foreach ($post_ids as $post_id) {
                update_post_meta($post_id, '_phpm_maintenance_status', $status_actions[$action]);
            }
            
            $sendback = add_query_arg('phpm_updated', count($post_ids), $sendback);
        }
        
        return $sendback;
    }
    
    /**
     * Add sortable columns
     */
    public static function property_sortable_columns($columns) {
        $columns['property_type'] = 'property_type';
        $columns['units'] = 'units';
        return $columns;
    }
    
    public static function unit_sortable_columns($columns) {
        $columns['rent_amount'] = 'rent_amount';
        $columns['status'] = 'status';
        return $columns;
    }
    
    public static function tenant_sortable_columns($columns) {
        $columns['move_in_date'] = 'move_in_date';
        return $columns;
    }
    
    public static function lease_sortable_columns($columns) {
        $columns['rent_amount'] = 'rent_amount';
        $columns['expiration'] = 'expiration';
        return $columns;
    }
    
    public static function maintenance_sortable_columns($columns) {
        $columns['priority'] = 'priority';
        $columns['status'] = 'status';
        return $columns;
    }
    
    /**
     * Quick edit fields
     */
    public static function quick_edit_fields($column_name, $post_type) {
        if ($post_type === 'phpm_unit' && $column_name === 'status') {
            ?>
            <fieldset class="inline-edit-col-right">
                <div class="inline-edit-col">
                    <label>
                        <span class="title"><?php _e('Status', 'plughaus-property'); ?></span>
                        <select name="unit_status">
                            <option value="available"><?php _e('Available', 'plughaus-property'); ?></option>
                            <option value="occupied"><?php _e('Occupied', 'plughaus-property'); ?></option>
                            <option value="maintenance"><?php _e('Under Maintenance', 'plughaus-property'); ?></option>
                            <option value="offline"><?php _e('Offline', 'plughaus-property'); ?></option>
                        </select>
                    </label>
                </div>
            </fieldset>
            <?php
        }
    }
    
    /**
     * Save quick edit data
     */
    public static function save_quick_edit_data($post_id) {
        if (isset($_POST['unit_status'])) {
            update_post_meta($post_id, '_phpm_unit_status', sanitize_text_field($_POST['unit_status']));
        }
    }
    
    /**
     * Admin notices for bulk actions
     */
    public static function bulk_action_notices() {
        if (isset($_GET['phpm_updated'])) {
            $count = intval($_GET['phpm_updated']);
            echo '<div class="notice notice-success is-dismissible">';
            echo '<p>' . sprintf(_n('%d item updated.', '%d items updated.', $count, 'plughaus-property'), $count) . '</p>';
            echo '</div>';
        }
    }
}

// Initialize list tables
PHPM_Admin_List_Tables::init();