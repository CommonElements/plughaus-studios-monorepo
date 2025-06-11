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
            'post_type' => 'phpm_property',
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
                'key' => '_phpm_availability_status',
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
        if (!$property || $property->post_type !== 'phpm_property') {
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
                                'taxonomy' => 'phpm_location',
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
                                'taxonomy' => 'phpm_property_type',
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
                                'post_type' => 'phpm_property',
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
            'post_type' => 'phpm_tenant',
            'meta_query' => array(
                array(
                    'key' => '_phpm_user_id',
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
        $address = get_post_meta($property_id, '_phpm_address', true);
        $city = get_post_meta($property_id, '_phpm_city', true);
        $units = get_post_meta($property_id, '_phpm_total_units', true);
        $available_units = get_post_meta($property_id, '_phpm_available_units', true);
        $rent_range = get_post_meta($property_id, '_phpm_rent_range', true);
        
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
        return array('meta_query' => array(), 'tax_query' => array());
    }
    
    private static function render_property_search_form($atts) {
        return '<!-- Search form would be rendered here -->';
    }
    
    private static function render_property_filters() {
        return '<!-- Property filters would be rendered here -->';
    }
    
    private static function render_pagination($query) {
        return '<!-- Pagination would be rendered here -->';
    }
    
    private static function render_property_meta($property_id) {
        return '<!-- Property meta would be rendered here -->';
    }
    
    private static function render_property_gallery($property_id) {
        return '<!-- Property gallery would be rendered here -->';
    }
    
    private static function render_property_features($property_id) {
        return '<!-- Property features would be rendered here -->';
    }
    
    private static function render_property_amenities($property_id) {
        return '<!-- Property amenities would be rendered here -->';
    }
    
    private static function render_available_units($property_id) {
        return '<!-- Available units would be rendered here -->';
    }
    
    private static function render_property_map($property_id, $settings) {
        return '<!-- Property map would be rendered here -->';
    }
    
    private static function render_property_contact_form($property_id) {
        return self::contact_form_shortcode(array('property_id' => $property_id));
    }
    
    private static function render_tenant_lease_info($tenant_id) {
        return '<!-- Tenant lease info would be rendered here -->';
    }
    
    private static function render_tenant_maintenance_requests($tenant_id, $limit = 5) {
        return '<!-- Tenant maintenance requests would be rendered here -->';
    }
    
    private static function render_tenant_payment_history($tenant_id) {
        return '<!-- Tenant payment history would be rendered here -->';
    }
    
    private static function render_tenant_documents($tenant_id) {
        return '<!-- Tenant documents would be rendered here -->';
    }
    
    private static function render_tenant_dashboard_stats($tenant_id) {
        return '<!-- Dashboard stats would be rendered here -->';
    }
    
    private static function render_tenant_payment_status($tenant_id) {
        return '<!-- Payment status would be rendered here -->';
    }
    
    private static function process_maintenance_request() {
        // Process maintenance request submission
    }
    
    private static function process_rental_application() {
        // Process rental application submission
    }
    
    private static function process_contact_form() {
        // Process contact form submission
    }
}

// Initialize shortcodes
PHPM_Shortcodes::init();