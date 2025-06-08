<?php
/**
 * Import/Export functionality for PlugHaus Property Management
 * Handles CSV and JSON import/export of property data
 */

if (!defined('ABSPATH')) {
    exit;
}

class PHPM_Import_Export {
    
    /**
     * Initialize import/export hooks
     */
    public static function init() {
        // AJAX handlers
        add_action('wp_ajax_phpm_export_data', array(__CLASS__, 'ajax_export_data'));
        add_action('wp_ajax_phpm_import_data', array(__CLASS__, 'ajax_import_data'));
        add_action('wp_ajax_phpm_validate_import_file', array(__CLASS__, 'ajax_validate_import_file'));
        add_action('wp_ajax_phpm_download_template', array(__CLASS__, 'ajax_download_template'));
        
        // File upload handling
        add_action('init', array(__CLASS__, 'handle_file_upload'));
    }
    
    /**
     * Export data to various formats
     */
    public static function export_data($post_type, $format = 'csv', $filters = array()) {
        $export_data = array();
        
        switch ($post_type) {
            case 'properties':
                $export_data = self::export_properties($filters);
                break;
            case 'units':
                $export_data = self::export_units($filters);
                break;
            case 'tenants':
                $export_data = self::export_tenants($filters);
                break;
            case 'leases':
                $export_data = self::export_leases($filters);
                break;
            case 'maintenance':
                $export_data = self::export_maintenance($filters);
                break;
            case 'all':
                $export_data = self::export_all_data($filters);
                break;
            default:
                return new WP_Error('invalid_post_type', __('Invalid data type for export.', 'plughaus-property'));
        }
        
        if (empty($export_data)) {
            return new WP_Error('no_data', __('No data found to export.', 'plughaus-property'));
        }
        
        return self::format_export_data($export_data, $format, $post_type);
    }
    
    /**
     * Export properties
     */
    private static function export_properties($filters = array()) {
        $args = array(
            'post_type' => 'phpm_property',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        );
        
        if (!empty($filters['date_from'])) {
            $args['date_query'][] = array(
                'after' => $filters['date_from'],
                'inclusive' => true
            );
        }
        
        if (!empty($filters['date_to'])) {
            $args['date_query'][] = array(
                'before' => $filters['date_to'],
                'inclusive' => true
            );
        }
        
        $properties = get_posts($args);
        $export_data = array();
        
        foreach ($properties as $property) {
            $property_data = array(
                'ID' => $property->ID,
                'Name' => $property->post_title,
                'Description' => $property->post_content,
                'Address' => get_post_meta($property->ID, '_phpm_property_address', true),
                'City' => get_post_meta($property->ID, '_phpm_property_city', true),
                'State' => get_post_meta($property->ID, '_phpm_property_state', true),
                'ZIP' => get_post_meta($property->ID, '_phpm_property_zip', true),
                'Units' => get_post_meta($property->ID, '_phpm_property_units', true),
                'Year Built' => get_post_meta($property->ID, '_phpm_property_year_built', true),
                'Square Footage' => get_post_meta($property->ID, '_phpm_property_square_footage', true),
                'Property Type' => implode(', ', wp_get_post_terms($property->ID, 'phpm_property_type', array('fields' => 'names'))),
                'Amenities' => implode(', ', wp_get_post_terms($property->ID, 'phpm_amenities', array('fields' => 'names'))),
                'Date Created' => $property->post_date
            );
            
            $export_data[] = apply_filters('phpm_export_property_data', $property_data, $property);
        }
        
        return $export_data;
    }
    
    /**
     * Export units
     */
    private static function export_units($filters = array()) {
        $args = array(
            'post_type' => 'phpm_unit',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        );
        
        if (!empty($filters['property_id'])) {
            $args['meta_query'][] = array(
                'key' => '_phpm_unit_property_id',
                'value' => $filters['property_id'],
                'compare' => '='
            );
        }
        
        $units = get_posts($args);
        $export_data = array();
        
        foreach ($units as $unit) {
            $property_id = get_post_meta($unit->ID, '_phpm_unit_property_id', true);
            $property_name = $property_id ? get_the_title($property_id) : '';
            
            $unit_data = array(
                'ID' => $unit->ID,
                'Unit Number' => get_post_meta($unit->ID, '_phpm_unit_number', true),
                'Property ID' => $property_id,
                'Property Name' => $property_name,
                'Bedrooms' => get_post_meta($unit->ID, '_phpm_unit_bedrooms', true),
                'Bathrooms' => get_post_meta($unit->ID, '_phpm_unit_bathrooms', true),
                'Square Feet' => get_post_meta($unit->ID, '_phpm_unit_square_feet', true),
                'Rent Amount' => get_post_meta($unit->ID, '_phpm_unit_rent', true),
                'Status' => get_post_meta($unit->ID, '_phpm_unit_status', true),
                'Description' => $unit->post_content,
                'Date Created' => $unit->post_date
            );
            
            $export_data[] = apply_filters('phpm_export_unit_data', $unit_data, $unit);
        }
        
        return $export_data;
    }
    
