<?php
/**
 * Public-facing functionality for PlugHaus Property Management
 */

if (!defined('ABSPATH')) {
    exit;
}

class PHPM_Public {
    
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
        // Template hooks
        add_filter('single_template', array($this, 'property_single_template'));
        add_filter('archive_template', array($this, 'property_archive_template'));
        
        // Property display hooks
        add_filter('the_content', array($this, 'property_content_filter'));
    }
    
    /**
     * Enqueue public styles
     */
    public function enqueue_styles() {
        // Only load on property pages
        if (!is_singular('phpm_property') && !is_post_type_archive('phpm_property')) {
            return;
        }
        
        wp_enqueue_style(
            'phpm-public',
            PHPM_PLUGIN_URL . 'core/assets/css/public.css',
            array(),
            PHPM_VERSION
        );
    }
    
    /**
     * Enqueue public scripts
     */
    public function enqueue_scripts() {
        // Only load on property pages or pages with shortcodes
        $post = get_post();
        if (!is_singular('phpm_property') && !is_post_type_archive('phpm_property') && (!$post || !has_shortcode($post->post_content, 'phpm_'))) {
            return;
        }
        
        wp_enqueue_script(
            'phpm-public',
            PHPM_PLUGIN_URL . 'core/assets/js/public.js',
            array('jquery'),
            PHPM_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script('phpm-public', 'phpm_public', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('phpm_public_nonce'),
            'api_url' => home_url('/wp-json/phpm/v1/'),
        ));
    }
    
    /**
     * Property single template
     */
    public function property_single_template($template) {
        global $post;
        
        if ('phpm_property' === $post->post_type) {
            // Check theme for template
            $theme_template = locate_template(array('single-phpm_property.php'));
            if ($theme_template) {
                return $theme_template;
            }
            
            // Use plugin template
            $plugin_template = PHPM_PLUGIN_DIR . 'templates/single-property.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }
        
        return $template;
    }
    
    /**
     * Property archive template
     */
    public function property_archive_template($template) {
        if (is_post_type_archive('phpm_property')) {
            // Check theme for template
            $theme_template = locate_template(array('archive-phpm_property.php'));
            if ($theme_template) {
                return $theme_template;
            }
            
            // Use plugin template
            $plugin_template = PHPM_PLUGIN_DIR . 'templates/archive-property.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }
        
        return $template;
    }
    
    /**
     * Filter property content
     */
    public function property_content_filter($content) {
        if (!is_singular('phpm_property') || !in_the_loop() || !is_main_query()) {
            return $content;
        }
        
        global $post;
        
        // Get property details
        $address = get_post_meta($post->ID, '_phpm_property_address', true);
        $city = get_post_meta($post->ID, '_phpm_property_city', true);
        $state = get_post_meta($post->ID, '_phpm_property_state', true);
        $zip = get_post_meta($post->ID, '_phpm_property_zip', true);
        $units = get_post_meta($post->ID, '_phpm_property_units', true);
        
        // Get property type
        $property_types = wp_get_post_terms($post->ID, 'phpm_property_type', array('fields' => 'names'));
        $property_type = !empty($property_types) ? $property_types[0] : '';
        
        // Get amenities
        $amenities = wp_get_post_terms($post->ID, 'phpm_amenities', array('fields' => 'names'));
        
        // Build property details HTML
        $property_details = '<div class="phpm-property-details">';
        
        // Address
        if ($address || $city || $state || $zip) {
            $property_details .= '<div class="phpm-property-address">';
            $property_details .= '<h3>' . __('Location', 'plughaus-property') . '</h3>';
            $property_details .= '<p>';
            if ($address) {
                $property_details .= esc_html($address) . '<br>';
            }
            if ($city || $state || $zip) {
                $property_details .= esc_html($city);
                if ($city && $state) {
                    $property_details .= ', ';
                }
                $property_details .= esc_html($state) . ' ' . esc_html($zip);
            }
            $property_details .= '</p>';
            $property_details .= '</div>';
        }
        
        // Property info
        $property_details .= '<div class="phpm-property-info">';
        $property_details .= '<h3>' . __('Property Information', 'plughaus-property') . '</h3>';
        $property_details .= '<ul>';
        
        if ($property_type) {
            $property_details .= '<li><strong>' . __('Type:', 'plughaus-property') . '</strong> ' . esc_html($property_type) . '</li>';
        }
        
        if ($units) {
            $property_details .= '<li><strong>' . __('Units:', 'plughaus-property') . '</strong> ' . esc_html($units) . '</li>';
        }
        
        $property_details .= '</ul>';
        $property_details .= '</div>';
        
        // Amenities
        if (!empty($amenities)) {
            $property_details .= '<div class="phpm-property-amenities">';
            $property_details .= '<h3>' . __('Amenities', 'plughaus-property') . '</h3>';
            $property_details .= '<ul>';
            foreach ($amenities as $amenity) {
                $property_details .= '<li>' . esc_html($amenity) . '</li>';
            }
            $property_details .= '</ul>';
            $property_details .= '</div>';
        }
        
        // Available units
        $available_units = $this->get_available_units($post->ID);
        if (!empty($available_units)) {
            $property_details .= '<div class="phpm-available-units">';
            $property_details .= '<h3>' . __('Available Units', 'plughaus-property') . '</h3>';
            $property_details .= '<div class="phpm-units-grid">';
            
            foreach ($available_units as $unit) {
                $property_details .= '<div class="phpm-unit-card">';
                $property_details .= '<h4>' . esc_html($unit->post_title) . '</h4>';
                
                $unit_rent = get_post_meta($unit->ID, '_phpm_unit_rent', true);
                if ($unit_rent) {
                    $property_details .= '<p class="phpm-unit-rent">' . sprintf(__('$%s/month', 'plughaus-property'), number_format($unit_rent)) . '</p>';
                }
                
                $property_details .= '<a href="' . get_permalink($unit->ID) . '" class="phpm-unit-link">' . __('View Details', 'plughaus-property') . '</a>';
                $property_details .= '</div>';
            }
            
            $property_details .= '</div>';
            $property_details .= '</div>';
        }
        
        $property_details .= '</div>';
        
        // Add property details after content
        return $content . $property_details;
    }
    
    /**
     * Get available units for a property
     */
    private function get_available_units($property_id) {
        $args = array(
            'post_type' => 'phpm_unit',
            'post_status' => 'available',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_phpm_unit_property',
                    'value' => $property_id,
                    'compare' => '='
                )
            )
        );
        
        $query = new WP_Query($args);
        return $query->posts;
    }
}