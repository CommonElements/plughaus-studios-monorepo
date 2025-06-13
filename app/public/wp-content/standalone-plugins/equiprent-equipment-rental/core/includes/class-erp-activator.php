<?php
/**
 * Fired during plugin activation
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 */
class ERP_Activator {

    /**
     * Short Description.
     *
     * Long Description.
     */
    public static function activate() {
        self::create_tables();
        self::create_capabilities();
        self::schedule_events();
        self::flush_rewrite_rules();
    }

    /**
     * Create plugin database tables
     */
    private static function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Equipment table
        $table_equipment = $wpdb->prefix . 'erp_equipment';
        $sql_equipment = "CREATE TABLE $table_equipment (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            description text,
            category varchar(100),
            brand varchar(100),
            model varchar(100),
            serial_number varchar(100),
            qr_code varchar(100),
            status varchar(50) DEFAULT 'available',
            condition_status varchar(50) DEFAULT 'excellent',
            daily_rate decimal(10,2),
            weekly_rate decimal(10,2),
            monthly_rate decimal(10,2),
            deposit_amount decimal(10,2),
            location varchar(255),
            weight decimal(8,2),
            dimensions varchar(100),
            power_requirements varchar(100),
            accessories text,
            maintenance_schedule varchar(50),
            last_maintenance_date date,
            next_maintenance_date date,
            purchase_date date,
            purchase_price decimal(10,2),
            depreciation_rate decimal(5,2),
            insurance_value decimal(10,2),
            image_gallery text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY serial_number (serial_number),
            UNIQUE KEY qr_code (qr_code),
            KEY status (status),
            KEY category (category),
            KEY location (location)
        ) $charset_collate;";

        // Customers table
        $table_customers = $wpdb->prefix . 'erp_customers';
        $sql_customers = "CREATE TABLE $table_customers (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) UNSIGNED,
            company_name varchar(255),
            contact_first_name varchar(100) NOT NULL,
            contact_last_name varchar(100) NOT NULL,
            email varchar(100) NOT NULL,
            phone varchar(20),
            mobile varchar(20),
            address_line_1 varchar(255),
            address_line_2 varchar(255),
            city varchar(100),
            state varchar(100),
            postal_code varchar(20),
            country varchar(100),
            delivery_instructions text,
            customer_type varchar(50) DEFAULT 'individual',
            credit_limit decimal(10,2) DEFAULT 0.00,
            current_balance decimal(10,2) DEFAULT 0.00,
            payment_terms varchar(50) DEFAULT 'immediate',
            tax_exempt tinyint(1) DEFAULT 0,
            tax_id varchar(50),
            license_number varchar(100),
            insurance_certificate varchar(255),
            emergency_contact_name varchar(255),
            emergency_contact_phone varchar(20),
            notes text,
            status varchar(50) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY email (email),
            KEY user_id (user_id),
            KEY customer_type (customer_type),
            KEY status (status)
        ) $charset_collate;";

        // Bookings table
        $table_bookings = $wpdb->prefix . 'erp_bookings';
        $sql_bookings = "CREATE TABLE $table_bookings (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            booking_number varchar(50) NOT NULL,
            customer_id mediumint(9) NOT NULL,
            status varchar(50) DEFAULT 'pending',
            booking_type varchar(50) DEFAULT 'rental',
            start_date date NOT NULL,
            end_date date NOT NULL,
            start_time time,
            end_time time,
            pickup_method varchar(50) DEFAULT 'customer_pickup',
            delivery_address text,
            delivery_date date,
            delivery_time_slot varchar(50),
            delivery_instructions text,
            return_method varchar(50) DEFAULT 'customer_return',
            return_address text,
            return_date date,
            return_time_slot varchar(50),
            subtotal decimal(10,2) DEFAULT 0.00,
            tax_amount decimal(10,2) DEFAULT 0.00,
            discount_amount decimal(10,2) DEFAULT 0.00,
            deposit_amount decimal(10,2) DEFAULT 0.00,
            total_amount decimal(10,2) DEFAULT 0.00,
            paid_amount decimal(10,2) DEFAULT 0.00,
            payment_status varchar(50) DEFAULT 'pending',
            payment_method varchar(50),
            transaction_id varchar(100),
            special_instructions text,
            damage_waiver tinyint(1) DEFAULT 0,
            damage_waiver_fee decimal(10,2) DEFAULT 0.00,
            notes text,
            created_by bigint(20) UNSIGNED,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY booking_number (booking_number),
            KEY customer_id (customer_id),
            KEY status (status),
            KEY start_date (start_date),
            KEY end_date (end_date),
            KEY payment_status (payment_status)
        ) $charset_collate;";

        // Booking Items table (equipment in each booking)
        $table_booking_items = $wpdb->prefix . 'erp_booking_items';
        $sql_booking_items = "CREATE TABLE $table_booking_items (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            booking_id mediumint(9) NOT NULL,
            equipment_id mediumint(9) NOT NULL,
            quantity int(11) DEFAULT 1,
            daily_rate decimal(10,2),
            total_days int(11),
            line_total decimal(10,2),
            condition_out varchar(50) DEFAULT 'excellent',
            condition_in varchar(50),
            condition_notes text,
            damage_assessment text,
            damage_charges decimal(10,2) DEFAULT 0.00,
            PRIMARY KEY (id),
            KEY booking_id (booking_id),
            KEY equipment_id (equipment_id)
        ) $charset_collate;";

        // Maintenance Records table
        $table_maintenance = $wpdb->prefix . 'erp_maintenance';
        $sql_maintenance = "CREATE TABLE $table_maintenance (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            equipment_id mediumint(9) NOT NULL,
            maintenance_type varchar(50) NOT NULL,
            scheduled_date date,
            completed_date date,
            technician_name varchar(255),
            description text,
            parts_used text,
            labor_hours decimal(5,2),
            parts_cost decimal(10,2) DEFAULT 0.00,
            labor_cost decimal(10,2) DEFAULT 0.00,
            total_cost decimal(10,2) DEFAULT 0.00,
            next_maintenance_date date,
            status varchar(50) DEFAULT 'scheduled',
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY equipment_id (equipment_id),
            KEY maintenance_type (maintenance_type),
            KEY status (status),
            KEY scheduled_date (scheduled_date)
        ) $charset_collate;";

        // Delivery Routes table (Pro feature)
        $table_routes = $wpdb->prefix . 'erp_delivery_routes';
        $sql_routes = "CREATE TABLE $table_routes (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            route_name varchar(255) NOT NULL,
            route_date date NOT NULL,
            driver_name varchar(255),
            vehicle varchar(255),
            status varchar(50) DEFAULT 'planned',
            total_distance decimal(8,2),
            estimated_time int(11),
            start_time time,
            end_time time,
            fuel_cost decimal(8,2),
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY route_date (route_date),
            KEY status (status)
        ) $charset_collate;";

        // Route Stops table (Pro feature)
        $table_route_stops = $wpdb->prefix . 'erp_route_stops';
        $sql_route_stops = "CREATE TABLE $table_route_stops (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            route_id mediumint(9) NOT NULL,
            booking_id mediumint(9) NOT NULL,
            stop_order int(11) NOT NULL,
            stop_type varchar(50) NOT NULL,
            address text NOT NULL,
            latitude decimal(10, 8),
            longitude decimal(11, 8),
            estimated_arrival time,
            actual_arrival time,
            estimated_duration int(11),
            actual_duration int(11),
            status varchar(50) DEFAULT 'pending',
            notes text,
            PRIMARY KEY (id),
            KEY route_id (route_id),
            KEY booking_id (booking_id),
            KEY stop_order (stop_order)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        dbDelta($sql_equipment);
        dbDelta($sql_customers);
        dbDelta($sql_bookings);
        dbDelta($sql_booking_items);
        dbDelta($sql_maintenance);
        dbDelta($sql_routes);
        dbDelta($sql_route_stops);

        // Add foreign key constraints (if supported)
        $wpdb->query("ALTER TABLE $table_booking_items ADD CONSTRAINT fk_booking_items_booking FOREIGN KEY (booking_id) REFERENCES $table_bookings (id) ON DELETE CASCADE");
        $wpdb->query("ALTER TABLE $table_booking_items ADD CONSTRAINT fk_booking_items_equipment FOREIGN KEY (equipment_id) REFERENCES $table_equipment (id) ON DELETE CASCADE");
        $wpdb->query("ALTER TABLE $table_maintenance ADD CONSTRAINT fk_maintenance_equipment FOREIGN KEY (equipment_id) REFERENCES $table_equipment (id) ON DELETE CASCADE");
        $wpdb->query("ALTER TABLE $table_route_stops ADD CONSTRAINT fk_route_stops_route FOREIGN KEY (route_id) REFERENCES $table_routes (id) ON DELETE CASCADE");
        $wpdb->query("ALTER TABLE $table_route_stops ADD CONSTRAINT fk_route_stops_booking FOREIGN KEY (booking_id) REFERENCES $table_bookings (id) ON DELETE CASCADE");
    }

    /**
     * Create custom capabilities
     */
    private static function create_capabilities() {
        $capabilities = array(
            'manage_equipment',
            'edit_equipment',
            'delete_equipment',
            'view_equipment',
            'manage_bookings',
            'edit_bookings',
            'delete_bookings',
            'view_bookings',
            'manage_customers',
            'edit_customers',
            'delete_customers',
            'view_customers',
            'manage_maintenance',
            'view_reports',
            'manage_routes',
            'view_analytics'
        );

        $admin = get_role('administrator');
        if ($admin) {
            foreach ($capabilities as $cap) {
                $admin->add_cap($cap);
            }
        }

        // Create custom role for equipment managers
        add_role(
            'equipment_manager',
            __('Equipment Manager', 'equiprent-pro'),
            array(
                'read' => true,
                'manage_equipment' => true,
                'edit_equipment' => true,
                'view_equipment' => true,
                'manage_bookings' => true,
                'edit_bookings' => true,
                'view_bookings' => true,
                'manage_customers' => true,
                'edit_customers' => true,
                'view_customers' => true,
                'manage_maintenance' => true,
                'view_reports' => true,
            )
        );
    }

    /**
     * Schedule recurring events
     */
    private static function schedule_events() {
        // Schedule daily maintenance checks
        if (!wp_next_scheduled('erp_daily_maintenance_check')) {
            wp_schedule_event(time(), 'daily', 'erp_daily_maintenance_check');
        }

        // Schedule weekly equipment availability updates
        if (!wp_next_scheduled('erp_weekly_availability_update')) {
            wp_schedule_event(time(), 'weekly', 'erp_weekly_availability_update');
        }
    }

    /**
     * Flush rewrite rules
     */
    private static function flush_rewrite_rules() {
        // Make sure post types are registered before flushing
        ERP_Post_Types::register_post_types();
        flush_rewrite_rules();
    }
}