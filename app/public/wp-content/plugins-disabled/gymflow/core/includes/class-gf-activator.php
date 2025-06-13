<?php
/**
 * GymFlow Activator Class
 *
 * Handles plugin activation tasks including database setup and initial configuration
 *
 * @package GymFlow
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * GF_Activator Class
 *
 * Fired during plugin activation
 */
class GF_Activator {

    /**
     * Plugin activation handler
     *
     * Sets up database tables, default options, and user capabilities
     */
    public static function activate() {
        global $wpdb;

        // Check WordPress and PHP version requirements
        self::check_requirements();

        // Create database tables
        self::create_tables();

        // Set up user capabilities
        self::setup_capabilities();

        // Create default options
        self::set_default_options();

        // Create upload directories
        self::create_upload_directories();

        // Schedule cron jobs
        self::schedule_cron_jobs();

        // Set activation flag
        update_option('gymflow_activation_time', current_time('timestamp'));
        update_option('gymflow_version', GYMFLOW_VERSION);

        // Flush rewrite rules
        flush_rewrite_rules();

        // Log activation
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('GymFlow plugin activated successfully');
        }
    }

    /**
     * Check WordPress and PHP version requirements
     */
    private static function check_requirements() {
        // Check WordPress version
        if (version_compare(get_bloginfo('version'), '5.8', '<')) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die(__('GymFlow requires WordPress 5.8 or higher.', 'gymflow'));
        }

        // Check PHP version
        if (version_compare(PHP_VERSION, '7.4', '<')) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die(__('GymFlow requires PHP 7.4 or higher.', 'gymflow'));
        }
    }

    /**
     * Create database tables
     */
    private static function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Members table
        $members_table = $wpdb->prefix . 'gf_members';
        $members_sql = "CREATE TABLE $members_table (
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
            KEY membership_end_date (membership_end_date)
        ) $charset_collate;";

        // Classes table
        $classes_table = $wpdb->prefix . 'gf_classes';
        $classes_sql = "CREATE TABLE $classes_table (
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
            KEY is_active (is_active)
        ) $charset_collate;";

        // Class schedules table
        $schedules_table = $wpdb->prefix . 'gf_class_schedules';
        $schedules_sql = "CREATE TABLE $schedules_table (
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
            KEY status (status)
        ) $charset_collate;";

        // Trainers table
        $trainers_table = $wpdb->prefix . 'gf_trainers';
        $trainers_sql = "CREATE TABLE $trainers_table (
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
            KEY is_active (is_active)
        ) $charset_collate;";

        // Equipment table
        $equipment_table = $wpdb->prefix . 'gf_equipment';
        $equipment_sql = "CREATE TABLE $equipment_table (
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
            KEY is_bookable (is_bookable)
        ) $charset_collate;";

        // Bookings table
        $bookings_table = $wpdb->prefix . 'gf_bookings';
        $bookings_sql = "CREATE TABLE $bookings_table (
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
            KEY payment_status (payment_status)
        ) $charset_collate;";

        // Check-ins table
        $checkins_table = $wpdb->prefix . 'gf_check_ins';
        $checkins_sql = "CREATE TABLE $checkins_table (
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
            KEY check_in_method (check_in_method)
        ) $charset_collate;";

        // Memberships table
        $memberships_table = $wpdb->prefix . 'gf_memberships';
        $memberships_sql = "CREATE TABLE $memberships_table (
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
            KEY sort_order (sort_order)
        ) $charset_collate;";

        // Payments table
        $payments_table = $wpdb->prefix . 'gf_payments';
        $payments_sql = "CREATE TABLE $payments_table (
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
            KEY due_date (due_date)
        ) $charset_collate;";

        // Include WordPress upgrade functions
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        // Execute table creation
        dbDelta($members_sql);
        dbDelta($classes_sql);
        dbDelta($schedules_sql);
        dbDelta($trainers_sql);
        dbDelta($equipment_sql);
        dbDelta($bookings_sql);
        dbDelta($checkins_sql);
        dbDelta($memberships_sql);
        dbDelta($payments_sql);

        // Store database version
        update_option('gymflow_db_version', '1.0.0');
    }

    /**
     * Set up user capabilities
     */
    private static function setup_capabilities() {
        // Administrator gets all capabilities
        $admin_role = get_role('administrator');
        if ($admin_role) {
            $admin_role->add_cap('manage_gymflow');
            $admin_role->add_cap('edit_gymflow');
            $admin_role->add_cap('view_gymflow_reports');
            $admin_role->add_cap('manage_gymflow_members');
            $admin_role->add_cap('manage_gymflow_classes');
            $admin_role->add_cap('manage_gymflow_trainers');
            $admin_role->add_cap('manage_gymflow_equipment');
            $admin_role->add_cap('manage_gymflow_bookings');
            $admin_role->add_cap('process_gymflow_payments');
        }

        // Create GymFlow Manager role
        add_role('gymflow_manager', __('GymFlow Manager', 'gymflow'), array(
            'read' => true,
            'manage_gymflow' => true,
            'edit_gymflow' => true,
            'view_gymflow_reports' => true,
            'manage_gymflow_members' => true,
            'manage_gymflow_classes' => true,
            'manage_gymflow_trainers' => true,
            'manage_gymflow_equipment' => true,
            'manage_gymflow_bookings' => true,
            'process_gymflow_payments' => true
        ));

        // Create GymFlow Staff role
        add_role('gymflow_staff', __('GymFlow Staff', 'gymflow'), array(
            'read' => true,
            'edit_gymflow' => true,
            'manage_gymflow_members' => true,
            'manage_gymflow_bookings' => true
        ));

        // Create GymFlow Trainer role
        add_role('gymflow_trainer', __('GymFlow Trainer', 'gymflow'), array(
            'read' => true,
            'view_gymflow_classes' => true,
            'manage_gymflow_bookings' => true
        ));
    }

    /**
     * Set default options
     */
    private static function set_default_options() {
        $default_options = array(
            'general_settings' => array(
                'studio_name' => get_bloginfo('name'),
                'studio_address' => '',
                'studio_phone' => '',
                'studio_email' => get_option('admin_email'),
                'timezone' => get_option('timezone_string'),
                'currency' => 'USD',
                'date_format' => get_option('date_format'),
                'time_format' => get_option('time_format')
            ),
            'booking_settings' => array(
                'advance_booking_days' => 30,
                'cancellation_hours' => 24,
                'allow_waitlist' => true,
                'auto_confirm_bookings' => true,
                'send_confirmation_emails' => true,
                'send_reminder_emails' => true,
                'reminder_hours_before' => 24
            ),
            'membership_settings' => array(
                'default_membership_duration' => 12,
                'grace_period_days' => 7,
                'auto_renewal' => false,
                'prorate_memberships' => true,
                'require_payment_method' => false
            ),
            'class_settings' => array(
                'default_class_duration' => 60,
                'default_class_capacity' => 20,
                'allow_drop_ins' => true,
                'require_booking' => false,
                'show_instructor_info' => true
            ),
            'equipment_settings' => array(
                'default_booking_duration' => 60,
                'max_advance_booking_days' => 7,
                'allow_recurring_bookings' => false,
                'require_approval' => false
            ),
            'notification_settings' => array(
                'enable_email_notifications' => true,
                'enable_sms_notifications' => false,
                'admin_notification_email' => get_option('admin_email'),
                'booking_confirmation_template' => 'default',
                'booking_reminder_template' => 'default',
                'membership_expiry_template' => 'default'
            ),
            'payment_settings' => array(
                'currency' => 'USD',
                'payment_methods' => array('cash', 'card'),
                'require_payment_on_booking' => false,
                'late_payment_fee' => 0,
                'refund_policy' => 'flexible'
            )
        );

        foreach ($default_options as $option_group => $options) {
            update_option('gymflow_' . $option_group, $options);
        }

        // Create default membership types
        self::create_default_memberships();
    }

    /**
     * Create default membership types
     */
    private static function create_default_memberships() {
        global $wpdb;

        $memberships_table = $wpdb->prefix . 'gf_memberships';

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
            $wpdb->insert($memberships_table, $membership);
        }
    }

    /**
     * Create upload directories
     */
    private static function create_upload_directories() {
        $upload_dir = wp_upload_dir();
        $gymflow_dir = $upload_dir['basedir'] . '/gymflow';

        $directories = array(
            $gymflow_dir,
            $gymflow_dir . '/members',
            $gymflow_dir . '/trainers',
            $gymflow_dir . '/equipment',
            $gymflow_dir . '/classes',
            $gymflow_dir . '/exports',
            $gymflow_dir . '/imports'
        );

        foreach ($directories as $dir) {
            if (!file_exists($dir)) {
                wp_mkdir_p($dir);
                
                // Create index.php to prevent directory browsing
                $index_file = $dir . '/index.php';
                if (!file_exists($index_file)) {
                    file_put_contents($index_file, '<?php // Silence is golden');
                }
            }
        }
    }

    /**
     * Schedule cron jobs
     */
    private static function schedule_cron_jobs() {
        // Daily cleanup tasks
        if (!wp_next_scheduled('gymflow_daily_cleanup')) {
            wp_schedule_event(time(), 'daily', 'gymflow_daily_cleanup');
        }

        // Hourly membership checks
        if (!wp_next_scheduled('gymflow_membership_check')) {
            wp_schedule_event(time(), 'hourly', 'gymflow_membership_check');
        }

        // Weekly reports (if pro)
        if (!wp_next_scheduled('gymflow_weekly_reports')) {
            wp_schedule_event(strtotime('next monday 9:00'), 'weekly', 'gymflow_weekly_reports');
        }
    }
}