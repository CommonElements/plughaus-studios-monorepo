<?php
/**
 * Meta Boxes for PlugHaus Property Management
 * Handles all custom fields for Properties, Units, Tenants, and Leases
 */

if (!defined('ABSPATH')) {
    exit;
}

class PHPM_Meta_Boxes {
    
    /**
     * Initialize meta boxes
     */
    public static function init() {
        add_action('add_meta_boxes', array(__CLASS__, 'add_meta_boxes'));
        add_action('save_post', array(__CLASS__, 'save_meta_boxes'));
    }
    
    /**
     * Add meta boxes for all post types
     */
    public static function add_meta_boxes() {
        // Property meta boxes
        add_meta_box(
            'phpm_property_details',
            __('Property Details', 'plughaus-property'),
            array(__CLASS__, 'property_details_meta_box'),
            'phpm_property',
            'normal',
            'high'
        );
        
        add_meta_box(
            'phpm_property_address',
            __('Address Information', 'plughaus-property'),
            array(__CLASS__, 'property_address_meta_box'),
            'phpm_property',
            'normal',
            'high'
        );
        
        add_meta_box(
            'phpm_property_units',
            __('Units Overview', 'plughaus-property'),
            array(__CLASS__, 'property_units_meta_box'),
            'phpm_property',
            'side',
            'default'
        );
        
        // Unit meta boxes
        add_meta_box(
            'phpm_unit_details',
            __('Unit Details', 'plughaus-property'),
            array(__CLASS__, 'unit_details_meta_box'),
            'phpm_unit',
            'normal',
            'high'
        );
        
        add_meta_box(
            'phpm_unit_property',
            __('Property Assignment', 'plughaus-property'),
            array(__CLASS__, 'unit_property_meta_box'),
            'phpm_unit',
            'side',
            'high'
        );
        
        add_meta_box(
            'phpm_unit_tenant',
            __('Current Tenant', 'plughaus-property'),
            array(__CLASS__, 'unit_tenant_meta_box'),
            'phpm_unit',
            'side',
            'default'
        );
        
        // Tenant meta boxes
        add_meta_box(
            'phpm_tenant_contact',
            __('Contact Information', 'plughaus-property'),
            array(__CLASS__, 'tenant_contact_meta_box'),
            'phpm_tenant',
            'normal',
            'high'
        );
        
        add_meta_box(
            'phpm_tenant_lease',
            __('Lease Information', 'plughaus-property'),
            array(__CLASS__, 'tenant_lease_meta_box'),
            'phpm_tenant',
            'side',
            'high'
        );
        
        add_meta_box(
            'phpm_tenant_emergency',
            __('Emergency Contact', 'plughaus-property'),
            array(__CLASS__, 'tenant_emergency_meta_box'),
            'phpm_tenant',
            'normal',
            'default'
        );
        
        // Lease meta boxes
        add_meta_box(
            'phpm_lease_details',
            __('Lease Details', 'plughaus-property'),
            array(__CLASS__, 'lease_details_meta_box'),
            'phpm_lease',
            'normal',
            'high'
        );
        
        add_meta_box(
            'phpm_lease_payments',
            __('Payment Information', 'plughaus-property'),
            array(__CLASS__, 'lease_payments_meta_box'),
            'phpm_lease',
            'normal',
            'default'
        );
        
        // Maintenance meta boxes
        add_meta_box(
            'phpm_maintenance_details',
            __('Request Details', 'plughaus-property'),
            array(__CLASS__, 'maintenance_details_meta_box'),
            'phpm_maintenance',
            'normal',
            'high'
        );
        
        add_meta_box(
            'phpm_maintenance_assignment',
            __('Assignment', 'plughaus-property'),
            array(__CLASS__, 'maintenance_assignment_meta_box'),
            'phpm_maintenance',
            'side',
            'high'
        );
    }
    
