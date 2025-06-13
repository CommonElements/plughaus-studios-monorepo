<?php
/**
 * Email Notifications System for PlugHaus Property Management
 * Handles automated email notifications for property management events
 *
 * @package PlugHausPropertyManagement
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * PlugHaus Property Management Email Notifications Class
 */
class PHPM_Email_Notifications {
    
    /**
     * Initialize email notification hooks
     */
    public static function init() {
        // Email template hooks
        add_action('wp_ajax_phpm_send_test_email', array(__CLASS__, 'ajax_send_test_email'));
        add_action('wp_ajax_phpm_send_notification', array(__CLASS__, 'ajax_send_notification'));
        
        // Automated notification triggers
        add_action('phpm_lease_expiring_soon', array(__CLASS__, 'send_lease_expiring_notification'), 10, 2);
        add_action('phpm_maintenance_request_created', array(__CLASS__, 'send_maintenance_notification'), 10, 2);
        add_action('phpm_maintenance_request_updated', array(__CLASS__, 'send_maintenance_update_notification'), 10, 3);
        add_action('phpm_tenant_late_payment', array(__CLASS__, 'send_payment_reminder'), 10, 2);
        add_action('phpm_lease_created', array(__CLASS__, 'send_welcome_notification'), 10, 2);
        
        // Daily cron for checking upcoming events
        add_action('phpm_daily_notification_check', array(__CLASS__, 'check_scheduled_notifications'));
        
        // Schedule daily cron if not already scheduled
        if (!wp_next_scheduled('phpm_daily_notification_check')) {
            wp_schedule_event(time(), 'daily', 'phpm_daily_notification_check');
        }
    }
    
    /**
     * Get email notification settings
     *
     * @return array
     */
    public static function get_email_settings() {
        $defaults = array(
            'from_name' => get_bloginfo('name'),
            'from_email' => get_option('admin_email'),
            'lease_expiring_enabled' => true,
            'lease_expiring_days' => 30,
            'maintenance_enabled' => true,
            'payment_reminders_enabled' => true,
            'payment_reminder_days' => array(5, 10, 15),
            'welcome_emails_enabled' => true,
            'manager_notifications' => true,
            'tenant_notifications' => true,
            'email_template' => 'default',
            'logo_url' => '',
            'footer_text' => sprintf(__('Sent from %s property management system', 'plughaus-property'), get_bloginfo('name'))
        );
        
        $settings = get_option('phpm_email_settings', array());
        return wp_parse_args($settings, $defaults);
    }
    
    /**
     * Update email notification settings
     *
     * @param array $settings
     * @return bool
     */
    public static function update_email_settings($settings) {
        $current_settings = self::get_email_settings();
        $new_settings = wp_parse_args($settings, $current_settings);
        
        // Sanitize settings
        $new_settings['from_name'] = sanitize_text_field($new_settings['from_name']);
        $new_settings['from_email'] = sanitize_email($new_settings['from_email']);
        $new_settings['lease_expiring_days'] = absint($new_settings['lease_expiring_days']);
        $new_settings['logo_url'] = esc_url_raw($new_settings['logo_url']);
        $new_settings['footer_text'] = wp_kses_post($new_settings['footer_text']);
        
        return update_option('phpm_email_settings', $new_settings);
    }
    
