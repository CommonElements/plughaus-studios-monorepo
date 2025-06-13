<?php
/**
 * StudioSnap Activator - Plugin activation and setup
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class SS_Activator {
    
    /**
     * Plugin activation tasks
     */
    public static function activate() {
        // Check WordPress version
        if (version_compare(get_bloginfo('version'), '5.8', '<')) {
            deactivate_plugins(basename(__FILE__));
            wp_die(__('StudioSnap requires WordPress 5.8 or higher.', 'studiosnap'));
        }
        
        // Check PHP version
        if (version_compare(PHP_VERSION, '7.4', '<')) {
            deactivate_plugins(basename(__FILE__));
            wp_die(__('StudioSnap requires PHP 7.4 or higher.', 'studiosnap'));
        }
        
        // Create database tables
        self::create_database_tables();
        
        // Set default options
        self::set_default_options();
        
        // Create upload directories
        self::create_upload_directories();
        
        // Register post types and statuses
        SS_Post_Types::register_post_types();
        SS_Post_Types::register_post_statuses();
        
        // Add roles and capabilities
        SS_Capabilities::add_roles_and_caps();
        
        // Create default pages
        self::create_default_pages();
        
        // Set activation flag
        update_option('ss_plugin_activated', true);
        update_option('ss_activation_date', current_time('mysql'));
        update_option('ss_version', SS_VERSION);
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Schedule cron jobs
        self::schedule_cron_jobs();
        
        // Create default session packages
        self::create_default_packages();
        
        // Log activation
        SS_Utilities::log_activity('StudioSnap plugin activated successfully', 'info', array(
            'version' => SS_VERSION,
            'wordpress_version' => get_bloginfo('version'),
            'php_version' => PHP_VERSION
        ));
    }
    
    /**
     * Create database tables
     */
    private static function create_database_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Session notes table
        $table_session_notes = $wpdb->prefix . 'ss_session_notes';
        $sql_session_notes = "CREATE TABLE $table_session_notes (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            session_id bigint(20) NOT NULL,
            user_id bigint(20) NOT NULL,
            note_type varchar(50) DEFAULT 'general',
            note_content longtext NOT NULL,
            is_private tinyint(1) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY session_id (session_id),
            KEY user_id (user_id),
            KEY note_type (note_type),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        // Client interactions table
        $table_client_interactions = $wpdb->prefix . 'ss_client_interactions';
        $sql_client_interactions = "CREATE TABLE $table_client_interactions (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            session_id bigint(20) DEFAULT NULL,
            interaction_type varchar(50) NOT NULL,
            interaction_date datetime DEFAULT CURRENT_TIMESTAMP,
            subject varchar(255) DEFAULT '',
            content longtext DEFAULT '',
            staff_user_id bigint(20) DEFAULT NULL,
            follow_up_date datetime DEFAULT NULL,
            status varchar(50) DEFAULT 'completed',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY client_id (client_id),
            KEY session_id (session_id),
            KEY interaction_type (interaction_type),
            KEY interaction_date (interaction_date),
            KEY follow_up_date (follow_up_date)
        ) $charset_collate;";
        
        // Payment transactions table
        $table_payments = $wpdb->prefix . 'ss_payments';
        $sql_payments = "CREATE TABLE $table_payments (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            invoice_id bigint(20) NOT NULL,
            session_id bigint(20) DEFAULT NULL,
            client_id bigint(20) NOT NULL,
            transaction_id varchar(255) DEFAULT '',
            payment_method varchar(50) DEFAULT 'cash',
            payment_gateway varchar(50) DEFAULT '',
            amount decimal(10,2) NOT NULL DEFAULT 0.00,
            currency varchar(3) DEFAULT 'USD',
            status varchar(50) DEFAULT 'pending',
            gateway_response longtext DEFAULT '',
            payment_date datetime DEFAULT CURRENT_TIMESTAMP,
            refund_amount decimal(10,2) DEFAULT 0.00,
            refund_date datetime DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY invoice_id (invoice_id),
            KEY session_id (session_id),
            KEY client_id (client_id),
            KEY transaction_id (transaction_id),
            KEY payment_method (payment_method),
            KEY status (status),
            KEY payment_date (payment_date)
        ) $charset_collate;";
        
        // Email log table
        $table_email_log = $wpdb->prefix . 'ss_email_log';
        $sql_email_log = "CREATE TABLE $table_email_log (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            recipient_email varchar(255) NOT NULL,
            recipient_name varchar(255) DEFAULT '',
            client_id bigint(20) DEFAULT NULL,
            session_id bigint(20) DEFAULT NULL,
            email_type varchar(50) NOT NULL,
            subject varchar(500) NOT NULL,
            content longtext NOT NULL,
            status varchar(50) DEFAULT 'sent',
            sent_at datetime DEFAULT CURRENT_TIMESTAMP,
            opened_at datetime DEFAULT NULL,
            clicked_at datetime DEFAULT NULL,
            bounced_at datetime DEFAULT NULL,
            error_message text DEFAULT '',
            template_used varchar(100) DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY recipient_email (recipient_email),
            KEY client_id (client_id),
            KEY session_id (session_id),
            KEY email_type (email_type),
            KEY status (status),
            KEY sent_at (sent_at)
        ) $charset_collate;";
        
        // Gallery access log table
        $table_gallery_access = $wpdb->prefix . 'ss_gallery_access';
        $sql_gallery_access = "CREATE TABLE $table_gallery_access (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            gallery_id bigint(20) NOT NULL,
            client_id bigint(20) DEFAULT NULL,
            access_token varchar(255) NOT NULL,
            visitor_ip varchar(45) DEFAULT '',
            user_agent text DEFAULT '',
            accessed_at datetime DEFAULT CURRENT_TIMESTAMP,
            pages_viewed int(11) DEFAULT 1,
            images_viewed text DEFAULT '',
            download_count int(11) DEFAULT 0,
            last_activity datetime DEFAULT CURRENT_TIMESTAMP,
            session_duration int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY gallery_id (gallery_id),
            KEY client_id (client_id),
            KEY access_token (access_token),
            KEY accessed_at (accessed_at),
            KEY visitor_ip (visitor_ip)
        ) $charset_collate;";
        
        // Analytics tracking table
        $table_analytics = $wpdb->prefix . 'ss_analytics';
        $sql_analytics = "CREATE TABLE $table_analytics (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            metric_type varchar(100) NOT NULL,
            metric_key varchar(255) NOT NULL,
            metric_value longtext NOT NULL,
            period_type varchar(50) DEFAULT 'daily',
            period_date date NOT NULL,
            metadata longtext DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY metric_type (metric_type),
            KEY metric_key (metric_key),
            KEY period_type (period_type),
            KEY period_date (period_date),
            UNIQUE KEY unique_metric (metric_type, metric_key, period_date)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        dbDelta($sql_session_notes);
        dbDelta($sql_client_interactions);
        dbDelta($sql_payments);
        dbDelta($sql_email_log);
        dbDelta($sql_gallery_access);
        dbDelta($sql_analytics);
        
        // Update database version
        update_option('ss_db_version', '1.0.0');
    }
    
    /**
     * Set default plugin options
     */
    private static function set_default_options() {
        $default_options = array(
            // General settings
            'ss_studio_name' => get_bloginfo('name'),
            'ss_studio_email' => get_option('admin_email'),
            'ss_studio_phone' => '',
            'ss_studio_address' => '',
            'ss_studio_city' => '',
            'ss_studio_state' => '',
            'ss_studio_zip' => '',
            'ss_studio_website' => home_url(),
            
            // Business hours
            'ss_monday_hours' => '9:00 AM - 5:00 PM',
            'ss_tuesday_hours' => '9:00 AM - 5:00 PM',
            'ss_wednesday_hours' => '9:00 AM - 5:00 PM',
            'ss_thursday_hours' => '9:00 AM - 5:00 PM',
            'ss_friday_hours' => '9:00 AM - 5:00 PM',
            'ss_saturday_hours' => '10:00 AM - 4:00 PM',
            'ss_sunday_hours' => 'Closed',
            
            // Booking settings
            'ss_booking_advance_days' => 90,
            'ss_booking_buffer_time' => 30,
            'ss_booking_confirmation_required' => 'yes',
            'ss_booking_deposit_required' => 'yes',
            'ss_booking_deposit_amount' => 50,
            'ss_booking_cancellation_policy' => '24 hours',
            
            // Email settings
            'ss_email_notifications' => 'yes',
            'ss_email_from_name' => get_bloginfo('name'),
            'ss_email_from_address' => get_option('admin_email'),
            'ss_email_booking_confirmation' => 'yes',
            'ss_email_session_reminder' => 'yes',
            'ss_email_gallery_ready' => 'yes',
            'ss_email_invoice_sent' => 'yes',
            
            // Currency and pricing
            'ss_currency' => 'USD',
            'ss_currency_symbol' => '$',
            'ss_currency_position' => 'before',
            'ss_tax_rate' => 0,
            'ss_tax_inclusive' => 'no',
            
            // Gallery settings
            'ss_gallery_protection' => 'password',
            'ss_gallery_watermark' => 'no',
            'ss_gallery_download_limit' => 10,
            'ss_gallery_expiry_days' => 90,
            'ss_image_quality' => 85,
            'ss_thumbnail_size' => 300,
            'ss_preview_size' => 800,
            
            // Contract settings
            'ss_contract_template' => self::get_default_contract_template(),
            'ss_contract_signature_required' => 'yes',
            'ss_contract_expiry_days' => 30,
            
            // Invoice settings
            'ss_invoice_prefix' => 'INV-',
            'ss_invoice_number_start' => 1001,
            'ss_invoice_due_days' => 30,
            'ss_late_fee_amount' => 25,
            'ss_late_fee_grace_days' => 5,
            
            // Client portal settings
            'ss_client_portal_enabled' => 'yes',
            'ss_client_registration' => 'admin_approval',
            'ss_client_dashboard_page' => 0,
            
            // Social media
            'ss_facebook_url' => '',
            'ss_instagram_url' => '',
            'ss_twitter_url' => '',
            'ss_linkedin_url' => '',
            
            // Pro features
            'ss_pro_license_key' => '',
            'ss_pro_license_status' => 'inactive'
        );
        
        foreach ($default_options as $option_key => $option_value) {
            if (get_option($option_key) === false) {
                add_option($option_key, $option_value);
            }
        }
    }
    
    /**
     * Create upload directories
     */
    private static function create_upload_directories() {
        $upload_dir = wp_upload_dir();
        $studiosnap_dir = $upload_dir['basedir'] . '/studiosnap';
        
        $directories = array(
            $studiosnap_dir,
            $studiosnap_dir . '/galleries',
            $studiosnap_dir . '/contracts',
            $studiosnap_dir . '/invoices',
            $studiosnap_dir . '/temp',
            $studiosnap_dir . '/backups'
        );
        
        foreach ($directories as $dir) {
            if (!file_exists($dir)) {
                wp_mkdir_p($dir);
                
                // Create .htaccess file for protection
                $htaccess_file = $dir . '/.htaccess';
                if (!file_exists($htaccess_file)) {
                    file_put_contents($htaccess_file, "deny from all\n");
                }
                
                // Create index.php file
                $index_file = $dir . '/index.php';
                if (!file_exists($index_file)) {
                    file_put_contents($index_file, "<?php\n// Silence is golden\n");
                }
            }
        }
    }
    
    /**
     * Create default pages
     */
    private static function create_default_pages() {
        // Client dashboard page
        $dashboard_page = array(
            'post_title' => __('Client Dashboard', 'studiosnap'),
            'post_content' => '[studiosnap_client_dashboard]',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_author' => 1,
            'meta_input' => array(
                '_ss_system_page' => 'client_dashboard'
            )
        );
        
        $dashboard_id = wp_insert_post($dashboard_page);
        if ($dashboard_id) {
            update_option('ss_client_dashboard_page', $dashboard_id);
        }
        
        // Booking page
        $booking_page = array(
            'post_title' => __('Book a Session', 'studiosnap'),
            'post_content' => '[studiosnap_booking_form]',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_author' => 1,
            'meta_input' => array(
                '_ss_system_page' => 'booking_form'
            )
        );
        
        $booking_id = wp_insert_post($booking_page);
        if ($booking_id) {
            update_option('ss_booking_page', $booking_id);
        }
        
        // Portfolio page
        $portfolio_page = array(
            'post_title' => __('Portfolio', 'studiosnap'),
            'post_content' => '[studiosnap_portfolio]',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_author' => 1,
            'meta_input' => array(
                '_ss_system_page' => 'portfolio'
            )
        );
        
        $portfolio_id = wp_insert_post($portfolio_page);
        if ($portfolio_id) {
            update_option('ss_portfolio_page', $portfolio_id);
        }
    }
    
    /**
     * Schedule cron jobs
     */
    private static function schedule_cron_jobs() {
        // Daily cleanup and maintenance
        if (!wp_next_scheduled('ss_daily_cleanup')) {
            wp_schedule_event(time(), 'daily', 'ss_daily_cleanup');
        }
        
        // Send reminder emails
        if (!wp_next_scheduled('ss_send_reminders')) {
            wp_schedule_event(time(), 'hourly', 'ss_send_reminders');
        }
        
        // Process recurring sessions
        if (!wp_next_scheduled('ss_process_recurring')) {
            wp_schedule_event(time(), 'daily', 'ss_process_recurring');
        }
        
        // Generate analytics
        if (!wp_next_scheduled('ss_generate_analytics')) {
            wp_schedule_event(time(), 'daily', 'ss_generate_analytics');
        }
        
        // Backup data
        if (!wp_next_scheduled('ss_backup_data')) {
            wp_schedule_event(time(), 'weekly', 'ss_backup_data');
        }
    }
    
    /**
     * Create default session packages
     */
    private static function create_default_packages() {
        $packages = SS_Utilities::get_session_packages();
        
        foreach ($packages as $package_key => $package_data) {
            $package_post = array(
                'post_title' => $package_data['name'],
                'post_content' => $package_data['description'],
                'post_status' => 'publish',
                'post_type' => 'ss_package',
                'meta_input' => array(
                    '_ss_package_type' => $package_key,
                    '_ss_package_price' => $package_data['price'],
                    '_ss_package_duration' => $package_data['duration'],
                    '_ss_package_featured' => ($package_key === 'portrait') ? 'yes' : 'no',
                    '_ss_package_includes' => array(
                        'consultation' => 'yes',
                        'shooting_time' => $package_data['duration'] . ' hours',
                        'edited_photos' => '20-30 high-resolution images',
                        'online_gallery' => 'yes',
                        'print_release' => 'yes'
                    )
                )
            );
            
            wp_insert_post($package_post);
        }
    }
    
    /**
     * Get default contract template
     */
    private static function get_default_contract_template() {
        return '
<h2>Photography Services Agreement</h2>

<p><strong>Client:</strong> {client_name}<br>
<strong>Date:</strong> {contract_date}<br>
<strong>Session Date:</strong> {session_date}<br>
<strong>Session Type:</strong> {session_type}</p>

<h3>Services</h3>
<p>Photographer agrees to provide professional photography services for the above-mentioned session.</p>

<h3>Payment Terms</h3>
<p>Total investment: {total_amount}<br>
Deposit required: {deposit_amount}<br>
Balance due: {balance_due}</p>

<h3>Cancellation Policy</h3>
<p>Client may cancel this agreement with 48 hours notice. Deposits are non-refundable within 48 hours of the scheduled session.</p>

<h3>Image Delivery</h3>
<p>Images will be delivered via online gallery within 2-3 weeks of the session date.</p>

<h3>Usage Rights</h3>
<p>Client receives personal usage rights to all delivered images. Photographer retains copyright and may use images for portfolio and marketing purposes.</p>

<p><strong>Client Signature:</strong> ________________________ <strong>Date:</strong> __________</p>
<p><strong>Photographer:</strong> {studio_name} <strong>Date:</strong> {contract_date}</p>
        ';
    }
    
    /**
     * Plugin deactivation tasks
     */
    public static function deactivate() {
        // Clear scheduled cron jobs
        wp_clear_scheduled_hook('ss_daily_cleanup');
        wp_clear_scheduled_hook('ss_send_reminders');
        wp_clear_scheduled_hook('ss_process_recurring');
        wp_clear_scheduled_hook('ss_generate_analytics');
        wp_clear_scheduled_hook('ss_backup_data');
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Log deactivation
        SS_Utilities::log_activity('StudioSnap plugin deactivated', 'info');
    }
    
    /**
     * Plugin uninstall tasks
     */
    public static function uninstall() {
        global $wpdb;
        
        // Remove custom tables
        $tables = array(
            $wpdb->prefix . 'ss_session_notes',
            $wpdb->prefix . 'ss_client_interactions',
            $wpdb->prefix . 'ss_payments',
            $wpdb->prefix . 'ss_email_log',
            $wpdb->prefix . 'ss_gallery_access',
            $wpdb->prefix . 'ss_analytics'
        );
        
        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS {$table}");
        }
        
        // Remove all plugin options
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'ss_%'");
        
        // Remove all post types and meta
        $post_types = array('ss_session', 'ss_client', 'ss_gallery', 'ss_package', 'ss_invoice', 'ss_contract');
        foreach ($post_types as $post_type) {
            $posts = get_posts(array('post_type' => $post_type, 'numberposts' => -1));
            foreach ($posts as $post) {
                wp_delete_post($post->ID, true);
            }
        }
        
        // Remove capabilities
        SS_Capabilities::remove_roles_and_caps();
        
        // Remove upload directories
        $upload_dir = wp_upload_dir();
        $studiosnap_dir = $upload_dir['basedir'] . '/studiosnap';
        if (file_exists($studiosnap_dir)) {
            self::delete_directory($studiosnap_dir);
        }
    }
    
    /**
     * Recursively delete directory
     */
    private static function delete_directory($dir) {
        if (!file_exists($dir)) {
            return true;
        }
        
        if (!is_dir($dir)) {
            return unlink($dir);
        }
        
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            
            if (!self::delete_directory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
        
        return rmdir($dir);
    }
}