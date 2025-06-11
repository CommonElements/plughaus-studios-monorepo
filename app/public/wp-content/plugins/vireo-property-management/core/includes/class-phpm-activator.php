<?php
/**
 * Plugin Activator for PlugHaus Property Management
 * Handles plugin activation tasks
 */

if (!defined('ABSPATH')) {
    exit;
}

class PHPM_Activator {
    
    /**
     * Activate the plugin
     */
    public static function activate() {
        // Create database tables
        self::create_database_tables();
        
        // Register post types and taxonomies
        self::register_content_types();
        
        // Set up user capabilities
        self::setup_capabilities();
        
        // Create default pages
        self::create_default_pages();
        
        // Set default options
        self::set_default_options();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Log activation
        if (class_exists('PHPM_Utilities')) {
            PHPM_Utilities::log('Plugin activated successfully', 'info');
        }
        
        // Set activation timestamp
        update_option('phpm_activation_date', current_time('timestamp'));
        update_option('phpm_version', PHPM_VERSION);
    }
    
    /**
     * Create database tables
     */
    private static function create_database_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Property views table
        $table_name = $wpdb->prefix . 'phpm_property_views';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            property_id bigint(20) unsigned NOT NULL,
            user_id bigint(20) unsigned DEFAULT NULL,
            ip_address varchar(45) NOT NULL,
            user_agent text,
            viewed_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY property_id (property_id),
            KEY user_id (user_id),
            KEY viewed_at (viewed_at)
        ) $charset_collate;";
        
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
        
        // Maintenance log table
        $table_name = $wpdb->prefix . 'phpm_maintenance_log';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            maintenance_id bigint(20) unsigned NOT NULL,
            action varchar(50) NOT NULL,
            notes text,
            user_id bigint(20) unsigned DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY maintenance_id (maintenance_id),
            KEY user_id (user_id),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        dbDelta($sql);
        
        // Payments table
        $table_name = $wpdb->prefix . 'phpm_payments';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            lease_id bigint(20) unsigned NOT NULL,
            tenant_id bigint(20) unsigned NOT NULL,
            amount decimal(10,2) NOT NULL,
            payment_date date NOT NULL,
            payment_method varchar(50) DEFAULT NULL,
            transaction_id varchar(100) DEFAULT NULL,
            status varchar(20) DEFAULT 'pending',
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY lease_id (lease_id),
            KEY tenant_id (tenant_id),
            KEY payment_date (payment_date),
            KEY status (status)
        ) $charset_collate;";
        
        dbDelta($sql);
        
        // Lease history table
        $table_name = $wpdb->prefix . 'phpm_lease_history';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            lease_id bigint(20) unsigned NOT NULL,
            field_changed varchar(100) NOT NULL,
            old_value text,
            new_value text,
            user_id bigint(20) unsigned DEFAULT NULL,
            changed_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY lease_id (lease_id),
            KEY user_id (user_id),
            KEY changed_at (changed_at)
        ) $charset_collate;";
        
        dbDelta($sql);
        
        // Documents table
        $table_name = $wpdb->prefix . 'phpm_documents';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            property_id bigint(20) unsigned DEFAULT NULL,
            unit_id bigint(20) unsigned DEFAULT NULL,
            tenant_id bigint(20) unsigned DEFAULT NULL,
            lease_id bigint(20) unsigned DEFAULT NULL,
            document_type varchar(50) NOT NULL,
            file_name varchar(255) NOT NULL,
            file_path varchar(500) NOT NULL,
            file_size bigint(20) unsigned DEFAULT NULL,
            mime_type varchar(100) DEFAULT NULL,
            uploaded_by bigint(20) unsigned DEFAULT NULL,
            uploaded_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY property_id (property_id),
            KEY unit_id (unit_id),
            KEY tenant_id (tenant_id),
            KEY lease_id (lease_id),
            KEY uploaded_by (uploaded_by)
        ) $charset_collate;";
        
        dbDelta($sql);
    }
    
    /**
     * Register post types and taxonomies
     */
    private static function register_content_types() {
        // Load and register post types
        require_once PHPM_CORE_DIR . 'includes/core/class-phpm-post-types.php';
        PHPM_Post_Types::register_post_types();
        
        // Load and register taxonomies
        require_once PHPM_CORE_DIR . 'includes/core/class-phpm-taxonomies.php';
        PHPM_Taxonomies::register_taxonomies();
    }
    
    /**
     * Set up user capabilities
     */
    private static function setup_capabilities() {
        require_once PHPM_CORE_DIR . 'includes/core/class-phpm-capabilities.php';
        PHPM_Capabilities::add_capabilities();
    }
    
    /**
     * Create default pages
     */
    private static function create_default_pages() {
        $pages = array(
            'tenant-portal' => array(
                'title' => __('Tenant Portal', 'plughaus-property'),
                'content' => '[phpm_tenant_portal]',
                'option_name' => 'phpm_tenant_portal_page_id'
            ),
            'maintenance-request' => array(
                'title' => __('Maintenance Request', 'plughaus-property'),
                'content' => '[phpm_maintenance_form]',
                'option_name' => 'phpm_maintenance_page_id'
            ),
            'property-listings' => array(
                'title' => __('Property Listings', 'plughaus-property'),
                'content' => '[phpm_property_listings]',
                'option_name' => 'phpm_properties_page_id'
            )
        );
        
        foreach ($pages as $slug => $page_data) {
            // Check if page already exists
            $existing_page = get_page_by_path($slug);
            
            if (!$existing_page) {
                $page_id = wp_insert_post(array(
                    'post_type' => 'page',
                    'post_title' => $page_data['title'],
                    'post_content' => $page_data['content'],
                    'post_status' => 'publish',
                    'post_name' => $slug
                ));
                
                if (!is_wp_error($page_id)) {
                    update_option($page_data['option_name'], $page_id);
                }
            } else {
                update_option($page_data['option_name'], $existing_page->ID);
            }
        }
    }
    
    /**
     * Set default options
     */
    private static function set_default_options() {
        $default_settings = array(
            'currency' => 'USD',
            'date_format' => 'Y-m-d',
            'enable_api' => true,
            'enable_notifications' => true,
            'tenant_portal_enabled' => true,
            'maintenance_requests_enabled' => true,
            'preserve_data_on_uninstall' => false
        );
        
        // Only set if settings don't exist
        if (!get_option('phpm_settings')) {
            update_option('phpm_settings', $default_settings);
        }
    }
}