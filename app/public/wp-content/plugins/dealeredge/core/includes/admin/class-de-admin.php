<?php
/**
 * The admin-specific functionality of the plugin
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class DE_Admin {
    
    public function __construct() {
        // Constructor actions
    }
    
    public function enqueue_styles() {
        if ($this->is_dealeredge_admin_page()) {
            wp_enqueue_style(
                'dealeredge-admin',
                DE_PLUGIN_URL . 'core/assets/css/admin.css',
                array(),
                DE_VERSION,
                'all'
            );
        }
    }
    
    public function enqueue_scripts() {
        if ($this->is_dealeredge_admin_page()) {
            wp_enqueue_script(
                'dealeredge-admin',
                DE_PLUGIN_URL . 'core/assets/js/admin.js',
                array('jquery'),
                DE_VERSION,
                false
            );
            
            // Localize script for AJAX
            wp_localize_script('dealeredge-admin', 'dealeredge_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('dealeredge_nonce'),
            ));
        }
    }
    
    public function add_admin_menus() {
        // Main menu page
        add_menu_page(
            __('DealerEdge', 'dealeredge'),
            __('DealerEdge', 'dealeredge'),
            'manage_options',
            'dealeredge',
            array($this, 'display_dashboard'),
            'dashicons-car',
            30
        );
        
        // Dashboard submenu
        add_submenu_page(
            'dealeredge',
            __('Dashboard', 'dealeredge'),
            __('Dashboard', 'dealeredge'),
            'manage_options',
            'dealeredge',
            array($this, 'display_dashboard')
        );
        
        // Vehicles submenu
        add_submenu_page(
            'dealeredge',
            __('Vehicles', 'dealeredge'),
            __('Vehicles', 'dealeredge'),
            'edit_posts',
            'edit.php?post_type=de_vehicle'
        );
        
        // Customers submenu
        add_submenu_page(
            'dealeredge',
            __('Customers', 'dealeredge'),
            __('Customers', 'dealeredge'),
            'edit_posts',
            'edit.php?post_type=de_customer'
        );
        
        // Work Orders submenu
        add_submenu_page(
            'dealeredge',
            __('Work Orders', 'dealeredge'),
            __('Work Orders', 'dealeredge'),
            'edit_posts',
            'edit.php?post_type=de_work_order'
        );
        
        // Sales submenu
        add_submenu_page(
            'dealeredge',
            __('Sales', 'dealeredge'),
            __('Sales', 'dealeredge'),
            'edit_posts',
            'edit.php?post_type=de_sale'
        );
        
        // Parts submenu
        add_submenu_page(
            'dealeredge',
            __('Parts', 'dealeredge'),
            __('Parts', 'dealeredge'),
            'edit_posts',
            'edit.php?post_type=de_part'
        );
        
        // Settings submenu
        add_submenu_page(
            'dealeredge',
            __('Settings', 'dealeredge'),
            __('Settings', 'dealeredge'),
            'manage_options',
            'dealeredge-settings',
            array($this, 'display_settings')
        );
    }
    
    public function display_dashboard() {
        include DE_CORE_DIR . 'includes/admin/views/dashboard.php';
    }
    
    public function display_settings() {
        include DE_CORE_DIR . 'includes/admin/views/settings.php';
    }
    
    private function is_dealeredge_admin_page() {
        $screen = get_current_screen();
        
        if (!$screen) {
            return false;
        }
        
        // Check if we're on a DealerEdge admin page
        $dealeredge_pages = array(
            'toplevel_page_dealeredge',
            'dealeredge_page_dealeredge-settings',
            'de_vehicle',
            'edit-de_vehicle',
            'de_customer', 
            'edit-de_customer',
            'de_work_order',
            'edit-de_work_order',
            'de_sale',
            'edit-de_sale',
            'de_part',
            'edit-de_part',
        );
        
        return in_array($screen->id, $dealeredge_pages) || 
               strpos($screen->id, 'dealeredge') !== false ||
               strpos($screen->post_type, 'de_') === 0;
    }
}