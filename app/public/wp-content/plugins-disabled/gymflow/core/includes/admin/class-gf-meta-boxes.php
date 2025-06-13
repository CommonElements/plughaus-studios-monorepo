<?php
/**
 * GymFlow Meta Boxes Class
 *
 * Handles all meta box functionality for custom post types
 *
 * @package GymFlow
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * GF_Meta_Boxes Class
 *
 * Meta box management functionality
 */
class GF_Meta_Boxes {

    /**
     * Initialize meta boxes
     */
    public function init() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_boxes'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_meta_box_scripts'));
    }

    /**
     * Add meta boxes
     */
    public function add_meta_boxes() {
        // Member meta boxes
        add_meta_box(
            'gf_member_details',
            __('Member Details', 'gymflow'),
            array($this, 'member_details_meta_box'),
            'gf_member',
            'normal',
            'high'
        );

        add_meta_box(
            'gf_member_membership',
            __('Membership Information', 'gymflow'),
            array($this, 'member_membership_meta_box'),
            'gf_member',
            'normal',
            'high'
        );

        add_meta_box(
            'gf_member_emergency',
            __('Emergency Contact', 'gymflow'),
            array($this, 'member_emergency_meta_box'),
            'gf_member',
            'side',
            'default'
        );

        add_meta_box(
            'gf_member_health',
            __('Health Information', 'gymflow'),
            array($this, 'member_health_meta_box'),
            'gf_member',
            'side',
            'default'
        );

        // Class meta boxes
        add_meta_box(
            'gf_class_details',
            __('Class Details', 'gymflow'),
            array($this, 'class_details_meta_box'),
            'gf_class',
            'normal',
            'high'
        );

        add_meta_box(
            'gf_class_scheduling',
            __('Scheduling Settings', 'gymflow'),
            array($this, 'class_scheduling_meta_box'),
            'gf_class',
            'normal',
            'high'
        );

        add_meta_box(
            'gf_class_pricing',
            __('Pricing', 'gymflow'),
            array($this, 'class_pricing_meta_box'),
            'gf_class',
            'side',
            'default'
        );

        // Trainer meta boxes
        add_meta_box(
            'gf_trainer_details',
            __('Trainer Details', 'gymflow'),
            array($this, 'trainer_details_meta_box'),
            'gf_trainer',
            'normal',
            'high'
        );

        add_meta_box(
            'gf_trainer_professional',
            __('Professional Information', 'gymflow'),
            array($this, 'trainer_professional_meta_box'),
            'gf_trainer',
            'normal',
            'high'
        );

        add_meta_box(
            'gf_trainer_compensation',
            __('Compensation', 'gymflow'),
            array($this, 'trainer_compensation_meta_box'),
            'gf_trainer',
            'side',
            'default'
        );

        // Equipment meta boxes
        add_meta_box(
            'gf_equipment_details',
            __('Equipment Details', 'gymflow'),
            array($this, 'equipment_details_meta_box'),
            'gf_equipment',
            'normal',
            'high'
        );

        add_meta_box(
            'gf_equipment_maintenance',
            __('Maintenance Information', 'gymflow'),
            array($this, 'equipment_maintenance_meta_box'),
            'gf_equipment',
            'normal',
            'high'
        );

        add_meta_box(
            'gf_equipment_booking',
            __('Booking Settings', 'gymflow'),
            array($this, 'equipment_booking_meta_box'),
            'gf_equipment',
            'side',
            'default'
        );

        // Booking meta boxes
        add_meta_box(
            'gf_booking_details',
            __('Booking Details', 'gymflow'),
            array($this, 'booking_details_meta_box'),
            'gf_booking',
            'normal',
            'high'
        );

        add_meta_box(
            'gf_booking_payment',
            __('Payment Information', 'gymflow'),
            array($this, 'booking_payment_meta_box'),
            'gf_booking',
            'side',
            'default'
        );

        add_meta_box(
            'gf_booking_status',
            __('Booking Status', 'gymflow'),
            array($this, 'booking_status_meta_box'),
            'gf_booking',
            'side',
            'default'
        );
    }

    /**
     * Enqueue meta box scripts
     */
    public function enqueue_meta_box_scripts($hook) {
        global $post_type;

        $gymflow_post_types = array('gf_member', 'gf_class', 'gf_trainer', 'gf_equipment', 'gf_booking');

        if (in_array($post_type, $gymflow_post_types) && ($hook === 'post.php' || $hook === 'post-new.php')) {
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_script('jquery-ui-timepicker-addon');
            wp_enqueue_style('jquery-ui');

            wp_enqueue_script(
                'gymflow-meta-boxes',
                GYMFLOW_PLUGIN_URL . 'core/assets/js/meta-boxes.js',
                array('jquery', 'jquery-ui-datepicker'),
                GYMFLOW_VERSION,
                true
            );

            wp_localize_script('gymflow-meta-boxes', 'gymflow_meta', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('gymflow_meta_boxes'),
                'strings' => array(
                    'confirm_delete' => __('Are you sure you want to delete this?', 'gymflow'),
                    'loading' => __('Loading...', 'gymflow')
                )
            ));
        }
    }

    /**
     * Member details meta box
     */
    public function member_details_meta_box($post) {
        wp_nonce_field('gf_member_details_nonce', 'gf_member_details_nonce');

        $member_number = get_post_meta($post->ID, '_member_number', true);
        $first_name = get_post_meta($post->ID, '_first_name', true);
        $last_name = get_post_meta($post->ID, '_last_name', true);
        $email = get_post_meta($post->ID, '_email', true);
        $phone = get_post_meta($post->ID, '_phone', true);
        $date_of_birth = get_post_meta($post->ID, '_date_of_birth', true);
        $gender = get_post_meta($post->ID, '_gender', true);

        ?>
        <table class="form-table">
            <tr>
                <th><label for="member_number"><?php _e('Member Number', 'gymflow'); ?></label></th>
                <td>
                    <input type="text" id="member_number" name="member_number" value="<?php echo esc_attr($member_number); ?>" class="regular-text" readonly />
                    <p class="description"><?php _e('Auto-generated unique member number', 'gymflow'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="first_name"><?php _e('First Name', 'gymflow'); ?> *</label></th>
                <td><input type="text" id="first_name" name="first_name" value="<?php echo esc_attr($first_name); ?>" class="regular-text" required /></td>
            </tr>
            <tr>
                <th><label for="last_name"><?php _e('Last Name', 'gymflow'); ?> *</label></th>
                <td><input type="text" id="last_name" name="last_name" value="<?php echo esc_attr($last_name); ?>" class="regular-text" required /></td>
            </tr>
            <tr>
                <th><label for="email"><?php _e('Email Address', 'gymflow'); ?> *</label></th>
                <td><input type="email" id="email" name="email" value="<?php echo esc_attr($email); ?>" class="regular-text" required /></td>
            </tr>
            <tr>
                <th><label for="phone"><?php _e('Phone Number', 'gymflow'); ?></label></th>
                <td><input type="tel" id="phone" name="phone" value="<?php echo esc_attr($phone); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="date_of_birth"><?php _e('Date of Birth', 'gymflow'); ?></label></th>
                <td><input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo esc_attr($date_of_birth); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="gender"><?php _e('Gender', 'gymflow'); ?></label></th>
                <td>
                    <select id="gender" name="gender">
                        <option value="prefer_not_to_say" <?php selected($gender, 'prefer_not_to_say'); ?>><?php _e('Prefer not to say', 'gymflow'); ?></option>
                        <option value="male" <?php selected($gender, 'male'); ?>><?php _e('Male', 'gymflow'); ?></option>
                        <option value="female" <?php selected($gender, 'female'); ?>><?php _e('Female', 'gymflow'); ?></option>
                        <option value="other" <?php selected($gender, 'other'); ?>><?php _e('Other', 'gymflow'); ?></option>
                    </select>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Member membership meta box
     */
    public function member_membership_meta_box($post) {
        $membership_type = get_post_meta($post->ID, '_membership_type', true);
        $membership_status = get_post_meta($post->ID, '_membership_status', true);
        $membership_start_date = get_post_meta($post->ID, '_membership_start_date', true);
        $membership_end_date = get_post_meta($post->ID, '_membership_end_date', true);

        // Get available membership types
        global $wpdb;
        $memberships_table = $wpdb->prefix . 'gf_memberships';
        $membership_types = $wpdb->get_results("SELECT * FROM {$memberships_table} WHERE is_active = 1 ORDER BY sort_order ASC");

        ?>
        <table class="form-table">
            <tr>
                <th><label for="membership_type"><?php _e('Membership Type', 'gymflow'); ?></label></th>
                <td>
                    <select id="membership_type" name="membership_type">
                        <option value=""><?php _e('Select Membership Type', 'gymflow'); ?></option>
                        <?php foreach ($membership_types as $type): ?>
                            <option value="<?php echo esc_attr($type->name); ?>" <?php selected($membership_type, $type->name); ?>>
                                <?php echo esc_html($type->name); ?> - <?php echo GF_Utilities::format_currency($type->price); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="membership_status"><?php _e('Membership Status', 'gymflow'); ?></label></th>
                <td>
                    <select id="membership_status" name="membership_status">
                        <option value="pending" <?php selected($membership_status, 'pending'); ?>><?php _e('Pending', 'gymflow'); ?></option>
                        <option value="active" <?php selected($membership_status, 'active'); ?>><?php _e('Active', 'gymflow'); ?></option>
                        <option value="expired" <?php selected($membership_status, 'expired'); ?>><?php _e('Expired', 'gymflow'); ?></option>
                        <option value="cancelled" <?php selected($membership_status, 'cancelled'); ?>><?php _e('Cancelled', 'gymflow'); ?></option>
                        <option value="on_hold" <?php selected($membership_status, 'on_hold'); ?>><?php _e('On Hold', 'gymflow'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="membership_start_date"><?php _e('Start Date', 'gymflow'); ?></label></th>
                <td><input type="date" id="membership_start_date" name="membership_start_date" value="<?php echo esc_attr($membership_start_date); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="membership_end_date"><?php _e('End Date', 'gymflow'); ?></label></th>
                <td><input type="date" id="membership_end_date" name="membership_end_date" value="<?php echo esc_attr($membership_end_date); ?>" class="regular-text" /></td>
            </tr>
        </table>
        <?php
    }

    /**
     * Member emergency contact meta box
     */
    public function member_emergency_meta_box($post) {
        $emergency_contact_name = get_post_meta($post->ID, '_emergency_contact_name', true);
        $emergency_contact_phone = get_post_meta($post->ID, '_emergency_contact_phone', true);

        ?>
        <table class="form-table">
            <tr>
                <th><label for="emergency_contact_name"><?php _e('Contact Name', 'gymflow'); ?></label></th>
                <td><input type="text" id="emergency_contact_name" name="emergency_contact_name" value="<?php echo esc_attr($emergency_contact_name); ?>" class="widefat" /></td>
            </tr>
            <tr>
                <th><label for="emergency_contact_phone"><?php _e('Contact Phone', 'gymflow'); ?></label></th>
                <td><input type="tel" id="emergency_contact_phone" name="emergency_contact_phone" value="<?php echo esc_attr($emergency_contact_phone); ?>" class="widefat" /></td>
            </tr>
        </table>
        <?php
    }

    /**
     * Member health information meta box
     */
    public function member_health_meta_box($post) {
        $health_conditions = get_post_meta($post->ID, '_health_conditions', true);

        ?>
        <table class="form-table">
            <tr>
                <th><label for="health_conditions"><?php _e('Health Conditions', 'gymflow'); ?></label></th>
                <td>
                    <textarea id="health_conditions" name="health_conditions" rows="4" class="widefat"><?php echo esc_textarea($health_conditions); ?></textarea>
                    <p class="description"><?php _e('List any relevant health conditions, allergies, or medical information', 'gymflow'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Class details meta box
     */
    public function class_details_meta_box($post) {
        wp_nonce_field('gf_class_details_nonce', 'gf_class_details_nonce');

        $duration = get_post_meta($post->ID, '_duration', true) ?: 60;
        $capacity = get_post_meta($post->ID, '_capacity', true) ?: 20;
        $difficulty_level = get_post_meta($post->ID, '_difficulty_level', true);
        $equipment_required = get_post_meta($post->ID, '_equipment_required', true);
        $instructor_id = get_post_meta($post->ID, '_instructor_id', true);

        // Get trainers
        $trainers = GF_Trainer::get_all();

        ?>
        <table class="form-table">
            <tr>
                <th><label for="duration"><?php _e('Duration (minutes)', 'gymflow'); ?></label></th>
                <td><input type="number" id="duration" name="duration" value="<?php echo esc_attr($duration); ?>" min="15" max="300" class="small-text" /></td>
            </tr>
            <tr>
                <th><label for="capacity"><?php _e('Maximum Capacity', 'gymflow'); ?></label></th>
                <td><input type="number" id="capacity" name="capacity" value="<?php echo esc_attr($capacity); ?>" min="1" max="100" class="small-text" /></td>
            </tr>
            <tr>
                <th><label for="difficulty_level"><?php _e('Difficulty Level', 'gymflow'); ?></label></th>
                <td>
                    <select id="difficulty_level" name="difficulty_level">
                        <option value="all_levels" <?php selected($difficulty_level, 'all_levels'); ?>><?php _e('All Levels', 'gymflow'); ?></option>
                        <option value="beginner" <?php selected($difficulty_level, 'beginner'); ?>><?php _e('Beginner', 'gymflow'); ?></option>
                        <option value="intermediate" <?php selected($difficulty_level, 'intermediate'); ?>><?php _e('Intermediate', 'gymflow'); ?></option>
                        <option value="advanced" <?php selected($difficulty_level, 'advanced'); ?>><?php _e('Advanced', 'gymflow'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="instructor_id"><?php _e('Default Instructor', 'gymflow'); ?></label></th>
                <td>
                    <select id="instructor_id" name="instructor_id">
                        <option value=""><?php _e('Select Instructor', 'gymflow'); ?></option>
                        <?php foreach ($trainers as $trainer): ?>
                            <option value="<?php echo esc_attr($trainer->id); ?>" <?php selected($instructor_id, $trainer->id); ?>>
                                <?php echo esc_html($trainer->first_name . ' ' . $trainer->last_name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="equipment_required"><?php _e('Equipment Required', 'gymflow'); ?></label></th>
                <td>
                    <textarea id="equipment_required" name="equipment_required" rows="3" class="widefat"><?php echo esc_textarea($equipment_required); ?></textarea>
                    <p class="description"><?php _e('List any special equipment or items needed for this class', 'gymflow'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Class pricing meta box
     */
    public function class_pricing_meta_box($post) {
        $price = get_post_meta($post->ID, '_price', true);
        $drop_in_price = get_post_meta($post->ID, '_drop_in_price', true);
        $is_active = get_post_meta($post->ID, '_is_active', true) !== '0';

        ?>
        <table class="form-table">
            <tr>
                <th><label for="price"><?php _e('Regular Price', 'gymflow'); ?></label></th>
                <td>
                    <input type="number" id="price" name="price" value="<?php echo esc_attr($price); ?>" step="0.01" min="0" class="small-text" />
                    <p class="description"><?php _e('Price for members with unlimited classes', 'gymflow'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="drop_in_price"><?php _e('Drop-in Price', 'gymflow'); ?></label></th>
                <td>
                    <input type="number" id="drop_in_price" name="drop_in_price" value="<?php echo esc_attr($drop_in_price); ?>" step="0.01" min="0" class="small-text" />
                    <p class="description"><?php _e('Price for single class attendance', 'gymflow'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="is_active"><?php _e('Active', 'gymflow'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" id="is_active" name="is_active" value="1" <?php checked($is_active); ?> />
                        <?php _e('This class is active and available for booking', 'gymflow'); ?>
                    </label>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Trainer details meta box
     */
    public function trainer_details_meta_box($post) {
        wp_nonce_field('gf_trainer_details_nonce', 'gf_trainer_details_nonce');

        $trainer_number = get_post_meta($post->ID, '_trainer_number', true);
        $first_name = get_post_meta($post->ID, '_first_name', true);
        $last_name = get_post_meta($post->ID, '_last_name', true);
        $email = get_post_meta($post->ID, '_email', true);
        $phone = get_post_meta($post->ID, '_phone', true);
        $hire_date = get_post_meta($post->ID, '_hire_date', true);
        $is_active = get_post_meta($post->ID, '_is_active', true) !== '0';

        ?>
        <table class="form-table">
            <tr>
                <th><label for="trainer_number"><?php _e('Trainer Number', 'gymflow'); ?></label></th>
                <td>
                    <input type="text" id="trainer_number" name="trainer_number" value="<?php echo esc_attr($trainer_number); ?>" class="regular-text" readonly />
                    <p class="description"><?php _e('Auto-generated unique trainer number', 'gymflow'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="first_name"><?php _e('First Name', 'gymflow'); ?> *</label></th>
                <td><input type="text" id="first_name" name="first_name" value="<?php echo esc_attr($first_name); ?>" class="regular-text" required /></td>
            </tr>
            <tr>
                <th><label for="last_name"><?php _e('Last Name', 'gymflow'); ?> *</label></th>
                <td><input type="text" id="last_name" name="last_name" value="<?php echo esc_attr($last_name); ?>" class="regular-text" required /></td>
            </tr>
            <tr>
                <th><label for="email"><?php _e('Email Address', 'gymflow'); ?> *</label></th>
                <td><input type="email" id="email" name="email" value="<?php echo esc_attr($email); ?>" class="regular-text" required /></td>
            </tr>
            <tr>
                <th><label for="phone"><?php _e('Phone Number', 'gymflow'); ?></label></th>
                <td><input type="tel" id="phone" name="phone" value="<?php echo esc_attr($phone); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="hire_date"><?php _e('Hire Date', 'gymflow'); ?></label></th>
                <td><input type="date" id="hire_date" name="hire_date" value="<?php echo esc_attr($hire_date); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="is_active"><?php _e('Active Status', 'gymflow'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" id="is_active" name="is_active" value="1" <?php checked($is_active); ?> />
                        <?php _e('This trainer is active and available', 'gymflow'); ?>
                    </label>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Trainer professional information meta box
     */
    public function trainer_professional_meta_box($post) {
        $bio = get_post_meta($post->ID, '_bio', true);
        $specialties = get_post_meta($post->ID, '_specialties', true);
        $certifications = get_post_meta($post->ID, '_certifications', true);

        ?>
        <table class="form-table">
            <tr>
                <th><label for="bio"><?php _e('Bio', 'gymflow'); ?></label></th>
                <td>
                    <textarea id="bio" name="bio" rows="4" class="widefat"><?php echo esc_textarea($bio); ?></textarea>
                    <p class="description"><?php _e('Brief biography and background information', 'gymflow'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="specialties"><?php _e('Specialties', 'gymflow'); ?></label></th>
                <td>
                    <textarea id="specialties" name="specialties" rows="3" class="widefat"><?php echo esc_textarea($specialties); ?></textarea>
                    <p class="description"><?php _e('Areas of expertise (comma-separated)', 'gymflow'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="certifications"><?php _e('Certifications', 'gymflow'); ?></label></th>
                <td>
                    <textarea id="certifications" name="certifications" rows="3" class="widefat"><?php echo esc_textarea($certifications); ?></textarea>
                    <p class="description"><?php _e('Professional certifications and credentials', 'gymflow'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Trainer compensation meta box
     */
    public function trainer_compensation_meta_box($post) {
        $hourly_rate = get_post_meta($post->ID, '_hourly_rate', true);
        $commission_rate = get_post_meta($post->ID, '_commission_rate', true);

        ?>
        <table class="form-table">
            <tr>
                <th><label for="hourly_rate"><?php _e('Hourly Rate', 'gymflow'); ?></label></th>
                <td>
                    <input type="number" id="hourly_rate" name="hourly_rate" value="<?php echo esc_attr($hourly_rate); ?>" step="0.01" min="0" class="small-text" />
                    <span class="description"><?php _e('Per hour for personal training', 'gymflow'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="commission_rate"><?php _e('Commission Rate (%)', 'gymflow'); ?></label></th>
                <td>
                    <input type="number" id="commission_rate" name="commission_rate" value="<?php echo esc_attr($commission_rate); ?>" step="0.1" min="0" max="100" class="small-text" />
                    <span class="description"><?php _e('Percentage of class/session revenue', 'gymflow'); ?></span>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Equipment details meta box
     */
    public function equipment_details_meta_box($post) {
        wp_nonce_field('gf_equipment_details_nonce', 'gf_equipment_details_nonce');

        $serial_number = get_post_meta($post->ID, '_serial_number', true);
        $purchase_date = get_post_meta($post->ID, '_purchase_date', true);
        $purchase_price = get_post_meta($post->ID, '_purchase_price', true);
        $current_value = get_post_meta($post->ID, '_current_value', true);
        $condition_rating = get_post_meta($post->ID, '_condition_rating', true);
        $location = get_post_meta($post->ID, '_location', true);
        $status = get_post_meta($post->ID, '_status', true);

        ?>
        <table class="form-table">
            <tr>
                <th><label for="serial_number"><?php _e('Serial Number', 'gymflow'); ?></label></th>
                <td><input type="text" id="serial_number" name="serial_number" value="<?php echo esc_attr($serial_number); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="purchase_date"><?php _e('Purchase Date', 'gymflow'); ?></label></th>
                <td><input type="date" id="purchase_date" name="purchase_date" value="<?php echo esc_attr($purchase_date); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="purchase_price"><?php _e('Purchase Price', 'gymflow'); ?></label></th>
                <td><input type="number" id="purchase_price" name="purchase_price" value="<?php echo esc_attr($purchase_price); ?>" step="0.01" min="0" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="current_value"><?php _e('Current Value', 'gymflow'); ?></label></th>
                <td><input type="number" id="current_value" name="current_value" value="<?php echo esc_attr($current_value); ?>" step="0.01" min="0" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="condition_rating"><?php _e('Condition', 'gymflow'); ?></label></th>
                <td>
                    <select id="condition_rating" name="condition_rating">
                        <option value="excellent" <?php selected($condition_rating, 'excellent'); ?>><?php _e('Excellent', 'gymflow'); ?></option>
                        <option value="good" <?php selected($condition_rating, 'good'); ?>><?php _e('Good', 'gymflow'); ?></option>
                        <option value="fair" <?php selected($condition_rating, 'fair'); ?>><?php _e('Fair', 'gymflow'); ?></option>
                        <option value="poor" <?php selected($condition_rating, 'poor'); ?>><?php _e('Poor', 'gymflow'); ?></option>
                        <option value="out_of_order" <?php selected($condition_rating, 'out_of_order'); ?>><?php _e('Out of Order', 'gymflow'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="location"><?php _e('Location', 'gymflow'); ?></label></th>
                <td><input type="text" id="location" name="location" value="<?php echo esc_attr($location); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="status"><?php _e('Status', 'gymflow'); ?></label></th>
                <td>
                    <select id="status" name="status">
                        <option value="available" <?php selected($status, 'available'); ?>><?php _e('Available', 'gymflow'); ?></option>
                        <option value="booked" <?php selected($status, 'booked'); ?>><?php _e('Booked', 'gymflow'); ?></option>
                        <option value="maintenance" <?php selected($status, 'maintenance'); ?>><?php _e('Maintenance', 'gymflow'); ?></option>
                        <option value="out_of_order" <?php selected($status, 'out_of_order'); ?>><?php _e('Out of Order', 'gymflow'); ?></option>
                    </select>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Equipment maintenance meta box
     */
    public function equipment_maintenance_meta_box($post) {
        $maintenance_schedule = get_post_meta($post->ID, '_maintenance_schedule', true);
        $last_maintenance_date = get_post_meta($post->ID, '_last_maintenance_date', true);
        $next_maintenance_date = get_post_meta($post->ID, '_next_maintenance_date', true);
        $usage_count = get_post_meta($post->ID, '_usage_count', true) ?: 0;

        ?>
        <table class="form-table">
            <tr>
                <th><label for="maintenance_schedule"><?php _e('Maintenance Schedule', 'gymflow'); ?></label></th>
                <td>
                    <select id="maintenance_schedule" name="maintenance_schedule">
                        <option value="" <?php selected($maintenance_schedule, ''); ?>><?php _e('No Schedule', 'gymflow'); ?></option>
                        <option value="weekly" <?php selected($maintenance_schedule, 'weekly'); ?>><?php _e('Weekly', 'gymflow'); ?></option>
                        <option value="monthly" <?php selected($maintenance_schedule, 'monthly'); ?>><?php _e('Monthly', 'gymflow'); ?></option>
                        <option value="quarterly" <?php selected($maintenance_schedule, 'quarterly'); ?>><?php _e('Quarterly', 'gymflow'); ?></option>
                        <option value="semi_annual" <?php selected($maintenance_schedule, 'semi_annual'); ?>><?php _e('Semi-Annual', 'gymflow'); ?></option>
                        <option value="annual" <?php selected($maintenance_schedule, 'annual'); ?>><?php _e('Annual', 'gymflow'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="last_maintenance_date"><?php _e('Last Maintenance', 'gymflow'); ?></label></th>
                <td><input type="date" id="last_maintenance_date" name="last_maintenance_date" value="<?php echo esc_attr($last_maintenance_date); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="next_maintenance_date"><?php _e('Next Maintenance', 'gymflow'); ?></label></th>
                <td><input type="date" id="next_maintenance_date" name="next_maintenance_date" value="<?php echo esc_attr($next_maintenance_date); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="usage_count"><?php _e('Usage Count', 'gymflow'); ?></label></th>
                <td>
                    <input type="number" id="usage_count" name="usage_count" value="<?php echo esc_attr($usage_count); ?>" min="0" class="small-text" readonly />
                    <p class="description"><?php _e('Automatically tracked when equipment is booked', 'gymflow'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Equipment booking meta box
     */
    public function equipment_booking_meta_box($post) {
        $is_bookable = get_post_meta($post->ID, '_is_bookable', true) !== '0';
        $booking_duration = get_post_meta($post->ID, '_booking_duration', true) ?: 60;
        $advance_booking_days = get_post_meta($post->ID, '_advance_booking_days', true) ?: 7;

        ?>
        <table class="form-table">
            <tr>
                <th><label for="is_bookable"><?php _e('Bookable', 'gymflow'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" id="is_bookable" name="is_bookable" value="1" <?php checked($is_bookable); ?> />
                        <?php _e('Allow members to book this equipment', 'gymflow'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th><label for="booking_duration"><?php _e('Default Duration (minutes)', 'gymflow'); ?></label></th>
                <td><input type="number" id="booking_duration" name="booking_duration" value="<?php echo esc_attr($booking_duration); ?>" min="15" max="480" class="small-text" /></td>
            </tr>
            <tr>
                <th><label for="advance_booking_days"><?php _e('Advance Booking (days)', 'gymflow'); ?></label></th>
                <td>
                    <input type="number" id="advance_booking_days" name="advance_booking_days" value="<?php echo esc_attr($advance_booking_days); ?>" min="1" max="365" class="small-text" />
                    <p class="description"><?php _e('How many days in advance can this equipment be booked', 'gymflow'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Booking details meta box
     */
    public function booking_details_meta_box($post) {
        wp_nonce_field('gf_booking_details_nonce', 'gf_booking_details_nonce');

        $booking_reference = get_post_meta($post->ID, '_booking_reference', true);
        $booking_type = get_post_meta($post->ID, '_booking_type', true);
        $member_id = get_post_meta($post->ID, '_member_id', true);
        $booking_date = get_post_meta($post->ID, '_booking_date', true);
        $start_time = get_post_meta($post->ID, '_start_time', true);
        $end_time = get_post_meta($post->ID, '_end_time', true);
        $booking_source = get_post_meta($post->ID, '_booking_source', true);

        ?>
        <table class="form-table">
            <tr>
                <th><label for="booking_reference"><?php _e('Booking Reference', 'gymflow'); ?></label></th>
                <td>
                    <input type="text" id="booking_reference" name="booking_reference" value="<?php echo esc_attr($booking_reference); ?>" class="regular-text" readonly />
                    <p class="description"><?php _e('Auto-generated unique booking reference', 'gymflow'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="booking_type"><?php _e('Booking Type', 'gymflow'); ?> *</label></th>
                <td>
                    <select id="booking_type" name="booking_type" required>
                        <option value=""><?php _e('Select Booking Type', 'gymflow'); ?></option>
                        <option value="class" <?php selected($booking_type, 'class'); ?>><?php _e('Class', 'gymflow'); ?></option>
                        <option value="equipment" <?php selected($booking_type, 'equipment'); ?>><?php _e('Equipment', 'gymflow'); ?></option>
                        <option value="personal_training" <?php selected($booking_type, 'personal_training'); ?>><?php _e('Personal Training', 'gymflow'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="member_id"><?php _e('Member', 'gymflow'); ?> *</label></th>
                <td>
                    <select id="member_id" name="member_id" required class="widefat">
                        <option value=""><?php _e('Select Member', 'gymflow'); ?></option>
                        <?php
                        $members = GF_Member::get_all(array('limit' => 100, 'status' => 'active'));
                        foreach ($members as $member):
                        ?>
                            <option value="<?php echo esc_attr($member->id); ?>" <?php selected($member_id, $member->id); ?>>
                                <?php echo esc_html($member->first_name . ' ' . $member->last_name . ' (' . $member->member_number . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="booking_date"><?php _e('Date', 'gymflow'); ?> *</label></th>
                <td><input type="date" id="booking_date" name="booking_date" value="<?php echo esc_attr($booking_date); ?>" class="regular-text" required /></td>
            </tr>
            <tr>
                <th><label for="start_time"><?php _e('Start Time', 'gymflow'); ?> *</label></th>
                <td><input type="time" id="start_time" name="start_time" value="<?php echo esc_attr($start_time); ?>" class="regular-text" required /></td>
            </tr>
            <tr>
                <th><label for="end_time"><?php _e('End Time', 'gymflow'); ?> *</label></th>
                <td><input type="time" id="end_time" name="end_time" value="<?php echo esc_attr($end_time); ?>" class="regular-text" required /></td>
            </tr>
            <tr>
                <th><label for="booking_source"><?php _e('Booking Source', 'gymflow'); ?></label></th>
                <td>
                    <select id="booking_source" name="booking_source">
                        <option value="admin" <?php selected($booking_source, 'admin'); ?>><?php _e('Admin', 'gymflow'); ?></option>
                        <option value="member_portal" <?php selected($booking_source, 'member_portal'); ?>><?php _e('Member Portal', 'gymflow'); ?></option>
                        <option value="walk_in" <?php selected($booking_source, 'walk_in'); ?>><?php _e('Walk-in', 'gymflow'); ?></option>
                        <option value="phone" <?php selected($booking_source, 'phone'); ?>><?php _e('Phone', 'gymflow'); ?></option>
                    </select>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Booking payment meta box
     */
    public function booking_payment_meta_box($post) {
        $amount = get_post_meta($post->ID, '_amount', true);
        $payment_status = get_post_meta($post->ID, '_payment_status', true);
        $payment_method = get_post_meta($post->ID, '_payment_method', true);

        ?>
        <table class="form-table">
            <tr>
                <th><label for="amount"><?php _e('Amount', 'gymflow'); ?></label></th>
                <td><input type="number" id="amount" name="amount" value="<?php echo esc_attr($amount); ?>" step="0.01" min="0" class="widefat" /></td>
            </tr>
            <tr>
                <th><label for="payment_status"><?php _e('Payment Status', 'gymflow'); ?></label></th>
                <td>
                    <select id="payment_status" name="payment_status" class="widefat">
                        <option value="pending" <?php selected($payment_status, 'pending'); ?>><?php _e('Pending', 'gymflow'); ?></option>
                        <option value="paid" <?php selected($payment_status, 'paid'); ?>><?php _e('Paid', 'gymflow'); ?></option>
                        <option value="refunded" <?php selected($payment_status, 'refunded'); ?>><?php _e('Refunded', 'gymflow'); ?></option>
                        <option value="partial" <?php selected($payment_status, 'partial'); ?>><?php _e('Partial', 'gymflow'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="payment_method"><?php _e('Payment Method', 'gymflow'); ?></label></th>
                <td>
                    <select id="payment_method" name="payment_method" class="widefat">
                        <option value=""><?php _e('Select Method', 'gymflow'); ?></option>
                        <option value="cash" <?php selected($payment_method, 'cash'); ?>><?php _e('Cash', 'gymflow'); ?></option>
                        <option value="card" <?php selected($payment_method, 'card'); ?>><?php _e('Credit/Debit Card', 'gymflow'); ?></option>
                        <option value="bank_transfer" <?php selected($payment_method, 'bank_transfer'); ?>><?php _e('Bank Transfer', 'gymflow'); ?></option>
                        <option value="online" <?php selected($payment_method, 'online'); ?>><?php _e('Online Payment', 'gymflow'); ?></option>
                    </select>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Booking status meta box
     */
    public function booking_status_meta_box($post) {
        $status = get_post_meta($post->ID, '_status', true);
        $confirmed_at = get_post_meta($post->ID, '_confirmed_at', true);
        $cancelled_at = get_post_meta($post->ID, '_cancelled_at', true);
        $cancellation_reason = get_post_meta($post->ID, '_cancellation_reason', true);

        ?>
        <table class="form-table">
            <tr>
                <th><label for="status"><?php _e('Status', 'gymflow'); ?></label></th>
                <td>
                    <select id="status" name="status" class="widefat">
                        <option value="confirmed" <?php selected($status, 'confirmed'); ?>><?php _e('Confirmed', 'gymflow'); ?></option>
                        <option value="pending" <?php selected($status, 'pending'); ?>><?php _e('Pending', 'gymflow'); ?></option>
                        <option value="cancelled" <?php selected($status, 'cancelled'); ?>><?php _e('Cancelled', 'gymflow'); ?></option>
                        <option value="completed" <?php selected($status, 'completed'); ?>><?php _e('Completed', 'gymflow'); ?></option>
                        <option value="no_show" <?php selected($status, 'no_show'); ?>><?php _e('No Show', 'gymflow'); ?></option>
                    </select>
                </td>
            </tr>
            <?php if ($confirmed_at): ?>
            <tr>
                <th><?php _e('Confirmed At', 'gymflow'); ?></th>
                <td><?php echo esc_html(GF_Utilities::format_datetime($confirmed_at)); ?></td>
            </tr>
            <?php endif; ?>
            <?php if ($cancelled_at): ?>
            <tr>
                <th><?php _e('Cancelled At', 'gymflow'); ?></th>
                <td><?php echo esc_html(GF_Utilities::format_datetime($cancelled_at)); ?></td>
            </tr>
            <tr>
                <th><label for="cancellation_reason"><?php _e('Reason', 'gymflow'); ?></label></th>
                <td>
                    <textarea id="cancellation_reason" name="cancellation_reason" rows="3" class="widefat" readonly><?php echo esc_textarea($cancellation_reason); ?></textarea>
                </td>
            </tr>
            <?php endif; ?>
        </table>
        <?php
    }

    /**
     * Save meta boxes data
     */
    public function save_meta_boxes($post_id) {
        // Check if this is an autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check user permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $post_type = get_post_type($post_id);

        switch ($post_type) {
            case 'gf_member':
                $this->save_member_meta($post_id);
                break;
            case 'gf_class':
                $this->save_class_meta($post_id);
                break;
            case 'gf_trainer':
                $this->save_trainer_meta($post_id);
                break;
            case 'gf_equipment':
                $this->save_equipment_meta($post_id);
                break;
            case 'gf_booking':
                $this->save_booking_meta($post_id);
                break;
        }
    }

    /**
     * Save member meta data
     */
    private function save_member_meta($post_id) {
        if (!isset($_POST['gf_member_details_nonce']) || !wp_verify_nonce($_POST['gf_member_details_nonce'], 'gf_member_details_nonce')) {
            return;
        }

        $meta_fields = array(
            'member_number', 'first_name', 'last_name', 'email', 'phone',
            'date_of_birth', 'gender', 'emergency_contact_name', 'emergency_contact_phone',
            'health_conditions', 'membership_type', 'membership_status',
            'membership_start_date', 'membership_end_date'
        );

        foreach ($meta_fields as $field) {
            if (isset($_POST[$field])) {
                $value = sanitize_text_field($_POST[$field]);
                if ($field === 'health_conditions') {
                    $value = sanitize_textarea_field($_POST[$field]);
                } elseif ($field === 'email') {
                    $value = sanitize_email($_POST[$field]);
                }
                update_post_meta($post_id, '_' . $field, $value);
            }
        }
    }

    /**
     * Save class meta data
     */
    private function save_class_meta($post_id) {
        if (!isset($_POST['gf_class_details_nonce']) || !wp_verify_nonce($_POST['gf_class_details_nonce'], 'gf_class_details_nonce')) {
            return;
        }

        $meta_fields = array(
            'duration' => 'int',
            'capacity' => 'int',
            'difficulty_level' => 'text',
            'equipment_required' => 'textarea',
            'instructor_id' => 'int',
            'price' => 'float',
            'drop_in_price' => 'float',
            'is_active' => 'checkbox'
        );

        foreach ($meta_fields as $field => $type) {
            if (isset($_POST[$field])) {
                $value = $_POST[$field];
                
                switch ($type) {
                    case 'int':
                        $value = intval($value);
                        break;
                    case 'float':
                        $value = floatval($value);
                        break;
                    case 'textarea':
                        $value = sanitize_textarea_field($value);
                        break;
                    case 'checkbox':
                        $value = $value ? 1 : 0;
                        break;
                    default:
                        $value = sanitize_text_field($value);
                        break;
                }
                
                update_post_meta($post_id, '_' . $field, $value);
            } elseif ($type === 'checkbox') {
                update_post_meta($post_id, '_' . $field, 0);
            }
        }
    }

    /**
     * Save trainer meta data
     */
    private function save_trainer_meta($post_id) {
        if (!isset($_POST['gf_trainer_details_nonce']) || !wp_verify_nonce($_POST['gf_trainer_details_nonce'], 'gf_trainer_details_nonce')) {
            return;
        }

        $meta_fields = array(
            'trainer_number', 'first_name', 'last_name', 'email', 'phone',
            'hire_date', 'bio', 'specialties', 'certifications',
            'hourly_rate', 'commission_rate', 'is_active'
        );

        foreach ($meta_fields as $field) {
            if (isset($_POST[$field])) {
                $value = $_POST[$field];
                
                if (in_array($field, array('bio', 'specialties', 'certifications'))) {
                    $value = sanitize_textarea_field($value);
                } elseif ($field === 'email') {
                    $value = sanitize_email($value);
                } elseif (in_array($field, array('hourly_rate', 'commission_rate'))) {
                    $value = floatval($value);
                } elseif ($field === 'is_active') {
                    $value = $value ? 1 : 0;
                } else {
                    $value = sanitize_text_field($value);
                }
                
                update_post_meta($post_id, '_' . $field, $value);
            } elseif ($field === 'is_active') {
                update_post_meta($post_id, '_' . $field, 0);
            }
        }
    }

    /**
     * Save equipment meta data
     */
    private function save_equipment_meta($post_id) {
        if (!isset($_POST['gf_equipment_details_nonce']) || !wp_verify_nonce($_POST['gf_equipment_details_nonce'], 'gf_equipment_details_nonce')) {
            return;
        }

        $meta_fields = array(
            'serial_number', 'purchase_date', 'purchase_price', 'current_value',
            'condition_rating', 'location', 'status', 'maintenance_schedule',
            'last_maintenance_date', 'next_maintenance_date', 'usage_count',
            'is_bookable', 'booking_duration', 'advance_booking_days'
        );

        foreach ($meta_fields as $field) {
            if (isset($_POST[$field])) {
                $value = $_POST[$field];
                
                if (in_array($field, array('purchase_price', 'current_value'))) {
                    $value = floatval($value);
                } elseif (in_array($field, array('usage_count', 'booking_duration', 'advance_booking_days'))) {
                    $value = intval($value);
                } elseif ($field === 'is_bookable') {
                    $value = $value ? 1 : 0;
                } else {
                    $value = sanitize_text_field($value);
                }
                
                update_post_meta($post_id, '_' . $field, $value);
            } elseif ($field === 'is_bookable') {
                update_post_meta($post_id, '_' . $field, 0);
            }
        }
    }

    /**
     * Save booking meta data
     */
    private function save_booking_meta($post_id) {
        if (!isset($_POST['gf_booking_details_nonce']) || !wp_verify_nonce($_POST['gf_booking_details_nonce'], 'gf_booking_details_nonce')) {
            return;
        }

        $meta_fields = array(
            'booking_reference', 'booking_type', 'member_id', 'booking_date',
            'start_time', 'end_time', 'booking_source', 'amount',
            'payment_status', 'payment_method', 'status', 'cancellation_reason'
        );

        foreach ($meta_fields as $field) {
            if (isset($_POST[$field])) {
                $value = $_POST[$field];
                
                if ($field === 'member_id') {
                    $value = intval($value);
                } elseif ($field === 'amount') {
                    $value = floatval($value);
                } elseif ($field === 'cancellation_reason') {
                    $value = sanitize_textarea_field($value);
                } else {
                    $value = sanitize_text_field($value);
                }
                
                update_post_meta($post_id, '_' . $field, $value);
            }
        }

        // Update confirmed/cancelled timestamps based on status
        $status = sanitize_text_field($_POST['status']);
        if ($status === 'confirmed' && !get_post_meta($post_id, '_confirmed_at', true)) {
            update_post_meta($post_id, '_confirmed_at', current_time('mysql'));
        }
        if ($status === 'cancelled' && !get_post_meta($post_id, '_cancelled_at', true)) {
            update_post_meta($post_id, '_cancelled_at', current_time('mysql'));
        }
    }
}