    /**
     * Export tenants
     */
    private static function export_tenants($filters = array()) {
        $args = array(
            'post_type' => 'phpm_tenant',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        );
        
        if (!empty($filters['status'])) {
            $args['meta_query'][] = array(
                'key' => '_phpm_tenant_status',
                'value' => $filters['status'],
                'compare' => '='
            );
        }
        
        $tenants = get_posts($args);
        $export_data = array();
        
        foreach ($tenants as $tenant) {
            $tenant_data = array(
                'ID' => $tenant->ID,
                'First Name' => get_post_meta($tenant->ID, '_phpm_tenant_first_name', true),
                'Last Name' => get_post_meta($tenant->ID, '_phpm_tenant_last_name', true),
                'Email' => get_post_meta($tenant->ID, '_phpm_tenant_email', true),
                'Phone' => get_post_meta($tenant->ID, '_phpm_tenant_phone', true),
                'Status' => get_post_meta($tenant->ID, '_phpm_tenant_status', true),
                'Move In Date' => get_post_meta($tenant->ID, '_phpm_tenant_move_in_date', true),
                'Emergency Contact' => get_post_meta($tenant->ID, '_phpm_tenant_emergency_contact', true),
                'Emergency Phone' => get_post_meta($tenant->ID, '_phpm_tenant_emergency_phone', true),
                'Notes' => $tenant->post_content,
                'Date Created' => $tenant->post_date
            );
            
            $export_data[] = apply_filters('phpm_export_tenant_data', $tenant_data, $tenant);
        }
        
        return $export_data;
    }
    
    /**
     * Export leases
     */
    private static function export_leases($filters = array()) {
        $args = array(
            'post_type' => 'phpm_lease',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        );
        
        if (!empty($filters['status'])) {
            $args['meta_query'][] = array(
                'key' => '_phpm_lease_status',
                'value' => $filters['status'],
                'compare' => '='
            );
        }
        
        $leases = get_posts($args);
        $export_data = array();
        
        foreach ($leases as $lease) {
            $property_id = get_post_meta($lease->ID, '_phpm_lease_property_id', true);
            $unit_id = get_post_meta($lease->ID, '_phpm_lease_unit_id', true);
            $tenant_id = get_post_meta($lease->ID, '_phpm_lease_tenant_id', true);
            
            $property_name = $property_id ? get_the_title($property_id) : '';
            $unit_number = $unit_id ? get_post_meta($unit_id, '_phpm_unit_number', true) : '';
            $tenant_name = $tenant_id ? get_the_title($tenant_id) : '';
            
            $lease_data = array(
                'ID' => $lease->ID,
                'Property ID' => $property_id,
                'Property Name' => $property_name,
                'Unit ID' => $unit_id,
                'Unit Number' => $unit_number,
                'Tenant ID' => $tenant_id,
                'Tenant Name' => $tenant_name,
                'Start Date' => get_post_meta($lease->ID, '_phpm_lease_start_date', true),
                'End Date' => get_post_meta($lease->ID, '_phpm_lease_end_date', true),
                'Rent Amount' => get_post_meta($lease->ID, '_phpm_lease_rent_amount', true),
                'Security Deposit' => get_post_meta($lease->ID, '_phpm_lease_security_deposit', true),
                'Status' => get_post_meta($lease->ID, '_phpm_lease_status', true),
                'Term Months' => get_post_meta($lease->ID, '_phpm_lease_term_months', true),
                'Notes' => $lease->post_content,
                'Date Created' => $lease->post_date
            );
            
            $export_data[] = apply_filters('phpm_export_lease_data', $lease_data, $lease);
        }
        
        return $export_data;
    }
    
