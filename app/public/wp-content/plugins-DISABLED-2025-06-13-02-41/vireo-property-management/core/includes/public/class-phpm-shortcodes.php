<?php
/**
 * Frontend Shortcodes for PlugHaus Property Management
 * Comprehensive shortcode system for displaying property management content
 *
 * @package PlugHausPropertyManagement
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class PHPM_Shortcodes {
    
    /**
     * Initialize shortcodes
     */
    public static function init() {
        // Property listings and search
        add_shortcode('vmp_property_listings', array(__CLASS__, 'property_listings_shortcode'));
        add_shortcode('vmp_property_detail', array(__CLASS__, 'property_detail_shortcode'));
        add_shortcode('vmp_property_search', array(__CLASS__, 'property_search_shortcode'));
        
        // Tenant portal and dashboard
        add_shortcode('vmp_tenant_portal', array(__CLASS__, 'tenant_portal_shortcode'));
        add_shortcode('vmp_tenant_dashboard', array(__CLASS__, 'tenant_dashboard_shortcode'));
        
        // Forms
        add_shortcode('vmp_maintenance_request_form', array(__CLASS__, 'maintenance_request_form_shortcode'));
        add_shortcode('vmp_rental_application', array(__CLASS__, 'rental_application_shortcode'));
        add_shortcode('vmp_contact_form', array(__CLASS__, 'contact_form_shortcode'));
        
        // Process form submissions
        add_action('init', array(__CLASS__, 'process_form_submissions'));
        
        // Enqueue frontend assets
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_frontend_assets'));
    }
    
    /**
     * Enqueue frontend assets
     */
    public static function enqueue_frontend_assets() {
        wp_enqueue_style(
            'phpm-frontend',
            PHPM_PLUGIN_URL . 'core/assets/css/frontend.css',
            array(),
            PHPM_VERSION
        );
        
        wp_enqueue_script(
            'phpm-frontend',
            PHPM_PLUGIN_URL . 'core/assets/js/frontend.js',
            array('jquery'),
            PHPM_VERSION,
            true
        );
        
        wp_localize_script('phpm-frontend', 'phpmFrontend', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('phpm_frontend_nonce'),
            'strings' => array(
                'loading' => __('Loading...', 'plughaus-property'),
                'error' => __('An error occurred. Please try again.', 'plughaus-property'),
                'success' => __('Success!', 'plughaus-property'),
                'confirm' => __('Are you sure?', 'plughaus-property')
            )
        ));
    }
    
    /**
     * Property listings shortcode
     * [phpm_property_listings limit="12" type="apartment" show_search="true" show_filters="true"]
     */
    public static function property_listings_shortcode($atts) {
        $settings = PHPM_Frontend_Settings_Admin::get_frontend_settings();
        
        if (!$settings['enable_property_listings']) {
            return '<p>' . __('Property listings are currently disabled.', 'plughaus-property') . '</p>';
        }
        
        $atts = shortcode_atts(array(
            'limit' => $settings['properties_per_page'],
            'type' => '',
            'location' => '',
            'min_rent' => '',
            'max_rent' => '',
            'bedrooms' => '',
            'show_search' => 'false',
            'show_filters' => 'false',
            'layout' => 'grid', // grid or list
            'columns' => '3'
        ), $atts, 'phpm_property_listings');
        
        ob_start();
        
        // Search form
        if ($atts['show_search'] === 'true') {
            echo self::render_property_search_form($atts);
        }
        
        // Build query arguments
        $query_args = array(
            'post_type' => 'vmp_property',
            'post_status' => 'publish',
            'posts_per_page' => intval($atts['limit']),
            'meta_query' => array()
        );
        
        // Apply filters from URL parameters or shortcode attributes
        $filters = self::get_property_filters($atts);
        if (!empty($filters['meta_query'])) {
            $query_args['meta_query'] = array_merge($query_args['meta_query'], $filters['meta_query']);
        }
        if (!empty($filters['tax_query'])) {
            $query_args['tax_query'] = $filters['tax_query'];
        }
        
        // Show only available properties if setting is enabled
        if ($settings['show_available_only']) {
            $query_args['meta_query'][] = array(
                'key' => '_vmp_availability_status',
                'value' => 'available',
                'compare' => '='
            );
        }
        
        $properties = new WP_Query($query_args);
        
        ?>
        <div class="phpm-property-listings" data-layout="<?php echo esc_attr($atts['layout']); ?>" data-columns="<?php echo esc_attr($atts['columns']); ?>">
            
            <?php if ($atts['show_filters'] === 'true') : ?>
                <div class="phpm-filters">
                    <?php echo self::render_property_filters(); ?>
                </div>
            <?php endif; ?>
            
            <div class="phpm-properties-container">
                <?php if ($properties->have_posts()) : ?>
                    
                    <div class="phpm-properties-grid columns-<?php echo esc_attr($atts['columns']); ?>">
                        <?php while ($properties->have_posts()) : $properties->the_post(); ?>
                            <div class="phpm-property-card">
                                <?php echo self::render_property_card(get_the_ID(), $settings); ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    
                    <?php echo self::render_pagination($properties); ?>
                    
                <?php else : ?>
                    <div class="phpm-no-properties">
                        <p><?php _e('No properties found matching your criteria.', 'plughaus-property'); ?></p>
                        <?php if (!empty($_GET)) : ?>
                            <a href="<?php echo remove_query_arg(array('property_type', 'location', 'min_rent', 'max_rent', 'bedrooms')); ?>" class="phpm-button">
                                <?php _e('Clear Filters', 'plughaus-property'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        
        wp_reset_postdata();
        return ob_get_clean();
    }
    
    /**
     * Property detail shortcode
     * [phpm_property_detail id="123" show_map="true" show_units="true"]
     */
    public static function property_detail_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => get_the_ID(),
            'show_map' => 'true',
            'show_units' => 'true',
            'show_contact' => 'true'
        ), $atts, 'phpm_property_detail');
        
        $property_id = intval($atts['id']);
        if (!$property_id) {
            return '<p>' . __('Invalid property ID.', 'plughaus-property') . '</p>';
        }
        
        $property = get_post($property_id);
        if (!$property || $property->post_type !== 'vmp_property') {
            return '<p>' . __('Property not found.', 'plughaus-property') . '</p>';
        }
        
        $settings = PHPM_Frontend_Settings_Admin::get_frontend_settings();
        
        ob_start();
        ?>
        <div class="phpm-property-detail">
            
            <!-- Property Header -->
            <div class="phpm-property-header">
                <h1 class="phpm-property-title"><?php echo esc_html($property->post_title); ?></h1>
                <div class="phpm-property-meta">
                    <?php echo self::render_property_meta($property_id); ?>
                </div>
            </div>
            
            <!-- Property Gallery -->
            <?php if ($settings['show_property_images']) : ?>
                <div class="phpm-property-gallery">
                    <?php echo self::render_property_gallery($property_id); ?>
                </div>
            <?php endif; ?>
            
            <!-- Property Details -->
            <div class="phpm-property-content">
                <div class="phpm-property-description">
                    <h3><?php _e('Property Description', 'plughaus-property'); ?></h3>
                    <?php echo wpautop($property->post_content); ?>
                </div>
                
                <div class="phpm-property-features">
                    <h3><?php _e('Property Features', 'plughaus-property'); ?></h3>
                    <?php echo self::render_property_features($property_id); ?>
                </div>
                
                <div class="phpm-property-amenities">
                    <h3><?php _e('Amenities', 'plughaus-property'); ?></h3>
                    <?php echo self::render_property_amenities($property_id); ?>
                </div>
            </div>
            
            <!-- Available Units -->
            <?php if ($atts['show_units'] === 'true') : ?>
                <div class="phpm-property-units">
                    <h3><?php _e('Available Units', 'plughaus-property'); ?></h3>
                    <?php echo self::render_available_units($property_id); ?>
                </div>
            <?php endif; ?>
            
            <!-- Property Map -->
            <?php if ($atts['show_map'] === 'true' && $settings['show_property_map']) : ?>
                <div class="phpm-property-map">
                    <h3><?php _e('Location', 'plughaus-property'); ?></h3>
                    <?php echo self::render_property_map($property_id, $settings); ?>
                </div>
            <?php endif; ?>
            
            <!-- Contact Section -->
            <?php if ($atts['show_contact'] === 'true') : ?>
                <div class="phpm-property-contact">
                    <h3><?php _e('Interested in This Property?', 'plughaus-property'); ?></h3>
                    <?php echo self::render_property_contact_form($property_id); ?>
                </div>
            <?php endif; ?>
            
        </div>
        <?php
        
        return ob_get_clean();
    }
    
    /**
     * Property search shortcode
     * [phpm_property_search style="horizontal" show_results="true"]
     */
    public static function property_search_shortcode($atts) {
        $settings = PHPM_Frontend_Settings_Admin::get_frontend_settings();
        
        if (!$settings['enable_public_search']) {
            return '<p>' . __('Property search is currently disabled.', 'plughaus-property') . '</p>';
        }
        
        $atts = shortcode_atts(array(
            'style' => 'vertical', // vertical, horizontal
            'show_results' => 'false',
            'results_page' => $settings['property_listing_page']
        ), $atts, 'phpm_property_search');
        
        ob_start();
        ?>
        <div class="phpm-property-search-form <?php echo esc_attr($atts['style']); ?>">
            <form method="get" action="<?php echo $atts['results_page'] ? get_permalink($atts['results_page']) : ''; ?>" class="phpm-search-form">
                
                <div class="phpm-search-fields">
                    
                    <!-- Location -->
                    <div class="phpm-search-field">
                        <label for="phpm-search-location"><?php _e('Location', 'plughaus-property'); ?></label>
                        <select name="location" id="phpm-search-location">
                            <option value=""><?php _e('Any Location', 'plughaus-property'); ?></option>
                            <?php
                            $locations = get_terms(array(
                                'taxonomy' => 'vmp_location',
                                'hide_empty' => true
                            ));
                            foreach ($locations as $location) {
                                $selected = isset($_GET['location']) && $_GET['location'] === $location->slug ? 'selected' : '';
                                echo '<option value="' . esc_attr($location->slug) . '" ' . $selected . '>' . esc_html($location->name) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    
                    <!-- Property Type -->
                    <div class="phpm-search-field">
                        <label for="phpm-search-type"><?php _e('Property Type', 'plughaus-property'); ?></label>
                        <select name="property_type" id="phpm-search-type">
                            <option value=""><?php _e('Any Type', 'plughaus-property'); ?></option>
                            <?php
                            $types = get_terms(array(
                                'taxonomy' => 'vmp_property_type',
                                'hide_empty' => true
                            ));
                            foreach ($types as $type) {
                                $selected = isset($_GET['property_type']) && $_GET['property_type'] === $type->slug ? 'selected' : '';
                                echo '<option value="' . esc_attr($type->slug) . '" ' . $selected . '>' . esc_html($type->name) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    
                    <!-- Bedrooms -->
                    <div class="phpm-search-field">
                        <label for="phpm-search-bedrooms"><?php _e('Bedrooms', 'plughaus-property'); ?></label>
                        <select name="bedrooms" id="phpm-search-bedrooms">
                            <option value=""><?php _e('Any', 'plughaus-property'); ?></option>
                            <?php for ($i = 1; $i <= 5; $i++) : 
                                $selected = isset($_GET['bedrooms']) && $_GET['bedrooms'] == $i ? 'selected' : '';
                            ?>
                                <option value="<?php echo $i; ?>" <?php echo $selected; ?>><?php echo $i; ?>+</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <!-- Price Range -->
                    <div class="phpm-search-field phpm-price-range">
                        <label><?php _e('Price Range', 'plughaus-property'); ?></label>
                        <div class="phpm-price-inputs">
                            <input type="number" name="min_rent" placeholder="<?php _e('Min', 'plughaus-property'); ?>" value="<?php echo esc_attr($_GET['min_rent'] ?? ''); ?>">
                            <span>-</span>
                            <input type="number" name="max_rent" placeholder="<?php _e('Max', 'plughaus-property'); ?>" value="<?php echo esc_attr($_GET['max_rent'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="phpm-search-submit">
                        <button type="submit" class="phpm-button phpm-button-primary">
                            <span class="dashicons dashicons-search"></span>
                            <?php _e('Search Properties', 'plughaus-property'); ?>
                        </button>
                    </div>
                    
                </div>
                
            </form>
        </div>
        
        <?php if ($atts['show_results'] === 'true' && !empty($_GET)) : ?>
            <div class="phpm-search-results">
                <h3><?php _e('Search Results', 'plughaus-property'); ?></h3>
                <?php echo self::property_listings_shortcode(array('limit' => 12, 'show_search' => 'false')); ?>
            </div>
        <?php endif; ?>
        <?php
        
        return ob_get_clean();
    }
    
    /**
     * Tenant portal shortcode
     * [phpm_tenant_portal show_payments="true" show_documents="true" show_maintenance="true"]
     */
    public static function tenant_portal_shortcode($atts) {
        $settings = PHPM_Frontend_Settings_Admin::get_frontend_settings();
        
        if (!$settings['enable_tenant_portal']) {
            return '<p>' . __('Tenant portal is currently disabled.', 'plughaus-property') . '</p>';
        }
        
        if ($settings['require_login_for_portal'] && !is_user_logged_in()) {
            return '<div class="phpm-login-required">
                        <p>' . __('Please log in to access the tenant portal.', 'plughaus-property') . '</p>
                        ' . wp_login_form(array('echo' => false, 'redirect' => get_permalink())) . '
                    </div>';
        }
        
        $atts = shortcode_atts(array(
            'show_payments' => 'true',
            'show_documents' => 'true',
            'show_maintenance' => 'true'
        ), $atts, 'phpm_tenant_portal');
        
        $tenant = self::get_current_tenant();
        if (!$tenant) {
            return '<p>' . __('No tenant account found for your user.', 'plughaus-property') . '</p>';
        }
        
        ob_start();
        ?>
        <div class="phpm-tenant-portal">
            
            <!-- Portal Header -->
            <div class="phpm-portal-header">
                <h2><?php _e('Tenant Portal', 'plughaus-property'); ?></h2>
                <p class="phpm-portal-welcome">
                    <?php printf(__('Welcome back, %s!', 'plughaus-property'), esc_html(wp_get_current_user()->display_name)); ?>
                </p>
            </div>
            
            <!-- Quick Actions -->
            <div class="phpm-portal-actions">
                <div class="phpm-action-buttons">
                    <?php if ($settings['enable_maintenance_requests']) : ?>
                        <a href="#maintenance-form" class="phpm-button phpm-button-primary" data-toggle="phpm-maintenance-form">
                            <span class="dashicons dashicons-admin-tools"></span>
                            <?php _e('Submit Maintenance Request', 'plughaus-property'); ?>
                        </a>
                    <?php endif; ?>
                    
                    <a href="#lease-info" class="phpm-button phpm-button-secondary">
                        <span class="dashicons dashicons-media-document"></span>
                        <?php _e('View Lease Details', 'plughaus-property'); ?>
                    </a>
                </div>
            </div>
            
            <!-- Portal Content -->
            <div class="phpm-portal-content">
                
                <!-- Lease Information -->
                <div class="phpm-portal-section" id="lease-info">
                    <h3><span class="dashicons dashicons-admin-home"></span> <?php _e('Your Lease', 'plughaus-property'); ?></h3>
                    <?php echo self::render_tenant_lease_info($tenant->ID); ?>
                </div>
                
                <!-- Maintenance Requests -->
                <?php if ($atts['show_maintenance'] === 'true') : ?>
                    <div class="phpm-portal-section" id="maintenance-requests">
                        <h3><span class="dashicons dashicons-admin-tools"></span> <?php _e('Maintenance Requests', 'plughaus-property'); ?></h3>
                        <?php echo self::render_tenant_maintenance_requests($tenant->ID); ?>
                    </div>
                <?php endif; ?>
                
                <!-- Payment History -->
                <?php if ($atts['show_payments'] === 'true') : ?>
                    <div class="phpm-portal-section" id="payment-history">
                        <h3><span class="dashicons dashicons-money"></span> <?php _e('Payment History', 'plughaus-property'); ?></h3>
                        <?php echo self::render_tenant_payment_history($tenant->ID); ?>
                    </div>
                <?php endif; ?>
                
                <!-- Documents -->
                <?php if ($atts['show_documents'] === 'true') : ?>
                    <div class="phpm-portal-section" id="documents">
                        <h3><span class="dashicons dashicons-media-document"></span> <?php _e('Documents', 'plughaus-property'); ?></h3>
                        <?php echo self::render_tenant_documents($tenant->ID); ?>
                    </div>
                <?php endif; ?>
                
            </div>
            
        </div>
        
        <!-- Maintenance Request Modal -->
        <?php if ($settings['enable_maintenance_requests']) : ?>
            <div id="phpm-maintenance-modal" class="phpm-modal" style="display: none;">
                <div class="phpm-modal-content">
                    <span class="phpm-modal-close">&times;</span>
                    <h3><?php _e('Submit Maintenance Request', 'plughaus-property'); ?></h3>
                    <?php echo self::maintenance_request_form_shortcode(array('show_history' => 'false')); ?>
                </div>
            </div>
        <?php endif; ?>
        <?php
        
        return ob_get_clean();
    }
    
    /**
     * Tenant dashboard shortcode (more comprehensive than portal)
     * [phpm_tenant_dashboard]
     */
    public static function tenant_dashboard_shortcode($atts) {
        $settings = PHPM_Frontend_Settings_Admin::get_frontend_settings();
        
        if (!$settings['enable_tenant_portal'] || !is_user_logged_in()) {
            return self::tenant_portal_shortcode($atts);
        }
        
        $tenant = self::get_current_tenant();
        if (!$tenant) {
            return '<p>' . __('No tenant account found.', 'plughaus-property') . '</p>';
        }
        
        ob_start();
        ?>
        <div class="phpm-tenant-dashboard">
            
            <!-- Dashboard Header -->
            <div class="phpm-dashboard-header">
                <h2><?php _e('Tenant Dashboard', 'plughaus-property'); ?></h2>
                <div class="phpm-dashboard-stats">
                    <?php echo self::render_tenant_dashboard_stats($tenant->ID); ?>
                </div>
            </div>
            
            <!-- Dashboard Grid -->
            <div class="phpm-dashboard-grid">
                
                <!-- Current Lease Widget -->
                <div class="phpm-dashboard-widget phpm-lease-widget">
                    <h3><?php _e('Current Lease', 'plughaus-property'); ?></h3>
                    <?php echo self::render_tenant_lease_info($tenant->ID); ?>
                </div>
                
                <!-- Recent Maintenance Widget -->
                <div class="phpm-dashboard-widget phpm-maintenance-widget">
                    <h3><?php _e('Recent Maintenance', 'plughaus-property'); ?></h3>
                    <?php echo self::render_tenant_maintenance_requests($tenant->ID, 3); ?>
                    <div class="phpm-widget-footer">
                        <a href="#" class="phpm-button" data-toggle="phpm-maintenance-form">
                            <?php _e('Submit New Request', 'plughaus-property'); ?>
                        </a>
                    </div>
                </div>
                
                <!-- Payment Status Widget -->
                <div class="phpm-dashboard-widget phpm-payment-widget">
                    <h3><?php _e('Payment Status', 'plughaus-property'); ?></h3>
                    <?php echo self::render_tenant_payment_status($tenant->ID); ?>
                </div>
                
                <!-- Quick Actions Widget -->
                <div class="phpm-dashboard-widget phpm-actions-widget">
                    <h3><?php _e('Quick Actions', 'plughaus-property'); ?></h3>
                    <div class="phpm-quick-actions">
                        <button class="phpm-action-btn" data-action="pay-rent">
                            <span class="dashicons dashicons-money"></span>
                            <?php _e('Pay Rent', 'plughaus-property'); ?>
                        </button>
                        <button class="phpm-action-btn" data-action="maintenance">
                            <span class="dashicons dashicons-admin-tools"></span>
                            <?php _e('Maintenance', 'plughaus-property'); ?>
                        </button>
                        <button class="phpm-action-btn" data-action="contact">
                            <span class="dashicons dashicons-email"></span>
                            <?php _e('Contact Manager', 'plughaus-property'); ?>
                        </button>
                    </div>
                </div>
                
            </div>
            
        </div>
        <?php
        
        return ob_get_clean();
    }
    
    /**
     * Maintenance request form shortcode
     * [phpm_maintenance_request_form show_history="true" allow_images="true"]
     */
    public static function maintenance_request_form_shortcode($atts) {
        $settings = PHPM_Frontend_Settings_Admin::get_frontend_settings();
        
        if (!$settings['enable_maintenance_requests']) {
            return '<p>' . __('Maintenance requests are currently disabled.', 'plughaus-property') . '</p>';
        }
        
        if (!is_user_logged_in()) {
            return '<p>' . __('Please log in to submit a maintenance request.', 'plughaus-property') . '</p>';
        }
        
        $atts = shortcode_atts(array(
            'show_history' => 'true',
            'allow_images' => 'true'
        ), $atts, 'phpm_maintenance_request_form');
        
        $tenant = self::get_current_tenant();
        if (!$tenant) {
            return '<p>' . __('No tenant account found.', 'plughaus-property') . '</p>';
        }
        
        ob_start();
        ?>
        <div class="phpm-maintenance-request-container">
            
            <!-- Request Form -->
            <form class="phpm-maintenance-form" method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('phpm_submit_maintenance_request', 'phpm_maintenance_nonce'); ?>
                <input type="hidden" name="action" value="phpm_submit_maintenance_request">
                <input type="hidden" name="tenant_id" value="<?php echo esc_attr($tenant->ID); ?>">
                
                <div class="phpm-form-row">
                    <div class="phpm-form-field">
                        <label for="maintenance_title"><?php _e('Title', 'plughaus-property'); ?> <span class="required">*</span></label>
                        <input type="text" id="maintenance_title" name="maintenance_title" required placeholder="<?php _e('Brief description of the issue', 'plughaus-property'); ?>">
                    </div>
                </div>
                
                <div class="phpm-form-row">
                    <div class="phpm-form-field">
                        <label for="maintenance_category"><?php _e('Category', 'plughaus-property'); ?></label>
                        <select id="maintenance_category" name="maintenance_category">
                            <option value=""><?php _e('Select Category', 'plughaus-property'); ?></option>
                            <option value="plumbing"><?php _e('Plumbing', 'plughaus-property'); ?></option>
                            <option value="electrical"><?php _e('Electrical', 'plughaus-property'); ?></option>
                            <option value="hvac"><?php _e('HVAC', 'plughaus-property'); ?></option>
                            <option value="appliance"><?php _e('Appliance', 'plughaus-property'); ?></option>
                            <option value="flooring"><?php _e('Flooring', 'plughaus-property'); ?></option>
                            <option value="exterior"><?php _e('Exterior', 'plughaus-property'); ?></option>
                            <option value="other"><?php _e('Other', 'plughaus-property'); ?></option>
                        </select>
                    </div>
                    
                    <div class="phpm-form-field">
                        <label for="maintenance_priority"><?php _e('Priority', 'plughaus-property'); ?></label>
                        <select id="maintenance_priority" name="maintenance_priority">
                            <option value="low"><?php _e('Low', 'plughaus-property'); ?></option>
                            <option value="normal" selected><?php _e('Normal', 'plughaus-property'); ?></option>
                            <option value="high"><?php _e('High', 'plughaus-property'); ?></option>
                            <option value="emergency"><?php _e('Emergency', 'plughaus-property'); ?></option>
                        </select>
                    </div>
                </div>
                
                <div class="phpm-form-row">
                    <div class="phpm-form-field">
                        <label for="maintenance_description"><?php _e('Description', 'plughaus-property'); ?> <span class="required">*</span></label>
                        <textarea id="maintenance_description" name="maintenance_description" rows="5" required placeholder="<?php _e('Please provide detailed information about the issue...', 'plughaus-property'); ?>"></textarea>
                    </div>
                </div>
                
                <?php if ($atts['allow_images'] === 'true') : ?>
                    <div class="phpm-form-row">
                        <div class="phpm-form-field">
                            <label for="maintenance_images"><?php _e('Photos (Optional)', 'plughaus-property'); ?></label>
                            <input type="file" id="maintenance_images" name="maintenance_images[]" multiple accept="image/*">
                            <p class="phpm-field-help"><?php _e('Upload photos to help us understand the issue (max 5 images, 5MB each)', 'plughaus-property'); ?></p>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="phpm-form-submit">
                    <button type="submit" class="phpm-button phpm-button-primary">
                        <span class="dashicons dashicons-upload"></span>
                        <?php _e('Submit Request', 'plughaus-property'); ?>
                    </button>
                </div>
                
            </form>
            
            <!-- Request History -->
            <?php if ($atts['show_history'] === 'true') : ?>
                <div class="phpm-maintenance-history">
                    <h3><?php _e('Your Recent Requests', 'plughaus-property'); ?></h3>
                    <?php echo self::render_tenant_maintenance_requests($tenant->ID, 10); ?>
                </div>
            <?php endif; ?>
            
        </div>
        <?php
        
        return ob_get_clean();
    }
    
    /**
     * Rental application shortcode
     * [phpm_rental_application]
     */
    public static function rental_application_shortcode($atts) {
        $settings = PHPM_Frontend_Settings_Admin::get_frontend_settings();
        
        if (!$settings['allow_online_applications']) {
            return '<p>' . __('Online applications are currently disabled. Please contact us directly.', 'plughaus-property') . '</p>';
        }
        
        $atts = shortcode_atts(array(
            'property_id' => ''
        ), $atts, 'phpm_rental_application');
        
        ob_start();
        ?>
        <div class="phpm-rental-application">
            
            <form class="phpm-application-form" method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('phpm_submit_rental_application', 'phpm_application_nonce'); ?>
                <input type="hidden" name="action" value="phpm_submit_rental_application">
                
                <?php if ($atts['property_id']) : ?>
                    <input type="hidden" name="property_id" value="<?php echo esc_attr($atts['property_id']); ?>">
                <?php endif; ?>
                
                <!-- Personal Information -->
                <div class="phpm-form-section">
                    <h3><?php _e('Personal Information', 'plughaus-property'); ?></h3>
                    
                    <div class="phpm-form-row">
                        <div class="phpm-form-field">
                            <label for="first_name"><?php _e('First Name', 'plughaus-property'); ?> <span class="required">*</span></label>
                            <input type="text" id="first_name" name="first_name" required>
                        </div>
                        <div class="phpm-form-field">
                            <label for="last_name"><?php _e('Last Name', 'plughaus-property'); ?> <span class="required">*</span></label>
                            <input type="text" id="last_name" name="last_name" required>
                        </div>
                    </div>
                    
                    <div class="phpm-form-row">
                        <div class="phpm-form-field">
                            <label for="email"><?php _e('Email Address', 'plughaus-property'); ?> <span class="required">*</span></label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="phpm-form-field">
                            <label for="phone"><?php _e('Phone Number', 'plughaus-property'); ?> <span class="required">*</span></label>
                            <input type="tel" id="phone" name="phone" required>
                        </div>
                    </div>
                    
                    <div class="phpm-form-row">
                        <div class="phpm-form-field">
                            <label for="date_of_birth"><?php _e('Date of Birth', 'plughaus-property'); ?> <span class="required">*</span></label>
                            <input type="date" id="date_of_birth" name="date_of_birth" required>
                        </div>
                        <div class="phpm-form-field">
                            <label for="ssn"><?php _e('Social Security Number', 'plughaus-property'); ?> <span class="required">*</span></label>
                            <input type="text" id="ssn" name="ssn" required placeholder="XXX-XX-XXXX">
                        </div>
                    </div>
                </div>
                
                <!-- Current Address -->
                <div class="phpm-form-section">
                    <h3><?php _e('Current Address', 'plughaus-property'); ?></h3>
                    
                    <div class="phpm-form-row">
                        <div class="phpm-form-field">
                            <label for="current_address"><?php _e('Street Address', 'plughaus-property'); ?> <span class="required">*</span></label>
                            <input type="text" id="current_address" name="current_address" required>
                        </div>
                    </div>
                    
                    <div class="phpm-form-row">
                        <div class="phpm-form-field">
                            <label for="current_city"><?php _e('City', 'plughaus-property'); ?> <span class="required">*</span></label>
                            <input type="text" id="current_city" name="current_city" required>
                        </div>
                        <div class="phpm-form-field">
                            <label for="current_state"><?php _e('State', 'plughaus-property'); ?> <span class="required">*</span></label>
                            <input type="text" id="current_state" name="current_state" required>
                        </div>
                        <div class="phpm-form-field">
                            <label for="current_zip"><?php _e('ZIP Code', 'plughaus-property'); ?> <span class="required">*</span></label>
                            <input type="text" id="current_zip" name="current_zip" required>
                        </div>
                    </div>
                    
                    <div class="phpm-form-row">
                        <div class="phpm-form-field">
                            <label for="current_rent"><?php _e('Current Monthly Rent', 'plughaus-property'); ?></label>
                            <input type="number" id="current_rent" name="current_rent" step="0.01">
                        </div>
                        <div class="phpm-form-field">
                            <label for="move_date"><?php _e('Desired Move Date', 'plughaus-property'); ?> <span class="required">*</span></label>
                            <input type="date" id="move_date" name="move_date" required>
                        </div>
                    </div>
                </div>
                
                <!-- Employment Information -->
                <div class="phpm-form-section">
                    <h3><?php _e('Employment Information', 'plughaus-property'); ?></h3>
                    
                    <div class="phpm-form-row">
                        <div class="phpm-form-field">
                            <label for="employer"><?php _e('Employer', 'plughaus-property'); ?> <span class="required">*</span></label>
                            <input type="text" id="employer" name="employer" required>
                        </div>
                        <div class="phpm-form-field">
                            <label for="job_title"><?php _e('Job Title', 'plughaus-property'); ?> <span class="required">*</span></label>
                            <input type="text" id="job_title" name="job_title" required>
                        </div>
                    </div>
                    
                    <div class="phpm-form-row">
                        <div class="phpm-form-field">
                            <label for="monthly_income"><?php _e('Monthly Income', 'plughaus-property'); ?> <span class="required">*</span></label>
                            <input type="number" id="monthly_income" name="monthly_income" step="0.01" required>
                        </div>
                        <div class="phpm-form-field">
                            <label for="employment_length"><?php _e('Length of Employment', 'plughaus-property'); ?></label>
                            <input type="text" id="employment_length" name="employment_length" placeholder="e.g., 2 years">
                        </div>
                    </div>
                </div>
                
                <!-- References -->
                <div class="phpm-form-section">
                    <h3><?php _e('References', 'plughaus-property'); ?></h3>
                    
                    <div class="phpm-form-row">
                        <div class="phpm-form-field">
                            <label for="emergency_contact_name"><?php _e('Emergency Contact Name', 'plughaus-property'); ?> <span class="required">*</span></label>
                            <input type="text" id="emergency_contact_name" name="emergency_contact_name" required>
                        </div>
                        <div class="phpm-form-field">
                            <label for="emergency_contact_phone"><?php _e('Emergency Contact Phone', 'plughaus-property'); ?> <span class="required">*</span></label>
                            <input type="tel" id="emergency_contact_phone" name="emergency_contact_phone" required>
                        </div>
                    </div>
                    
                    <div class="phpm-form-row">
                        <div class="phpm-form-field">
                            <label for="emergency_contact_relationship"><?php _e('Relationship', 'plughaus-property'); ?></label>
                            <input type="text" id="emergency_contact_relationship" name="emergency_contact_relationship" placeholder="e.g., Parent, Sibling">
                        </div>
                    </div>
                </div>
                
                <!-- Consent and Agreement -->
                <div class="phpm-form-section">
                    <h3><?php _e('Consent and Agreement', 'plughaus-property'); ?></h3>
                    
                    <div class="phpm-form-field phpm-checkbox-field">
                        <label>
                            <input type="checkbox" name="consent_background_check" value="1" required>
                            <?php _e('I consent to a background and credit check', 'plughaus-property'); ?> <span class="required">*</span>
                        </label>
                    </div>
                    
                    <div class="phpm-form-field phpm-checkbox-field">
                        <label>
                            <input type="checkbox" name="agree_terms" value="1" required>
                            <?php _e('I agree to the terms and conditions', 'plughaus-property'); ?> <span class="required">*</span>
                        </label>
                    </div>
                    
                    <div class="phpm-form-field phpm-checkbox-field">
                        <label>
                            <input type="checkbox" name="information_accurate" value="1" required>
                            <?php _e('I certify that all information provided is accurate and complete', 'plughaus-property'); ?> <span class="required">*</span>
                        </label>
                    </div>
                </div>
                
                <div class="phpm-form-submit">
                    <button type="submit" class="phpm-button phpm-button-primary phpm-button-large">
                        <span class="dashicons dashicons-yes"></span>
                        <?php _e('Submit Application', 'plughaus-property'); ?>
                    </button>
                    <p class="phpm-form-note">
                        <?php _e('Application processing typically takes 24-48 hours. You will be contacted with a decision.', 'plughaus-property'); ?>
                    </p>
                </div>
                
            </form>
            
        </div>
        <?php
        
        return ob_get_clean();
    }
    
    /**
     * Contact form shortcode
     * [phpm_contact_form property_id="123" show_property_select="true"]
     */
    public static function contact_form_shortcode($atts) {
        $atts = shortcode_atts(array(
            'property_id' => '',
            'show_property_select' => 'false'
        ), $atts, 'phpm_contact_form');
        
        ob_start();
        ?>
        <div class="phpm-contact-form-container">
            
            <form class="phpm-contact-form" method="post">
                <?php wp_nonce_field('phpm_submit_contact_form', 'phpm_contact_nonce'); ?>
                <input type="hidden" name="action" value="phpm_submit_contact_form">
                
                <?php if ($atts['show_property_select'] === 'true' && !$atts['property_id']) : ?>
                    <div class="phpm-form-field">
                        <label for="property_inquiry"><?php _e('Property of Interest', 'plughaus-property'); ?></label>
                        <select id="property_inquiry" name="property_inquiry">
                            <option value=""><?php _e('Select a property (optional)', 'plughaus-property'); ?></option>
                            <?php
                            $properties = get_posts(array(
                                'post_type' => 'vmp_property',
                                'post_status' => 'publish',
                                'numberposts' => -1
                            ));
                            foreach ($properties as $property) {
                                echo '<option value="' . esc_attr($property->ID) . '">' . esc_html($property->post_title) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                <?php elseif ($atts['property_id']) : ?>
                    <input type="hidden" name="property_inquiry" value="<?php echo esc_attr($atts['property_id']); ?>">
                    <div class="phpm-inquiry-property">
                        <p><strong><?php _e('Inquiring about:', 'plughaus-property'); ?></strong> <?php echo esc_html(get_the_title($atts['property_id'])); ?></p>
                    </div>
                <?php endif; ?>
                
                <div class="phpm-form-row">
                    <div class="phpm-form-field">
                        <label for="contact_name"><?php _e('Name', 'plughaus-property'); ?> <span class="required">*</span></label>
                        <input type="text" id="contact_name" name="contact_name" required>
                    </div>
                    
                    <div class="phpm-form-field">
                        <label for="contact_email"><?php _e('Email', 'plughaus-property'); ?> <span class="required">*</span></label>
                        <input type="email" id="contact_email" name="contact_email" required>
                    </div>
                </div>
                
                <div class="phpm-form-row">
                    <div class="phpm-form-field">
                        <label for="contact_phone"><?php _e('Phone', 'plughaus-property'); ?></label>
                        <input type="tel" id="contact_phone" name="contact_phone">
                    </div>
                    
                    <div class="phpm-form-field">
                        <label for="contact_subject"><?php _e('Subject', 'plughaus-property'); ?></label>
                        <select id="contact_subject" name="contact_subject">
                            <option value="general"><?php _e('General Inquiry', 'plughaus-property'); ?></option>
                            <option value="rental"><?php _e('Rental Information', 'plughaus-property'); ?></option>
                            <option value="application"><?php _e('Application Status', 'plughaus-property'); ?></option>
                            <option value="maintenance"><?php _e('Maintenance Issue', 'plughaus-property'); ?></option>
                            <option value="other"><?php _e('Other', 'plughaus-property'); ?></option>
                        </select>
                    </div>
                </div>
                
                <div class="phpm-form-field">
                    <label for="contact_message"><?php _e('Message', 'plughaus-property'); ?> <span class="required">*</span></label>
                    <textarea id="contact_message" name="contact_message" rows="5" required placeholder="<?php _e('Please provide details about your inquiry...', 'plughaus-property'); ?>"></textarea>
                </div>
                
                <div class="phpm-form-submit">
                    <button type="submit" class="phpm-button phpm-button-primary">
                        <span class="dashicons dashicons-email"></span>
                        <?php _e('Send Message', 'plughaus-property'); ?>
                    </button>
                </div>
                
            </form>
            
        </div>
        <?php
        
        return ob_get_clean();
    }
    
    /**
     * Process form submissions
     */
    public static function process_form_submissions() {
        // Process maintenance request
        if (isset($_POST['action']) && $_POST['action'] === 'phpm_submit_maintenance_request') {
            self::process_maintenance_request();
        }
        
        // Process rental application
        if (isset($_POST['action']) && $_POST['action'] === 'phpm_submit_rental_application') {
            self::process_rental_application();
        }
        
        // Process contact form
        if (isset($_POST['action']) && $_POST['action'] === 'phpm_submit_contact_form') {
            self::process_contact_form();
        }
    }
    
    /**
     * Helper functions for rendering components
     */
    
    private static function get_current_tenant() {
        if (!is_user_logged_in()) {
            return false;
        }
        
        $user_id = get_current_user_id();
        
        $tenant_query = new WP_Query(array(
            'post_type' => 'vmp_tenant',
            'meta_query' => array(
                array(
                    'key' => '_vmp_user_id',
                    'value' => $user_id,
                    'compare' => '='
                )
            ),
            'posts_per_page' => 1
        ));
        
        if ($tenant_query->have_posts()) {
            return $tenant_query->posts[0];
        }
        
        return false;
    }
    
    private static function render_property_card($property_id, $settings) {
        $address = get_post_meta($property_id, '_vmp_address', true);
        $city = get_post_meta($property_id, '_vmp_city', true);
        $units = get_post_meta($property_id, '_vmp_total_units', true);
        $available_units = get_post_meta($property_id, '_vmp_available_units', true);
        $rent_range = get_post_meta($property_id, '_vmp_rent_range', true);
        
        ob_start();
        ?>
        <?php if ($settings['show_property_images'] && has_post_thumbnail($property_id)) : ?>
            <div class="phpm-property-image">
                <a href="<?php echo get_permalink($property_id); ?>">
                    <?php echo get_the_post_thumbnail($property_id, 'medium'); ?>
                </a>
                <div class="phpm-property-status">
                    <?php if ($available_units) : ?>
                        <span class="phpm-available"><?php printf(__('%d Available', 'plughaus-property'), $available_units); ?></span>
                    <?php else : ?>
                        <span class="phpm-full"><?php _e('Full', 'plughaus-property'); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="phpm-property-content">
            <h3 class="phpm-property-title">
                <a href="<?php echo get_permalink($property_id); ?>"><?php echo get_the_title($property_id); ?></a>
            </h3>
            
            <?php if ($address || $city) : ?>
                <p class="phpm-property-location">
                    <span class="dashicons dashicons-location"></span>
                    <?php echo esc_html($address); ?>
                    <?php if ($address && $city) echo ', '; ?>
                    <?php echo esc_html($city); ?>
                </p>
            <?php endif; ?>
            
            <div class="phpm-property-details">
                <?php if ($units) : ?>
                    <span class="phpm-property-units">
                        <span class="dashicons dashicons-admin-home"></span>
                        <?php printf(_n('%d Unit', '%d Units', $units, 'plughaus-property'), $units); ?>
                    </span>
                <?php endif; ?>
                
                <?php if ($rent_range) : ?>
                    <span class="phpm-property-rent">
                        <span class="dashicons dashicons-money"></span>
                        $<?php echo esc_html($rent_range); ?>/month
                    </span>
                <?php endif; ?>
            </div>
            
            <div class="phpm-property-actions">
                <a href="<?php echo get_permalink($property_id); ?>" class="phpm-button phpm-button-primary">
                    <?php _e('View Details', 'plughaus-property'); ?>
                </a>
                
                <?php if ($available_units) : ?>
                    <a href="<?php echo get_permalink($property_id); ?>#contact" class="phpm-button phpm-button-secondary">
                        <?php _e('Contact', 'plughaus-property'); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php
        
        return ob_get_clean();
    }
    
    // Additional helper methods would continue here...
    // For brevity, I'm including the key framework with placeholder implementations
    
    private static function get_property_filters($atts) {
        $meta_query = array();
        $tax_query = array();
        
        // Filter by property type
        if (!empty($atts['type']) || !empty($_GET['property_type'])) {
            $type = !empty($atts['type']) ? $atts['type'] : $_GET['property_type'];
            $tax_query[] = array(
                'taxonomy' => 'vmp_property_type',
                'field' => 'slug',
                'terms' => $type
            );
        }
        
        // Filter by location
        if (!empty($atts['location']) || !empty($_GET['location'])) {
            $location = !empty($atts['location']) ? $atts['location'] : $_GET['location'];
            $tax_query[] = array(
                'taxonomy' => 'vmp_location',
                'field' => 'slug',
                'terms' => $location
            );
        }
        
        // Filter by rent range
        if (!empty($atts['min_rent']) || !empty($_GET['min_rent'])) {
            $min_rent = !empty($atts['min_rent']) ? $atts['min_rent'] : $_GET['min_rent'];
            $meta_query[] = array(
                'key' => '_vmp_property_rent_min',
                'value' => floatval($min_rent),
                'compare' => '>=',
                'type' => 'NUMERIC'
            );
        }
        
        if (!empty($atts['max_rent']) || !empty($_GET['max_rent'])) {
            $max_rent = !empty($atts['max_rent']) ? $atts['max_rent'] : $_GET['max_rent'];
            $meta_query[] = array(
                'key' => '_vmp_property_rent_max',
                'value' => floatval($max_rent),
                'compare' => '<=',
                'type' => 'NUMERIC'
            );
        }
        
        // Filter by bedrooms
        if (!empty($atts['bedrooms']) || !empty($_GET['bedrooms'])) {
            $bedrooms = !empty($atts['bedrooms']) ? $atts['bedrooms'] : $_GET['bedrooms'];
            $meta_query[] = array(
                'key' => '_vmp_property_bedrooms_min',
                'value' => intval($bedrooms),
                'compare' => '>=',
                'type' => 'NUMERIC'
            );
        }
        
        return array('meta_query' => $meta_query, 'tax_query' => $tax_query);
    }
    
    private static function render_property_search_form($atts) {
        ob_start();
        ?>
        <div class="vmp-property-search-form">
            <form method="get" class="vmp-search-form">
                <div class="vmp-search-fields">
                    <div class="vmp-search-field">
                        <label for="property_type"><?php _e('Type', 'plughaus-property'); ?></label>
                        <select name="property_type" id="property_type">
                            <option value=""><?php _e('Any Type', 'plughaus-property'); ?></option>
                            <option value="apartment" <?php selected($_GET['property_type'] ?? '', 'apartment'); ?>><?php _e('Apartment', 'plughaus-property'); ?></option>
                            <option value="house" <?php selected($_GET['property_type'] ?? '', 'house'); ?>><?php _e('House', 'plughaus-property'); ?></option>
                            <option value="condo" <?php selected($_GET['property_type'] ?? '', 'condo'); ?>><?php _e('Condo', 'plughaus-property'); ?></option>
                        </select>
                    </div>
                    
                    <div class="vmp-search-field">
                        <label for="min_rent"><?php _e('Min Rent', 'plughaus-property'); ?></label>
                        <input type="number" name="min_rent" id="min_rent" value="<?php echo esc_attr($_GET['min_rent'] ?? ''); ?>" placeholder="$500">
                    </div>
                    
                    <div class="vmp-search-field">
                        <label for="max_rent"><?php _e('Max Rent', 'plughaus-property'); ?></label>
                        <input type="number" name="max_rent" id="max_rent" value="<?php echo esc_attr($_GET['max_rent'] ?? ''); ?>" placeholder="$3000">
                    </div>
                    
                    <div class="vmp-search-field">
                        <label for="bedrooms"><?php _e('Bedrooms', 'plughaus-property'); ?></label>
                        <select name="bedrooms" id="bedrooms">
                            <option value=""><?php _e('Any', 'plughaus-property'); ?></option>
                            <?php for ($i = 1; $i <= 5; $i++) : ?>
                                <option value="<?php echo $i; ?>" <?php selected($_GET['bedrooms'] ?? '', $i); ?>><?php echo $i; ?>+</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <div class="vmp-search-submit">
                        <button type="submit" class="vmp-button vmp-button-primary">
                            <?php _e('Search', 'plughaus-property'); ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
    
    private static function render_property_filters() {
        return '<!-- Property filters would be rendered here -->';
    }
    
    private static function render_pagination($query) {
        if ($query->max_num_pages <= 1) {
            return '';
        }
        
        $current_page = max(1, get_query_var('paged'));
        $total_pages = $query->max_num_pages;
        
        ob_start();
        ?>
        <div class="vmp-pagination">
            <div class="vmp-pagination-info">
                <?php printf(
                    __('Page %d of %d', 'plughaus-property'),
                    $current_page,
                    $total_pages
                ); ?>
            </div>
            
            <div class="vmp-pagination-links">
                <?php if ($current_page > 1) : ?>
                    <a href="<?php echo get_pagenum_link($current_page - 1); ?>" class="vmp-button vmp-button-secondary">
                        <?php _e('Previous', 'plughaus-property'); ?>
                    </a>
                <?php endif; ?>
                
                <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++) : ?>
                    <?php if ($i == $current_page) : ?>
                        <span class="vmp-button vmp-button-primary current"><?php echo $i; ?></span>
                    <?php else : ?>
                        <a href="<?php echo get_pagenum_link($i); ?>" class="vmp-button vmp-button-secondary"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($current_page < $total_pages) : ?>
                    <a href="<?php echo get_pagenum_link($current_page + 1); ?>" class="vmp-button vmp-button-secondary">
                        <?php _e('Next', 'plughaus-property'); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    private static function render_property_meta($property_id) {
        $address = get_post_meta($property_id, '_vmp_property_address', true);
        $city = get_post_meta($property_id, '_vmp_property_city', true);
        $state = get_post_meta($property_id, '_vmp_property_state', true);
        $zip = get_post_meta($property_id, '_vmp_property_zip', true);
        $units = get_post_meta($property_id, '_vmp_property_units', true);
        $type = get_post_meta($property_id, '_vmp_property_type', true);
        
        ob_start();
        ?>
        <div class="vmp-property-meta">
            <?php if ($address || $city || $state) : ?>
                <div class="vmp-property-address">
                    <span class="dashicons dashicons-location"></span>
                    <span class="vmp-address-text">
                        <?php 
                        $address_parts = array_filter(array($address, $city, $state, $zip));
                        echo esc_html(implode(', ', $address_parts));
                        ?>
                    </span>
                </div>
            <?php endif; ?>
            
            <div class="vmp-property-details">
                <?php if ($type) : ?>
                    <span class="vmp-property-type">
                        <span class="dashicons dashicons-building"></span>
                        <?php echo esc_html(ucfirst($type)); ?>
                    </span>
                <?php endif; ?>
                
                <?php if ($units) : ?>
                    <span class="vmp-property-units">
                        <span class="dashicons dashicons-admin-home"></span>
                        <?php printf(_n('%d Unit', '%d Units', $units, 'plughaus-property'), $units); ?>
                    </span>
                <?php endif; ?>
                
                <span class="vmp-property-id">
                    <span class="dashicons dashicons-tag"></span>
                    <?php printf(__('ID: %d', 'plughaus-property'), $property_id); ?>
                </span>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    private static function render_property_gallery($property_id) {
        return '<!-- Property gallery would be rendered here -->';
    }
    
    private static function render_property_features($property_id) {
        $year_built = get_post_meta($property_id, '_vmp_property_year_built', true);
        $square_footage = get_post_meta($property_id, '_vmp_property_square_footage', true);
        $lot_size = get_post_meta($property_id, '_vmp_property_lot_size', true);
        $parking = get_post_meta($property_id, '_vmp_property_parking', true);
        $pets_allowed = get_post_meta($property_id, '_vmp_property_pets_allowed', true);
        
        ob_start();
        ?>
        <div class="vmp-property-features">
            <ul class="vmp-features-list">
                <?php if ($year_built) : ?>
                    <li class="vmp-feature">
                        <span class="dashicons dashicons-calendar-alt"></span>
                        <?php printf(__('Built in %s', 'plughaus-property'), esc_html($year_built)); ?>
                    </li>
                <?php endif; ?>
                
                <?php if ($square_footage) : ?>
                    <li class="vmp-feature">
                        <span class="dashicons dashicons-admin-home"></span>
                        <?php printf(__('%s sq ft', 'plughaus-property'), number_format($square_footage)); ?>
                    </li>
                <?php endif; ?>
                
                <?php if ($lot_size) : ?>
                    <li class="vmp-feature">
                        <span class="dashicons dashicons-location-alt"></span>
                        <?php printf(__('%s acre lot', 'plughaus-property'), number_format($lot_size, 2)); ?>
                    </li>
                <?php endif; ?>
                
                <?php if ($parking) : ?>
                    <li class="vmp-feature">
                        <span class="dashicons dashicons-car"></span>
                        <?php echo esc_html($parking); ?>
                    </li>
                <?php endif; ?>
                
                <?php if ($pets_allowed) : ?>
                    <li class="vmp-feature">
                        <span class="dashicons dashicons-heart"></span>
                        <?php _e('Pet Friendly', 'plughaus-property'); ?>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
        <?php
        return ob_get_clean();
    }
    
    private static function render_property_amenities($property_id) {
        $amenities = get_post_meta($property_id, '_vmp_property_amenities', true);
        $amenities_list = wp_get_post_terms($property_id, 'vmp_amenities');
        
        ob_start();
        ?>
        <div class="vmp-property-amenities">
            <?php if ($amenities_list && !is_wp_error($amenities_list)) : ?>
                <ul class="vmp-amenities-list">
                    <?php foreach ($amenities_list as $amenity) : ?>
                        <li class="vmp-amenity">
                            <span class="dashicons dashicons-yes"></span>
                            <?php echo esc_html($amenity->name); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php elseif ($amenities) : ?>
                <div class="vmp-amenities-text">
                    <?php echo wpautop(esc_html($amenities)); ?>
                </div>
            <?php else : ?>
                <p class="vmp-no-amenities">
                    <?php _e('No amenities listed.', 'plughaus-property'); ?>
                </p>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    private static function render_available_units($property_id) {
        global $wpdb;
        
        // Query units from database
        $units = $wpdb->get_results($wpdb->prepare("
            SELECT * FROM {$wpdb->prefix}vmp_units 
            WHERE property_id = %d AND status = 'available'
            ORDER BY unit_number ASC
        ", $property_id));
        
        ob_start();
        ?>
        <div class="vmp-available-units">
            <?php if ($units) : ?>
                <div class="vmp-units-grid">
                    <?php foreach ($units as $unit) : ?>
                        <div class="vmp-unit-card">
                            <div class="vmp-unit-header">
                                <h4 class="vmp-unit-title">
                                    <?php printf(__('Unit %s', 'plughaus-property'), esc_html($unit->unit_number)); ?>
                                </h4>
                                <span class="vmp-unit-status available">
                                    <?php _e('Available', 'plughaus-property'); ?>
                                </span>
                            </div>
                            
                            <div class="vmp-unit-details">
                                <?php if ($unit->bedrooms) : ?>
                                    <span class="vmp-unit-bedrooms">
                                        <span class="dashicons dashicons-admin-home"></span>
                                        <?php printf(_n('%d Bedroom', '%d Bedrooms', $unit->bedrooms, 'plughaus-property'), $unit->bedrooms); ?>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if ($unit->bathrooms) : ?>
                                    <span class="vmp-unit-bathrooms">
                                        <span class="dashicons dashicons-admin-tools"></span>
                                        <?php printf(_n('%s Bath', '%s Baths', $unit->bathrooms, 'plughaus-property'), number_format($unit->bathrooms, 1)); ?>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if ($unit->square_footage) : ?>
                                    <span class="vmp-unit-sqft">
                                        <span class="dashicons dashicons-editor-expand"></span>
                                        <?php printf(__('%s sq ft', 'plughaus-property'), number_format($unit->square_footage)); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($unit->rent_amount) : ?>
                                <div class="vmp-unit-rent">
                                    <span class="vmp-rent-amount">
                                        $<?php echo number_format($unit->rent_amount); ?>
                                    </span>
                                    <span class="vmp-rent-period"><?php _e('/month', 'plughaus-property'); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($unit->available_date) : ?>
                                <div class="vmp-unit-available-date">
                                    <?php printf(__('Available: %s', 'plughaus-property'), date_i18n(get_option('date_format'), strtotime($unit->available_date))); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="vmp-unit-actions">
                                <a href="#contact" class="vmp-button vmp-button-primary">
                                    <?php _e('Inquire', 'plughaus-property'); ?>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <div class="vmp-no-units">
                    <p><?php _e('No units are currently available.', 'plughaus-property'); ?></p>
                    <p><?php _e('Please check back later or contact us to be notified when units become available.', 'plughaus-property'); ?></p>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    private static function render_property_map($property_id, $settings) {
        return '<!-- Property map would be rendered here -->';
    }
    
    private static function render_property_contact_form($property_id) {
        return self::contact_form_shortcode(array('property_id' => $property_id));
    }
    
    private static function render_tenant_lease_info($tenant_id) {
        global $wpdb;
        
        // Get current lease for tenant
        $lease = $wpdb->get_row($wpdb->prepare("
            SELECT l.*, p.name as property_name, u.unit_number
            FROM {$wpdb->prefix}vmp_leases l
            LEFT JOIN {$wpdb->prefix}vmp_properties p ON l.property_id = p.id
            LEFT JOIN {$wpdb->prefix}vmp_units u ON l.unit_id = u.id
            WHERE l.tenant_id = %d AND l.status = 'active'
            ORDER BY l.start_date DESC
            LIMIT 1
        ", $tenant_id));
        
        ob_start();
        ?>
        <div class="vmp-tenant-lease-info">
            <?php if ($lease) : ?>
                <div class="vmp-lease-details">
                    <div class="vmp-lease-property">
                        <h4><?php echo esc_html($lease->property_name); ?></h4>
                        <?php if ($lease->unit_number) : ?>
                            <p class="vmp-unit-info"><?php printf(__('Unit %s', 'plughaus-property'), esc_html($lease->unit_number)); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="vmp-lease-terms">
                        <div class="vmp-lease-dates">
                            <span class="vmp-lease-start">
                                <strong><?php _e('Lease Start:', 'plughaus-property'); ?></strong>
                                <?php echo date_i18n(get_option('date_format'), strtotime($lease->start_date)); ?>
                            </span>
                            <span class="vmp-lease-end">
                                <strong><?php _e('Lease End:', 'plughaus-property'); ?></strong>
                                <?php echo date_i18n(get_option('date_format'), strtotime($lease->end_date)); ?>
                            </span>
                        </div>
                        
                        <div class="vmp-lease-financial">
                            <span class="vmp-monthly-rent">
                                <strong><?php _e('Monthly Rent:', 'plughaus-property'); ?></strong>
                                $<?php echo number_format($lease->rent_amount, 2); ?>
                            </span>
                            <?php if ($lease->deposit_amount) : ?>
                                <span class="vmp-security-deposit">
                                    <strong><?php _e('Security Deposit:', 'plughaus-property'); ?></strong>
                                    $<?php echo number_format($lease->deposit_amount, 2); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="vmp-lease-status">
                            <?php 
                            $days_remaining = ceil((strtotime($lease->end_date) - time()) / (60 * 60 * 24));
                            if ($days_remaining > 0) :
                            ?>
                                <span class="vmp-status-active">
                                    <?php printf(_n('%d day remaining', '%d days remaining', $days_remaining, 'plughaus-property'), $days_remaining); ?>
                                </span>
                            <?php else : ?>
                                <span class="vmp-status-expired">
                                    <?php _e('Lease has expired', 'plughaus-property'); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <div class="vmp-no-lease">
                    <p><?php _e('No active lease found.', 'plughaus-property'); ?></p>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    private static function render_tenant_maintenance_requests($tenant_id, $limit = 5) {
        global $wpdb;
        
        // Query maintenance requests from database
        $requests = $wpdb->get_results($wpdb->prepare("
            SELECT mr.*, p.name as property_name, u.unit_number
            FROM {$wpdb->prefix}vmp_maintenance_requests mr
            LEFT JOIN {$wpdb->prefix}vmp_properties p ON mr.property_id = p.id
            LEFT JOIN {$wpdb->prefix}vmp_units u ON mr.unit_id = u.id
            WHERE mr.tenant_id = %d
            ORDER BY mr.created_at DESC
            LIMIT %d
        ", $tenant_id, $limit));
        
        ob_start();
        ?>
        <div class="vmp-maintenance-requests">
            <?php if ($requests) : ?>
                <div class="vmp-requests-list">
                    <?php foreach ($requests as $request) : ?>
                        <div class="vmp-request-item status-<?php echo esc_attr($request->status); ?>">
                            <div class="vmp-request-header">
                                <h4 class="vmp-request-title"><?php echo esc_html($request->title); ?></h4>
                                <span class="vmp-request-status status-<?php echo esc_attr($request->status); ?>">
                                    <?php echo esc_html(ucfirst($request->status)); ?>
                                </span>
                            </div>
                            
                            <div class="vmp-request-meta">
                                <span class="vmp-request-date">
                                    <span class="dashicons dashicons-calendar-alt"></span>
                                    <?php echo date_i18n(get_option('date_format'), strtotime($request->created_at)); ?>
                                </span>
                                
                                <?php if ($request->property_name) : ?>
                                    <span class="vmp-request-property">
                                        <span class="dashicons dashicons-admin-home"></span>
                                        <?php echo esc_html($request->property_name); ?>
                                        <?php if ($request->unit_number) : ?>
                                            - Unit <?php echo esc_html($request->unit_number); ?>
                                        <?php endif; ?>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if ($request->priority) : ?>
                                    <span class="vmp-request-priority priority-<?php echo esc_attr($request->priority); ?>">
                                        <span class="dashicons dashicons-flag"></span>
                                        <?php echo esc_html(ucfirst($request->priority)); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($request->description) : ?>
                                <div class="vmp-request-description">
                                    <?php echo wp_kses_post(wp_trim_words($request->description, 20)); ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($request->scheduled_date && strtotime($request->scheduled_date) > time()) : ?>
                                <div class="vmp-request-scheduled">
                                    <span class="dashicons dashicons-calendar"></span>
                                    <?php printf(__('Scheduled: %s', 'plughaus-property'), date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($request->scheduled_date))); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <div class="vmp-no-requests">
                    <p><?php _e('No maintenance requests found.', 'plughaus-property'); ?></p>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    private static function render_tenant_payment_history($tenant_id) {
        global $wpdb;
        
        // Query payment history from database
        $payments = $wpdb->get_results($wpdb->prepare("
            SELECT p.*, pr.name as property_name, l.start_date, l.end_date
            FROM {$wpdb->prefix}vmp_payments p
            LEFT JOIN {$wpdb->prefix}vmp_leases l ON p.lease_id = l.id
            LEFT JOIN {$wpdb->prefix}vmp_properties pr ON p.property_id = pr.id
            WHERE p.tenant_id = %d
            ORDER BY p.payment_date DESC
            LIMIT 12
        ", $tenant_id));
        
        ob_start();
        ?>
        <div class="vmp-payment-history">
            <?php if ($payments) : ?>
                <div class="vmp-payments-table">
                    <table class="vmp-table">
                        <thead>
                            <tr>
                                <th><?php _e('Date', 'plughaus-property'); ?></th>
                                <th><?php _e('Type', 'plughaus-property'); ?></th>
                                <th><?php _e('Amount', 'plughaus-property'); ?></th>
                                <th><?php _e('Status', 'plughaus-property'); ?></th>
                                <th><?php _e('Method', 'plughaus-property'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payments as $payment) : ?>
                                <tr class="payment-status-<?php echo esc_attr($payment->status); ?>">
                                    <td class="vmp-payment-date">
                                        <?php echo date_i18n(get_option('date_format'), strtotime($payment->payment_date)); ?>
                                        <?php if ($payment->due_date && strtotime($payment->payment_date) > strtotime($payment->due_date)) : ?>
                                            <span class="vmp-late-indicator" title="<?php _e('Late Payment', 'plughaus-property'); ?>">
                                                <span class="dashicons dashicons-warning"></span>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="vmp-payment-type">
                                        <?php echo esc_html(ucfirst(str_replace('_', ' ', $payment->payment_type))); ?>
                                        <?php if ($payment->property_name) : ?>
                                            <small class="vmp-property-ref"><?php echo esc_html($payment->property_name); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="vmp-payment-amount">
                                        <span class="vmp-amount-primary">$<?php echo number_format($payment->amount, 2); ?></span>
                                        <?php if ($payment->late_fee > 0 || $payment->other_fees > 0) : ?>
                                            <small class="vmp-fees">
                                                <?php if ($payment->late_fee > 0) : ?>
                                                    + $<?php echo number_format($payment->late_fee, 2); ?> <?php _e('late fee', 'plughaus-property'); ?>
                                                <?php endif; ?>
                                                <?php if ($payment->other_fees > 0) : ?>
                                                    + $<?php echo number_format($payment->other_fees, 2); ?> <?php _e('fees', 'plughaus-property'); ?>
                                                <?php endif; ?>
                                            </small>
                                            <div class="vmp-total-amount">
                                                <?php _e('Total:', 'plughaus-property'); ?> $<?php echo number_format($payment->total_amount, 2); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="vmp-payment-status">
                                        <span class="vmp-status-badge status-<?php echo esc_attr($payment->status); ?>">
                                            <?php echo esc_html(ucfirst($payment->status)); ?>
                                        </span>
                                    </td>
                                    <td class="vmp-payment-method">
                                        <?php if ($payment->payment_method) : ?>
                                            <?php echo esc_html(ucfirst(str_replace('_', ' ', $payment->payment_method))); ?>
                                        <?php endif; ?>
                                        <?php if ($payment->transaction_id) : ?>
                                            <small class="vmp-transaction-id">
                                                ID: <?php echo esc_html($payment->transaction_id); ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="vmp-payment-summary">
                    <?php
                    $total_paid = array_sum(array_column($payments, 'total_amount'));
                    $recent_payments = array_filter($payments, function($p) {
                        return strtotime($p->payment_date) > strtotime('-30 days');
                    });
                    ?>
                    <div class="vmp-summary-stats">
                        <div class="vmp-stat">
                            <span class="vmp-stat-label"><?php _e('Total Paid (Last 12)', 'plughaus-property'); ?></span>
                            <span class="vmp-stat-value">$<?php echo number_format($total_paid, 2); ?></span>
                        </div>
                        <div class="vmp-stat">
                            <span class="vmp-stat-label"><?php _e('Payments This Month', 'plughaus-property'); ?></span>
                            <span class="vmp-stat-value"><?php echo count($recent_payments); ?></span>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <div class="vmp-no-payments">
                    <p><?php _e('No payment history found.', 'plughaus-property'); ?></p>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    private static function render_tenant_documents($tenant_id) {
        global $wpdb;
        
        // Query documents from database
        $documents = $wpdb->get_results($wpdb->prepare("
            SELECT d.*, p.name as property_name
            FROM {$wpdb->prefix}vmp_documents d
            LEFT JOIN {$wpdb->prefix}vmp_properties p ON d.property_id = p.id
            WHERE d.tenant_id = %d AND d.is_public = 1
            ORDER BY d.uploaded_at DESC
        ", $tenant_id));
        
        ob_start();
        ?>
        <div class="vmp-tenant-documents">
            <?php if ($documents) : ?>
                <div class="vmp-documents-list">
                    <?php foreach ($documents as $document) : ?>
                        <div class="vmp-document-item">
                            <div class="vmp-document-icon">
                                <?php
                                $file_extension = pathinfo($document->file_name, PATHINFO_EXTENSION);
                                switch (strtolower($file_extension)) {
                                    case 'pdf':
                                        echo '<span class="dashicons dashicons-media-document"></span>';
                                        break;
                                    case 'doc':
                                    case 'docx':
                                        echo '<span class="dashicons dashicons-media-text"></span>';
                                        break;
                                    case 'jpg':
                                    case 'jpeg':
                                    case 'png':
                                    case 'gif':
                                        echo '<span class="dashicons dashicons-format-image"></span>';
                                        break;
                                    default:
                                        echo '<span class="dashicons dashicons-media-default"></span>';
                                }
                                ?>
                            </div>
                            
                            <div class="vmp-document-info">
                                <h4 class="vmp-document-title"><?php echo esc_html($document->title); ?></h4>
                                
                                <div class="vmp-document-meta">
                                    <span class="vmp-document-type">
                                        <?php echo esc_html(ucfirst(str_replace('_', ' ', $document->document_type))); ?>
                                    </span>
                                    
                                    <span class="vmp-document-date">
                                        <?php echo date_i18n(get_option('date_format'), strtotime($document->uploaded_at)); ?>
                                    </span>
                                    
                                    <?php if ($document->file_size) : ?>
                                        <span class="vmp-document-size">
                                            <?php echo size_format($document->file_size); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($document->description) : ?>
                                    <div class="vmp-document-description">
                                        <?php echo esc_html($document->description); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($document->expiry_date && strtotime($document->expiry_date) < time()) : ?>
                                    <div class="vmp-document-expired">
                                        <span class="dashicons dashicons-warning"></span>
                                        <?php printf(__('Expired: %s', 'plughaus-property'), date_i18n(get_option('date_format'), strtotime($document->expiry_date))); ?>
                                    </div>
                                <?php elseif ($document->expiry_date) : ?>
                                    <div class="vmp-document-expires">
                                        <?php printf(__('Expires: %s', 'plughaus-property'), date_i18n(get_option('date_format'), strtotime($document->expiry_date))); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="vmp-document-actions">
                                <a href="<?php echo esc_url(home_url('/wp-content/plugins/vireo-property-management/download.php?doc=' . $document->id . '&token=' . wp_create_nonce('vmp_download_' . $document->id))); ?>" 
                                   class="vmp-button vmp-button-secondary" 
                                   target="_blank">
                                    <span class="dashicons dashicons-download"></span>
                                    <?php _e('Download', 'plughaus-property'); ?>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <div class="vmp-no-documents">
                    <p><?php _e('No documents available.', 'plughaus-property'); ?></p>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    private static function render_tenant_dashboard_stats($tenant_id) {
        global $wpdb;
        
        // Get current lease information
        $lease = $wpdb->get_row($wpdb->prepare("
            SELECT l.*, p.name as property_name, u.unit_number
            FROM {$wpdb->prefix}vmp_leases l
            LEFT JOIN {$wpdb->prefix}vmp_properties p ON l.property_id = p.id
            LEFT JOIN {$wpdb->prefix}vmp_units u ON l.unit_id = u.id
            WHERE l.tenant_id = %d AND l.status = 'active'
            ORDER BY l.start_date DESC
            LIMIT 1
        ", $tenant_id));
        
        // Get payment statistics
        $payment_stats = $wpdb->get_row($wpdb->prepare("
            SELECT 
                COUNT(*) as total_payments,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_payments,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_payments,
                SUM(CASE WHEN payment_date > due_date THEN 1 ELSE 0 END) as late_payments
            FROM {$wpdb->prefix}vmp_payments
            WHERE tenant_id = %d AND payment_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        ", $tenant_id));
        
        // Get maintenance request statistics
        $maintenance_stats = $wpdb->get_row($wpdb->prepare("
            SELECT 
                COUNT(*) as total_requests,
                SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as open_requests,
                SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_requests,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_requests
            FROM {$wpdb->prefix}vmp_maintenance_requests
            WHERE tenant_id = %d
        ", $tenant_id));
        
        // Calculate lease days remaining
        $days_remaining = 0;
        if ($lease && $lease->end_date) {
            $days_remaining = max(0, ceil((strtotime($lease->end_date) - time()) / (60 * 60 * 24)));
        }
        
        ob_start();
        ?>
        <div class="vmp-dashboard-stats">
            <div class="vmp-stats-grid">
                
                <!-- Lease Status -->
                <?php if ($lease) : ?>
                    <div class="vmp-stat-card lease-status">
                        <div class="vmp-stat-icon">
                            <span class="dashicons dashicons-admin-home"></span>
                        </div>
                        <div class="vmp-stat-content">
                            <div class="vmp-stat-number"><?php echo $days_remaining; ?></div>
                            <div class="vmp-stat-label"><?php _e('Days Remaining', 'plughaus-property'); ?></div>
                            <div class="vmp-stat-sublabel"><?php echo esc_html($lease->property_name); ?></div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Payment Status -->
                <div class="vmp-stat-card payment-status">
                    <div class="vmp-stat-icon">
                        <span class="dashicons dashicons-money-alt"></span>
                    </div>
                    <div class="vmp-stat-content">
                        <div class="vmp-stat-number"><?php echo $payment_stats ? $payment_stats->completed_payments : 0; ?></div>
                        <div class="vmp-stat-label"><?php _e('Payments Made', 'plughaus-property'); ?></div>
                        <div class="vmp-stat-sublabel"><?php _e('Last 12 months', 'plughaus-property'); ?></div>
                    </div>
                </div>
                
                <!-- Maintenance Requests -->
                <div class="vmp-stat-card maintenance-status">
                    <div class="vmp-stat-icon">
                        <span class="dashicons dashicons-admin-tools"></span>
                    </div>
                    <div class="vmp-stat-content">
                        <div class="vmp-stat-number"><?php echo $maintenance_stats ? $maintenance_stats->open_requests + $maintenance_stats->in_progress_requests : 0; ?></div>
                        <div class="vmp-stat-label"><?php _e('Active Requests', 'plughaus-property'); ?></div>
                        <div class="vmp-stat-sublabel">
                            <?php 
                            if ($maintenance_stats && $maintenance_stats->total_requests > 0) {
                                printf(__('%d total requests', 'plughaus-property'), $maintenance_stats->total_requests);
                            } else {
                                _e('No requests', 'plughaus-property');
                            }
                            ?>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Performance -->
                <?php if ($payment_stats && $payment_stats->total_payments > 0) : ?>
                    <div class="vmp-stat-card performance-status">
                        <div class="vmp-stat-icon">
                            <span class="dashicons dashicons-awards"></span>
                        </div>
                        <div class="vmp-stat-content">
                            <?php 
                            $on_time_rate = round((($payment_stats->total_payments - $payment_stats->late_payments) / $payment_stats->total_payments) * 100);
                            ?>
                            <div class="vmp-stat-number"><?php echo $on_time_rate; ?>%</div>
                            <div class="vmp-stat-label"><?php _e('On-Time Rate', 'plughaus-property'); ?></div>
                            <div class="vmp-stat-sublabel">
                                <?php printf(_n('%d late payment', '%d late payments', $payment_stats->late_payments, 'plughaus-property'), $payment_stats->late_payments); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
            </div>
            
            <!-- Additional Status Information -->
            <div class="vmp-status-alerts">
                <?php if ($lease && $days_remaining <= 30 && $days_remaining > 0) : ?>
                    <div class="vmp-alert vmp-alert-warning">
                        <span class="dashicons dashicons-warning"></span>
                        <?php printf(__('Your lease expires in %d days. Contact your property manager about renewal.', 'plughaus-property'), $days_remaining); ?>
                    </div>
                <?php elseif ($lease && $days_remaining <= 0) : ?>
                    <div class="vmp-alert vmp-alert-danger">
                        <span class="dashicons dashicons-warning"></span>
                        <?php _e('Your lease has expired. Please contact your property manager immediately.', 'plughaus-property'); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($payment_stats && $payment_stats->pending_payments > 0) : ?>
                    <div class="vmp-alert vmp-alert-info">
                        <span class="dashicons dashicons-info"></span>
                        <?php printf(_n('You have %d pending payment.', 'You have %d pending payments.', $payment_stats->pending_payments, 'plughaus-property'), $payment_stats->pending_payments); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($maintenance_stats && ($maintenance_stats->open_requests + $maintenance_stats->in_progress_requests) > 0) : ?>
                    <div class="vmp-alert vmp-alert-info">
                        <span class="dashicons dashicons-admin-tools"></span>
                        <?php 
                        $active_requests = $maintenance_stats->open_requests + $maintenance_stats->in_progress_requests;
                        printf(_n('You have %d active maintenance request.', 'You have %d active maintenance requests.', $active_requests, 'plughaus-property'), $active_requests);
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    private static function render_tenant_payment_status($tenant_id) {
        global $wpdb;
        
        // Get current lease
        $lease = $wpdb->get_row($wpdb->prepare("
            SELECT l.*, p.name as property_name
            FROM {$wpdb->prefix}vmp_leases l
            LEFT JOIN {$wpdb->prefix}vmp_properties p ON l.property_id = p.id
            WHERE l.tenant_id = %d AND l.status = 'active'
            ORDER BY l.start_date DESC
            LIMIT 1
        ", $tenant_id));
        
        if (!$lease) {
            return '<p>' . __('No active lease found.', 'plughaus-property') . '</p>';
        }
        
        // Calculate next due date based on rent_due_day
        $rent_due_day = $lease->rent_due_day ?: 1;
        $current_month = date('Y-m');
        $next_due_date = $current_month . '-' . str_pad($rent_due_day, 2, '0', STR_PAD_LEFT);
        
        // If we've passed this month's due date, calculate next month
        if (date('Y-m-d') > $next_due_date) {
            $next_due_date = date('Y-m-d', strtotime('first day of next month +' . ($rent_due_day - 1) . ' days'));
        }
        
        // Get latest payment for current period
        $latest_payment = $wpdb->get_row($wpdb->prepare("
            SELECT *
            FROM {$wpdb->prefix}vmp_payments
            WHERE tenant_id = %d AND lease_id = %d
            ORDER BY payment_date DESC
            LIMIT 1
        ", $tenant_id, $lease->id));
        
        // Check if rent is paid for current period
        $is_current = false;
        $days_until_due = 0;
        $payment_status = 'pending';
        
        if ($latest_payment) {
            $payment_month = date('Y-m', strtotime($latest_payment->payment_date));
            $current_month = date('Y-m', strtotime($next_due_date));
            
            if ($payment_month === $current_month && $latest_payment->status === 'completed') {
                $is_current = true;
                $payment_status = 'current';
            }
        }
        
        $days_until_due = ceil((strtotime($next_due_date) - time()) / (60 * 60 * 24));
        
        if (!$is_current && $days_until_due < 0) {
            $payment_status = 'overdue';
        } elseif (!$is_current && $days_until_due <= 5) {
            $payment_status = 'due_soon';
        }
        
        ob_start();
        ?>
        <div class="vmp-payment-status">
            <div class="vmp-payment-overview status-<?php echo esc_attr($payment_status); ?>">
                
                <div class="vmp-payment-header">
                    <div class="vmp-payment-icon">
                        <?php if ($payment_status === 'current') : ?>
                            <span class="dashicons dashicons-yes-alt"></span>
                        <?php elseif ($payment_status === 'overdue') : ?>
                            <span class="dashicons dashicons-warning"></span>
                        <?php else : ?>
                            <span class="dashicons dashicons-money-alt"></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="vmp-payment-info">
                        <h4 class="vmp-payment-title">
                            <?php if ($payment_status === 'current') : ?>
                                <?php _e('Rent Paid', 'plughaus-property'); ?>
                            <?php elseif ($payment_status === 'overdue') : ?>
                                <?php _e('Rent Overdue', 'plughaus-property'); ?>
                            <?php elseif ($payment_status === 'due_soon') : ?>
                                <?php _e('Rent Due Soon', 'plughaus-property'); ?>
                            <?php else : ?>
                                <?php _e('Next Rent Due', 'plughaus-property'); ?>
                            <?php endif; ?>
                        </h4>
                        
                        <div class="vmp-payment-amount">
                            <span class="vmp-amount">$<?php echo number_format($lease->rent_amount, 2); ?></span>
                            <span class="vmp-period"><?php _e('/ month', 'plughaus-property'); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="vmp-payment-details">
                    <div class="vmp-detail-row">
                        <span class="vmp-detail-label"><?php _e('Next Due Date:', 'plughaus-property'); ?></span>
                        <span class="vmp-detail-value"><?php echo date_i18n(get_option('date_format'), strtotime($next_due_date)); ?></span>
                    </div>
                    
                    <div class="vmp-detail-row">
                        <span class="vmp-detail-label"><?php _e('Property:', 'plughaus-property'); ?></span>
                        <span class="vmp-detail-value"><?php echo esc_html($lease->property_name); ?></span>
                    </div>
                    
                    <?php if ($payment_status === 'current' && $latest_payment) : ?>
                        <div class="vmp-detail-row">
                            <span class="vmp-detail-label"><?php _e('Last Payment:', 'plughaus-property'); ?></span>
                            <span class="vmp-detail-value"><?php echo date_i18n(get_option('date_format'), strtotime($latest_payment->payment_date)); ?></span>
                        </div>
                    <?php elseif ($payment_status === 'overdue') : ?>
                        <div class="vmp-detail-row vmp-overdue">
                            <span class="vmp-detail-label"><?php _e('Days Overdue:', 'plughaus-property'); ?></span>
                            <span class="vmp-detail-value"><?php echo abs($days_until_due); ?> <?php _e('days', 'plughaus-property'); ?></span>
                        </div>
                    <?php elseif ($payment_status === 'due_soon') : ?>
                        <div class="vmp-detail-row vmp-due-soon">
                            <span class="vmp-detail-label"><?php _e('Due In:', 'plughaus-property'); ?></span>
                            <span class="vmp-detail-value"><?php echo $days_until_due; ?> <?php _e('days', 'plughaus-property'); ?></span>
                        </div>
                    <?php else : ?>
                        <div class="vmp-detail-row">
                            <span class="vmp-detail-label"><?php _e('Days Until Due:', 'plughaus-property'); ?></span>
                            <span class="vmp-detail-value"><?php echo $days_until_due; ?> <?php _e('days', 'plughaus-property'); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if ($payment_status !== 'current') : ?>
                    <div class="vmp-payment-actions">
                        <button class="vmp-button vmp-button-primary" data-action="pay-rent">
                            <span class="dashicons dashicons-money-alt"></span>
                            <?php _e('Pay Rent Online', 'plughaus-property'); ?>
                        </button>
                    </div>
                <?php endif; ?>
                
                <?php if ($lease->late_fee_amount > 0 && $payment_status === 'overdue') : ?>
                    <div class="vmp-late-fee-notice">
                        <span class="dashicons dashicons-info"></span>
                        <?php printf(__('Late fee of $%s may apply after %d days past due date.', 'plughaus-property'), 
                            number_format($lease->late_fee_amount, 2), 
                            $lease->late_fee_grace_days ?: 5
                        ); ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Payment Methods Info -->
            <?php if ($payment_status !== 'current') : ?>
                <div class="vmp-payment-methods">
                    <h5><?php _e('Payment Options', 'plughaus-property'); ?></h5>
                    <ul class="vmp-payment-options">
                        <li>
                            <span class="dashicons dashicons-admin-site"></span>
                            <?php _e('Online payment portal (recommended)', 'plughaus-property'); ?>
                        </li>
                        <li>
                            <span class="dashicons dashicons-money"></span>
                            <?php _e('Check or money order', 'plughaus-property'); ?>
                        </li>
                        <?php if ($lease->payment_method) : ?>
                            <li>
                                <span class="dashicons dashicons-bank"></span>
                                <?php echo esc_html(ucfirst(str_replace('_', ' ', $lease->payment_method))); ?>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Process maintenance request form submission
     */
    private static function process_maintenance_request() {
        if (!isset($_POST['phmp_maintenance_nonce']) || 
            !wp_verify_nonce($_POST['phmp_maintenance_nonce'], 'phmp_submit_maintenance_request')) {
            return;
        }
        
        global $wpdb;
        
        $property_id = intval($_POST['property_id']);
        $unit_id = intval($_POST['unit_id']);
        $tenant_id = get_current_user_id();
        $title = sanitize_text_field($_POST['maintenance_title']);
        $description = sanitize_textarea_field($_POST['maintenance_description']);
        $category = sanitize_text_field($_POST['maintenance_category']);
        $priority = sanitize_text_field($_POST['maintenance_priority']);
        $access_required = isset($_POST['tenant_access_required']) ? 1 : 0;
        
        // Validate required fields
        if (empty($title) || empty($description) || empty($property_id)) {
            wp_die(__('Please fill in all required fields.', 'plughaus-property'));
        }
        
        // Insert maintenance request
        $table_name = $wpdb->prefix . 'vmp_maintenance_requests';
        $result = $wpdb->insert(
            $table_name,
            array(
                'property_id' => $property_id,
                'unit_id' => $unit_id ?: null,
                'tenant_id' => $tenant_id,
                'requested_by' => $tenant_id,
                'title' => $title,
                'description' => $description,
                'category' => $category,
                'priority' => $priority,
                'status' => 'open',
                'tenant_access_required' => $access_required,
                'emergency_request' => ($priority === 'emergency') ? 1 : 0,
                'created_at' => current_time('mysql')
            ),
            array('%d', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s')
        );
        
        if ($result) {
            // Send notification email to property manager
            $property = $wpdb->get_row($wpdb->prepare(
                "SELECT name FROM {$wpdb->prefix}vmp_properties WHERE id = %d",
                $property_id
            ));
            
            $to = get_option('admin_email');
            $subject = sprintf(__('[Property Management] New Maintenance Request: %s', 'plughaus-property'), $title);
            $message = sprintf(
                __("A new maintenance request has been submitted:\n\nProperty: %s\nTitle: %s\nPriority: %s\nDescription: %s\n\nTenant Access Required: %s\n\nPlease log in to the admin panel to review and assign this request.", 'plughaus-property'),
                $property ? $property->name : __('Unknown', 'plughaus-property'),
                $title,
                ucfirst($priority),
                $description,
                $access_required ? __('Yes', 'plughaus-property') : __('No', 'plughaus-property')
            );
            
            wp_mail($to, $subject, $message);
            
            // Redirect with success message
            wp_redirect(add_query_arg('maintenance_submitted', '1', wp_get_referer()));
            exit;
        } else {
            wp_die(__('Error submitting maintenance request. Please try again.', 'plughaus-property'));
        }
    }
    
    /**
     * Process rental application form submission
     */
    private static function process_rental_application() {
        if (!isset($_POST['phmp_application_nonce']) || 
            !wp_verify_nonce($_POST['phmp_application_nonce'], 'phmp_submit_rental_application')) {
            return;
        }
        
        global $wpdb;
        
        // Collect and sanitize form data
        $property_id = intval($_POST['desired_property']);
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $date_of_birth = sanitize_text_field($_POST['date_of_birth']);
        $ssn_last_four = sanitize_text_field($_POST['ssn_last_four']);
        $employment_status = sanitize_text_field($_POST['employment_status']);
        $employer = sanitize_text_field($_POST['employer']);
        $monthly_income = floatval($_POST['monthly_income']);
        $current_address = sanitize_textarea_field($_POST['current_address']);
        $emergency_contact_name = sanitize_text_field($_POST['emergency_contact_name']);
        $emergency_contact_phone = sanitize_text_field($_POST['emergency_contact_phone']);
        $emergency_contact_relationship = sanitize_text_field($_POST['emergency_contact_relationship']);
        
        // Validate required fields
        if (empty($first_name) || empty($last_name) || empty($email) || empty($phone)) {
            wp_die(__('Please fill in all required fields.', 'plughaus-property'));
        }
        
        // Validate email format
        if (!is_email($email)) {
            wp_die(__('Please provide a valid email address.', 'plughaus-property'));
        }
        
        // Check if application already exists for this email/property combination
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}vmp_tenants WHERE email = %s AND status = 'applicant'",
            $email
        ));
        
        if ($existing) {
            wp_die(__('An application with this email address already exists.', 'plughaus-property'));
        }
        
        // Insert tenant record as applicant
        $table_name = $wpdb->prefix . 'vmp_tenants';
        $result = $wpdb->insert(
            $table_name,
            array(
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'phone' => $phone,
                'date_of_birth' => $date_of_birth ?: null,
                'ssn_last_four' => $ssn_last_four,
                'employment_status' => $employment_status,
                'employer' => $employer,
                'monthly_income' => $monthly_income ?: null,
                'current_address' => $current_address,
                'emergency_contact_name' => $emergency_contact_name,
                'emergency_contact_phone' => $emergency_contact_phone,
                'emergency_contact_relationship' => $emergency_contact_relationship,
                'status' => 'applicant',
                'background_check_status' => 'pending',
                'created_at' => current_time('mysql')
            ),
            array(
                '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f', 
                '%s', '%s', '%s', '%s', '%s', '%s', '%s'
            )
        );
        
        if ($result) {
            $applicant_id = $wpdb->insert_id;
            
            // Create activity log entry
            $wpdb->insert(
                $wpdb->prefix . 'vmp_activity_log',
                array(
                    'action' => 'rental_application_submitted',
                    'object_type' => 'tenant',
                    'object_id' => $applicant_id,
                    'new_values' => json_encode(array(
                        'property_id' => $property_id,
                        'applicant_name' => $first_name . ' ' . $last_name,
                        'email' => $email
                    )),
                    'ip_address' => $_SERVER['REMOTE_ADDR'],
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                    'created_at' => current_time('mysql')
                ),
                array('%s', '%s', '%d', '%s', '%s', '%s', '%s')
            );
            
            // Send notification email to property manager
            $property = $wpdb->get_row($wpdb->prepare(
                "SELECT name FROM {$wpdb->prefix}vmp_properties WHERE id = %d",
                $property_id
            ));
            
            $to = get_option('admin_email');
            $subject = sprintf(__('[Property Management] New Rental Application: %s %s', 'plughaus-property'), $first_name, $last_name);
            $message = sprintf(
                __("A new rental application has been submitted:\n\nApplicant: %s %s\nEmail: %s\nPhone: %s\nProperty Interest: %s\nMonthly Income: $%s\nEmployment: %s at %s\n\nPlease log in to the admin panel to review this application and initiate background checks.", 'plughaus-property'),
                $first_name,
                $last_name,
                $email,
                $phone,
                $property ? $property->name : __('Not specified', 'plughaus-property'),
                number_format($monthly_income, 2),
                $employment_status,
                $employer
            );
            
            wp_mail($to, $subject, $message);
            
            // Send confirmation email to applicant
            $applicant_subject = __('Rental Application Received', 'plughaus-property');
            $applicant_message = sprintf(
                __("Dear %s,\n\nThank you for submitting your rental application. We have received your information and will begin processing your application.\n\nNext steps:\n1. Background and credit check (typically 24-48 hours)\n2. Application review\n3. Decision notification\n\nWe will contact you within 48 hours with an update.\n\nBest regards,\nProperty Management Team", 'plughaus-property'),
                $first_name
            );
            
            wp_mail($email, $applicant_subject, $applicant_message);
            
            // Redirect with success message
            wp_redirect(add_query_arg('application_submitted', '1', wp_get_referer()));
            exit;
        } else {
            wp_die(__('Error submitting application. Please try again.', 'plughaus-property'));
        }
    }
    
    /**
     * Process contact form submission
     */
    private static function process_contact_form() {
        if (!isset($_POST['phmp_contact_nonce']) || 
            !wp_verify_nonce($_POST['phmp_contact_nonce'], 'phmp_submit_contact_form')) {
            return;
        }
        
        global $wpdb;
        
        $name = sanitize_text_field($_POST['contact_name']);
        $email = sanitize_email($_POST['contact_email']);
        $phone = sanitize_text_field($_POST['contact_phone']);
        $property_inquiry = intval($_POST['property_inquiry']);
        $subject = sanitize_text_field($_POST['contact_subject']);
        $message = sanitize_textarea_field($_POST['contact_message']);
        
        // Validate required fields
        if (empty($name) || empty($email) || empty($subject) || empty($message)) {
            wp_die(__('Please fill in all required fields.', 'plughaus-property'));
        }
        
        // Validate email format
        if (!is_email($email)) {
            wp_die(__('Please provide a valid email address.', 'plughaus-property'));
        }
        
        // Get property information if specified
        $property_info = '';
        if ($property_inquiry) {
            $property = $wpdb->get_row($wpdb->prepare(
                "SELECT name, address FROM {$wpdb->prefix}vmp_properties WHERE id = %d",
                $property_inquiry
            ));
            
            if ($property) {
                $property_info = sprintf(
                    __("\n\nProperty of Interest: %s\nAddress: %s", 'plughaus-property'),
                    $property->name,
                    $property->address
                );
            }
        }
        
        // Create activity log entry
        $wpdb->insert(
            $wpdb->prefix . 'vmp_activity_log',
            array(
                'action' => 'contact_form_submitted',
                'object_type' => 'communication',
                'new_values' => json_encode(array(
                    'name' => $name,
                    'email' => $email,
                    'subject' => $subject,
                    'property_inquiry' => $property_inquiry
                )),
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'created_at' => current_time('mysql')
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s')
        );
        
        // Send email to property manager
        $to = get_option('admin_email');
        $email_subject = sprintf(__('[Property Management] Contact Form: %s', 'plughaus-property'), $subject);
        $email_message = sprintf(
            __("A new contact form submission has been received:\n\nName: %s\nEmail: %s\nPhone: %s\nSubject: %s\n\nMessage:\n%s%s\n\nPlease respond to this inquiry promptly.", 'plughaus-property'),
            $name,
            $email,
            $phone ?: __('Not provided', 'plughaus-property'),
            $subject,
            $message,
            $property_info
        );
        
        $headers = array(
            'Content-Type: text/plain; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
            'Reply-To: ' . $name . ' <' . $email . '>'
        );
        
        $mail_sent = wp_mail($to, $email_subject, $email_message, $headers);
        
        if ($mail_sent) {
            // Send confirmation email to sender
            $confirmation_subject = __('Thank you for contacting us', 'plughaus-property');
            $confirmation_message = sprintf(
                __("Dear %s,\n\nThank you for contacting us. We have received your message and will respond within 24 hours.\n\nYour message:\nSubject: %s\n%s\n\nBest regards,\nProperty Management Team", 'plughaus-property'),
                $name,
                $subject,
                $message
            );
            
            wp_mail($email, $confirmation_subject, $confirmation_message);
            
            // Redirect with success message
            wp_redirect(add_query_arg('contact_sent', '1', wp_get_referer()));
            exit;
        } else {
            wp_die(__('Error sending message. Please try again.', 'plughaus-property'));
        }
    }
}

// Initialize shortcodes
PHPM_Shortcodes::init();