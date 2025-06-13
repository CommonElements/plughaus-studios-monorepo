<?php
/**
 * Meta boxes for EquipRent Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Meta boxes class
 */
class ERP_Meta_Boxes {

    /**
     * Initialize meta boxes
     */
    public static function init() {
        add_action('add_meta_boxes', array(__CLASS__, 'add_meta_boxes'));
        add_action('save_post', array(__CLASS__, 'save_meta_boxes'));
    }

    /**
     * Add meta boxes
     */
    public static function add_meta_boxes() {
        // Equipment meta boxes
        if (function_exists('add_meta_box')) {
            add_meta_box(
                'erp_equipment_details',
                __('Equipment Details', 'equiprent-pro'),
                array(__CLASS__, 'equipment_details_meta_box'),
                'erp_equipment',
                'normal',
                'high'
            );

            add_meta_box(
                'erp_equipment_pricing',
                __('Pricing Information', 'equiprent-pro'),
                array(__CLASS__, 'equipment_pricing_meta_box'),
                'erp_equipment',
                'side',
                'default'
            );
        }
    }

    /**
     * Equipment details meta box
     */
    public static function equipment_details_meta_box($post) {
        // Add nonce for security
        if (function_exists('wp_nonce_field')) {
            wp_nonce_field('erp_equipment_meta_box', 'erp_equipment_meta_nonce');
        }

        // Get current values
        $brand = get_post_meta($post->ID, '_erp_brand', true);
        $model = get_post_meta($post->ID, '_erp_model', true);
        $serial_number = get_post_meta($post->ID, '_erp_serial_number', true);
        $condition_status = get_post_meta($post->ID, '_erp_condition_status', true);
        $location = get_post_meta($post->ID, '_erp_location', true);

        ?>
        <table class="form-table">
            <tr>
                <th><label for="erp_brand"><?php _e('Brand', 'equiprent-pro'); ?></label></th>
                <td><input type="text" id="erp_brand" name="erp_brand" value="<?php echo esc_attr($brand); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="erp_model"><?php _e('Model', 'equiprent-pro'); ?></label></th>
                <td><input type="text" id="erp_model" name="erp_model" value="<?php echo esc_attr($model); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="erp_serial_number"><?php _e('Serial Number', 'equiprent-pro'); ?></label></th>
                <td><input type="text" id="erp_serial_number" name="erp_serial_number" value="<?php echo esc_attr($serial_number); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="erp_condition_status"><?php _e('Condition', 'equiprent-pro'); ?></label></th>
                <td>
                    <select id="erp_condition_status" name="erp_condition_status">
                        <option value="excellent" <?php selected($condition_status, 'excellent'); ?>><?php _e('Excellent', 'equiprent-pro'); ?></option>
                        <option value="good" <?php selected($condition_status, 'good'); ?>><?php _e('Good', 'equiprent-pro'); ?></option>
                        <option value="fair" <?php selected($condition_status, 'fair'); ?>><?php _e('Fair', 'equiprent-pro'); ?></option>
                        <option value="poor" <?php selected($condition_status, 'poor'); ?>><?php _e('Poor', 'equiprent-pro'); ?></option>
                        <option value="needs_repair" <?php selected($condition_status, 'needs_repair'); ?>><?php _e('Needs Repair', 'equiprent-pro'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="erp_location"><?php _e('Location', 'equiprent-pro'); ?></label></th>
                <td><input type="text" id="erp_location" name="erp_location" value="<?php echo esc_attr($location); ?>" class="regular-text" /></td>
            </tr>
        </table>
        <?php
    }

    /**
     * Equipment pricing meta box
     */
    public static function equipment_pricing_meta_box($post) {
        // Get current values
        $daily_rate = get_post_meta($post->ID, '_erp_daily_rate', true);
        $weekly_rate = get_post_meta($post->ID, '_erp_weekly_rate', true);
        $monthly_rate = get_post_meta($post->ID, '_erp_monthly_rate', true);
        $deposit_amount = get_post_meta($post->ID, '_erp_deposit_amount', true);

        ?>
        <table class="form-table">
            <tr>
                <th><label for="erp_daily_rate"><?php _e('Daily Rate', 'equiprent-pro'); ?></label></th>
                <td><input type="number" id="erp_daily_rate" name="erp_daily_rate" value="<?php echo esc_attr($daily_rate); ?>" step="0.01" min="0" class="small-text" /></td>
            </tr>
            <tr>
                <th><label for="erp_weekly_rate"><?php _e('Weekly Rate', 'equiprent-pro'); ?></label></th>
                <td><input type="number" id="erp_weekly_rate" name="erp_weekly_rate" value="<?php echo esc_attr($weekly_rate); ?>" step="0.01" min="0" class="small-text" /></td>
            </tr>
            <tr>
                <th><label for="erp_monthly_rate"><?php _e('Monthly Rate', 'equiprent-pro'); ?></label></th>
                <td><input type="number" id="erp_monthly_rate" name="erp_monthly_rate" value="<?php echo esc_attr($monthly_rate); ?>" step="0.01" min="0" class="small-text" /></td>
            </tr>
            <tr>
                <th><label for="erp_deposit_amount"><?php _e('Deposit', 'equiprent-pro'); ?></label></th>
                <td><input type="number" id="erp_deposit_amount" name="erp_deposit_amount" value="<?php echo esc_attr($deposit_amount); ?>" step="0.01" min="0" class="small-text" /></td>
            </tr>
        </table>
        <?php
    }

    /**
     * Save meta box data
     */
    public static function save_meta_boxes($post_id) {
        // Check if this is an autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check nonce
        if (!isset($_POST['erp_equipment_meta_nonce']) || (function_exists('wp_verify_nonce') && !wp_verify_nonce($_POST['erp_equipment_meta_nonce'], 'erp_equipment_meta_box'))) {
            return;
        }

        // Check permissions
        if (isset($_POST['post_type']) && $_POST['post_type'] === 'erp_equipment') {
            if (function_exists('current_user_can') && !current_user_can('edit_equipment', $post_id)) {
                return;
            }
        }

        // Save meta fields
        $meta_fields = array(
            'erp_brand' => '_erp_brand',
            'erp_model' => '_erp_model',
            'erp_serial_number' => '_erp_serial_number',
            'erp_condition_status' => '_erp_condition_status',
            'erp_location' => '_erp_location',
            'erp_daily_rate' => '_erp_daily_rate',
            'erp_weekly_rate' => '_erp_weekly_rate',
            'erp_monthly_rate' => '_erp_monthly_rate',
            'erp_deposit_amount' => '_erp_deposit_amount'
        );

        foreach ($meta_fields as $field => $meta_key) {
            if (isset($_POST[$field])) {
                $value = sanitize_text_field($_POST[$field]);
                if (function_exists('update_post_meta')) {
                    update_post_meta($post_id, $meta_key, $value);
                }
            }
        }
    }
}