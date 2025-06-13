<?php
/**
 * The public-facing functionality of the plugin
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class DE_Public {
    
    public function __construct() {
        // Constructor actions
    }
    
    public function enqueue_styles() {
        wp_enqueue_style(
            'dealeredge-public',
            DE_PLUGIN_URL . 'core/assets/css/public.css',
            array(),
            DE_VERSION,
            'all'
        );
    }
    
    public function enqueue_scripts() {
        wp_enqueue_script(
            'dealeredge-public',
            DE_PLUGIN_URL . 'core/assets/js/public.js',
            array('jquery'),
            DE_VERSION,
            false
        );
    }
}