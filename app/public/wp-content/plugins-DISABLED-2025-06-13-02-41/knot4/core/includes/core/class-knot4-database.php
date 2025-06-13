<?php
/**
 * Database schema and management for Knot4
 * 
 * @package Knot4
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Knot4_Database {
    
    /**
     * Initialize database hooks
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'maybe_create_tables'));
    }
    
    /**
     * Create database tables if they don't exist
     */
    public static function maybe_create_tables() {
        $current_version = get_option('knot4_db_version', '0.0.0');
        
        if (version_compare($current_version, KNOT4_VERSION, '<')) {
            self::create_tables();
            update_option('knot4_db_version', KNOT4_VERSION);
        }
    }
    
    /**
     * Create all database tables
     */
    public static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Donations table
        $donations_table = $wpdb->prefix . 'knot4_donations';
        $donations_sql = "CREATE TABLE $donations_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            donor_id bigint(20) unsigned DEFAULT NULL,
            form_id bigint(20) unsigned DEFAULT NULL,
            campaign_id bigint(20) unsigned DEFAULT NULL,
            reference_id varchar(50) NOT NULL,
            amount decimal(10,2) NOT NULL DEFAULT '0.00',
            currency varchar(3) NOT NULL DEFAULT 'USD',
            frequency varchar(20) NOT NULL DEFAULT 'once',
            status varchar(20) NOT NULL DEFAULT 'pending',
            payment_method varchar(50) DEFAULT NULL,
            payment_gateway varchar(50) DEFAULT NULL,
            gateway_transaction_id varchar(255) DEFAULT NULL,
            donor_email varchar(255) NOT NULL,
            donor_first_name varchar(100) DEFAULT NULL,
            donor_last_name varchar(100) DEFAULT NULL,
            donor_address text DEFAULT NULL,
            donor_phone varchar(50) DEFAULT NULL,
            is_anonymous tinyint(1) NOT NULL DEFAULT 0,
            is_recurring tinyint(1) NOT NULL DEFAULT 0,
            fund_designation varchar(255) DEFAULT NULL,
            dedication_type varchar(50) DEFAULT NULL,
            dedication_name varchar(255) DEFAULT NULL,
            dedication_message text DEFAULT NULL,
            notes text DEFAULT NULL,
            receipt_sent_at datetime DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY donor_id (donor_id),
            KEY form_id (form_id),
            KEY campaign_id (campaign_id),
            KEY reference_id (reference_id),
            KEY status (status),
            KEY donor_email (donor_email),
            KEY created_at (created_at),
            KEY gateway_transaction_id (gateway_transaction_id)
        ) $charset_collate;";
        
        // Recurring donations table
        $recurring_table = $wpdb->prefix . 'knot4_recurring_donations';
        $recurring_sql = "CREATE TABLE $recurring_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            parent_donation_id bigint(20) unsigned NOT NULL,
            donor_id bigint(20) unsigned NOT NULL,
            gateway_subscription_id varchar(255) NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'active',
            frequency varchar(20) NOT NULL DEFAULT 'monthly',
            amount decimal(10,2) NOT NULL DEFAULT '0.00',
            currency varchar(3) NOT NULL DEFAULT 'USD',
            next_payment_date datetime DEFAULT NULL,
            last_payment_date datetime DEFAULT NULL,
            failure_count int(11) NOT NULL DEFAULT 0,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY parent_donation_id (parent_donation_id),
            KEY donor_id (donor_id),
            KEY gateway_subscription_id (gateway_subscription_id),
            KEY status (status),
            KEY next_payment_date (next_payment_date)
        ) $charset_collate;";
        
        // Event registrations table
        $registrations_table = $wpdb->prefix . 'knot4_event_registrations';
        $registrations_sql = "CREATE TABLE $registrations_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            event_id bigint(20) unsigned NOT NULL,
            attendee_email varchar(255) NOT NULL,
            attendee_first_name varchar(100) NOT NULL,
            attendee_last_name varchar(100) NOT NULL,
            attendee_phone varchar(50) DEFAULT NULL,
            registration_type varchar(50) DEFAULT 'general',
            ticket_quantity int(11) NOT NULL DEFAULT 1,
            total_amount decimal(10,2) NOT NULL DEFAULT '0.00',
            payment_status varchar(20) NOT NULL DEFAULT 'pending',
            payment_method varchar(50) DEFAULT NULL,
            gateway_transaction_id varchar(255) DEFAULT NULL,
            check_in_status varchar(20) NOT NULL DEFAULT 'not_checked_in',
            check_in_time datetime DEFAULT NULL,
            special_requirements text DEFAULT NULL,
            registration_source varchar(100) DEFAULT 'website',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY event_id (event_id),
            KEY attendee_email (attendee_email),
            KEY payment_status (payment_status),
            KEY check_in_status (check_in_status),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        // Activity log table
        $activity_table = $wpdb->prefix . 'knot4_activity_log';
        $activity_sql = "CREATE TABLE $activity_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            type varchar(50) NOT NULL,
            message text NOT NULL,
            object_id bigint(20) unsigned DEFAULT 0,
            object_type varchar(50) DEFAULT NULL,
            user_id bigint(20) unsigned DEFAULT 0,
            ip_address varchar(45) DEFAULT NULL,
            user_agent text DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY type (type),
            KEY object_id (object_id),
            KEY user_id (user_id),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        // Email templates table
        $templates_table = $wpdb->prefix . 'knot4_email_templates';
        $templates_sql = "CREATE TABLE $templates_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            type varchar(50) NOT NULL,
            subject varchar(255) NOT NULL,
            content text NOT NULL,
            is_active tinyint(1) NOT NULL DEFAULT 1,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY type (type),
            KEY is_active (is_active)
        ) $charset_collate;";
        
        // Form submissions table (for contact forms, volunteer forms, etc.)
        $submissions_table = $wpdb->prefix . 'knot4_form_submissions';
        $submissions_sql = "CREATE TABLE $submissions_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            form_type varchar(50) NOT NULL,
            form_id bigint(20) unsigned DEFAULT NULL,
            submitter_email varchar(255) NOT NULL,
            submitter_name varchar(255) DEFAULT NULL,
            submission_data longtext DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'unread',
            ip_address varchar(45) DEFAULT NULL,
            user_agent text DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY form_type (form_type),
            KEY form_id (form_id),
            KEY submitter_email (submitter_email),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        // Email log table (Pro feature)
        $email_log_table = $wpdb->prefix . 'knot4_email_log';
        $email_log_sql = "CREATE TABLE $email_log_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            template_key varchar(100) NOT NULL,
            to_email varchar(255) NOT NULL,
            subject varchar(255) NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'sent',
            sent_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            opened_at datetime DEFAULT NULL,
            clicked_at datetime DEFAULT NULL,
            bounced_at datetime DEFAULT NULL,
            error_message text DEFAULT NULL,
            PRIMARY KEY (id),
            KEY template_key (template_key),
            KEY to_email (to_email),
            KEY status (status),
            KEY sent_at (sent_at)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        dbDelta($donations_sql);
        dbDelta($recurring_sql);
        dbDelta($registrations_sql);
        dbDelta($activity_sql);
        dbDelta($templates_sql);
        dbDelta($submissions_sql);
        dbDelta($email_log_sql);
        
        // Create default email templates
        self::create_default_email_templates();
        
        // Log activity
        Knot4_Utilities::log_activity('system', 'Database tables created/updated', 0, 0);
    }
    
    /**
     * Create default email templates
     */
    private static function create_default_email_templates() {
        global $wpdb;
        
        $templates_table = $wpdb->prefix . 'knot4_email_templates';
        
        // Check if templates already exist
        $existing = $wpdb->get_var("SELECT COUNT(*) FROM $templates_table");
        if ($existing > 0) {
            return;
        }
        
        $templates = array(
            array(
                'name' => 'Donation Thank You',
                'type' => 'donation_thank_you',
                'subject' => 'Thank you for your generous donation!',
                'content' => self::get_default_donation_thank_you_template(),
            ),
            array(
                'name' => 'Donation Receipt',
                'type' => 'donation_receipt',
                'subject' => 'Your donation receipt from {{organization_name}}',
                'content' => self::get_default_donation_receipt_template(),
            ),
            array(
                'name' => 'Event Registration Confirmation',
                'type' => 'event_registration',
                'subject' => 'Registration confirmed: {{event_title}}',
                'content' => self::get_default_event_registration_template(),
            ),
            array(
                'name' => 'Event Reminder',
                'type' => 'event_reminder',
                'subject' => 'Reminder: {{event_title}} is coming up!',
                'content' => self::get_default_event_reminder_template(),
            ),
        );
        
        foreach ($templates as $template) {
            $wpdb->insert($templates_table, $template, array('%s', '%s', '%s', '%s'));
        }
    }
    
    /**
     * Get default donation thank you template
     */
    private static function get_default_donation_thank_you_template() {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <h1 style="color: #2c3e50;">Thank You for Your Generous Donation!</h1>
            
            <p>Dear {{donor_first_name}},</p>
            
            <p>We are incredibly grateful for your donation of <strong>{{amount}}</strong> to {{organization_name}}. Your generosity makes a real difference in our mission.</p>
            
            <div style="background-color: #f8f9fa; padding: 20px; border-left: 4px solid #007cba; margin: 20px 0;">
                <h3 style="margin-top: 0;">Donation Details</h3>
                <p><strong>Amount:</strong> {{amount}}<br>
                <strong>Date:</strong> {{donation_date}}<br>
                <strong>Reference:</strong> {{reference_id}}</p>
            </div>
            
            <p>Your support helps us continue our important work. We will send you a formal receipt for tax purposes shortly.</p>
            
            <p>With heartfelt gratitude,<br>
            The {{organization_name}} Team</p>
            
            <hr style="margin: 30px 0;">
            <p style="font-size: 12px; color: #666;">
                {{organization_name}}<br>
                {{organization_address}}<br>
                {{organization_phone}} | {{organization_email}}
            </p>
        </div>';
    }
    
    /**
     * Get default donation receipt template
     */
    private static function get_default_donation_receipt_template() {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <h1 style="color: #2c3e50;">Official Donation Receipt</h1>
            
            <p><strong>Receipt #:</strong> {{reference_id}}</p>
            
            <div style="background-color: #f8f9fa; padding: 20px; margin: 20px 0;">
                <h3 style="margin-top: 0;">Donor Information</h3>
                <p>{{donor_first_name}} {{donor_last_name}}<br>
                {{donor_email}}</p>
                
                <h3>Donation Details</h3>
                <p><strong>Amount:</strong> {{amount}}<br>
                <strong>Date:</strong> {{donation_date}}<br>
                <strong>Payment Method:</strong> {{payment_method}}</p>
                
                {{#if fund_designation}}
                <p><strong>Fund:</strong> {{fund_designation}}</p>
                {{/if}}
                
                {{#if dedication_name}}
                <p><strong>In Honor/Memory of:</strong> {{dedication_name}}</p>
                {{/if}}
            </div>
            
            <p><strong>Tax Information:</strong> {{organization_name}} is a registered 501(c)(3) nonprofit organization. Tax ID: {{tax_id}}. No goods or services were provided in exchange for this donation.</p>
            
            <p>Thank you for your continued support!</p>
            
            <hr style="margin: 30px 0;">
            <p style="font-size: 12px; color: #666;">
                {{organization_name}}<br>
                {{organization_address}}<br>
                {{organization_phone}} | {{organization_email}}
            </p>
        </div>';
    }
    
    /**
     * Get default event registration template
     */
    private static function get_default_event_registration_template() {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <h1 style="color: #2c3e50;">Registration Confirmed!</h1>
            
            <p>Dear {{attendee_first_name}},</p>
            
            <p>Thank you for registering for <strong>{{event_title}}</strong>. We are excited to see you there!</p>
            
            <div style="background-color: #f8f9fa; padding: 20px; border-left: 4px solid #28a745; margin: 20px 0;">
                <h3 style="margin-top: 0;">Event Details</h3>
                <p><strong>Event:</strong> {{event_title}}<br>
                <strong>Date:</strong> {{event_date}}<br>
                <strong>Time:</strong> {{event_time}}<br>
                <strong>Location:</strong> {{event_location}}</p>
                
                <p><strong>Tickets:</strong> {{ticket_quantity}}<br>
                {{#if total_amount}}
                <strong>Total Paid:</strong> {{total_amount}}
                {{/if}}</p>
            </div>
            
            {{#if event_description}}
            <div>
                <h3>About This Event</h3>
                <p>{{event_description}}</p>
            </div>
            {{/if}}
            
            <p>Please save this email as your confirmation. If you have any questions, please contact us at {{organization_email}}.</p>
            
            <p>See you soon!<br>
            The {{organization_name}} Team</p>
        </div>';
    }
    
    /**
     * Get default event reminder template
     */
    private static function get_default_event_reminder_template() {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <h1 style="color: #2c3e50;">Event Reminder</h1>
            
            <p>Dear {{attendee_first_name}},</p>
            
            <p>This is a friendly reminder that <strong>{{event_title}}</strong> is coming up soon!</p>
            
            <div style="background-color: #fff3cd; padding: 20px; border-left: 4px solid #ffc107; margin: 20px 0;">
                <h3 style="margin-top: 0;">Event Details</h3>
                <p><strong>Date:</strong> {{event_date}}<br>
                <strong>Time:</strong> {{event_time}}<br>
                <strong>Location:</strong> {{event_location}}</p>
            </div>
            
            <p>We look forward to seeing you there! If you need to make any changes to your registration or have questions, please contact us.</p>
            
            <p>Best regards,<br>
            The {{organization_name}} Team</p>
        </div>';
    }
    
    /**
     * Drop all plugin tables (for uninstall)
     */
    public static function drop_tables() {
        global $wpdb;
        
        $tables = array(
            'knot4_donations',
            'knot4_recurring_donations',
            'knot4_event_registrations',
            'knot4_activity_log',
            'knot4_email_templates',
            'knot4_form_submissions',
            'knot4_email_log',
        );
        
        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}{$table}");
        }
    }
}