    /**
     * Export maintenance requests
     */
    private static function export_maintenance($filters = array()) {
        $args = array(
            'post_type' => 'phpm_maintenance',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        );
        
        if (!empty($filters['status'])) {
            $args['meta_query'][] = array(
                'key' => '_phpm_maintenance_status',
                'value' => $filters['status'],
                'compare' => '='
            );
        }
        
        $maintenance_requests = get_posts($args);
        $export_data = array();
        
        foreach ($maintenance_requests as $request) {
            $property_id = get_post_meta($request->ID, '_phpm_maintenance_property_id', true);
            $unit_id = get_post_meta($request->ID, '_phpm_maintenance_unit_id', true);
            
            $property_name = $property_id ? get_the_title($property_id) : '';
            $unit_number = $unit_id ? get_post_meta($unit_id, '_phpm_unit_number', true) : '';
            
            $request_data = array(
                'ID' => $request->ID,
                'Title' => $request->post_title,
                'Description' => $request->post_content,
                'Property ID' => $property_id,
                'Property Name' => $property_name,
                'Unit ID' => $unit_id,
                'Unit Number' => $unit_number,
                'Priority' => get_post_meta($request->ID, '_phpm_maintenance_priority', true),
                'Status' => get_post_meta($request->ID, '_phpm_maintenance_status', true),
                'Category' => get_post_meta($request->ID, '_phpm_maintenance_category', true),
                'Cost' => get_post_meta($request->ID, '_phpm_maintenance_cost', true),
                'Requested By' => get_post_meta($request->ID, '_phpm_maintenance_requested_by', true),
                'Date Created' => $request->post_date
            );
            
            $export_data[] = apply_filters('phpm_export_maintenance_data', $request_data, $request);
        }
        
        return $export_data;
    }
    
    /**
     * Export all data
     */
    private static function export_all_data($filters = array()) {
        return array(
            'properties' => self::export_properties($filters),
            'units' => self::export_units($filters),
            'tenants' => self::export_tenants($filters),
            'leases' => self::export_leases($filters),
            'maintenance' => self::export_maintenance($filters),
            'export_info' => array(
                'plugin_version' => PM_VERSION,
                'export_date' => current_time('mysql'),
                'wordpress_version' => get_bloginfo('version'),
                'site_url' => get_site_url()
            )
        );
    }
    
    /**
     * Format export data
     */
    private static function format_export_data($data, $format, $type) {
        switch ($format) {
            case 'csv':
                return self::generate_csv($data, $type);
            case 'json':
                return self::generate_json($data, $type);
            default:
                return new WP_Error('invalid_format', __('Invalid export format.', 'plughaus-property'));
        }
    }
    
    /**
     * Generate CSV export
     */
    private static function generate_csv($data, $type) {
        if (empty($data)) {
            return new WP_Error('no_data', __('No data to export.', 'plughaus-property'));
        }
        
        $filename = 'phpm_' . $type . '_' . date('Y-m-d_H-i-s') . '.csv';
        
        // For full data export, create multiple CSV files in a ZIP
        if ($type === 'all') {
            return self::generate_csv_zip($data, $filename);
        }
        
        // Create CSV content
        $csv_output = '';
        
        // Add headers
        if (!empty($data[0])) {
            $csv_output .= self::array_to_csv_line(array_keys($data[0]));
        }
        
        // Add data rows
        foreach ($data as $row) {
            $csv_output .= self::array_to_csv_line($row);
        }
        
        return array(
            'filename' => $filename,
            'content' => $csv_output,
            'mimetype' => 'text/csv'
        );
    }
    
    /**
     * Generate JSON export
     */
    private static function generate_json($data, $type) {
        $filename = 'phpm_' . $type . '_' . date('Y-m-d_H-i-s') . '.json';
        
        $json_content = wp_json_encode($data, JSON_PRETTY_PRINT);
        
        return array(
            'filename' => $filename,
            'content' => $json_content,
            'mimetype' => 'application/json'
        );
    }
    
