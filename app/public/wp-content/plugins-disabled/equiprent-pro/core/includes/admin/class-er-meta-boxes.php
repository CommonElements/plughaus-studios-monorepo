<?php
/**
 * Meta Boxes for EquipRent Pro
 * Handles all custom fields for Equipment, Bookings, Customers, and Maintenance
 *
 * @package EquipRent_Pro
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handles meta boxes for all EquipRent Pro post types
 */
class ER_Meta_Boxes {
    
    /**
     * Initialize meta boxes
     */
    public static function init() {
        add_action('add_meta_boxes', array(__CLASS__, 'add_meta_boxes'));
        add_action('save_post', array(__CLASS__, 'save_meta_boxes'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_meta_box_scripts'));
    }
    
    /**
     * Add meta boxes for all post types
     */
    public static function add_meta_boxes() {
        // Equipment meta boxes
        add_meta_box(
            'er_equipment_details',
            __('Equipment Details', 'equiprent-pro'),
            array(__CLASS__, 'equipment_details_meta_box'),
            'equipment',
            'normal',
            'high'
        );
        
        add_meta_box(
            'er_equipment_pricing',
            __('Pricing & Availability', 'equiprent-pro'),
            array(__CLASS__, 'equipment_pricing_meta_box'),
            'equipment',
            'normal',
            'high'
        );
        
        add_meta_box(
            'er_equipment_specifications',
            __('Specifications', 'equiprent-pro'),
            array(__CLASS__, 'equipment_specifications_meta_box'),
            'equipment',
            'normal',
            'default'
        );
        
        add_meta_box(
            'er_equipment_maintenance',
            __('Maintenance Info', 'equiprent-pro'),
            array(__CLASS__, 'equipment_maintenance_meta_box'),
            'equipment',
            'side',
            'default'
        );
        
        add_meta_box(
            'er_equipment_bookings',
            __('Current Bookings', 'equiprent-pro'),
            array(__CLASS__, 'equipment_bookings_meta_box'),
            'equipment',
            'side',
            'default'
        );
        
        // Booking meta boxes
        add_meta_box(
            'er_booking_details',
            __('Booking Details', 'equiprent-pro'),
            array(__CLASS__, 'booking_details_meta_box'),
            'er_booking',
            'normal',
            'high'
        );
        
        add_meta_box(
            'er_booking_equipment',
            __('Equipment Items', 'equiprent-pro'),
            array(__CLASS__, 'booking_equipment_meta_box'),
            'er_booking',
            'normal',
            'high'
        );
        
        add_meta_box(
            'er_booking_payment',
            __('Payment Information', 'equiprent-pro'),
            array(__CLASS__, 'booking_payment_meta_box'),
            'er_booking',
            'normal',
            'default'
        );
        
        add_meta_box(
            'er_booking_delivery',
            __('Delivery/Pickup', 'equiprent-pro'),
            array(__CLASS__, 'booking_delivery_meta_box'),
            'er_booking',
            'side',
            'high'
        );
        
        add_meta_box(
            'er_booking_notes',
            __('Booking Notes', 'equiprent-pro'),
            array(__CLASS__, 'booking_notes_meta_box'),
            'er_booking',
            'side',
            'default'
        );
        
        // Customer meta boxes
        add_meta_box(
            'er_customer_contact',
            __('Contact Information', 'equiprent-pro'),
            array(__CLASS__, 'customer_contact_meta_box'),
            'er_customer',
            'normal',
            'high'
        );
        
        add_meta_box(
            'er_customer_business',
            __('Business Information', 'equiprent-pro'),
            array(__CLASS__, 'customer_business_meta_box'),
            'er_customer',
            'normal',
            'default'
        );
        
        add_meta_box(
            'er_customer_billing',
            __('Billing & Payment', 'equiprent-pro'),
            array(__CLASS__, 'customer_billing_meta_box'),
            'er_customer',
            'normal',
            'default'
        );
        
        add_meta_box(
            'er_customer_bookings',
            __('Booking History', 'equiprent-pro'),
            array(__CLASS__, 'customer_bookings_meta_box'),
            'er_customer',
            'side',
            'high'
        );
        
        add_meta_box(
            'er_customer_notes',
            __('Customer Notes', 'equiprent-pro'),
            array(__CLASS__, 'customer_notes_meta_box'),
            'er_customer',
            'side',
            'default'
        );
        
        // Maintenance meta boxes
        add_meta_box(
            'er_maintenance_details',
            __('Maintenance Details', 'equiprent-pro'),
            array(__CLASS__, 'maintenance_details_meta_box'),
            'er_maintenance',
            'normal',
            'high'
        );
        
        add_meta_box(
            'er_maintenance_assignment',
            __('Assignment & Scheduling', 'equiprent-pro'),
            array(__CLASS__, 'maintenance_assignment_meta_box'),
            'er_maintenance',
            'side',
            'high'
        );
        
        add_meta_box(
            'er_maintenance_parts',
            __('Parts & Costs', 'equiprent-pro'),
            array(__CLASS__, 'maintenance_parts_meta_box'),
            'er_maintenance',
            'normal',
            'default'
        );
    }
    
    /**
     * Enqueue scripts for meta boxes
     */
    public static function enqueue_meta_box_scripts() {
        $screen = get_current_screen();
        
        if (in_array($screen->post_type, array('equipment', 'er_booking', 'er_customer', 'er_maintenance'))) {
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_style('jquery-ui-datepicker', '//code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css');
        }
    }
    
    /**
     * Equipment details meta box
     */
    public static function equipment_details_meta_box($post) {
        wp_nonce_field('er_equipment_details', 'er_equipment_details_nonce');
        
        $sku = get_post_meta($post->ID, '_equipment_sku', true);
        $model = get_post_meta($post->ID, '_equipment_model', true);
        $serial_number = get_post_meta($post->ID, '_equipment_serial_number', true);
        $year = get_post_meta($post->ID, '_equipment_year', true);
        $condition = get_post_meta($post->ID, '_equipment_condition', true);
        $status = get_post_meta($post->ID, '_equipment_status', true) ?: 'available';
        $acquisition_date = get_post_meta($post->ID, '_equipment_acquisition_date', true);
        $acquisition_cost = get_post_meta($post->ID, '_equipment_acquisition_cost', true);
        $location = get_post_meta($post->ID, '_equipment_location', true);
        $notes = get_post_meta($post->ID, '_equipment_notes', true);
        
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="equipment_sku"><?php _e('SKU/Item Code', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="text" name="equipment_sku" id="equipment_sku" value="<?php echo esc_attr($sku); ?>" class="regular-text" />
                    <p class="description"><?php _e('Unique identifier for this equipment item', 'equiprent-pro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="equipment_model"><?php _e('Model Number', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="text" name="equipment_model" id="equipment_model" value="<?php echo esc_attr($model); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="equipment_serial_number"><?php _e('Serial Number', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="text" name="equipment_serial_number" id="equipment_serial_number" value="<?php echo esc_attr($serial_number); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="equipment_year"><?php _e('Year', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="number" name="equipment_year" id="equipment_year" value="<?php echo esc_attr($year); ?>" class="small-text" min="1900" max="<?php echo date('Y'); ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="equipment_condition"><?php _e('Condition', 'equiprent-pro'); ?></label></th>
                <td>
                    <select name="equipment_condition" id="equipment_condition" class="regular-text">
                        <option value=""><?php _e('Select Condition', 'equiprent-pro'); ?></option>
                        <option value="excellent" <?php selected($condition, 'excellent'); ?>><?php _e('Excellent', 'equiprent-pro'); ?></option>
                        <option value="good" <?php selected($condition, 'good'); ?>><?php _e('Good', 'equiprent-pro'); ?></option>
                        <option value="fair" <?php selected($condition, 'fair'); ?>><?php _e('Fair', 'equiprent-pro'); ?></option>
                        <option value="poor" <?php selected($condition, 'poor'); ?>><?php _e('Poor', 'equiprent-pro'); ?></option>
                        <option value="needs_repair" <?php selected($condition, 'needs_repair'); ?>><?php _e('Needs Repair', 'equiprent-pro'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="equipment_status"><?php _e('Status', 'equiprent-pro'); ?></label></th>
                <td>
                    <select name="equipment_status" id="equipment_status" class="regular-text">
                        <?php 
                        $statuses = ER_Post_Types::get_equipment_statuses();
                        foreach ($statuses as $status_key => $status_label) : ?>
                            <option value="<?php echo esc_attr($status_key); ?>" <?php selected($status, $status_key); ?>>
                                <?php echo esc_html($status_label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="equipment_acquisition_date"><?php _e('Acquisition Date', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="date" name="equipment_acquisition_date" id="equipment_acquisition_date" value="<?php echo esc_attr($acquisition_date); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="equipment_acquisition_cost"><?php _e('Acquisition Cost', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="number" name="equipment_acquisition_cost" id="equipment_acquisition_cost" value="<?php echo esc_attr($acquisition_cost); ?>" class="regular-text" step="0.01" />
                    <span><?php echo ER_Utilities::get_setting('currency_symbol', '$'); ?></span>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="equipment_location"><?php _e('Current Location', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="text" name="equipment_location" id="equipment_location" value="<?php echo esc_attr($location); ?>" class="regular-text" />
                    <p class="description"><?php _e('Where is this equipment currently stored?', 'equiprent-pro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="equipment_notes"><?php _e('Notes', 'equiprent-pro'); ?></label></th>
                <td>
                    <textarea name="equipment_notes" id="equipment_notes" rows="4" class="large-text"><?php echo esc_textarea($notes); ?></textarea>
                    <p class="description"><?php _e('Internal notes about this equipment item', 'equiprent-pro'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Equipment pricing meta box
     */
    public static function equipment_pricing_meta_box($post) {
        $daily_rate = get_post_meta($post->ID, '_equipment_daily_rate', true);
        $weekly_rate = get_post_meta($post->ID, '_equipment_weekly_rate', true);
        $monthly_rate = get_post_meta($post->ID, '_equipment_monthly_rate', true);
        $security_deposit = get_post_meta($post->ID, '_equipment_security_deposit', true);
        $replacement_value = get_post_meta($post->ID, '_equipment_replacement_value', true);
        $minimum_rental_period = get_post_meta($post->ID, '_equipment_min_rental_period', true) ?: '1';
        $maximum_rental_period = get_post_meta($post->ID, '_equipment_max_rental_period', true);
        $advance_booking_days = get_post_meta($post->ID, '_equipment_advance_booking_days', true) ?: '30';
        $total_quantity = get_post_meta($post->ID, '_equipment_total_quantity', true) ?: '1';
        $available_quantity = get_post_meta($post->ID, '_equipment_available_quantity', true) ?: '1';
        
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="equipment_daily_rate"><?php _e('Daily Rate', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="number" name="equipment_daily_rate" id="equipment_daily_rate" value="<?php echo esc_attr($daily_rate); ?>" class="regular-text" step="0.01" />
                    <span><?php echo ER_Utilities::get_setting('currency_symbol', '$'); ?></span>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="equipment_weekly_rate"><?php _e('Weekly Rate', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="number" name="equipment_weekly_rate" id="equipment_weekly_rate" value="<?php echo esc_attr($weekly_rate); ?>" class="regular-text" step="0.01" />
                    <span><?php echo ER_Utilities::get_setting('currency_symbol', '$'); ?></span>
                    <p class="description"><?php _e('Optional: Override daily rate for weekly rentals', 'equiprent-pro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="equipment_monthly_rate"><?php _e('Monthly Rate', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="number" name="equipment_monthly_rate" id="equipment_monthly_rate" value="<?php echo esc_attr($monthly_rate); ?>" class="regular-text" step="0.01" />
                    <span><?php echo ER_Utilities::get_setting('currency_symbol', '$'); ?></span>
                    <p class="description"><?php _e('Optional: Override daily rate for monthly rentals', 'equiprent-pro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="equipment_security_deposit"><?php _e('Security Deposit', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="number" name="equipment_security_deposit" id="equipment_security_deposit" value="<?php echo esc_attr($security_deposit); ?>" class="regular-text" step="0.01" />
                    <span><?php echo ER_Utilities::get_setting('currency_symbol', '$'); ?></span>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="equipment_replacement_value"><?php _e('Replacement Value', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="number" name="equipment_replacement_value" id="equipment_replacement_value" value="<?php echo esc_attr($replacement_value); ?>" class="regular-text" step="0.01" />
                    <span><?php echo ER_Utilities::get_setting('currency_symbol', '$'); ?></span>
                    <p class="description"><?php _e('Cost to replace if lost or damaged', 'equiprent-pro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="equipment_min_rental_period"><?php _e('Minimum Rental Period', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="number" name="equipment_min_rental_period" id="equipment_min_rental_period" value="<?php echo esc_attr($minimum_rental_period); ?>" class="small-text" min="1" />
                    <span><?php _e('days', 'equiprent-pro'); ?></span>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="equipment_max_rental_period"><?php _e('Maximum Rental Period', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="number" name="equipment_max_rental_period" id="equipment_max_rental_period" value="<?php echo esc_attr($maximum_rental_period); ?>" class="small-text" min="1" />
                    <span><?php _e('days (leave empty for no limit)', 'equiprent-pro'); ?></span>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="equipment_advance_booking_days"><?php _e('Advance Booking', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="number" name="equipment_advance_booking_days" id="equipment_advance_booking_days" value="<?php echo esc_attr($advance_booking_days); ?>" class="small-text" min="0" />
                    <span><?php _e('days in advance', 'equiprent-pro'); ?></span>
                    <p class="description"><?php _e('How far in advance can this be booked?', 'equiprent-pro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="equipment_total_quantity"><?php _e('Total Quantity', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="number" name="equipment_total_quantity" id="equipment_total_quantity" value="<?php echo esc_attr($total_quantity); ?>" class="small-text" min="1" />
                    <p class="description"><?php _e('Total number of this item in inventory', 'equiprent-pro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="equipment_available_quantity"><?php _e('Available Quantity', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="number" name="equipment_available_quantity" id="equipment_available_quantity" value="<?php echo esc_attr($available_quantity); ?>" class="small-text" min="0" />
                    <p class="description"><?php _e('Number currently available for rental', 'equiprent-pro'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Equipment specifications meta box
     */
    public static function equipment_specifications_meta_box($post) {
        $weight = get_post_meta($post->ID, '_equipment_weight', true);
        $dimensions_length = get_post_meta($post->ID, '_equipment_dimensions_length', true);
        $dimensions_width = get_post_meta($post->ID, '_equipment_dimensions_width', true);
        $dimensions_height = get_post_meta($post->ID, '_equipment_dimensions_height', true);
        $power_requirements = get_post_meta($post->ID, '_equipment_power_requirements', true);
        $fuel_type = get_post_meta($post->ID, '_equipment_fuel_type', true);
        $accessories_included = get_post_meta($post->ID, '_equipment_accessories_included', true);
        $setup_instructions = get_post_meta($post->ID, '_equipment_setup_instructions', true);
        $safety_requirements = get_post_meta($post->ID, '_equipment_safety_requirements', true);
        
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="equipment_weight"><?php _e('Weight', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="number" name="equipment_weight" id="equipment_weight" value="<?php echo esc_attr($weight); ?>" class="regular-text" step="0.1" />
                    <span><?php _e('lbs', 'equiprent-pro'); ?></span>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Dimensions', 'equiprent-pro'); ?></th>
                <td>
                    <label for="equipment_dimensions_length"><?php _e('L:', 'equiprent-pro'); ?></label>
                    <input type="number" name="equipment_dimensions_length" id="equipment_dimensions_length" value="<?php echo esc_attr($dimensions_length); ?>" class="small-text" step="0.1" />
                    
                    <label for="equipment_dimensions_width"><?php _e('W:', 'equiprent-pro'); ?></label>
                    <input type="number" name="equipment_dimensions_width" id="equipment_dimensions_width" value="<?php echo esc_attr($dimensions_width); ?>" class="small-text" step="0.1" />
                    
                    <label for="equipment_dimensions_height"><?php _e('H:', 'equiprent-pro'); ?></label>
                    <input type="number" name="equipment_dimensions_height" id="equipment_dimensions_height" value="<?php echo esc_attr($dimensions_height); ?>" class="small-text" step="0.1" />
                    <span><?php _e('inches', 'equiprent-pro'); ?></span>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="equipment_power_requirements"><?php _e('Power Requirements', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="text" name="equipment_power_requirements" id="equipment_power_requirements" value="<?php echo esc_attr($power_requirements); ?>" class="regular-text" />
                    <p class="description"><?php _e('e.g., "110V AC", "12V DC", "Battery powered"', 'equiprent-pro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="equipment_fuel_type"><?php _e('Fuel Type', 'equiprent-pro'); ?></label></th>
                <td>
                    <select name="equipment_fuel_type" id="equipment_fuel_type" class="regular-text">
                        <option value=""><?php _e('Select Fuel Type', 'equiprent-pro'); ?></option>
                        <option value="electric" <?php selected($fuel_type, 'electric'); ?>><?php _e('Electric', 'equiprent-pro'); ?></option>
                        <option value="gasoline" <?php selected($fuel_type, 'gasoline'); ?>><?php _e('Gasoline', 'equiprent-pro'); ?></option>
                        <option value="diesel" <?php selected($fuel_type, 'diesel'); ?>><?php _e('Diesel', 'equiprent-pro'); ?></option>
                        <option value="propane" <?php selected($fuel_type, 'propane'); ?>><?php _e('Propane', 'equiprent-pro'); ?></option>
                        <option value="battery" <?php selected($fuel_type, 'battery'); ?>><?php _e('Battery', 'equiprent-pro'); ?></option>
                        <option value="manual" <?php selected($fuel_type, 'manual'); ?>><?php _e('Manual', 'equiprent-pro'); ?></option>
                        <option value="other" <?php selected($fuel_type, 'other'); ?>><?php _e('Other', 'equiprent-pro'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="equipment_accessories_included"><?php _e('Accessories Included', 'equiprent-pro'); ?></label></th>
                <td>
                    <textarea name="equipment_accessories_included" id="equipment_accessories_included" rows="3" class="large-text"><?php echo esc_textarea($accessories_included); ?></textarea>
                    <p class="description"><?php _e('List all accessories, attachments, and included items', 'equiprent-pro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="equipment_setup_instructions"><?php _e('Setup Instructions', 'equiprent-pro'); ?></label></th>
                <td>
                    <textarea name="equipment_setup_instructions" id="equipment_setup_instructions" rows="4" class="large-text"><?php echo esc_textarea($setup_instructions); ?></textarea>
                    <p class="description"><?php _e('Instructions for customers on how to set up this equipment', 'equiprent-pro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="equipment_safety_requirements"><?php _e('Safety Requirements', 'equiprent-pro'); ?></label></th>
                <td>
                    <textarea name="equipment_safety_requirements" id="equipment_safety_requirements" rows="3" class="large-text"><?php echo esc_textarea($safety_requirements); ?></textarea>
                    <p class="description"><?php _e('Safety precautions, required protective equipment, etc.', 'equiprent-pro'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Equipment maintenance info meta box
     */
    public static function equipment_maintenance_meta_box($post) {
        $last_maintenance = get_post_meta($post->ID, '_equipment_last_maintenance', true);
        $next_maintenance = get_post_meta($post->ID, '_equipment_next_maintenance', true);
        $maintenance_hours = get_post_meta($post->ID, '_equipment_maintenance_hours', true);
        $total_rental_hours = get_post_meta($post->ID, '_equipment_total_rental_hours', true);
        $maintenance_notes = get_post_meta($post->ID, '_equipment_maintenance_notes', true);
        
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="equipment_last_maintenance"><?php _e('Last Maintenance', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="date" name="equipment_last_maintenance" id="equipment_last_maintenance" value="<?php echo esc_attr($last_maintenance); ?>" class="widefat" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="equipment_next_maintenance"><?php _e('Next Maintenance Due', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="date" name="equipment_next_maintenance" id="equipment_next_maintenance" value="<?php echo esc_attr($next_maintenance); ?>" class="widefat" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="equipment_maintenance_hours"><?php _e('Maintenance Interval', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="number" name="equipment_maintenance_hours" id="equipment_maintenance_hours" value="<?php echo esc_attr($maintenance_hours); ?>" class="widefat" min="0" />
                    <p class="description"><?php _e('Hours between maintenance', 'equiprent-pro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="equipment_total_rental_hours"><?php _e('Total Rental Hours', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="number" name="equipment_total_rental_hours" id="equipment_total_rental_hours" value="<?php echo esc_attr($total_rental_hours); ?>" class="widefat" min="0" step="0.1" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="equipment_maintenance_notes"><?php _e('Maintenance Notes', 'equiprent-pro'); ?></label></th>
                <td>
                    <textarea name="equipment_maintenance_notes" id="equipment_maintenance_notes" rows="4" class="widefat"><?php echo esc_textarea($maintenance_notes); ?></textarea>
                </td>
            </tr>
        </table>
        
        <p><a href="<?php echo admin_url('post-new.php?post_type=er_maintenance&equipment_id=' . $post->ID); ?>" class="button button-secondary"><?php _e('Schedule Maintenance', 'equiprent-pro'); ?></a></p>
        <?php
    }
    
    /**
     * Equipment current bookings meta box
     */
    public static function equipment_bookings_meta_box($post) {
        global $wpdb;
        
        // Get current and upcoming bookings for this equipment
        $bookings = $wpdb->get_results($wpdb->prepare("
            SELECT p.ID, p.post_title, 
                   pm1.meta_value as start_date,
                   pm2.meta_value as end_date,
                   pm3.meta_value as booking_status,
                   pm4.meta_value as customer_id
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_start_date'
            LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_end_date'
            LEFT JOIN {$wpdb->postmeta} pm3 ON p.ID = pm3.post_id AND pm3.meta_key = '_booking_status'
            LEFT JOIN {$wpdb->postmeta} pm4 ON p.ID = pm4.post_id AND pm4.meta_key = '_customer_id'
            WHERE p.post_type = 'er_booking'
            AND p.post_status = 'publish'
            AND EXISTS (
                SELECT 1 FROM {$wpdb->postmeta} pm5 
                WHERE pm5.post_id = p.ID 
                AND pm5.meta_key = '_equipment_items' 
                AND pm5.meta_value LIKE %s
            )
            AND pm2.meta_value >= %s
            ORDER BY pm1.meta_value ASC
            LIMIT 10
        ", '%"' . $post->ID . '"%', date('Y-m-d')));
        
        ?>
        <div class="er-equipment-bookings">
            <?php if (!empty($bookings)) : ?>
                <ul>
                    <?php foreach ($bookings as $booking) : 
                        $customer = $booking->customer_id ? get_post($booking->customer_id) : null;
                        $status_class = 'status-' . ($booking->booking_status ?: 'pending');
                        ?>
                        <li class="booking-item <?php echo esc_attr($status_class); ?>">
                            <div class="booking-info">
                                <strong><a href="<?php echo get_edit_post_link($booking->ID); ?>"><?php echo esc_html($booking->post_title); ?></a></strong>
                                <div class="booking-dates">
                                    <?php 
                                    if ($booking->start_date && $booking->end_date) {
                                        echo ER_Utilities::format_date($booking->start_date) . ' - ' . ER_Utilities::format_date($booking->end_date);
                                    }
                                    ?>
                                </div>
                                <?php if ($customer) : ?>
                                    <div class="booking-customer">
                                        <a href="<?php echo get_edit_post_link($customer->ID); ?>"><?php echo esc_html($customer->post_title); ?></a>
                                    </div>
                                <?php endif; ?>
                                <span class="booking-status"><?php echo esc_html(ucfirst($booking->booking_status ?: 'pending')); ?></span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p><?php _e('No current or upcoming bookings', 'equiprent-pro'); ?></p>
            <?php endif; ?>
            
            <p><a href="<?php echo admin_url('post-new.php?post_type=er_booking&equipment_id=' . $post->ID); ?>" class="button button-primary"><?php _e('Create Booking', 'equiprent-pro'); ?></a></p>
        </div>
        <?php
    }
    
    /**
     * Booking details meta box
     */
    public static function booking_details_meta_box($post) {
        wp_nonce_field('er_booking_details', 'er_booking_details_nonce');
        
        $booking_number = get_post_meta($post->ID, '_booking_number', true);
        $customer_id = get_post_meta($post->ID, '_customer_id', true);
        $start_date = get_post_meta($post->ID, '_start_date', true);
        $end_date = get_post_meta($post->ID, '_end_date', true);
        $booking_status = get_post_meta($post->ID, '_booking_status', true) ?: 'pending';
        $total_amount = get_post_meta($post->ID, '_total_amount', true);
        $discount_amount = get_post_meta($post->ID, '_discount_amount', true);
        $tax_amount = get_post_meta($post->ID, '_tax_amount', true);
        $special_instructions = get_post_meta($post->ID, '_special_instructions', true);
        
        // Get customer from URL if creating new booking
        if (!$customer_id && isset($_GET['customer_id'])) {
            $customer_id = intval($_GET['customer_id']);
        }
        
        $customers = get_posts(array(
            'post_type' => 'er_customer',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));
        
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="booking_number"><?php _e('Booking Number', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="text" name="booking_number" id="booking_number" value="<?php echo esc_attr($booking_number); ?>" class="regular-text" />
                    <p class="description"><?php _e('Unique booking reference number', 'equiprent-pro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="customer_id"><?php _e('Customer', 'equiprent-pro'); ?> *</label></th>
                <td>
                    <select name="customer_id" id="customer_id" class="regular-text er-customer-select" required>
                        <option value=""><?php _e('Select Customer', 'equiprent-pro'); ?></option>
                        <?php foreach ($customers as $customer) : ?>
                            <option value="<?php echo $customer->ID; ?>" <?php selected($customer_id, $customer->ID); ?>>
                                <?php echo esc_html($customer->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <a href="<?php echo admin_url('post-new.php?post_type=er_customer'); ?>" class="button button-secondary" target="_blank"><?php _e('Add New Customer', 'equiprent-pro'); ?></a>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="start_date"><?php _e('Start Date', 'equiprent-pro'); ?> *</label></th>
                <td>
                    <input type="date" name="start_date" id="start_date" value="<?php echo esc_attr($start_date); ?>" class="regular-text er-date-picker" required />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="end_date"><?php _e('End Date', 'equiprent-pro'); ?> *</label></th>
                <td>
                    <input type="date" name="end_date" id="end_date" value="<?php echo esc_attr($end_date); ?>" class="regular-text er-date-picker" required />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="booking_status"><?php _e('Status', 'equiprent-pro'); ?></label></th>
                <td>
                    <select name="booking_status" id="booking_status" class="regular-text">
                        <?php 
                        $statuses = ER_Post_Types::get_booking_statuses();
                        foreach ($statuses as $status_key => $status_label) : ?>
                            <option value="<?php echo esc_attr($status_key); ?>" <?php selected($booking_status, $status_key); ?>>
                                <?php echo esc_html($status_label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="total_amount"><?php _e('Total Amount', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="number" name="total_amount" id="total_amount" value="<?php echo esc_attr($total_amount); ?>" class="regular-text" step="0.01" readonly />
                    <span><?php echo ER_Utilities::get_setting('currency_symbol', '$'); ?></span>
                    <p class="description"><?php _e('Automatically calculated from equipment items', 'equiprent-pro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="discount_amount"><?php _e('Discount', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="number" name="discount_amount" id="discount_amount" value="<?php echo esc_attr($discount_amount); ?>" class="regular-text" step="0.01" />
                    <span><?php echo ER_Utilities::get_setting('currency_symbol', '$'); ?></span>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="tax_amount"><?php _e('Tax', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="number" name="tax_amount" id="tax_amount" value="<?php echo esc_attr($tax_amount); ?>" class="regular-text" step="0.01" />
                    <span><?php echo ER_Utilities::get_setting('currency_symbol', '$'); ?></span>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="special_instructions"><?php _e('Special Instructions', 'equiprent-pro'); ?></label></th>
                <td>
                    <textarea name="special_instructions" id="special_instructions" rows="4" class="large-text"><?php echo esc_textarea($special_instructions); ?></textarea>
                    <p class="description"><?php _e('Special requests, delivery instructions, etc.', 'equiprent-pro'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Booking equipment items meta box
     */
    public static function booking_equipment_meta_box($post) {
        $equipment_items = get_post_meta($post->ID, '_equipment_items', true);
        if (!is_array($equipment_items)) {
            $equipment_items = array();
        }
        
        // Get equipment from URL if creating new booking
        if (empty($equipment_items) && isset($_GET['equipment_id'])) {
            $equipment_id = intval($_GET['equipment_id']);
            $equipment_items = array(
                array(
                    'equipment_id' => $equipment_id,
                    'quantity' => 1,
                    'daily_rate' => get_post_meta($equipment_id, '_equipment_daily_rate', true)
                )
            );
        }
        
        $all_equipment = get_posts(array(
            'post_type' => 'equipment',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));
        
        ?>
        <div id="er-booking-equipment-items">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Equipment', 'equiprent-pro'); ?></th>
                        <th><?php _e('Quantity', 'equiprent-pro'); ?></th>
                        <th><?php _e('Daily Rate', 'equiprent-pro'); ?></th>
                        <th><?php _e('Total', 'equiprent-pro'); ?></th>
                        <th><?php _e('Actions', 'equiprent-pro'); ?></th>
                    </tr>
                </thead>
                <tbody id="equipment-items-list">
                    <?php if (!empty($equipment_items)) : ?>
                        <?php foreach ($equipment_items as $index => $item) : 
                            $equipment = get_post($item['equipment_id']);
                            ?>
                            <tr class="equipment-item-row" data-index="<?php echo $index; ?>">
                                <td>
                                    <select name="equipment_items[<?php echo $index; ?>][equipment_id]" class="equipment-select" required>
                                        <option value=""><?php _e('Select Equipment', 'equiprent-pro'); ?></option>
                                        <?php foreach ($all_equipment as $eq) : ?>
                                            <option value="<?php echo $eq->ID; ?>" <?php selected($item['equipment_id'], $eq->ID); ?> data-daily-rate="<?php echo esc_attr(get_post_meta($eq->ID, '_equipment_daily_rate', true)); ?>">
                                                <?php echo esc_html($eq->post_title); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="equipment_items[<?php echo $index; ?>][quantity]" value="<?php echo esc_attr($item['quantity']); ?>" class="small-text item-quantity" min="1" required />
                                </td>
                                <td>
                                    <input type="number" name="equipment_items[<?php echo $index; ?>][daily_rate]" value="<?php echo esc_attr($item['daily_rate']); ?>" class="regular-text item-rate" step="0.01" />
                                    <span><?php echo ER_Utilities::get_setting('currency_symbol', '$'); ?></span>
                                </td>
                                <td class="item-total">
                                    <?php echo ER_Utilities::format_currency($item['quantity'] * $item['daily_rate']); ?>
                                </td>
                                <td>
                                    <button type="button" class="button remove-equipment-item"><?php _e('Remove', 'equiprent-pro'); ?></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <p>
                <button type="button" id="add-equipment-item" class="button button-secondary"><?php _e('Add Equipment Item', 'equiprent-pro'); ?></button>
            </p>
        </div>
        
        <script type="text/html" id="equipment-item-template">
            <tr class="equipment-item-row" data-index="{{INDEX}}">
                <td>
                    <select name="equipment_items[{{INDEX}}][equipment_id]" class="equipment-select" required>
                        <option value=""><?php _e('Select Equipment', 'equiprent-pro'); ?></option>
                        <?php foreach ($all_equipment as $eq) : ?>
                            <option value="<?php echo $eq->ID; ?>" data-daily-rate="<?php echo esc_attr(get_post_meta($eq->ID, '_equipment_daily_rate', true)); ?>">
                                <?php echo esc_html($eq->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <input type="number" name="equipment_items[{{INDEX}}][quantity]" value="1" class="small-text item-quantity" min="1" required />
                </td>
                <td>
                    <input type="number" name="equipment_items[{{INDEX}}][daily_rate]" value="" class="regular-text item-rate" step="0.01" />
                    <span><?php echo ER_Utilities::get_setting('currency_symbol', '$'); ?></span>
                </td>
                <td class="item-total">
                    <?php echo ER_Utilities::get_setting('currency_symbol', '$'); ?>0.00
                </td>
                <td>
                    <button type="button" class="button remove-equipment-item"><?php _e('Remove', 'equiprent-pro'); ?></button>
                </td>
            </tr>
        </script>
        <?php
    }
    
    /**
     * Booking payment information meta box
     */
    public static function booking_payment_meta_box($post) {
        $payment_status = get_post_meta($post->ID, '_payment_status', true) ?: 'pending';
        $payment_method = get_post_meta($post->ID, '_payment_method', true);
        $deposit_amount = get_post_meta($post->ID, '_deposit_amount', true);
        $deposit_paid = get_post_meta($post->ID, '_deposit_paid', true);
        $balance_due = get_post_meta($post->ID, '_balance_due', true);
        $payment_terms = get_post_meta($post->ID, '_payment_terms', true);
        $payment_notes = get_post_meta($post->ID, '_payment_notes', true);
        
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="payment_status"><?php _e('Payment Status', 'equiprent-pro'); ?></label></th>
                <td>
                    <select name="payment_status" id="payment_status" class="regular-text">
                        <?php 
                        $statuses = ER_Post_Types::get_payment_statuses();
                        foreach ($statuses as $status_key => $status_label) : ?>
                            <option value="<?php echo esc_attr($status_key); ?>" <?php selected($payment_status, $status_key); ?>>
                                <?php echo esc_html($status_label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="payment_method"><?php _e('Payment Method', 'equiprent-pro'); ?></label></th>
                <td>
                    <select name="payment_method" id="payment_method" class="regular-text">
                        <option value=""><?php _e('Select Method', 'equiprent-pro'); ?></option>
                        <option value="cash" <?php selected($payment_method, 'cash'); ?>><?php _e('Cash', 'equiprent-pro'); ?></option>
                        <option value="check" <?php selected($payment_method, 'check'); ?>><?php _e('Check', 'equiprent-pro'); ?></option>
                        <option value="credit_card" <?php selected($payment_method, 'credit_card'); ?>><?php _e('Credit Card', 'equiprent-pro'); ?></option>
                        <option value="debit_card" <?php selected($payment_method, 'debit_card'); ?>><?php _e('Debit Card', 'equiprent-pro'); ?></option>
                        <option value="bank_transfer" <?php selected($payment_method, 'bank_transfer'); ?>><?php _e('Bank Transfer', 'equiprent-pro'); ?></option>
                        <option value="online" <?php selected($payment_method, 'online'); ?>><?php _e('Online Payment', 'equiprent-pro'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="deposit_amount"><?php _e('Deposit Required', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="number" name="deposit_amount" id="deposit_amount" value="<?php echo esc_attr($deposit_amount); ?>" class="regular-text" step="0.01" />
                    <span><?php echo ER_Utilities::get_setting('currency_symbol', '$'); ?></span>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="deposit_paid"><?php _e('Deposit Paid', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="number" name="deposit_paid" id="deposit_paid" value="<?php echo esc_attr($deposit_paid); ?>" class="regular-text" step="0.01" />
                    <span><?php echo ER_Utilities::get_setting('currency_symbol', '$'); ?></span>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="balance_due"><?php _e('Balance Due', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="number" name="balance_due" id="balance_due" value="<?php echo esc_attr($balance_due); ?>" class="regular-text" step="0.01" readonly />
                    <span><?php echo ER_Utilities::get_setting('currency_symbol', '$'); ?></span>
                    <p class="description"><?php _e('Automatically calculated', 'equiprent-pro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="payment_terms"><?php _e('Payment Terms', 'equiprent-pro'); ?></label></th>
                <td>
                    <select name="payment_terms" id="payment_terms" class="regular-text">
                        <option value="full_upfront" <?php selected($payment_terms, 'full_upfront'); ?>><?php _e('Full Payment Upfront', 'equiprent-pro'); ?></option>
                        <option value="deposit_balance" <?php selected($payment_terms, 'deposit_balance'); ?>><?php _e('Deposit + Balance on Pickup', 'equiprent-pro'); ?></option>
                        <option value="deposit_balance_return" <?php selected($payment_terms, 'deposit_balance_return'); ?>><?php _e('Deposit + Balance on Return', 'equiprent-pro'); ?></option>
                        <option value="net_30" <?php selected($payment_terms, 'net_30'); ?>><?php _e('Net 30 Days', 'equiprent-pro'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="payment_notes"><?php _e('Payment Notes', 'equiprent-pro'); ?></label></th>
                <td>
                    <textarea name="payment_notes" id="payment_notes" rows="3" class="large-text"><?php echo esc_textarea($payment_notes); ?></textarea>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Booking delivery/pickup meta box
     */
    public static function booking_delivery_meta_box($post) {
        $delivery_method = get_post_meta($post->ID, '_delivery_method', true);
        $delivery_address = get_post_meta($post->ID, '_delivery_address', true);
        $delivery_date = get_post_meta($post->ID, '_delivery_date', true);
        $delivery_time = get_post_meta($post->ID, '_delivery_time', true);
        $pickup_date = get_post_meta($post->ID, '_pickup_date', true);
        $pickup_time = get_post_meta($post->ID, '_pickup_time', true);
        $delivery_fee = get_post_meta($post->ID, '_delivery_fee', true);
        $delivery_notes = get_post_meta($post->ID, '_delivery_notes', true);
        
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="delivery_method"><?php _e('Delivery Method', 'equiprent-pro'); ?></label></th>
                <td>
                    <select name="delivery_method" id="delivery_method" class="widefat">
                        <option value="pickup" <?php selected($delivery_method, 'pickup'); ?>><?php _e('Customer Pickup', 'equiprent-pro'); ?></option>
                        <option value="delivery" <?php selected($delivery_method, 'delivery'); ?>><?php _e('We Deliver', 'equiprent-pro'); ?></option>
                        <option value="shipping" <?php selected($delivery_method, 'shipping'); ?>><?php _e('Shipping', 'equiprent-pro'); ?></option>
                    </select>
                </td>
            </tr>
            <tr class="delivery-address-row" style="display: <?php echo ($delivery_method === 'delivery' || $delivery_method === 'shipping') ? 'table-row' : 'none'; ?>;">
                <th scope="row"><label for="delivery_address"><?php _e('Delivery Address', 'equiprent-pro'); ?></label></th>
                <td>
                    <textarea name="delivery_address" id="delivery_address" rows="3" class="widefat"><?php echo esc_textarea($delivery_address); ?></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="delivery_date"><?php _e('Delivery Date', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="date" name="delivery_date" id="delivery_date" value="<?php echo esc_attr($delivery_date); ?>" class="widefat" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="delivery_time"><?php _e('Delivery Time', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="time" name="delivery_time" id="delivery_time" value="<?php echo esc_attr($delivery_time); ?>" class="widefat" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="pickup_date"><?php _e('Pickup Date', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="date" name="pickup_date" id="pickup_date" value="<?php echo esc_attr($pickup_date); ?>" class="widefat" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="pickup_time"><?php _e('Pickup Time', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="time" name="pickup_time" id="pickup_time" value="<?php echo esc_attr($pickup_time); ?>" class="widefat" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="delivery_fee"><?php _e('Delivery Fee', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="number" name="delivery_fee" id="delivery_fee" value="<?php echo esc_attr($delivery_fee); ?>" class="widefat" step="0.01" />
                    <span><?php echo ER_Utilities::get_setting('currency_symbol', '$'); ?></span>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="delivery_notes"><?php _e('Delivery Notes', 'equiprent-pro'); ?></label></th>
                <td>
                    <textarea name="delivery_notes" id="delivery_notes" rows="3" class="widefat"><?php echo esc_textarea($delivery_notes); ?></textarea>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Booking notes meta box
     */
    public static function booking_notes_meta_box($post) {
        $internal_notes = get_post_meta($post->ID, '_internal_notes', true);
        
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="internal_notes"><?php _e('Internal Notes', 'equiprent-pro'); ?></label></th>
                <td>
                    <textarea name="internal_notes" id="internal_notes" rows="6" class="widefat"><?php echo esc_textarea($internal_notes); ?></textarea>
                    <p class="description"><?php _e('Internal notes not visible to customer', 'equiprent-pro'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Customer contact information meta box
     */
    public static function customer_contact_meta_box($post) {
        wp_nonce_field('er_customer_contact', 'er_customer_contact_nonce');
        
        $customer_type = get_post_meta($post->ID, '_customer_type', true) ?: 'individual';
        $first_name = get_post_meta($post->ID, '_first_name', true);
        $last_name = get_post_meta($post->ID, '_last_name', true);
        $email = get_post_meta($post->ID, '_email', true);
        $phone = get_post_meta($post->ID, '_phone', true);
        $mobile_phone = get_post_meta($post->ID, '_mobile_phone', true);
        $address = get_post_meta($post->ID, '_address', true);
        $city = get_post_meta($post->ID, '_city', true);
        $state = get_post_meta($post->ID, '_state', true);
        $zip_code = get_post_meta($post->ID, '_zip_code', true);
        $country = get_post_meta($post->ID, '_country', true) ?: 'US';
        
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="customer_type"><?php _e('Customer Type', 'equiprent-pro'); ?></label></th>
                <td>
                    <select name="customer_type" id="customer_type" class="regular-text">
                        <option value="individual" <?php selected($customer_type, 'individual'); ?>><?php _e('Individual', 'equiprent-pro'); ?></option>
                        <option value="business" <?php selected($customer_type, 'business'); ?>><?php _e('Business', 'equiprent-pro'); ?></option>
                    </select>
                </td>
            </tr>
            <tr class="individual-fields" style="display: <?php echo $customer_type === 'individual' ? 'table-row' : 'none'; ?>;">
                <th scope="row"><label for="first_name"><?php _e('First Name', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="text" name="first_name" id="first_name" value="<?php echo esc_attr($first_name); ?>" class="regular-text" />
                </td>
            </tr>
            <tr class="individual-fields" style="display: <?php echo $customer_type === 'individual' ? 'table-row' : 'none'; ?>;">
                <th scope="row"><label for="last_name"><?php _e('Last Name', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="text" name="last_name" id="last_name" value="<?php echo esc_attr($last_name); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="email"><?php _e('Email Address', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="email" name="email" id="email" value="<?php echo esc_attr($email); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="phone"><?php _e('Phone Number', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="tel" name="phone" id="phone" value="<?php echo esc_attr($phone); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="mobile_phone"><?php _e('Mobile Phone', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="tel" name="mobile_phone" id="mobile_phone" value="<?php echo esc_attr($mobile_phone); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="address"><?php _e('Address', 'equiprent-pro'); ?></label></th>
                <td>
                    <textarea name="address" id="address" rows="3" class="large-text"><?php echo esc_textarea($address); ?></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="city"><?php _e('City', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="text" name="city" id="city" value="<?php echo esc_attr($city); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="state"><?php _e('State/Province', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="text" name="state" id="state" value="<?php echo esc_attr($state); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="zip_code"><?php _e('ZIP/Postal Code', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="text" name="zip_code" id="zip_code" value="<?php echo esc_attr($zip_code); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="country"><?php _e('Country', 'equiprent-pro'); ?></label></th>
                <td>
                    <select name="country" id="country" class="regular-text">
                        <option value="US" <?php selected($country, 'US'); ?>><?php _e('United States', 'equiprent-pro'); ?></option>
                        <option value="CA" <?php selected($country, 'CA'); ?>><?php _e('Canada', 'equiprent-pro'); ?></option>
                        <option value="UK" <?php selected($country, 'UK'); ?>><?php _e('United Kingdom', 'equiprent-pro'); ?></option>
                        <option value="AU" <?php selected($country, 'AU'); ?>><?php _e('Australia', 'equiprent-pro'); ?></option>
                    </select>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Customer business information meta box
     */
    public static function customer_business_meta_box($post) {
        $customer_type = get_post_meta($post->ID, '_customer_type', true) ?: 'individual';
        $business_name = get_post_meta($post->ID, '_business_name', true);
        $tax_id = get_post_meta($post->ID, '_tax_id', true);
        $business_license = get_post_meta($post->ID, '_business_license', true);
        $contact_person = get_post_meta($post->ID, '_contact_person', true);
        $website = get_post_meta($post->ID, '_website', true);
        
        ?>
        <div class="business-fields" style="display: <?php echo $customer_type === 'business' ? 'block' : 'none'; ?>;">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="business_name"><?php _e('Business Name', 'equiprent-pro'); ?></label></th>
                    <td>
                        <input type="text" name="business_name" id="business_name" value="<?php echo esc_attr($business_name); ?>" class="regular-text" />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="tax_id"><?php _e('Tax ID/EIN', 'equiprent-pro'); ?></label></th>
                    <td>
                        <input type="text" name="tax_id" id="tax_id" value="<?php echo esc_attr($tax_id); ?>" class="regular-text" />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="business_license"><?php _e('Business License', 'equiprent-pro'); ?></label></th>
                    <td>
                        <input type="text" name="business_license" id="business_license" value="<?php echo esc_attr($business_license); ?>" class="regular-text" />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="contact_person"><?php _e('Primary Contact', 'equiprent-pro'); ?></label></th>
                    <td>
                        <input type="text" name="contact_person" id="contact_person" value="<?php echo esc_attr($contact_person); ?>" class="regular-text" />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="website"><?php _e('Website', 'equiprent-pro'); ?></label></th>
                    <td>
                        <input type="url" name="website" id="website" value="<?php echo esc_attr($website); ?>" class="regular-text" />
                    </td>
                </tr>
            </table>
        </div>
        <?php
    }
    
    /**
     * Customer billing & payment meta box
     */
    public static function customer_billing_meta_box($post) {
        $billing_same_as_shipping = get_post_meta($post->ID, '_billing_same_as_shipping', true);
        $billing_address = get_post_meta($post->ID, '_billing_address', true);
        $preferred_payment_method = get_post_meta($post->ID, '_preferred_payment_method', true);
        $credit_limit = get_post_meta($post->ID, '_credit_limit', true);
        $payment_terms = get_post_meta($post->ID, '_payment_terms', true);
        $tax_exempt = get_post_meta($post->ID, '_tax_exempt', true);
        $tax_exempt_number = get_post_meta($post->ID, '_tax_exempt_number', true);
        
        ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="billing_same_as_shipping"><?php _e('Billing Address', 'equiprent-pro'); ?></label>
                </th>
                <td>
                    <input type="checkbox" name="billing_same_as_shipping" id="billing_same_as_shipping" value="1" <?php checked($billing_same_as_shipping, '1'); ?> />
                    <label for="billing_same_as_shipping"><?php _e('Same as shipping address', 'equiprent-pro'); ?></label>
                </td>
            </tr>
            <tr class="billing-address-row" style="display: <?php echo $billing_same_as_shipping ? 'none' : 'table-row'; ?>;">
                <th scope="row"><label for="billing_address"><?php _e('Billing Address', 'equiprent-pro'); ?></label></th>
                <td>
                    <textarea name="billing_address" id="billing_address" rows="3" class="large-text"><?php echo esc_textarea($billing_address); ?></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="preferred_payment_method"><?php _e('Preferred Payment Method', 'equiprent-pro'); ?></label></th>
                <td>
                    <select name="preferred_payment_method" id="preferred_payment_method" class="regular-text">
                        <option value=""><?php _e('Select Method', 'equiprent-pro'); ?></option>
                        <option value="cash" <?php selected($preferred_payment_method, 'cash'); ?>><?php _e('Cash', 'equiprent-pro'); ?></option>
                        <option value="check" <?php selected($preferred_payment_method, 'check'); ?>><?php _e('Check', 'equiprent-pro'); ?></option>
                        <option value="credit_card" <?php selected($preferred_payment_method, 'credit_card'); ?>><?php _e('Credit Card', 'equiprent-pro'); ?></option>
                        <option value="bank_transfer" <?php selected($preferred_payment_method, 'bank_transfer'); ?>><?php _e('Bank Transfer', 'equiprent-pro'); ?></option>
                        <option value="online" <?php selected($preferred_payment_method, 'online'); ?>><?php _e('Online Payment', 'equiprent-pro'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="credit_limit"><?php _e('Credit Limit', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="number" name="credit_limit" id="credit_limit" value="<?php echo esc_attr($credit_limit); ?>" class="regular-text" step="0.01" />
                    <span><?php echo ER_Utilities::get_setting('currency_symbol', '$'); ?></span>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="payment_terms"><?php _e('Payment Terms', 'equiprent-pro'); ?></label></th>
                <td>
                    <select name="payment_terms" id="payment_terms" class="regular-text">
                        <option value="immediate" <?php selected($payment_terms, 'immediate'); ?>><?php _e('Payment on Pickup', 'equiprent-pro'); ?></option>
                        <option value="net_15" <?php selected($payment_terms, 'net_15'); ?>><?php _e('Net 15 Days', 'equiprent-pro'); ?></option>
                        <option value="net_30" <?php selected($payment_terms, 'net_30'); ?>><?php _e('Net 30 Days', 'equiprent-pro'); ?></option>
                        <option value="net_60" <?php selected($payment_terms, 'net_60'); ?>><?php _e('Net 60 Days', 'equiprent-pro'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="tax_exempt"><?php _e('Tax Exempt', 'equiprent-pro'); ?></label>
                </th>
                <td>
                    <input type="checkbox" name="tax_exempt" id="tax_exempt" value="1" <?php checked($tax_exempt, '1'); ?> />
                    <label for="tax_exempt"><?php _e('Customer is tax exempt', 'equiprent-pro'); ?></label>
                </td>
            </tr>
            <tr class="tax-exempt-number-row" style="display: <?php echo $tax_exempt ? 'table-row' : 'none'; ?>;">
                <th scope="row"><label for="tax_exempt_number"><?php _e('Tax Exempt Number', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="text" name="tax_exempt_number" id="tax_exempt_number" value="<?php echo esc_attr($tax_exempt_number); ?>" class="regular-text" />
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Customer booking history meta box
     */
    public static function customer_bookings_meta_box($post) {
        $bookings = get_posts(array(
            'post_type' => 'er_booking',
            'meta_query' => array(
                array(
                    'key' => '_customer_id',
                    'value' => $post->ID,
                    'compare' => '='
                )
            ),
            'posts_per_page' => 10,
            'orderby' => 'date',
            'order' => 'DESC'
        ));
        
        ?>
        <div class="er-customer-bookings">
            <?php if (!empty($bookings)) : ?>
                <ul>
                    <?php foreach ($bookings as $booking) : 
                        $start_date = get_post_meta($booking->ID, '_start_date', true);
                        $end_date = get_post_meta($booking->ID, '_end_date', true);
                        $total_amount = get_post_meta($booking->ID, '_total_amount', true);
                        $status = get_post_meta($booking->ID, '_booking_status', true);
                        ?>
                        <li class="booking-item">
                            <div class="booking-info">
                                <strong><a href="<?php echo get_edit_post_link($booking->ID); ?>"><?php echo esc_html($booking->post_title); ?></a></strong>
                                <div class="booking-dates">
                                    <?php 
                                    if ($start_date && $end_date) {
                                        echo ER_Utilities::format_date($start_date) . ' - ' . ER_Utilities::format_date($end_date);
                                    }
                                    ?>
                                </div>
                                <?php if ($total_amount) : ?>
                                    <div class="booking-total"><?php echo ER_Utilities::format_currency($total_amount); ?></div>
                                <?php endif; ?>
                                <span class="booking-status status-<?php echo esc_attr($status); ?>"><?php echo esc_html(ucfirst($status)); ?></span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p><?php _e('No bookings yet', 'equiprent-pro'); ?></p>
            <?php endif; ?>
            
            <p><a href="<?php echo admin_url('post-new.php?post_type=er_booking&customer_id=' . $post->ID); ?>" class="button button-primary"><?php _e('Create Booking', 'equiprent-pro'); ?></a></p>
        </div>
        <?php
    }
    
    /**
     * Customer notes meta box
     */
    public static function customer_notes_meta_box($post) {
        $customer_notes = get_post_meta($post->ID, '_customer_notes', true);
        $customer_rating = get_post_meta($post->ID, '_customer_rating', true);
        
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="customer_rating"><?php _e('Customer Rating', 'equiprent-pro'); ?></label></th>
                <td>
                    <select name="customer_rating" id="customer_rating" class="widefat">
                        <option value=""><?php _e('No Rating', 'equiprent-pro'); ?></option>
                        <option value="5" <?php selected($customer_rating, '5'); ?>><?php _e('5 Stars - Excellent', 'equiprent-pro'); ?></option>
                        <option value="4" <?php selected($customer_rating, '4'); ?>><?php _e('4 Stars - Good', 'equiprent-pro'); ?></option>
                        <option value="3" <?php selected($customer_rating, '3'); ?>><?php _e('3 Stars - Average', 'equiprent-pro'); ?></option>
                        <option value="2" <?php selected($customer_rating, '2'); ?>><?php _e('2 Stars - Poor', 'equiprent-pro'); ?></option>
                        <option value="1" <?php selected($customer_rating, '1'); ?>><?php _e('1 Star - Very Poor', 'equiprent-pro'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="customer_notes"><?php _e('Internal Notes', 'equiprent-pro'); ?></label></th>
                <td>
                    <textarea name="customer_notes" id="customer_notes" rows="6" class="widefat"><?php echo esc_textarea($customer_notes); ?></textarea>
                    <p class="description"><?php _e('Internal notes about this customer', 'equiprent-pro'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Maintenance details meta box
     */
    public static function maintenance_details_meta_box($post) {
        wp_nonce_field('er_maintenance_details', 'er_maintenance_details_nonce');
        
        $equipment_id = get_post_meta($post->ID, '_equipment_id', true);
        $maintenance_type = get_post_meta($post->ID, '_maintenance_type', true);
        $priority = get_post_meta($post->ID, '_priority', true) ?: 'medium';
        $status = get_post_meta($post->ID, '_maintenance_status', true) ?: 'scheduled';
        $description = get_post_meta($post->ID, '_maintenance_description', true);
        $estimated_duration = get_post_meta($post->ID, '_estimated_duration', true);
        $estimated_cost = get_post_meta($post->ID, '_estimated_cost', true);
        
        // Get equipment from URL if creating new maintenance
        if (!$equipment_id && isset($_GET['equipment_id'])) {
            $equipment_id = intval($_GET['equipment_id']);
        }
        
        $equipment_items = get_posts(array(
            'post_type' => 'equipment',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));
        
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="equipment_id"><?php _e('Equipment', 'equiprent-pro'); ?> *</label></th>
                <td>
                    <select name="equipment_id" id="equipment_id" class="regular-text" required>
                        <option value=""><?php _e('Select Equipment', 'equiprent-pro'); ?></option>
                        <?php foreach ($equipment_items as $equipment) : ?>
                            <option value="<?php echo $equipment->ID; ?>" <?php selected($equipment_id, $equipment->ID); ?>>
                                <?php echo esc_html($equipment->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="maintenance_type"><?php _e('Maintenance Type', 'equiprent-pro'); ?></label></th>
                <td>
                    <select name="maintenance_type" id="maintenance_type" class="regular-text">
                        <option value="preventive" <?php selected($maintenance_type, 'preventive'); ?>><?php _e('Preventive', 'equiprent-pro'); ?></option>
                        <option value="repair" <?php selected($maintenance_type, 'repair'); ?>><?php _e('Repair', 'equiprent-pro'); ?></option>
                        <option value="inspection" <?php selected($maintenance_type, 'inspection'); ?>><?php _e('Inspection', 'equiprent-pro'); ?></option>
                        <option value="cleaning" <?php selected($maintenance_type, 'cleaning'); ?>><?php _e('Cleaning', 'equiprent-pro'); ?></option>
                        <option value="calibration" <?php selected($maintenance_type, 'calibration'); ?>><?php _e('Calibration', 'equiprent-pro'); ?></option>
                        <option value="upgrade" <?php selected($maintenance_type, 'upgrade'); ?>><?php _e('Upgrade', 'equiprent-pro'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="priority"><?php _e('Priority', 'equiprent-pro'); ?></label></th>
                <td>
                    <select name="priority" id="priority" class="regular-text">
                        <option value="low" <?php selected($priority, 'low'); ?>><?php _e('Low', 'equiprent-pro'); ?></option>
                        <option value="medium" <?php selected($priority, 'medium'); ?>><?php _e('Medium', 'equiprent-pro'); ?></option>
                        <option value="high" <?php selected($priority, 'high'); ?>><?php _e('High', 'equiprent-pro'); ?></option>
                        <option value="urgent" <?php selected($priority, 'urgent'); ?>><?php _e('Urgent', 'equiprent-pro'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="maintenance_status"><?php _e('Status', 'equiprent-pro'); ?></label></th>
                <td>
                    <select name="maintenance_status" id="maintenance_status" class="regular-text">
                        <option value="scheduled" <?php selected($status, 'scheduled'); ?>><?php _e('Scheduled', 'equiprent-pro'); ?></option>
                        <option value="in_progress" <?php selected($status, 'in_progress'); ?>><?php _e('In Progress', 'equiprent-pro'); ?></option>
                        <option value="completed" <?php selected($status, 'completed'); ?>><?php _e('Completed', 'equiprent-pro'); ?></option>
                        <option value="cancelled" <?php selected($status, 'cancelled'); ?>><?php _e('Cancelled', 'equiprent-pro'); ?></option>
                        <option value="on_hold" <?php selected($status, 'on_hold'); ?>><?php _e('On Hold', 'equiprent-pro'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="maintenance_description"><?php _e('Description', 'equiprent-pro'); ?></label></th>
                <td>
                    <textarea name="maintenance_description" id="maintenance_description" rows="4" class="large-text"><?php echo esc_textarea($description); ?></textarea>
                    <p class="description"><?php _e('Detailed description of maintenance work to be performed', 'equiprent-pro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="estimated_duration"><?php _e('Estimated Duration', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="number" name="estimated_duration" id="estimated_duration" value="<?php echo esc_attr($estimated_duration); ?>" class="small-text" step="0.5" />
                    <span><?php _e('hours', 'equiprent-pro'); ?></span>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="estimated_cost"><?php _e('Estimated Cost', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="number" name="estimated_cost" id="estimated_cost" value="<?php echo esc_attr($estimated_cost); ?>" class="regular-text" step="0.01" />
                    <span><?php echo ER_Utilities::get_setting('currency_symbol', '$'); ?></span>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Maintenance assignment & scheduling meta box
     */
    public static function maintenance_assignment_meta_box($post) {
        $assigned_to = get_post_meta($post->ID, '_assigned_to', true);
        $scheduled_date = get_post_meta($post->ID, '_scheduled_date', true);
        $start_date = get_post_meta($post->ID, '_start_date', true);
        $completion_date = get_post_meta($post->ID, '_completion_date', true);
        $technician_notes = get_post_meta($post->ID, '_technician_notes', true);
        
        // Get users who can be assigned maintenance
        $assignable_users = get_users(array('capability' => 'edit_equipment'));
        
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="assigned_to"><?php _e('Assigned To', 'equiprent-pro'); ?></label></th>
                <td>
                    <select name="assigned_to" id="assigned_to" class="widefat">
                        <option value=""><?php _e('Unassigned', 'equiprent-pro'); ?></option>
                        <?php foreach ($assignable_users as $user) : ?>
                            <option value="<?php echo $user->ID; ?>" <?php selected($assigned_to, $user->ID); ?>>
                                <?php echo esc_html($user->display_name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="scheduled_date"><?php _e('Scheduled Date', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="datetime-local" name="scheduled_date" id="scheduled_date" value="<?php echo esc_attr($scheduled_date); ?>" class="widefat" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="start_date"><?php _e('Started', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="datetime-local" name="start_date" id="start_date" value="<?php echo esc_attr($start_date); ?>" class="widefat" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="completion_date"><?php _e('Completed', 'equiprent-pro'); ?></label></th>
                <td>
                    <input type="datetime-local" name="completion_date" id="completion_date" value="<?php echo esc_attr($completion_date); ?>" class="widefat" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="technician_notes"><?php _e('Technician Notes', 'equiprent-pro'); ?></label></th>
                <td>
                    <textarea name="technician_notes" id="technician_notes" rows="4" class="widefat"><?php echo esc_textarea($technician_notes); ?></textarea>
                    <p class="description"><?php _e('Notes from the technician performing the work', 'equiprent-pro'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Maintenance parts & costs meta box
     */
    public static function maintenance_parts_meta_box($post) {
        $parts_used = get_post_meta($post->ID, '_parts_used', true);
        if (!is_array($parts_used)) {
            $parts_used = array();
        }
        
        $labor_hours = get_post_meta($post->ID, '_labor_hours', true);
        $labor_rate = get_post_meta($post->ID, '_labor_rate', true);
        $total_parts_cost = get_post_meta($post->ID, '_total_parts_cost', true);
        $total_labor_cost = get_post_meta($post->ID, '_total_labor_cost', true);
        $total_cost = get_post_meta($post->ID, '_total_maintenance_cost', true);
        
        ?>
        <div id="er-maintenance-parts">
            <h4><?php _e('Parts Used', 'equiprent-pro'); ?></h4>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Part Number/Description', 'equiprent-pro'); ?></th>
                        <th><?php _e('Quantity', 'equiprent-pro'); ?></th>
                        <th><?php _e('Unit Cost', 'equiprent-pro'); ?></th>
                        <th><?php _e('Total', 'equiprent-pro'); ?></th>
                        <th><?php _e('Actions', 'equiprent-pro'); ?></th>
                    </tr>
                </thead>
                <tbody id="parts-list">
                    <?php if (!empty($parts_used)) : ?>
                        <?php foreach ($parts_used as $index => $part) : ?>
                            <tr class="part-row" data-index="<?php echo $index; ?>">
                                <td>
                                    <input type="text" name="parts_used[<?php echo $index; ?>][description]" value="<?php echo esc_attr($part['description']); ?>" class="regular-text" placeholder="<?php _e('Part description', 'equiprent-pro'); ?>" />
                                </td>
                                <td>
                                    <input type="number" name="parts_used[<?php echo $index; ?>][quantity]" value="<?php echo esc_attr($part['quantity']); ?>" class="small-text part-quantity" min="1" />
                                </td>
                                <td>
                                    <input type="number" name="parts_used[<?php echo $index; ?>][unit_cost]" value="<?php echo esc_attr($part['unit_cost']); ?>" class="regular-text part-unit-cost" step="0.01" />
                                </td>
                                <td class="part-total">
                                    <?php echo ER_Utilities::format_currency($part['quantity'] * $part['unit_cost']); ?>
                                </td>
                                <td>
                                    <button type="button" class="button remove-part"><?php _e('Remove', 'equiprent-pro'); ?></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <p>
                <button type="button" id="add-part" class="button button-secondary"><?php _e('Add Part', 'equiprent-pro'); ?></button>
            </p>
            
            <h4><?php _e('Labor & Costs', 'equiprent-pro'); ?></h4>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="labor_hours"><?php _e('Labor Hours', 'equiprent-pro'); ?></label></th>
                    <td>
                        <input type="number" name="labor_hours" id="labor_hours" value="<?php echo esc_attr($labor_hours); ?>" class="regular-text" step="0.25" />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="labor_rate"><?php _e('Labor Rate (per hour)', 'equiprent-pro'); ?></label></th>
                    <td>
                        <input type="number" name="labor_rate" id="labor_rate" value="<?php echo esc_attr($labor_rate); ?>" class="regular-text" step="0.01" />
                        <span><?php echo ER_Utilities::get_setting('currency_symbol', '$'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="total_parts_cost"><?php _e('Total Parts Cost', 'equiprent-pro'); ?></label></th>
                    <td>
                        <input type="number" name="total_parts_cost" id="total_parts_cost" value="<?php echo esc_attr($total_parts_cost); ?>" class="regular-text" step="0.01" readonly />
                        <span><?php echo ER_Utilities::get_setting('currency_symbol', '$'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="total_labor_cost"><?php _e('Total Labor Cost', 'equiprent-pro'); ?></label></th>
                    <td>
                        <input type="number" name="total_labor_cost" id="total_labor_cost" value="<?php echo esc_attr($total_labor_cost); ?>" class="regular-text" step="0.01" readonly />
                        <span><?php echo ER_Utilities::get_setting('currency_symbol', '$'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="total_maintenance_cost"><?php _e('Total Maintenance Cost', 'equiprent-pro'); ?></label></th>
                    <td>
                        <input type="number" name="total_maintenance_cost" id="total_maintenance_cost" value="<?php echo esc_attr($total_cost); ?>" class="regular-text" step="0.01" readonly />
                        <span><?php echo ER_Utilities::get_setting('currency_symbol', '$'); ?></span>
                    </td>
                </tr>
            </table>
        </div>
        
        <script type="text/html" id="part-row-template">
            <tr class="part-row" data-index="{{INDEX}}">
                <td>
                    <input type="text" name="parts_used[{{INDEX}}][description]" value="" class="regular-text" placeholder="<?php _e('Part description', 'equiprent-pro'); ?>" />
                </td>
                <td>
                    <input type="number" name="parts_used[{{INDEX}}][quantity]" value="1" class="small-text part-quantity" min="1" />
                </td>
                <td>
                    <input type="number" name="parts_used[{{INDEX}}][unit_cost]" value="" class="regular-text part-unit-cost" step="0.01" />
                </td>
                <td class="part-total">
                    <?php echo ER_Utilities::get_setting('currency_symbol', '$'); ?>0.00
                </td>
                <td>
                    <button type="button" class="button remove-part"><?php _e('Remove', 'equiprent-pro'); ?></button>
                </td>
            </tr>
        </script>
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
        
        // Save equipment meta
        if ($post_type === 'equipment' && isset($_POST['er_equipment_details_nonce'])) {
            if (wp_verify_nonce($_POST['er_equipment_details_nonce'], 'er_equipment_details')) {
                self::save_equipment_meta($post_id);
            }
        }
        
        // Save booking meta
        if ($post_type === 'er_booking' && isset($_POST['er_booking_details_nonce'])) {
            if (wp_verify_nonce($_POST['er_booking_details_nonce'], 'er_booking_details')) {
                self::save_booking_meta($post_id);
            }
        }
        
        // Save customer meta
        if ($post_type === 'er_customer' && isset($_POST['er_customer_contact_nonce'])) {
            if (wp_verify_nonce($_POST['er_customer_contact_nonce'], 'er_customer_contact')) {
                self::save_customer_meta($post_id);
            }
        }
        
        // Save maintenance meta
        if ($post_type === 'er_maintenance' && isset($_POST['er_maintenance_details_nonce'])) {
            if (wp_verify_nonce($_POST['er_maintenance_details_nonce'], 'er_maintenance_details')) {
                self::save_maintenance_meta($post_id);
            }
        }
    }
    
    /**
     * Save equipment meta fields
     */
    private static function save_equipment_meta($post_id) {
        $equipment_fields = array(
            'equipment_sku' => 'sanitize_text_field',
            'equipment_model' => 'sanitize_text_field',
            'equipment_serial_number' => 'sanitize_text_field',
            'equipment_year' => 'intval',
            'equipment_condition' => 'sanitize_text_field',
            'equipment_status' => 'sanitize_text_field',
            'equipment_acquisition_date' => 'sanitize_text_field',
            'equipment_acquisition_cost' => 'floatval',
            'equipment_location' => 'sanitize_text_field',
            'equipment_notes' => 'sanitize_textarea_field',
            'equipment_daily_rate' => 'floatval',
            'equipment_weekly_rate' => 'floatval',
            'equipment_monthly_rate' => 'floatval',
            'equipment_security_deposit' => 'floatval',
            'equipment_replacement_value' => 'floatval',
            'equipment_min_rental_period' => 'intval',
            'equipment_max_rental_period' => 'intval',
            'equipment_advance_booking_days' => 'intval',
            'equipment_total_quantity' => 'intval',
            'equipment_available_quantity' => 'intval',
            'equipment_weight' => 'floatval',
            'equipment_dimensions_length' => 'floatval',
            'equipment_dimensions_width' => 'floatval',
            'equipment_dimensions_height' => 'floatval',
            'equipment_power_requirements' => 'sanitize_text_field',
            'equipment_fuel_type' => 'sanitize_text_field',
            'equipment_accessories_included' => 'sanitize_textarea_field',
            'equipment_setup_instructions' => 'sanitize_textarea_field',
            'equipment_safety_requirements' => 'sanitize_textarea_field',
            'equipment_last_maintenance' => 'sanitize_text_field',
            'equipment_next_maintenance' => 'sanitize_text_field',
            'equipment_maintenance_hours' => 'intval',
            'equipment_total_rental_hours' => 'floatval',
            'equipment_maintenance_notes' => 'sanitize_textarea_field'
        );
        
        foreach ($equipment_fields as $field => $sanitizer) {
            if (isset($_POST[$field])) {
                $value = call_user_func($sanitizer, $_POST[$field]);
                update_post_meta($post_id, '_' . $field, $value);
            }
        }
    }
    
    /**
     * Save booking meta fields
     */
    private static function save_booking_meta($post_id) {
        $booking_fields = array(
            'booking_number' => 'sanitize_text_field',
            'customer_id' => 'intval',
            'start_date' => 'sanitize_text_field',
            'end_date' => 'sanitize_text_field',
            'booking_status' => 'sanitize_text_field',
            'total_amount' => 'floatval',
            'discount_amount' => 'floatval',
            'tax_amount' => 'floatval',
            'special_instructions' => 'sanitize_textarea_field',
            'payment_status' => 'sanitize_text_field',
            'payment_method' => 'sanitize_text_field',
            'deposit_amount' => 'floatval',
            'deposit_paid' => 'floatval',
            'balance_due' => 'floatval',
            'payment_terms' => 'sanitize_text_field',
            'payment_notes' => 'sanitize_textarea_field',
            'delivery_method' => 'sanitize_text_field',
            'delivery_address' => 'sanitize_textarea_field',
            'delivery_date' => 'sanitize_text_field',
            'delivery_time' => 'sanitize_text_field',
            'pickup_date' => 'sanitize_text_field',
            'pickup_time' => 'sanitize_text_field',
            'delivery_fee' => 'floatval',
            'delivery_notes' => 'sanitize_textarea_field',
            'internal_notes' => 'sanitize_textarea_field'
        );
        
        foreach ($booking_fields as $field => $sanitizer) {
            if (isset($_POST[$field])) {
                $value = call_user_func($sanitizer, $_POST[$field]);
                update_post_meta($post_id, '_' . $field, $value);
            }
        }
        
        // Save equipment items
        if (isset($_POST['equipment_items']) && is_array($_POST['equipment_items'])) {
            $equipment_items = array();
            foreach ($_POST['equipment_items'] as $item) {
                if (!empty($item['equipment_id'])) {
                    $equipment_items[] = array(
                        'equipment_id' => intval($item['equipment_id']),
                        'quantity' => intval($item['quantity']),
                        'daily_rate' => floatval($item['daily_rate'])
                    );
                }
            }
            update_post_meta($post_id, '_equipment_items', $equipment_items);
        }
    }
    
    /**
     * Save customer meta fields
     */
    private static function save_customer_meta($post_id) {
        $customer_fields = array(
            'customer_type' => 'sanitize_text_field',
            'first_name' => 'sanitize_text_field',
            'last_name' => 'sanitize_text_field',
            'email' => 'sanitize_email',
            'phone' => 'sanitize_text_field',
            'mobile_phone' => 'sanitize_text_field',
            'address' => 'sanitize_textarea_field',
            'city' => 'sanitize_text_field',
            'state' => 'sanitize_text_field',
            'zip_code' => 'sanitize_text_field',
            'country' => 'sanitize_text_field',
            'business_name' => 'sanitize_text_field',
            'tax_id' => 'sanitize_text_field',
            'business_license' => 'sanitize_text_field',
            'contact_person' => 'sanitize_text_field',
            'website' => 'esc_url_raw',
            'billing_same_as_shipping' => 'sanitize_text_field',
            'billing_address' => 'sanitize_textarea_field',
            'preferred_payment_method' => 'sanitize_text_field',
            'credit_limit' => 'floatval',
            'payment_terms' => 'sanitize_text_field',
            'tax_exempt' => 'sanitize_text_field',
            'tax_exempt_number' => 'sanitize_text_field',
            'customer_notes' => 'sanitize_textarea_field',
            'customer_rating' => 'intval'
        );
        
        foreach ($customer_fields as $field => $sanitizer) {
            if (isset($_POST[$field])) {
                $value = call_user_func($sanitizer, $_POST[$field]);
                update_post_meta($post_id, '_' . $field, $value);
            }
        }
        
        // Update post title based on customer type
        $customer_type = get_post_meta($post_id, '_customer_type', true);
        if ($customer_type === 'business') {
            $business_name = get_post_meta($post_id, '_business_name', true);
            if ($business_name) {
                wp_update_post(array(
                    'ID' => $post_id,
                    'post_title' => $business_name
                ));
            }
        } else {
            $first_name = get_post_meta($post_id, '_first_name', true);
            $last_name = get_post_meta($post_id, '_last_name', true);
            if ($first_name || $last_name) {
                wp_update_post(array(
                    'ID' => $post_id,
                    'post_title' => trim($first_name . ' ' . $last_name)
                ));
            }
        }
    }
    
    /**
     * Save maintenance meta fields
     */
    private static function save_maintenance_meta($post_id) {
        $maintenance_fields = array(
            'equipment_id' => 'intval',
            'maintenance_type' => 'sanitize_text_field',
            'priority' => 'sanitize_text_field',
            'maintenance_status' => 'sanitize_text_field',
            'maintenance_description' => 'sanitize_textarea_field',
            'estimated_duration' => 'floatval',
            'estimated_cost' => 'floatval',
            'assigned_to' => 'intval',
            'scheduled_date' => 'sanitize_text_field',
            'start_date' => 'sanitize_text_field',
            'completion_date' => 'sanitize_text_field',
            'technician_notes' => 'sanitize_textarea_field',
            'labor_hours' => 'floatval',
            'labor_rate' => 'floatval',
            'total_parts_cost' => 'floatval',
            'total_labor_cost' => 'floatval',
            'total_maintenance_cost' => 'floatval'
        );
        
        foreach ($maintenance_fields as $field => $sanitizer) {
            if (isset($_POST[$field])) {
                $value = call_user_func($sanitizer, $_POST[$field]);
                update_post_meta($post_id, '_' . $field, $value);
            }
        }
        
        // Save parts used
        if (isset($_POST['parts_used']) && is_array($_POST['parts_used'])) {
            $parts_used = array();
            foreach ($_POST['parts_used'] as $part) {
                if (!empty($part['description'])) {
                    $parts_used[] = array(
                        'description' => sanitize_text_field($part['description']),
                        'quantity' => intval($part['quantity']),
                        'unit_cost' => floatval($part['unit_cost'])
                    );
                }
            }
            update_post_meta($post_id, '_parts_used', $parts_used);
        }
    }
}