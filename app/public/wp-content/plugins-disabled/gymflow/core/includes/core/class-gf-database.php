<?php
/**
 * GymFlow Database Class
 *
 * Handles database operations and schema management for the fitness studio management system
 *
 * @package GymFlow
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * GF_Database Class
 *
 * Manages database operations, migrations, and schema updates
 */
class GF_Database {

    /**
     * Current database version
     */
    const DB_VERSION = '1.0.0';

    /**
     * Initialize database management
     */
    public function init() {
        add_action('init', array($this, 'check_database_version'));
    }

    /**
     * Check if database needs updating
     */
    public function check_database_version() {
        $current_version = get_option('gymflow_db_version', '0');
        
        if (version_compare($current_version, self::DB_VERSION, '<')) {
            $this->update_database();
        }
    }

    /**
     * Update database to current version
     */
    public static function update_database() {
        $current_version = get_option('gymflow_db_version', '0');
        
        // Run migrations based on current version
        if (version_compare($current_version, '1.0.0', '<')) {
            self::migrate_to_1_0_0();
        }
        
        // Update version number
        update_option('gymflow_db_version', self::DB_VERSION);
        
        // Clear any cached data
        self::clear_cache();
        
        GF_Utilities::log('Database updated to version ' . self::DB_VERSION);
    }

    /**
     * Migration to version 1.0.0
     */
    private static function migrate_to_1_0_0() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Create all tables
        self::create_members_table($charset_collate);
        self::create_classes_table($charset_collate);
        self::create_class_schedules_table($charset_collate);
        self::create_trainers_table($charset_collate);
        self::create_equipment_table($charset_collate);
        self::create_bookings_table($charset_collate);
        self::create_check_ins_table($charset_collate);
        self::create_memberships_table($charset_collate);
        self::create_payments_table($charset_collate);
        
        // Create indexes for better performance
        self::create_database_indexes();
        