    /**
     * Generate CSV ZIP for full export
     */
    private static function generate_csv_zip($data, $base_filename) {
        if (!class_exists('ZipArchive')) {
            return new WP_Error('zip_not_available', __('ZIP functionality not available on this server.', 'plughaus-property'));
        }
        
        $upload_dir = wp_upload_dir();
        $temp_dir = $upload_dir['basedir'] . '/phpm-exports/';
        
        if (!file_exists($temp_dir)) {
            wp_mkdir_p($temp_dir);
        }
        
        $zip_filename = str_replace('.csv', '.zip', $base_filename);
        $zip_path = $temp_dir . $zip_filename;
        
        $zip = new ZipArchive();
        if ($zip->open($zip_path, ZipArchive::CREATE) !== TRUE) {
            return new WP_Error('zip_creation_failed', __('Failed to create ZIP file.', 'plughaus-property'));
        }
        
        // Add each data type as a separate CSV file
        foreach ($data as $data_type => $items) {
            if ($data_type === 'export_info' || empty($items)) {
                continue;
            }
            
            $csv_content = '';
            
            // Add headers
            if (!empty($items[0])) {
                $csv_content .= self::array_to_csv_line(array_keys($items[0]));
            }
            
            // Add data rows
            foreach ($items as $row) {
                $csv_content .= self::array_to_csv_line($row);
            }
            
            $zip->addFromString($data_type . '.csv', $csv_content);
        }
        
        // Add export info as a text file
        if (isset($data['export_info'])) {
            $info_content = "PlugHaus Property Management Export\n";
            $info_content .= "==================================\n\n";
            foreach ($data['export_info'] as $key => $value) {
                $info_content .= ucwords(str_replace('_', ' ', $key)) . ": " . $value . "\n";
            }
            $zip->addFromString('export_info.txt', $info_content);
        }
        
        $zip->close();
        
        return array(
            'filename' => $zip_filename,
            'filepath' => $zip_path,
            'mimetype' => 'application/zip'
        );
    }
    
    /**
     * Convert array to CSV line
     */
    private static function array_to_csv_line($array) {
        $csv_line = '';
        $temp = fopen('php://temp', 'r+');
        fputcsv($temp, $array);
        rewind($temp);
        $csv_line = fgets($temp);
        fclose($temp);
        return $csv_line;
    }
    
    /**
     * Import data from file
     */
    public static function import_data($file_path, $data_type, $options = array()) {
        if (!file_exists($file_path)) {
            return new WP_Error('file_not_found', __('Import file not found.', 'plughaus-property'));
        }
        
        $file_info = pathinfo($file_path);
        $extension = strtolower($file_info['extension']);
        
        switch ($extension) {
            case 'csv':
                return self::import_csv($file_path, $data_type, $options);
            case 'json':
                return self::import_json($file_path, $data_type, $options);
            default:
                return new WP_Error('unsupported_format', __('Unsupported file format.', 'plughaus-property'));
        }
    }
    
    /**
     * Import CSV data
     */
    private static function import_csv($file_path, $data_type, $options = array()) {
        $handle = fopen($file_path, 'r');
        if (!$handle) {
            return new WP_Error('file_read_error', __('Could not read import file.', 'plughaus-property'));
        }
        
        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            return new WP_Error('invalid_csv', __('Invalid CSV format.', 'plughaus-property'));
        }
        
        $imported = 0;
        $errors = array();
        $row_number = 1; // Start at 1 for headers
        
        while (($row = fgetcsv($handle)) !== FALSE) {
            $row_number++;
            
            if (count($row) !== count($headers)) {
                $errors[] = sprintf(__('Row %d: Column count mismatch.', 'plughaus-property'), $row_number);
                continue;
            }
            
            $data = array_combine($headers, $row);
            $result = self::import_single_item($data, $data_type, $options);
            
            if (is_wp_error($result)) {
                $errors[] = sprintf(__('Row %d: %s', 'plughaus-property'), $row_number, $result->get_error_message());
            } else {
                $imported++;
            }
        }
        
        fclose($handle);
        
