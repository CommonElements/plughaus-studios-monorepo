<?php
/**
 * EquipRent Pro Activator
 *
 * @package EquipRent_Pro
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Fired during plugin activation
 */
class ER_Activator {

    /**
     * Database schema version
     */
    const DB_VERSION = '1.0.0';

    /**
     * Plugin activation tasks
     */
    public static function activate() {
        // Check WordPress and PHP versions
        self::check_requirements();
        
        // Register post types and taxonomies first
        self::register_content_types();
        
        // Create database tables
        self::create_tables();
        
        // Set default options
        self::set_default_options();
        
        // Create default pages
        self::create_default_pages();
        
        // Set capabilities
        self::set_capabilities();
        
        // Setup sample data if requested
        self::maybe_create_sample_data();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Set activation flags and version
        add_option('equiprent_activated', true);
        add_option('equiprent_activation_date', current_time('mysql'));
        add_option('equiprent_db_version', self::DB_VERSION);
        add_option('equiprent_version', EQUIPRENT_VERSION);
        
        // Log activation
        self::log_activation();
    }

    /**
     * Check WordPress and PHP requirements
     */
    private static function check_requirements() {
        // Check PHP version
        if (version_compare(PHP_VERSION, '7.4', '<')) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die(__('EquipRent Pro requires PHP 7.4 or higher. You are running PHP ' . PHP_VERSION, 'equiprent-pro'));
        }