    /**
     * Property details meta box
     */
    public static function property_details_meta_box($post) {
        wp_nonce_field('phpm_property_details', 'phpm_property_details_nonce');
        
        $property_type = get_post_meta($post->ID, '_phpm_property_type', true);
        $total_units = get_post_meta($post->ID, '_phpm_property_total_units', true);
        $year_built = get_post_meta($post->ID, '_phpm_property_year_built', true);
        $square_footage = get_post_meta($post->ID, '_phpm_property_square_footage', true);
        $lot_size = get_post_meta($post->ID, '_phpm_property_lot_size', true);
        $description = get_post_meta($post->ID, '_phpm_property_description', true);
        
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="property_type"><?php _e('Property Type', 'plughaus-property'); ?></label></th>
                <td>
                    <select name="property_type" id="property_type" class="regular-text">
                        <option value=""><?php _e('Select Type', 'plughaus-property'); ?></option>
                        <option value="single-family" <?php selected($property_type, 'single-family'); ?>><?php _e('Single Family Home', 'plughaus-property'); ?></option>
                        <option value="apartment" <?php selected($property_type, 'apartment'); ?>><?php _e('Apartment Building', 'plughaus-property'); ?></option>
                        <option value="condo" <?php selected($property_type, 'condo'); ?>><?php _e('Condominium', 'plughaus-property'); ?></option>
                        <option value="townhouse" <?php selected($property_type, 'townhouse'); ?>><?php _e('Townhouse', 'plughaus-property'); ?></option>
                        <option value="duplex" <?php selected($property_type, 'duplex'); ?>><?php _e('Duplex', 'plughaus-property'); ?></option>
                        <option value="commercial" <?php selected($property_type, 'commercial'); ?>><?php _e('Commercial', 'plughaus-property'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="total_units"><?php _e('Total Units', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="number" name="total_units" id="total_units" value="<?php echo esc_attr($total_units); ?>" class="small-text" min="1" />
                    <p class="description"><?php _e('Number of rentable units in this property', 'plughaus-property'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="year_built"><?php _e('Year Built', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="number" name="year_built" id="year_built" value="<?php echo esc_attr($year_built); ?>" class="small-text" min="1800" max="<?php echo date('Y'); ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="square_footage"><?php _e('Square Footage', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="number" name="square_footage" id="square_footage" value="<?php echo esc_attr($square_footage); ?>" class="regular-text" />
                    <span><?php _e('sq ft', 'plughaus-property'); ?></span>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="lot_size"><?php _e('Lot Size', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="text" name="lot_size" id="lot_size" value="<?php echo esc_attr($lot_size); ?>" class="regular-text" />
                    <p class="description"><?php _e('e.g., "0.25 acres" or "5,000 sq ft"', 'plughaus-property'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="property_description"><?php _e('Description', 'plughaus-property'); ?></label></th>
                <td>
                    <textarea name="property_description" id="property_description" rows="4" class="large-text"><?php echo esc_textarea($description); ?></textarea>
                    <p class="description"><?php _e('Additional property details and amenities', 'plughaus-property'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Property address meta box
     */
    public static function property_address_meta_box($post) {
        $street_address = get_post_meta($post->ID, '_phpm_property_address', true);
        $city = get_post_meta($post->ID, '_phpm_property_city', true);
        $state = get_post_meta($post->ID, '_phpm_property_state', true);
        $zip_code = get_post_meta($post->ID, '_phpm_property_zip', true);
        $country = get_post_meta($post->ID, '_phpm_property_country', true) ?: 'US';
        
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="street_address"><?php _e('Street Address', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="text" name="street_address" id="street_address" value="<?php echo esc_attr($street_address); ?>" class="large-text" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="city"><?php _e('City', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="text" name="city" id="city" value="<?php echo esc_attr($city); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="state"><?php _e('State/Province', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="text" name="state" id="state" value="<?php echo esc_attr($state); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="zip_code"><?php _e('ZIP/Postal Code', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="text" name="zip_code" id="zip_code" value="<?php echo esc_attr($zip_code); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="country"><?php _e('Country', 'plughaus-property'); ?></label></th>
                <td>
                    <select name="country" id="country" class="regular-text">
                        <option value="US" <?php selected($country, 'US'); ?>><?php _e('United States', 'plughaus-property'); ?></option>
                        <option value="CA" <?php selected($country, 'CA'); ?>><?php _e('Canada', 'plughaus-property'); ?></option>
                        <option value="UK" <?php selected($country, 'UK'); ?>><?php _e('United Kingdom', 'plughaus-property'); ?></option>
                        <option value="AU" <?php selected($country, 'AU'); ?>><?php _e('Australia', 'plughaus-property'); ?></option>
                    </select>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Property units overview meta box
     */
    public static function property_units_meta_box($post) {
        $units = get_posts(array(
            'post_type' => 'phpm_unit',
            'meta_query' => array(
                array(
                    'key' => '_phpm_unit_property_id',
                    'value' => $post->ID,
                    'compare' => '='
                )
            ),
            'posts_per_page' => -1
        ));
        
        ?>
        <div class="phpm-units-overview">
            <p><strong><?php echo count($units); ?></strong> <?php _e('units assigned to this property', 'plughaus-property'); ?></p>
            
            <?php if (!empty($units)) : ?>
                <ul>
                    <?php foreach ($units as $unit) : 
                        $unit_number = get_post_meta($unit->ID, '_phpm_unit_number', true);
                        $rent = get_post_meta($unit->ID, '_phpm_unit_rent', true);
                    ?>
                        <li>
                            <a href="<?php echo get_edit_post_link($unit->ID); ?>">
                                <?php echo $unit_number ? sprintf(__('Unit %s', 'plughaus-property'), $unit_number) : $unit->post_title; ?>
                            </a>
                            <?php if ($rent) : ?>
                                <span class="unit-rent">($<?php echo number_format($rent); ?>/mo)</span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            
            <p><a href="<?php echo admin_url('post-new.php?post_type=phpm_unit&property_id=' . $post->ID); ?>" class="button button-secondary"><?php _e('Add New Unit', 'plughaus-property'); ?></a></p>
        </div>
        <?php
    }
    
    /**
     * Unit details meta box
     */
    public static function unit_details_meta_box($post) {
        wp_nonce_field('phpm_unit_details', 'phpm_unit_details_nonce');
        
        $unit_number = get_post_meta($post->ID, '_phpm_unit_number', true);
        $bedrooms = get_post_meta($post->ID, '_phpm_unit_bedrooms', true);
        $bathrooms = get_post_meta($post->ID, '_phpm_unit_bathrooms', true);
        $square_feet = get_post_meta($post->ID, '_phpm_unit_square_feet', true);
        $rent_amount = get_post_meta($post->ID, '_phpm_unit_rent', true);
        $security_deposit = get_post_meta($post->ID, '_phpm_unit_security_deposit', true);
        $features = get_post_meta($post->ID, '_phpm_unit_features', true);
        
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="unit_number"><?php _e('Unit Number', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="text" name="unit_number" id="unit_number" value="<?php echo esc_attr($unit_number); ?>" class="regular-text" />
                    <p class="description"><?php _e('e.g., "101", "A", "2B"', 'plughaus-property'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="bedrooms"><?php _e('Bedrooms', 'plughaus-property'); ?></label></th>
                <td>
                    <select name="bedrooms" id="bedrooms">
                        <option value=""><?php _e('Select', 'plughaus-property'); ?></option>
                        <option value="0" <?php selected($bedrooms, '0'); ?>><?php _e('Studio', 'plughaus-property'); ?></option>
                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                            <option value="<?php echo $i; ?>" <?php selected($bedrooms, $i); ?>><?php echo $i; ?></option>
                        <?php endfor; ?>
                        <option value="6+" <?php selected($bedrooms, '6+'); ?>><?php _e('6+', 'plughaus-property'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="bathrooms"><?php _e('Bathrooms', 'plughaus-property'); ?></label></th>
                <td>
                    <select name="bathrooms" id="bathrooms">
                        <option value=""><?php _e('Select', 'plughaus-property'); ?></option>
                        <?php 
                        $bathroom_options = array('0.5', '1', '1.5', '2', '2.5', '3', '3.5', '4', '4+');
                        foreach ($bathroom_options as $option) : ?>
                            <option value="<?php echo $option; ?>" <?php selected($bathrooms, $option); ?>><?php echo $option; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="square_feet"><?php _e('Square Feet', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="number" name="square_feet" id="square_feet" value="<?php echo esc_attr($square_feet); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="rent_amount"><?php _e('Monthly Rent', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="number" name="rent_amount" id="rent_amount" value="<?php echo esc_attr($rent_amount); ?>" class="regular-text" step="0.01" />
                    <span>$</span>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="security_deposit"><?php _e('Security Deposit', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="number" name="security_deposit" id="security_deposit" value="<?php echo esc_attr($security_deposit); ?>" class="regular-text" step="0.01" />
                    <span>$</span>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="unit_features"><?php _e('Features & Amenities', 'plughaus-property'); ?></label></th>
                <td>
                    <textarea name="unit_features" id="unit_features" rows="4" class="large-text"><?php echo esc_textarea($features); ?></textarea>
                    <p class="description"><?php _e('List special features (one per line)', 'plughaus-property'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Unit property assignment meta box
     */
    public static function unit_property_meta_box($post) {
        $property_id = get_post_meta($post->ID, '_phpm_unit_property_id', true);
        
        // Get property ID from URL if creating new unit
        if (!$property_id && isset($_GET['property_id'])) {
            $property_id = intval($_GET['property_id']);
        }
        
        $properties = get_posts(array(
            'post_type' => 'phpm_property',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));
        
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="property_id"><?php _e('Property', 'plughaus-property'); ?></label></th>
                <td>
                    <select name="property_id" id="property_id" class="widefat">
                        <option value=""><?php _e('Select Property', 'plughaus-property'); ?></option>
                        <?php foreach ($properties as $property) : ?>
                            <option value="<?php echo $property->ID; ?>" <?php selected($property_id, $property->ID); ?>>
                                <?php echo esc_html($property->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description"><?php _e('Which property does this unit belong to?', 'plughaus-property'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Save all meta box data
     */
    public static function save_meta_boxes($post_id) {
        // Check if this is an autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        $post_type = get_post_type($post_id);
        
        // Save property meta
        if ($post_type === 'phpm_property' && isset($_POST['phpm_property_details_nonce'])) {
            if (wp_verify_nonce($_POST['phpm_property_details_nonce'], 'phpm_property_details')) {
                $property_fields = array(
                    'property_type', 'total_units', 'year_built', 'square_footage', 
                    'lot_size', 'property_description', 'street_address', 'city', 
                    'state', 'zip_code', 'country'
                );
                
                foreach ($property_fields as $field) {
                    if (isset($_POST[$field])) {
                        update_post_meta($post_id, '_phpm_' . $field, sanitize_text_field($_POST[$field]));
                    }
                }
            }
        }
        
        // Save unit meta
        if ($post_type === 'phpm_unit' && isset($_POST['phpm_unit_details_nonce'])) {
            if (wp_verify_nonce($_POST['phpm_unit_details_nonce'], 'phpm_unit_details')) {
                $unit_fields = array(
                    'unit_number' => 'sanitize_text_field',
                    'bedrooms' => 'sanitize_text_field', 
                    'bathrooms' => 'sanitize_text_field',
                    'square_feet' => 'intval',
                    'rent_amount' => 'floatval',
                    'security_deposit' => 'floatval',
                    'unit_features' => 'sanitize_textarea_field',
                    'property_id' => 'intval'
                );
                
                foreach ($unit_fields as $field => $sanitizer) {
                    if (isset($_POST[$field])) {
                        $value = call_user_func($sanitizer, $_POST[$field]);
                        update_post_meta($post_id, '_phpm_' . $field, $value);
                    }
                }
            }
        }
    }
}

// Initialize meta boxes
PHPM_Meta_Boxes::init();