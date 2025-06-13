<?php
/**
 * DealerEdge Plugin Activator
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class DE_Activator {
    
    public static function activate() {
        // Create database tables
        self::create_tables();
        
        // Set default options
        self::set_default_options();
        
        // Create default taxonomies
        self::create_default_taxonomies();
        
        // Set transient to show activation notice
        set_transient('dealeredge_activation_notice', true, 30);
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    private static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Vehicle History Table
        $table_name = $wpdb->prefix . 'de_vehicle_history';
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            vehicle_id bigint(20) NOT NULL,
            customer_id bigint(20) NOT NULL,
            work_order_id bigint(20) DEFAULT NULL,
            service_type varchar(100) NOT NULL,
            description text,
            cost decimal(10,2) DEFAULT 0.00,
            date_performed datetime DEFAULT CURRENT_TIMESTAMP,
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY vehicle_id (vehicle_id),
            KEY customer_id (customer_id),
            KEY work_order_id (work_order_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Parts Usage Tracking Table
        $table_name = $wpdb->prefix . 'de_parts_usage';
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            part_id bigint(20) NOT NULL,
            work_order_id bigint(20) DEFAULT NULL,
            sale_id bigint(20) DEFAULT NULL,
            quantity int(11) NOT NULL DEFAULT 1,
            unit_cost decimal(10,2) DEFAULT 0.00,
            total_cost decimal(10,2) DEFAULT 0.00,
            usage_type enum('service','sale','adjustment') DEFAULT 'service',
            date_used datetime DEFAULT CURRENT_TIMESTAMP,
            notes text,
            PRIMARY KEY (id),
            KEY part_id (part_id),
            KEY work_order_id (work_order_id),
            KEY sale_id (sale_id)
        ) $charset_collate;";
        
        dbDelta($sql);
        
        // Customer Vehicle Relationships Table
        $table_name = $wpdb->prefix . 'de_customer_vehicles';
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            customer_id bigint(20) NOT NULL,
            vehicle_id bigint(20) NOT NULL,
            relationship_type enum('owner','authorized_user','emergency_contact') DEFAULT 'owner',
            is_primary tinyint(1) DEFAULT 0,
            date_added datetime DEFAULT CURRENT_TIMESTAMP,
            notes text,
            PRIMARY KEY (id),
            UNIQUE KEY customer_vehicle (customer_id, vehicle_id),
            KEY customer_id (customer_id),
            KEY vehicle_id (vehicle_id)
        ) $charset_collate;";
        
        dbDelta($sql);
        
        // Service Templates Table (for common services)
        $table_name = $wpdb->prefix . 'de_service_templates';
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            description text,
            category varchar(100),
            estimated_time int(11) DEFAULT 60,
            labor_cost decimal(10,2) DEFAULT 0.00,
            parts_list text,
            instructions text,
            is_active tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY category (category),
            KEY is_active (is_active)
        ) $charset_collate;";
        
        dbDelta($sql);
    }
    
    private static function set_default_options() {
        // General Settings
        add_option('dealeredge_business_name', get_bloginfo('name'));
        add_option('dealeredge_business_type', 'auto_shop'); // auto_shop, dealership, both
        add_option('dealeredge_currency', 'USD');
        add_option('dealeredge_tax_rate', '8.25');
        add_option('dealeredge_labor_rate', '120.00');
        
        // Work Order Settings
        add_option('dealeredge_work_order_prefix', 'WO-');
        add_option('dealeredge_work_order_start_number', '1000');
        add_option('dealeredge_auto_assign_wo_numbers', '1');
        
        // Sales Settings
        add_option('dealeredge_sale_prefix', 'SALE-');
        add_option('dealeredge_sale_start_number', '1000');
        add_option('dealeredge_auto_assign_sale_numbers', '1');
        
        // Inventory Settings
        add_option('dealeredge_low_stock_threshold', '5');
        add_option('dealeredge_track_parts_inventory', '1');
        add_option('dealeredge_auto_deduct_parts', '1');
        
        // Notification Settings
        add_option('dealeredge_email_notifications', '1');
        add_option('dealeredge_admin_email', get_option('admin_email'));
        
        // Version
        add_option('dealeredge_version', DE_VERSION);
        add_option('dealeredge_db_version', '1.0.0');
    }
    
    private static function create_default_taxonomies() {
        // Default Vehicle Makes
        $vehicle_makes = array(
            'Ford', 'Chevrolet', 'Toyota', 'Honda', 'Nissan', 'BMW', 'Mercedes-Benz',
            'Audi', 'Volkswagen', 'Hyundai', 'Kia', 'Mazda', 'Subaru', 'Jeep',
            'Ram', 'GMC', 'Cadillac', 'Lexus', 'Infiniti', 'Acura', 'Volvo',
            'Jaguar', 'Land Rover', 'Porsche', 'Tesla', 'Buick', 'Chrysler',
            'Dodge', 'Lincoln', 'Mitsubishi', 'Genesis', 'Alfa Romeo'
        );
        
        foreach ($vehicle_makes as $make) {
            if (!term_exists($make, 'de_vehicle_make')) {
                wp_insert_term($make, 'de_vehicle_make');
            }
        }
        
        // Default Service Categories
        $service_categories = array(
            array('name' => 'Oil Change & Lubrication', 'slug' => 'oil-change'),
            array('name' => 'Brake Service', 'slug' => 'brake-service'),
            array('name' => 'Tire Service', 'slug' => 'tire-service'),
            array('name' => 'Engine Repair', 'slug' => 'engine-repair'),
            array('name' => 'Transmission Service', 'slug' => 'transmission'),
            array('name' => 'Electrical System', 'slug' => 'electrical'),
            array('name' => 'Air Conditioning', 'slug' => 'ac-service'),
            array('name' => 'Suspension & Steering', 'slug' => 'suspension'),
            array('name' => 'Exhaust System', 'slug' => 'exhaust'),
            array('name' => 'Cooling System', 'slug' => 'cooling'),
            array('name' => 'Fuel System', 'slug' => 'fuel-system'),
            array('name' => 'Diagnostic Service', 'slug' => 'diagnostic'),
            array('name' => 'General Maintenance', 'slug' => 'maintenance'),
            array('name' => 'Inspection', 'slug' => 'inspection'),
            array('name' => 'Body Work', 'slug' => 'body-work')
        );
        
        foreach ($service_categories as $category) {
            if (!term_exists($category['name'], 'de_service_category')) {
                wp_insert_term($category['name'], 'de_service_category', array(
                    'slug' => $category['slug']
                ));
            }
        }
        
        // Default Part Categories
        $part_categories = array(
            array('name' => 'Fluids & Filters', 'slug' => 'fluids-filters'),
            array('name' => 'Brake Parts', 'slug' => 'brake-parts'),
            array('name' => 'Engine Parts', 'slug' => 'engine-parts'),
            array('name' => 'Transmission Parts', 'slug' => 'transmission-parts'),
            array('name' => 'Electrical Parts', 'slug' => 'electrical-parts'),
            array('name' => 'Belts & Hoses', 'slug' => 'belts-hoses'),
            array('name' => 'Tires & Wheels', 'slug' => 'tires-wheels'),
            array('name' => 'Suspension Parts', 'slug' => 'suspension-parts'),
            array('name' => 'Exhaust Parts', 'slug' => 'exhaust-parts'),
            array('name' => 'Body Parts', 'slug' => 'body-parts'),
            array('name' => 'Interior Parts', 'slug' => 'interior-parts'),
            array('name' => 'Tools & Equipment', 'slug' => 'tools-equipment'),
            array('name' => 'Maintenance Items', 'slug' => 'maintenance-items')
        );
        
        foreach ($part_categories as $category) {
            if (!term_exists($category['name'], 'de_part_category')) {
                wp_insert_term($category['name'], 'de_part_category', array(
                    'slug' => $category['slug']
                ));
            }
        }
    }
}