        // Check WordPress version
        global $wp_version;
        if (version_compare($wp_version, '5.8', '<')) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die(__('EquipRent Pro requires WordPress 5.8 or higher. You are running WordPress ' . $wp_version, 'equiprent-pro'));
        }
    }

    /**
     * Register post types and taxonomies
     */
    private static function register_content_types() {
        // Load and register post types
        if (file_exists(EQUIPRENT_CORE_DIR . 'includes/core/class-er-post-types.php')) {
            require_once EQUIPRENT_CORE_DIR . 'includes/core/class-er-post-types.php';
            if (class_exists('ER_Post_Types')) {
                ER_Post_Types::register_post_types();
            }
        }
        
        // Load and register taxonomies
        if (file_exists(EQUIPRENT_CORE_DIR . 'includes/core/class-er-taxonomies.php')) {
            require_once EQUIPRENT_CORE_DIR . 'includes/core/class-er-taxonomies.php';
            if (class_exists('ER_Taxonomies')) {
                ER_Taxonomies::register_taxonomies();
            }
        }
    }

    /**
     * Create database tables
     */
    private static function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Equipment table
        $table_equipment = $wpdb->prefix . 'er_equipment';
        $sql_equipment = "CREATE TABLE $table_equipment (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            post_id bigint(20) unsigned NOT NULL,
            sku varchar(100) NOT NULL,
            barcode varchar(100) DEFAULT NULL,
            serial_number varchar(100) DEFAULT NULL,
            purchase_date date DEFAULT NULL,
            purchase_price decimal(10,2) DEFAULT NULL,
            daily_rate decimal(10,2) DEFAULT NULL,
            weekly_rate decimal(10,2) DEFAULT NULL,
            monthly_rate decimal(10,2) DEFAULT NULL,
            status varchar(20) DEFAULT 'available',
            condition_rating int(1) DEFAULT 5,
            last_maintenance date DEFAULT NULL,
            next_maintenance date DEFAULT NULL,
            location varchar(255) DEFAULT NULL,
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY sku (sku),
            KEY post_id (post_id),
            KEY status (status)
        ) $charset_collate;";

        // Bookings table
        $table_bookings = $wpdb->prefix . 'er_bookings';
        $sql_bookings = "CREATE TABLE $table_bookings (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            booking_number varchar(50) NOT NULL,
            customer_id bigint(20) unsigned NOT NULL,
            status varchar(20) DEFAULT 'pending',
            booking_date datetime DEFAULT CURRENT_TIMESTAMP,
            start_date datetime NOT NULL,
            end_date datetime NOT NULL,
            pickup_date datetime DEFAULT NULL,
            return_date datetime DEFAULT NULL,
            delivery_required tinyint(1) DEFAULT 0,
            delivery_address text,
            pickup_required tinyint(1) DEFAULT 0,
            pickup_address text,
            subtotal decimal(10,2) DEFAULT 0.00,
            tax_amount decimal(10,2) DEFAULT 0.00,
            delivery_fee decimal(10,2) DEFAULT 0.00,
            total_amount decimal(10,2) DEFAULT 0.00,
            deposit_amount decimal(10,2) DEFAULT 0.00,
            payment_status varchar(20) DEFAULT 'pending',
            payment_method varchar(50) DEFAULT NULL,
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY booking_number (booking_number),
            KEY customer_id (customer_id),
            KEY status (status),
            KEY start_date (start_date),
            KEY end_date (end_date)
        ) $charset_collate;";

        // Booking items table
        $table_booking_items = $wpdb->prefix . 'er_booking_items';
        $sql_booking_items = "CREATE TABLE $table_booking_items (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            booking_id bigint(20) unsigned NOT NULL,
            equipment_id bigint(20) unsigned NOT NULL,
            quantity int(11) DEFAULT 1,
            daily_rate decimal(10,2) DEFAULT 0.00,
            total_days int(11) DEFAULT 1,
            subtotal decimal(10,2) DEFAULT 0.00,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY booking_id (booking_id),
            KEY equipment_id (equipment_id)
        ) $charset_collate;";

        // Customers table (extends WP users)
        $table_customers = $wpdb->prefix . 'er_customers';
        $sql_customers = "CREATE TABLE $table_customers (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned DEFAULT NULL,
            customer_type varchar(20) DEFAULT 'individual',
            company_name varchar(255) DEFAULT NULL,
            contact_person varchar(255) DEFAULT NULL,
            phone varchar(20) DEFAULT NULL,
            alt_phone varchar(20) DEFAULT NULL,
            address_line1 varchar(255) DEFAULT NULL,
            address_line2 varchar(255) DEFAULT NULL,
            city varchar(100) DEFAULT NULL,
            state varchar(100) DEFAULT NULL,
            postal_code varchar(20) DEFAULT NULL,
            country varchar(100) DEFAULT NULL,
            emergency_contact_name varchar(255) DEFAULT NULL,
            emergency_contact_phone varchar(20) DEFAULT NULL,
            driver_license varchar(50) DEFAULT NULL,
            credit_limit decimal(10,2) DEFAULT 0.00,
            payment_terms int(11) DEFAULT 30,
            tax_exempt tinyint(1) DEFAULT 0,
            customer_notes text,
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY status (status)
        ) $charset_collate;";

        // Maintenance records table
        $table_maintenance = $wpdb->prefix . 'er_maintenance';
        $sql_maintenance = "CREATE TABLE $table_maintenance (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            equipment_id bigint(20) unsigned NOT NULL,
            maintenance_type varchar(50) NOT NULL,
            description text,
            performed_by varchar(255) DEFAULT NULL,
            performed_date datetime DEFAULT NULL,
            cost decimal(10,2) DEFAULT 0.00,
            next_due_date date DEFAULT NULL,
            status varchar(20) DEFAULT 'scheduled',
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY equipment_id (equipment_id),
            KEY maintenance_type (maintenance_type),
            KEY status (status),
            KEY next_due_date (next_due_date)
        ) $charset_collate;";

        // Damage reports table
        $table_damage = $wpdb->prefix . 'er_damage_reports';
        $sql_damage = "CREATE TABLE $table_damage (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            equipment_id bigint(20) unsigned NOT NULL,
            booking_id bigint(20) unsigned DEFAULT NULL,
            customer_id bigint(20) unsigned DEFAULT NULL,
            damage_type varchar(50) NOT NULL,
            severity varchar(20) DEFAULT 'minor',
            description text NOT NULL,
            repair_cost decimal(10,2) DEFAULT 0.00,
            repair_notes text,
            reported_by bigint(20) unsigned DEFAULT NULL,
            reported_date datetime DEFAULT CURRENT_TIMESTAMP,
            status varchar(20) DEFAULT 'open',
            resolved_date datetime DEFAULT NULL,
            PRIMARY KEY (id),
            KEY equipment_id (equipment_id),
            KEY booking_id (booking_id),
            KEY customer_id (customer_id),
            KEY status (status),
            KEY reported_date (reported_date)
        ) $charset_collate;";

        // Payments table
        $table_payments = $wpdb->prefix . 'er_payments';
        $sql_payments = "CREATE TABLE $table_payments (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            booking_id bigint(20) unsigned NOT NULL,
            customer_id bigint(20) unsigned NOT NULL,
            payment_type varchar(20) DEFAULT 'rental',
            amount decimal(10,2) NOT NULL,
            payment_date datetime DEFAULT CURRENT_TIMESTAMP,
            payment_method varchar(50) DEFAULT NULL,
            transaction_id varchar(100) DEFAULT NULL,
            gateway varchar(50) DEFAULT NULL,
            status varchar(20) DEFAULT 'pending',
            notes text,
            refunded_amount decimal(10,2) DEFAULT 0.00,
            refund_date datetime DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY transaction_id (transaction_id),
            KEY booking_id (booking_id),
            KEY customer_id (customer_id),
            KEY payment_date (payment_date),
            KEY status (status)
        ) $charset_collate;";

        // Activity log table
        $table_activity = $wpdb->prefix . 'er_activity_log';
        $sql_activity = "CREATE TABLE $table_activity (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            entity_type varchar(50) NOT NULL,
            entity_id bigint(20) unsigned NOT NULL,
            action varchar(50) NOT NULL,
            description text,
            user_id bigint(20) unsigned DEFAULT NULL,
            ip_address varchar(45) DEFAULT NULL,
            user_agent text,
            metadata longtext,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY entity_type_id (entity_type, entity_id),
            KEY user_id (user_id),
            KEY action (action),
            KEY created_at (created_at)
        ) $charset_collate;";

        // Availability calendar table
        $table_availability = $wpdb->prefix . 'er_availability';
        $sql_availability = "CREATE TABLE $table_availability (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            equipment_id bigint(20) unsigned NOT NULL,
            date date NOT NULL,
            available_quantity int(11) DEFAULT 0,
            reserved_quantity int(11) DEFAULT 0,
            maintenance_quantity int(11) DEFAULT 0,
            status varchar(20) DEFAULT 'available',
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY equipment_date (equipment_id, date),
            KEY equipment_id (equipment_id),
            KEY date (date),
            KEY status (status)
        ) $charset_collate;";

        // Documents table
        $table_documents = $wpdb->prefix . 'er_documents';
        $sql_documents = "CREATE TABLE $table_documents (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            entity_type varchar(50) NOT NULL,
            entity_id bigint(20) unsigned NOT NULL,
            document_type varchar(50) NOT NULL,
            file_name varchar(255) NOT NULL,
            file_path varchar(500) NOT NULL,
            file_size bigint(20) unsigned DEFAULT NULL,
            mime_type varchar(100) DEFAULT NULL,
            description text,
            uploaded_by bigint(20) unsigned DEFAULT NULL,
            uploaded_at datetime DEFAULT CURRENT_TIMESTAMP,
            is_public tinyint(1) DEFAULT 0,
            PRIMARY KEY (id),
            KEY entity_type_id (entity_type, entity_id),
            KEY document_type (document_type),
            KEY uploaded_by (uploaded_by),
            KEY uploaded_at (uploaded_at)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        dbDelta($sql_equipment);
        dbDelta($sql_bookings);
        dbDelta($sql_booking_items);
        dbDelta($sql_customers);
        dbDelta($sql_maintenance);
        dbDelta($sql_damage);
        dbDelta($sql_payments);
        dbDelta($sql_activity);
        dbDelta($sql_availability);
        dbDelta($sql_documents);

        // Update database version
        update_option('equiprent_db_version', self::DB_VERSION);
    }

    /**
     * Set default plugin options
     */
    private static function set_default_options() {
        // Core settings
        $default_settings = array(
            'currency' => 'USD',
            'currency_symbol' => '$',
            'currency_position' => 'before',
            'decimal_separator' => '.',
            'thousand_separator' => ',',
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i',
            'timezone' => get_option('timezone_string', 'UTC'),
            'business_name' => get_bloginfo('name'),
            'business_email' => get_option('admin_email'),
            'business_phone' => '',
            'business_address' => '',
            'booking_prefix' => 'ER',
            'default_rental_period' => 'daily',
            'min_rental_period' => 1,
            'max_rental_period' => 365,
            'require_deposit' => true,
            'default_deposit_percentage' => 25,
            'late_fee_enabled' => true,
            'late_fee_amount' => 25.00,
            'late_fee_type' => 'fixed',
            'grace_period_hours' => 2,
            'tax_enabled' => false,
            'tax_rate' => 8.25,
            'tax_inclusive' => false,
            'inventory_tracking' => true,
            'low_stock_threshold' => 5,
            'maintenance_alerts' => true,
            'email_notifications' => true,
            'sms_notifications' => false,
            'booking_confirmation_email' => true,
            'payment_reminder_email' => true,
            'damage_report_email' => true,
            'maintenance_due_email' => true,
            'api_enabled' => true,
            'customer_portal_enabled' => true,
            'online_booking_enabled' => true,
            'delivery_service_enabled' => false,
            'pickup_service_enabled' => false,
            'damage_waiver_enabled' => false,
            'damage_waiver_fee' => 0.00,
            'preserve_data_on_uninstall' => false,
            'debug_mode' => false,
        );

        // Only set if settings don't exist
        if (!get_option('equiprent_settings')) {
            update_option('equiprent_settings', $default_settings);
        }

        // Individual options for backwards compatibility
        $individual_options = array(
            'equiprent_currency' => $default_settings['currency'],
            'equiprent_currency_symbol' => $default_settings['currency_symbol'],
            'equiprent_currency_position' => $default_settings['currency_position'],
            'equiprent_date_format' => $default_settings['date_format'],
            'equiprent_time_format' => $default_settings['time_format'],
            'equiprent_timezone' => $default_settings['timezone'],
            'equiprent_business_name' => $default_settings['business_name'],
            'equiprent_business_email' => $default_settings['business_email'],
            'equiprent_booking_prefix' => $default_settings['booking_prefix'],
            'equiprent_default_rental_period' => $default_settings['default_rental_period'],
            'equiprent_require_deposit' => $default_settings['require_deposit'],
            'equiprent_default_deposit_percentage' => $default_settings['default_deposit_percentage'],
            'equiprent_late_fee_enabled' => $default_settings['late_fee_enabled'],
            'equiprent_late_fee_amount' => $default_settings['late_fee_amount'],
            'equiprent_late_fee_type' => $default_settings['late_fee_type'],
            'equiprent_tax_enabled' => $default_settings['tax_enabled'],
            'equiprent_tax_rate' => $default_settings['tax_rate'],
            'equiprent_inventory_tracking' => $default_settings['inventory_tracking'],
            'equiprent_low_stock_threshold' => $default_settings['low_stock_threshold'],
            'equiprent_email_notifications' => $default_settings['email_notifications'],
            'equiprent_sms_notifications' => $default_settings['sms_notifications'],
        );

        foreach ($individual_options as $option => $value) {
            add_option($option, $value);
        }

        // Default email templates
        $email_templates = array(
            'booking_confirmation' => array(
                'subject' => 'Booking Confirmation - {booking_number}',
                'body' => 'Dear {customer_name},\n\nYour booking has been confirmed.\n\nBooking Number: {booking_number}\nStart Date: {start_date}\nEnd Date: {end_date}\nTotal Amount: {total_amount}\n\nThank you for your business!'
            ),
            'payment_reminder' => array(
                'subject' => 'Payment Reminder - {booking_number}',
                'body' => 'Dear {customer_name},\n\nThis is a reminder that payment is due for your booking.\n\nBooking Number: {booking_number}\nAmount Due: {amount_due}\nDue Date: {due_date}\n\nPlease make payment as soon as possible.'
            ),
            'return_reminder' => array(
                'subject' => 'Equipment Return Reminder - {booking_number}',
                'body' => 'Dear {customer_name},\n\nThis is a reminder that your rental equipment is due to be returned.\n\nBooking Number: {booking_number}\nReturn Date: {return_date}\nEquipment: {equipment_list}\n\nPlease return the equipment on time to avoid late fees.'
            ),
        );

        add_option('equiprent_email_templates', $email_templates);
    }

    /**
     * Create default pages
     */
    private static function create_default_pages() {
        $pages = array(
            'equipment-catalog' => array(
                'title' => __('Equipment Catalog', 'equiprent-pro'),
                'content' => '[equiprent_catalog]',
                'slug' => 'equipment-catalog',
                'description' => 'Browse our complete equipment catalog and check availability'
            ),
            'booking-form' => array(
                'title' => __('Make a Reservation', 'equiprent-pro'),
                'content' => '[equiprent_booking_form]',
                'slug' => 'make-reservation',
                'description' => 'Create a new equipment rental reservation'
            ),
            'customer-dashboard' => array(
                'title' => __('Customer Dashboard', 'equiprent-pro'),
                'content' => '[equiprent_customer_dashboard]',
                'slug' => 'customer-dashboard',
                'description' => 'Customer portal for managing bookings and account'
            ),
            'my-bookings' => array(
                'title' => __('My Bookings', 'equiprent-pro'),
                'content' => '[equiprent_customer_bookings]',
                'slug' => 'my-bookings',
                'description' => 'View and manage your current and past bookings'
            ),
            'booking-checkout' => array(
                'title' => __('Booking Checkout', 'equiprent-pro'),
                'content' => '[equiprent_checkout]',
                'slug' => 'booking-checkout',
                'description' => 'Complete your booking and payment'
            ),
            'booking-confirmation' => array(
                'title' => __('Booking Confirmation', 'equiprent-pro'),
                'content' => '[equiprent_booking_confirmation]',
                'slug' => 'booking-confirmation',
                'description' => 'Booking confirmation and receipt'
            ),
            'terms-conditions' => array(
                'title' => __('Terms and Conditions', 'equiprent-pro'),
                'content' => 'Please add your equipment rental terms and conditions here.',
                'slug' => 'rental-terms-conditions',
                'description' => 'Equipment rental terms and conditions'
            ),
            'damage-waiver' => array(
                'title' => __('Damage Waiver', 'equiprent-pro'),
                'content' => 'Please add your damage waiver policy here.',
                'slug' => 'damage-waiver',
                'description' => 'Equipment damage waiver information'
            )
        );

        foreach ($pages as $key => $page_data) {
            // Check if page already exists
            $existing_page = get_page_by_path($page_data['slug']);
            
            if (!$existing_page) {
                $page_id = wp_insert_post(array(
                    'post_title' => $page_data['title'],
                    'post_content' => $page_data['content'],
                    'post_name' => $page_data['slug'],
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'post_excerpt' => $page_data['description'],
                    'meta_input' => array(
                        '_equiprent_page_type' => $key,
                        '_equiprent_page_description' => $page_data['description']
                    )
                ));

                if ($page_id && !is_wp_error($page_id)) {
                    add_option('equiprent_page_' . $key, $page_id);
                }
            } else {
                add_option('equiprent_page_' . $key, $existing_page->ID);
            }
        }
    }

    /**
     * Set user capabilities
     */
    private static function set_capabilities() {
        $admin_role = get_role('administrator');
        
        if ($admin_role) {
            // Equipment management capabilities
            $admin_role->add_cap('manage_equipment');
            $admin_role->add_cap('edit_equipment');
            $admin_role->add_cap('delete_equipment');
            $admin_role->add_cap('create_equipment');
            $admin_role->add_cap('publish_equipment');
            
            // Booking management capabilities
            $admin_role->add_cap('manage_bookings');
            $admin_role->add_cap('edit_bookings');
            $admin_role->add_cap('delete_bookings');
            $admin_role->add_cap('create_bookings');
            $admin_role->add_cap('process_bookings');
            $admin_role->add_cap('cancel_bookings');
            
            // Customer management capabilities
            $admin_role->add_cap('manage_rental_customers');
            $admin_role->add_cap('edit_rental_customers');
            $admin_role->add_cap('delete_rental_customers');
            $admin_role->add_cap('view_customer_data');
            
            // Payment capabilities
            $admin_role->add_cap('manage_payments');
            $admin_role->add_cap('process_payments');
            $admin_role->add_cap('refund_payments');
            
            // Maintenance capabilities
            $admin_role->add_cap('manage_maintenance');
            $admin_role->add_cap('schedule_maintenance');
            $admin_role->add_cap('complete_maintenance');
            
            // Damage report capabilities
            $admin_role->add_cap('manage_damage_reports');
            $admin_role->add_cap('create_damage_reports');
            $admin_role->add_cap('resolve_damage_reports');
            
            // Settings capabilities
            $admin_role->add_cap('manage_equiprent_settings');
            $admin_role->add_cap('configure_pricing');
            $admin_role->add_cap('manage_email_templates');
            
            // Reporting capabilities
            $admin_role->add_cap('view_equiprent_reports');
            $admin_role->add_cap('export_data');
            $admin_role->add_cap('view_analytics');
        }

        // Create rental manager role
        add_role('rental_manager', __('Rental Manager', 'equiprent-pro'), array(
            'read' => true,
            'manage_equipment' => true,
            'edit_equipment' => true,
            'create_equipment' => true,
            'publish_equipment' => true,
            'manage_bookings' => true,
            'edit_bookings' => true,
            'create_bookings' => true,
            'process_bookings' => true,
            'cancel_bookings' => true,
            'manage_rental_customers' => true,
            'edit_rental_customers' => true,
            'view_customer_data' => true,
            'manage_payments' => true,
            'process_payments' => true,
            'manage_maintenance' => true,
            'schedule_maintenance' => true,
            'complete_maintenance' => true,
            'manage_damage_reports' => true,
            'create_damage_reports' => true,
            'resolve_damage_reports' => true,
            'view_equiprent_reports' => true,
            'export_data' => true,
            'view_analytics' => true,
        ));

        // Create rental staff role
        add_role('rental_staff', __('Rental Staff', 'equiprent-pro'), array(
            'read' => true,
            'edit_equipment' => true,
            'create_bookings' => true,
            'edit_bookings' => true,
            'process_bookings' => true,
            'manage_rental_customers' => true,
            'edit_rental_customers' => true,
            'view_customer_data' => true,
            'process_payments' => true,
            'schedule_maintenance' => true,
            'create_damage_reports' => true,
            'view_equiprent_reports' => true,
        ));

        // Create rental customer role
        add_role('rental_customer', __('Rental Customer', 'equiprent-pro'), array(
            'read' => true,
            'create_bookings' => true,
            'edit_own_bookings' => true,
            'view_own_bookings' => true,
            'upload_files' => true,
        ));
    }

    /**
     * Maybe create sample data
     */
    private static function maybe_create_sample_data() {
        // Only create sample data if requested
        if (!apply_filters('equiprent_create_sample_data', false)) {
            return;
        }

        // Create sample equipment categories
        $categories = array(
            'Power Tools' => 'Professional grade power tools for construction and DIY projects',
            'Construction Equipment' => 'Heavy duty construction equipment and machinery',
            'Party & Event Equipment' => 'Tables, chairs, tents, and party supplies',
            'Audio Visual' => 'Sound systems, projectors, and AV equipment',
            'Lawn & Garden' => 'Landscaping tools and garden equipment',
        );

        foreach ($categories as $cat_name => $cat_desc) {
            if (!term_exists($cat_name, 'equipment_category')) {
                wp_insert_term($cat_name, 'equipment_category', array(
                    'description' => $cat_desc,
                    'slug' => sanitize_title($cat_name)
                ));
            }
        }

        // Sample equipment types would go here if taxonomies are set up
        // This can be expanded based on the actual taxonomy structure
    }

    /**
     * Log activation
     */
    private static function log_activation() {
        // Create activation log entry
        if (class_exists('ER_Utilities')) {
            ER_Utilities::log('EquipRent Pro activated successfully', 'info', array(
                'version' => EQUIPRENT_VERSION,
                'db_version' => self::DB_VERSION,
                'php_version' => PHP_VERSION,
                'wp_version' => get_bloginfo('version'),
                'activation_date' => current_time('mysql')
            ));
        }
    }

    /**
     * Check for database updates
     */
    public static function check_for_updates() {
        $current_db_version = get_option('equiprent_db_version', '0.0.0');
        
        if (version_compare($current_db_version, self::DB_VERSION, '<')) {
            self::update_database($current_db_version);
        }
    }

    /**
     * Update database schema
     */
    private static function update_database($from_version) {
        global $wpdb;
        
        // Future database updates would go here
        // Example:
        // if (version_compare($from_version, '1.1.0', '<')) {
        //     // Add new columns or tables for version 1.1.0
        // }
        
        // Update the database version
        update_option('equiprent_db_version', self::DB_VERSION);
        
        // Log the update
        if (class_exists('ER_Utilities')) {
            ER_Utilities::log("Database updated from version {$from_version} to " . self::DB_VERSION, 'info');
        }
    }

    /**
     * Get activation date
     */
    public static function get_activation_date() {
        return get_option('equiprent_activation_date');
    }

    /**
     * Check if plugin is activated
     */
    public static function is_activated() {
        return get_option('equiprent_activated', false);
    }

    /**
     * Get database version
     */
    public static function get_db_version() {
        return get_option('equiprent_db_version', '0.0.0');
    }
}