        // Insert default data
        self::insert_default_data();
    }

    /**
     * Create members table
     */
    private static function create_members_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'gf_members';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NULL,
            member_number varchar(50) NOT NULL UNIQUE,
            first_name varchar(100) NOT NULL,
            last_name varchar(100) NOT NULL,
            email varchar(255) NOT NULL,
            phone varchar(20) DEFAULT '',
            date_of_birth date NULL,
            gender enum('male','female','other','prefer_not_to_say') DEFAULT 'prefer_not_to_say',
            emergency_contact_name varchar(100) DEFAULT '',
            emergency_contact_phone varchar(20) DEFAULT '',
            health_conditions text DEFAULT '',
            membership_type varchar(50) DEFAULT 'basic',
            membership_status enum('active','expired','pending','cancelled','on_hold') DEFAULT 'pending',
            membership_start_date date NULL,
            membership_end_date date NULL,
            notes text DEFAULT '',
            profile_photo_url varchar(255) DEFAULT '',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY member_number (member_number),
            KEY user_id (user_id),
            KEY email (email),
            KEY membership_status (membership_status),
            KEY membership_start_date (membership_start_date),
            KEY membership_end_date (membership_end_date),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Create classes table
     */
    private static function create_classes_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'gf_classes';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            description text DEFAULT '',
            category varchar(100) DEFAULT 'fitness',
            duration int unsigned DEFAULT 60,
            capacity int unsigned DEFAULT 20,
            difficulty_level enum('beginner','intermediate','advanced','all_levels') DEFAULT 'all_levels',
            equipment_required text DEFAULT '',
            instructor_id bigint(20) unsigned NULL,
            price decimal(10,2) DEFAULT 0.00,
            drop_in_price decimal(10,2) DEFAULT 0.00,
            is_active tinyint(1) DEFAULT 1,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY instructor_id (instructor_id),
            KEY category (category),
            KEY is_active (is_active),
            KEY difficulty_level (difficulty_level),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Create class schedules table
     */
    private static function create_class_schedules_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'gf_class_schedules';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            class_id bigint(20) unsigned NOT NULL,
            instructor_id bigint(20) unsigned NULL,
            date date NOT NULL,
            start_time time NOT NULL,
            end_time time NOT NULL,
            room varchar(100) DEFAULT '',
            max_capacity int unsigned DEFAULT 20,
            current_bookings int unsigned DEFAULT 0,
            status enum('scheduled','cancelled','completed') DEFAULT 'scheduled',
            notes text DEFAULT '',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY class_id (class_id),
            KEY instructor_id (instructor_id),
            KEY date (date),
            KEY start_time (start_time),
            KEY status (status),
            KEY date_time (date, start_time)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Create trainers table
     */
    private static function create_trainers_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'gf_trainers';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NULL,
            trainer_number varchar(50) NOT NULL UNIQUE,
            first_name varchar(100) NOT NULL,
            last_name varchar(100) NOT NULL,
            email varchar(255) NOT NULL,
            phone varchar(20) DEFAULT '',
            bio text DEFAULT '',
            specialties text DEFAULT '',
            certifications text DEFAULT '',
            hire_date date NULL,
            hourly_rate decimal(8,2) DEFAULT 0.00,
            commission_rate decimal(5,2) DEFAULT 0.00,
            profile_photo_url varchar(255) DEFAULT '',
            is_active tinyint(1) DEFAULT 1,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY trainer_number (trainer_number),
            KEY user_id (user_id),
            KEY email (email),
            KEY is_active (is_active),
            KEY hire_date (hire_date)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Create equipment table
     */
    private static function create_equipment_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'gf_equipment';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            description text DEFAULT '',
            category varchar(100) DEFAULT 'general',
            serial_number varchar(100) DEFAULT '',
            purchase_date date NULL,
            purchase_price decimal(10,2) DEFAULT 0.00,
            current_value decimal(10,2) DEFAULT 0.00,
            condition_rating enum('excellent','good','fair','poor','out_of_order') DEFAULT 'good',
            location varchar(100) DEFAULT '',
            status enum('available','booked','maintenance','out_of_order') DEFAULT 'available',
            maintenance_schedule varchar(100) DEFAULT '',
            last_maintenance_date date NULL,
            next_maintenance_date date NULL,
            usage_count int unsigned DEFAULT 0,
            notes text DEFAULT '',
            photo_url varchar(255) DEFAULT '',
            is_bookable tinyint(1) DEFAULT 1,
            booking_duration int unsigned DEFAULT 60,
            advance_booking_days int unsigned DEFAULT 7,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY category (category),
            KEY status (status),
            KEY location (location),
            KEY is_bookable (is_bookable),
            KEY condition_rating (condition_rating),
            KEY next_maintenance_date (next_maintenance_date)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Create bookings table
     */
    private static function create_bookings_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'gf_bookings';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            booking_type enum('class','equipment','personal_training') NOT NULL,
            booking_reference varchar(50) NOT NULL UNIQUE,
            member_id bigint(20) unsigned NOT NULL,
            class_schedule_id bigint(20) unsigned NULL,
            equipment_id bigint(20) unsigned NULL,
            trainer_id bigint(20) unsigned NULL,
            booking_date date NOT NULL,
            start_time time NOT NULL,
            end_time time NOT NULL,
            status enum('confirmed','pending','cancelled','completed','no_show') DEFAULT 'confirmed',
            payment_status enum('pending','paid','refunded','partial') DEFAULT 'pending',
            amount decimal(10,2) DEFAULT 0.00,
            payment_method varchar(50) DEFAULT '',
            notes text DEFAULT '',
            booking_source enum('admin','member_portal','walk_in','phone') DEFAULT 'admin',
            confirmed_at datetime NULL,
            cancelled_at datetime NULL,
            cancellation_reason text DEFAULT '',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY booking_reference (booking_reference),
            KEY member_id (member_id),
            KEY class_schedule_id (class_schedule_id),
            KEY equipment_id (equipment_id),
            KEY trainer_id (trainer_id),
            KEY booking_date (booking_date),
            KEY status (status),
            KEY payment_status (payment_status),
            KEY booking_type (booking_type),
            KEY date_time (booking_date, start_time)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Create check-ins table
     */
    private static function create_check_ins_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'gf_check_ins';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            member_id bigint(20) unsigned NOT NULL,
            check_in_time datetime NOT NULL,
            check_out_time datetime NULL,
            check_in_method enum('manual','qr_code','mobile_app','front_desk') DEFAULT 'front_desk',
            location varchar(100) DEFAULT '',
            staff_id bigint(20) unsigned NULL,
            notes text DEFAULT '',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY member_id (member_id),
            KEY check_in_time (check_in_time),
            KEY check_in_method (check_in_method),
            KEY staff_id (staff_id),
            KEY check_in_date (check_in_time)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Create memberships table
     */
    private static function create_memberships_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'gf_memberships';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            description text DEFAULT '',
            duration_months int unsigned DEFAULT 1,
            price decimal(10,2) NOT NULL,
            setup_fee decimal(10,2) DEFAULT 0.00,
            benefits text DEFAULT '',
            class_credits int unsigned DEFAULT 0,
            personal_training_credits int unsigned DEFAULT 0,
            equipment_booking_included tinyint(1) DEFAULT 1,
            guest_passes_included int unsigned DEFAULT 0,
            is_active tinyint(1) DEFAULT 1,
            sort_order int unsigned DEFAULT 0,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY is_active (is_active),
            KEY sort_order (sort_order),
            KEY price (price)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Create payments table
     */
    private static function create_payments_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'gf_payments';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            payment_reference varchar(50) NOT NULL UNIQUE,
            member_id bigint(20) unsigned NOT NULL,
            booking_id bigint(20) unsigned NULL,
            membership_id bigint(20) unsigned NULL,
            amount decimal(10,2) NOT NULL,
            currency varchar(3) DEFAULT 'USD',
            payment_method varchar(50) NOT NULL,
            payment_gateway varchar(50) DEFAULT '',
            gateway_transaction_id varchar(255) DEFAULT '',
            status enum('pending','processing','completed','failed','cancelled','refunded') DEFAULT 'pending',
            payment_date datetime NOT NULL,
            due_date date NULL,
            description text DEFAULT '',
            metadata text DEFAULT '',
            refund_amount decimal(10,2) DEFAULT 0.00,
            refund_date datetime NULL,
            refund_reason text DEFAULT '',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY payment_reference (payment_reference),
            KEY member_id (member_id),
            KEY booking_id (booking_id),
            KEY membership_id (membership_id),
            KEY status (status),
            KEY payment_date (payment_date),
            KEY due_date (due_date),
            KEY gateway_transaction_id (gateway_transaction_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Create additional database indexes for performance
     */
    private static function create_database_indexes() {
        global $wpdb;
        
        // Additional composite indexes for common queries
        $indexes = array(
            // Members table
            $wpdb->prefix . 'gf_members' => array(
                'membership_status_dates' => '(membership_status, membership_start_date, membership_end_date)',
                'name_search' => '(first_name, last_name)',
            ),
            
            // Bookings table
            $wpdb->prefix . 'gf_bookings' => array(
                'member_date_status' => '(member_id, booking_date, status)',
                'equipment_date_time' => '(equipment_id, booking_date, start_time, end_time)',
                'trainer_date_time' => '(trainer_id, booking_date, start_time, end_time)',
            ),
            
            // Check-ins table
            $wpdb->prefix . 'gf_check_ins' => array(
                'member_date' => '(member_id, check_in_time)',
            ),
            
            // Payments table
            $wpdb->prefix . 'gf_payments' => array(
                'member_payment_date' => '(member_id, payment_date)',
                'status_date' => '(status, payment_date)',
            )
        );
        
        foreach ($indexes as $table => $table_indexes) {
            foreach ($table_indexes as $index_name => $columns) {
                $sql = "CREATE INDEX {$index_name} ON {$table} {$columns}";
                $wpdb->query($sql);
            }
        }
    }

    /**
     * Insert default data
     */
    private static function insert_default_data() {
        // Insert default membership types
        self::insert_default_memberships();
        
        // Create default taxonomy terms
        if (class_exists('GF_Taxonomies')) {
            GF_Taxonomies::create_default_terms();
        }
    }

    /**
     * Insert default membership types
     */
    private static function insert_default_memberships() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'gf_memberships';
        
        // Check if memberships already exist
        $count = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}");
        if ($count > 0) {
            return; // Don't insert if memberships already exist
        }
        
        $default_memberships = array(
            array(
                'name' => __('Basic Monthly', 'gymflow'),
                'description' => __('Basic gym access with standard amenities', 'gymflow'),
                'duration_months' => 1,
                'price' => 49.99,
                'setup_fee' => 0.00,
                'benefits' => __('Gym access, Basic equipment use, Locker room access', 'gymflow'),
                'class_credits' => 4,
                'personal_training_credits' => 0,
                'equipment_booking_included' => 1,
                'guest_passes_included' => 1,
                'sort_order' => 1
            ),
            array(
                'name' => __('Premium Monthly', 'gymflow'),
                'description' => __('Full access with unlimited classes and premium features', 'gymflow'),
                'duration_months' => 1,
                'price' => 79.99,
                'setup_fee' => 0.00,
                'benefits' => __('Full gym access, Unlimited classes, Premium equipment, Guest privileges', 'gymflow'),
                'class_credits' => 999,
                'personal_training_credits' => 1,
                'equipment_booking_included' => 1,
                'guest_passes_included' => 3,
                'sort_order' => 2
            ),
            array(
                'name' => __('Annual Basic', 'gymflow'),
                'description' => __('12-month commitment with 2 months free', 'gymflow'),
                'duration_months' => 12,
                'price' => 499.99,
                'setup_fee' => 0.00,
                'benefits' => __('Gym access, Basic equipment use, Locker room access, 2 months free', 'gymflow'),
                'class_credits' => 48,
                'personal_training_credits' => 0,
                'equipment_booking_included' => 1,
                'guest_passes_included' => 12,
                'sort_order' => 3
            )
        );
        
        foreach ($default_memberships as $membership) {
            $wpdb->insert($table_name, $membership);
        }
    }

    /**
     * Get table statistics
     */
    public static function get_table_stats() {
        global $wpdb;
        
        $tables = array(
            'members' => $wpdb->prefix . 'gf_members',
            'classes' => $wpdb->prefix . 'gf_classes',
            'class_schedules' => $wpdb->prefix . 'gf_class_schedules',
            'trainers' => $wpdb->prefix . 'gf_trainers',
            'equipment' => $wpdb->prefix . 'gf_equipment',
            'bookings' => $wpdb->prefix . 'gf_bookings',
            'check_ins' => $wpdb->prefix . 'gf_check_ins',
            'memberships' => $wpdb->prefix . 'gf_memberships',
            'payments' => $wpdb->prefix . 'gf_payments'
        );
        
        $stats = array();
        
        foreach ($tables as $key => $table) {
            $count = $wpdb->get_var("SELECT COUNT(*) FROM {$table}");
            $stats[$key] = intval($count);
        }
        
        return $stats;
    }

    /**
     * Optimize database tables
     */
    public static function optimize_tables() {
        global $wpdb;
        
        $tables = array(
            $wpdb->prefix . 'gf_members',
            $wpdb->prefix . 'gf_classes',
            $wpdb->prefix . 'gf_class_schedules',
            $wpdb->prefix . 'gf_trainers',
            $wpdb->prefix . 'gf_equipment',
            $wpdb->prefix . 'gf_bookings',
            $wpdb->prefix . 'gf_check_ins',
            $wpdb->prefix . 'gf_memberships',
            $wpdb->prefix . 'gf_payments'
        );
        
        foreach ($tables as $table) {
            $wpdb->query("OPTIMIZE TABLE {$table}");
        }
        
        GF_Utilities::log('Database tables optimized');
    }

    /**
     * Clear cached data
     */
    private static function clear_cache() {
        // Clear WordPress object cache
        wp_cache_flush();
        
        // Clear specific GymFlow transients
        $transients = array(
            'gymflow_member_count',
            'gymflow_class_count',
            'gymflow_trainer_count',
            'gymflow_equipment_count',
            'gymflow_booking_count',
            'gymflow_revenue_stats',
            'gymflow_attendance_stats'
        );
        
        foreach ($transients as $transient) {
            delete_transient($transient);
        }
    }

    /**
     * Check database integrity
     */
    public static function check_integrity() {
        global $wpdb;
        
        $issues = array();
        
        // Check for orphaned records
        $orphaned_bookings = $wpdb->get_var("
            SELECT COUNT(*) FROM {$wpdb->prefix}gf_bookings b 
            LEFT JOIN {$wpdb->prefix}gf_members m ON b.member_id = m.id 
            WHERE m.id IS NULL
        ");
        
        if ($orphaned_bookings > 0) {
            $issues[] = sprintf(__('%d orphaned bookings found', 'gymflow'), $orphaned_bookings);
        }
        
        // Check for invalid foreign key references
        $invalid_class_schedules = $wpdb->get_var("
            SELECT COUNT(*) FROM {$wpdb->prefix}gf_class_schedules cs 
            LEFT JOIN {$wpdb->prefix}gf_classes c ON cs.class_id = c.id 
            WHERE c.id IS NULL
        ");
        
        if ($invalid_class_schedules > 0) {
            $issues[] = sprintf(__('%d invalid class schedule references found', 'gymflow'), $invalid_class_schedules);
        }
        
        return $issues;
    }

    /**
     * Backup database tables
     */
    public static function backup_tables() {
        // This would create a backup of all GymFlow tables
        // Implementation would depend on specific backup requirements
        
        $backup_data = array();
        $tables = array(
            'members', 'classes', 'class_schedules', 'trainers', 
            'equipment', 'bookings', 'check_ins', 'memberships', 'payments'
        );
        
        global $wpdb;
        
        foreach ($tables as $table) {
            $table_name = $wpdb->prefix . 'gf_' . $table;
            $data = $wpdb->get_results("SELECT * FROM {$table_name}", ARRAY_A);
            $backup_data[$table] = $data;
        }
        
        // Save backup to file
        $upload_dir = GF_Utilities::get_upload_dir();
        $backup_file = $upload_dir['path'] . '/backup_' . date('Y-m-d_H-i-s') . '.json';
        
        file_put_contents($backup_file, json_encode($backup_data, JSON_PRETTY_PRINT));
        
        return $backup_file;
    }

    /**
     * Get database size
     */
    public static function get_database_size() {
        global $wpdb;
        
        $tables = array(
            $wpdb->prefix . 'gf_members',
            $wpdb->prefix . 'gf_classes',
            $wpdb->prefix . 'gf_class_schedules',
            $wpdb->prefix . 'gf_trainers',
            $wpdb->prefix . 'gf_equipment',
            $wpdb->prefix . 'gf_bookings',
            $wpdb->prefix . 'gf_check_ins',
            $wpdb->prefix . 'gf_memberships',
            $wpdb->prefix . 'gf_payments'
        );
        
        $total_size = 0;
        
        foreach ($tables as $table) {
            $size = $wpdb->get_var($wpdb->prepare("
                SELECT ROUND(((data_length + index_length) / 1024 / 1024), 2) 
                FROM information_schema.TABLES 
                WHERE table_schema = %s 
                AND table_name = %s
            ", DB_NAME, $table));
            
            $total_size += floatval($size);
        }
        
        return $total_size; // Size in MB
    }
}