        return array(
            'imported' => $imported,
            'errors' => $errors,
            'total_rows' => $row_number - 1
        );
    }
    
    /**
     * Import JSON data
     */
    private static function import_json($file_path, $data_type, $options = array()) {
        $json_content = file_get_contents($file_path);
        $data = json_decode($json_content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error('invalid_json', __('Invalid JSON format.', 'plughaus-property'));
        }
        
        $imported = 0;
        $errors = array();
        
        // Handle full export format
        if (isset($data['properties']) || isset($data['units'])) {
            return self::import_full_json($data, $options);
        }
        
        // Handle single data type
        foreach ($data as $index => $item) {
            $result = self::import_single_item($item, $data_type, $options);
            
            if (is_wp_error($result)) {
                $errors[] = sprintf(__('Item %d: %s', 'plughaus-property'), $index + 1, $result->get_error_message());
            } else {
                $imported++;
            }
        }
        
        return array(
            'imported' => $imported,
            'errors' => $errors,
            'total_items' => count($data)
        );
    }
    
    /**
     * Import full JSON export
     */
    private static function import_full_json($data, $options = array()) {
        $results = array();
        $import_order = array('properties', 'units', 'tenants', 'leases', 'maintenance');
        
        foreach ($import_order as $data_type) {
            if (!isset($data[$data_type]) || empty($data[$data_type])) {
                continue;
            }
            
            $type_imported = 0;
            $type_errors = array();
            
            foreach ($data[$data_type] as $index => $item) {
                $result = self::import_single_item($item, $data_type, $options);
                
                if (is_wp_error($result)) {
                    $type_errors[] = sprintf(__('%s %d: %s', 'plughaus-property'), $data_type, $index + 1, $result->get_error_message());
                } else {
                    $type_imported++;
                }
            }
            
            $results[$data_type] = array(
                'imported' => $type_imported,
                'errors' => $type_errors,
                'total_items' => count($data[$data_type])
            );
        }
        
        return $results;
    }
    
    /**
     * Import single item
     */
    private static function import_single_item($data, $data_type, $options = array()) {
        $update_existing = isset($options['update_existing']) ? $options['update_existing'] : false;
        
        switch ($data_type) {
            case 'properties':
                return self::import_property($data, $update_existing);
            case 'units':
                return self::import_unit($data, $update_existing);
            case 'tenants':
                return self::import_tenant($data, $update_existing);
            case 'leases':
                return self::import_lease($data, $update_existing);
            case 'maintenance':
                return self::import_maintenance($data, $update_existing);
            default:
                return new WP_Error('invalid_data_type', __('Invalid data type for import.', 'plughaus-property'));
        }
    }
    
    /**
     * Import single property
     */
    private static function import_property($data, $update_existing = false) {
        // Check if property exists by address
        $existing_property = null;
        if (!empty($data['Address']) && !empty($data['City'])) {
            $existing = get_posts(array(
                'post_type' => 'phpm_property',
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => '_phpm_property_address',
                        'value' => $data['Address'],
                        'compare' => '='
                    ),
                    array(
                        'key' => '_phpm_property_city',
                        'value' => $data['City'],
                        'compare' => '='
                    )
                ),
                'posts_per_page' => 1
            ));
            
            if (!empty($existing)) {
                $existing_property = $existing[0];
                
                if (!$update_existing) {
                    return new WP_Error('property_exists', __('Property already exists at this address.', 'plughaus-property'));
                }
            }
        }
        
        $property_data = array(
            'post_type' => 'phpm_property',
            'post_title' => sanitize_text_field($data['Name']),
            'post_content' => wp_kses_post($data['Description']),
            'post_status' => 'publish'
        );
        
        if ($existing_property) {
            $property_data['ID'] = $existing_property->ID;
            $property_id = wp_update_post($property_data);
        } else {
            $property_id = wp_insert_post($property_data);
        }
        
        if (is_wp_error($property_id)) {
            return $property_id;
        }
        
        // Update meta fields
        $meta_fields = array(
            'Address' => '_phpm_property_address',
            'City' => '_phpm_property_city',
            'State' => '_phpm_property_state',
            'ZIP' => '_phpm_property_zip',
            'Units' => '_phpm_property_units',
            'Year Built' => '_phpm_property_year_built',
            'Square Footage' => '_phpm_property_square_footage'
        );
        
        foreach ($meta_fields as $csv_field => $meta_key) {
            if (isset($data[$csv_field]) && $data[$csv_field] !== '') {
                update_post_meta($property_id, $meta_key, sanitize_text_field($data[$csv_field]));
            }
        }
        
        // Handle taxonomies
        if (!empty($data['Property Type'])) {
            $types = array_map('trim', explode(',', $data['Property Type']));
            wp_set_object_terms($property_id, $types, 'phpm_property_type');
        }
        
        if (!empty($data['Amenities'])) {
            $amenities = array_map('trim', explode(',', $data['Amenities']));
            wp_set_object_terms($property_id, $amenities, 'phpm_amenities');
        }
        
        return $property_id;
    }
    
    /**
     * Import single unit
     */
    private static function import_unit($data, $update_existing = false) {
        // Find property by name or ID
        $property_id = null;
        
        if (!empty($data['Property ID'])) {
            $property = get_post($data['Property ID']);
            if ($property && $property->post_type === 'phpm_property') {
                $property_id = $property->ID;
            }
        } elseif (!empty($data['Property Name'])) {
            $property = get_page_by_title($data['Property Name'], OBJECT, 'phpm_property');
            if ($property) {
                $property_id = $property->ID;
            }
        }
        
        if (!$property_id) {
            return new WP_Error('property_not_found', __('Property not found for unit.', 'plughaus-property'));
        }
        
        // Check if unit exists
        $existing_unit = null;
        if (!empty($data['Unit Number'])) {
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
                        'value' => $data['Unit Number'],
                        'compare' => '='
                    )
                ),
                'posts_per_page' => 1
            ));
            
            if (!empty($existing)) {
                $existing_unit = $existing[0];
                
                if (!$update_existing) {
                    return new WP_Error('unit_exists', __('Unit already exists.', 'plughaus-property'));
                }
            }
        }
        
        $unit_data = array(
            'post_type' => 'phpm_unit',
            'post_title' => sanitize_text_field($data['Unit Number'] . ' - ' . get_the_title($property_id)),
            'post_content' => wp_kses_post($data['Description']),
            'post_status' => 'publish'
        );
        
        if ($existing_unit) {
            $unit_data['ID'] = $existing_unit->ID;
            $unit_id = wp_update_post($unit_data);
        } else {
            $unit_id = wp_insert_post($unit_data);
        }
        
        if (is_wp_error($unit_id)) {
            return $unit_id;
        }
        
        // Update meta fields
        $meta_fields = array(
            'Unit Number' => '_phpm_unit_number',
            'Bedrooms' => '_phpm_unit_bedrooms',
            'Bathrooms' => '_phpm_unit_bathrooms',
            'Square Feet' => '_phpm_unit_square_feet',
            'Rent Amount' => '_phpm_unit_rent',
            'Status' => '_phpm_unit_status'
        );
        
        foreach ($meta_fields as $csv_field => $meta_key) {
            if (isset($data[$csv_field]) && $data[$csv_field] !== '') {
                update_post_meta($unit_id, $meta_key, sanitize_text_field($data[$csv_field]));
            }
        }
        
        // Always set property ID
        update_post_meta($unit_id, '_phpm_unit_property_id', $property_id);
        
        return $unit_id;
    }
    
    /**
     * Import single tenant
     */
    private static function import_tenant($data, $update_existing = false) {
        // Check if tenant exists by email
        $existing_tenant = null;
        if (!empty($data['Email'])) {
            $existing = get_posts(array(
                'post_type' => 'phpm_tenant',
                'meta_query' => array(
                    array(
                        'key' => '_phpm_tenant_email',
                        'value' => $data['Email'],
                        'compare' => '='
                    )
                ),
                'posts_per_page' => 1
            ));
            
            if (!empty($existing)) {
                $existing_tenant = $existing[0];
                
                if (!$update_existing) {
                    return new WP_Error('tenant_exists', __('Tenant with this email already exists.', 'plughaus-property'));
                }
            }
        }
        
        $tenant_name = trim($data['First Name'] . ' ' . $data['Last Name']);
        
        $tenant_data = array(
            'post_type' => 'phpm_tenant',
            'post_title' => sanitize_text_field($tenant_name),
            'post_content' => wp_kses_post($data['Notes']),
            'post_status' => 'publish'
        );
        
        if ($existing_tenant) {
            $tenant_data['ID'] = $existing_tenant->ID;
            $tenant_id = wp_update_post($tenant_data);
        } else {
            $tenant_id = wp_insert_post($tenant_data);
        }
        
        if (is_wp_error($tenant_id)) {
            return $tenant_id;
        }
        
        // Update meta fields
        $meta_fields = array(
            'First Name' => '_phpm_tenant_first_name',
            'Last Name' => '_phpm_tenant_last_name',
            'Email' => '_phpm_tenant_email',
            'Phone' => '_phpm_tenant_phone',
            'Status' => '_phpm_tenant_status',
            'Move In Date' => '_phpm_tenant_move_in_date',
            'Emergency Contact' => '_phpm_tenant_emergency_contact',
            'Emergency Phone' => '_phpm_tenant_emergency_phone'
        );
        
        foreach ($meta_fields as $csv_field => $meta_key) {
            if (isset($data[$csv_field]) && $data[$csv_field] !== '') {
                update_post_meta($tenant_id, $meta_key, sanitize_text_field($data[$csv_field]));
            }
        }
        
        return $tenant_id;
    }
    
    /**
     * Import single lease
     */
    private static function import_lease($data, $update_existing = false) {
        // Find property, unit, and tenant
        $property_id = self::find_property_id($data);
        $unit_id = self::find_unit_id($data, $property_id);
        $tenant_id = self::find_tenant_id($data);
        
        if (!$property_id || !$unit_id || !$tenant_id) {
            return new WP_Error('missing_references', __('Could not find property, unit, or tenant for lease.', 'plughaus-property'));
        }
        
        $lease_data = array(
            'post_type' => 'phpm_lease',
            'post_title' => sprintf(__('Lease: %s - %s', 'plughaus-property'), 
                get_the_title($unit_id), 
                get_the_title($tenant_id)
            ),
            'post_content' => wp_kses_post($data['Notes']),
            'post_status' => 'publish'
        );
        
        $lease_id = wp_insert_post($lease_data);
        
        if (is_wp_error($lease_id)) {
            return $lease_id;
        }
        
        // Update meta fields
        $meta_fields = array(
            'Start Date' => '_phpm_lease_start_date',
            'End Date' => '_phpm_lease_end_date',
            'Rent Amount' => '_phpm_lease_rent_amount',
            'Security Deposit' => '_phpm_lease_security_deposit',
            'Status' => '_phpm_lease_status',
            'Term Months' => '_phpm_lease_term_months'
        );
        
        foreach ($meta_fields as $csv_field => $meta_key) {
            if (isset($data[$csv_field]) && $data[$csv_field] !== '') {
                update_post_meta($lease_id, $meta_key, sanitize_text_field($data[$csv_field]));
            }
        }
        
        // Set relationships
        update_post_meta($lease_id, '_phpm_lease_property_id', $property_id);
        update_post_meta($lease_id, '_phpm_lease_unit_id', $unit_id);
        update_post_meta($lease_id, '_phpm_lease_tenant_id', $tenant_id);
        
        return $lease_id;
    }
    
    /**
     * Import single maintenance request
     */
    private static function import_maintenance($data, $update_existing = false) {
        $property_id = self::find_property_id($data);
        $unit_id = !empty($data['Unit Number']) ? self::find_unit_id($data, $property_id) : null;
        
        if (!$property_id) {
            return new WP_Error('property_not_found', __('Property not found for maintenance request.', 'plughaus-property'));
        }
        
        $maintenance_data = array(
            'post_type' => 'phpm_maintenance',
            'post_title' => sanitize_text_field($data['Title']),
            'post_content' => wp_kses_post($data['Description']),
            'post_status' => 'publish'
        );
        
        $maintenance_id = wp_insert_post($maintenance_data);
        
        if (is_wp_error($maintenance_id)) {
            return $maintenance_id;
        }
        
        // Update meta fields
        $meta_fields = array(
            'Priority' => '_phpm_maintenance_priority',
            'Status' => '_phpm_maintenance_status',
            'Category' => '_phpm_maintenance_category',
            'Cost' => '_phpm_maintenance_cost',
            'Requested By' => '_phpm_maintenance_requested_by'
        );
        
        foreach ($meta_fields as $csv_field => $meta_key) {
            if (isset($data[$csv_field]) && $data[$csv_field] !== '') {
                update_post_meta($maintenance_id, $meta_key, sanitize_text_field($data[$csv_field]));
            }
        }
        
        // Set relationships
        update_post_meta($maintenance_id, '_phpm_maintenance_property_id', $property_id);
        if ($unit_id) {
            update_post_meta($maintenance_id, '_phpm_maintenance_unit_id', $unit_id);
        }
        
        return $maintenance_id;
    }
    
    /**
     * Helper functions for finding IDs
     */
    private static function find_property_id($data) {
        if (!empty($data['Property ID'])) {
            $property = get_post($data['Property ID']);
            if ($property && $property->post_type === 'phpm_property') {
                return $property->ID;
            }
        }
        
        if (!empty($data['Property Name'])) {
            $property = get_page_by_title($data['Property Name'], OBJECT, 'phpm_property');
            if ($property) {
                return $property->ID;
            }
        }
        
        return null;
    }
    
    private static function find_unit_id($data, $property_id = null) {
        if (!empty($data['Unit ID'])) {
            $unit = get_post($data['Unit ID']);
            if ($unit && $unit->post_type === 'phpm_unit') {
                return $unit->ID;
            }
        }
        
        if (!empty($data['Unit Number']) && $property_id) {
            $units = get_posts(array(
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
                        'value' => $data['Unit Number'],
                        'compare' => '='
                    )
                ),
                'posts_per_page' => 1
            ));
            
            if (!empty($units)) {
                return $units[0]->ID;
            }
        }
        
        return null;
    }
    
    private static function find_tenant_id($data) {
        if (!empty($data['Tenant ID'])) {
            $tenant = get_post($data['Tenant ID']);
            if ($tenant && $tenant->post_type === 'phpm_tenant') {
                return $tenant->ID;
            }
        }
        
        if (!empty($data['Tenant Name'])) {
            $tenant = get_page_by_title($data['Tenant Name'], OBJECT, 'phpm_tenant');
            if ($tenant) {
                return $tenant->ID;
            }
        }
        
        return null;
    }
    
    /**
     * Generate CSV template
     */
    public static function generate_template($data_type) {
        $templates = array(
            'properties' => array(
                'Name', 'Description', 'Address', 'City', 'State', 'ZIP', 
                'Units', 'Year Built', 'Square Footage', 'Property Type', 'Amenities'
            ),
            'units' => array(
                'Unit Number', 'Property Name', 'Bedrooms', 'Bathrooms', 
                'Square Feet', 'Rent Amount', 'Status', 'Description'
            ),
            'tenants' => array(
                'First Name', 'Last Name', 'Email', 'Phone', 'Status', 
                'Move In Date', 'Emergency Contact', 'Emergency Phone', 'Notes'
            ),
            'leases' => array(
                'Property Name', 'Unit Number', 'Tenant Name', 'Start Date', 
                'End Date', 'Rent Amount', 'Security Deposit', 'Status', 'Term Months', 'Notes'
            ),
            'maintenance' => array(
                'Title', 'Description', 'Property Name', 'Unit Number', 
                'Priority', 'Status', 'Category', 'Cost', 'Requested By'
            )
        );
        
        if (!isset($templates[$data_type])) {
            return new WP_Error('invalid_template', __('Invalid template type.', 'plughaus-property'));
        }
        
        $filename = 'phpm_' . $data_type . '_template.csv';
        $csv_content = self::array_to_csv_line($templates[$data_type]);
        
        return array(
            'filename' => $filename,
            'content' => $csv_content,
            'mimetype' => 'text/csv'
        );
    }
    
    /**
     * AJAX handlers
     */
    public static function ajax_export_data() {
        check_ajax_referer('phpm_admin_nonce', 'nonce');
        
        if (!current_user_can('export')) {
            wp_send_json_error(__('Insufficient permissions.', 'plughaus-property'));
        }
        
        $data_type = sanitize_text_field($_POST['data_type']);
        $format = sanitize_text_field($_POST['format']);
        $filters = isset($_POST['filters']) ? $_POST['filters'] : array();
        
        $result = self::export_data($data_type, $format, $filters);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        // For file exports, store temporarily and return download URL
        if (isset($result['filepath'])) {
            // ZIP file - return download URL
            $upload_dir = wp_upload_dir();
            $file_url = str_replace($upload_dir['basedir'], $upload_dir['baseurl'], $result['filepath']);
            
            wp_send_json_success(array(
                'download_url' => $file_url,
                'filename' => $result['filename']
            ));
        } else {
            // Direct content - trigger download
            wp_send_json_success(array(
                'content' => $result['content'],
                'filename' => $result['filename'],
                'mimetype' => $result['mimetype']
            ));
        }
    }
    
    public static function ajax_import_data() {
        check_ajax_referer('phpm_admin_nonce', 'nonce');
        
        if (!current_user_can('import')) {
            wp_send_json_error(__('Insufficient permissions.', 'plughaus-property'));
        }
        
        if (!isset($_FILES['import_file'])) {
            wp_send_json_error(__('No file uploaded.', 'plughaus-property'));
        }
        
        $file = $_FILES['import_file'];
        $data_type = sanitize_text_field($_POST['data_type']);
        $options = array(
            'update_existing' => isset($_POST['update_existing']) && $_POST['update_existing'] === 'true'
        );
        
        // Move uploaded file to temp location
        $upload_dir = wp_upload_dir();
        $temp_dir = $upload_dir['basedir'] . '/phpm-imports/';
        wp_mkdir_p($temp_dir);
        
        $temp_file = $temp_dir . basename($file['name']);
        if (!move_uploaded_file($file['tmp_name'], $temp_file)) {
            wp_send_json_error(__('Failed to process upload.', 'plughaus-property'));
        }
        
        $result = self::import_data($temp_file, $data_type, $options);
        
        // Clean up temp file
        unlink($temp_file);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success($result);
    }
    
    public static function ajax_download_template() {
        check_ajax_referer('phpm_admin_nonce', 'nonce');
        
        if (!current_user_can('export')) {
            wp_send_json_error(__('Insufficient permissions.', 'plughaus-property'));
        }
        
        $data_type = sanitize_text_field($_POST['data_type']);
        $result = self::generate_template($data_type);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success($result);
    }
    
    /**
     * Handle file upload
     */
    public static function handle_file_upload() {
        // This method can be used for future file upload enhancements
    }
}

// Initialize import/export
PHPM_Import_Export::init();