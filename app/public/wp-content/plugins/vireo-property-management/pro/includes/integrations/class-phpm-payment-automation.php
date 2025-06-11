<?php
/**
 * Payment Automation Integration for Pro Version
 */

if (!defined('ABSPATH')) {
    exit;
}

class PHPM_Payment_Automation {
    
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
        
        // Payment automation hooks
        add_action('phpm_rent_due_reminder', array($this, 'send_rent_due_reminder'));
        add_action('phpm_late_payment_notice', array($this, 'send_late_payment_notice'));
        add_action('phpm_process_auto_payments', array($this, 'process_automatic_payments'));
        
        // Admin settings
        add_action('admin_menu', array($this, 'add_payment_settings_page'));
        add_action('admin_init', array($this, 'register_payment_settings'));
        
        // Schedule automation events
        $this->schedule_automation_events();
    }
    
    /**
     * Schedule automation events
     */
    private function schedule_automation_events() {
        // Schedule daily payment processing
        if (!wp_next_scheduled('phpm_process_auto_payments')) {
            wp_schedule_event(time(), 'daily', 'phpm_process_auto_payments');
        }
        
        // Schedule rent due reminders (3 days before due date)
        if (!wp_next_scheduled('phpm_rent_due_reminder')) {
            wp_schedule_event(time(), 'daily', 'phpm_rent_due_reminder');
        }
        
        // Schedule late payment notices (1 day after due date)
        if (!wp_next_scheduled('phpm_late_payment_notice')) {
            wp_schedule_event(time(), 'daily', 'phpm_late_payment_notice');
        }
    }
    
    /**
     * Send rent due reminders
     */
    public function send_rent_due_reminder() {
        $reminder_days = get_option('phpm_rent_reminder_days', 3);
        $target_date = date('Y-m-d', strtotime("+{$reminder_days} days"));
        
        // Get leases with rent due on target date
        $leases = $this->get_leases_with_rent_due($target_date);
        
        foreach ($leases as $lease) {
            $this->send_rent_reminder_email($lease);
            
            // Log the reminder
            $this->log_payment_event($lease['id'], 'rent_reminder_sent', array(
                'due_date' => $target_date,
                'amount' => $lease['rent_amount']
            ));
        }
    }
    
    /**
     * Send late payment notices
     */
    public function send_late_payment_notice() {
        $grace_days = get_option('phpm_late_payment_grace_days', 1);
        $target_date = date('Y-m-d', strtotime("-{$grace_days} days"));
        
        // Get overdue payments
        $overdue_payments = $this->get_overdue_payments($target_date);
        
        foreach ($overdue_payments as $payment) {
            $this->send_late_payment_email($payment);
            
            // Apply late fees if configured
            if (get_option('phpm_auto_apply_late_fees', false)) {
                $this->apply_late_fee($payment);
            }
            
            // Log the notice
            $this->log_payment_event($payment['lease_id'], 'late_notice_sent', array(
                'overdue_amount' => $payment['amount'],
                'days_overdue' => $this->calculate_days_overdue($payment['due_date'])
            ));
        }
    }
    
    /**
     * Process automatic payments
     */
    public function process_automatic_payments() {
        if (!get_option('phpm_enable_auto_payments', false)) {
            return;
        }
        
        // Get payments due today that have auto-pay enabled
        $auto_payments = $this->get_scheduled_auto_payments();
        
        foreach ($auto_payments as $payment) {
            $result = $this->process_payment($payment);
            
            if ($result['success']) {
                $this->log_payment_event($payment['lease_id'], 'auto_payment_success', array(
                    'amount' => $payment['amount'],
                    'transaction_id' => $result['transaction_id']
                ));
                
                $this->send_payment_confirmation_email($payment, $result);
            } else {
                $this->log_payment_event($payment['lease_id'], 'auto_payment_failed', array(
                    'amount' => $payment['amount'],
                    'error' => $result['error']
                ));
                
                $this->send_payment_failure_email($payment, $result);
            }
        }
    }
    
    /**
     * Add payment settings page
     */
    public function add_payment_settings_page() {
        add_submenu_page(
            'phpm-settings',
            __('Payment Automation', 'plughaus-property'),
            __('Payment Automation', 'plughaus-property'),
            'manage_options',
            'phpm-payment-automation',
            array($this, 'render_payment_settings_page')
        );
    }
    
    /**
     * Register payment settings
     */
    public function register_payment_settings() {
        // Payment automation section
        add_settings_section(
            'phpm_payment_automation_section',
            __('Payment Automation Settings', 'plughaus-property'),
            array($this, 'payment_automation_section_callback'),
            'phpm_payment_automation'
        );
        
        // Auto-payment settings
        add_settings_field(
            'phpm_enable_auto_payments',
            __('Enable Automatic Payments', 'plughaus-property'),
            array($this, 'checkbox_field_callback'),
            'phpm_payment_automation',
            'phpm_payment_automation_section',
            array(
                'option_name' => 'phpm_enable_auto_payments',
                'description' => __('Allow tenants to set up automatic rent payments.', 'plughaus-property')
            )
        );
        
        // Reminder settings
        add_settings_field(
            'phpm_rent_reminder_days',
            __('Rent Reminder Days', 'plughaus-property'),
            array($this, 'number_field_callback'),
            'phpm_payment_automation',
            'phpm_payment_automation_section',
            array(
                'option_name' => 'phpm_rent_reminder_days',
                'description' => __('Send rent reminders this many days before due date.', 'plughaus-property'),
                'default' => 3
            )
        );
        
        // Late fee settings
        add_settings_field(
            'phpm_auto_apply_late_fees',
            __('Auto-Apply Late Fees', 'plughaus-property'),
            array($this, 'checkbox_field_callback'),
            'phpm_payment_automation',
            'phpm_payment_automation_section',
            array(
                'option_name' => 'phpm_auto_apply_late_fees',
                'description' => __('Automatically apply late fees to overdue payments.', 'plughaus-property')
            )
        );
        
        add_settings_field(
            'phpm_late_fee_amount',
            __('Late Fee Amount', 'plughaus-property'),
            array($this, 'number_field_callback'),
            'phpm_payment_automation',
            'phpm_payment_automation_section',
            array(
                'option_name' => 'phpm_late_fee_amount',
                'description' => __('Fixed late fee amount in dollars.', 'plughaus-property'),
                'default' => 50
            )
        );
        
        // Register all settings
        register_setting('phpm_payment_automation_group', 'phpm_enable_auto_payments');
        register_setting('phpm_payment_automation_group', 'phpm_rent_reminder_days');
        register_setting('phpm_payment_automation_group', 'phpm_auto_apply_late_fees');
        register_setting('phpm_payment_automation_group', 'phpm_late_fee_amount');
        register_setting('phpm_payment_automation_group', 'phpm_late_payment_grace_days');
    }
    
    /**
     * Render payment settings page
     */
    public function render_payment_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Payment Automation Settings', 'plughaus-property'); ?></h1>
            
            <form method="post" action="options.php">
                <?php
                settings_fields('phpm_payment_automation_group');
                do_settings_sections('phpm_payment_automation');
                submit_button();
                ?>
            </form>
            
            <div class="phpm-payment-automation-status">
                <h2><?php _e('Automation Status', 'plughaus-property'); ?></h2>
                
                <table class="widefat">
                    <thead>
                        <tr>
                            <th><?php _e('Automation Task', 'plughaus-property'); ?></th>
                            <th><?php _e('Status', 'plughaus-property'); ?></th>
                            <th><?php _e('Next Run', 'plughaus-property'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php _e('Rent Due Reminders', 'plughaus-property'); ?></td>
                            <td>
                                <?php if (wp_next_scheduled('phpm_rent_due_reminder')): ?>
                                    <span class="phpm-status-active"><?php _e('Active', 'plughaus-property'); ?></span>
                                <?php else: ?>
                                    <span class="phmp-status-inactive"><?php _e('Inactive', 'plughaus-property'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo wp_next_scheduled('phpm_rent_due_reminder') ? date('Y-m-d H:i:s', wp_next_scheduled('phpm_rent_due_reminder')) : '-'; ?></td>
                        </tr>
                        <tr>
                            <td><?php _e('Late Payment Notices', 'plughaus-property'); ?></td>
                            <td>
                                <?php if (wp_next_scheduled('phpm_late_payment_notice')): ?>
                                    <span class="phpm-status-active"><?php _e('Active', 'plughaus-property'); ?></span>
                                <?php else: ?>
                                    <span class="phpm-status-inactive"><?php _e('Inactive', 'plughaus-property'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo wp_next_scheduled('phpm_late_payment_notice') ? date('Y-m-d H:i:s', wp_next_scheduled('phpm_late_payment_notice')) : '-'; ?></td>
                        </tr>
                        <tr>
                            <td><?php _e('Automatic Payments', 'plughaus-property'); ?></td>
                            <td>
                                <?php if (get_option('phpm_enable_auto_payments', false)): ?>
                                    <span class="phpm-status-active"><?php _e('Enabled', 'plughaus-property'); ?></span>
                                <?php else: ?>
                                    <span class="phpm-status-inactive"><?php _e('Disabled', 'plughaus-property'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo wp_next_scheduled('phpm_process_auto_payments') ? date('Y-m-d H:i:s', wp_next_scheduled('phpm_process_auto_payments')) : '-'; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }
    
    /**
     * Helper methods for database queries and payment processing
     */
    private function get_leases_with_rent_due($date) {
        // Query leases with rent due on specific date
        // This would be replaced with actual database queries
        return array();
    }
    
    private function get_overdue_payments($date) {
        // Query overdue payments
        return array();
    }
    
    private function get_scheduled_auto_payments() {
        // Query payments scheduled for automatic processing
        return array();
    }
    
    private function process_payment($payment) {
        // Process payment through Stripe, PayPal, etc.
        // This would integrate with actual payment processors
        return array(
            'success' => true,
            'transaction_id' => 'txn_' . uniqid(),
            'message' => 'Payment processed successfully'
        );
    }
    
    private function send_rent_reminder_email($lease) {
        // Send rent reminder email to tenant
    }
    
    private function send_late_payment_email($payment) {
        // Send late payment notice to tenant
    }
    
    private function send_payment_confirmation_email($payment, $result) {
        // Send payment confirmation email
    }
    
    private function send_payment_failure_email($payment, $result) {
        // Send payment failure notification
    }
    
    private function apply_late_fee($payment) {
        // Apply late fee to tenant's account
    }
    
    private function calculate_days_overdue($due_date) {
        return (strtotime('now') - strtotime($due_date)) / (60 * 60 * 24);
    }
    
    private function log_payment_event($lease_id, $event_type, $data = array()) {
        // Log payment events for audit trail
        $log_entry = array(
            'lease_id' => $lease_id,
            'event_type' => $event_type,
            'event_data' => $data,
            'timestamp' => current_time('mysql')
        );
        
        // This would insert into a payment_logs table
    }
    
    /**
     * Settings field callbacks
     */
    public function payment_automation_section_callback() {
        echo '<p>' . __('Configure automatic payment processing and notifications.', 'plughaus-property') . '</p>';
    }
    
    public function checkbox_field_callback($args) {
        $option_name = $args['option_name'];
        $value = get_option($option_name, false);
        $description = isset($args['description']) ? $args['description'] : '';
        
        echo '<input type="checkbox" id="' . $option_name . '" name="' . $option_name . '" value="1" ' . checked(1, $value, false) . ' />';
        echo '<label for="' . $option_name . '">' . $description . '</label>';
    }
    
    public function number_field_callback($args) {
        $option_name = $args['option_name'];
        $value = get_option($option_name, $args['default']);
        $description = isset($args['description']) ? $args['description'] : '';
        
        echo '<input type="number" id="' . $option_name . '" name="' . $option_name . '" value="' . esc_attr($value) . '" class="small-text" />';
        echo '<p class="description">' . $description . '</p>';
    }
}