    /**
     * Send lease expiring notification
     *
     * @param int $lease_id
     * @param int $days_until_expiry
     */
    public static function send_lease_expiring_notification($lease_id, $days_until_expiry) {
        $settings = self::get_email_settings();
        
        if (!$settings['lease_expiring_enabled']) {
            return;
        }
        
        $lease = get_post($lease_id);
        if (!$lease) {
            return;
        }
        
        $tenant_id = get_post_meta($lease_id, '_phpm_tenant_id', true);
        $property_id = get_post_meta($lease_id, '_phpm_property_id', true);
        $unit_id = get_post_meta($lease_id, '_phpm_unit_id', true);
        
        $tenant_email = get_post_meta($tenant_id, '_phpm_email', true);
        $tenant_name = get_the_title($tenant_id);
        $property_name = get_the_title($property_id);
        $unit_number = get_post_meta($unit_id, '_phpm_unit_number', true);
        $end_date = get_post_meta($lease_id, '_phpm_end_date', true);
        
        if (!$tenant_email) {
            return;
        }
        
        // Send to tenant
        if ($settings['tenant_notifications']) {
            $subject = sprintf(__('Lease Expiring Soon - %s', 'plughaus-property'), $property_name);
            $template_data = array(
                'recipient_name' => $tenant_name,
                'property_name' => $property_name,
                'unit_number' => $unit_number,
                'days_until_expiry' => $days_until_expiry,
                'end_date' => date_i18n(get_option('date_format'), strtotime($end_date)),
                'contact_info' => self::get_contact_info()
            );
            
            $message = self::get_email_template('lease_expiring_tenant', $template_data);
            self::send_email($tenant_email, $subject, $message);
        }
        
        // Send to property manager
        if ($settings['manager_notifications']) {
            $manager_email = $settings['from_email'];
            $subject = sprintf(__('Tenant Lease Expiring - %s', 'plughaus-property'), $tenant_name);
            $template_data = array(
                'tenant_name' => $tenant_name,
                'property_name' => $property_name,
                'unit_number' => $unit_number,
                'days_until_expiry' => $days_until_expiry,
                'end_date' => date_i18n(get_option('date_format'), strtotime($end_date)),
                'tenant_email' => $tenant_email,
                'lease_id' => $lease_id
            );
            
            $message = self::get_email_template('lease_expiring_manager', $template_data);
            self::send_email($manager_email, $subject, $message);
        }
    }
    
    /**
     * Send maintenance request notification
     *
     * @param int $maintenance_id
     * @param string $type (created|updated)
     */
    public static function send_maintenance_notification($maintenance_id, $type = 'created') {
        $settings = self::get_email_settings();
        
        if (!$settings['maintenance_enabled']) {
            return;
        }
        
        $maintenance = get_post($maintenance_id);
        if (!$maintenance) {
            return;
        }
        
        $tenant_id = get_post_meta($maintenance_id, '_phpm_tenant_id', true);
        $property_id = get_post_meta($maintenance_id, '_phpm_property_id', true);
        $unit_id = get_post_meta($maintenance_id, '_phpm_unit_id', true);
        $priority = get_post_meta($maintenance_id, '_phpm_priority', true);
        $category = get_post_meta($maintenance_id, '_phpm_category', true);
        $status = get_post_meta($maintenance_id, '_phpm_status', true);
        
        $tenant_email = get_post_meta($tenant_id, '_phpm_email', true);
        $tenant_name = get_the_title($tenant_id);
        $property_name = get_the_title($property_id);
        $unit_number = get_post_meta($unit_id, '_phpm_unit_number', true);
        
        $template_data = array(
            'tenant_name' => $tenant_name,
            'property_name' => $property_name,
            'unit_number' => $unit_number,
            'request_title' => $maintenance->post_title,
            'request_description' => $maintenance->post_content,
            'priority' => ucfirst($priority),
            'category' => ucfirst($category),
            'status' => ucfirst(str_replace('_', ' ', $status)),
            'request_id' => $maintenance_id,
            'date_created' => date_i18n(get_option('date_format'), strtotime($maintenance->post_date)),
            'contact_info' => self::get_contact_info()
        );
        
        // Send confirmation to tenant
        if ($tenant_email && $settings['tenant_notifications']) {
            if ($type === 'created') {
                $subject = sprintf(__('Maintenance Request Received - %s', 'plughaus-property'), $maintenance->post_title);
                $message = self::get_email_template('maintenance_received_tenant', $template_data);
            } else {
                $subject = sprintf(__('Maintenance Request Update - %s', 'plughaus-property'), $maintenance->post_title);
                $message = self::get_email_template('maintenance_updated_tenant', $template_data);
            }
            
            self::send_email($tenant_email, $subject, $message);
        }
        
        // Send notification to property manager
        if ($settings['manager_notifications']) {
            $manager_email = $settings['from_email'];
            
            if ($type === 'created') {
                $subject = sprintf(__('New Maintenance Request - %s', 'plughaus-property'), $priority);
                $message = self::get_email_template('maintenance_new_manager', $template_data);
            } else {
                $subject = sprintf(__('Maintenance Request Updated - %s', 'plughaus-property'), $maintenance->post_title);
                $message = self::get_email_template('maintenance_updated_manager', $template_data);
            }
            
            self::send_email($manager_email, $subject, $message);
        }
    }
    
