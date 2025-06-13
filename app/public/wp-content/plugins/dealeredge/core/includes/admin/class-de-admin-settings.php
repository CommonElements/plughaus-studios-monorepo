<?php
/**
 * DealerEdge Admin Settings
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class DE_Admin_Settings {
    
    public function __construct() {
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    public function register_settings() {
        register_setting('dealeredge_settings', 'dealeredge_business_name');
        register_setting('dealeredge_settings', 'dealeredge_business_type');
        register_setting('dealeredge_settings', 'dealeredge_currency');
        register_setting('dealeredge_settings', 'dealeredge_tax_rate');
        register_setting('dealeredge_settings', 'dealeredge_labor_rate');
    }
}