<?php
/**
 * Shortcodes for PlugHaus Property Management
 */

if (!defined('ABSPATH')) {
    exit;
}

class PHPM_Shortcodes {
    
    /**
     * Initialize shortcodes
     */
    public static function init() {
        // Property listings
        add_shortcode('phpm_properties', array(__CLASS__, 'properties_shortcode'));
        add_shortcode('phpm_property_search', array(__CLASS__, 'property_search_shortcode'));
        
        // Tenant portal
        add_shortcode('phpm_tenant_portal', array(__CLASS__, 'tenant_portal_shortcode'));
        add_shortcode('phpm_maintenance_request', array(__CLASS__, 'maintenance_request_shortcode'));
        
        // Property details
        add_shortcode('phpm_property_details', array(__CLASS__, 'property_details_shortcode'));
        add_shortcode('phpm_available_units', array(__CLASS__, 'available_units_shortcode'));
    }
    
    /**
     * Properties listing shortcode
     * Usage: [phpm_properties limit="10" type="apartment" location="downtown"]
     */
    public static function properties_shortcode($atts) {
        $atts = shortcode_atts(array(
            'limit' => 10,
            'type' => '',
            'location' => '',
            'amenities' => '',
            'orderby' => 'date',
            'order' => 'DESC',
        ), $atts, 'phpm_properties');
        
        $args = array(
            'post_type' => 'phpm_property',
            'posts_per_page' => intval($atts['limit']),
            'orderby' => $atts['orderby'],
            'order' => $atts['order'],
            'post_status' => 'publish',
        );
        
        // Add taxonomy queries
        $tax_query = array();
        
        if (!empty($atts['type'])) {
            $tax_query[] = array(
                'taxonomy' => 'phpm_property_type',
                'field' => 'slug',
                'terms' => $atts['type'],
            );
        }
        
        if (!empty($atts['location'])) {
            $tax_query[] = array(
                'taxonomy' => 'phpm_location',
                'field' => 'slug',
                'terms' => $atts['location'],
            );
        }
        
        if (!empty($atts['amenities'])) {
            $amenities = explode(',', $atts['amenities']);
            $tax_query[] = array(
                'taxonomy' => 'phpm_amenities',
                'field' => 'slug',
                'terms' => $amenities,
            );
        }
        
        if (!empty($tax_query)) {
            $args['tax_query'] = $tax_query;
        }
        
        $query = new WP_Query($args);
        
        ob_start();
        
        if ($query->have_posts()) {
            echo '<div class="phpm-properties-grid">';
            
            while ($query->have_posts()) {
                $query->the_post();
                
                $address = get_post_meta(get_the_ID(), '_phpm_property_address', true);
                $city = get_post_meta(get_the_ID(), '_phpm_property_city', true);
                $units = get_post_meta(get_the_ID(), '_phpm_property_units', true);
                
                ?>
                <div class="phpm-property-card">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="phpm-property-image">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('medium'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <div class="phpm-property-content">
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        
                        <?php if ($address || $city) : ?>
                            <p class="phpm-property-location">
                                <?php echo esc_html($address); ?>
                                <?php if ($address && $city) echo ', '; ?>
                                <?php echo esc_html($city); ?>
                            </p>
                        <?php endif; ?>
                        
                        <?php if ($units) : ?>
                            <p class="phpm-property-units">
                                <?php echo sprintf(_n('%s unit', '%s units', $units, 'plughaus-property'), $units); ?>
                            </p>
                        <?php endif; ?>
                        
                        <a href="<?php the_permalink(); ?>" class="phpm-button">
                            <?php _e('View Property', 'plughaus-property'); ?>
                        </a>
                    </div>
                </div>
                <?php
            }
            
            echo '</div>';
            
            wp_reset_postdata();
        } else {
            echo '<p>' . __('No properties found.', 'plughaus-property') . '</p>';
        }
        
        return ob_get_clean();
    }
    
    /**
     * Property search shortcode
     * Usage: [phpm_property_search]
     */
    public static function property_search_shortcode($atts) {
        ob_start();
        ?>
        <form class="phpm-property-search" method="get" action="<?php echo esc_url(get_post_type_archive_link('phpm_property')); ?>">
            <div class="phpm-search-fields">
                <div class="phpm-search-field">
                    <label for="phpm-search-location"><?php _e('Location', 'plughaus-property'); ?></label>
                    <?php
                    wp_dropdown_categories(array(
                        'taxonomy' => 'phpm_location',
                        'name' => 'location',
                        'id' => 'phpm-search-location',
                        'show_option_all' => __('All Locations', 'plughaus-property'),
                        'hierarchical' => true,
                        'hide_empty' => true,
                    ));
                    ?>
                </div>
                
                <div class="phpm-search-field">
                    <label for="phpm-search-type"><?php _e('Property Type', 'plughaus-property'); ?></label>
                    <?php
                    wp_dropdown_categories(array(
                        'taxonomy' => 'phpm_property_type',
                        'name' => 'property_type',
                        'id' => 'phpm-search-type',
                        'show_option_all' => __('All Types', 'plughaus-property'),
                        'hierarchical' => true,
                        'hide_empty' => true,
                    ));
                    ?>
                </div>
                
                <div class="phpm-search-field">
                    <label for="phpm-search-min-rent"><?php _e('Min Rent', 'plughaus-property'); ?></label>
                    <input type="number" name="min_rent" id="phpm-search-min-rent" placeholder="<?php _e('Min', 'plughaus-property'); ?>">
                </div>
                
                <div class="phpm-search-field">
                    <label for="phpm-search-max-rent"><?php _e('Max Rent', 'plughaus-property'); ?></label>
                    <input type="number" name="max_rent" id="phpm-search-max-rent" placeholder="<?php _e('Max', 'plughaus-property'); ?>">
                </div>
                
                <div class="phpm-search-submit">
                    <button type="submit" class="phpm-button phpm-button-primary">
                        <?php _e('Search Properties', 'plughaus-property'); ?>
                    </button>
                </div>
            </div>
        </form>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Tenant portal shortcode
     * Usage: [phpm_tenant_portal]
     */
    public static function tenant_portal_shortcode($atts) {
        if (!is_user_logged_in()) {
            return '<p>' . __('Please log in to access the tenant portal.', 'plughaus-property') . '</p>';
        }
        
        $current_user = wp_get_current_user();
        
        // Get tenant record
        $tenant = self::get_tenant_by_user($current_user->ID);
        
        if (!$tenant) {
            return '<p>' . __('No tenant account found for your user.', 'plughaus-property') . '</p>';
        }
        
        ob_start();
        ?>
        <div class="phpm-tenant-portal">
            <h2><?php _e('Tenant Portal', 'plughaus-property'); ?></h2>
            
            <div class="phpm-portal-welcome">
                <p><?php echo sprintf(__('Welcome, %s!', 'plughaus-property'), esc_html($current_user->display_name)); ?></p>
            </div>
            
            <div class="phpm-portal-sections">
                <div class="phpm-portal-section">
                    <h3><?php _e('Your Lease', 'plughaus-property'); ?></h3>
                    <?php echo self::get_tenant_lease_info($tenant->ID); ?>
                </div>
                
                <div class="phpm-portal-section">
                    <h3><?php _e('Maintenance Requests', 'plughaus-property'); ?></h3>
                    <?php echo self::get_tenant_maintenance_requests($tenant->ID); ?>
                    <a href="#" class="phpm-button" data-toggle="phpm-maintenance-form">
                        <?php _e('Submit New Request', 'plughaus-property'); ?>
                    </a>
                </div>
                
                <div class="phpm-portal-section">
                    <h3><?php _e('Payment History', 'plughaus-property'); ?></h3>
                    <?php echo self::get_tenant_payment_history($tenant->ID); ?>
                </div>
                
                <div class="phpm-portal-section">
                    <h3><?php _e('Documents', 'plughaus-property'); ?></h3>
                    <?php echo self::get_tenant_documents($tenant->ID); ?>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Maintenance request form shortcode
     * Usage: [phpm_maintenance_request]
     */
    public static function maintenance_request_shortcode($atts) {
        if (!is_user_logged_in()) {
            return '<p>' . __('Please log in to submit a maintenance request.', 'plughaus-property') . '</p>';
        }
        
        ob_start();
        ?>
        <form class="phpm-maintenance-request-form" method="post">
            <?php wp_nonce_field('phpm_maintenance_request', 'phpm_maintenance_nonce'); ?>
            
            <div class="phpm-form-field">
                <label for="phpm-maintenance-title"><?php _e('Issue Title', 'plughaus-property'); ?> <span class="required">*</span></label>
                <input type="text" name="title" id="phpm-maintenance-title" required>
            </div>
            
            <div class="phpm-form-field">
                <label for="phpm-maintenance-category"><?php _e('Category', 'plughaus-property'); ?> <span class="required">*</span></label>
                <?php
                wp_dropdown_categories(array(
                    'taxonomy' => 'phpm_maintenance_category',
                    'name' => 'category',
                    'id' => 'phpm-maintenance-category',
                    'show_option_none' => __('Select Category', 'plughaus-property'),
                    'option_none_value' => '',
                    'required' => true,
                ));
                ?>
            </div>
            
            <div class="phpm-form-field">
                <label for="phpm-maintenance-priority"><?php _e('Priority', 'plughaus-property'); ?></label>
                <select name="priority" id="phpm-maintenance-priority">
                    <option value="low"><?php _e('Low', 'plughaus-property'); ?></option>
                    <option value="medium" selected><?php _e('Medium', 'plughaus-property'); ?></option>
                    <option value="high"><?php _e('High', 'plughaus-property'); ?></option>
                    <option value="emergency"><?php _e('Emergency', 'plughaus-property'); ?></option>
                </select>
            </div>
            
            <div class="phpm-form-field">
                <label for="phpm-maintenance-description"><?php _e('Description', 'plughaus-property'); ?> <span class="required">*</span></label>
                <textarea name="description" id="phpm-maintenance-description" rows="5" required></textarea>
            </div>
            
            <div class="phpm-form-submit">
                <button type="submit" name="phpm_submit_maintenance" class="phpm-button phpm-button-primary">
                    <?php _e('Submit Request', 'plughaus-property'); ?>
                </button>
            </div>
        </form>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Property details shortcode
     * Usage: [phpm_property_details id="123"]
     */
    public static function property_details_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => get_the_ID(),
        ), $atts, 'phpm_property_details');
        
        $property_id = intval($atts['id']);
        
        if (!$property_id) {
            return '';
        }
        
        $property = get_post($property_id);
        
        if (!$property || 'phpm_property' !== $property->post_type) {
            return '';
        }
        
        // Use the same logic as the content filter
        $public = new PHPM_Public();
        return $public->property_content_filter('');
    }
    
    /**
     * Available units shortcode
     * Usage: [phpm_available_units property="123" limit="5"]
     */
    public static function available_units_shortcode($atts) {
        $atts = shortcode_atts(array(
            'property' => '',
            'limit' => -1,
        ), $atts, 'phpm_available_units');
        
        $args = array(
            'post_type' => 'phpm_unit',
            'post_status' => 'available',
            'posts_per_page' => intval($atts['limit']),
        );
        
        if (!empty($atts['property'])) {
            $args['meta_query'] = array(
                array(
                    'key' => '_phpm_unit_property',
                    'value' => intval($atts['property']),
                    'compare' => '=',
                ),
            );
        }
        
        $query = new WP_Query($args);
        
        ob_start();
        
        if ($query->have_posts()) {
            echo '<div class="phpm-units-list">';
            
            while ($query->have_posts()) {
                $query->the_post();
                
                $rent = get_post_meta(get_the_ID(), '_phpm_unit_rent', true);
                $bedrooms = get_post_meta(get_the_ID(), '_phpm_unit_bedrooms', true);
                $bathrooms = get_post_meta(get_the_ID(), '_phpm_unit_bathrooms', true);
                $sqft = get_post_meta(get_the_ID(), '_phpm_unit_sqft', true);
                
                ?>
                <div class="phpm-unit-item">
                    <h4><?php the_title(); ?></h4>
                    
                    <div class="phpm-unit-details">
                        <?php if ($rent) : ?>
                            <span class="phpm-unit-rent"><?php echo sprintf(__('$%s/month', 'plughaus-property'), number_format($rent)); ?></span>
                        <?php endif; ?>
                        
                        <?php if ($bedrooms) : ?>
                            <span class="phpm-unit-bedrooms"><?php echo sprintf(_n('%s Bed', '%s Beds', $bedrooms, 'plughaus-property'), $bedrooms); ?></span>
                        <?php endif; ?>
                        
                        <?php if ($bathrooms) : ?>
                            <span class="phpm-unit-bathrooms"><?php echo sprintf(_n('%s Bath', '%s Baths', $bathrooms, 'plughaus-property'), $bathrooms); ?></span>
                        <?php endif; ?>
                        
                        <?php if ($sqft) : ?>
                            <span class="phpm-unit-sqft"><?php echo sprintf(__('%s sq ft', 'plughaus-property'), number_format($sqft)); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <a href="<?php the_permalink(); ?>" class="phpm-unit-link">
                        <?php _e('View Details', 'plughaus-property'); ?>
                    </a>
                </div>
                <?php
            }
            
            echo '</div>';
            
            wp_reset_postdata();
        } else {
            echo '<p>' . __('No available units found.', 'plughaus-property') . '</p>';
        }
        
        return ob_get_clean();
    }
    
    /**
     * Helper: Get tenant by user ID
     */
    private static function get_tenant_by_user($user_id) {
        $args = array(
            'post_type' => 'phpm_tenant',
            'meta_query' => array(
                array(
                    'key' => '_phpm_tenant_user_id',
                    'value' => $user_id,
                    'compare' => '=',
                ),
            ),
            'posts_per_page' => 1,
        );
        
        $query = new WP_Query($args);
        
        if ($query->have_posts()) {
            return $query->posts[0];
        }
        
        return false;
    }
    
    /**
     * Helper: Get tenant lease info
     */
    private static function get_tenant_lease_info($tenant_id) {
        // This would query for active leases
        return '<p>' . __('No active lease found.', 'plughaus-property') . '</p>';
    }
    
    /**
     * Helper: Get tenant maintenance requests
     */
    private static function get_tenant_maintenance_requests($tenant_id) {
        // This would query for maintenance requests
        return '<p>' . __('No maintenance requests found.', 'plughaus-property') . '</p>';
    }
    
    /**
     * Helper: Get tenant payment history
     */
    private static function get_tenant_payment_history($tenant_id) {
        // This would query for payment history
        return '<p>' . __('No payment history found.', 'plughaus-property') . '</p>';
    }
    
    /**
     * Helper: Get tenant documents
     */
    private static function get_tenant_documents($tenant_id) {
        // This would query for tenant documents
        return '<p>' . __('No documents found.', 'plughaus-property') . '</p>';
    }
}

// Initialize shortcodes
PHPM_Shortcodes::init();