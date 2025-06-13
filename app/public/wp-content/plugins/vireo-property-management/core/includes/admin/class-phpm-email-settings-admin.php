<?php
/**
 * Email Settings Admin Page for PlugHaus Property Management
 *
 * @package PlugHausPropertyManagement
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class PHPM_Email_Settings_Admin {
    
    /**
     * Initialize admin hooks
     */
    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_admin_menu'), 20);
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_scripts'));
        add_action('wp_ajax_phpm_save_email_settings', array(__CLASS__, 'ajax_save_settings'));
    }
    
    /**
     * Add admin menu item
     */
    public static function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=phpm_property',
            __('Email Settings', 'plughaus-property'),
            __('Email Settings', 'plughaus-property'),
            'manage_options',
            'phpm-email-settings',
            array(__CLASS__, 'admin_page')
        );
    }
    
    /**
     * Enqueue admin scripts
     */
    public static function enqueue_scripts($hook) {
        if ('phpm_property_page_phpm-email-settings' !== $hook) {
            return;
        }
        
        wp_enqueue_script(
            'phpm-email-settings-admin',
            PHPM_PLUGIN_URL . 'core/assets/js/email-settings-admin.js',
            array('jquery'),
            PHPM_VERSION,
            true
        );
        
        wp_localize_script('phpm-email-settings-admin', 'phpmEmailSettings', array(
            'nonce' => wp_create_nonce('phpm_admin_nonce'),
            'ajax_url' => admin_url('admin-ajax.php'),
            'strings' => array(
                'saving' => __('Saving...', 'plughaus-property'),
                'saved' => __('Settings saved!', 'plughaus-property'),
                'error' => __('Error saving settings.', 'plughaus-property'),
                'sending_test' => __('Sending test email...', 'plughaus-property'),
                'test_sent' => __('Test email sent!', 'plughaus-property'),
                'test_error' => __('Failed to send test email.', 'plughaus-property')
            )
        ));
        
        wp_enqueue_style(
            'phpm-email-settings-admin',
            PHPM_PLUGIN_URL . 'core/assets/css/email-settings-admin.css',
            array(),
            PHPM_VERSION
        );
    }
    
    /**
     * Display admin page
     */
    public static function admin_page() {
        $settings = PHPM_Email_Notifications::get_email_settings();
        
        ?>
        <div class="wrap">
            <h1><?php _e('Email Notification Settings', 'plughaus-property'); ?></h1>
            
            <div class="phpm-email-settings-container">
                
                <!-- General Settings -->
                <div class="card">
                    <h2><span class="dashicons dashicons-email-alt"></span> <?php _e('General Email Settings', 'plughaus-property'); ?></h2>
                    
                    <form id="phpm-email-settings-form">
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="from_name"><?php _e('From Name', 'plughaus-property'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="from_name" name="from_name" value="<?php echo esc_attr($settings['from_name']); ?>" class="regular-text" />
                                    <p class="description"><?php _e('The name that appears in the "From" field of emails.', 'plughaus-property'); ?></p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="from_email"><?php _e('From Email', 'plughaus-property'); ?></label>
                                </th>
                                <td>
                                    <input type="email" id="from_email" name="from_email" value="<?php echo esc_attr($settings['from_email']); ?>" class="regular-text" />
                                    <p class="description"><?php _e('The email address that emails are sent from.', 'plughaus-property'); ?></p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="logo_url"><?php _e('Email Logo URL', 'plughaus-property'); ?></label>
                                </th>
                                <td>
                                    <input type="url" id="logo_url" name="logo_url" value="<?php echo esc_attr($settings['logo_url']); ?>" class="regular-text" />
                                    <p class="description"><?php _e('URL to logo image to display in email headers. Leave blank for no logo.', 'plughaus-property'); ?></p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="footer_text"><?php _e('Email Footer', 'plughaus-property'); ?></label>
                                </th>
                                <td>
                                    <textarea id="footer_text" name="footer_text" rows="3" class="large-text"><?php echo esc_textarea($settings['footer_text']); ?></textarea>
                                    <p class="description"><?php _e('Text to display in the footer of all emails.', 'plughaus-property'); ?></p>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
                
                <!-- Notification Types -->
                <div class="card">
                    <h2><span class="dashicons dashicons-bell"></span> <?php _e('Notification Types', 'plughaus-property'); ?></h2>
                    
                    <div class="notification-settings">
                        
                        <!-- Lease Expiring Notifications -->
                        <div class="notification-type">
                            <h3>
                                <label>
                                    <input type="checkbox" name="lease_expiring_enabled" value="1" <?php checked($settings['lease_expiring_enabled']); ?> />
                                    <?php _e('Lease Expiring Notifications', 'plughaus-property'); ?>
                                </label>
                            </h3>
                            <div class="notification-options" <?php echo $settings['lease_expiring_enabled'] ? '' : 'style="display: none;"'; ?>>
                                <p>
                                    <label>
                                        <?php _e('Send notification when lease expires in', 'plughaus-property'); ?>
                                        <input type="number" name="lease_expiring_days" value="<?php echo esc_attr($settings['lease_expiring_days']); ?>" min="1" max="365" style="width: 60px;" />
                                        <?php _e('days', 'plughaus-property'); ?>
                                    </label>
                                </p>
                                <p class="description"><?php _e('Automatically sends notifications to tenants and property managers when leases are expiring.', 'plughaus-property'); ?></p>
                            </div>
                        </div>
                        
                        <!-- Maintenance Notifications -->
                        <div class="notification-type">
                            <h3>
                                <label>
                                    <input type="checkbox" name="maintenance_enabled" value="1" <?php checked($settings['maintenance_enabled']); ?> />
                                    <?php _e('Maintenance Request Notifications', 'plughaus-property'); ?>
                                </label>
                            </h3>
                            <div class="notification-options" <?php echo $settings['maintenance_enabled'] ? '' : 'style="display: none;"'; ?>>
                                <p class="description"><?php _e('Sends notifications when maintenance requests are created or updated.', 'plughaus-property'); ?></p>
                            </div>
                        </div>
                        
                        <!-- Payment Reminders -->
                        <div class="notification-type">
                            <h3>
                                <label>
                                    <input type="checkbox" name="payment_reminders_enabled" value="1" <?php checked($settings['payment_reminders_enabled']); ?> />
                                    <?php _e('Payment Reminder Notifications', 'plughaus-property'); ?>
                                </label>
                            </h3>
                            <div class="notification-options" <?php echo $settings['payment_reminders_enabled'] ? '' : 'style="display: none;"'; ?>>
                                <p>
                                    <label><?php _e('Send reminders after', 'plughaus-property'); ?></label><br>
                                    <?php
                                    $reminder_days = $settings['payment_reminder_days'];
                                    foreach (array(5, 10, 15, 30) as $day) {
                                        $checked = in_array($day, $reminder_days) ? 'checked' : '';
                                        echo '<label style="margin-right: 15px;"><input type="checkbox" name="payment_reminder_days[]" value="' . $day . '" ' . $checked . '> ' . sprintf(__('%d days', 'plughaus-property'), $day) . '</label>';
                                    }
                                    ?>
                                </p>
                                <p class="description"><?php _e('Automatically sends payment reminders to tenants with overdue rent.', 'plughaus-property'); ?></p>
                            </div>
                        </div>
                        
                        <!-- Welcome Emails -->
                        <div class="notification-type">
                            <h3>
                                <label>
                                    <input type="checkbox" name="welcome_emails_enabled" value="1" <?php checked($settings['welcome_emails_enabled']); ?> />
                                    <?php _e('Welcome Email for New Tenants', 'plughaus-property'); ?>
                                </label>
                            </h3>
                            <div class="notification-options" <?php echo $settings['welcome_emails_enabled'] ? '' : 'style="display: none;"'; ?>>
                                <p class="description"><?php _e('Sends a welcome email when a new lease is created.', 'plughaus-property'); ?></p>
                            </div>
                        </div>
                        
                    </div>
                </div>
                
                <!-- Recipients -->
                <div class="card">
                    <h2><span class="dashicons dashicons-groups"></span> <?php _e('Email Recipients', 'plughaus-property'); ?></h2>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php _e('Tenant Notifications', 'plughaus-property'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="tenant_notifications" value="1" <?php checked($settings['tenant_notifications']); ?> />
                                    <?php _e('Send notifications to tenants', 'plughaus-property'); ?>
                                </label>
                                <p class="description"><?php _e('Enable this to send relevant notifications directly to tenants.', 'plughaus-property'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><?php _e('Manager Notifications', 'plughaus-property'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="manager_notifications" value="1" <?php checked($settings['manager_notifications']); ?> />
                                    <?php _e('Send notifications to property manager', 'plughaus-property'); ?>
                                </label>
                                <p class="description"><?php _e('Enable this to send notifications to the property manager email address.', 'plughaus-property'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Test Email -->
                <div class="card">
                    <h2><span class="dashicons dashicons-admin-tools"></span> <?php _e('Test Email System', 'plughaus-property'); ?></h2>
                    
                    <p><?php _e('Send a test email to verify your email settings are working correctly.', 'plughaus-property'); ?></p>
                    
                    <div class="test-email-section">
                        <p>
                            <label for="test_email"><?php _e('Test Email Address:', 'plughaus-property'); ?></label><br>
                            <input type="email" id="test_email" value="<?php echo esc_attr(get_option('admin_email')); ?>" class="regular-text" />
                        </p>
                        
                        <p>
                            <label for="test_template"><?php _e('Email Template:', 'plughaus-property'); ?></label><br>
                            <select id="test_template" class="regular-text">
                                <option value="lease_expiring_tenant"><?php _e('Lease Expiring (Tenant)', 'plughaus-property'); ?></option>
                                <option value="lease_expiring_manager"><?php _e('Lease Expiring (Manager)', 'plughaus-property'); ?></option>
                                <option value="maintenance_received_tenant"><?php _e('Maintenance Request Received', 'plughaus-property'); ?></option>
                                <option value="payment_reminder"><?php _e('Payment Reminder', 'plughaus-property'); ?></option>
                                <option value="welcome_tenant"><?php _e('Welcome Email', 'plughaus-property'); ?></option>
                            </select>
                        </p>
                        
                        <p>
                            <button type="button" id="send-test-email" class="button button-secondary">
                                <span class="dashicons dashicons-email"></span>
                                <?php _e('Send Test Email', 'plughaus-property'); ?>
                            </button>
                        </p>
                    </div>
                </div>
                
                <!-- Email Templates Preview -->
                <div class="card">
                    <h2><span class="dashicons dashicons-media-document"></span> <?php _e('Email Templates', 'plughaus-property'); ?></h2>
                    
                    <p><?php _e('Email templates use placeholder variables that are automatically replaced with actual data when emails are sent.', 'plughaus-property'); ?></p>
                    
                    <div class="template-variables">
                        <h3><?php _e('Available Variables', 'plughaus-property'); ?></h3>
                        <div class="variables-grid">
                            <div class="variable-group">
                                <h4><?php _e('Tenant Variables', 'plughaus-property'); ?></h4>
                                <ul>
                                    <li><code>{{tenant_name}}</code> - <?php _e('Tenant full name', 'plughaus-property'); ?></li>
                                    <li><code>{{recipient_name}}</code> - <?php _e('Email recipient name', 'plughaus-property'); ?></li>
                                </ul>
                            </div>
                            
                            <div class="variable-group">
                                <h4><?php _e('Property Variables', 'plughaus-property'); ?></h4>
                                <ul>
                                    <li><code>{{property_name}}</code> - <?php _e('Property name', 'plughaus-property'); ?></li>
                                    <li><code>{{unit_number}}</code> - <?php _e('Unit number', 'plughaus-property'); ?></li>
                                    <li><code>{{property_address}}</code> - <?php _e('Property address', 'plughaus-property'); ?></li>
                                </ul>
                            </div>
                            
                            <div class="variable-group">
                                <h4><?php _e('Date Variables', 'plughaus-property'); ?></h4>
                                <ul>
                                    <li><code>{{end_date}}</code> - <?php _e('Lease end date', 'plughaus-property'); ?></li>
                                    <li><code>{{move_in_date}}</code> - <?php _e('Move-in date', 'plughaus-property'); ?></li>
                                    <li><code>{{date_created}}</code> - <?php _e('Creation date', 'plughaus-property'); ?></li>
                                </ul>
                            </div>
                            
                            <div class="variable-group">
                                <h4><?php _e('Financial Variables', 'plughaus-property'); ?></h4>
                                <ul>
                                    <li><code>{{rent_amount}}</code> - <?php _e('Monthly rent amount', 'plughaus-property'); ?></li>
                                    <li><code>{{days_overdue}}</code> - <?php _e('Days payment is overdue', 'plughaus-property'); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="template-note">
                        <p><strong><?php _e('Note:', 'plughaus-property'); ?></strong> <?php _e('Templates are automatically wrapped in HTML layout with your logo and footer text. Content is styled for optimal email display.', 'plughaus-property'); ?></p>
                    </div>
                </div>
                
                <!-- Save Button -->
                <div class="submit-section">
                    <p class="submit">
                        <button type="button" id="save-email-settings" class="button button-primary button-large">
                            <span class="dashicons dashicons-yes"></span>
                            <?php _e('Save Email Settings', 'plughaus-property'); ?>
                        </button>
                    </p>
                </div>
                
            </div>
            
            <div id="email-settings-feedback" class="notice" style="display: none;">
                <p><span id="feedback-message"></span></p>
            </div>
        </div>
        <?php
    }
    
    /**
     * AJAX handler for saving email settings
     */
    public static function ajax_save_settings() {
        check_ajax_referer('phpm_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions.', 'plughaus-property'));
        }
        
        // Sanitize and prepare settings
        $settings = array();
        
        // Basic settings
        $settings['from_name'] = sanitize_text_field($_POST['from_name']);
        $settings['from_email'] = sanitize_email($_POST['from_email']);
        $settings['logo_url'] = esc_url_raw($_POST['logo_url']);
        $settings['footer_text'] = wp_kses_post($_POST['footer_text']);
        
        // Notification toggles
        $settings['lease_expiring_enabled'] = isset($_POST['lease_expiring_enabled']);
        $settings['maintenance_enabled'] = isset($_POST['maintenance_enabled']);
        $settings['payment_reminders_enabled'] = isset($_POST['payment_reminders_enabled']);
        $settings['welcome_emails_enabled'] = isset($_POST['welcome_emails_enabled']);
        
        // Recipient settings
        $settings['tenant_notifications'] = isset($_POST['tenant_notifications']);
        $settings['manager_notifications'] = isset($_POST['manager_notifications']);
        
        // Lease expiring days
        $settings['lease_expiring_days'] = absint($_POST['lease_expiring_days']);
        
        // Payment reminder days
        $reminder_days = isset($_POST['payment_reminder_days']) ? array_map('absint', $_POST['payment_reminder_days']) : array();
        $settings['payment_reminder_days'] = $reminder_days;
        
        // Update settings
        $updated = PHPM_Email_Notifications::update_email_settings($settings);
        
        if ($updated) {
            wp_send_json_success(__('Email settings saved successfully!', 'plughaus-property'));
        } else {
            wp_send_json_error(__('Failed to save email settings.', 'plughaus-property'));
        }
    }
}

// Initialize email settings admin
PHPM_Email_Settings_Admin::init();