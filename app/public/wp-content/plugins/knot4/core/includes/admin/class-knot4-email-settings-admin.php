<?php
/**
 * Email Settings Admin for Knot4
 * 
 * @package Knot4
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Knot4_Email_Settings_Admin {
    
    /**
     * Initialize email settings admin
     */
    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_admin_menu'));
        add_action('admin_init', array(__CLASS__, 'register_settings'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_assets'));
        add_action('wp_ajax_knot4_get_template_data', array(__CLASS__, 'get_template_data_ajax'));
        add_action('wp_ajax_knot4_preview_email', array(__CLASS__, 'preview_email_ajax'));
        add_action('wp_ajax_knot4_test_email', array(__CLASS__, 'test_email_ajax'));
        add_action('wp_ajax_knot4_reset_template', array(__CLASS__, 'reset_template_ajax'));
    }
    
    /**
     * Add admin menu
     */
    public static function add_admin_menu() {
        add_submenu_page(
            'knot4-dashboard',
            __('Email Templates', 'knot4'),
            __('Email Templates', 'knot4'),
            'manage_knot4_nonprofit',
            'knot4-email-templates',
            array(__CLASS__, 'render_email_templates_page')
        );
    }
    
    /**
     * Register settings
     */
    public static function register_settings() {
        register_setting('knot4_email_templates_group', 'knot4_email_templates', array(
            'sanitize_callback' => array(__CLASS__, 'sanitize_email_templates'),
        ));
        
        register_setting('knot4_email_automation_group', 'knot4_email_automation', array(
            'sanitize_callback' => array(__CLASS__, 'sanitize_email_automation'),
        ));
    }
    
    /**
     * Enqueue admin assets
     */
    public static function enqueue_assets($hook) {
        if ($hook !== 'knot4_page_knot4-email-templates') {
            return;
        }
        
        wp_enqueue_style(
            'knot4-email-settings-admin',
            KNOT4_PLUGIN_URL . 'core/assets/css/email-settings-admin.css',
            array(),
            KNOT4_VERSION
        );
        
        wp_enqueue_script(
            'knot4-email-settings-admin',
            KNOT4_PLUGIN_URL . 'core/assets/js/email-settings-admin.js',
            array('jquery', 'wp-color-picker'),
            KNOT4_VERSION,
            true
        );
        
        wp_localize_script('knot4-email-settings-admin', 'knot4EmailSettings', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('knot4_email_admin_nonce'),
            'isPro' => Knot4_Utilities::is_pro(),
            'strings' => array(
                'previewing' => __('Generating Preview...', 'knot4'),
                'sendingTest' => __('Sending Test Email...', 'knot4'),
                'testSent' => __('Test email sent successfully!', 'knot4'),
                'testFailed' => __('Failed to send test email.', 'knot4'),
                'resetting' => __('Resetting...', 'knot4'),
                'resetSuccess' => __('Template reset to default.', 'knot4'),
                'resetFailed' => __('Failed to reset template.', 'knot4'),
                'confirmReset' => __('Are you sure you want to reset this template to default?', 'knot4'),
                'validationError' => __('Please correct the errors below.', 'knot4'),
                'ajaxError' => __('An error occurred. Please try again.', 'knot4'),
                'previewError' => __('Failed to generate preview.', 'knot4'),
                'invalidEmail' => __('Please enter a valid email address.', 'knot4'),
                'noVariables' => __('No variables available for this template.', 'knot4'),
                'subjectRequired' => __('Subject line is required.', 'knot4'),
                'contentRequired' => __('Email content is required.', 'knot4'),
                'autoSaved' => __('Template auto-saved.', 'knot4'),
            )
        ));
        
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_editor();
    }
    
    /**
     * Render email templates page
     */
    public static function render_email_templates_page() {
        $templates = Knot4_Email_Manager::get_templates();
        $saved_templates = get_option('knot4_email_templates', array());
        $automation_settings = get_option('knot4_email_automation', array());
        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'templates';
        
        ?>
        <div class="wrap knot4-email-settings">
            <h1 class="knot4-page-title">
                <span class="dashicons dashicons-email-alt"></span>
                <?php _e('Email Templates & Automation', 'knot4'); ?>
            </h1>
            
            <nav class="nav-tab-wrapper knot4-email-tabs">
                <a href="?page=knot4-email-templates&tab=templates" 
                   class="nav-tab <?php echo $active_tab === 'templates' ? 'nav-tab-active' : ''; ?>">
                    <?php _e('Templates', 'knot4'); ?>
                </a>
                <a href="?page=knot4-email-templates&tab=automation" 
                   class="nav-tab <?php echo $active_tab === 'automation' ? 'nav-tab-active' : ''; ?>">
                    <?php _e('Automation', 'knot4'); ?>
                </a>
                <a href="?page=knot4-email-templates&tab=logs" 
                   class="nav-tab <?php echo $active_tab === 'logs' ? 'nav-tab-active' : ''; ?>">
                    <?php _e('Email Logs', 'knot4'); ?>
                    <?php if (Knot4_Utilities::is_pro()): ?>
                    <span class="knot4-pro-badge"><?php _e('Pro', 'knot4'); ?></span>
                    <?php endif; ?>
                </a>
            </nav>
            
            <div class="knot4-tab-content">
                <?php if ($active_tab === 'templates'): ?>
                    <?php self::render_templates_tab($templates, $saved_templates); ?>
                <?php elseif ($active_tab === 'automation'): ?>
                    <?php self::render_automation_tab($automation_settings); ?>
                <?php elseif ($active_tab === 'logs'): ?>
                    <?php self::render_logs_tab(); ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render templates tab
     */
    private static function render_templates_tab($templates, $saved_templates) {
        ?>
        <div class="knot4-templates-container">
            <div class="knot4-templates-sidebar">
                <h3><?php _e('Email Templates', 'knot4'); ?></h3>
                <ul class="knot4-template-list">
                    <?php foreach ($templates as $key => $template): ?>
                    <li>
                        <button type="button" class="knot4-template-btn" data-template="<?php echo esc_attr($key); ?>">
                            <strong><?php echo esc_html($template['name']); ?></strong>
                            <span><?php echo esc_html($template['description']); ?></span>
                            <?php if (isset($saved_templates[$key])): ?>
                            <span class="knot4-modified-indicator"><?php _e('Modified', 'knot4'); ?></span>
                            <?php endif; ?>
                        </button>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="knot4-template-editor">
                <div class="knot4-editor-header">
                    <h3 class="knot4-template-title"><?php _e('Select a template to edit', 'knot4'); ?></h3>
                    <div class="knot4-editor-actions">
                        <button type="button" class="button knot4-preview-btn" disabled>
                            <?php _e('Preview', 'knot4'); ?>
                        </button>
                        <button type="button" class="button knot4-test-btn" disabled>
                            <?php _e('Send Test', 'knot4'); ?>
                        </button>
                        <button type="button" class="button knot4-reset-btn" disabled>
                            <?php _e('Reset to Default', 'knot4'); ?>
                        </button>
                    </div>
                </div>
                
                <form id="knot4-template-form" method="post" action="options.php" style="display: none;">
                    <?php settings_fields('knot4_email_templates_group'); ?>
                    <input type="hidden" id="current-template-key" name="template_key" value="">
                    
                    <div class="knot4-template-fields">
                        <div class="knot4-field-group">
                            <label for="template-subject"><?php _e('Subject Line', 'knot4'); ?></label>
                            <input type="text" id="template-subject" name="knot4_email_templates[subject]" class="large-text">
                            <p class="description"><?php _e('You can use variables like {donor_name}, {organization_name}, etc.', 'knot4'); ?></p>
                        </div>
                        
                        <div class="knot4-field-group">
                            <label for="template-content"><?php _e('Email Content', 'knot4'); ?></label>
                            <?php 
                            wp_editor('', 'template-content', array(
                                'textarea_name' => 'knot4_email_templates[content]',
                                'textarea_rows' => 15,
                                'media_buttons' => false,
                                'teeny' => true,
                                'quicktags' => true,
                            )); 
                            ?>
                        </div>
                        
                        <div class="knot4-available-variables">
                            <h4><?php _e('Available Variables', 'knot4'); ?></h4>
                            <div class="knot4-variables-list"></div>
                        </div>
                    </div>
                    
                    <div class="knot4-form-actions">
                        <?php submit_button(__('Save Template', 'knot4'), 'primary', 'submit', false); ?>
                    </div>
                </form>
                
                <div class="knot4-template-placeholder">
                    <div class="knot4-placeholder-content">
                        <span class="dashicons dashicons-email-alt"></span>
                        <h3><?php _e('Email Template Editor', 'knot4'); ?></h3>
                        <p><?php _e('Select a template from the list to customize its subject line and content.', 'knot4'); ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Preview Modal -->
        <div id="knot4-preview-modal" class="knot4-modal" style="display: none;">
            <div class="knot4-modal-content">
                <div class="knot4-modal-header">
                    <h3><?php _e('Email Preview', 'knot4'); ?></h3>
                    <button type="button" class="knot4-modal-close">&times;</button>
                </div>
                <div class="knot4-modal-body">
                    <div class="knot4-preview-content">
                        <div class="knot4-loading"><?php _e('Loading preview...', 'knot4'); ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Test Email Modal -->
        <div id="knot4-test-modal" class="knot4-modal" style="display: none;">
            <div class="knot4-modal-content">
                <div class="knot4-modal-header">
                    <h3><?php _e('Send Test Email', 'knot4'); ?></h3>
                    <button type="button" class="knot4-modal-close">&times;</button>
                </div>
                <div class="knot4-modal-body">
                    <form id="knot4-test-email-form">
                        <div class="knot4-field-group">
                            <label for="test-email-address"><?php _e('Send test email to:', 'knot4'); ?></label>
                            <input type="email" id="test-email-address" value="<?php echo esc_attr(get_option('admin_email')); ?>" class="regular-text" required>
                        </div>
                        <div class="knot4-form-actions">
                            <button type="submit" class="button button-primary"><?php _e('Send Test Email', 'knot4'); ?></button>
                            <button type="button" class="button knot4-modal-close"><?php _e('Cancel', 'knot4'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render automation tab
     */
    private static function render_automation_tab($settings) {
        ?>
        <div class="knot4-automation-settings">
            <form method="post" action="options.php">
                <?php settings_fields('knot4_email_automation_group'); ?>
                
                <div class="knot4-settings-section">
                    <h3><?php _e('Automated Email Settings', 'knot4'); ?></h3>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php _e('Send Donation Receipts', 'knot4'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="knot4_email_automation[send_donation_receipts]" 
                                           value="1" <?php checked(isset($settings['send_donation_receipts']) ? $settings['send_donation_receipts'] : 1); ?>>
                                    <?php _e('Automatically send donation receipts to donors', 'knot4'); ?>
                                </label>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><?php _e('Send Event Confirmations', 'knot4'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="knot4_email_automation[send_event_confirmations]" 
                                           value="1" <?php checked(isset($settings['send_event_confirmations']) ? $settings['send_event_confirmations'] : 1); ?>>
                                    <?php _e('Automatically send event registration confirmations', 'knot4'); ?>
                                </label>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><?php _e('Send Volunteer Confirmations', 'knot4'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="knot4_email_automation[send_volunteer_confirmations]" 
                                           value="1" <?php checked(isset($settings['send_volunteer_confirmations']) ? $settings['send_volunteer_confirmations'] : 1); ?>>
                                    <?php _e('Automatically send volunteer application confirmations', 'knot4'); ?>
                                </label>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><?php _e('Send Newsletter Welcome', 'knot4'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="knot4_email_automation[send_newsletter_welcome]" 
                                           value="1" <?php checked(isset($settings['send_newsletter_welcome']) ? $settings['send_newsletter_welcome'] : 1); ?>>
                                    <?php _e('Automatically send welcome emails to newsletter subscribers', 'knot4'); ?>
                                </label>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><?php _e('Admin Notifications', 'knot4'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="knot4_email_automation[admin_donation_notifications]" 
                                           value="1" <?php checked(isset($settings['admin_donation_notifications']) ? $settings['admin_donation_notifications'] : 1); ?>>
                                    <?php _e('Send admin notifications for new donations', 'knot4'); ?>
                                </label>
                                <br>
                                <label>
                                    <input type="checkbox" name="knot4_email_automation[admin_volunteer_notifications]" 
                                           value="1" <?php checked(isset($settings['admin_volunteer_notifications']) ? $settings['admin_volunteer_notifications'] : 1); ?>>
                                    <?php _e('Send admin notifications for volunteer applications', 'knot4'); ?>
                                </label>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <?php if (Knot4_Utilities::is_pro()): ?>
                <div class="knot4-settings-section">
                    <h3><?php _e('Advanced Automation', 'knot4'); ?> <span class="knot4-pro-badge"><?php _e('Pro', 'knot4'); ?></span></h3>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php _e('Donation Reminders', 'knot4'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="knot4_email_automation[donation_reminders]" 
                                           value="1" <?php checked(isset($settings['donation_reminders']) ? $settings['donation_reminders'] : 0); ?>>
                                    <?php _e('Send donation reminders to past donors', 'knot4'); ?>
                                </label>
                                <p class="description">
                                    <?php _e('Send after:', 'knot4'); ?>
                                    <input type="number" name="knot4_email_automation[reminder_days]" 
                                           value="<?php echo isset($settings['reminder_days']) ? intval($settings['reminder_days']) : 90; ?>" 
                                           min="30" max="365" style="width: 60px;">
                                    <?php _e('days since last donation', 'knot4'); ?>
                                </p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><?php _e('Birthday Emails', 'knot4'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="knot4_email_automation[birthday_emails]" 
                                           value="1" <?php checked(isset($settings['birthday_emails']) ? $settings['birthday_emails'] : 0); ?>>
                                    <?php _e('Send birthday emails to donors', 'knot4'); ?>
                                </label>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><?php _e('Donation Anniversary', 'knot4'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="knot4_email_automation[donation_anniversary]" 
                                           value="1" <?php checked(isset($settings['donation_anniversary']) ? $settings['donation_anniversary'] : 0); ?>>
                                    <?php _e('Send emails on donation anniversary dates', 'knot4'); ?>
                                </label>
                            </td>
                        </tr>
                    </table>
                </div>
                <?php else: ?>
                <div class="knot4-pro-upgrade-notice">
                    <h3><?php _e('Advanced Email Automation', 'knot4'); ?></h3>
                    <p><?php _e('Upgrade to Pro to unlock advanced email automation features including donation reminders, birthday emails, and anniversary campaigns.', 'knot4'); ?></p>
                    <a href="<?php echo admin_url('admin.php?page=knot4-upgrade'); ?>" class="button button-primary">
                        <?php _e('Upgrade to Pro', 'knot4'); ?>
                    </a>
                </div>
                <?php endif; ?>
                
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
    
    /**
     * Render logs tab
     */
    private static function render_logs_tab() {
        if (!Knot4_Utilities::is_pro()) {
            ?>
            <div class="knot4-pro-upgrade-notice">
                <h3><?php _e('Email Logs', 'knot4'); ?></h3>
                <p><?php _e('Email logging is a Pro feature. Upgrade to track all email activity including delivery status and engagement metrics.', 'knot4'); ?></p>
                <a href="<?php echo admin_url('admin.php?page=knot4-upgrade'); ?>" class="button button-primary">
                    <?php _e('Upgrade to Pro', 'knot4'); ?>
                </a>
            </div>
            <?php
            return;
        }
        
        // Pro email logs implementation would go here
        ?>
        <div class="knot4-email-logs">
            <h3><?php _e('Email Activity Log', 'knot4'); ?></h3>
            <p><?php _e('Email logging functionality will be implemented in the Pro version.', 'knot4'); ?></p>
        </div>
        <?php
    }
    
    /**
     * AJAX handlers
     */
    public static function get_template_data_ajax() {
        check_ajax_referer('knot4_email_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_knot4_nonprofit')) {
            wp_die(__('You do not have sufficient permissions.', 'knot4'));
        }
        
        $template_key = sanitize_text_field($_POST['template_key']);
        $templates = Knot4_Email_Manager::get_templates();
        
        if (!isset($templates[$template_key])) {
            wp_send_json_error(__('Invalid template.', 'knot4'));
        }
        
        $template = $templates[$template_key];
        $saved_templates = get_option('knot4_email_templates', array());
        
        // Get custom template or use default
        $custom_template = isset($saved_templates[$template_key]) ? 
                          $saved_templates[$template_key] : array();
        
        $subject = isset($custom_template['subject']) ? 
                  $custom_template['subject'] : $template['subject'];
        $content = isset($custom_template['content']) ? 
                  $custom_template['content'] : $template['default_template'];
        
        wp_send_json_success(array(
            'name' => $template['name'],
            'subject' => $subject,
            'content' => $content,
            'variables' => $template['variables']
        ));
    }
    
    public static function preview_email_ajax() {
        check_ajax_referer('knot4_email_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_knot4_nonprofit')) {
            wp_die(__('You do not have sufficient permissions.', 'knot4'));
        }
        
        $template_key = sanitize_text_field($_POST['template_key']);
        $subject = sanitize_text_field($_POST['subject']);
        $content = wp_kses_post($_POST['content']);
        
        // Generate sample variables for preview
        $sample_variables = self::get_sample_variables($template_key);
        
        // Replace variables
        $preview_subject = str_replace(array_keys($sample_variables), array_values($sample_variables), $subject);
        $preview_content = str_replace(array_keys($sample_variables), array_values($sample_variables), $content);
        
        // Wrap in email template
        $html_content = Knot4_Email_Manager::wrap_email_content($preview_content, $preview_subject, $sample_variables);
        
        wp_send_json_success(array(
            'subject' => $preview_subject,
            'content' => $html_content
        ));
    }
    
    public static function test_email_ajax() {
        check_ajax_referer('knot4_email_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_knot4_nonprofit')) {
            wp_die(__('You do not have sufficient permissions.', 'knot4'));
        }
        
        $template_key = sanitize_text_field($_POST['template_key']);
        $test_email = sanitize_email($_POST['test_email']);
        $subject = sanitize_text_field($_POST['subject']);
        $content = wp_kses_post($_POST['content']);
        
        if (!$test_email) {
            wp_send_json_error(__('Invalid email address.', 'knot4'));
        }
        
        // Generate sample variables
        $sample_variables = self::get_sample_variables($template_key);
        
        // Replace variables
        $test_subject = str_replace(array_keys($sample_variables), array_values($sample_variables), $subject);
        $test_content = str_replace(array_keys($sample_variables), array_values($sample_variables), $content);
        
        // Send test email
        $sent = Knot4_Email_Manager::send_template_email($template_key, $test_email, $sample_variables);
        
        if ($sent) {
            wp_send_json_success(__('Test email sent successfully!', 'knot4'));
        } else {
            wp_send_json_error(__('Failed to send test email.', 'knot4'));
        }
    }
    
    public static function reset_template_ajax() {
        check_ajax_referer('knot4_email_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_knot4_nonprofit')) {
            wp_die(__('You do not have sufficient permissions.', 'knot4'));
        }
        
        $template_key = sanitize_text_field($_POST['template_key']);
        $templates = Knot4_Email_Manager::get_templates();
        
        if (!isset($templates[$template_key])) {
            wp_send_json_error(__('Invalid template.', 'knot4'));
        }
        
        // Remove custom template to revert to default
        $saved_templates = get_option('knot4_email_templates', array());
        unset($saved_templates[$template_key]);
        update_option('knot4_email_templates', $saved_templates);
        
        wp_send_json_success(array(
            'subject' => $templates[$template_key]['subject'],
            'content' => $templates[$template_key]['default_template']
        ));
    }
    
    /**
     * Get sample variables for preview
     */
    private static function get_sample_variables($template_key) {
        $org_settings = Knot4_Utilities::get_organization_settings();
        
        $common_vars = array(
            '{organization_name}' => $org_settings['organization_name'] ?: 'Sample Organization',
            '{tax_id}' => $org_settings['tax_id'] ?: '12-3456789',
        );
        
        switch ($template_key) {
            case 'donation_receipt':
                return array_merge($common_vars, array(
                    '{donor_name}' => 'John Smith',
                    '{donor_first_name}' => 'John',
                    '{donor_email}' => 'john@example.com',
                    '{donation_amount}' => '$100.00',
                    '{donation_date}' => date_i18n(get_option('date_format')),
                    '{donation_id}' => 'DON-001',
                    '{receipt_url}' => home_url('/receipt/?id=DON-001'),
                ));
                
            case 'event_confirmation':
                return array_merge($common_vars, array(
                    '{attendee_name}' => 'Jane Doe',
                    '{attendee_first_name}' => 'Jane',
                    '{attendee_email}' => 'jane@example.com',
                    '{event_title}' => 'Annual Fundraising Gala',
                    '{event_date}' => date_i18n(get_option('date_format'), strtotime('+30 days')),
                    '{event_time}' => '7:00 PM',
                    '{event_location}' => 'Community Center',
                    '{event_url}' => home_url('/events/annual-gala'),
                    '{registration_id}' => 'REG-001',
                ));
                
            case 'volunteer_confirmation':
                return array_merge($common_vars, array(
                    '{volunteer_name}' => 'Mike Johnson',
                    '{volunteer_first_name}' => 'Mike',
                    '{volunteer_email}' => 'mike@example.com',
                    '{volunteer_interests}' => 'Event Support, Marketing',
                ));
                
            case 'newsletter_welcome':
                return array_merge($common_vars, array(
                    '{subscriber_name}' => 'Sarah Wilson',
                    '{subscriber_email}' => 'sarah@example.com',
                    '{unsubscribe_url}' => home_url('/unsubscribe/?email=sarah@example.com'),
                ));
                
            case 'donation_reminder':
                return array_merge($common_vars, array(
                    '{contact_name}' => 'Robert Brown',
                    '{contact_email}' => 'robert@example.com',
                    '{last_donation_amount}' => '$50.00',
                    '{last_donation_date}' => date_i18n(get_option('date_format'), strtotime('-90 days')),
                    '{donation_url}' => home_url('/donate'),
                ));
                
            default:
                return $common_vars;
        }
    }
    
    /**
     * Sanitization functions
     */
    public static function sanitize_email_templates($input) {
        $sanitized = array();
        
        if (isset($input['subject'])) {
            $sanitized['subject'] = sanitize_text_field($input['subject']);
        }
        
        if (isset($input['content'])) {
            $sanitized['content'] = wp_kses_post($input['content']);
        }
        
        return $sanitized;
    }
    
    public static function sanitize_email_automation($input) {
        $sanitized = array();
        
        $boolean_fields = array(
            'send_donation_receipts',
            'send_event_confirmations', 
            'send_volunteer_confirmations',
            'send_newsletter_welcome',
            'admin_donation_notifications',
            'admin_volunteer_notifications',
            'donation_reminders',
            'birthday_emails',
            'donation_anniversary'
        );
        
        foreach ($boolean_fields as $field) {
            $sanitized[$field] = isset($input[$field]) ? 1 : 0;
        }
        
        if (isset($input['reminder_days'])) {
            $sanitized['reminder_days'] = max(30, min(365, intval($input['reminder_days'])));
        }
        
        return $sanitized;
    }
}