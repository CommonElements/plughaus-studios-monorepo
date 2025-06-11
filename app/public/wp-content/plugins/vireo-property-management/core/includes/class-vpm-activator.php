<?php
/**
 * Plugin Activator for Vireo Property Management
 * Handles plugin activation tasks
 */

if (!defined('ABSPATH')) {
    exit;
}

class VPM_Activator {
    
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
        if (class_exists('VPM_Utilities')) {
            VPM_Utilities::log('Plugin activated successfully', 'info');
        }
        
        // Set activation timestamp
        update_option('vpm_activation_date', current_time('timestamp'));
        update_option('vpm_version', VPM_VERSION);
    }
    
    /**
     * Create database tables
     */
    private static function create_database_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Properties table
        $table_name = $wpdb->prefix . 'vpm_properties';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            address text NOT NULL,
            city varchar(100) NOT NULL,
            state varchar(50) NOT NULL,
            zip_code varchar(20) NOT NULL,
            country varchar(100) DEFAULT 'United States',
            property_type varchar(50) DEFAULT 'residential',
            total_units int(11) DEFAULT 1,
            year_built year DEFAULT NULL,
            square_footage int(11) DEFAULT NULL,
            lot_size decimal(10,2) DEFAULT NULL,
            purchase_price decimal(10,2) DEFAULT NULL,
            current_value decimal(10,2) DEFAULT NULL,
            monthly_rent decimal(10,2) DEFAULT NULL,
            status varchar(20) DEFAULT 'active',
            description text,
            amenities text,
            owner_id bigint(20) unsigned DEFAULT NULL,
            manager_id bigint(20) unsigned DEFAULT NULL,
            insurance_policy varchar(255) DEFAULT NULL,
            insurance_expiry date DEFAULT NULL,
            property_tax_annual decimal(10,2) DEFAULT NULL,
            hoa_fees_monthly decimal(10,2) DEFAULT NULL,
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY owner_id (owner_id),
            KEY manager_id (manager_id),
            KEY status (status),
            KEY property_type (property_type),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
        
        // Units table
        $table_name = $wpdb->prefix . 'vpm_units';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            property_id bigint(20) unsigned NOT NULL,
            unit_number varchar(50) NOT NULL,
            unit_type varchar(50) DEFAULT 'apartment',
            bedrooms int(2) DEFAULT NULL,
            bathrooms decimal(3,1) DEFAULT NULL,
            square_footage int(11) DEFAULT NULL,
            rent_amount decimal(10,2) NOT NULL,
            deposit_amount decimal(10,2) DEFAULT NULL,
            pet_deposit decimal(10,2) DEFAULT NULL,
            status varchar(20) DEFAULT 'available',
            amenities text,
            description text,
            floor_plan varchar(255) DEFAULT NULL,
            parking_spaces int(2) DEFAULT 0,
            storage_unit tinyint(1) DEFAULT 0,
            balcony_patio tinyint(1) DEFAULT 0,
            furnished tinyint(1) DEFAULT 0,
            utilities_included text,
            lease_terms text,
            available_date date DEFAULT NULL,
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY property_id (property_id),
            KEY status (status),
            KEY available_date (available_date),
            UNIQUE KEY property_unit (property_id, unit_number),
            FOREIGN KEY (property_id) REFERENCES {$wpdb->prefix}vpm_properties(id) ON DELETE CASCADE
        ) $charset_collate;";
        
        dbDelta($sql);
        
        // Tenants table
        $table_name = $wpdb->prefix . 'vpm_tenants';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned DEFAULT NULL,
            first_name varchar(100) NOT NULL,
            last_name varchar(100) NOT NULL,
            email varchar(255) NOT NULL,
            phone varchar(20) DEFAULT NULL,
            mobile_phone varchar(20) DEFAULT NULL,
            date_of_birth date DEFAULT NULL,
            ssn_last_four varchar(4) DEFAULT NULL,
            employment_status varchar(50) DEFAULT NULL,
            employer varchar(255) DEFAULT NULL,
            monthly_income decimal(10,2) DEFAULT NULL,
            emergency_contact_name varchar(255) DEFAULT NULL,
            emergency_contact_phone varchar(20) DEFAULT NULL,
            emergency_contact_relationship varchar(100) DEFAULT NULL,
            current_address text,
            previous_address text,
            credit_score int(3) DEFAULT NULL,
            background_check_status varchar(20) DEFAULT 'pending',
            background_check_date date DEFAULT NULL,
            status varchar(20) DEFAULT 'active',
            move_in_date date DEFAULT NULL,
            move_out_date date DEFAULT NULL,
            security_deposit decimal(10,2) DEFAULT NULL,
            pet_deposit decimal(10,2) DEFAULT NULL,
            notes text,
            documents text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY email (email),
            KEY status (status),
            KEY move_in_date (move_in_date),
            KEY move_out_date (move_out_date),
            FOREIGN KEY (user_id) REFERENCES {$wpdb->users}(ID) ON DELETE SET NULL
        ) $charset_collate;";
        
        dbDelta($sql);
        
        // Leases table
        $table_name = $wpdb->prefix . 'vpm_leases';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            property_id bigint(20) unsigned NOT NULL,
            unit_id bigint(20) unsigned NOT NULL,
            tenant_id bigint(20) unsigned NOT NULL,
            lease_number varchar(100) DEFAULT NULL,
            lease_type varchar(50) DEFAULT 'fixed_term',
            start_date date NOT NULL,
            end_date date NOT NULL,
            rent_amount decimal(10,2) NOT NULL,
            deposit_amount decimal(10,2) DEFAULT NULL,
            pet_deposit decimal(10,2) DEFAULT NULL,
            late_fee_amount decimal(10,2) DEFAULT NULL,
            late_fee_grace_days int(2) DEFAULT 5,
            rent_due_day int(2) DEFAULT 1,
            payment_method varchar(50) DEFAULT NULL,
            auto_renewal tinyint(1) DEFAULT 0,
            renewal_notice_days int(3) DEFAULT 30,
            status varchar(20) DEFAULT 'active',
            terms_conditions text,
            special_conditions text,
            lease_document_path varchar(500) DEFAULT NULL,
            signed_date date DEFAULT NULL,
            co_signers text,
            utilities_included text,
            parking_included tinyint(1) DEFAULT 0,
            pets_allowed tinyint(1) DEFAULT 0,
            pet_restrictions text,
            smoking_allowed tinyint(1) DEFAULT 0,
            subletting_allowed tinyint(1) DEFAULT 0,
            notice_to_vacate_days int(3) DEFAULT 30,
            security_deposit_return_days int(3) DEFAULT 30,
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY property_id (property_id),
            KEY unit_id (unit_id),
            KEY tenant_id (tenant_id),
            KEY status (status),
            KEY start_date (start_date),
            KEY end_date (end_date),
            UNIQUE KEY lease_number (lease_number),
            FOREIGN KEY (property_id) REFERENCES {$wpdb->prefix}vpm_properties(id) ON DELETE CASCADE,
            FOREIGN KEY (unit_id) REFERENCES {$wpdb->prefix}vpm_units(id) ON DELETE CASCADE,
            FOREIGN KEY (tenant_id) REFERENCES {$wpdb->prefix}vpm_tenants(id) ON DELETE CASCADE
        ) $charset_collate;";
        
        dbDelta($sql);
        
        // Maintenance Requests table
        $table_name = $wpdb->prefix . 'vpm_maintenance_requests';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            property_id bigint(20) unsigned NOT NULL,
            unit_id bigint(20) unsigned DEFAULT NULL,
            tenant_id bigint(20) unsigned DEFAULT NULL,
            requested_by bigint(20) unsigned DEFAULT NULL,
            assigned_to bigint(20) unsigned DEFAULT NULL,
            title varchar(255) NOT NULL,
            description text NOT NULL,
            category varchar(100) DEFAULT NULL,
            priority varchar(20) DEFAULT 'medium',
            status varchar(20) DEFAULT 'open',
            estimated_cost decimal(10,2) DEFAULT NULL,
            actual_cost decimal(10,2) DEFAULT NULL,
            vendor_id bigint(20) unsigned DEFAULT NULL,
            vendor_contact varchar(255) DEFAULT NULL,
            scheduled_date datetime DEFAULT NULL,
            completed_date datetime DEFAULT NULL,
            tenant_access_required tinyint(1) DEFAULT 1,
            emergency_request tinyint(1) DEFAULT 0,
            images text,
            documents text,
            internal_notes text,
            tenant_notes text,
            work_performed text,
            materials_used text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY property_id (property_id),
            KEY unit_id (unit_id),
            KEY tenant_id (tenant_id),
            KEY assigned_to (assigned_to),
            KEY status (status),
            KEY priority (priority),
            KEY scheduled_date (scheduled_date),
            KEY created_at (created_at),
            FOREIGN KEY (property_id) REFERENCES {$wpdb->prefix}vpm_properties(id) ON DELETE CASCADE,
            FOREIGN KEY (unit_id) REFERENCES {$wpdb->prefix}vpm_units(id) ON DELETE SET NULL,
            FOREIGN KEY (tenant_id) REFERENCES {$wpdb->prefix}vpm_tenants(id) ON DELETE SET NULL
        ) $charset_collate;";
        
        dbDelta($sql);
        
        // Payments table
        $table_name = $wpdb->prefix . 'vpm_payments';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            lease_id bigint(20) unsigned NOT NULL,
            tenant_id bigint(20) unsigned NOT NULL,
            property_id bigint(20) unsigned NOT NULL,
            payment_type varchar(50) DEFAULT 'rent',
            amount decimal(10,2) NOT NULL,
            payment_date date NOT NULL,
            due_date date DEFAULT NULL,
            payment_method varchar(50) DEFAULT NULL,
            transaction_id varchar(100) DEFAULT NULL,
            check_number varchar(50) DEFAULT NULL,
            reference_number varchar(100) DEFAULT NULL,
            status varchar(20) DEFAULT 'pending',
            late_fee decimal(10,2) DEFAULT 0.00,
            other_fees decimal(10,2) DEFAULT 0.00,
            total_amount decimal(10,2) NOT NULL,
            processed_by bigint(20) unsigned DEFAULT NULL,
            processed_date datetime DEFAULT NULL,
            reconciled tinyint(1) DEFAULT 0,
            reconciled_date datetime DEFAULT NULL,
            notes text,
            receipt_number varchar(100) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY lease_id (lease_id),
            KEY tenant_id (tenant_id),
            KEY property_id (property_id),
            KEY payment_date (payment_date),
            KEY due_date (due_date),
            KEY status (status),
            KEY payment_type (payment_type),
            FOREIGN KEY (lease_id) REFERENCES {$wpdb->prefix}vpm_leases(id) ON DELETE CASCADE,
            FOREIGN KEY (tenant_id) REFERENCES {$wpdb->prefix}vpm_tenants(id) ON DELETE CASCADE,
            FOREIGN KEY (property_id) REFERENCES {$wpdb->prefix}vpm_properties(id) ON DELETE CASCADE
        ) $charset_collate;";
        
        dbDelta($sql);
        
        // Expenses table
        $table_name = $wpdb->prefix . 'vpm_expenses';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            property_id bigint(20) unsigned NOT NULL,
            unit_id bigint(20) unsigned DEFAULT NULL,
            maintenance_request_id bigint(20) unsigned DEFAULT NULL,
            expense_type varchar(100) NOT NULL,
            category varchar(100) DEFAULT NULL,
            vendor varchar(255) DEFAULT NULL,
            description text NOT NULL,
            amount decimal(10,2) NOT NULL,
            expense_date date NOT NULL,
            payment_method varchar(50) DEFAULT NULL,
            check_number varchar(50) DEFAULT NULL,
            invoice_number varchar(100) DEFAULT NULL,
            receipt_number varchar(100) DEFAULT NULL,
            tax_deductible tinyint(1) DEFAULT 1,
            recurring tinyint(1) DEFAULT 0,
            recurring_frequency varchar(20) DEFAULT NULL,
            status varchar(20) DEFAULT 'pending',
            paid_date date DEFAULT NULL,
            approved_by bigint(20) unsigned DEFAULT NULL,
            approved_date datetime DEFAULT NULL,
            receipt_image varchar(500) DEFAULT NULL,
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY property_id (property_id),
            KEY unit_id (unit_id),
            KEY maintenance_request_id (maintenance_request_id),
            KEY expense_date (expense_date),
            KEY expense_type (expense_type),
            KEY category (category),
            KEY status (status),
            FOREIGN KEY (property_id) REFERENCES {$wpdb->prefix}vpm_properties(id) ON DELETE CASCADE,
            FOREIGN KEY (unit_id) REFERENCES {$wpdb->prefix}vpm_units(id) ON DELETE SET NULL,
            FOREIGN KEY (maintenance_request_id) REFERENCES {$wpdb->prefix}vpm_maintenance_requests(id) ON DELETE SET NULL
        ) $charset_collate;";
        
        dbDelta($sql);
        
        // Documents table
        $table_name = $wpdb->prefix . 'vpm_documents';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            property_id bigint(20) unsigned DEFAULT NULL,
            unit_id bigint(20) unsigned DEFAULT NULL,
            tenant_id bigint(20) unsigned DEFAULT NULL,
            lease_id bigint(20) unsigned DEFAULT NULL,
            maintenance_request_id bigint(20) unsigned DEFAULT NULL,
            document_type varchar(50) NOT NULL,
            title varchar(255) NOT NULL,
            file_name varchar(255) NOT NULL,
            file_path varchar(500) NOT NULL,
            file_size bigint(20) unsigned DEFAULT NULL,
            mime_type varchar(100) DEFAULT NULL,
            description text,
            tags varchar(500) DEFAULT NULL,
            is_public tinyint(1) DEFAULT 0,
            expiry_date date DEFAULT NULL,
            uploaded_by bigint(20) unsigned DEFAULT NULL,
            uploaded_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY property_id (property_id),
            KEY unit_id (unit_id),
            KEY tenant_id (tenant_id),
            KEY lease_id (lease_id),
            KEY maintenance_request_id (maintenance_request_id),
            KEY document_type (document_type),
            KEY uploaded_by (uploaded_by),
            KEY expiry_date (expiry_date),
            FOREIGN KEY (property_id) REFERENCES {$wpdb->prefix}vpm_properties(id) ON DELETE CASCADE,
            FOREIGN KEY (unit_id) REFERENCES {$wpdb->prefix}vpm_units(id) ON DELETE CASCADE,
            FOREIGN KEY (tenant_id) REFERENCES {$wpdb->prefix}vpm_tenants(id) ON DELETE CASCADE,
            FOREIGN KEY (lease_id) REFERENCES {$wpdb->prefix}vpm_leases(id) ON DELETE CASCADE,
            FOREIGN KEY (maintenance_request_id) REFERENCES {$wpdb->prefix}vpm_maintenance_requests(id) ON DELETE CASCADE
        ) $charset_collate;";
        
        dbDelta($sql);
        
        // Communications table
        $table_name = $wpdb->prefix . 'vpm_communications';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            property_id bigint(20) unsigned DEFAULT NULL,
            tenant_id bigint(20) unsigned DEFAULT NULL,
            lease_id bigint(20) unsigned DEFAULT NULL,
            maintenance_request_id bigint(20) unsigned DEFAULT NULL,
            communication_type varchar(50) DEFAULT 'email',
            direction varchar(20) DEFAULT 'outbound',
            from_email varchar(255) DEFAULT NULL,
            to_email varchar(255) DEFAULT NULL,
            cc_email text DEFAULT NULL,
            subject varchar(500) DEFAULT NULL,
            message text NOT NULL,
            status varchar(20) DEFAULT 'sent',
            sent_at datetime DEFAULT NULL,
            read_at datetime DEFAULT NULL,
            replied_at datetime DEFAULT NULL,
            template_used varchar(100) DEFAULT NULL,
            automated tinyint(1) DEFAULT 0,
            attachments text DEFAULT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY property_id (property_id),
            KEY tenant_id (tenant_id),
            KEY lease_id (lease_id),
            KEY maintenance_request_id (maintenance_request_id),
            KEY communication_type (communication_type),
            KEY status (status),
            KEY sent_at (sent_at),
            FOREIGN KEY (property_id) REFERENCES {$wpdb->prefix}vpm_properties(id) ON DELETE SET NULL,
            FOREIGN KEY (tenant_id) REFERENCES {$wpdb->prefix}vpm_tenants(id) ON DELETE SET NULL,
            FOREIGN KEY (lease_id) REFERENCES {$wpdb->prefix}vpm_leases(id) ON DELETE SET NULL,
            FOREIGN KEY (maintenance_request_id) REFERENCES {$wpdb->prefix}vpm_maintenance_requests(id) ON DELETE SET NULL
        ) $charset_collate;";
        
        dbDelta($sql);
        
        // Settings table
        $table_name = $wpdb->prefix . 'vpm_settings';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            setting_name varchar(255) NOT NULL,
            setting_value longtext,
            setting_type varchar(50) DEFAULT 'string',
            description text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY setting_name (setting_name)
        ) $charset_collate;";
        
        dbDelta($sql);
        
        // Activity Log table
        $table_name = $wpdb->prefix . 'vpm_activity_log';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned DEFAULT NULL,
            action varchar(100) NOT NULL,
            object_type varchar(50) DEFAULT NULL,
            object_id bigint(20) unsigned DEFAULT NULL,
            old_values text DEFAULT NULL,
            new_values text DEFAULT NULL,
            ip_address varchar(45) DEFAULT NULL,
            user_agent text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY action (action),
            KEY object_type (object_type),
            KEY object_id (object_id),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        dbDelta($sql);
    }
    
    /**
     * Register post types and taxonomies
     */
    private static function register_content_types() {
        // Ensure post types and taxonomies are registered for rewrite rules
        if (function_exists('vpm_register_post_types')) {
            vpm_register_post_types();
        }
        
        if (function_exists('vpm_register_taxonomies')) {
            vpm_register_taxonomies();
        }
    }
    
    /**
     * Set up user capabilities
     */
    private static function setup_capabilities() {
        $admin_role = get_role('administrator');
        
        if ($admin_role) {
            // Property management capabilities
            $capabilities = [
                'manage_properties',
                'edit_properties',
                'delete_properties',
                'manage_tenants',
                'edit_tenants',
                'delete_tenants',
                'manage_leases',
                'edit_leases',
                'delete_leases',
                'manage_maintenance',
                'edit_maintenance',
                'delete_maintenance',
                'view_reports',
                'manage_payments',
                'export_data',
                'import_data',
                'manage_vpm_settings'
            ];
            
            foreach ($capabilities as $cap) {
                $admin_role->add_cap($cap);
            }
        }
        
        // Create property manager role
        if (!get_role('property_manager')) {
            add_role('property_manager', 'Property Manager', [
                'read' => true,
                'manage_properties' => true,
                'edit_properties' => true,
                'manage_tenants' => true,
                'edit_tenants' => true,
                'manage_leases' => true,
                'edit_leases' => true,
                'manage_maintenance' => true,
                'edit_maintenance' => true,
                'view_reports' => true,
                'manage_payments' => true
            ]);
        }
        
        // Create tenant role
        if (!get_role('property_tenant')) {
            add_role('property_tenant', 'Property Tenant', [
                'read' => true,
                'submit_maintenance_requests' => true,
                'view_lease_info' => true,
                'view_payment_history' => true
            ]);
        }
    }
    
    /**
     * Create default pages
     */
    private static function create_default_pages() {
        // Property Portal page
        $portal_page = get_page_by_title('Property Portal');
        if (!$portal_page) {
            $page_data = [
                'post_title' => 'Property Portal',
                'post_content' => '[vpm_property_portal]',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_slug' => 'property-portal'
            ];
            
            $page_id = wp_insert_post($page_data);
            if ($page_id) {
                update_option('vpm_portal_page_id', $page_id);
            }
        }
        
        // Tenant Dashboard page
        $dashboard_page = get_page_by_title('Tenant Dashboard');
        if (!$dashboard_page) {
            $page_data = [
                'post_title' => 'Tenant Dashboard',
                'post_content' => '[vpm_tenant_dashboard]',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_slug' => 'tenant-dashboard'
            ];
            
            $page_id = wp_insert_post($page_data);
            if ($page_id) {
                update_option('vpm_dashboard_page_id', $page_id);
            }
        }
        
        // Property Search page
        $search_page = get_page_by_title('Property Search');
        if (!$search_page) {
            $page_data = [
                'post_title' => 'Property Search',
                'post_content' => '[vpm_property_search]',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_slug' => 'property-search'
            ];
            
            $page_id = wp_insert_post($page_data);
            if ($page_id) {
                update_option('vpm_search_page_id', $page_id);
            }
        }
    }
    
    /**
     * Set default options
     */
    private static function set_default_options() {
        $default_options = [
            'vpm_currency' => 'USD',
            'vpm_currency_symbol' => '$',
            'vpm_date_format' => 'Y-m-d',
            'vpm_timezone' => get_option('timezone_string', 'America/New_York'),
            'vpm_rent_due_day' => 1,
            'vpm_late_fee_grace_days' => 5,
            'vpm_late_fee_amount' => 50.00,
            'vpm_security_deposit_return_days' => 30,
            'vpm_notice_to_vacate_days' => 30,
            'vpm_email_notifications' => 1,
            'vpm_auto_email_reminders' => 1,
            'vpm_maintenance_auto_assign' => 0,
            'vpm_tenant_portal_enabled' => 1,
            'vpm_online_payments_enabled' => 0,
            'vpm_background_checks_required' => 1,
            'vpm_credit_check_required' => 1,
            'vpm_minimum_credit_score' => 600,
            'vpm_maximum_occupants_per_bedroom' => 2,
            'vpm_pet_policy' => 'case_by_case',
            'vpm_smoking_policy' => 'no_smoking',
            'vpm_subletting_policy' => 'not_allowed'
        ];
        
        foreach ($default_options as $option_name => $option_value) {
            if (get_option($option_name) === false) {
                add_option($option_name, $option_value);
            }
        }
        
        // Insert default settings into vpm_settings table
        global $wpdb;
        $settings_table = $wpdb->prefix . 'vpm_settings';
        
        $default_settings = [
            [
                'setting_name' => 'company_name',
                'setting_value' => get_bloginfo('name'),
                'setting_type' => 'string',
                'description' => 'Property management company name'
            ],
            [
                'setting_name' => 'company_address',
                'setting_value' => '',
                'setting_type' => 'text',
                'description' => 'Company address for documents and communications'
            ],
            [
                'setting_name' => 'company_phone',
                'setting_value' => '',
                'setting_type' => 'string',
                'description' => 'Primary company phone number'
            ],
            [
                'setting_name' => 'company_email',
                'setting_value' => get_option('admin_email'),
                'setting_type' => 'email',
                'description' => 'Primary company email address'
            ],
            [
                'setting_name' => 'default_lease_terms',
                'setting_value' => 12,
                'setting_type' => 'number',
                'description' => 'Default lease term in months'
            ],
            [
                'setting_name' => 'auto_late_fees',
                'setting_value' => 1,
                'setting_type' => 'boolean',
                'description' => 'Automatically apply late fees'
            ]
        ];
        
        foreach ($default_settings as $setting) {
            $existing = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $settings_table WHERE setting_name = %s",
                $setting['setting_name']
            ));
            
            if (!$existing) {
                $wpdb->insert($settings_table, $setting);
            }
        }
    }
}