    /**
     * Send maintenance update notification wrapper
     *
     * @param int $maintenance_id
     * @param string $old_status
     * @param string $new_status
     */
    public static function send_maintenance_update_notification($maintenance_id, $old_status, $new_status) {
        self::send_maintenance_notification($maintenance_id, 'updated');
    }
    
    /**
     * Send payment reminder notification
     *
     * @param int $tenant_id
     * @param int $days_overdue
     */
    public static function send_payment_reminder($tenant_id, $days_overdue) {
        $settings = self::get_email_settings();
        
        if (!$settings['payment_reminders_enabled'] || !$settings['tenant_notifications']) {
            return;
        }
        
        // Check if this number of days is in our reminder schedule
        if (!in_array($days_overdue, $settings['payment_reminder_days'])) {
            return;
        }
        
        $tenant_email = get_post_meta($tenant_id, '_phpm_email', true);
        $tenant_name = get_the_title($tenant_id);
        
        if (!$tenant_email) {
            return;
        }
        
        // Get active lease for tenant
        $lease_query = new WP_Query(array(
            'post_type' => 'phpm_lease',
            'meta_query' => array(
                array(
                    'key' => '_phpm_tenant_id',
                    'value' => $tenant_id,
                    'compare' => '='
                ),
                array(
                    'key' => '_phpm_status',
                    'value' => 'active',
                    'compare' => '='
                )
            ),
            'posts_per_page' => 1
        ));
        
        if (!$lease_query->have_posts()) {
            return;
        }
        
        $lease = $lease_query->posts[0];
        $property_id = get_post_meta($lease->ID, '_phpm_property_id', true);
        $unit_id = get_post_meta($lease->ID, '_phpm_unit_id', true);
        $rent_amount = get_post_meta($lease->ID, '_phpm_rent_amount', true);
        
        $property_name = get_the_title($property_id);
        $unit_number = get_post_meta($unit_id, '_phpm_unit_number', true);
        
        $subject = sprintf(__('Payment Reminder - %s', 'plughaus-property'), $property_name);
        $template_data = array(
            'tenant_name' => $tenant_name,
            'property_name' => $property_name,
            'unit_number' => $unit_number,
            'rent_amount' => PHPM_Utilities::format_currency($rent_amount),
            'days_overdue' => $days_overdue,
            'contact_info' => self::get_contact_info(),
            'payment_instructions' => self::get_payment_instructions()
        );
        
        $message = self::get_email_template('payment_reminder', $template_data);
        self::send_email($tenant_email, $subject, $message);
        
        wp_reset_postdata();
    }
    
    /**
     * Send welcome notification to new tenant
     *
     * @param int $lease_id
     * @param int $tenant_id
     */
    public static function send_welcome_notification($lease_id, $tenant_id) {
        $settings = self::get_email_settings();
        
        if (!$settings['welcome_emails_enabled'] || !$settings['tenant_notifications']) {
            return;
        }
        
        $tenant_email = get_post_meta($tenant_id, '_phpm_email', true);
        $tenant_name = get_the_title($tenant_id);
        
        if (!$tenant_email) {
            return;
        }
        
        $property_id = get_post_meta($lease_id, '_phpm_property_id', true);
        $unit_id = get_post_meta($lease_id, '_phpm_unit_id', true);
        $start_date = get_post_meta($lease_id, '_phpm_start_date', true);
        $rent_amount = get_post_meta($lease_id, '_phpm_rent_amount', true);
        
        $property_name = get_the_title($property_id);
        $unit_number = get_post_meta($unit_id, '_phpm_unit_number', true);
        $property_address = get_post_meta($property_id, '_phpm_address', true);
        
        $subject = sprintf(__('Welcome to %s!', 'plughaus-property'), $property_name);
        $template_data = array(
            'tenant_name' => $tenant_name,
            'property_name' => $property_name,
            'unit_number' => $unit_number,
            'property_address' => $property_address,
            'move_in_date' => date_i18n(get_option('date_format'), strtotime($start_date)),
            'rent_amount' => PHPM_Utilities::format_currency($rent_amount),
            'contact_info' => self::get_contact_info(),
            'payment_instructions' => self::get_payment_instructions(),
            'important_info' => self::get_tenant_important_info()
        );
        
        $message = self::get_email_template('welcome_tenant', $template_data);
        self::send_email($tenant_email, $subject, $message);
    }
    
