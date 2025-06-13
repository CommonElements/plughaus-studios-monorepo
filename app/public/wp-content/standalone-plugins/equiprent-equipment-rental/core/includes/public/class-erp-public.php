<?php
/**
 * The public-facing functionality of the plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * The public-facing functionality of the plugin.
 */
class ERP_Public {

    /**
     * Initialize the class and set its properties.
     */
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_erp_check_availability', array($this, 'ajax_check_availability'));
        add_action('wp_ajax_nopriv_erp_check_availability', array($this, 'ajax_check_availability'));
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            'equiprent-pro-public',
            ERP_PLUGIN_URL . 'assets/css/public.css',
            array(),
            ERP_VERSION,
            'all'
        );
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            'equiprent-pro-public',
            ERP_PLUGIN_URL . 'assets/js/public.js',
            array('jquery'),
            ERP_VERSION,
            false
        );

        // Localize script for AJAX
        wp_localize_script('equiprent-pro-public', 'erp_public_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('erp_public_nonce'),
            'messages' => array(
                'loading' => __('Loading...', 'equiprent-pro'),
                'error' => __('An error occurred. Please try again.', 'equiprent-pro'),
                'equipment_unavailable' => __('Selected equipment is not available for the chosen dates.', 'equiprent-pro'),
            )
        ));
    }

    /**
     * AJAX handler for checking equipment availability
     */
    public function ajax_check_availability() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'erp_public_nonce')) {
            wp_die(__('Security check failed.', 'equiprent-pro'));
        }

        $equipment_id = intval($_POST['equipment_id']);
        $start_date = sanitize_text_field($_POST['start_date']);
        $end_date = sanitize_text_field($_POST['end_date']);

        if (!$equipment_id || !$start_date || !$end_date) {
            wp_send_json_error(__('Missing required fields.', 'equiprent-pro'));
        }

        $is_available = ERP_Utilities::check_equipment_availability($equipment_id, $start_date, $end_date);

        if ($is_available) {
            wp_send_json_success(array(
                'available' => true,
                'message' => __('Equipment is available for the selected dates.', 'equiprent-pro')
            ));
        } else {
            wp_send_json_error(__('Equipment is not available for the selected dates.', 'equiprent-pro'));
        }
    }
}