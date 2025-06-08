<?php
/**
 * Fired during plugin activation
 */

if (!defined('ABSPATH')) {
    exit;
}

class PHPM_Activator {
    
    /**
     * Plugin activation
     */
    public static function activate() {
        // Create database tables
        self::create_tables();
        
        // Add capabilities
        PHPM_Capabilities::add_capabilities();
        
        // Create default pages
        self::create_pages();
        
        // Schedule cron jobs
        self::schedule_events();
        
        // Set default options
        self::set_default_options();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Create custom database tables
     */
    private static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Property views tracking table
        $table_name = $wpdb->prefix . 'phpm_property_views';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            property_id bigint(20) NOT NULL,
            user_id bigint(20) DEFAULT NULL,
            ip_address varchar(45) DEFAULT NULL,
            view_date datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY property_id (property_id),
            KEY user_id (user_id),
            KEY view_date (view_date)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Maintenance log table
        $table_name = $wpdb->prefix . 'phpm_maintenance_log';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            request_id bigint(20) NOT NULL,
            user_id bigint(20) NOT NULL,
            action varchar(50) NOT NULL,
            note text,
            log_date datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY request_id (request_id),
            KEY user_id (user_id),
            KEY log_date (log_date)
        ) $charset_collate;";
        
        dbDelta($sql);
        
        // Payment history table
        $table_name = $wpdb->prefix . 'phpm_payments';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            lease_id bigint(20) NOT NULL,
            tenant_id bigint(20) NOT NULL,
            amount decimal(10,2) NOT NULL,
            payment_date date NOT NULL,
            payment_method varchar(50),
            transaction_id varchar(255),
            status varchar(20) DEFAULT 'pending',
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY lease_id (lease_id),
            KEY tenant_id (tenant_id),
            KEY payment_date (payment_date),
            KEY status (status)
        ) $charset_collate;";
        
        dbDelta($sql);
    }
    
    /**
     * Create default pages
     */
    private static function create_pages() {
        $pages = array(
            array(
                'title' => __('Properties', 'plughaus-property'),
                'content' => '[phpm_property_search]' . "\n\n" . '[phpm_properties]',
                'option' => 'phpm_properties_page_id',
            ),
            array(
                'title' => __('Tenant Portal', 'plughaus-property'),
                'content' => '[phpm_tenant_portal]',
                'option' => 'phpm_tenant_portal_page_id',
            ),
            array(
                'title' => __('Submit Maintenance Request', 'plughaus-property'),
                'content' => '[phpm_maintenance_request]',
                'option' => 'phpm_maintenance_page_id',
            ),
        );
        
        foreach ($pages as $page) {
            $page_id = wp_insert_post(array(
                'post_title' => $page['title'],
                'post_content' => $page['content'],
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_author' => get_current_user_id(),
            ));
            
            if ($page_id && !is_wp_error($page_id)) {
                update_option($page['option'], $page_id);
            }
        }
    }
    
    /**
     * Schedule cron events
     */
    private static function schedule_events() {
        // Schedule daily lease expiration check
        if (!wp_next_scheduled('phpm_daily_lease_check')) {
            wp_schedule_event(time(), 'daily', 'phpm_daily_lease_check');
        }
        
        // Schedule weekly reports
        if (!wp_next_scheduled('phpm_weekly_reports')) {
            wp_schedule_event(time(), 'weekly', 'phpm_weekly_reports');
        }
    }
    
    /**
     * Set default plugin options
     */
    private static function set_default_options() {
        $default_options = array(
            'currency' => 'USD',
            'date_format' => get_option('date_format'),
            'admin_email' => get_option('admin_email'),
            'email_notifications' => array('new_tenant', 'lease_expiry', 'maintenance_request'),
            'enable_api' => 1,
            'lease_expiry_notice_days' => 30,
            'late_fee_percentage' => 5,
            'maintenance_auto_assign' => 0,
        );
        
        $existing_options = get_option('phpm_settings', array());
        $options = wp_parse_args($existing_options, $default_options);
        
        update_option('phpm_settings', $options);
        
        // Set plugin version
        update_option('phpm_version', PHPM_VERSION);
    }
}