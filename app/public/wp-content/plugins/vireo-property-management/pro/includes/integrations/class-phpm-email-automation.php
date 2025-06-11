<?php
/**
 * Email Automation Integration for Pro Version
 */

if (!defined('ABSPATH')) {
    exit;
}

class PHPM_Email_Automation {
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Only load if license is valid
        if (!PHPM_License_Manager::is_valid()) {
            return;
        }
        
        // Email automation triggers
        add_action('phpm_new_tenant_signup', array($this, 'send_welcome_email'));
        add_action('phpm_lease_expiring_soon', array($this, 'send_lease_renewal_email'));
        add_action('phpm_maintenance_request_created', array($this, 'send_maintenance_notification'));
        add_action('phpm_maintenance_request_completed', array($this, 'send_maintenance_completion_email'));
        
        // Admin settings
        add_action('admin_menu', array($this, 'add_email_settings_page'));
        add_action('admin_init', array($this, 'register_email_settings'));
        
        // Email template management
        add_action('wp_ajax_phpm_save_email_template', array($this, 'save_email_template'));
        add_action('wp_ajax_phpm_preview_email_template', array($this, 'preview_email_template'));
    }
    
    /**
     * Send welcome email to new tenants
     */
    public function send_welcome_email($tenant_data) {
        $template = $this->get_email_template('welcome_tenant');
        $subject = $this->process_template_variables($template['subject'], $tenant_data);
        $message = $this->process_template_variables($template['message'], $tenant_data);
        
        $this->send_email($tenant_data['email'], $subject, $message, 'welcome_tenant');
    }
    
    /**
     * Send lease renewal email
     */
    public function send_lease_renewal_email($lease_data) {
        $template = $this->get_email_template('lease_renewal');
        $subject = $this->process_template_variables($template['subject'], $lease_data);
        $message = $this->process_template_variables($template['message'], $lease_data);
        
        $this->send_email($lease_data['tenant_email'], $subject, $message, 'lease_renewal');
    }
    
    /**
     * Send maintenance request notification
     */
    public function send_maintenance_notification($request_data) {
        // Notify property manager
        $manager_template = $this->get_email_template('maintenance_request_manager');
        $manager_subject = $this->process_template_variables($manager_template['subject'], $request_data);
        $manager_message = $this->process_template_variables($manager_template['message'], $request_data);
        
        $manager_email = get_option('phpm_manager_email', get_option('admin_email'));
        $this->send_email($manager_email, $manager_subject, $manager_message, 'maintenance_request_manager');
        
        // Notify tenant (confirmation)
        $tenant_template = $this->get_email_template('maintenance_request_tenant');
        $tenant_subject = $this->process_template_variables($tenant_template['subject'], $request_data);
        $tenant_message = $this->process_template_variables($tenant_template['message'], $request_data);
        
        $this->send_email($request_data['tenant_email'], $tenant_subject, $tenant_message, 'maintenance_request_tenant');
    }
    
    /**
     * Send maintenance completion email
     */
    public function send_maintenance_completion_email($request_data) {
        $template = $this->get_email_template('maintenance_completed');
        $subject = $this->process_template_variables($template['subject'], $request_data);
        $message = $this->process_template_variables($template['message'], $request_data);
        
        $this->send_email($request_data['tenant_email'], $subject, $message, 'maintenance_completed');
    }
    
    /**
     * Get email template
     */
    private function get_email_template($template_name) {
        $templates = get_option('phpm_email_templates', $this->get_default_templates());
        
        if (isset($templates[$template_name])) {
            return $templates[$template_name];
        }
        
        return $this->get_default_template($template_name);
    }
    
    /**
     * Get default email templates
     */
    private function get_default_templates() {
        return array(
            'welcome_tenant' => array(
                'subject' => __('Welcome to {{property_name}}!', 'plughaus-property'),
                'message' => __("Dear {{tenant_name}},\n\nWelcome to your new home at {{property_address}}!\n\nYour lease begins on {{lease_start_date}} and your monthly rent is ${{rent_amount}}.\n\nIf you have any questions, please don't hesitate to contact us.\n\nBest regards,\n{{property_manager_name}}", 'plughaus-property'),
                'enabled' => true
            ),
            'lease_renewal' => array(
                'subject' => __('Lease Renewal Notice - {{property_address}}', 'plughaus-property'),
                'message' => __("Dear {{tenant_name}},\n\nYour lease at {{property_address}} is set to expire on {{lease_end_date}}.\n\nWe would like to offer you a lease renewal. Please contact us to discuss terms.\n\nBest regards,\n{{property_manager_name}}", 'plughaus-property'),
                'enabled' => true
            ),
            'maintenance_request_manager' => array(
                'subject' => __('New Maintenance Request - {{property_address}}', 'plughaus-property'),
                'message' => __("A new maintenance request has been submitted:\n\nProperty: {{property_address}}\nUnit: {{unit_number}}\nTenant: {{tenant_name}}\nDescription: {{request_description}}\nPriority: {{request_priority}}\n\nPlease review and assign to appropriate maintenance staff.", 'plughaus-property'),
                'enabled' => true
            ),
            'maintenance_request_tenant' => array(
                'subject' => __('Maintenance Request Received - {{property_address}}', 'plughaus-property'),
                'message' => __("Dear {{tenant_name}},\n\nWe have received your maintenance request for {{property_address}}, Unit {{unit_number}}.\n\nRequest: {{request_description}}\nRequest ID: {{request_id}}\n\nWe will contact you soon to schedule the repair.\n\nThank you,\n{{property_manager_name}}", 'plughaus-property'),
                'enabled' => true
            ),
            'maintenance_completed' => array(
                'subject' => __('Maintenance Request Completed - {{property_address}}', 'plughaus-property'),
                'message' => __("Dear {{tenant_name}},\n\nThe maintenance request for {{property_address}}, Unit {{unit_number}} has been completed.\n\nWork Performed: {{work_completed}}\nCompletion Date: {{completion_date}}\n\nIf you have any concerns, please contact us.\n\nThank you,\n{{property_manager_name}}", 'plughaus-property'),
                'enabled' => true
            )
        );
    }
    
    /**
     * Process template variables
     */
    private function process_template_variables($template, $data) {
        $variables = array(
            '{{tenant_name}}' => isset($data['tenant_name']) ? $data['tenant_name'] : '',
            '{{property_name}}' => isset($data['property_name']) ? $data['property_name'] : '',
            '{{property_address}}' => isset($data['property_address']) ? $data['property_address'] : '',
            '{{unit_number}}' => isset($data['unit_number']) ? $data['unit_number'] : '',
            '{{rent_amount}}' => isset($data['rent_amount']) ? number_format($data['rent_amount'], 2) : '',
            '{{lease_start_date}}' => isset($data['lease_start_date']) ? date('F j, Y', strtotime($data['lease_start_date'])) : '',
            '{{lease_end_date}}' => isset($data['lease_end_date']) ? date('F j, Y', strtotime($data['lease_end_date'])) : '',
            '{{property_manager_name}}' => get_option('phpm_manager_name', get_bloginfo('name')),
            '{{request_description}}' => isset($data['request_description']) ? $data['request_description'] : '',
            '{{request_priority}}' => isset($data['request_priority']) ? $data['request_priority'] : '',
            '{{request_id}}' => isset($data['request_id']) ? $data['request_id'] : '',
            '{{work_completed}}' => isset($data['work_completed']) ? $data['work_completed'] : '',
            '{{completion_date}}' => isset($data['completion_date']) ? date('F j, Y', strtotime($data['completion_date'])) : date('F j, Y'),
        );
        
        return str_replace(array_keys($variables), array_values($variables), $template);
    }
    
    /**
     * Send email
     */
    private function send_email($to, $subject, $message, $template_type = '') {
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_option('phpm_manager_name', get_bloginfo('name')) . ' <' . get_option('admin_email') . '>'
        );
        
        // Convert line breaks to HTML
        $html_message = nl2br($message);
        
        // Wrap in basic HTML template
        $html_message = $this->wrap_in_html_template($html_message, $subject);
        
        $sent = wp_mail($to, $subject, $html_message, $headers);
        
        // Log email sending
        $this->log_email_sent($to, $subject, $template_type, $sent);
        
        return $sent;
    }
    
    /**
     * Wrap email content in HTML template
     */
    private function wrap_in_html_template($content, $subject) {
        $template = get_option('phpm_email_html_template', $this->get_default_html_template());
        
        $variables = array(
            '{{email_subject}}' => $subject,
            '{{email_content}}' => $content,
            '{{site_name}}' => get_bloginfo('name'),
            '{{site_url}}' => home_url(),
            '{{year}}' => date('Y')
        );
        
        return str_replace(array_keys($variables), array_values($variables), $template);
    }
    
    /**
     * Get default HTML email template
     */
    private function get_default_html_template() {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>{{email_subject}}</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .email-container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .email-header { background: #f8f9fa; padding: 20px; border-bottom: 2px solid #007cba; }
                .email-content { padding: 20px; }
                .email-footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class="email-container">
                <div class="email-header">
                    <h2>{{site_name}}</h2>
                </div>
                <div class="email-content">
                    {{email_content}}
                </div>
                <div class="email-footer">
                    <p>&copy; {{year}} {{site_name}}. All rights reserved.</p>
                    <p><a href="{{site_url}}">{{site_url}}</a></p>
                </div>
            </div>
        </body>
        </html>';
    }
    
    /**
     * Add email settings page
     */
    public function add_email_settings_page() {
        add_submenu_page(
            'phpm-settings',
            __('Email Automation', 'plughaus-property'),
            __('Email Automation', 'plughaus-property'),
            'manage_options',
            'phpm-email-automation',
            array($this, 'render_email_settings_page')
        );
    }
    
    /**
     * Register email settings
     */
    public function register_email_settings() {
        // Email automation section
        add_settings_section(
            'phpm_email_automation_section',
            __('Email Automation Settings', 'plughaus-property'),
            array($this, 'email_automation_section_callback'),
            'phpm_email_automation'
        );
        
        // Manager information
        add_settings_field(
            'phpm_manager_name',
            __('Property Manager Name', 'plughaus-property'),
            array($this, 'text_field_callback'),
            'phpm_email_automation',
            'phpm_email_automation_section',
            array(
                'option_name' => 'phpm_manager_name',
                'description' => __('Name used in email signatures.', 'plughaus-property')
            )
        );
        
        add_settings_field(
            'phpm_manager_email',
            __('Property Manager Email', 'plughaus-property'),
            array($this, 'email_field_callback'),
            'phpm_email_automation',
            'phpm_email_automation_section',
            array(
                'option_name' => 'phpm_manager_email',
                'description' => __('Email address for receiving notifications.', 'plughaus-property')
            )
        );
        
        // Register settings
        register_setting('phpm_email_automation_group', 'phpm_manager_name');
        register_setting('phpm_email_automation_group', 'phpm_manager_email');
        register_setting('phpm_email_automation_group', 'phpm_email_templates');
    }
    
    /**
     * Render email settings page
     */
    public function render_email_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Email Automation Settings', 'plughaus-property'); ?></h1>
            
            <div class="phpm-email-automation-tabs">
                <nav class="nav-tab-wrapper">
                    <a href="#general" class="nav-tab nav-tab-active"><?php _e('General Settings', 'plughaus-property'); ?></a>
                    <a href="#templates" class="nav-tab"><?php _e('Email Templates', 'plughaus-property'); ?></a>
                    <a href="#logs" class="nav-tab"><?php _e('Email Logs', 'plughaus-property'); ?></a>
                </nav>
                
                <div id="general" class="tab-content active">
                    <form method="post" action="options.php">
                        <?php
                        settings_fields('phpm_email_automation_group');
                        do_settings_sections('phpm_email_automation');
                        submit_button();
                        ?>
                    </form>
                </div>
                
                <div id="templates" class="tab-content">
                    <?php $this->render_email_templates_tab(); ?>
                </div>
                
                <div id="logs" class="tab-content">
                    <?php $this->render_email_logs_tab(); ?>
                </div>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('.nav-tab').on('click', function(e) {
                e.preventDefault();
                var target = $(this).attr('href');
                
                $('.nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');
                
                $('.tab-content').removeClass('active');
                $(target).addClass('active');
            });
        });
        </script>
        <?php
    }
    
    /**
     * Render email templates tab
     */
    private function render_email_templates_tab() {
        $templates = get_option('phpm_email_templates', $this->get_default_templates());
        
        ?>
        <div class="phpm-email-templates">
            <h2><?php _e('Email Templates', 'plughaus-property'); ?></h2>
            
            <?php foreach ($templates as $template_name => $template_data): ?>
            <div class="phpm-email-template-card">
                <h3><?php echo esc_html(ucwords(str_replace('_', ' ', $template_name))); ?></h3>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Enabled', 'plughaus-property'); ?></th>
                        <td>
                            <input type="checkbox" 
                                   name="phpm_email_templates[<?php echo $template_name; ?>][enabled]" 
                                   value="1" 
                                   <?php checked($template_data['enabled'], true); ?> />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Subject', 'plughaus-property'); ?></th>
                        <td>
                            <input type="text" 
                                   name="phpm_email_templates[<?php echo $template_name; ?>][subject]" 
                                   value="<?php echo esc_attr($template_data['subject']); ?>" 
                                   class="large-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Message', 'plughaus-property'); ?></th>
                        <td>
                            <textarea name="phpm_email_templates[<?php echo $template_name; ?>][message]" 
                                      class="large-text" 
                                      rows="8"><?php echo esc_textarea($template_data['message']); ?></textarea>
                            <p class="description">
                                <?php _e('Available variables: {{tenant_name}}, {{property_address}}, {{unit_number}}, {{rent_amount}}, {{lease_start_date}}, {{lease_end_date}}, {{property_manager_name}}', 'plughaus-property'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <button type="button" class="button" onclick="previewEmailTemplate('<?php echo $template_name; ?>')">
                        <?php _e('Preview', 'plughaus-property'); ?>
                    </button>
                </p>
            </div>
            <?php endforeach; ?>
            
            <p class="submit">
                <input type="submit" class="button button-primary" value="<?php _e('Save All Templates', 'plughaus-property'); ?>">
            </p>
        </div>
        <?php
    }
    
    /**
     * Render email logs tab
     */
    private function render_email_logs_tab() {
        ?>
        <div class="phpm-email-logs">
            <h2><?php _e('Email Logs', 'plughaus-property'); ?></h2>
            
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th><?php _e('Date', 'plughaus-property'); ?></th>
                        <th><?php _e('Recipient', 'plughaus-property'); ?></th>
                        <th><?php _e('Subject', 'plughaus-property'); ?></th>
                        <th><?php _e('Template', 'plughaus-property'); ?></th>
                        <th><?php _e('Status', 'plughaus-property'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $logs = $this->get_email_logs(50);
                    if (empty($logs)):
                    ?>
                    <tr>
                        <td colspan="5"><?php _e('No email logs found.', 'plughaus-property'); ?></td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?php echo esc_html($log['date']); ?></td>
                            <td><?php echo esc_html($log['recipient']); ?></td>
                            <td><?php echo esc_html($log['subject']); ?></td>
                            <td><?php echo esc_html($log['template']); ?></td>
                            <td>
                                <?php if ($log['status']): ?>
                                    <span class="phpm-status-success"><?php _e('Sent', 'plughaus-property'); ?></span>
                                <?php else: ?>
                                    <span class="phpm-status-error"><?php _e('Failed', 'plughaus-property'); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
    
    /**
     * Helper methods
     */
    private function get_default_template($template_name) {
        $defaults = $this->get_default_templates();
        return isset($defaults[$template_name]) ? $defaults[$template_name] : array();
    }
    
    private function log_email_sent($to, $subject, $template_type, $status) {
        // Log email sending for audit trail
        // This would insert into an email_logs table
    }
    
    private function get_email_logs($limit = 50) {
        // Return sample logs for demo
        return array(
            array(
                'date' => date('Y-m-d H:i:s'),
                'recipient' => 'tenant@example.com',
                'subject' => 'Welcome to 123 Main St!',
                'template' => 'welcome_tenant',
                'status' => true
            )
        );
    }
    
    /**
     * Settings field callbacks
     */
    public function email_automation_section_callback() {
        echo '<p>' . __('Configure automated email notifications and templates.', 'plughaus-property') . '</p>';
    }
    
    public function text_field_callback($args) {
        $option_name = $args['option_name'];
        $value = get_option($option_name, '');
        $description = isset($args['description']) ? $args['description'] : '';
        
        echo '<input type="text" id="' . $option_name . '" name="' . $option_name . '" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . $description . '</p>';
    }
    
    public function email_field_callback($args) {
        $option_name = $args['option_name'];
        $value = get_option($option_name, get_option('admin_email'));
        $description = isset($args['description']) ? $args['description'] : '';
        
        echo '<input type="email" id="' . $option_name . '" name="' . $option_name . '" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . $description . '</p>';
    }
}