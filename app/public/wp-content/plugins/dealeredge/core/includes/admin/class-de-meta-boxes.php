<?php
/**
 * DealerEdge Meta Boxes
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class DE_Meta_Boxes {
    
    public static function init() {
        add_action('add_meta_boxes', array(__CLASS__, 'add_meta_boxes'));
        add_action('save_post', array(__CLASS__, 'save_meta_boxes'));
    }
    
    public static function add_meta_boxes() {
        // Vehicle meta boxes
        add_meta_box(
            'de_vehicle_details',
            __('Vehicle Details', 'dealeredge'),
            array(__CLASS__, 'vehicle_details_meta_box'),
            'de_vehicle',
            'normal',
            'high'
        );
        
        // Customer meta boxes
        add_meta_box(
            'de_customer_details',
            __('Customer Information', 'dealeredge'),
            array(__CLASS__, 'customer_details_meta_box'),
            'de_customer',
            'normal',
            'high'
        );
        
        // Work Order meta boxes
        add_meta_box(
            'de_work_order_details',
            __('Work Order Details', 'dealeredge'),
            array(__CLASS__, 'work_order_details_meta_box'),
            'de_work_order',
            'normal',
            'high'
        );
        
        // Sale meta boxes
        add_meta_box(
            'de_sale_details',
            __('Sale Details', 'dealeredge'),
            array(__CLASS__, 'sale_details_meta_box'),
            'de_sale',
            'normal',
            'high'
        );
        
        // Part meta boxes
        add_meta_box(
            'de_part_details',
            __('Part Details', 'dealeredge'),
            array(__CLASS__, 'part_details_meta_box'),
            'de_part',
            'normal',
            'high'
        );
    }
    
    public static function vehicle_details_meta_box($post) {
        wp_nonce_field('de_vehicle_meta_box', 'de_vehicle_meta_box_nonce');
        
        $year = get_post_meta($post->ID, '_de_vehicle_year', true);
        $vin = get_post_meta($post->ID, '_de_vehicle_vin', true);
        $mileage = get_post_meta($post->ID, '_de_vehicle_mileage', true);
        $engine = get_post_meta($post->ID, '_de_vehicle_engine', true);
        $transmission = get_post_meta($post->ID, '_de_vehicle_transmission', true);
        $color = get_post_meta($post->ID, '_de_vehicle_color', true);
        $condition = get_post_meta($post->ID, '_de_vehicle_condition', true);
        $status = get_post_meta($post->ID, '_de_vehicle_status', true);
        $purchase_price = get_post_meta($post->ID, '_de_vehicle_purchase_price', true);
        $sale_price = get_post_meta($post->ID, '_de_vehicle_sale_price', true);
        $notes = get_post_meta($post->ID, '_de_vehicle_notes', true);
        ?>
        
        <table class="form-table">
            <tr>
                <th><label for="de_vehicle_year"><?php _e('Year', 'dealeredge'); ?></label></th>
                <td><input type="number" id="de_vehicle_year" name="de_vehicle_year" value="<?php echo esc_attr($year); ?>" min="1900" max="<?php echo date('Y') + 1; ?>" /></td>
            </tr>
            <tr>
                <th><label for="de_vehicle_vin"><?php _e('VIN', 'dealeredge'); ?></label></th>
                <td>
                    <input type="text" id="de_vehicle_vin" name="de_vehicle_vin" value="<?php echo esc_attr($vin); ?>" maxlength="17" style="width: 300px;" />
                    <p class="description"><?php _e('Vehicle Identification Number (17 characters)', 'dealeredge'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="de_vehicle_mileage"><?php _e('Mileage', 'dealeredge'); ?></label></th>
                <td><input type="number" id="de_vehicle_mileage" name="de_vehicle_mileage" value="<?php echo esc_attr($mileage); ?>" min="0" /></td>
            </tr>
            <tr>
                <th><label for="de_vehicle_engine"><?php _e('Engine', 'dealeredge'); ?></label></th>
                <td><input type="text" id="de_vehicle_engine" name="de_vehicle_engine" value="<?php echo esc_attr($engine); ?>" style="width: 300px;" /></td>
            </tr>
            <tr>
                <th><label for="de_vehicle_transmission"><?php _e('Transmission', 'dealeredge'); ?></label></th>
                <td>
                    <select id="de_vehicle_transmission" name="de_vehicle_transmission">
                        <option value=""><?php _e('Select...', 'dealeredge'); ?></option>
                        <option value="manual" <?php selected($transmission, 'manual'); ?>><?php _e('Manual', 'dealeredge'); ?></option>
                        <option value="automatic" <?php selected($transmission, 'automatic'); ?>><?php _e('Automatic', 'dealeredge'); ?></option>
                        <option value="cvt" <?php selected($transmission, 'cvt'); ?>><?php _e('CVT', 'dealeredge'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="de_vehicle_color"><?php _e('Color', 'dealeredge'); ?></label></th>
                <td><input type="text" id="de_vehicle_color" name="de_vehicle_color" value="<?php echo esc_attr($color); ?>" /></td>
            </tr>
            <tr>
                <th><label for="de_vehicle_condition"><?php _e('Condition', 'dealeredge'); ?></label></th>
                <td>
                    <select id="de_vehicle_condition" name="de_vehicle_condition">
                        <option value=""><?php _e('Select...', 'dealeredge'); ?></option>
                        <?php foreach (DE_Utilities::get_vehicle_conditions() as $key => $label) : ?>
                            <option value="<?php echo esc_attr($key); ?>" <?php selected($condition, $key); ?>><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="de_vehicle_status"><?php _e('Status', 'dealeredge'); ?></label></th>
                <td>
                    <select id="de_vehicle_status" name="de_vehicle_status">
                        <option value="available" <?php selected($status, 'available'); ?>><?php _e('Available', 'dealeredge'); ?></option>
                        <option value="sold" <?php selected($status, 'sold'); ?>><?php _e('Sold', 'dealeredge'); ?></option>
                        <option value="in_service" <?php selected($status, 'in_service'); ?>><?php _e('In Service', 'dealeredge'); ?></option>
                        <option value="pending" <?php selected($status, 'pending'); ?>><?php _e('Pending', 'dealeredge'); ?></option>
                        <option value="wholesale" <?php selected($status, 'wholesale'); ?>><?php _e('Wholesale', 'dealeredge'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="de_vehicle_purchase_price"><?php _e('Purchase Price', 'dealeredge'); ?></label></th>
                <td><input type="number" id="de_vehicle_purchase_price" name="de_vehicle_purchase_price" value="<?php echo esc_attr($purchase_price); ?>" step="0.01" min="0" /></td>
            </tr>
            <tr>
                <th><label for="de_vehicle_sale_price"><?php _e('Sale Price', 'dealeredge'); ?></label></th>
                <td><input type="number" id="de_vehicle_sale_price" name="de_vehicle_sale_price" value="<?php echo esc_attr($sale_price); ?>" step="0.01" min="0" /></td>
            </tr>
            <tr>
                <th><label for="de_vehicle_notes"><?php _e('Notes', 'dealeredge'); ?></label></th>
                <td><textarea id="de_vehicle_notes" name="de_vehicle_notes" rows="4" style="width: 100%;"><?php echo esc_textarea($notes); ?></textarea></td>
            </tr>
        </table>
        
        <?php
    }
    
    public static function customer_details_meta_box($post) {
        wp_nonce_field('de_customer_meta_box', 'de_customer_meta_box_nonce');
        
        $first_name = get_post_meta($post->ID, '_de_customer_first_name', true);
        $last_name = get_post_meta($post->ID, '_de_customer_last_name', true);
        $email = get_post_meta($post->ID, '_de_customer_email', true);
        $phone = get_post_meta($post->ID, '_de_customer_phone', true);
        $address = get_post_meta($post->ID, '_de_customer_address', true);
        $city = get_post_meta($post->ID, '_de_customer_city', true);
        $state = get_post_meta($post->ID, '_de_customer_state', true);
        $zip = get_post_meta($post->ID, '_de_customer_zip', true);
        $license_number = get_post_meta($post->ID, '_de_customer_license_number', true);
        $notes = get_post_meta($post->ID, '_de_customer_notes', true);
        ?>
        
        <table class="form-table">
            <tr>
                <th><label for="de_customer_first_name"><?php _e('First Name', 'dealeredge'); ?></label></th>
                <td><input type="text" id="de_customer_first_name" name="de_customer_first_name" value="<?php echo esc_attr($first_name); ?>" required /></td>
            </tr>
            <tr>
                <th><label for="de_customer_last_name"><?php _e('Last Name', 'dealeredge'); ?></label></th>
                <td><input type="text" id="de_customer_last_name" name="de_customer_last_name" value="<?php echo esc_attr($last_name); ?>" required /></td>
            </tr>
            <tr>
                <th><label for="de_customer_email"><?php _e('Email', 'dealeredge'); ?></label></th>
                <td><input type="email" id="de_customer_email" name="de_customer_email" value="<?php echo esc_attr($email); ?>" /></td>
            </tr>
            <tr>
                <th><label for="de_customer_phone"><?php _e('Phone', 'dealeredge'); ?></label></th>
                <td><input type="tel" id="de_customer_phone" name="de_customer_phone" value="<?php echo esc_attr($phone); ?>" /></td>
            </tr>
            <tr>
                <th><label for="de_customer_address"><?php _e('Address', 'dealeredge'); ?></label></th>
                <td><input type="text" id="de_customer_address" name="de_customer_address" value="<?php echo esc_attr($address); ?>" style="width: 100%;" /></td>
            </tr>
            <tr>
                <th><label for="de_customer_city"><?php _e('City', 'dealeredge'); ?></label></th>
                <td><input type="text" id="de_customer_city" name="de_customer_city" value="<?php echo esc_attr($city); ?>" /></td>
            </tr>
            <tr>
                <th><label for="de_customer_state"><?php _e('State', 'dealeredge'); ?></label></th>
                <td><input type="text" id="de_customer_state" name="de_customer_state" value="<?php echo esc_attr($state); ?>" maxlength="2" /></td>
            </tr>
            <tr>
                <th><label for="de_customer_zip"><?php _e('ZIP Code', 'dealeredge'); ?></label></th>
                <td><input type="text" id="de_customer_zip" name="de_customer_zip" value="<?php echo esc_attr($zip); ?>" /></td>
            </tr>
            <tr>
                <th><label for="de_customer_license_number"><?php _e('Driver License #', 'dealeredge'); ?></label></th>
                <td><input type="text" id="de_customer_license_number" name="de_customer_license_number" value="<?php echo esc_attr($license_number); ?>" /></td>
            </tr>
            <tr>
                <th><label for="de_customer_notes"><?php _e('Notes', 'dealeredge'); ?></label></th>
                <td><textarea id="de_customer_notes" name="de_customer_notes" rows="4" style="width: 100%;"><?php echo esc_textarea($notes); ?></textarea></td>
            </tr>
        </table>
        
        <?php
    }
    
    public static function work_order_details_meta_box($post) {
        wp_nonce_field('de_work_order_meta_box', 'de_work_order_meta_box_nonce');
        
        $wo_number = get_post_meta($post->ID, '_de_work_order_number', true);
        $customer_id = get_post_meta($post->ID, '_de_customer_id', true);
        $vehicle_id = get_post_meta($post->ID, '_de_vehicle_id', true);
        $status = get_post_meta($post->ID, '_de_work_order_status', true);
        $priority = get_post_meta($post->ID, '_de_work_order_priority', true);
        $labor_hours = get_post_meta($post->ID, '_de_labor_hours', true);
        $labor_rate = get_post_meta($post->ID, '_de_labor_rate', true) ?: get_option('dealeredge_labor_rate', '120.00');
        $parts_cost = get_post_meta($post->ID, '_de_parts_cost', true);
        $tax_amount = get_post_meta($post->ID, '_de_tax_amount', true);
        $total_amount = get_post_meta($post->ID, '_de_total_amount', true);
        $date_promised = get_post_meta($post->ID, '_de_date_promised', true);
        
        if (!$wo_number && $post->post_status !== 'auto-draft') {
            $wo_number = DE_Utilities::get_next_work_order_number();
        }
        ?>
        
        <table class="form-table">
            <tr>
                <th><label for="de_work_order_number"><?php _e('Work Order #', 'dealeredge'); ?></label></th>
                <td><input type="text" id="de_work_order_number" name="de_work_order_number" value="<?php echo esc_attr($wo_number); ?>" readonly /></td>
            </tr>
            <tr>
                <th><label for="de_customer_id"><?php _e('Customer', 'dealeredge'); ?></label></th>
                <td>
                    <select id="de_customer_id" name="de_customer_id" required>
                        <option value=""><?php _e('Select Customer...', 'dealeredge'); ?></option>
                        <?php
                        $customers = get_posts(array('post_type' => 'de_customer', 'posts_per_page' => -1));
                        foreach ($customers as $customer) {
                            $selected = selected($customer_id, $customer->ID, false);
                            echo '<option value="' . $customer->ID . '" ' . $selected . '>' . DE_Utilities::get_customer_display_name($customer->ID) . '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="de_vehicle_id"><?php _e('Vehicle', 'dealeredge'); ?></label></th>
                <td>
                    <select id="de_vehicle_id" name="de_vehicle_id" required>
                        <option value=""><?php _e('Select Vehicle...', 'dealeredge'); ?></option>
                        <?php
                        $vehicles = get_posts(array('post_type' => 'de_vehicle', 'posts_per_page' => -1));
                        foreach ($vehicles as $vehicle) {
                            $selected = selected($vehicle_id, $vehicle->ID, false);
                            echo '<option value="' . $vehicle->ID . '" ' . $selected . '>' . DE_Utilities::get_vehicle_display_name($vehicle->ID) . '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="de_work_order_status"><?php _e('Status', 'dealeredge'); ?></label></th>
                <td>
                    <select id="de_work_order_status" name="de_work_order_status">
                        <?php foreach (DE_Utilities::get_work_order_statuses() as $key => $label) : ?>
                            <option value="<?php echo esc_attr($key); ?>" <?php selected($status, $key); ?>><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="de_date_promised"><?php _e('Promised Date', 'dealeredge'); ?></label></th>
                <td><input type="date" id="de_date_promised" name="de_date_promised" value="<?php echo esc_attr($date_promised); ?>" /></td>
            </tr>
            <tr>
                <th><label for="de_labor_hours"><?php _e('Labor Hours', 'dealeredge'); ?></label></th>
                <td><input type="number" id="de_labor_hours" name="de_labor_hours" value="<?php echo esc_attr($labor_hours); ?>" step="0.25" min="0" /></td>
            </tr>
            <tr>
                <th><label for="de_labor_rate"><?php _e('Labor Rate', 'dealeredge'); ?></label></th>
                <td><input type="number" id="de_labor_rate" name="de_labor_rate" value="<?php echo esc_attr($labor_rate); ?>" step="0.01" min="0" /></td>
            </tr>
            <tr>
                <th><label for="de_parts_cost"><?php _e('Parts Cost', 'dealeredge'); ?></label></th>
                <td><input type="number" id="de_parts_cost" name="de_parts_cost" value="<?php echo esc_attr($parts_cost); ?>" step="0.01" min="0" /></td>
            </tr>
        </table>
        
        <?php
    }
    
    public static function sale_details_meta_box($post) {
        // Implementation for sale details
        wp_nonce_field('de_sale_meta_box', 'de_sale_meta_box_nonce');
        // ... Sale-specific fields
    }
    
    public static function part_details_meta_box($post) {
        // Implementation for part details
        wp_nonce_field('de_part_meta_box', 'de_part_meta_box_nonce');
        // ... Part-specific fields
    }
    
    public static function save_meta_boxes($post_id) {
        // Check if it's an autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check user permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Save vehicle meta
        if (isset($_POST['de_vehicle_meta_box_nonce']) && wp_verify_nonce($_POST['de_vehicle_meta_box_nonce'], 'de_vehicle_meta_box')) {
            $fields = array(
                'de_vehicle_year', 'de_vehicle_vin', 'de_vehicle_mileage', 'de_vehicle_engine',
                'de_vehicle_transmission', 'de_vehicle_color', 'de_vehicle_condition',
                'de_vehicle_status', 'de_vehicle_purchase_price', 'de_vehicle_sale_price', 'de_vehicle_notes'
            );
            
            foreach ($fields as $field) {
                if (isset($_POST[$field])) {
                    $value = sanitize_text_field($_POST[$field]);
                    if ($field === 'de_vehicle_vin') {
                        $value = DE_Utilities::sanitize_vin($value);
                    }
                    update_post_meta($post_id, '_' . $field, $value);
                }
            }
        }
        
        // Save customer meta
        if (isset($_POST['de_customer_meta_box_nonce']) && wp_verify_nonce($_POST['de_customer_meta_box_nonce'], 'de_customer_meta_box')) {
            $fields = array(
                'de_customer_first_name', 'de_customer_last_name', 'de_customer_email',
                'de_customer_phone', 'de_customer_address', 'de_customer_city',
                'de_customer_state', 'de_customer_zip', 'de_customer_license_number', 'de_customer_notes'
            );
            
            foreach ($fields as $field) {
                if (isset($_POST[$field])) {
                    $value = sanitize_text_field($_POST[$field]);
                    if ($field === 'de_customer_phone') {
                        $value = DE_Utilities::format_phone($value);
                    }
                    update_post_meta($post_id, '_' . $field, $value);
                }
            }
        }
        
        // Save work order meta
        if (isset($_POST['de_work_order_meta_box_nonce']) && wp_verify_nonce($_POST['de_work_order_meta_box_nonce'], 'de_work_order_meta_box')) {
            $fields = array(
                'de_work_order_number', 'de_customer_id', 'de_vehicle_id', 'de_work_order_status',
                'de_labor_hours', 'de_labor_rate', 'de_parts_cost', 'de_date_promised'
            );
            
            foreach ($fields as $field) {
                if (isset($_POST[$field])) {
                    $value = sanitize_text_field($_POST[$field]);
                    update_post_meta($post_id, '_' . $field, $value);
                }
            }
            
            // Calculate totals
            $labor_hours = floatval($_POST['de_labor_hours'] ?? 0);
            $labor_rate = floatval($_POST['de_labor_rate'] ?? 0);
            $parts_cost = floatval($_POST['de_parts_cost'] ?? 0);
            
            $labor_cost = $labor_hours * $labor_rate;
            $subtotal = $labor_cost + $parts_cost;
            $tax_amount = DE_Utilities::calculate_tax($subtotal);
            $total_amount = $subtotal + $tax_amount;
            
            update_post_meta($post_id, '_de_labor_cost', $labor_cost);
            update_post_meta($post_id, '_de_subtotal', $subtotal);
            update_post_meta($post_id, '_de_tax_amount', $tax_amount);
            update_post_meta($post_id, '_de_total_amount', $total_amount);
        }
    }
}