    /**
     * Check for scheduled notifications (runs daily)
     */
    public static function check_scheduled_notifications() {
        $settings = self::get_email_settings();
        
        // Check for expiring leases
        if ($settings['lease_expiring_enabled']) {
            self::check_expiring_leases();
        }
        
        // Check for payment reminders
        if ($settings['payment_reminders_enabled']) {
            self::check_payment_reminders();
        }
    }
    
    /**
     * Check for expiring leases and send notifications
     */
    private static function check_expiring_leases() {
        $settings = self::get_email_settings();
        $days_ahead = $settings['lease_expiring_days'];
        
        $target_date = date('Y-m-d', strtotime("+{$days_ahead} days"));
        
        $lease_query = new WP_Query(array(
            'post_type' => 'phpm_lease',
            'meta_query' => array(
                array(
                    'key' => '_phpm_end_date',
                    'value' => $target_date,
                    'compare' => '='
                ),
                array(
                    'key' => '_phpm_status',
                    'value' => 'active',
                    'compare' => '='
                )
            ),
            'posts_per_page' => -1
        ));
        
        if ($lease_query->have_posts()) {
            foreach ($lease_query->posts as $lease) {
                // Check if we've already sent notification for this lease
                $notification_sent = get_post_meta($lease->ID, '_phpm_expiry_notification_sent', true);
                if (!$notification_sent) {
                    do_action('phpm_lease_expiring_soon', $lease->ID, $days_ahead);
                    update_post_meta($lease->ID, '_phpm_expiry_notification_sent', current_time('mysql'));
                }
            }
        }
        
        wp_reset_postdata();
    }
    
    /**
     * Check for overdue payments and send reminders
     */
    private static function check_payment_reminders() {
        $settings = self::get_email_settings();
        $reminder_days = $settings['payment_reminder_days'];
        
        foreach ($reminder_days as $days) {
            $target_date = date('Y-m-d', strtotime("-{$days} days"));
            
            // This would need integration with a payment tracking system
            // For now, we'll create a hook that can be used by payment plugins
            do_action('phpm_check_overdue_payments', $target_date, $days);
        }
    }
    
    /**
     * Get email template content
     *
     * @param string $template_name
     * @param array $data
     * @return string
     */
    public static function get_email_template($template_name, $data = array()) {
        $templates = self::get_default_templates();
        
        if (!isset($templates[$template_name])) {
            return '';
        }
        
        $template = $templates[$template_name];
        
        // Replace placeholders
        foreach ($data as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }
        
        // Wrap in email layout
        return self::wrap_email_content($template, $data);
    }
    
