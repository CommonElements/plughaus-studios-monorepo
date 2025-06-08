<?php
/**
 * Settings management for PlugHaus Property Management
 */

if (!defined('ABSPATH')) {
    exit;
}

class PHPM_Admin_Settings {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    /**
     * Register plugin settings
     */
    public function register_settings() {
        // Register settings group
        register_setting('phpm_settings_group', 'phpm_settings', array($this, 'sanitize_settings'));
        
        // General Settings Section
        add_settings_section(
            'phpm_general_settings',
            __('General Settings', 'plughaus-property'),
            array($this, 'render_general_settings_section'),
            'phpm_settings'
        );
        
        // Currency setting
        add_settings_field(
            'phpm_currency',
            __('Currency', 'plughaus-property'),
            array($this, 'render_currency_field'),
            'phpm_settings',
            'phpm_general_settings'
        );
        
        // Date format setting
        add_settings_field(
            'phpm_date_format',
            __('Date Format', 'plughaus-property'),
            array($this, 'render_date_format_field'),
            'phpm_settings',
            'phpm_general_settings'
        );
        
        // Email Settings Section
        add_settings_section(
            'phpm_email_settings',
            __('Email Settings', 'plughaus-property'),
            array($this, 'render_email_settings_section'),
            'phpm_settings'
        );
        
        // Admin email
        add_settings_field(
            'phpm_admin_email',
            __('Admin Email', 'plughaus-property'),
            array($this, 'render_admin_email_field'),
            'phpm_settings',
            'phpm_email_settings'
        );
        
        // Email notifications
        add_settings_field(
            'phpm_email_notifications',
            __('Email Notifications', 'plughaus-property'),
            array($this, 'render_email_notifications_field'),
            'phpm_settings',
            'phpm_email_settings'
        );
        
        // API Settings Section
        add_settings_section(
            'phpm_api_settings',
            __('API Settings', 'plughaus-property'),
            array($this, 'render_api_settings_section'),
            'phpm_settings'
        );
        
        // Enable API
        add_settings_field(
            'phpm_enable_api',
            __('Enable REST API', 'plughaus-property'),
            array($this, 'render_enable_api_field'),
            'phpm_settings',
            'phpm_api_settings'
        );
    }
    
    /**
     * Sanitize settings
     */
    public function sanitize_settings($input) {
        $sanitized = array();
        
        // Sanitize currency
        if (isset($input['currency'])) {
            $sanitized['currency'] = sanitize_text_field($input['currency']);
        }
        
        // Sanitize date format
        if (isset($input['date_format'])) {
            $sanitized['date_format'] = sanitize_text_field($input['date_format']);
        }
        
        // Sanitize admin email
        if (isset($input['admin_email'])) {
            $sanitized['admin_email'] = sanitize_email($input['admin_email']);
        }
        
        // Sanitize email notifications
        if (isset($input['email_notifications'])) {
            $sanitized['email_notifications'] = array_map('sanitize_text_field', $input['email_notifications']);
        }
        
        // Sanitize API setting
        $sanitized['enable_api'] = isset($input['enable_api']) ? 1 : 0;
        
        return $sanitized;
    }
    
    /**
     * Render general settings section
     */
    public function render_general_settings_section() {
        echo '<p>' . __('Configure general settings for your property management system.', 'plughaus-property') . '</p>';
    }
    
    /**
     * Render currency field
     */
    public function render_currency_field() {
        $options = get_option('phpm_settings');
        $currency = isset($options['currency']) ? $options['currency'] : 'USD';
        
        $currencies = array(
            'USD' => __('US Dollar ($)', 'plughaus-property'),
            'EUR' => __('Euro (€)', 'plughaus-property'),
            'GBP' => __('British Pound (£)', 'plughaus-property'),
            'CAD' => __('Canadian Dollar (C$)', 'plughaus-property'),
            'AUD' => __('Australian Dollar (A$)', 'plughaus-property'),
        );
        
        echo '<select name="phpm_settings[currency]" id="phpm_currency">';
        foreach ($currencies as $code => $name) {
            echo '<option value="' . esc_attr($code) . '"' . selected($currency, $code, false) . '>' . esc_html($name) . '</option>';
        }
        echo '</select>';
    }
    
    /**
     * Render date format field
     */
    public function render_date_format_field() {
        $options = get_option('phpm_settings');
        $date_format = isset($options['date_format']) ? $options['date_format'] : get_option('date_format');
        
        $formats = array(
            'Y-m-d' => date('Y-m-d'),
            'm/d/Y' => date('m/d/Y'),
            'd/m/Y' => date('d/m/Y'),
            'F j, Y' => date('F j, Y'),
        );
        
        foreach ($formats as $format => $example) {
            echo '<label>';
            echo '<input type="radio" name="phpm_settings[date_format]" value="' . esc_attr($format) . '"' . checked($date_format, $format, false) . '>';
            echo ' <span>' . esc_html($example) . '</span>';
            echo '</label><br>';
        }
    }
    
    /**
     * Render email settings section
     */
    public function render_email_settings_section() {
        echo '<p>' . __('Configure email notifications and settings.', 'plughaus-property') . '</p>';
    }
    
    /**
     * Render admin email field
     */
    public function render_admin_email_field() {
        $options = get_option('phpm_settings');
        $admin_email = isset($options['admin_email']) ? $options['admin_email'] : get_option('admin_email');
        
        echo '<input type="email" name="phpm_settings[admin_email]" value="' . esc_attr($admin_email) . '" class="regular-text" />';
        echo '<p class="description">' . __('Email address for admin notifications.', 'plughaus-property') . '</p>';
    }
    
    /**
     * Render email notifications field
     */
    public function render_email_notifications_field() {
        $options = get_option('phpm_settings');
        $notifications = isset($options['email_notifications']) ? $options['email_notifications'] : array();
        
        $notification_types = array(
            'new_tenant' => __('New tenant registration', 'plughaus-property'),
            'lease_expiry' => __('Lease expiry reminders', 'plughaus-property'),
            'maintenance_request' => __('New maintenance requests', 'plughaus-property'),
            'payment_received' => __('Payment received', 'plughaus-property'),
        );
        
        foreach ($notification_types as $type => $label) {
            $checked = in_array($type, $notifications);
            echo '<label>';
            echo '<input type="checkbox" name="phpm_settings[email_notifications][]" value="' . esc_attr($type) . '"' . checked($checked, true, false) . '>';
            echo ' ' . esc_html($label);
            echo '</label><br>';
        }
    }
    
    /**
     * Render API settings section
     */
    public function render_api_settings_section() {
        echo '<p>' . __('Configure REST API access for external integrations.', 'plughaus-property') . '</p>';
    }
    
    /**
     * Render enable API field
     */
    public function render_enable_api_field() {
        $options = get_option('phpm_settings');
        $enable_api = isset($options['enable_api']) ? $options['enable_api'] : 1;
        
        echo '<label>';
        echo '<input type="checkbox" name="phpm_settings[enable_api]" value="1"' . checked($enable_api, 1, false) . '>';
        echo ' ' . __('Enable REST API access', 'plughaus-property');
        echo '</label>';
        echo '<p class="description">' . __('Allow external applications to access property data via REST API.', 'plughaus-property') . '</p>';
    }
}

// Initialize settings
new PHPM_Admin_Settings();