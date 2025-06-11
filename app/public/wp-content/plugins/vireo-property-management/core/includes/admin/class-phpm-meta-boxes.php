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
     * Unit current tenant meta box
     */
    public static function unit_tenant_meta_box($post) {
        $current_lease = get_posts(array(
            'post_type' => 'phpm_lease',
            'meta_query' => array(
                array(
                    'key' => '_phpm_lease_unit_id',
                    'value' => $post->ID,
                    'compare' => '='
                ),
                array(
                    'key' => '_phpm_lease_status',
                    'value' => 'active',
                    'compare' => '='
                )
            ),
            'posts_per_page' => 1
        ));
        
        ?>
        <div class="phpm-unit-tenant">
            <?php if (!empty($current_lease)) : 
                $lease = $current_lease[0];
                $tenant_id = get_post_meta($lease->ID, '_phpm_lease_tenant_id', true);
                if ($tenant_id) {
                    $tenant = get_post($tenant_id);
                    $tenant_email = get_post_meta($tenant_id, '_phpm_tenant_email', true);
                    $tenant_phone = get_post_meta($tenant_id, '_phpm_tenant_phone', true);
                    ?>
                    <p><strong><?php _e('Current Tenant:', 'plughaus-property'); ?></strong></p>
                    <p><a href="<?php echo get_edit_post_link($tenant_id); ?>"><?php echo esc_html($tenant->post_title); ?></a></p>
                    <?php if ($tenant_email) : ?>
                        <p><?php _e('Email:', 'plughaus-property'); ?> <a href="mailto:<?php echo esc_attr($tenant_email); ?>"><?php echo esc_html($tenant_email); ?></a></p>
                    <?php endif; ?>
                    <?php if ($tenant_phone) : ?>
                        <p><?php _e('Phone:', 'plughaus-property'); ?> <a href="tel:<?php echo esc_attr($tenant_phone); ?>"><?php echo esc_html($tenant_phone); ?></a></p>
                    <?php endif; ?>
                    <p><a href="<?php echo get_edit_post_link($lease->ID); ?>" class="button button-secondary"><?php _e('View Lease', 'plughaus-property'); ?></a></p>
                <?php } ?>
            <?php else : ?>
                <p><?php _e('No current tenant', 'plughaus-property'); ?></p>
                <p><a href="<?php echo admin_url('post-new.php?post_type=phpm_lease&unit_id=' . $post->ID); ?>" class="button button-primary"><?php _e('Create Lease', 'plughaus-property'); ?></a></p>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Tenant contact information meta box
     */
    public static function tenant_contact_meta_box($post) {
        wp_nonce_field('phpm_tenant_contact', 'phpm_tenant_contact_nonce');
        
        $email = get_post_meta($post->ID, '_phpm_tenant_email', true);
        $phone = get_post_meta($post->ID, '_phpm_tenant_phone', true);
        $alternate_phone = get_post_meta($post->ID, '_phpm_tenant_alternate_phone', true);
        $date_of_birth = get_post_meta($post->ID, '_phpm_tenant_date_of_birth', true);
        $ssn_last_four = get_post_meta($post->ID, '_phpm_tenant_ssn_last_four', true);
        $employer = get_post_meta($post->ID, '_phpm_tenant_employer', true);
        $employer_phone = get_post_meta($post->ID, '_phpm_tenant_employer_phone', true);
        $monthly_income = get_post_meta($post->ID, '_phpm_tenant_monthly_income', true);
        
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="tenant_email"><?php _e('Email Address', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="email" name="tenant_email" id="tenant_email" value="<?php echo esc_attr($email); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="tenant_phone"><?php _e('Primary Phone', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="tel" name="tenant_phone" id="tenant_phone" value="<?php echo esc_attr($phone); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="tenant_alternate_phone"><?php _e('Alternate Phone', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="tel" name="tenant_alternate_phone" id="tenant_alternate_phone" value="<?php echo esc_attr($alternate_phone); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="tenant_date_of_birth"><?php _e('Date of Birth', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="date" name="tenant_date_of_birth" id="tenant_date_of_birth" value="<?php echo esc_attr($date_of_birth); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="tenant_ssn_last_four"><?php _e('Last 4 SSN Digits', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="text" name="tenant_ssn_last_four" id="tenant_ssn_last_four" value="<?php echo esc_attr($ssn_last_four); ?>" class="small-text" maxlength="4" pattern="[0-9]{4}" />
                    <p class="description"><?php _e('For identification purposes only', 'plughaus-property'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="tenant_employer"><?php _e('Employer', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="text" name="tenant_employer" id="tenant_employer" value="<?php echo esc_attr($employer); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="tenant_employer_phone"><?php _e('Employer Phone', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="tel" name="tenant_employer_phone" id="tenant_employer_phone" value="<?php echo esc_attr($employer_phone); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="tenant_monthly_income"><?php _e('Monthly Income', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="number" name="tenant_monthly_income" id="tenant_monthly_income" value="<?php echo esc_attr($monthly_income); ?>" class="regular-text" step="0.01" />
                    <span>$</span>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Tenant lease information meta box
     */
    public static function tenant_lease_meta_box($post) {
        $current_lease = get_posts(array(
            'post_type' => 'phpm_lease',
            'meta_query' => array(
                array(
                    'key' => '_phpm_lease_tenant_id',
                    'value' => $post->ID,
                    'compare' => '='
                ),
                array(
                    'key' => '_phpm_lease_status',
                    'value' => 'active',
                    'compare' => '='
                )
            ),
            'posts_per_page' => 1
        ));
        
        ?>
        <div class="phpm-tenant-lease">
            <?php if (!empty($current_lease)) : 
                $lease = $current_lease[0];
                $unit_id = get_post_meta($lease->ID, '_phpm_lease_unit_id', true);
                $property_id = get_post_meta($lease->ID, '_phpm_lease_property_id', true);
                $rent_amount = get_post_meta($lease->ID, '_phpm_lease_rent_amount', true);
                $start_date = get_post_meta($lease->ID, '_phpm_lease_start_date', true);
                $end_date = get_post_meta($lease->ID, '_phpm_lease_end_date', true);
                
                $unit = $unit_id ? get_post($unit_id) : null;
                $property = $property_id ? get_post($property_id) : null;
                ?>
                <p><strong><?php _e('Current Lease:', 'plughaus-property'); ?></strong></p>
                <p><a href="<?php echo get_edit_post_link($lease->ID); ?>"><?php echo esc_html($lease->post_title); ?></a></p>
                
                <?php if ($property) : ?>
                    <p><?php _e('Property:', 'plughaus-property'); ?> <a href="<?php echo get_edit_post_link($property->ID); ?>"><?php echo esc_html($property->post_title); ?></a></p>
                <?php endif; ?>
                
                <?php if ($unit) : ?>
                    <p><?php _e('Unit:', 'plughaus-property'); ?> <a href="<?php echo get_edit_post_link($unit->ID); ?>"><?php echo esc_html($unit->post_title); ?></a></p>
                <?php endif; ?>
                
                <?php if ($rent_amount) : ?>
                    <p><?php _e('Rent:', 'plughaus-property'); ?> $<?php echo number_format($rent_amount, 2); ?>/mo</p>
                <?php endif; ?>
                
                <?php if ($start_date && $end_date) : ?>
                    <p><?php _e('Term:', 'plughaus-property'); ?> <?php echo date('M j, Y', strtotime($start_date)); ?> - <?php echo date('M j, Y', strtotime($end_date)); ?></p>
                <?php endif; ?>
                
            <?php else : ?>
                <p><?php _e('No active lease', 'plughaus-property'); ?></p>
                <p><a href="<?php echo admin_url('post-new.php?post_type=phpm_lease&tenant_id=' . $post->ID); ?>" class="button button-primary"><?php _e('Create Lease', 'plughaus-property'); ?></a></p>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Tenant emergency contact meta box
     */
    public static function tenant_emergency_meta_box($post) {
        $emergency_name = get_post_meta($post->ID, '_phpm_tenant_emergency_name', true);
        $emergency_relationship = get_post_meta($post->ID, '_phpm_tenant_emergency_relationship', true);
        $emergency_phone = get_post_meta($post->ID, '_phpm_tenant_emergency_phone', true);
        $emergency_email = get_post_meta($post->ID, '_phpm_tenant_emergency_email', true);
        $emergency_address = get_post_meta($post->ID, '_phpm_tenant_emergency_address', true);
        
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="emergency_name"><?php _e('Full Name', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="text" name="emergency_name" id="emergency_name" value="<?php echo esc_attr($emergency_name); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="emergency_relationship"><?php _e('Relationship', 'plughaus-property'); ?></label></th>
                <td>
                    <select name="emergency_relationship" id="emergency_relationship" class="regular-text">
                        <option value=""><?php _e('Select Relationship', 'plughaus-property'); ?></option>
                        <option value="spouse" <?php selected($emergency_relationship, 'spouse'); ?>><?php _e('Spouse', 'plughaus-property'); ?></option>
                        <option value="parent" <?php selected($emergency_relationship, 'parent'); ?>><?php _e('Parent', 'plughaus-property'); ?></option>
                        <option value="child" <?php selected($emergency_relationship, 'child'); ?>><?php _e('Child', 'plughaus-property'); ?></option>
                        <option value="sibling" <?php selected($emergency_relationship, 'sibling'); ?>><?php _e('Sibling', 'plughaus-property'); ?></option>
                        <option value="friend" <?php selected($emergency_relationship, 'friend'); ?>><?php _e('Friend', 'plughaus-property'); ?></option>
                        <option value="other" <?php selected($emergency_relationship, 'other'); ?>><?php _e('Other', 'plughaus-property'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="emergency_phone"><?php _e('Phone Number', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="tel" name="emergency_phone" id="emergency_phone" value="<?php echo esc_attr($emergency_phone); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="emergency_email"><?php _e('Email Address', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="email" name="emergency_email" id="emergency_email" value="<?php echo esc_attr($emergency_email); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="emergency_address"><?php _e('Address', 'plughaus-property'); ?></label></th>
                <td>
                    <textarea name="emergency_address" id="emergency_address" rows="3" class="large-text"><?php echo esc_textarea($emergency_address); ?></textarea>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Lease details meta box
     */
    public static function lease_details_meta_box($post) {
        wp_nonce_field('phpm_lease_details', 'phpm_lease_details_nonce');
        
        $property_id = get_post_meta($post->ID, '_phpm_lease_property_id', true);
        $unit_id = get_post_meta($post->ID, '_phpm_lease_unit_id', true);
        $tenant_id = get_post_meta($post->ID, '_phpm_lease_tenant_id', true);
        $start_date = get_post_meta($post->ID, '_phpm_lease_start_date', true);
        $end_date = get_post_meta($post->ID, '_phpm_lease_end_date', true);
        $rent_amount = get_post_meta($post->ID, '_phpm_lease_rent_amount', true);
        $security_deposit = get_post_meta($post->ID, '_phpm_lease_security_deposit', true);
        $lease_type = get_post_meta($post->ID, '_phpm_lease_type', true);
        $status = get_post_meta($post->ID, '_phpm_lease_status', true);
        $late_fee = get_post_meta($post->ID, '_phpm_lease_late_fee', true);
        $pet_fee = get_post_meta($post->ID, '_phpm_lease_pet_fee', true);
        
        // Get property and unit from URL if creating new lease
        if (!$property_id && isset($_GET['property_id'])) {
            $property_id = intval($_GET['property_id']);
        }
        if (!$unit_id && isset($_GET['unit_id'])) {
            $unit_id = intval($_GET['unit_id']);
        }
        if (!$tenant_id && isset($_GET['tenant_id'])) {
            $tenant_id = intval($_GET['tenant_id']);
        }
        
        $properties = get_posts(array('post_type' => 'phpm_property', 'posts_per_page' => -1, 'post_status' => 'publish'));
        $units = get_posts(array('post_type' => 'phpm_unit', 'posts_per_page' => -1, 'post_status' => 'publish'));
        $tenants = get_posts(array('post_type' => 'phpm_tenant', 'posts_per_page' => -1, 'post_status' => 'publish'));
        
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="lease_property_id"><?php _e('Property', 'plughaus-property'); ?></label></th>
                <td>
                    <select name="lease_property_id" id="lease_property_id" class="regular-text">
                        <option value=""><?php _e('Select Property', 'plughaus-property'); ?></option>
                        <?php foreach ($properties as $property) : ?>
                            <option value="<?php echo $property->ID; ?>" <?php selected($property_id, $property->ID); ?>>
                                <?php echo esc_html($property->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="lease_unit_id"><?php _e('Unit', 'plughaus-property'); ?></label></th>
                <td>
                    <select name="lease_unit_id" id="lease_unit_id" class="regular-text">
                        <option value=""><?php _e('Select Unit', 'plughaus-property'); ?></option>
                        <?php foreach ($units as $unit) : ?>
                            <option value="<?php echo $unit->ID; ?>" <?php selected($unit_id, $unit->ID); ?>>
                                <?php echo esc_html($unit->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="lease_tenant_id"><?php _e('Tenant', 'plughaus-property'); ?></label></th>
                <td>
                    <select name="lease_tenant_id" id="lease_tenant_id" class="regular-text">
                        <option value=""><?php _e('Select Tenant', 'plughaus-property'); ?></option>
                        <?php foreach ($tenants as $tenant) : ?>
                            <option value="<?php echo $tenant->ID; ?>" <?php selected($tenant_id, $tenant->ID); ?>>
                                <?php echo esc_html($tenant->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="lease_start_date"><?php _e('Start Date', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="date" name="lease_start_date" id="lease_start_date" value="<?php echo esc_attr($start_date); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="lease_end_date"><?php _e('End Date', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="date" name="lease_end_date" id="lease_end_date" value="<?php echo esc_attr($end_date); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="lease_rent_amount"><?php _e('Monthly Rent', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="number" name="lease_rent_amount" id="lease_rent_amount" value="<?php echo esc_attr($rent_amount); ?>" class="regular-text" step="0.01" />
                    <span>$</span>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="lease_security_deposit"><?php _e('Security Deposit', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="number" name="lease_security_deposit" id="lease_security_deposit" value="<?php echo esc_attr($security_deposit); ?>" class="regular-text" step="0.01" />
                    <span>$</span>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="lease_type"><?php _e('Lease Type', 'plughaus-property'); ?></label></th>
                <td>
                    <select name="lease_type" id="lease_type" class="regular-text">
                        <option value=""><?php _e('Select Type', 'plughaus-property'); ?></option>
                        <option value="fixed" <?php selected($lease_type, 'fixed'); ?>><?php _e('Fixed Term', 'plughaus-property'); ?></option>
                        <option value="month-to-month" <?php selected($lease_type, 'month-to-month'); ?>><?php _e('Month-to-Month', 'plughaus-property'); ?></option>
                        <option value="weekly" <?php selected($lease_type, 'weekly'); ?>><?php _e('Weekly', 'plughaus-property'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="lease_status"><?php _e('Status', 'plughaus-property'); ?></label></th>
                <td>
                    <select name="lease_status" id="lease_status" class="regular-text">
                        <option value="draft" <?php selected($status, 'draft'); ?>><?php _e('Draft', 'plughaus-property'); ?></option>
                        <option value="active" <?php selected($status, 'active'); ?>><?php _e('Active', 'plughaus-property'); ?></option>
                        <option value="expired" <?php selected($status, 'expired'); ?>><?php _e('Expired', 'plughaus-property'); ?></option>
                        <option value="terminated" <?php selected($status, 'terminated'); ?>><?php _e('Terminated', 'plughaus-property'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="lease_late_fee"><?php _e('Late Fee', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="number" name="lease_late_fee" id="lease_late_fee" value="<?php echo esc_attr($late_fee); ?>" class="regular-text" step="0.01" />
                    <span>$</span>
                    <p class="description"><?php _e('Fee charged for late rent payments', 'plughaus-property'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="lease_pet_fee"><?php _e('Pet Fee', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="number" name="lease_pet_fee" id="lease_pet_fee" value="<?php echo esc_attr($pet_fee); ?>" class="regular-text" step="0.01" />
                    <span>$</span>
                    <p class="description"><?php _e('Monthly pet fee (if applicable)', 'plughaus-property'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Lease payments information meta box
     */
    public static function lease_payments_meta_box($post) {
        $due_date = get_post_meta($post->ID, '_phpm_lease_due_date', true) ?: '1';
        $payment_method = get_post_meta($post->ID, '_phpm_lease_payment_method', true);
        $auto_pay = get_post_meta($post->ID, '_phpm_lease_auto_pay', true);
        
        // Get recent payments for this lease
        global $wpdb;
        $payments = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}vmp_payments WHERE lease_id = %d ORDER BY payment_date DESC LIMIT 5",
            $post->ID
        ));
        
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="lease_due_date"><?php _e('Due Date', 'plughaus-property'); ?></label></th>
                <td>
                    <select name="lease_due_date" id="lease_due_date" class="regular-text">
                        <?php for ($i = 1; $i <= 31; $i++) : ?>
                            <option value="<?php echo $i; ?>" <?php selected($due_date, $i); ?>>
                                <?php echo $i . ($i == 1 ? 'st' : ($i == 2 ? 'nd' : ($i == 3 ? 'rd' : 'th'))); ?> <?php _e('of the month', 'plughaus-property'); ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="lease_payment_method"><?php _e('Preferred Payment Method', 'plughaus-property'); ?></label></th>
                <td>
                    <select name="lease_payment_method" id="lease_payment_method" class="regular-text">
                        <option value=""><?php _e('Select Method', 'plughaus-property'); ?></option>
                        <option value="check" <?php selected($payment_method, 'check'); ?>><?php _e('Check', 'plughaus-property'); ?></option>
                        <option value="cash" <?php selected($payment_method, 'cash'); ?>><?php _e('Cash', 'plughaus-property'); ?></option>
                        <option value="bank_transfer" <?php selected($payment_method, 'bank_transfer'); ?>><?php _e('Bank Transfer', 'plughaus-property'); ?></option>
                        <option value="online" <?php selected($payment_method, 'online'); ?>><?php _e('Online Payment', 'plughaus-property'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="lease_auto_pay"><?php _e('Auto Pay Enabled', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="checkbox" name="lease_auto_pay" id="lease_auto_pay" value="1" <?php checked($auto_pay, '1'); ?> />
                    <label for="lease_auto_pay"><?php _e('Automatic monthly payments enabled', 'plughaus-property'); ?></label>
                </td>
            </tr>
        </table>
        
        <?php if (!empty($payments)) : ?>
            <h4><?php _e('Recent Payments', 'plughaus-property'); ?></h4>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Date', 'plughaus-property'); ?></th>
                        <th><?php _e('Amount', 'plughaus-property'); ?></th>
                        <th><?php _e('Method', 'plughaus-property'); ?></th>
                        <th><?php _e('Status', 'plughaus-property'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $payment) : ?>
                        <tr>
                            <td><?php echo date('M j, Y', strtotime($payment->payment_date)); ?></td>
                            <td>$<?php echo number_format($payment->amount, 2); ?></td>
                            <td><?php echo ucfirst(str_replace('_', ' ', $payment->payment_method)); ?></td>
                            <td><span class="payment-status status-<?php echo esc_attr($payment->status); ?>"><?php echo ucfirst($payment->status); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p><?php _e('No payments recorded yet.', 'plughaus-property'); ?></p>
        <?php endif; ?>
        
        <p><a href="<?php echo admin_url('post-new.php?post_type=phpm_payment&lease_id=' . $post->ID); ?>" class="button button-secondary"><?php _e('Record Payment', 'plughaus-property'); ?></a></p>
        <?php
    }
    
    /**
     * Maintenance request details meta box
     */
    public static function maintenance_details_meta_box($post) {
        wp_nonce_field('phpm_maintenance_details', 'phpm_maintenance_details_nonce');
        
        $property_id = get_post_meta($post->ID, '_phpm_maintenance_property_id', true);
        $unit_id = get_post_meta($post->ID, '_phpm_maintenance_unit_id', true);
        $tenant_id = get_post_meta($post->ID, '_phpm_maintenance_tenant_id', true);
        $priority = get_post_meta($post->ID, '_phpm_maintenance_priority', true);
        $category = get_post_meta($post->ID, '_phpm_maintenance_category', true);
        $status = get_post_meta($post->ID, '_phpm_maintenance_status', true);
        $description = get_post_meta($post->ID, '_phpm_maintenance_description', true);
        $tenant_accessible = get_post_meta($post->ID, '_phpm_maintenance_tenant_accessible', true);
        $estimated_cost = get_post_meta($post->ID, '_phpm_maintenance_estimated_cost', true);
        $actual_cost = get_post_meta($post->ID, '_phpm_maintenance_actual_cost', true);
        
        $properties = get_posts(array('post_type' => 'phpm_property', 'posts_per_page' => -1, 'post_status' => 'publish'));
        $units = get_posts(array('post_type' => 'phpm_unit', 'posts_per_page' => -1, 'post_status' => 'publish'));
        $tenants = get_posts(array('post_type' => 'phpm_tenant', 'posts_per_page' => -1, 'post_status' => 'publish'));
        
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="maintenance_property_id"><?php _e('Property', 'plughaus-property'); ?></label></th>
                <td>
                    <select name="maintenance_property_id" id="maintenance_property_id" class="regular-text">
                        <option value=""><?php _e('Select Property', 'plughaus-property'); ?></option>
                        <?php foreach ($properties as $property) : ?>
                            <option value="<?php echo $property->ID; ?>" <?php selected($property_id, $property->ID); ?>>
                                <?php echo esc_html($property->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="maintenance_unit_id"><?php _e('Unit (Optional)', 'plughaus-property'); ?></label></th>
                <td>
                    <select name="maintenance_unit_id" id="maintenance_unit_id" class="regular-text">
                        <option value=""><?php _e('Select Unit', 'plughaus-property'); ?></option>
                        <?php foreach ($units as $unit) : ?>
                            <option value="<?php echo $unit->ID; ?>" <?php selected($unit_id, $unit->ID); ?>>
                                <?php echo esc_html($unit->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="maintenance_tenant_id"><?php _e('Requesting Tenant', 'plughaus-property'); ?></label></th>
                <td>
                    <select name="maintenance_tenant_id" id="maintenance_tenant_id" class="regular-text">
                        <option value=""><?php _e('Select Tenant', 'plughaus-property'); ?></option>
                        <?php foreach ($tenants as $tenant) : ?>
                            <option value="<?php echo $tenant->ID; ?>" <?php selected($tenant_id, $tenant->ID); ?>>
                                <?php echo esc_html($tenant->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="maintenance_priority"><?php _e('Priority', 'plughaus-property'); ?></label></th>
                <td>
                    <select name="maintenance_priority" id="maintenance_priority" class="regular-text">
                        <option value="low" <?php selected($priority, 'low'); ?>><?php _e('Low', 'plughaus-property'); ?></option>
                        <option value="medium" <?php selected($priority, 'medium'); ?>><?php _e('Medium', 'plughaus-property'); ?></option>
                        <option value="high" <?php selected($priority, 'high'); ?>><?php _e('High', 'plughaus-property'); ?></option>
                        <option value="emergency" <?php selected($priority, 'emergency'); ?>><?php _e('Emergency', 'plughaus-property'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="maintenance_category"><?php _e('Category', 'plughaus-property'); ?></label></th>
                <td>
                    <select name="maintenance_category" id="maintenance_category" class="regular-text">
                        <option value=""><?php _e('Select Category', 'plughaus-property'); ?></option>
                        <option value="plumbing" <?php selected($category, 'plumbing'); ?>><?php _e('Plumbing', 'plughaus-property'); ?></option>
                        <option value="electrical" <?php selected($category, 'electrical'); ?>><?php _e('Electrical', 'plughaus-property'); ?></option>
                        <option value="hvac" <?php selected($category, 'hvac'); ?>><?php _e('HVAC', 'plughaus-property'); ?></option>
                        <option value="appliances" <?php selected($category, 'appliances'); ?>><?php _e('Appliances', 'plughaus-property'); ?></option>
                        <option value="flooring" <?php selected($category, 'flooring'); ?>><?php _e('Flooring', 'plughaus-property'); ?></option>
                        <option value="painting" <?php selected($category, 'painting'); ?>><?php _e('Painting', 'plughaus-property'); ?></option>
                        <option value="exterior" <?php selected($category, 'exterior'); ?>><?php _e('Exterior', 'plughaus-property'); ?></option>
                        <option value="security" <?php selected($category, 'security'); ?>><?php _e('Security', 'plughaus-property'); ?></option>
                        <option value="other" <?php selected($category, 'other'); ?>><?php _e('Other', 'plughaus-property'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="maintenance_status"><?php _e('Status', 'plughaus-property'); ?></label></th>
                <td>
                    <select name="maintenance_status" id="maintenance_status" class="regular-text">
                        <option value="open" <?php selected($status, 'open'); ?>><?php _e('Open', 'plughaus-property'); ?></option>
                        <option value="in_progress" <?php selected($status, 'in_progress'); ?>><?php _e('In Progress', 'plughaus-property'); ?></option>
                        <option value="on_hold" <?php selected($status, 'on_hold'); ?>><?php _e('On Hold', 'plughaus-property'); ?></option>
                        <option value="completed" <?php selected($status, 'completed'); ?>><?php _e('Completed', 'plughaus-property'); ?></option>
                        <option value="cancelled" <?php selected($status, 'cancelled'); ?>><?php _e('Cancelled', 'plughaus-property'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="maintenance_description"><?php _e('Description', 'plughaus-property'); ?></label></th>
                <td>
                    <textarea name="maintenance_description" id="maintenance_description" rows="4" class="large-text"><?php echo esc_textarea($description); ?></textarea>
                    <p class="description"><?php _e('Detailed description of the maintenance issue', 'plughaus-property'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="maintenance_tenant_accessible"><?php _e('Tenant Access Required', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="checkbox" name="maintenance_tenant_accessible" id="maintenance_tenant_accessible" value="1" <?php checked($tenant_accessible, '1'); ?> />
                    <label for="maintenance_tenant_accessible"><?php _e('Access to tenant unit required', 'plughaus-property'); ?></label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="maintenance_estimated_cost"><?php _e('Estimated Cost', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="number" name="maintenance_estimated_cost" id="maintenance_estimated_cost" value="<?php echo esc_attr($estimated_cost); ?>" class="regular-text" step="0.01" />
                    <span>$</span>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="maintenance_actual_cost"><?php _e('Actual Cost', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="number" name="maintenance_actual_cost" id="maintenance_actual_cost" value="<?php echo esc_attr($actual_cost); ?>" class="regular-text" step="0.01" />
                    <span>$</span>
                    <p class="description"><?php _e('Final cost after completion', 'plughaus-property'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Maintenance assignment meta box
     */
    public static function maintenance_assignment_meta_box($post) {
        $assigned_to = get_post_meta($post->ID, '_phpm_maintenance_assigned_to', true);
        $assigned_date = get_post_meta($post->ID, '_phpm_maintenance_assigned_date', true);
        $scheduled_date = get_post_meta($post->ID, '_phpm_maintenance_scheduled_date', true);
        $completed_date = get_post_meta($post->ID, '_phpm_maintenance_completed_date', true);
        $contractor_name = get_post_meta($post->ID, '_phpm_maintenance_contractor_name', true);
        $contractor_phone = get_post_meta($post->ID, '_phpm_maintenance_contractor_phone', true);
        $contractor_email = get_post_meta($post->ID, '_phpm_maintenance_contractor_email', true);
        
        // Get users who can be assigned maintenance tasks
        $assignable_users = get_users(array('capability' => 'edit_phpm_maintenance'));
        
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="maintenance_assigned_to"><?php _e('Assigned To', 'plughaus-property'); ?></label></th>
                <td>
                    <select name="maintenance_assigned_to" id="maintenance_assigned_to" class="widefat">
                        <option value=""><?php _e('Unassigned', 'plughaus-property'); ?></option>
                        <?php foreach ($assignable_users as $user) : ?>
                            <option value="<?php echo $user->ID; ?>" <?php selected($assigned_to, $user->ID); ?>>
                                <?php echo esc_html($user->display_name); ?> (<?php echo esc_html($user->user_email); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="maintenance_assigned_date"><?php _e('Date Assigned', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="date" name="maintenance_assigned_date" id="maintenance_assigned_date" value="<?php echo esc_attr($assigned_date); ?>" class="widefat" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="maintenance_scheduled_date"><?php _e('Scheduled Date', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="datetime-local" name="maintenance_scheduled_date" id="maintenance_scheduled_date" value="<?php echo esc_attr($scheduled_date); ?>" class="widefat" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="maintenance_completed_date"><?php _e('Completed Date', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="datetime-local" name="maintenance_completed_date" id="maintenance_completed_date" value="<?php echo esc_attr($completed_date); ?>" class="widefat" />
                </td>
            </tr>
        </table>
        
        <h4><?php _e('External Contractor', 'plughaus-property'); ?></h4>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="maintenance_contractor_name"><?php _e('Contractor Name', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="text" name="maintenance_contractor_name" id="maintenance_contractor_name" value="<?php echo esc_attr($contractor_name); ?>" class="widefat" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="maintenance_contractor_phone"><?php _e('Phone', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="tel" name="maintenance_contractor_phone" id="maintenance_contractor_phone" value="<?php echo esc_attr($contractor_phone); ?>" class="widefat" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="maintenance_contractor_email"><?php _e('Email', 'plughaus-property'); ?></label></th>
                <td>
                    <input type="email" name="maintenance_contractor_email" id="maintenance_contractor_email" value="<?php echo esc_attr($contractor_email); ?>" class="widefat" />
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