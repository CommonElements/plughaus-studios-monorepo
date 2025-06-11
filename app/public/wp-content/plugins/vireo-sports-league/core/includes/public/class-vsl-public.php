<?php
/**
 * Public-facing functionality for Vireo Sports League
 * 
 * @package Vireo_Sports_League
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class VSL_Public {
    
    /**
     * Initialize public functionality
     */
    public function __construct() {
        add_action('init', array($this, 'init'));
    }
    
    /**
     * Initialize
     */
    public function init() {
        add_filter('template_include', array($this, 'template_include'));
        add_action('wp_head', array($this, 'add_custom_css'));
    }
    
    /**
     * Enqueue public styles
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            'vsl-public',
            VIREO_LEAGUE_PLUGIN_URL . 'core/assets/css/public.css',
            array(),
            VIREO_LEAGUE_VERSION
        );
        
        // Add custom theme color
        $display_settings = VSL_Utilities::get_display_settings();
        if (!empty($display_settings['theme_color'])) {
            $custom_css = "
                .vsl-theme-color { color: {$display_settings['theme_color']} !important; }
                .vsl-theme-bg { background-color: {$display_settings['theme_color']} !important; }
                .vsl-theme-border { border-color: {$display_settings['theme_color']} !important; }
            ";
            wp_add_inline_style('vsl-public', $custom_css);
        }
    }
    
    /**
     * Enqueue public scripts
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            'vsl-public',
            VIREO_LEAGUE_PLUGIN_URL . 'core/assets/js/public.js',
            array('jquery'),
            VIREO_LEAGUE_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script('vsl-public', 'vsl_public', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('vsl_public_nonce'),
            'strings' => array(
                'loading' => __('Loading...', 'vireo-league'),
                'error' => __('An error occurred. Please try again.', 'vireo-league'),
                'no_data' => __('No data available.', 'vireo-league'),
            )
        ));
    }
    
    /**
     * Include custom templates
     */
    public function template_include($template) {
        if (is_singular('vsl_league')) {
            $custom_template = $this->locate_template('single-league.php');
            if ($custom_template) {
                return $custom_template;
            }
        }
        
        if (is_singular('vsl_team')) {
            $custom_template = $this->locate_template('single-team.php');
            if ($custom_template) {
                return $custom_template;
            }
        }
        
        if (is_singular('vsl_player')) {
            $custom_template = $this->locate_template('single-player.php');
            if ($custom_template) {
                return $custom_template;
            }
        }
        
        if (is_singular('vsl_match')) {
            $custom_template = $this->locate_template('single-match.php');
            if ($custom_template) {
                return $custom_template;
            }
        }
        
        if (is_post_type_archive('vsl_league')) {
            $custom_template = $this->locate_template('archive-league.php');
            if ($custom_template) {
                return $custom_template;
            }
        }
        
        return $template;
    }
    
    /**
     * Locate template file
     */
    private function locate_template($template_name) {
        // Check theme directory first
        $theme_template = locate_template($template_name);
        if ($theme_template) {
            return $theme_template;
        }
        
        // Check plugin templates directory
        $plugin_template = VIREO_LEAGUE_CORE_DIR . 'templates/' . $template_name;
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
        
        return false;
    }
    
    /**
     * Add custom CSS to head
     */
    public function add_custom_css() {
        $display_settings = VSL_Utilities::get_display_settings();
        
        if (!empty($display_settings['theme_color'])) {
            echo '<style type="text/css">';
            echo '.vsl-standings table th { background-color: ' . esc_attr($display_settings['theme_color']) . '; }';
            echo '.vsl-match-result.win { border-left: 3px solid ' . esc_attr($display_settings['theme_color']) . '; }';
            echo '</style>';
        }
    }
}