    /**
     * Wrap email content in HTML layout
     *
     * @param string $content
     * @param array $data
     * @return string
     */
    private static function wrap_email_content($content, $data = array()) {
        $settings = self::get_email_settings();
        
        $logo_html = '';
        if (!empty($settings['logo_url'])) {
            $logo_html = '<img src="' . esc_url($settings['logo_url']) . '" alt="' . esc_attr($settings['from_name']) . '" style="max-width: 200px; margin-bottom: 20px;">';
        }
        
        $html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . esc_html($settings['from_name']) . '</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .email-container { max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .email-header { text-align: center; border-bottom: 2px solid #0073aa; padding-bottom: 20px; margin-bottom: 30px; }
        .email-content { margin-bottom: 30px; }
        .email-footer { border-top: 1px solid #ddd; padding-top: 20px; text-align: center; font-size: 12px; color: #666; }
        .button { display: inline-block; padding: 10px 20px; background-color: #0073aa; color: #ffffff; text-decoration: none; border-radius: 4px; margin: 10px 0; }
        .highlight { background-color: #fff3cd; padding: 10px; border-left: 4px solid #ffc107; margin: 15px 0; }
        .property-info { background-color: #f8f9fa; padding: 15px; border-radius: 4px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            ' . $logo_html . '
            <h1>' . esc_html($settings['from_name']) . '</h1>
        </div>
        <div class="email-content">
            ' . $content . '
        </div>
        <div class="email-footer">
            <p>' . wp_kses_post($settings['footer_text']) . '</p>
        </div>
    </div>
</body>
</html>';
        
        return $html;
    }
    
    /**
     * Get default email templates
     *
     * @return array
     */
    private static function get_default_templates() {
        return array(
            'lease_expiring_tenant' => '
                <h2>' . __('Lease Expiring Soon', 'plughaus-property') . '</h2>
                <p>' . __('Dear {{recipient_name}},', 'plughaus-property') . '</p>
                <p>' . __('This is a friendly reminder that your lease for {{property_name}} (Unit {{unit_number}}) will be expiring in {{days_until_expiry}} days on {{end_date}}.', 'plughaus-property') . '</p>
                <div class="highlight">
                    <p><strong>' . __('Important:', 'plughaus-property') . '</strong> ' . __('Please contact us to discuss lease renewal options or move-out procedures.', 'plughaus-property') . '</p>
                </div>
                <div class="property-info">
                    <h3>' . __('Property Information', 'plughaus-property') . '</h3>
                    <p><strong>' . __('Property:', 'plughaus-property') . '</strong> {{property_name}}</p>
                    <p><strong>' . __('Unit:', 'plughaus-property') . '</strong> {{unit_number}}</p>
                    <p><strong>' . __('Lease End Date:', 'plughaus-property') . '</strong> {{end_date}}</p>
                </div>
                <p>{{contact_info}}</p>
            ',
            
            'lease_expiring_manager' => '
                <h2>' . __('Tenant Lease Expiring', 'plughaus-property') . '</h2>
                <p>' . __('The following tenant lease is expiring in {{days_until_expiry}} days:', 'plughaus-property') . '</p>
                <div class="property-info">
                    <h3>' . __('Lease Information', 'plughaus-property') . '</h3>
                    <p><strong>' . __('Tenant:', 'plughaus-property') . '</strong> {{tenant_name}}</p>
                    <p><strong>' . __('Property:', 'plughaus-property') . '</strong> {{property_name}}</p>
                    <p><strong>' . __('Unit:', 'plughaus-property') . '</strong> {{unit_number}}</p>
                    <p><strong>' . __('End Date:', 'plughaus-property') . '</strong> {{end_date}}</p>
                    <p><strong>' . __('Email:', 'plughaus-property') . '</strong> {{tenant_email}}</p>
                </div>
                <p>' . __('Action may be required for lease renewal or unit turnover.', 'plughaus-property') . '</p>
            ',
            
            'maintenance_received_tenant' => '
                <h2>' . __('Maintenance Request Received', 'plughaus-property') . '</h2>
                <p>' . __('Dear {{tenant_name}},', 'plughaus-property') . '</p>
                <p>' . __('We have received your maintenance request and will address it as soon as possible.', 'plughaus-property') . '</p>
                <div class="property-info">
                    <h3>' . __('Request Details', 'plughaus-property') . '</h3>
                    <p><strong>' . __('Request ID:', 'plughaus-property') . '</strong> #{{request_id}}</p>
                    <p><strong>' . __('Property:', 'plughaus-property') . '</strong> {{property_name}} (Unit {{unit_number}})</p>
                    <p><strong>' . __('Issue:', 'plughaus-property') . '</strong> {{request_title}}</p>
                    <p><strong>' . __('Priority:', 'plughaus-property') . '</strong> {{priority}}</p>
                    <p><strong>' . __('Submitted:', 'plughaus-property') . '</strong> {{date_created}}</p>
                </div>
                <p>' . __('We will keep you updated on the progress of this request.', 'plughaus-property') . '</p>
                <p>{{contact_info}}</p>
            ',
            
            'maintenance_updated_tenant' => '
                <h2>' . __('Maintenance Request Update', 'plughaus-property') . '</h2>
                <p>' . __('Dear {{tenant_name}},', 'plughaus-property') . '</p>
                <p>' . __('Your maintenance request has been updated:', 'plughaus-property') . '</p>
                <div class="property-info">
                    <h3>' . __('Request Details', 'plughaus-property') . '</h3>
                    <p><strong>' . __('Request ID:', 'plughaus-property') . '</strong> #{{request_id}}</p>
                    <p><strong>' . __('Issue:', 'plughaus-property') . '</strong> {{request_title}}</p>
                    <p><strong>' . __('Status:', 'plughaus-property') . '</strong> {{status}}</p>
                </div>
                <p>{{contact_info}}</p>
            ',
            
            'maintenance_new_manager' => '
                <h2>' . __('New Maintenance Request', 'plughaus-property') . '</h2>
                <p>' . __('A new {{priority}} priority maintenance request has been submitted:', 'plughaus-property') . '</p>
                <div class="property-info">
                    <h3>' . __('Request Details', 'plughaus-property') . '</h3>
                    <p><strong>' . __('Request ID:', 'plughaus-property') . '</strong> #{{request_id}}</p>
                    <p><strong>' . __('Tenant:', 'plughaus-property') . '</strong> {{tenant_name}}</p>
                    <p><strong>' . __('Property:', 'plughaus-property') . '</strong> {{property_name}} (Unit {{unit_number}})</p>
                    <p><strong>' . __('Issue:', 'plughaus-property') . '</strong> {{request_title}}</p>
                    <p><strong>' . __('Priority:', 'plughaus-property') . '</strong> {{priority}}</p>
                    <p><strong>' . __('Category:', 'plughaus-property') . '</strong> {{category}}</p>
                    <p><strong>' . __('Description:', 'plughaus-property') . '</strong></p>
                    <p>{{request_description}}</p>
                </div>
            ',
            
            'payment_reminder' => '
                <h2>' . __('Payment Reminder', 'plughaus-property') . '</h2>
                <p>' . __('Dear {{tenant_name}},', 'plughaus-property') . '</p>
                <p>' . __('This is a reminder that your rent payment is now {{days_overdue}} days overdue.', 'plughaus-property') . '</p>
                <div class="highlight">
                    <p><strong>' . __('Amount Due:', 'plughaus-property') . '</strong> {{rent_amount}}</p>
                </div>
                <div class="property-info">
                    <h3>' . __('Property Information', 'plughaus-property') . '</h3>
                    <p><strong>' . __('Property:', 'plughaus-property') . '</strong> {{property_name}}</p>
                    <p><strong>' . __('Unit:', 'plughaus-property') . '</strong> {{unit_number}}</p>
                </div>
                <p>{{payment_instructions}}</p>
                <p>' . __('Please contact us immediately if you have any questions or concerns.', 'plughaus-property') . '</p>
                <p>{{contact_info}}</p>
            ',
            
            'welcome_tenant' => '
                <h2>' . __('Welcome to Your New Home!', 'plughaus-property') . '</h2>
                <p>' . __('Dear {{tenant_name}},', 'plughaus-property') . '</p>
                <p>' . __('Welcome to {{property_name}}! We are excited to have you as our newest resident.', 'plughaus-property') . '</p>
                <div class="property-info">
                    <h3>' . __('Your New Home', 'plughaus-property') . '</h3>
                    <p><strong>' . __('Property:', 'plughaus-property') . '</strong> {{property_name}}</p>
                    <p><strong>' . __('Unit:', 'plughaus-property') . '</strong> {{unit_number}}</p>
                    <p><strong>' . __('Address:', 'plughaus-property') . '</strong> {{property_address}}</p>
                    <p><strong>' . __('Move-in Date:', 'plughaus-property') . '</strong> {{move_in_date}}</p>
                    <p><strong>' . __('Monthly Rent:', 'plughaus-property') . '</strong> {{rent_amount}}</p>
                </div>
                <div class="highlight">
                    <h3>' . __('Important Information', 'plughaus-property') . '</h3>
                    <p>{{important_info}}</p>
                </div>
                <p>{{payment_instructions}}</p>
                <p>' . __('We look forward to providing you with excellent service during your tenancy.', 'plughaus-property') . '</p>
                <p>{{contact_info}}</p>
            '
        );
    }
    
    /**
     * Send email using WordPress mail function
     *
     * @param string $to
     * @param string $subject
     * @param string $message
     * @param array $headers
     * @return bool
     */
    public static function send_email($to, $subject, $message, $headers = array()) {
        $settings = self::get_email_settings();
        
        // Set up headers
        $default_headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $settings['from_name'] . ' <' . $settings['from_email'] . '>'
        );
        
        $headers = array_merge($default_headers, $headers);
        
        // Log email attempt
        PHPM_Utilities::log("Sending email to: {$to}, Subject: {$subject}", 'info');
        
        // Send email
        $sent = wp_mail($to, $subject, $message, $headers);
        
        if (!$sent) {
            PHPM_Utilities::log("Failed to send email to: {$to}", 'error');
        }
        
        return $sent;
    }
    
    /**
     * Get contact information for emails
     *
     * @return string
     */
    private static function get_contact_info() {
        $settings = self::get_email_settings();
        
        return sprintf(
            __('If you have any questions, please contact us at %s or call our office.', 'plughaus-property'),
            $settings['from_email']
        );
    }
    
    /**
     * Get payment instructions for emails
     *
     * @return string
     */
    private static function get_payment_instructions() {
        return __('Please submit your payment through our online portal or contact our office for payment options.', 'plughaus-property');
    }
    
    /**
     * Get important tenant information
     *
     * @return string
     */
    private static function get_tenant_important_info() {
        return __('Please familiarize yourself with the lease terms, emergency contact information, and building policies provided in your lease agreement.', 'plughaus-property');
    }
    
    /**
     * AJAX handler for sending test email
     */
    public static function ajax_send_test_email() {
        check_ajax_referer('phpm_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions.', 'plughaus-property'));
        }
        
        $email = sanitize_email($_POST['email']);
        $template = sanitize_text_field($_POST['template']);
        
        if (!$email) {
            wp_send_json_error(__('Invalid email address.', 'plughaus-property'));
        }
        
        $test_data = array(
            'recipient_name' => 'Test User',
            'tenant_name' => 'John Doe',
            'property_name' => 'Sample Property',
            'unit_number' => '101',
            'days_until_expiry' => 30,
            'end_date' => date_i18n(get_option('date_format'), strtotime('+30 days')),
            'request_title' => 'Test Maintenance Request',
            'priority' => 'Normal',
            'request_id' => '123',
            'contact_info' => self::get_contact_info()
        );
        
        $subject = sprintf(__('Test Email - %s', 'plughaus-property'), ucfirst(str_replace('_', ' ', $template)));
        $message = self::get_email_template($template, $test_data);
        
        $sent = self::send_email($email, $subject, $message);
        
        if ($sent) {
            wp_send_json_success(__('Test email sent successfully!', 'plughaus-property'));
        } else {
            wp_send_json_error(__('Failed to send test email.', 'plughaus-property'));
        }
    }
    
    /**
     * AJAX handler for sending notification
     */
    public static function ajax_send_notification() {
        check_ajax_referer('phpm_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions.', 'plughaus-property'));
        }
        
        $notification_type = sanitize_text_field($_POST['notification_type']);
        $post_id = absint($_POST['post_id']);
        
        switch ($notification_type) {
            case 'lease_expiring':
                $days_until = absint($_POST['days_until']);
                do_action('phpm_lease_expiring_soon', $post_id, $days_until);
                break;
                
            case 'maintenance_created':
                do_action('phpm_maintenance_request_created', $post_id);
                break;
                
            case 'welcome_tenant':
                $tenant_id = absint($_POST['tenant_id']);
                do_action('phpm_lease_created', $post_id, $tenant_id);
                break;
        }
        
        wp_send_json_success(__('Notification sent successfully!', 'plughaus-property'));
    }
}

// Initialize email notifications
PHPM_Email_Notifications::init();