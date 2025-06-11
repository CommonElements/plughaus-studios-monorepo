<?php
/**
 * Email Manager for Knot4
 * 
 * @package Knot4
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Knot4_Email_Manager {
    
    /**
     * Email templates
     */
    private static $templates = array();
    
    /**
     * Initialize email manager
     */
    public static function init() {
        // Register email templates
        add_action('init', array(__CLASS__, 'register_email_templates'));
        
        // Email hooks
        add_action('knot4_donation_completed', array(__CLASS__, 'send_donation_receipt'), 10, 2);
        add_action('knot4_event_registered', array(__CLASS__, 'send_event_confirmation'), 10, 2);
        add_action('knot4_volunteer_submitted', array(__CLASS__, 'send_volunteer_confirmation'), 10, 1);
        add_action('knot4_newsletter_subscribed', array(__CLASS__, 'send_newsletter_welcome'), 10, 1);
        
        // Admin email notifications
        add_action('knot4_donation_completed', array(__CLASS__, 'send_admin_donation_notification'), 10, 2);
        add_action('knot4_volunteer_submitted', array(__CLASS__, 'send_admin_volunteer_notification'), 10, 1);
        
        // Scheduled email automation
        add_action('knot4_daily_email_automation', array(__CLASS__, 'process_daily_automation'));
        add_action('knot4_weekly_email_automation', array(__CLASS__, 'process_weekly_automation'));
        
        // Schedule cron events if not already scheduled
        if (!wp_next_scheduled('knot4_daily_email_automation')) {
            wp_schedule_event(time(), 'daily', 'knot4_daily_email_automation');
        }
        
        if (!wp_next_scheduled('knot4_weekly_email_automation')) {
            wp_schedule_event(time(), 'weekly', 'knot4_weekly_email_automation');
        }
    }
    
    /**
     * Register email templates
     */
    public static function register_email_templates() {
        self::$templates = array(
            'donation_receipt' => array(
                'name' => __('Donation Receipt', 'knot4'),
                'description' => __('Sent to donors after successful donation', 'knot4'),
                'subject' => __('Thank you for your donation!', 'knot4'),
                'variables' => array(
                    '{donor_name}' => __('Donor full name', 'knot4'),
                    '{donor_first_name}' => __('Donor first name', 'knot4'),
                    '{donor_email}' => __('Donor email address', 'knot4'),
                    '{donation_amount}' => __('Donation amount', 'knot4'),
                    '{donation_date}' => __('Donation date', 'knot4'),
                    '{donation_id}' => __('Donation ID', 'knot4'),
                    '{organization_name}' => __('Organization name', 'knot4'),
                    '{tax_id}' => __('Tax ID number', 'knot4'),
                    '{receipt_url}' => __('Receipt download URL', 'knot4'),
                ),
                'default_template' => self::get_default_donation_receipt_template(),
            ),
            
            'event_confirmation' => array(
                'name' => __('Event Registration Confirmation', 'knot4'),
                'description' => __('Sent when someone registers for an event', 'knot4'),
                'subject' => __('Event Registration Confirmed: {event_title}', 'knot4'),
                'variables' => array(
                    '{attendee_name}' => __('Attendee full name', 'knot4'),
                    '{attendee_first_name}' => __('Attendee first name', 'knot4'),
                    '{attendee_email}' => __('Attendee email', 'knot4'),
                    '{event_title}' => __('Event title', 'knot4'),
                    '{event_date}' => __('Event date', 'knot4'),
                    '{event_time}' => __('Event time', 'knot4'),
                    '{event_location}' => __('Event location', 'knot4'),
                    '{event_url}' => __('Event page URL', 'knot4'),
                    '{registration_id}' => __('Registration ID', 'knot4'),
                ),
                'default_template' => self::get_default_event_confirmation_template(),
            ),
            
            'volunteer_confirmation' => array(
                'name' => __('Volunteer Application Confirmation', 'knot4'),
                'description' => __('Sent when someone submits volunteer application', 'knot4'),
                'subject' => __('Thank you for volunteering!', 'knot4'),
                'variables' => array(
                    '{volunteer_name}' => __('Volunteer full name', 'knot4'),
                    '{volunteer_first_name}' => __('Volunteer first name', 'knot4'),
                    '{volunteer_email}' => __('Volunteer email', 'knot4'),
                    '{volunteer_interests}' => __('Areas of interest', 'knot4'),
                    '{organization_name}' => __('Organization name', 'knot4'),
                ),
                'default_template' => self::get_default_volunteer_confirmation_template(),
            ),
            
            'newsletter_welcome' => array(
                'name' => __('Newsletter Welcome', 'knot4'),
                'description' => __('Welcome email for newsletter subscribers', 'knot4'),
                'subject' => __('Welcome to our newsletter!', 'knot4'),
                'variables' => array(
                    '{subscriber_name}' => __('Subscriber name', 'knot4'),
                    '{subscriber_email}' => __('Subscriber email', 'knot4'),
                    '{unsubscribe_url}' => __('Unsubscribe URL', 'knot4'),
                    '{organization_name}' => __('Organization name', 'knot4'),
                ),
                'default_template' => self::get_default_newsletter_welcome_template(),
            ),
            
            'donation_reminder' => array(
                'name' => __('Donation Reminder', 'knot4'),
                'description' => __('Follow-up email to encourage donations', 'knot4'),
                'subject' => __('Your support makes a difference', 'knot4'),
                'variables' => array(
                    '{contact_name}' => __('Contact name', 'knot4'),
                    '{contact_email}' => __('Contact email', 'knot4'),
                    '{last_donation_amount}' => __('Last donation amount', 'knot4'),
                    '{last_donation_date}' => __('Last donation date', 'knot4'),
                    '{organization_name}' => __('Organization name', 'knot4'),
                    '{donation_url}' => __('Donation page URL', 'knot4'),
                ),
                'default_template' => self::get_default_donation_reminder_template(),
            ),
            
            'admin_donation_notification' => array(
                'name' => __('Admin Donation Notification', 'knot4'),
                'description' => __('Notification sent to admins when donation received', 'knot4'),
                'subject' => __('New donation received: {donation_amount}', 'knot4'),
                'variables' => array(
                    '{donor_name}' => __('Donor full name', 'knot4'),
                    '{donor_email}' => __('Donor email', 'knot4'),
                    '{donation_amount}' => __('Donation amount', 'knot4'),
                    '{donation_date}' => __('Donation date', 'knot4'),
                    '{donation_id}' => __('Donation ID', 'knot4'),
                    '{admin_url}' => __('Admin dashboard URL', 'knot4'),
                ),
                'default_template' => self::get_default_admin_donation_notification_template(),
            ),
        );
        
        // Allow plugins to add custom templates
        self::$templates = apply_filters('knot4_email_templates', self::$templates);
    }
    
    /**
     * Send email using template
     */
    public static function send_template_email($template_key, $to_email, $variables = array()) {
        if (!isset(self::$templates[$template_key])) {
            return false;
        }
        
        $template = self::$templates[$template_key];
        
        // Get custom template or use default
        $email_settings = Knot4_Utilities::get_email_settings();
        $custom_template = isset($email_settings['templates'][$template_key]) ? 
                          $email_settings['templates'][$template_key] : array();
        
        $subject = isset($custom_template['subject']) ? 
                  $custom_template['subject'] : $template['subject'];
        $content = isset($custom_template['content']) ? 
                  $custom_template['content'] : $template['default_template'];
        
        // Replace variables
        $subject = self::replace_variables($subject, $variables);
        $content = self::replace_variables($content, $variables);
        
        // Set up email headers
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
        );
        
        // From email and name
        $from_name = isset($email_settings['from_name']) ? 
                    $email_settings['from_name'] : get_bloginfo('name');
        $from_email = isset($email_settings['from_email']) ? 
                     $email_settings['from_email'] : get_option('admin_email');
        
        $headers[] = 'From: ' . $from_name . ' <' . $from_email . '>';
        
        if (isset($email_settings['reply_to']) && !empty($email_settings['reply_to'])) {
            $headers[] = 'Reply-To: ' . $email_settings['reply_to'];
        }
        
        // Wrap content in email template
        $html_content = self::wrap_email_content($content, $subject, $variables);
        
        // Send email
        $sent = wp_mail($to_email, $subject, $html_content, $headers);
        
        // Log email
        self::log_email($template_key, $to_email, $subject, $sent);
        
        return $sent;
    }
    
    /**
     * Replace variables in content
     */
    private static function replace_variables($content, $variables) {
        foreach ($variables as $key => $value) {
            $content = str_replace($key, $value, $content);
        }
        return $content;
    }
    
    /**
     * Wrap content in HTML email template
     */
    public static function wrap_email_content($content, $subject, $variables) {
        $org_settings = Knot4_Utilities::get_organization_settings();
        $org_name = $org_settings['organization_name'];
        
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo esc_html($subject); ?></title>
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    line-height: 1.6; 
                    color: #333; 
                    margin: 0; 
                    padding: 0; 
                    background-color: #f4f4f4; 
                }
                .email-container { 
                    max-width: 600px; 
                    margin: 0 auto; 
                    background: white; 
                    border-radius: 8px; 
                    overflow: hidden; 
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
                }
                .email-header { 
                    background: #667eea; 
                    color: white; 
                    padding: 20px; 
                    text-align: center; 
                }
                .email-body { 
                    padding: 30px; 
                }
                .email-footer { 
                    background: #f8f9fa; 
                    padding: 20px; 
                    text-align: center; 
                    font-size: 12px; 
                    color: #666; 
                    border-top: 1px solid #e1e1e1; 
                }
                .button { 
                    display: inline-block; 
                    padding: 12px 24px; 
                    background: #667eea; 
                    color: white; 
                    text-decoration: none; 
                    border-radius: 6px; 
                    margin: 15px 0; 
                }
                .donation-details { 
                    background: #f8f9fa; 
                    padding: 15px; 
                    border-radius: 6px; 
                    margin: 15px 0; 
                }
                h1, h2, h3 { 
                    color: #333; 
                }
                .text-center { 
                    text-align: center; 
                }
            </style>
        </head>
        <body>
            <div class="email-container">
                <div class="email-header">
                    <h1><?php echo esc_html($org_name); ?></h1>
                </div>
                
                <div class="email-body">
                    <?php echo wpautop($content); ?>
                </div>
                
                <div class="email-footer">
                    <p><?php echo esc_html($org_name); ?></p>
                    <?php if (!empty($org_settings['organization_address'])): ?>
                    <p><?php echo nl2br(esc_html($org_settings['organization_address'])); ?></p>
                    <?php endif; ?>
                    
                    <?php if (!Knot4_Utilities::is_pro()): ?>
                    <p><small><?php printf(__('Powered by %s', 'knot4'), '<a href="https://plughausstudios.com/knot4/" style="color: #667eea;">Knot4</a>'); ?></small></p>
                    <?php endif; ?>
                </div>
            </div>
        </body>
        </html>
        <?php
        
        return ob_get_clean();
    }
    
    /**
     * Send donation receipt
     */
    public static function send_donation_receipt($donation_id, $donor_data) {
        $variables = array(
            '{donor_name}' => $donor_data['first_name'] . ' ' . $donor_data['last_name'],
            '{donor_first_name}' => $donor_data['first_name'],
            '{donor_email}' => $donor_data['email'],
            '{donation_amount}' => Knot4_Utilities::format_currency($donor_data['amount']),
            '{donation_date}' => date_i18n(get_option('date_format')),
            '{donation_id}' => $donation_id,
            '{organization_name}' => Knot4_Utilities::get_organization_settings()['organization_name'],
            '{tax_id}' => Knot4_Utilities::get_organization_settings()['tax_id'],
            '{receipt_url}' => home_url('/receipt/?id=' . $donation_id),
        );
        
        return self::send_template_email('donation_receipt', $donor_data['email'], $variables);
    }
    
    /**
     * Send event confirmation
     */
    public static function send_event_confirmation($event_id, $attendee_data) {
        $event = get_post($event_id);
        if (!$event) return false;
        
        $event_date = get_post_meta($event_id, '_knot4_event_date', true);
        $event_time = get_post_meta($event_id, '_knot4_event_time', true);
        $event_location = get_post_meta($event_id, '_knot4_event_location', true);
        
        $variables = array(
            '{attendee_name}' => $attendee_data['first_name'] . ' ' . $attendee_data['last_name'],
            '{attendee_first_name}' => $attendee_data['first_name'],
            '{attendee_email}' => $attendee_data['email'],
            '{event_title}' => $event->post_title,
            '{event_date}' => $event_date ? date_i18n(get_option('date_format'), strtotime($event_date)) : '',
            '{event_time}' => $event_time,
            '{event_location}' => $event_location,
            '{event_url}' => get_permalink($event_id),
            '{registration_id}' => isset($attendee_data['registration_id']) ? $attendee_data['registration_id'] : '',
        );
        
        return self::send_template_email('event_confirmation', $attendee_data['email'], $variables);
    }
    
    /**
     * Send volunteer confirmation
     */
    public static function send_volunteer_confirmation($volunteer_data) {
        $interests = is_array($volunteer_data['interests']) ? 
                    implode(', ', $volunteer_data['interests']) : $volunteer_data['interests'];
        
        $variables = array(
            '{volunteer_name}' => $volunteer_data['first_name'] . ' ' . $volunteer_data['last_name'],
            '{volunteer_first_name}' => $volunteer_data['first_name'],
            '{volunteer_email}' => $volunteer_data['email'],
            '{volunteer_interests}' => $interests,
            '{organization_name}' => Knot4_Utilities::get_organization_settings()['organization_name'],
        );
        
        return self::send_template_email('volunteer_confirmation', $volunteer_data['email'], $variables);
    }
    
    /**
     * Send newsletter welcome
     */
    public static function send_newsletter_welcome($subscriber_data) {
        $variables = array(
            '{subscriber_name}' => isset($subscriber_data['name']) ? $subscriber_data['name'] : '',
            '{subscriber_email}' => $subscriber_data['email'],
            '{unsubscribe_url}' => home_url('/unsubscribe/?email=' . urlencode($subscriber_data['email'])),
            '{organization_name}' => Knot4_Utilities::get_organization_settings()['organization_name'],
        );
        
        return self::send_template_email('newsletter_welcome', $subscriber_data['email'], $variables);
    }
    
    /**
     * Send admin donation notification
     */
    public static function send_admin_donation_notification($donation_id, $donor_data) {
        $admin_email = get_option('admin_email');
        
        $variables = array(
            '{donor_name}' => $donor_data['first_name'] . ' ' . $donor_data['last_name'],
            '{donor_email}' => $donor_data['email'],
            '{donation_amount}' => Knot4_Utilities::format_currency($donor_data['amount']),
            '{donation_date}' => date_i18n(get_option('date_format')),
            '{donation_id}' => $donation_id,
            '{admin_url}' => admin_url('admin.php?page=knot4-donations'),
        );
        
        return self::send_template_email('admin_donation_notification', $admin_email, $variables);
    }
    
    /**
     * Send admin volunteer notification
     */
    public static function send_admin_volunteer_notification($volunteer_data) {
        $admin_email = get_option('admin_email');
        
        $subject = sprintf(__('New volunteer application from %s', 'knot4'), 
                          $volunteer_data['first_name'] . ' ' . $volunteer_data['last_name']);
        
        $content = sprintf(
            __('A new volunteer application has been submitted:\n\nName: %s\nEmail: %s\nInterests: %s\n\nView all volunteers: %s', 'knot4'),
            $volunteer_data['first_name'] . ' ' . $volunteer_data['last_name'],
            $volunteer_data['email'],
            is_array($volunteer_data['interests']) ? implode(', ', $volunteer_data['interests']) : $volunteer_data['interests'],
            admin_url('edit.php?post_type=knot4_volunteer')
        );
        
        return wp_mail($admin_email, $subject, $content);
    }
    
    /**
     * Process daily email automation
     */
    public static function process_daily_automation() {
        // Send birthday emails
        self::send_birthday_emails();
        
        // Send donation anniversary emails
        self::send_donation_anniversary_emails();
        
        // Process any scheduled campaigns
        self::process_scheduled_campaigns();
    }
    
    /**
     * Process weekly email automation
     */
    public static function process_weekly_automation() {
        // Send donation reminders to inactive donors
        self::send_donation_reminders();
        
        // Send volunteer follow-ups
        self::send_volunteer_followups();
        
        // Newsletter automation
        self::process_newsletter_automation();
    }
    
    /**
     * Log email activity
     */
    private static function log_email($template_key, $to_email, $subject, $sent) {
        if (Knot4_Utilities::is_pro()) {
            global $wpdb;
            
            $wpdb->insert(
                $wpdb->prefix . 'knot4_email_log',
                array(
                    'template_key' => $template_key,
                    'to_email' => $to_email,
                    'subject' => $subject,
                    'status' => $sent ? 'sent' : 'failed',
                    'sent_at' => current_time('mysql'),
                ),
                array('%s', '%s', '%s', '%s', '%s')
            );
        }
    }
    
    /**
     * Get email templates
     */
    public static function get_templates() {
        return self::$templates;
    }
    
    /**
     * Default email templates
     */
    private static function get_default_donation_receipt_template() {
        return 'Dear {donor_first_name},

Thank you so much for your generous donation of {donation_amount}!

Your support means the world to us and helps us continue our important work. Here are the details of your donation:

<div class="donation-details">
<strong>Donation Details:</strong><br>
Amount: {donation_amount}<br>
Date: {donation_date}<br>
Donation ID: {donation_id}
</div>

Your donation is tax-deductible. Our tax ID number is: {tax_id}

<div class="text-center">
<a href="{receipt_url}" class="button">Download Receipt</a>
</div>

Thank you again for your support!

With gratitude,<br>
The {organization_name} Team';
    }
    
    private static function get_default_event_confirmation_template() {
        return 'Hi {attendee_first_name},

Thank you for registering for {event_title}!

We\'re excited to see you there. Here are the event details:

<div class="donation-details">
<strong>Event Details:</strong><br>
Event: {event_title}<br>
Date: {event_date}<br>
Time: {event_time}<br>
Location: {event_location}<br>
Registration ID: {registration_id}
</div>

<div class="text-center">
<a href="{event_url}" class="button">View Event Details</a>
</div>

If you have any questions, please don\'t hesitate to contact us.

See you there!<br>
The {organization_name} Team';
    }
    
    private static function get_default_volunteer_confirmation_template() {
        return 'Dear {volunteer_first_name},

Thank you for your interest in volunteering with {organization_name}!

We have received your volunteer application and are excited about your interest in the following areas:
{volunteer_interests}

Our volunteer coordinator will review your application and contact you within the next few days to discuss opportunities that match your interests and availability.

Thank you for wanting to make a difference in our community!

Best regards,<br>
The {organization_name} Team';
    }
    
    private static function get_default_newsletter_welcome_template() {
        return 'Welcome to our newsletter!

Thank you for subscribing to updates from {organization_name}.

You\'ll receive regular updates about our programs, events, and impact in the community. We promise to respect your inbox and only send you meaningful content.

<div class="text-center">
<a href="{unsubscribe_url}" style="font-size: 12px; color: #666;">Unsubscribe</a>
</div>

Best regards,<br>
The {organization_name} Team';
    }
    
    private static function get_default_donation_reminder_template() {
        return 'Dear {contact_name},

We hope this message finds you well!

Your previous support of {last_donation_amount} on {last_donation_date} made a real difference in our mission. As we continue our important work, we\'re reaching out to see if you might consider supporting us again.

Every contribution, no matter the size, helps us create positive change in our community.

<div class="text-center">
<a href="{donation_url}" class="button">Make a Donation</a>
</div>

Thank you for considering another gift to {organization_name}.

With gratitude,<br>
The {organization_name} Team';
    }
    
    private static function get_default_admin_donation_notification_template() {
        return 'A new donation has been received!

<div class="donation-details">
<strong>Donation Details:</strong><br>
Donor: {donor_name} ({donor_email})<br>
Amount: {donation_amount}<br>
Date: {donation_date}<br>
Donation ID: {donation_id}
</div>

<div class="text-center">
<a href="{admin_url}" class="button">View in Dashboard</a>
</div>';
    }
    
    /**
     * Helper methods for automation
     */
    private static function send_birthday_emails() {
        // Implementation for birthday emails
    }
    
    private static function send_donation_anniversary_emails() {
        // Implementation for donation anniversary emails
    }
    
    private static function process_scheduled_campaigns() {
        // Implementation for scheduled email campaigns
    }
    
    private static function send_donation_reminders() {
        // Implementation for donation reminder emails
    }
    
    private static function send_volunteer_followups() {
        // Implementation for volunteer follow-up emails
    }
    
    private static function process_newsletter_automation() {
        // Implementation for newsletter automation
    }
}