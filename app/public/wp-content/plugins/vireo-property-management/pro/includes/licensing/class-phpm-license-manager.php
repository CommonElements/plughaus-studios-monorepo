<?php
/**
 * License Manager for PlugHaus Property Management Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

class PHPM_License_Manager {
    
    /**
     * License API endpoint
     */
    const LICENSE_API_URL = 'https://plughausstudios.com/wp-json/lmfwc/v2/licenses/activate';
    
    /**
     * License option key
     */
    const LICENSE_OPTION_KEY = 'phpm_license_key';
    
    /**
     * License status option key
     */
    const LICENSE_STATUS_KEY = 'phpm_license_status';
    
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
        // License validation endpoint
        add_action('rest_api_init', array($this, 'register_routes'));
        
        // License check cron
        add_action('phpm_license_check', array($this, 'check_license_status'));
        if (!wp_next_scheduled('phpm_license_check')) {
            wp_schedule_event(time(), 'daily', 'phpm_license_check');
        }
        
        // License settings
        add_action('admin_init', array($this, 'register_license_settings'));
        
        // AJAX handlers
        add_action('wp_ajax_phpm_deactivate_license', array($this, 'ajax_deactivate_license'));
    }
    
    /**
     * Register REST API routes
     */
    public static function register_routes() {
        register_rest_route('phls/v1', '/validate', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'validate_license_endpoint'),
            'permission_callback' => array(__CLASS__, 'license_permission_check'),
        ));
        
        register_rest_route('phls/v1', '/status', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_license_status_endpoint'),
            'permission_callback' => array(__CLASS__, 'license_permission_check'),
        ));
    }
    
    /**
     * Permission check for license endpoints
     */
    public static function license_permission_check() {
        return current_user_can('manage_options');
    }
    
    /**
     * Validate license via REST API endpoint
     */
    public static function validate_license_endpoint($request) {
        $license_key = sanitize_text_field($request->get_param('license_key'));
        
        if (empty($license_key)) {
            return new WP_Error('missing_license', 'License key is required.', array('status' => 400));
        }
        
        $validation_result = self::validate_license($license_key);
        
        if ($validation_result['valid']) {
            update_option(self::LICENSE_OPTION_KEY, $license_key);
            update_option(self::LICENSE_STATUS_KEY, 'valid');
            update_option('phpm_license_expires', $validation_result['expires']);
            
            return array(
                'success' => true,
                'message' => 'License activated successfully.',
                'expires' => $validation_result['expires']
            );
        } else {
            delete_option(self::LICENSE_OPTION_KEY);
            update_option(self::LICENSE_STATUS_KEY, 'invalid');
            
            return new WP_Error('invalid_license', $validation_result['message'], array('status' => 400));
        }
    }
    
    /**
     * Get license status via REST API endpoint
     */
    public static function get_license_status_endpoint() {
        $license_status = get_option(self::LICENSE_STATUS_KEY, 'not_activated');
        $license_key = get_option(self::LICENSE_OPTION_KEY, '');
        $expires = get_option('phpm_license_expires', '');
        
        return array(
            'status' => $license_status,
            'license_key' => !empty($license_key) ? substr($license_key, 0, 8) . '...' : '',
            'expires' => $expires,
            'is_valid' => $license_status === 'valid'
        );
    }
    
    /**
     * Validate license with remote server
     */
    public static function validate_license($license_key) {
        // For now, return a mock validation
        // In production, this would make an HTTP request to your license server
        
        // Mock validation logic
        if (strlen($license_key) >= 16 && strpos($license_key, 'PHPM-') === 0) {
            return array(
                'valid' => true,
                'message' => 'License is valid.',
                'expires' => date('Y-m-d', strtotime('+1 year'))
            );
        }
        
        return array(
            'valid' => false,
            'message' => 'Invalid license key format.',
            'expires' => ''
        );
    }
    
    /**
     * Check if current license is valid
     */
    public static function is_valid() {
        $license_status = get_option(self::LICENSE_STATUS_KEY, 'not_activated');
        $expires = get_option('phpm_license_expires', '');
        
        if ($license_status !== 'valid') {
            return false;
        }
        
        // Check expiration
        if (!empty($expires) && strtotime($expires) < time()) {
            update_option(self::LICENSE_STATUS_KEY, 'expired');
            return false;
        }
        
        return true;
    }
    
    /**
     * Check license status via cron
     */
    public function check_license_status() {
        $license_key = get_option(self::LICENSE_OPTION_KEY, '');
        
        if (empty($license_key)) {
            return;
        }
        
        $validation_result = self::validate_license($license_key);
        
        if ($validation_result['valid']) {
            update_option(self::LICENSE_STATUS_KEY, 'valid');
            update_option('phpm_license_expires', $validation_result['expires']);
        } else {
            update_option(self::LICENSE_STATUS_KEY, 'invalid');
        }
    }
    
    /**
     * Register license settings
     */
    public function register_license_settings() {
        // Add license section to settings
        add_settings_section(
            'phpm_license_section',
            __('Pro License', 'plughaus-property'),
            array($this, 'license_section_callback'),
            'phpm_settings'
        );
        
        add_settings_field(
            'phpm_license_key',
            __('License Key', 'plughaus-property'),
            array($this, 'license_key_field_callback'),
            'phpm_settings',
            'phpm_license_section'
        );
        
        register_setting('phpm_settings_group', 'phpm_license_key', array(
            'sanitize_callback' => array($this, 'sanitize_license_key')
        ));
    }
    
    /**
     * License section callback
     */
    public function license_section_callback() {
        echo '<p>' . __('Enter your Pro license key to unlock advanced features.', 'plughaus-property') . '</p>';
        
        $status = get_option(self::LICENSE_STATUS_KEY, 'not_activated');
        $status_class = $status === 'valid' ? 'notice-success' : 'notice-warning';
        $status_text = $status === 'valid' ? __('Active', 'plughaus-property') : __('Not Active', 'plughaus-property');
        
        echo '<div class="notice ' . $status_class . ' inline"><p><strong>' . __('License Status:', 'plughaus-property') . '</strong> ' . $status_text . '</p></div>';
    }
    
    /**
     * License key field callback
     */
    public function license_key_field_callback() {
        $license_key = get_option(self::LICENSE_OPTION_KEY, '');
        $display_key = !empty($license_key) ? substr($license_key, 0, 8) . str_repeat('*', strlen($license_key) - 8) : '';
        
        echo '<input type="text" id="phpm_license_key" name="phpm_license_key" value="' . esc_attr($display_key) . '" class="regular-text" placeholder="PHPM-XXXX-XXXX-XXXX-XXXX" />';
        echo '<p class="description">' . __('Your Pro license key from PlugHaus Studios.', 'plughaus-property') . '</p>';
        
        if (!empty($license_key)) {
            echo '<button type="button" class="button" id="phpm-validate-license">' . __('Validate License', 'plughaus-property') . '</button>';
            echo '<button type="button" class="button" id="phpm-deactivate-license">' . __('Deactivate License', 'plughaus-property') . '</button>';
        }
    }
    
    /**
     * Sanitize license key
     */
    public function sanitize_license_key($input) {
        $license_key = sanitize_text_field($input);
        
        // Only validate if a new key is provided
        if (!empty($license_key) && !strpos($license_key, '*')) {
            $validation_result = self::validate_license($license_key);
            
            if ($validation_result['valid']) {
                update_option(self::LICENSE_STATUS_KEY, 'valid');
                update_option('phpm_license_expires', $validation_result['expires']);
                
                add_settings_error('phpm_license_key', 'license_activated', __('License activated successfully!', 'plughaus-property'), 'success');
                
                return $license_key;
            } else {
                update_option(self::LICENSE_STATUS_KEY, 'invalid');
                
                add_settings_error('phpm_license_key', 'license_invalid', $validation_result['message'], 'error');
                
                return '';
            }
        }
        
        // Return existing key if masked input
        return get_option(self::LICENSE_OPTION_KEY, '');
    }
    
    /**
     * Deactivate license
     */
    public static function deactivate_license() {
        delete_option(self::LICENSE_OPTION_KEY);
        update_option(self::LICENSE_STATUS_KEY, 'not_activated');
        delete_option('phpm_license_expires');
    }
    
    /**
     * Get license information
     */
    public static function get_license_info() {
        return array(
            'status' => get_option(self::LICENSE_STATUS_KEY, 'not_activated'),
            'expires' => get_option('phpm_license_expires', ''),
            'is_valid' => self::is_valid()
        );
    }
    
    /**
     * AJAX handler for license deactivation
     */
    public function ajax_deactivate_license() {
        // Check nonce
        if (!wp_verify_nonce($_POST['nonce'], 'vmp_admin_nonce')) {
            wp_die(__('Security check failed.', 'plughaus-property'));
        }
        
        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions.', 'plughaus-property'));
        }
        
        // Deactivate license
        self::deactivate_license();
        
        wp_send_json_success(array(
            'message' => __('License deactivated successfully.', 'plughaus-property')
        ));
    }
}